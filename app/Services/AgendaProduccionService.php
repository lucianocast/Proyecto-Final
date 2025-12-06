<?php

namespace App\Services;

use App\Models\Pedido;
use Carbon\Carbon;

class AgendaProduccionService
{
    /**
     * Capacidad máxima de pedidos por día (configurable).
     */
    private const CAPACIDAD_MAXIMA_PEDIDOS_DIA = 15;

    /**
     * Horas de trabajo por día.
     */
    private const HORAS_TRABAJO_DIA = 8;

    /**
     * Validar si se puede agendar un pedido en una fecha determinada.
     *
     * @param Carbon $fechaEntrega
     * @param int|null $pedidoIdExcluir ID del pedido a excluir (para ediciones)
     * @return array ['valido' => bool, 'mensaje' => string, 'capacidad_disponible' => int]
     */
    public function validarCapacidadProduccion(Carbon $fechaEntrega, ?int $pedidoIdExcluir = null): array
    {
        // Validar que no sea domingo
        if ($fechaEntrega->isSunday()) {
            return [
                'valido' => false,
                'mensaje' => 'No se puede programar producción los domingos.',
                'capacidad_disponible' => 0,
            ];
        }

        // Contar pedidos activos para esa fecha (excluyendo cancelados y el pedido actual si existe)
        $pedidosDelDia = Pedido::whereDate('fecha_entrega', $fechaEntrega->format('Y-m-d'))
            ->whereNotIn('status', ['cancelado', 'entregado'])
            ->when($pedidoIdExcluir, function ($query) use ($pedidoIdExcluir) {
                $query->where('id', '!=', $pedidoIdExcluir);
            })
            ->count();

        $capacidadDisponible = self::CAPACIDAD_MAXIMA_PEDIDOS_DIA - $pedidosDelDia;

        if ($capacidadDisponible <= 0) {
            return [
                'valido' => false,
                'mensaje' => "La agenda de producción está completa para el {$fechaEntrega->format('d/m/Y')}. Capacidad máxima: " . self::CAPACIDAD_MAXIMA_PEDIDOS_DIA . " pedidos.",
                'capacidad_disponible' => 0,
            ];
        }

        // Advertencia si queda poca capacidad
        if ($capacidadDisponible <= 3) {
            return [
                'valido' => true,
                'mensaje' => "⚠️ Capacidad limitada: solo quedan {$capacidadDisponible} espacios disponibles para el {$fechaEntrega->format('d/m/Y')}.",
                'capacidad_disponible' => $capacidadDisponible,
            ];
        }

        return [
            'valido' => true,
            'mensaje' => "Capacidad disponible: {$capacidadDisponible} pedidos para el {$fechaEntrega->format('d/m/Y')}.",
            'capacidad_disponible' => $capacidadDisponible,
        ];
    }

    /**
     * Obtener fechas con capacidad disponible en los próximos N días.
     *
     * @param int $dias Número de días a revisar
     * @return array
     */
    public function obtenerFechasDisponibles(int $dias = 14): array
    {
        $fechasDisponibles = [];
        $fechaActual = now()->addDay(); // Empezar desde mañana

        for ($i = 0; $i < $dias; $i++) {
            $fecha = $fechaActual->copy()->addDays($i);

            // Saltar domingos
            if ($fecha->isSunday()) {
                continue;
            }

            $validacion = $this->validarCapacidadProduccion($fecha);

            if ($validacion['valido'] && $validacion['capacidad_disponible'] > 0) {
                $fechasDisponibles[] = [
                    'fecha' => $fecha->format('Y-m-d'),
                    'fecha_formateada' => $fecha->format('d/m/Y'),
                    'dia_semana' => $fecha->locale('es')->dayName,
                    'capacidad_disponible' => $validacion['capacidad_disponible'],
                ];
            }
        }

        return $fechasDisponibles;
    }

    /**
     * Obtener estadísticas de agenda para un rango de fechas.
     *
     * @param Carbon $fechaInicio
     * @param Carbon $fechaFin
     * @return array
     */
    public function obtenerEstadisticasAgenda(Carbon $fechaInicio, Carbon $fechaFin): array
    {
        $pedidos = Pedido::whereBetween('fecha_entrega', [$fechaInicio, $fechaFin])
            ->whereNotIn('status', ['cancelado'])
            ->selectRaw('DATE(fecha_entrega) as fecha, COUNT(*) as total')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $estadisticas = [];
        $fecha = $fechaInicio->copy();

        while ($fecha->lessThanOrEqualTo($fechaFin)) {
            if (!$fecha->isSunday()) {
                $pedidosDelDia = $pedidos->firstWhere('fecha', $fecha->format('Y-m-d'));
                $total = $pedidosDelDia ? $pedidosDelDia->total : 0;
                $porcentajeCapacidad = ($total / self::CAPACIDAD_MAXIMA_PEDIDOS_DIA) * 100;

                $estadisticas[] = [
                    'fecha' => $fecha->format('Y-m-d'),
                    'fecha_formateada' => $fecha->format('d/m/Y'),
                    'dia_semana' => $fecha->locale('es')->dayName,
                    'pedidos' => $total,
                    'capacidad_maxima' => self::CAPACIDAD_MAXIMA_PEDIDOS_DIA,
                    'capacidad_disponible' => self::CAPACIDAD_MAXIMA_PEDIDOS_DIA - $total,
                    'porcentaje_ocupacion' => round($porcentajeCapacidad, 2),
                    'estado' => $this->getEstadoCapacidad($porcentajeCapacidad),
                ];
            }

            $fecha->addDay();
        }

        return $estadisticas;
    }

    /**
     * Determinar el estado de capacidad basado en el porcentaje de ocupación.
     */
    private function getEstadoCapacidad(float $porcentaje): string
    {
        if ($porcentaje >= 100) return 'completo';
        if ($porcentaje >= 80) return 'alto';
        if ($porcentaje >= 50) return 'medio';
        return 'bajo';
    }

    /**
     * Validar tiempo mínimo de anticipación para un pedido.
     */
    public function validarTiempoAnticipacion(Carbon $fechaEntrega, string $tipoPedido = 'normal'): array
    {
        $horasMinimas = match ($tipoPedido) {
            'simple' => 12,
            'normal' => 24,
            'complejo' => 48,
            default => 24,
        };

        $horasDisponibles = now()->diffInHours($fechaEntrega);

        if ($horasDisponibles < $horasMinimas) {
            return [
                'valido' => false,
                'mensaje' => "Se requieren al menos {$horasMinimas} horas de anticipación para este tipo de pedido. Horas disponibles: {$horasDisponibles}.",
                'horas_requeridas' => $horasMinimas,
                'horas_disponibles' => $horasDisponibles,
            ];
        }

        return [
            'valido' => true,
            'mensaje' => "Tiempo de anticipación suficiente.",
            'horas_requeridas' => $horasMinimas,
            'horas_disponibles' => $horasDisponibles,
        ];
    }
}
