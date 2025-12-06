<?php

namespace App\Contracts;

use App\Models\Pedido;

/**
 * Contrato para pasarelas de pago
 * 
 * Define la interfaz que deben implementar todas las pasarelas de pago
 * del sistema (Google Pay, Mercado Pago, Stripe, etc.)
 */
interface PaymentGateway
{
    /**
     * Procesar un cargo en la pasarela de pago
     *
     * @param Pedido $pedido Pedido asociado al pago
     * @param float $monto Monto a cobrar
     * @param array $tokenData Datos del token de pago (token, metadata, etc.)
     * 
     * @return array Array con estructura:
     *   - success (bool): Indica si el pago fue exitoso
     *   - transaction_id (string): ID de la transacción en la pasarela
     *   - message (string): Mensaje descriptivo del resultado
     * 
     * @throws \Exception Si hay un error crítico en la comunicación
     */
    public function charge(Pedido $pedido, float $monto, array $tokenData): array;
}
