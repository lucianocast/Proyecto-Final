<?php

namespace App\Services;

use App\Models\OrdenDeCompra;
use App\Models\Proveedor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * UC-20: Servicio para calcular métricas de desempeño de proveedores
 */
class ProveedorPerformanceService
{
    /**
     * Calcula las métricas de desempeño para uno o varios proveedores
     * 
     * @param string $fechaDesde
     * @param string $fechaHasta
     * @param array|null $proveedoresIds Array de IDs o null para todos
     * @return Collection
     */
    public function calcularDesempeno(string $fechaDesde, string $fechaHasta, ?array $proveedoresIds = null): Collection
    {
        $query = OrdenDeCompra::query()
            ->whereBetween('fecha_emision', [$fechaDesde, $fechaHasta])
            ->with(['proveedor', 'items']);

        if ($proveedoresIds) {
            $query->whereIn('proveedor_id', $proveedoresIds);
        }

        $ordenes = $query->get()->groupBy('proveedor_id');

        $resultados = collect();

        foreach ($ordenes as $proveedorId => $ordenesProveedor) {
            $proveedor = $ordenesProveedor->first()->proveedor;
            
            if (!$proveedor) continue;

            $metricas = $this->calcularMetricasProveedor($ordenesProveedor);
            
            $resultados->push([
                'proveedor_id' => $proveedorId,
                'proveedor_nombre' => $proveedor->nombre_empresa,
                'proveedor_contacto' => $proveedor->nombre_contacto,
                'proveedor_email' => $proveedor->email_pedidos,
                'metricas' => $metricas,
            ]);
        }

        // Ordenar por cumplimiento de entrega (descendente)
        return $resultados->sortByDesc('metricas.cumplimiento_entrega');
    }

    /**
     * Calcula las métricas específicas para un proveedor
     */
    protected function calcularMetricasProveedor(Collection $ordenes): array
    {
        $totalOrdenes = $ordenes->count();
        $ordenesRecibidas = $ordenes->whereIn('status', ['recibida_total', 'recibida_parcial']);
        $ordenesRecibidas_total = $ordenesRecibidas->count();

        // 1. Cumplimiento de Entrega (%)
        // Órdenes entregadas a tiempo vs total de órdenes recibidas
        $ordenesATiempo = $ordenesRecibidas->filter(function ($orden) {
            if (!$orden->fecha_entrega_esperada) return false;
            
            // Buscar la última recepción de esta orden
            $ultimaRecepcion = DB::table('lotes')
                ->where('orden_de_compra_id', $orden->id)
                ->orderBy('fecha_vencimiento', 'desc')
                ->first();
            
            if (!$ultimaRecepcion) return false;
            
            // Comparar fecha de recepción con fecha esperada
            return \Carbon\Carbon::parse($ultimaRecepcion->created_at)
                ->lessThanOrEqualTo(\Carbon\Carbon::parse($orden->fecha_entrega_esperada));
        })->count();

        $cumplimientoEntrega = $ordenesRecibidas_total > 0 
            ? ($ordenesATiempo / $ordenesRecibidas_total) * 100 
            : 0;

        // 2. Precisión de Cantidades (%)
        // Cantidad recibida vs cantidad solicitada
        $cantidadSolicitadaTotal = 0;
        $cantidadRecibidaTotal = 0;

        foreach ($ordenesRecibidas as $orden) {
            foreach ($orden->items as $item) {
                $cantidadSolicitadaTotal += $item->cantidad;
                
                // Obtener cantidad recibida de lotes
                $cantidadRecibida = DB::table('lotes')
                    ->where('orden_de_compra_id', $orden->id)
                    ->where('insumo_id', $item->insumo_id)
                    ->sum('cantidad_ingresada');
                
                $cantidadRecibidaTotal += $cantidadRecibida;
            }
        }

        $precisionCantidades = $cantidadSolicitadaTotal > 0 
            ? ($cantidadRecibidaTotal / $cantidadSolicitadaTotal) * 100 
            : 0;

        // 3. Costo Promedio por Orden
        $costoTotal = $ordenes->sum('total_calculado');
        $costoPromedio = $totalOrdenes > 0 ? $costoTotal / $totalOrdenes : 0;

        // 4. Distribución de Estados
        $distribucionEstados = [
            'pendiente' => $ordenes->where('status', 'pendiente')->count(),
            'aprobada' => $ordenes->where('status', 'aprobada')->count(),
            'recibida_parcial' => $ordenes->where('status', 'recibida_parcial')->count(),
            'recibida_total' => $ordenes->where('status', 'recibida_total')->count(),
            'cancelada' => $ordenes->where('status', 'cancelada')->count(),
        ];

        // 5. Tiempos promedio
        $tiemposEntrega = [];
        foreach ($ordenesRecibidas as $orden) {
            $ultimaRecepcion = DB::table('lotes')
                ->where('orden_de_compra_id', $orden->id)
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($ultimaRecepcion && $orden->fecha_emision) {
                $diasEntrega = \Carbon\Carbon::parse($orden->fecha_emision)
                    ->diffInDays(\Carbon\Carbon::parse($ultimaRecepcion->created_at));
                $tiemposEntrega[] = $diasEntrega;
            }
        }

        $tiempoPromedioEntrega = count($tiemposEntrega) > 0 
            ? array_sum($tiemposEntrega) / count($tiemposEntrega) 
            : 0;

        // 6. Puntuación global (0-100)
        // Ponderación: 40% cumplimiento, 30% precisión, 20% sin canceladas, 10% rapidez
        $porcentajeSinCancelar = $totalOrdenes > 0 
            ? (($totalOrdenes - $distribucionEstados['cancelada']) / $totalOrdenes) * 100 
            : 100;
        
        $puntuacionRapidez = $tiempoPromedioEntrega > 0 
            ? max(0, 100 - ($tiempoPromedioEntrega * 2)) 
            : 50; // Neutro si no hay datos

        $puntuacionGlobal = (
            ($cumplimientoEntrega * 0.40) +
            ($precisionCantidades * 0.30) +
            ($porcentajeSinCancelar * 0.20) +
            ($puntuacionRapidez * 0.10)
        );

        return [
            'total_ordenes' => $totalOrdenes,
            'ordenes_recibidas' => $ordenesRecibidas_total,
            'ordenes_a_tiempo' => $ordenesATiempo,
            'cumplimiento_entrega' => round($cumplimientoEntrega, 2),
            'cantidad_solicitada_total' => $cantidadSolicitadaTotal,
            'cantidad_recibida_total' => $cantidadRecibidaTotal,
            'precision_cantidades' => round($precisionCantidades, 2),
            'costo_total' => $costoTotal,
            'costo_promedio_orden' => round($costoPromedio, 2),
            'tiempo_promedio_entrega_dias' => round($tiempoPromedioEntrega, 1),
            'distribucion_estados' => $distribucionEstados,
            'puntuacion_global' => round($puntuacionGlobal, 2),
        ];
    }

