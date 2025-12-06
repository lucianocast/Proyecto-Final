<?php

namespace App\Services;

use App\Models\Insumo;
use App\Models\OrdenDeCompra;
use App\Models\OrdenDeCompraItem;
use App\Models\Proveedor;
use App\Models\Pedido;
use App\Models\Receta;
use App\Models\AuditLog;
use App\Notifications\OrdenCompraAutomaticaNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Servicio para el Proceso Automatizado Inteligente #1
 * Planificación y Sugerencia de Compras Automáticas
 * 
 * Funcionalidades:
 * - Monitoreo continuo de stock crítico
 * - Análisis de demanda futura (pedidos confirmados + histórico)
 * - Ajuste dinámico de niveles mínimos según estacionalidad
 * - Evaluación automática de proveedores (precio, cumplimiento, tiempo)
 * - Generación automática de órdenes de compra al mejor proveedor
 * - Notificación vía email (Mailtrap) para confirmación
 */
class PlanificacionComprasService
{
    /**
     * Ejecutar análisis completo y generar órdenes de compra si es necesario
     * Este método se ejecuta automáticamente vía comando programado
     */
    public function ejecutarAnalisisAutomatico(): array
    {
        Log::info('🤖 [Proceso Inteligente #1] Iniciando análisis de planificación de compras');

        $resultados = [
            'insumos_analizados' => 0,
            'insumos_criticos' => 0,
            'ordenes_generadas' => 0,
            'ordenes_creadas' => [],
            'errores' => [],
        ];

        try {
            // 1. Obtener todos los insumos activos
            $insumos = Insumo::where('activo', true)->get();
            $resultados['insumos_analizados'] = $insumos->count();

            foreach ($insumos as $insumo) {
                try {
                    // 2. Calcular nivel crítico dinámico
                    $nivelCriticoDinamico = $this->calcularNivelCriticoDinamico($insumo);

                    // 3. Verificar si está en nivel crítico
                    if ($insumo->cantidad_disponible <= $nivelCriticoDinamico) {
                        $resultados['insumos_criticos']++;

                        // 4. Calcular demanda futura estimada
                        $demandaEstimada = $this->calcularDemandaFutura($insumo);

                        // 5. Calcular cantidad óptima a comprar
                        $cantidadOptima = $this->calcularCantidadOptima($insumo, $demandaEstimada, $nivelCriticoDinamico);

                        Log::info("📊 Análisis de {$insumo->nombre}", [
                            'stock_actual' => $insumo->cantidad_disponible,
                            'nivel_critico' => $nivelCriticoDinamico,
                            'demanda_estimada' => $demandaEstimada,
                            'cantidad_optima' => $cantidadOptima,
                        ]);

                        if ($cantidadOptima > 0) {
                            // 6. Evaluar y seleccionar mejor proveedor
                            $mejorProveedor = $this->evaluarMejorProveedor($insumo);

                            if ($mejorProveedor) {
                                // 7. Generar orden de compra automática
                                $orden = $this->generarOrdenCompraAutomatica($insumo, $cantidadOptima, $mejorProveedor, $demandaEstimada);

                                if ($orden) {
                                    $resultados['ordenes_generadas']++;
                                    $resultados['ordenes_creadas'][] = [
                                        'orden_id' => $orden->id,
                                        'insumo' => $insumo->nombre,
                                        'cantidad' => $cantidadOptima,
                                        'proveedor' => $mejorProveedor->nombre_empresa,
                                        'total' => $orden->total_calculado,
                                    ];

                                    // 8. Enviar notificación vía Mailtrap
                                    $this->enviarNotificacionOrdenAutomatica($orden, $insumo, $mejorProveedor);
                                }
                            } else {
                                $resultados['errores'][] = "Insumo {$insumo->nombre}: No hay proveedores disponibles";
                                Log::warning("⚠️ {$insumo->nombre}: Sin proveedores asignados");
                            }
                        } else {
                            $resultados['errores'][] = "Insumo {$insumo->nombre}: Cantidad óptima calculada es 0 o negativa (stock suficiente considerando demanda)";
                            Log::info("ℹ️ {$insumo->nombre}: No requiere compra (cantidad óptima: {$cantidadOptima})");
                        }
                    }
                } catch (\Exception $e) {
                    $resultados['errores'][] = "Error procesando insumo {$insumo->nombre}: {$e->getMessage()}";
                    Log::error("Error procesando insumo {$insumo->id}", ['error' => $e->getMessage()]);
                }
            }

            // 9. Registrar en auditoría
            $this->registrarEnAuditoria($resultados);

            Log::info('✅ [Proceso Inteligente #1] Análisis completado', $resultados);

        } catch (\Exception $e) {
            Log::error('❌ [Proceso Inteligente #1] Error crítico', ['error' => $e->getMessage()]);
            $resultados['errores'][] = "Error crítico: {$e->getMessage()}";
        }

        return $resultados;
    }

