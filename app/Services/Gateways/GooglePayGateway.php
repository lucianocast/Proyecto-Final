<?php

namespace App\Services\Gateways;

use App\Contracts\PaymentGateway;
use App\Models\Pedido;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Implementación de Google Pay como pasarela de pago
 * 
 * Procesa pagos a través de Google Pay API
 */
class GooglePayGateway implements PaymentGateway
{
    protected string $apiEndpoint;
    protected string $merchantId;
    protected string $secretKey;
    protected bool $sandboxMode;

    public function __construct()
    {
        // Cargar configuración desde config/services.php
        $this->apiEndpoint = config('services.google_pay.api_endpoint');
        $this->merchantId = config('services.google_pay.merchant_id');
        $this->secretKey = config('services.google_pay.secret_key');
        $this->sandboxMode = config('services.google_pay.sandbox', true);
    }

    /**
     * Procesar cargo en Google Pay
     *
     * @param Pedido $pedido
     * @param float $monto
     * @param array $tokenData Debe contener 'token' y opcionalmente 'email', 'billing_address'
     * @return array
     */
    public function charge(Pedido $pedido, float $monto, array $tokenData): array
    {
        try {
            // Validar que el token esté presente
            if (!isset($tokenData['token'])) {
                return [
                    'success' => false,
                    'transaction_id' => '',
                    'message' => 'Token de pago no proporcionado',
                ];
            }

            // Preparar datos para la API
            $payload = [
                'merchant_id' => $this->merchantId,
                'payment_token' => $tokenData['token'],
                'amount' => $monto,
                'currency' => 'ARS',
                'order_reference' => "PEDIDO-{$pedido->id}",
                'description' => "Pedido #{$pedido->id} - {$pedido->cliente->nombre}",
                'customer' => [
                    'email' => $tokenData['email'] ?? $pedido->cliente->email ?? 'cliente@ejemplo.com',
                    'name' => $pedido->cliente->nombre,
                ],
            ];

            // Agregar dirección de facturación si está presente
            if (isset($tokenData['billing_address'])) {
                $payload['billing_address'] = $tokenData['billing_address'];
            }

            Log::info('Google Pay charge attempt', [
                'pedido_id' => $pedido->id,
                'monto' => $monto,
                'sandbox' => $this->sandboxMode,
            ]);

            // Realizar llamada a la API
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->secretKey}",
                'Content-Type' => 'application/json',
                'X-Sandbox-Mode' => $this->sandboxMode ? 'true' : 'false',
            ])
            ->timeout(30)
            ->post($this->apiEndpoint . '/charges', $payload);

            // Procesar respuesta
            if ($response->successful()) {
                $data = $response->json();

                // Verificar estado del pago
                if (isset($data['status']) && $data['status'] === 'completed') {
                    Log::info('Google Pay charge successful', [
                        'transaction_id' => $data['transaction_id'] ?? 'N/A',
                        'pedido_id' => $pedido->id,
                    ]);

                    return [
                        'success' => true,
                        'transaction_id' => $data['transaction_id'] ?? $this->generateMockTransactionId(),
                        'message' => 'Pago procesado exitosamente con Google Pay',
                    ];
                }

                // Pago rechazado por la pasarela
                Log::warning('Google Pay charge declined', [
                    'pedido_id' => $pedido->id,
                    'reason' => $data['decline_reason'] ?? 'Unknown',
                ]);

                return [
                    'success' => false,
                    'transaction_id' => $data['transaction_id'] ?? '',
                    'message' => $data['decline_message'] ?? 'Pago rechazado por Google Pay',
                ];
            }

            // Error HTTP
            Log::error('Google Pay API error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'pedido_id' => $pedido->id,
            ]);

            return [
                'success' => false,
                'transaction_id' => '',
                'message' => 'Error al comunicarse con Google Pay. Por favor, intente nuevamente.',
            ];

        } catch (Exception $e) {
            // Capturar cualquier excepción y registrarla
            Log::error('Google Pay charge exception', [
                'message' => $e->getMessage(),
                'pedido_id' => $pedido->id,
                'trace' => $e->getTraceAsString(),
            ]);

            // En modo sandbox, simular éxito para desarrollo
            if ($this->sandboxMode && config('services.google_pay.simulate_success', false)) {
                return [
                    'success' => true,
                    'transaction_id' => $this->generateMockTransactionId(),
                    'message' => 'Pago simulado exitoso (Sandbox Mode)',
                ];
            }

            throw new Exception(
                "Error crítico al procesar pago con Google Pay: {$e->getMessage()}"
            );
        }
    }

    /**
     * Generar ID de transacción simulado para modo sandbox
     */
    protected function generateMockTransactionId(): string
    {
        return 'GPAY_' . strtoupper(uniqid()) . '_' . time();
    }
}
