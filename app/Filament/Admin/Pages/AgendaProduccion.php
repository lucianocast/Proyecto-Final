<?php

namespace App\Filament\Admin\Pages;

use App\Models\OrdenProduccion;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class AgendaProduccion extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Producción';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Agenda de Producción';
    protected static ?string $title = 'Agenda de Producción';

    protected static string $view = 'filament.admin.pages.agenda-produccion';

    public ?array $data = [];
    public string $vista = 'semana'; // dia, semana, mes
    public ?string $fechaSeleccionada = null;
    public ?Collection $agendaData = null;
    public ?array $alertas = null;
    public int $capacidadMaximaDiaria = 10; // Configurable

    public function mount(): void
    {
        $this->fechaSeleccionada = now()->toDateString();
        $this->form->fill([
            'fecha_base' => now(),
            'vista' => 'semana',
            'producto_id' => null,
            'estado_op' => null,
            'usuario_id' => null,
        ]);
        
        $this->cargarAgenda();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('vista')
                    ->options([
                        'dia' => 'Vista Diaria',
                        'semana' => 'Vista Semanal',
                        'mes' => 'Vista Mensual',
                    ])
                    ->default('semana')
                    ->live()
                    ->afterStateUpdated(fn () => $this->cargarAgenda()),
                
                DatePicker::make('fecha_base')
                    ->label('Fecha')
                    ->default(now())
                    ->live()
                    ->afterStateUpdated(fn () => $this->cargarAgenda()),
                
                Select::make('producto_id')
                    ->label('Filtrar por Producto')
                    ->options(Producto::activos()->orderBy('nombre')->pluck('nombre', 'id'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn () => $this->cargarAgenda()),
                
                Select::make('estado_op')
                    ->label('Estado de OP')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'en_proceso' => 'En Proceso',
                        'terminada' => 'Terminada',
                    ])
                    ->live()
                    ->afterStateUpdated(fn () => $this->cargarAgenda()),
                
                Select::make('usuario_id')
                    ->label('Colaborador')
                    ->options(User::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn () => $this->cargarAgenda()),
            ])
            ->columns(5)
            ->statePath('data');
    }

    public function cargarAgenda(): void
    {
        $vista = $this->data['vista'] ?? 'semana';
        $fechaBase = Carbon::parse($this->data['fecha_base'] ?? now());
        $productoId = $this->data['producto_id'] ?? null;
        $estadoOp = $this->data['estado_op'] ?? null;
        $usuarioId = $this->data['usuario_id'] ?? null;

        // Determinar rango de fechas según la vista
        switch ($vista) {
            case 'dia':
                $fechaInicio = $fechaBase->copy()->startOfDay();
                $fechaFin = $fechaBase->copy()->endOfDay();
                break;
            case 'semana':
                $fechaInicio = $fechaBase->copy()->startOfWeek();
                $fechaFin = $fechaBase->copy()->endOfWeek();
                break;
            case 'mes':
                $fechaInicio = $fechaBase->copy()->startOfMonth();
                $fechaFin = $fechaBase->copy()->endOfMonth();
                break;
            default:
                $fechaInicio = $fechaBase->copy()->startOfWeek();
                $fechaFin = $fechaBase->copy()->endOfWeek();
        }

        // Cargar pedidos
        $pedidosQuery = Pedido::with(['cliente', 'items.producto'])
            ->whereBetween('fecha_entrega', [$fechaInicio, $fechaFin])
            ->whereNotIn('status', ['cancelado', 'entregado']);

        if ($productoId) {
            $pedidosQuery->whereHas('items', function ($q) use ($productoId) {
                $q->where('producto_id', $productoId);
            });
        }

        $pedidos = $pedidosQuery->get();

        // Cargar órdenes de producción
        $opsQuery = OrdenProduccion::with(['producto', 'receta', 'usuario', 'pedidos'])
            ->where(function ($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                  ->orWhereBetween('fecha_limite', [$fechaInicio, $fechaFin]);
            });

        if ($productoId) {
            $opsQuery->where('producto_id', $productoId);
        }

        if ($estadoOp) {
            $opsQuery->where('estado', $estadoOp);
        }

        if ($usuarioId) {
            $opsQuery->where('user_id', $usuarioId);
        }

        $ordenesProduccion = $opsQuery->get();

        // Organizar datos por fecha
        $agenda = collect();
        $currentDate = $fechaInicio->copy();

        while ($currentDate <= $fechaFin) {
            $dateKey = $currentDate->toDateString();
            
            // Pedidos de este día
            $pedidosDia = $pedidos->filter(function ($pedido) use ($currentDate) {
                return $pedido->fecha_entrega->isSameDay($currentDate);
            })->sortBy('fecha_entrega');

            // OPs de este día (fecha_inicio o fecha_limite)
            $opsDia = $ordenesProduccion->filter(function ($op) use ($currentDate) {
                $inicioMatch = $op->fecha_inicio && Carbon::parse($op->fecha_inicio)->isSameDay($currentDate);
                $limiteMatch = $op->fecha_limite && Carbon::parse($op->fecha_limite)->isSameDay($currentDate);
                return $inicioMatch || $limiteMatch;
            })->sortBy('fecha_limite');

            // Detectar sobrecarga
            $cargaDia = $opsDia->where('estado', '!=', 'terminada')->count();
            $sobrecarga = $cargaDia > $this->capacidadMaximaDiaria;

            // Detectar OPs atrasadas
            $opsAtrasadas = $opsDia->filter(function ($op) use ($currentDate) {
                return $op->estado !== 'terminada' 
                    && $op->fecha_limite 
                    && Carbon::parse($op->fecha_limite)->lt($currentDate);
            });

            $agenda->push([
                'fecha' => $currentDate->copy(),
                'es_hoy' => $currentDate->isToday(),
                'es_pasado' => $currentDate->isPast() && !$currentDate->isToday(),
                'dia_semana' => $currentDate->locale('es')->dayName,
                'pedidos' => $pedidosDia,
                'ordenes_produccion' => $opsDia,
                'carga_trabajo' => $cargaDia,
                'sobrecarga' => $sobrecarga,
                'ops_atrasadas' => $opsAtrasadas->count(),
                'tiene_alertas' => $sobrecarga || $opsAtrasadas->count() > 0,
            ]);

            $currentDate->addDay();
        }

        $this->agendaData = $agenda;

        // Calcular alertas generales
        $this->calcularAlertas();
    }

    protected function calcularAlertas(): void
    {
        if (!$this->agendaData) {
            $this->alertas = null;
            return;
        }

        $alertas = [];

        // Alerta de días con sobrecarga
        $diasSobrecarga = $this->agendaData->filter(fn ($dia) => $dia['sobrecarga'])->count();
        if ($diasSobrecarga > 0) {
            $alertas[] = [
                'tipo' => 'sobrecarga',
                'severidad' => 'warning',
                'titulo' => 'Sobrecarga de Producción',
                'mensaje' => "{$diasSobrecarga} día(s) exceden la capacidad máxima de {$this->capacidadMaximaDiaria} OP(s).",
                'icono' => 'heroicon-o-exclamation-triangle',
            ];
        }

        // Alerta de OPs atrasadas
        $totalOpsAtrasadas = $this->agendaData->sum('ops_atrasadas');
        if ($totalOpsAtrasadas > 0) {
            $alertas[] = [
                'tipo' => 'atrasadas',
                'severidad' => 'danger',
                'titulo' => 'Órdenes Atrasadas',
                'mensaje' => "{$totalOpsAtrasadas} orden(es) de producción tienen fecha límite vencida.",
                'icono' => 'heroicon-o-clock',
            ];
        }

        // Alerta de pedidos próximos (dentro de 2 días)
        $pedidosProximos = Pedido::whereBetween('fecha_entrega', [now(), now()->addDays(2)])
            ->whereNotIn('status', ['listo', 'entregado', 'cancelado'])
            ->count();
        
        if ($pedidosProximos > 0) {
            $alertas[] = [
                'tipo' => 'urgente',
                'severidad' => 'info',
                'titulo' => 'Pedidos Urgentes',
                'mensaje' => "{$pedidosProximos} pedido(s) deben entregarse en los próximos 2 días.",
                'icono' => 'heroicon-o-fire',
            ];
        }

        $this->alertas = $alertas;
    }

    public function cambiarSemana(string $direccion): void
    {
        $fechaBase = Carbon::parse($this->data['fecha_base']);
        
        if ($direccion === 'anterior') {
            $nuevaFecha = $fechaBase->subWeek();
        } else {
            $nuevaFecha = $fechaBase->addWeek();
        }

        $this->data['fecha_base'] = $nuevaFecha->toDateString();
        $this->form->fill(['fecha_base' => $nuevaFecha]);
        $this->cargarAgenda();
    }

    public function irHoy(): void
    {
        $this->data['fecha_base'] = now()->toDateString();
        $this->form->fill(['fecha_base' => now()]);
        $this->cargarAgenda();
    }

    public function verDetallePedido(int $pedidoId): void
    {
        $this->redirect(route('filament.admin.resources.pedidos.edit', ['record' => $pedidoId]));
    }

    public function verDetalleOP(int $opId): void
    {
        $this->redirect(route('filament.admin.resources.orden-produccions.edit', ['record' => $opId]));
    }
}
