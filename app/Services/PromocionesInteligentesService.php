<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\Pedido;
use App\Models\OrdenProduccion;
use App\Models\Promocion;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Servicio para el Proceso Automatizado Inteligente #2
 * Generación de Promociones Inteligentes para Días con Baja Producción
 * 
 * Funcionalidades:
 * - Monitoreo permanente del calendario de pedidos y volumen de producción
 * - Cálculo dinámico del umbral de actividad mínima esperado por día
 * - Identificación automática de días con baja carga de trabajo
 * - Activación inmediata de promociones comerciales específicas
 * - Publicación automática en el catálogo web
 * - Registro completo en historial de auditoría
 */
class PromocionesInteligentesService
{
    /**
     * Ejecutar análisis de días con baja producción y activar promociones
     * Este método se ejecuta automáticamente vía comando programado (diario)
     */
    public function ejecutarAnalisisPromociones(): array
    {
        Log::info('🤖 [Proceso Inteligente #2] Iniciando análisis de promociones inteligentes');

        $resultados = [
            'dias_analizados' => 0,
            'dias_baja_produccion' => 0,
            'promociones_activadas' => 0,
            'promociones_creadas' => [],
            'errores' => [],
        ];

        try {
            // Analizar próximos 15 días
            $diasAAnalizar = 15;
            $resultados['dias_analizados'] = $diasAAnalizar;

            for ($i = 1; $i <= $diasAAnalizar; $i++) {
                $fecha = now()->addDays($i);
                
                try {
                    // 1. Calcular carga de trabajo del día
                    $cargaTrabajo = $this->calcularCargaTrabajo($fecha);

                    // 2. Calcular umbral mínimo dinámico
                    $umbralMinimo = $this->calcularUmbralMinimoDinamico($fecha);

                    // 3. Verificar si está por debajo del umbral
                    if ($cargaTrabajo < $umbralMinimo) {
                        $resultados['dias_baja_produccion']++;

                        Log::info("📉 Día con baja producción detectado: {$fecha->format('d/m/Y')}", [
                            'carga_trabajo' => $cargaTrabajo,
                            'umbral_minimo' => $umbralMinimo,
                            'diferencia' => $umbralMinimo - $cargaTrabajo,
                        ]);

                        // 4. Generar promoción inteligente para ese día
                        $promocion = $this->generarPromocionInteligente($fecha, $cargaTrabajo, $umbralMinimo);

                        if ($promocion) {
                            $resultados['promociones_activadas']++;
                            $resultados['promociones_creadas'][] = [
                                'fecha' => $fecha->format('d/m/Y'),
                                'promocion_id' => $promocion->id,
                                'tipo' => $promocion->tipo_descuento,
                                'descuento' => $promocion->valor_descuento,
                                'productos' => $promocion->productos->pluck('nombre')->toArray(),
                            ];
                        }
                    }
                } catch (\Exception $e) {
                    $resultados['errores'][] = "Error analizando día {$fecha->format('d/m/Y')}: {$e->getMessage()}";
                    Log::error("Error analizando día", ['fecha' => $fecha, 'error' => $e->getMessage()]);
                }
            }

            // 5. Registrar en auditoría
            $this->registrarEnAuditoria($resultados);

            Log::info('✅ [Proceso Inteligente #2] Análisis completado', $resultados);

        } catch (\Exception $e) {
            Log::error('❌ [Proceso Inteligente #2] Error crítico', ['error' => $e->getMessage()]);
            $resultados['errores'][] = "Error crítico: {$e->getMessage()}";
        }

        return $resultados;
    }

    /**
     * Calcular la carga de trabajo de un día específico
     * Basado en: pedidos confirmados + órdenes de producción programadas
     */
    protected function calcularCargaTrabajo(Carbon $fecha): float
    {
        // 1. Pedidos con fecha de entrega en ese día
        $pedidosDelDia = Pedido::whereDate('fecha_entrega', $fecha)
            ->whereIn('status', ['confirmado', 'en_produccion', 'pendiente'])
            ->with('items')
            ->get();

        $cargaPedidos = 0;
        foreach ($pedidosDelDia as $pedido) {
            foreach ($pedido->items as $item) {
                // Cada item suma a la carga (cantidad * complejidad del producto)
                $complejidad = $this->obtenerComplejidadProducto($item->producto);
                $cargaPedidos += $item->cantidad * $complejidad;
            }
        }

        // 2. Órdenes de producción programadas para ese día
        $ordenesProduccion = OrdenProduccion::whereDate('fecha_limite', $fecha)
            ->whereIn('status', ['pendiente', 'en_proceso'])
            ->get();

        $cargaOrdenes = $ordenesProduccion->sum('cantidad_producto') * 2; // Factor 2 por ser producción directa

        // Carga total (normalizada entre 0-100)
        $cargaTotal = $cargaPedidos + $cargaOrdenes;

        return $cargaTotal;
    }

