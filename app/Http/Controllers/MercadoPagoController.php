<?php

namespace App\Http\Controllers;

use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MercadoPagoController extends Controller
{
    protected MercadoPagoService $mercadoPagoService;

    public function __construct(MercadoPagoService $mercadoPagoService)
    {
        $this->mercadoPagoService = $mercadoPagoService;
    }

    /**
     * Página de éxito después del pago
     */
    public function success(Request $request)
    {
        $paymentId = $request->query('payment_id');
        $status = $request->query('status');
        $externalReference = $request->query('external_reference');

        return view('mercadopago.success', compact('paymentId', 'status', 'externalReference'));
    }

    /**
     * Página de fallo después del pago
     */
    public function failure(Request $request)
    {
        return view('mercadopago.failure');
    }

    /**
     * Página de pago pendiente
     */
    public function pending(Request $request)
    {
        return view('mercadopago.pending');
    }

    /**
     * Webhook para notificaciones de Mercado Pago
     */
    public function webhook(Request $request)
    {
        Log::info('Mercado Pago Webhook received', $request->all());

        $success = $this->mercadoPagoService->processWebhook($request->all());

        if ($success) {
            return response()->json(['status' => 'success'], 200);
        }

        return response()->json(['status' => 'error'], 400);
    }
}
