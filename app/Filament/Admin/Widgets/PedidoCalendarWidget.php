<?php

namespace App\Filament\Admin\Widgets;

// --- IMPORTACIONES NECESARIAS ---
use App\Models\Pedido;
use App\Filament\Admin\Resources\PedidoResource;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
// --- FIN DE IMPORTACIONES ---

class PedidoCalendarWidget extends FullCalendarWidget
{
    /**
     * Obtiene los eventos para mostrar en el calendario.
     */
    public function fetchEvents(array $info): array
    {
        // 1. Obtenemos los pedidos
        $pedidos = Pedido::with('cliente')
            // 2. Filtramos por estado (¡el requisito clave!)
            ->whereIn('status', ['en_produccion', 'listo'])
            // 3. Obtenemos los pedidos dentro del rango de fechas que mira el calendario
            ->where('fecha_entrega', '>=', $info['start'])
            ->where('fecha_entrega', '<=', $info['end'])
            ->get();

        // 4. Mapeamos los resultados a arrays de eventos
        return $pedidos->map(function (Pedido $pedido) {
            return [
                'title' => $pedido->cliente->nombre,
                'start' => $pedido->fecha_entrega,
                'url' => PedidoResource::getUrl('edit', ['record' => $pedido]),
            ];
        })->toArray();
    }
}