    /**
     * Obtener complejidad de un producto (basado en cantidad de insumos en receta)
     */
    protected function obtenerComplejidadProducto($producto): float
    {
        if (!$producto || !$producto->receta) {
            return 1; // Complejidad mínima
        }

        $cantidadInsumos = $producto->receta->insumos()->count();

        // Más insumos = mayor complejidad
        if ($cantidadInsumos >= 10) return 3;
        if ($cantidadInsumos >= 6) return 2;
        if ($cantidadInsumos >= 3) return 1.5;
        return 1;
    }

    /**
     * Calcular umbral mínimo dinámico basado en histórico y estacionalidad
     */
    protected function calcularUmbralMinimoDinamico(Carbon $fecha): float
    {
        // Promedio de carga de trabajo del mismo día de la semana en últimas 4 semanas
        $diaSemana = $fecha->dayOfWeek;
        
        $promedioHistorico = 0;
        for ($i = 1; $i <= 4; $i++) {
            $fechaPasada = now()->subWeeks($i)->startOfWeek()->addDays($diaSemana);
            $promedioHistorico += $this->obtenerCargaHistorica($fechaPasada);
        }
        $promedioHistorico = $promedioHistorico / 4;

        // Factor estacional (mes actual)
        $factorEstacional = $this->calcularFactorEstacionalVentas($fecha->month);

        // Umbral = 60% del promedio histórico ajustado por estacionalidad
        $umbral = $promedioHistorico * 0.6 * $factorEstacional;

        // Garantizar un mínimo razonable
        return max($umbral, 10); // Al menos 10 unidades de carga
    }

    /**
     * Obtener carga histórica de una fecha pasada
     */
    protected function obtenerCargaHistorica(Carbon $fecha): float
    {
        $pedidos = Pedido::whereDate('fecha_entrega', $fecha)
            ->whereIn('status', ['entregado', 'completado'])
            ->with('items')
            ->get();

        $carga = 0;
        foreach ($pedidos as $pedido) {
            foreach ($pedido->items as $item) {
                $complejidad = $this->obtenerComplejidadProducto($item->producto);
                $carga += $item->cantidad * $complejidad;
            }
        }

        return $carga;
    }

    /**
     * Calcular factor estacional de ventas (1 = normal, >1 = alta temporada, <1 = baja)
     */
    protected function calcularFactorEstacionalVentas(int $mes): float
    {
        // Análisis de ventas del mismo mes en años anteriores
        $ventasMes = Pedido::whereMonth('created_at', $mes)
            ->whereYear('created_at', '>=', now()->subYears(2)->year)
            ->whereIn('status', ['entregado', 'completado'])
            ->sum('total');

        $ventasPromedioAnual = Pedido::whereYear('created_at', '>=', now()->subYears(2)->year)
            ->whereIn('status', ['entregado', 'completado'])
            ->sum('total') / 12;

        $factor = $ventasPromedioAnual > 0 ? $ventasMes / $ventasPromedioAnual : 1;

        // Limitar entre 0.7 y 1.4
        return max(0.7, min(1.4, $factor ?: 1));
    }