    /**
     * Obtiene el detalle de órdenes de un proveedor para análisis
     */
    public function obtenerDetalleOrdenes(int $proveedorId, string $fechaDesde, string $fechaHasta): Collection
    {
        return OrdenDeCompra::query()
            ->where('proveedor_id', $proveedorId)
            ->whereBetween('fecha_emision', [$fechaDesde, $fechaHasta])
            ->with(['items.insumo', 'user'])
            ->orderBy('fecha_emision', 'desc')
            ->get()
            ->map(function ($orden) {
                // Calcular días de retraso/adelanto
                $diasDiferencia = null;
                if ($orden->fecha_entrega_esperada) {
                    $ultimaRecepcion = DB::table('lotes')
                        ->where('orden_de_compra_id', $orden->id)
                        ->orderBy('created_at', 'desc')
                        ->first();
                    
                    if ($ultimaRecepcion) {
                        $diasDiferencia = \Carbon\Carbon::parse($orden->fecha_entrega_esperada)
                            ->diffInDays(\Carbon\Carbon::parse($ultimaRecepcion->created_at), false);
                    }
                }

                return [
                    'id' => $orden->id,
                    'fecha_emision' => $orden->fecha_emision,
                    'fecha_entrega_esperada' => $orden->fecha_entrega_esperada,
                    'status' => $orden->status,
                    'total' => $orden->total_calculado,
                    'items_count' => $orden->items->count(),
                    'dias_diferencia' => $diasDiferencia,
                    'usuario' => $orden->user?->name,
                ];
            });
    }

    /**
     * Genera el ranking de proveedores por criterio
     */
    public function generarRanking(Collection $proveedoresMetricas, string $criterio = 'puntuacion_global'): Collection
    {
        return $proveedoresMetricas->sortByDesc(function ($proveedor) use ($criterio) {
            return $proveedor['metricas'][$criterio] ?? 0;
        })->values()->map(function ($proveedor, $index) use ($criterio) {
            $proveedor['ranking'] = $index + 1;
            $proveedor['criterio_ranking'] = $criterio;
            return $proveedor;
        });
    }
}
