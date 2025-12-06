<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\OrdenDeCompra;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PdfReportService
{
    /**
     * Generar PDF de un pedido individual.
     */
    public function generarPdfPedido(Pedido $pedido)
    {
        $pdf = Pdf::loadView('pdf.pedido', [
            'pedido' => $pedido->load(['cliente', 'items.productoVariante.producto', 'vendedor', 'pagos']),
        ]);

        return $pdf->stream("pedido_{$pedido->id}.pdf");
    }

    /**
     * Generar PDF de reporte de pedidos con filtros.
     */
    public function generarReportePedidos(array $filtros = [])
    {
        $query = Pedido::with(['cliente', 'vendedor']);

        // Aplicar filtros
        if (!empty($filtros['fecha_desde'])) {
            $query->whereDate('fecha_entrega', '>=', $filtros['fecha_desde']);
        }

        if (!empty($filtros['fecha_hasta'])) {
            $query->whereDate('fecha_entrega', '<=', $filtros['fecha_hasta']);
        }

        if (!empty($filtros['status'])) {
            $query->whereIn('status', (array) $filtros['status']);
        }

        if (!empty($filtros['cliente_id'])) {
            $query->where('cliente_id', $filtros['cliente_id']);
        }

        $pedidos = $query->orderBy('fecha_entrega', 'desc')->get();

        // Calcular totales
        $totalGeneral = $pedidos->sum('total_calculado');
        $totalAbonado = $pedidos->sum('monto_abonado');
        $totalPendiente = $pedidos->sum('saldo_pendiente');

        $pdf = Pdf::loadView('pdf.reporte-pedidos', [
            'pedidos' => $pedidos,
            'filtros' => $filtros,
            'totalGeneral' => $totalGeneral,
            'totalAbonado' => $totalAbonado,
            'totalPendiente' => $totalPendiente,
            'fecha_generacion' => now(),
        ]);

        $nombreArchivo = 'reporte_pedidos_' . now()->format('Y-m-d_His') . '.pdf';
        return $pdf->stream($nombreArchivo);
    }

    /**
     * Generar PDF de una orden de compra individual.
     */
    public function generarPdfOrdenCompra(OrdenDeCompra $orden)
    {
        $pdf = Pdf::loadView('pdf.orden-compra', [
            'orden' => $orden->load(['proveedor', 'items.insumo', 'user']),
        ]);

        return $pdf->stream("orden_compra_{$orden->id}.pdf");
    }

    /**
     * Generar PDF de reporte de órdenes de compra.
     */
    public function generarReporteOrdenesCompra(array $filtros = [])
    {
        $query = OrdenDeCompra::with(['proveedor', 'user']);

        // Aplicar filtros
        if (!empty($filtros['fecha_desde'])) {
            $query->whereDate('fecha_emision', '>=', $filtros['fecha_desde']);
        }

        if (!empty($filtros['fecha_hasta'])) {
            $query->whereDate('fecha_emision', '<=', $filtros['fecha_hasta']);
        }

        if (!empty($filtros['status'])) {
            $query->whereIn('status', (array) $filtros['status']);
        }

        if (!empty($filtros['proveedor_id'])) {
            $query->where('proveedor_id', $filtros['proveedor_id']);
        }

        $ordenes = $query->orderBy('fecha_emision', 'desc')->get();

        // Calcular totales
        $totalGeneral = $ordenes->sum('total_calculado');

        $pdf = Pdf::loadView('pdf.reporte-ordenes-compra', [
            'ordenes' => $ordenes,
            'filtros' => $filtros,
            'totalGeneral' => $totalGeneral,
            'fecha_generacion' => now(),
        ]);

        $nombreArchivo = 'reporte_ordenes_compra_' . now()->format('Y-m-d_His') . '.pdf';
        return $pdf->stream($nombreArchivo);
    }

    /**
     * Generar PDF de reporte de ventas por período.
     */
    public function generarReporteVentas(Carbon $fechaInicio, Carbon $fechaFin)
    {
        $pedidos = Pedido::with(['cliente', 'items.producto'])
            ->whereBetween('fecha_entrega', [$fechaInicio, $fechaFin])
            ->whereIn('status', ['entregado'])
            ->orderBy('fecha_entrega', 'desc')
            ->get();

        // Estadísticas
        $totalVentas = $pedidos->sum('total_calculado');
        $totalCobrado = $pedidos->sum('monto_abonado');
        $cantidadPedidos = $pedidos->count();
        $ticketPromedio = $cantidadPedidos > 0 ? $totalVentas / $cantidadPedidos : 0;

        // Productos más vendidos
        $productosMasVendidos = [];
        foreach ($pedidos as $pedido) {
            foreach ($pedido->items as $item) {
                $nombreProducto = $item->producto->nombre;
                if (!isset($productosMasVendidos[$nombreProducto])) {
                    $productosMasVendidos[$nombreProducto] = [
                        'nombre' => $nombreProducto,
                        'cantidad' => 0,
                        'total' => 0,
                    ];
                }
                $productosMasVendidos[$nombreProducto]['cantidad'] += $item->cantidad;
                $productosMasVendidos[$nombreProducto]['total'] += $item->subtotal;
            }
        }

        // Ordenar por cantidad
        usort($productosMasVendidos, function ($a, $b) {
            return $b['cantidad'] <=> $a['cantidad'];
        });

        $pdf = Pdf::loadView('pdf.reporte-ventas', [
            'pedidos' => $pedidos,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'totalVentas' => $totalVentas,
            'totalCobrado' => $totalCobrado,
            'cantidadPedidos' => $cantidadPedidos,
            'ticketPromedio' => $ticketPromedio,
            'productosMasVendidos' => array_slice($productosMasVendidos, 0, 10),
            'fecha_generacion' => now(),
        ]);

        $nombreArchivo = 'reporte_ventas_' . $fechaInicio->format('Y-m-d') . '_' . $fechaFin->format('Y-m-d') . '.pdf';
        return $pdf->stream($nombreArchivo);
    }
}