    /**
     * Calcular nivel crítico dinámico según estacionalidad y rotación
     */
    protected function calcularNivelCriticoDinamico(Insumo $insumo): float
    {
        // Stock mínimo base definido
        $stockMinimoBase = $insumo->stock_minimo ?? 0;

        // Factor de ajuste por estacionalidad (análisis del mes actual)
        $mesActual = now()->month;
        $factorEstacional = $this->calcularFactorEstacional($insumo, $mesActual);

        // Factor de ajuste por frecuencia de reposición (últimos 30 días)
        $factorRotacion = $this->calcularFactorRotacion($insumo);

        // Cálculo dinámico: stock mínimo * factores de ajuste
        $nivelCriticoDinamico = $stockMinimoBase * $factorEstacional * $factorRotacion;

        // Garantizar un mínimo razonable (al menos el 80% del stock mínimo)
        return max($nivelCriticoDinamico, $stockMinimoBase * 0.8);
    }

    /**
     * Calcular factor estacional basado en histórico de ventas del mes
     */
    protected function calcularFactorEstacional(Insumo $insumo, int $mes): float
    {
        // Analizar consumo del mismo mes en años anteriores
        $consumoMesActual = DB::table('movimiento_stocks')
            ->where('insumo_id', $insumo->id)
            ->where('tipo', 'salida')
            ->whereMonth('created_at', $mes)
            ->whereYear('created_at', '>=', now()->subYears(2)->year)
            ->sum('cantidad');

        $consumoPromedioAnual = DB::table('movimiento_stocks')
            ->where('insumo_id', $insumo->id)
            ->where('tipo', 'salida')
            ->whereYear('created_at', '>=', now()->subYears(2)->year)
            ->avg('cantidad') ?? 1;

        // Si el consumo del mes es mayor al promedio, aumentar el nivel crítico
        $factor = $consumoPromedioAnual > 0 ? ($consumoMesActual / $consumoPromedioAnual) / 12 : 1;

        // Limitar factor entre 0.8 y 1.5 (no bajar mucho ni subir demasiado)
        return max(0.8, min(1.5, $factor ?: 1));
    }

    /**
     * Calcular factor de rotación basado en frecuencia de uso
     */
    protected function calcularFactorRotacion(Insumo $insumo): float
    {
        $ultimoMes = now()->subDays(30);

        $movimientos = DB::table('movimiento_stocks')
            ->where('insumo_id', $insumo->id)
            ->where('tipo', 'salida')
            ->where('created_at', '>=', $ultimoMes)
            ->count();

        // Más movimientos = mayor rotación = necesita más stock
        if ($movimientos >= 15) return 1.3; // Alta rotación
        if ($movimientos >= 8) return 1.1;  // Media rotación
        if ($movimientos >= 3) return 1.0;  // Rotación normal
        return 0.9; // Baja rotación
    }