    /**
     * Generar promoción inteligente para el día con baja producción
     */
    protected function generarPromocionInteligente(Carbon $fecha, float $cargaActual, float $umbral): ?Promocion
    {
        try {
            DB::beginTransaction();

            // Verificar si ya existe una promoción activa para ese día
            $promocionExistente = Promocion::where('fecha_inicio', '<=', $fecha)
                ->where('fecha_fin', '>=', $fecha)
                ->where('activo', true)
                ->where('generada_automaticamente', true)
                ->first();

            if ($promocionExistente) {
                Log::info("Ya existe promoción para {$fecha->format('d/m/Y')}, omitiendo...");
                DB::rollBack();
                return null;
            }

            // Calcular porcentaje de descuento basado en la diferencia
            $diferencia = $umbral - $cargaActual;
            $porcentajeDescuento = $this->calcularDescuentoOptimo($diferencia, $umbral);

            // Seleccionar productos con insumos disponibles
            $productosElegibles = $this->seleccionarProductosParaPromocion();

            if ($productosElegibles->isEmpty()) {
                Log::warning("No hay productos elegibles con insumos disponibles");
                DB::rollBack();
                return null;
            }

            // Crear promoción
            $promocion = Promocion::create([
                'nombre' => "Promoción Especial - " . $fecha->isoFormat('dddd D [de] MMMM'),
                'descripcion' => "¡Aprovecha esta oferta especial! Descuento automático en productos seleccionados para pedidos del " . $fecha->format('d/m/Y'),
                'tipo_descuento' => 'porcentaje',
                'valor_descuento' => $porcentajeDescuento,
                'fecha_inicio' => now(),
                'fecha_fin' => $fecha->copy()->endOfDay(),
                'activo' => true,
                'condiciones' => json_encode([
                    'fecha_entrega_especifica' => $fecha->format('Y-m-d'),
                    'generada_por_sistema' => true,
                    'carga_detectada' => $cargaActual,
                    'umbral_referencia' => $umbral,
                ]),
                'generada_automaticamente' => true,
            ]);

            // Asociar productos
            $promocion->productos()->attach($productosElegibles->pluck('id'));

            // Registrar en auditoría
            AuditLog::create([
                'user_id' => 1,
                'action' => 'promocion_automatica_creada',
                'auditable_type' => Promocion::class,
                'auditable_id' => $promocion->id,
                'old_values' => null,
                'new_values' => [
                    'fecha_objetivo' => $fecha->format('Y-m-d'),
                    'descuento' => $porcentajeDescuento,
                    'productos' => $productosElegibles->pluck('nombre')->toArray(),
                    'carga_actual' => $cargaActual,
                    'umbral_minimo' => $umbral,
                ],
                'justification' => "Promoción generada automáticamente para incentivar ventas en día con baja producción prevista",
            ]);

            DB::commit();

            Log::info("✅ Promoción creada: {$promocion->nombre}", [
                'descuento' => $porcentajeDescuento,
                'productos' => $productosElegibles->count(),
            ]);

            return $promocion;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error generando promoción inteligente', [
                'fecha' => $fecha,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Calcular descuento óptimo según la diferencia con el umbral
     */
    protected function calcularDescuentoOptimo(float $diferencia, float $umbral): float
    {
        // Porcentaje de déficit
        $porcentajeDeficit = ($diferencia / $umbral) * 100;

        // Escala de descuentos
        if ($porcentajeDeficit >= 60) return 25; // Muy baja actividad: 25%
        if ($porcentajeDeficit >= 40) return 20; // Baja actividad: 20%
        if ($porcentajeDeficit >= 25) return 15; // Moderada baja: 15%
        return 10; // Ligeramente baja: 10%
    }

    /**
     * Seleccionar productos elegibles para la promoción
     * Criterio: productos con insumos disponibles en stock
     */
    protected function seleccionarProductosParaPromocion()
    {
        $productos = Producto::where('activo', true)
            ->with(['receta.insumos'])
            ->get();

        $productosElegibles = collect();

        foreach ($productos as $producto) {
            if (!$producto->receta) {
                continue;
            }

            // Verificar si todos los insumos tienen stock suficiente (al menos 5 unidades)
            $tieneStock = true;
            foreach ($producto->receta->insumos as $insumo) {
                $cantidadRequerida = $insumo->pivot->cantidad ?? 0;
                
                if ($insumo->cantidad_disponible < ($cantidadRequerida * 5)) {
                    $tieneStock = false;
                    break;
                }
            }

            if ($tieneStock) {
                $productosElegibles->push($producto);
            }
        }

        // Seleccionar hasta 5 productos (los más vendidos si hay más)
        if ($productosElegibles->count() > 5) {
            // Ordenar por popularidad (cantidad de veces pedido en últimos 30 días)
            $productosElegibles = $productosElegibles->sortByDesc(function($producto) {
                return DB::table('pedido_items')
                    ->where('producto_id', $producto->id)
                    ->where('created_at', '>=', now()->subDays(30))
                    ->sum('cantidad');
            })->take(5);
        }

        return $productosElegibles;
    }

    /**
     * Registrar resumen en auditoría
     */
    protected function registrarEnAuditoria(array $resultados): void
    {
        AuditLog::create([
            'user_id' => 1,
            'action' => 'proceso_automatico_promociones',
            'auditable_type' => null,
            'auditable_id' => null,
            'old_values' => null,
            'new_values' => $resultados,
            'justification' => 'Ejecución automática del proceso inteligente de generación de promociones',
        ]);
    }
}
