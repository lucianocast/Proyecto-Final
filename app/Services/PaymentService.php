<?php

namespace App\Services;

use App\Contracts\PaymentGateway;
use App\Models\Pedido;
use App\Models\Pago;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Servicio de negocio para procesar pagos
 * 
 * Implementa UC-09: Registrar pago
 * Orquesta la lógica de negocio después de procesar el cargo en la pasarela
 */
class PaymentService
{
    protected PaymentGateway $gateway;

    /**
     * Constructor con inyección de dependencias
     *
     * @param PaymentGateway $gateway Pasarela de pago (Google Pay, Mercado Pago, etc.)
     */
    public function __construct(PaymentGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Procesar un pago completo (UC-09)
     *
     * @param Pedido $pedido Pedido al que se aplicará el pago
     * @param float $monto Monto a pagar
     * @param array $tokenData Datos del token de pago
     * @param string $methodName Nombre del método de pago (google_pay, mercado_pago, etc.)
     * 
     * @return Pago Instancia del pago creado
     * @throws Exception Si el pago falla o hay errores
     */
    public function processPayment(
        Pedido $pedido,
        float $monto,
        array $tokenData,
        string $methodName
    ): Pago {
        // Validaciones previas
        $this->validatePayment($pedido, $monto);

        // Iniciar transacción de base de datos
        DB::beginTransaction();

        try {
            // PASO 1: Procesar cargo en la pasarela
            Log::info('Procesando pago', [
                'pedido_id' => $pedido->id,
                'monto' => $monto,
                'metodo' => $methodName,
            ]);

            $chargeResult = $this->gateway->charge($pedido, $monto, $tokenData);

            // Verificar si el cargo fue exitoso
            if (!$chargeResult['success']) {
                throw new Exception($chargeResult['message'] ?? 'Error al procesar el pago');
            }

            // PASO 2: Crear registro de Pago
            $pago = Pago::create([
                'pedido_id' => $pedido->id,
                'monto' => $monto,
                'metodo' => $methodName,
                'estado' => 'confirmado',
                'referencia_externa' => $chargeResult['transaction_id'],
                'fecha_pago' => now(),
            ]);

            Log::info('Pago registrado en BD', [
                'pago_id' => $pago->id,
                'transaction_id' => $chargeResult['transaction_id'],
            ]);

            // PASO 3: Actualizar saldo del pedido
            $pedido->monto_abonado += $monto;
            $pedido->saldo_pendiente = $pedido->total_calculado - $pedido->monto_abonado;

            // PASO 4: Cambiar estado del pedido si está completamente pagado
            if ($pedido->saldo_pendiente <= 0) {
                $oldStatus = $pedido->status;
                
                // Si el pedido está pendiente, cambiarlo a confirmado
                if ($pedido->status === 'pendiente') {
                    $pedido->status = 'en_produccion';
                }

                Log::info('Pedido completamente pagado', [
                    'pedido_id' => $pedido->id,
                    'old_status' => $oldStatus,
                    'new_status' => $pedido->status,
                ]);

                // Registrar en auditoría
                $pedido->auditAction(
                    action: 'fully_paid',
                    data: [
                        'pago_id' => $pago->id,
                        'monto' => $monto,
                        'old_status' => $oldStatus,
                        'new_status' => $pedido->status,
                    ]
                );
            }

            $pedido->save();

            // Registrar acción de pago en auditoría
            $pedido->auditAction(
                action: 'payment_received',
                data: [
                    'pago_id' => $pago->id,
                    'monto' => $monto,
                    'metodo' => $methodName,
                    'transaction_id' => $chargeResult['transaction_id'],
                    'saldo_anterior' => $pedido->saldo_pendiente + $monto,
                    'saldo_nuevo' => $pedido->saldo_pendiente,
                ]
            );

            // Confirmar transacción
            DB::commit();

            Log::info('Pago procesado exitosamente', [
                'pago_id' => $pago->id,
                'pedido_id' => $pedido->id,
                'saldo_pendiente' => $pedido->saldo_pendiente,
            ]);

            return $pago;

        } catch (Exception $e) {
            // Revertir transacción en caso de error
            DB::rollBack();

            Log::error('Error al procesar pago', [
                'pedido_id' => $pedido->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new Exception("Error al procesar el pago: {$e->getMessage()}");
        }
    }

    /**
     * Validar que el pago pueda procesarse
     *
     * @param Pedido $pedido
     * @param float $monto
     * @throws Exception Si las validaciones fallan
     */
    protected function validatePayment(Pedido $pedido, float $monto): void
    {
        // Validar que el monto sea positivo
        if ($monto <= 0) {
            throw new Exception('El monto del pago debe ser mayor a cero');
        }

        // Validar que el pedido tenga saldo pendiente
        if ($pedido->saldo_pendiente <= 0) {
            throw new Exception('Este pedido no tiene saldo pendiente');
        }

        // Validar que el monto no exceda el saldo pendiente
        if ($monto > $pedido->saldo_pendiente) {
            throw new Exception(
                "El monto a pagar ($" . number_format($monto, 2) . ") " .
                "excede el saldo pendiente ($" . number_format($pedido->saldo_pendiente, 2) . ")"
            );
        }

        // Validar que el pedido no esté cancelado
        if ($pedido->status === 'cancelado') {
            throw new Exception('No se puede procesar un pago para un pedido cancelado');
        }

        // Validar que el pedido no esté entregado (pagos posteriores a la entrega)
        if ($pedido->status === 'entregado' && $pedido->saldo_pendiente <= 0) {
            throw new Exception('Este pedido ya fue entregado y no tiene saldo pendiente');
        }
    }

    /**
     * Obtener el saldo pendiente de un pedido
     */
    public function getPendingBalance(Pedido $pedido): float
    {
        return $pedido->saldo_pendiente;
    }

    /**
     * Verificar si un pedido está completamente pagado
     */
    public function isFullyPaid(Pedido $pedido): bool
    {
        return $pedido->saldo_pendiente <= 0;
    }
}