    /**
     * Calcular demanda futura estimada (próximos 15 días)
     */
    protected function calcularDemandaFutura(Insumo $insumo): float
    {
        $fechaLimite = now()->addDays(15);

        // 1. Demanda confirmada: pedidos confirmados que aún no se produjeron
        $pedidosConfirmados = Pedido::whereIn('status', ['confirmado', 'en_produccion'])
            ->where('fecha_entrega', '<=', $fechaLimite)
            ->with('items.producto.receta.insumos')
            ->get();

        $demandaConfirmada = 0;
        foreach ($pedidosConfirmados as $pedido) {
            foreach ($pedido->items as $item) {
                if ($item->producto && $item->producto->receta) {
                    $receta = $item->producto->receta;
                    $insumoEnReceta = $receta->insumos->where('id', $insumo->id)->first();
                    
                    if ($insumoEnReceta) {
                        $cantidadPorUnidad = $insumoEnReceta->pivot->cantidad ?? 0;
                        $demandaConfirmada += $cantidadPorUnidad * $item->cantidad;
                    }
                }
            }
        }

        // 2. Demanda proyectada: promedio de consumo de últimos 30 días * 15 días
        $consumoPromedioDiario = DB::table('movimiento_stocks')
            ->where('insumo_id', $insumo->id)
            ->where('tipo', 'salida')
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('cantidad') / 30;

        $demandaProyectada = $consumoPromedioDiario * 15;

        // Retornar la mayor entre confirmada y proyectada (más conservador)
        return max($demandaConfirmada, $demandaProyectada);
    }

    /**
     * Calcular cantidad óptima a comprar
     */
    protected function calcularCantidadOptima(Insumo $insumo, float $demandaFutura, float $nivelCritico): float
    {
        $stockActual = $insumo->cantidad_disponible;

        // Cantidad necesaria = Demanda futura + Nivel crítico - Stock actual
        $cantidadNecesaria = ($demandaFutura + $nivelCritico) - $stockActual;

        // Redondear al múltiplo superior de cantidad_por_bulto si existe
        if ($insumo->proveedores()->first() && $insumo->proveedores()->first()->pivot->cantidad_por_bulto) {
            $cantidadPorBulto = $insumo->proveedores()->first()->pivot->cantidad_por_bulto;
            $cantidadNecesaria = ceil($cantidadNecesaria / $cantidadPorBulto) * $cantidadPorBulto;
        }

        return max(0, $cantidadNecesaria);
    }

    /**
     * Evaluar y seleccionar el mejor proveedor para el insumo
     * Criterios: Precio, Historial de cumplimiento, Tiempo de entrega
     */
    protected function evaluarMejorProveedor(Insumo $insumo): ?Proveedor
    {
        $proveedoresDelInsumo = $insumo->proveedores()
            ->where('activo', true)
            ->withPivot(['precio', 'tiempo_entrega_dias'])
            ->get();

        if ($proveedoresDelInsumo->isEmpty()) {
            return null;
        }

        $mejorProveedor = null;
        $mejorPuntaje = -1;

        foreach ($proveedoresDelInsumo as $proveedor) {
            // 1. Puntaje por precio (menor precio = mejor puntaje)
            $precio = $proveedor->pivot->precio ?? 999999;
            $precioMinimo = $proveedoresDelInsumo->min('pivot.precio') ?: 1;
            $puntajePrecio = $precioMinimo > 0 ? (10 * $precioMinimo / $precio) : 5;

            // 2. Puntaje por cumplimiento (% de OC entregadas a tiempo)
            $puntajeCumplimiento = $this->calcularPuntajeCumplimiento($proveedor);

            // 3. Puntaje por tiempo de entrega (menos días = mejor puntaje)
            $tiempoEntrega = $proveedor->pivot->tiempo_entrega_dias ?? 7;
            $puntajeTiempo = max(0, 10 - ($tiempoEntrega / 2));

            // Puntaje total ponderado: 40% precio, 35% cumplimiento, 25% tiempo
            $puntajeTotal = ($puntajePrecio * 0.4) + ($puntajeCumplimiento * 0.35) + ($puntajeTiempo * 0.25);

            if ($puntajeTotal > $mejorPuntaje) {
                $mejorPuntaje = $puntajeTotal;
                $mejorProveedor = $proveedor;
            }
        }

        return $mejorProveedor;
    }

