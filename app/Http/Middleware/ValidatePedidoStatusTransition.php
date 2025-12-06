<?php

namespace App\Http\Middleware;

use App\Models\Pedido;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidatePedidoStatusTransition
{
    /**
     * Transiciones válidas de estados de pedido.
     * Define qué estados pueden cambiar a qué otros estados.
     */
    private const VALID_TRANSITIONS = [
        'pendiente' => ['en_produccion', 'cancelado'],
        'en_produccion' => ['listo', 'cancelado'],
        'listo' => ['entregado', 'cancelado'],
        'entregado' => [], // Estado final, no permite cambios
        'cancelado' => [], // Estado final, no permite cambios
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo validar en actualizaciones que incluyan el campo status
        if ($request->isMethod('PUT') || $request->isMethod('PATCH')) {
            $newStatus = $request->input('status');
            
            if ($newStatus) {
                $pedidoId = $request->route('pedido') ?? $request->route('record');
                
                if ($pedidoId) {
                    $pedido = Pedido::find($pedidoId);
                    
                    if ($pedido && $pedido->status !== $newStatus) {
                        $currentStatus = $pedido->status;
                        $allowedTransitions = self::VALID_TRANSITIONS[$currentStatus] ?? [];
                        
                        if (!in_array($newStatus, $allowedTransitions)) {
                            return response()->json([
                                'message' => "No se puede cambiar el estado de '{$currentStatus}' a '{$newStatus}'. Transiciones válidas: " . implode(', ', $allowedTransitions),
                                'current_status' => $currentStatus,
                                'attempted_status' => $newStatus,
                                'allowed_transitions' => $allowedTransitions,
                            ], 422);
                        }
                    }
                }
            }
        }

        return $next($request);
    }
}
