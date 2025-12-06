<x-filament-panels::page>
    {{-- Formulario de filtros --}}
    <div class="mb-6">
        <form wire:submit.prevent="cargarAgenda">
            {{ $this->form }}
        </form>
    </div>

    {{-- Alertas --}}
    @if($alertas && count($alertas) > 0)
        <div class="mb-6 space-y-3">
            @foreach($alertas as $alerta)
                <div class="rounded-lg p-4 border-l-4 
                    @if($alerta['severidad'] === 'danger') bg-red-50 border-red-500 dark:bg-red-900/20
                    @elseif($alerta['severidad'] === 'warning') bg-yellow-50 border-yellow-500 dark:bg-yellow-900/20
                    @else bg-blue-50 border-blue-500 dark:bg-blue-900/20
                    @endif">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            @if($alerta['severidad'] === 'danger')
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            @elseif($alerta['severidad'] === 'warning')
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-semibold 
                                @if($alerta['severidad'] === 'danger') text-red-800 dark:text-red-200
                                @elseif($alerta['severidad'] === 'warning') text-yellow-800 dark:text-yellow-200
                                @else text-blue-800 dark:text-blue-200
                                @endif">
                                {{ $alerta['titulo'] }}
                            </h3>
                            <p class="mt-1 text-sm 
                                @if($alerta['severidad'] === 'danger') text-red-700 dark:text-red-300
                                @elseif($alerta['severidad'] === 'warning') text-yellow-700 dark:text-yellow-300
                                @else text-blue-700 dark:text-blue-300
                                @endif">
                                {{ $alerta['mensaje'] }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Controles de navegación --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-2">
            <button 
                wire:click="cambiarSemana('anterior')"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Anterior
            </button>
            
            <button 
                wire:click="irHoy"
                class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                Hoy
            </button>
            
            <button 
                wire:click="cambiarSemana('siguiente')"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                Siguiente
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            @if($agendaData && $agendaData->count() > 0)
                {{ $agendaData->first()['fecha']->locale('es')->isoFormat('D [de] MMMM, YYYY') }}
                @if($data['vista'] !== 'dia')
                    - {{ $agendaData->last()['fecha']->locale('es')->isoFormat('D [de] MMMM, YYYY') }}
                @endif
            @endif
        </div>
    </div>

    {{-- Vista de Agenda --}}
    @if($agendaData && $agendaData->count() > 0)
        <div class="grid gap-4 
            @if($data['vista'] === 'dia') grid-cols-1
            @elseif($data['vista'] === 'semana') grid-cols-7
            @else grid-cols-7
            @endif">
            
            @foreach($agendaData as $dia)
                <div class="bg-white dark:bg-gray-800 rounded-lg border 
                    @if($dia['es_hoy']) border-primary-500 border-2
                    @elseif($dia['tiene_alertas']) border-red-500
                    @else border-gray-200 dark:border-gray-700
                    @endif
                    overflow-hidden">
                    
                    {{-- Encabezado del día --}}
                    <div class="p-3 border-b 
                        @if($dia['es_hoy']) bg-primary-50 dark:bg-primary-900/20
                        @elseif($dia['sobrecarga']) bg-red-50 dark:bg-red-900/20
                        @elseif($dia['es_pasado']) bg-gray-50 dark:bg-gray-900/50
                        @else bg-gray-50 dark:bg-gray-900/20
                        @endif">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                    {{ $dia['dia_semana'] }}
                                </div>
                                <div class="text-lg font-bold 
                                    @if($dia['es_hoy']) text-primary-600 dark:text-primary-400
                                    @else text-gray-900 dark:text-gray-100
                                    @endif">
                                    {{ $dia['fecha']->format('d') }}
                                </div>
                            </div>
                            
                            {{-- Indicadores --}}
                            <div class="flex flex-col items-end space-y-1">
                                @if($dia['sobrecarga'])
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Sobrecarga
                                    </span>
                                @endif
                                @if($dia['ops_atrasadas'] > 0)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-200">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $dia['ops_atrasadas'] }} atrasada(s)
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Contador de carga --}}
                        <div class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                            Carga: {{ $dia['carga_trabajo'] }}/{{ $capacidadMaximaDiaria }} OP(s)
                        </div>
                    </div>

                    {{-- Contenido del día --}}
                    <div class="p-3 space-y-3 max-h-96 overflow-y-auto">
                        {{-- Pedidos --}}
                        @if($dia['pedidos']->count() > 0)
                            <div>
                                <h4 class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                    Pedidos ({{ $dia['pedidos']->count() }})
                                </h4>
                                <div class="space-y-2">
                                    @foreach($dia['pedidos'] as $pedido)
                                        <div 
                                            wire:click="verDetallePedido({{ $pedido->id }})"
                                            class="p-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 cursor-pointer hover:bg-blue-100 dark:hover:bg-blue-900/30 transition">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1 min-w-0">
                                                    <div class="text-xs font-medium text-blue-900 dark:text-blue-200 truncate">
                                                        #{{ $pedido->id }} - {{ $pedido->cliente->nombre }}
                                                    </div>
                                                    <div class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                                                        {{ $pedido->items->count() }} producto(s)
                                                    </div>
                                                </div>
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                    @if($pedido->status === 'pendiente') bg-yellow-100 text-yellow-800
                                                    @elseif($pedido->status === 'confirmado') bg-green-100 text-green-800
                                                    @elseif($pedido->status === 'en_produccion') bg-purple-100 text-purple-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ ucfirst($pedido->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Órdenes de Producción --}}
                        @if($dia['ordenes_produccion']->count() > 0)
                            <div>
                                <h4 class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Órdenes de Producción ({{ $dia['ordenes_produccion']->count() }})
                                </h4>
                                <div class="space-y-2">
                                    @foreach($dia['ordenes_produccion'] as $op)
                                        <div 
                                            wire:click="verDetalleOP({{ $op->id }})"
                                            class="p-2 rounded-lg border cursor-pointer transition
                                                @if($op->estado === 'pendiente') bg-yellow-50 dark:bg-yellow-900/20 border-yellow-300 dark:border-yellow-700 hover:bg-yellow-100 dark:hover:bg-yellow-900/30
                                                @elseif($op->estado === 'en_proceso') bg-purple-50 dark:bg-purple-900/20 border-purple-300 dark:border-purple-700 hover:bg-purple-100 dark:hover:bg-purple-900/30
                                                @elseif($op->estado === 'terminada') bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-700 hover:bg-green-100 dark:hover:bg-green-900/30
                                                @else bg-gray-50 dark:bg-gray-900/20 border-gray-300 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-900/30
                                                @endif">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1 min-w-0">
                                                    <div class="text-xs font-medium text-gray-900 dark:text-gray-100 truncate">
                                                        OP #{{ $op->id }} - {{ $op->producto->nombre }}
                                                    </div>
                                                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                                        {{ $op->cantidad_a_producir }} unidad(es)
                                                    </div>
                                                    @if($op->usuario)
                                                        <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                                            👤 {{ $op->usuario->name }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-2 flex flex-col items-end space-y-1">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                        @if($op->estado === 'pendiente') bg-yellow-100 text-yellow-800
                                                        @elseif($op->estado === 'en_proceso') bg-purple-100 text-purple-800
                                                        @elseif($op->estado === 'terminada') bg-green-100 text-green-800
                                                        @else bg-gray-100 text-gray-800
                                                        @endif">
                                                        {{ ucfirst(str_replace('_', ' ', $op->estado)) }}
                                                    </span>
                                                    @if($op->fecha_limite && \Carbon\Carbon::parse($op->fecha_limite)->lt(now()) && $op->estado !== 'terminada')
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                            ⏰ Atrasada
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Día sin actividades --}}
                        @if($dia['pedidos']->count() === 0 && $dia['ordenes_produccion']->count() === 0)
                            <div class="text-center py-8 text-gray-400 dark:text-gray-600">
                                <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="text-sm">Sin actividades</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-500 dark:text-gray-400">No hay datos para mostrar en el período seleccionado</p>
        </div>
    @endif

    {{-- Leyenda --}}
    <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Leyenda</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 rounded bg-blue-100 border border-blue-300"></div>
                <span class="text-gray-700 dark:text-gray-300">Pedidos</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 rounded bg-yellow-100 border border-yellow-300"></div>
                <span class="text-gray-700 dark:text-gray-300">OP Pendiente</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 rounded bg-purple-100 border border-purple-300"></div>
                <span class="text-gray-700 dark:text-gray-300">OP En Proceso</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 rounded bg-green-100 border border-green-300"></div>
                <span class="text-gray-700 dark:text-gray-300">OP Terminada</span>
            </div>
        </div>
        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700 text-xs text-gray-600 dark:text-gray-400">
            <strong>Capacidad máxima diaria:</strong> {{ $capacidadMaximaDiaria }} órdenes de producción simultáneas
        </div>
    </div>
</x-filament-panels::page>