    /**
     * Calcular puntaje de cumplimiento del proveedor
     */
    protected function calcularPuntajeCumplimiento(Proveedor $proveedor): float
    {
        $ordenesRecibidas = $proveedor->ordenesDeCompra()
            ->where('status', 'Recibida')
            ->where('created_at', '>=', now()->subMonths(6))
            ->count();

        $ordenesCanceladas = $proveedor->ordenesDeCompra()
            ->where('status', 'Cancelada')
            ->where('created_at', '>=', now()->subMonths(6))
            ->count();

        $totalOrdenes = $ordenesRecibidas + $ordenesCanceladas;

        if ($totalOrdenes === 0) {
            return 7; // Puntaje neutral para proveedores sin historial
        }

        // Porcentaje de cumplimiento * 10
        return ($ordenesRecibidas / $totalOrdenes) * 10;
    }

    /**
     * Generar orden de compra automática
     */
    protected function generarOrdenCompraAutomatica(Insumo $insumo, float $cantidad, Proveedor $proveedor, float $demandaEstimada): ?OrdenDeCompra
    {
        try {
            DB::beginTransaction();

            $precioUnitario = $proveedor->insumos()
                ->where('insumo_id', $insumo->id)
                ->first()
                ->pivot
                ->precio ?? 0;

            $totalCalculado = $cantidad * $precioUnitario;

            $tiempoEntrega = $proveedor->insumos()
                ->where('insumo_id', $insumo->id)
                ->first()
                ->pivot
                ->tiempo_entrega_dias ?? 7;

            // Crear orden de compra
            $orden = OrdenDeCompra::create([
                'proveedor_id' => $proveedor->id,
                'user_id' => 1, // Usuario del sistema automatizado
                'status' => 'Pendiente',
                'fecha_emision' => now(),
                'fecha_entrega_esperada' => now()->addDays($tiempoEntrega),
                'total_calculado' => $totalCalculado,
            ]);

            // Crear item de la orden
            OrdenDeCompraItem::create([
                'orden_de_compra_id' => $orden->id,
                'insumo_id' => $insumo->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'subtotal' => $totalCalculado,
            ]);

            // Registrar en auditoría
            AuditLog::create([
                'user_id' => 1,
                'action' => 'orden_compra_automatica',
                'auditable_type' => OrdenDeCompra::class,
                'auditable_id' => $orden->id,
                'old_values' => null,
                'new_values' => [
                    'insumo' => $insumo->nombre,
                    'cantidad' => (float) $cantidad,
                    'proveedor' => $proveedor->nombre_empresa,
                    'precio_unitario' => (float) $precioUnitario,
                    'total' => (float) $totalCalculado,
                    'demanda_estimada' => (float) $demandaEstimada,
                    'stock_actual' => (float) ($insumo->cantidad_disponible ?? 0),
                ],
                'justification' => "Orden de compra generada automaticamente por el proceso inteligente debido a stock critico",
            ]);

            DB::commit();

            return $orden;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error generando orden de compra automática', [
                'insumo_id' => $insumo->id,
                'proveedor_id' => $proveedor->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Enviar notificación vía email (Mailtrap) para confirmación
     */
    protected function enviarNotificacionOrdenAutomatica(OrdenDeCompra $orden, Insumo $insumo, Proveedor $proveedor): void
    {
        try {
            // Buscar administradores para notificar
            $administradores = \App\Models\User::whereHas('roles', function($q) {
                $q->where('name', 'administrador');
            })->get();

            foreach ($administradores as $admin) {
                $admin->notify(new OrdenCompraAutomaticaNotification($orden, $insumo, $proveedor));
            }

            Log::info('📧 Notificación enviada vía Mailtrap', [
                'orden_id' => $orden->id,
                'destinatarios' => $administradores->pluck('email')->toArray()
            ]);

        } catch (\Exception $e) {
            Log::error('Error enviando notificación', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Registrar resumen en auditoría
     */
    protected function registrarEnAuditoria(array $resultados): void
    {
        AuditLog::create([
            'user_id' => 1, // Sistema automatizado
            'action' => 'proceso_automatico_compras',
            'auditable_type' => null,
            'auditable_id' => null,
            'old_values' => null,
            'new_values' => $resultados,
            'justification' => 'Ejecución automática del proceso inteligente de planificación de compras',
        ]);
    }
}
