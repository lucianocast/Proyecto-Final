<?php

namespace App\Services;

use App\Models\Pago;
use App\Models\Pedido;
use MercadoPago\SDK;
use MercadoPago\Preference;
use MercadoPago\Item;
use MercadoPago\Payment;
use Exception;

class MercadoPagoService
{
    public function __construct()
    {
        // Inicializar SDK con el access token desde config
        SDK::setAccessToken(config('services.mercadopago.access_token'));
    }

    /**
     * Crear preferencia de pago para un pedido
     */
    public function createPreference(Pedido $pedido): array
    {
        try {
            $preference = new Preference();

            // Configurar items del pedido
            $items = [];
            foreach ($pedido->items as $item) {
                $mpItem = new Item();
                $mpItem->title = $item->productoVariante->producto->nombre . ' - ' . $item->productoVariante->descripcion;
                $mpItem->quantity = $item->cantidad;
                $mpItem->unit_price = (float) $item->precio_unitario;
                $items[] = $mpItem;
            }

            $preference->items = $items;

            // Configurar datos del pagador
            if ($pedido->cliente) {
                $preference->payer = [
                    'name' => $pedido->cliente->nombre,
                    'email' => $pedido->cliente->email ?? 'cliente@pasteleria.com',
                    'phone' => [
                        'number' => $pedido->cliente->telefono ?? '',
                    ],
                ];
            }

            // URLs de retorno
            $preference->back_urls = [
                'success' => route('mercadopago.success'),
                'failure' => route('mercadopago.failure'),
                'pending' => route('mercadopago.pending'),
            ];

            $preference->auto_return = 'approved';

            // Referencia externa (ID del pedido)
            $preference->external_reference = (string) $pedido->id;

            // Notificación webhook
            $preference->notification_url = route('mercadopago.webhook');

            // Guardar preferencia
            $preference->save();

            return [
                'success' => true,
                'preference_id' => $preference->id,
                'init_point' => $preference->init_point,
                'sandbox_init_point' => $preference->sandbox_init_point,
            ];

        } catch (Exception $e) {
            \Log::error('Error creating Mercado Pago preference: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Procesar notificación de webhook
     */
    public function processWebhook(array $data): bool
    {
        try {
            // Verificar que sea una notificación de pago
            if (!isset($data['type']) || $data['type'] !== 'payment') {
                return false;
            }

            // Obtener información del pago
            $paymentId = $data['data']['id'] ?? null;
            if (!$paymentId) {
                return false;
            }

            $payment = Payment::find_by_id($paymentId);
            
            // Obtener el pedido asociado
            $pedidoId = $payment->external_reference;
            $pedido = Pedido::find($pedidoId);

            if (!$pedido) {
                \Log::error("Pedido no encontrado: {$pedidoId}");
                return false;
            }

            // Procesar según el estado del pago
            if ($payment->status === 'approved') {
                // Crear registro de pago
                Pago::create([
                    'pedido_id' => $pedido->id,
                    'monto' => $payment->transaction_amount,
                    'metodo' => 'mercado_pago',
                    'estado' => 'confirmado',
                    'referencia_externa' => $payment->id,
                    'fecha_pago' => now(),
                ]);

                // Actualizar monto abonado del pedido
                $pedido->monto_abonado += $payment->transaction_amount;
                $pedido->saldo_pendiente = $pedido->total_calculado - $pedido->monto_abonado;
                $pedido->save();

                // Registrar en auditoría
                $pedido->auditAction(
                    action: 'payment_received',
                    data: [
                        'payment_id' => $payment->id,
                        'amount' => $payment->transaction_amount,
                        'method' => 'mercado_pago',
                    ]
                );

                return true;
            }

            return false;

        } catch (Exception $e) {
            \Log::error('Error processing Mercado Pago webhook: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener información de un pago
     */
    public function getPaymentInfo(string $paymentId): ?Payment
    {
        try {
            return Payment::find_by_id($paymentId);
        } catch (Exception $e) {
            \Log::error('Error getting payment info: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Reembolsar un pago
     */
    public function refundPayment(string $paymentId): array
    {
        try {
            $payment = Payment::find_by_id($paymentId);
            
            if (!$payment) {
                return [
                    'success' => false,
                    'error' => 'Pago no encontrado',
                ];
            }

            $refund = $payment->refund();

            return [
                'success' => true,
                'refund_id' => $refund->id,
            ];

        } catch (Exception $e) {
            \Log::error('Error refunding payment: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
