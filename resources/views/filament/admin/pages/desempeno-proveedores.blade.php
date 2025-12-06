<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Formulario de criterios --}}
        <div>
            <x-filament::section>
                <form wire:submit.prevent="analizarDesempeno">
                    {{ $this->form }}
                    
                    <div class="mt-6">
                        <x-filament::button type="submit" color="primary" icon="heroicon-o-chart-bar-square">
                            Analizar Desempeño
                        </x-filament::button>
                    </div>
                </form>
            </x-filament::section>
        </div>

        {{-- Panel de resultados --}}
        @if($analisisData)
            {{-- Promedios generales --}}
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-chart-bar class="w-5 h-5" />
                        Métricas Generales
                    </div>
                </x-slot>
                
                <x-slot name="description">
                    Período: {{ $analisisData['periodo']['desde'] }} - {{ $analisisData['periodo']['hasta'] }}
                    | {{ $analisisData['total_proveedores'] }} proveedor(es) analizados
                </x-slot>

                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border-l-4 border-blue-500">
                        <div class="text-xs text-gray-600 dark:text-gray-400 uppercase font-semibold">Cumplimiento Promedio</div>
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                            {{ number_format($analisisData['promedios']['cumplimiento_entrega'], 1) }}%
                        </div>
                    </div>

                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 border-l-4 border-green-500">
                        <div class="text-xs text-gray-600 dark:text-gray-400 uppercase font-semibold">Precisión Promedio</div>
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">
                            {{ number_format($analisisData['promedios']['precision_cantidades'], 1) }}%
                        </div>
                    </div>

                    <div class="bg-amber-50 dark:bg-amber-900/20 rounded-lg p-4 border-l-4 border-amber-500">
                        <div class="text-xs text-gray-600 dark:text-gray-400 uppercase font-semibold">Costo Prom/Orden</div>
                        <div class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">
                            ${{ number_format($analisisData['promedios']['costo_promedio_orden'], 0) }}
                        </div>
                    </div>

                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 border-l-4 border-purple-500">
                        <div class="text-xs text-gray-600 dark:text-gray-400 uppercase font-semibold">Tiempo Prom Entrega</div>
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1">
                            {{ number_format($analisisData['promedios']['tiempo_promedio_entrega'], 1) }} días
                        </div>
                    </div>

                    <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4 border-l-4 border-indigo-500">
                        <div class="text-xs text-gray-600 dark:text-gray-400 uppercase font-semibold">Puntuación Global</div>
                        <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">
                            {{ number_format($analisisData['promedios']['puntuacion_global'], 1) }}/100
                        </div>
                    </div>
                </div>
            </x-filament::section>

            {{-- Ranking de proveedores --}}
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-trophy class="w-5 h-5 text-yellow-500" />
                        Ranking de Proveedores
                    </div>
                </x-slot>
                
                <x-slot name="description">
                    Ordenado por: {{ 
                        collect([
                            'puntuacion_global' => 'Puntuación Global',
                            'cumplimiento_entrega' => 'Cumplimiento de Entrega',
                            'precision_cantidades' => 'Precisión de Cantidades',
                            'costo_promedio_orden' => 'Mejor Costo Promedio',
                        ])->get($analisisData['criterios']['criterio_ranking']) 
                    }}
                </x-slot>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                <th class="px-4 py-3 text-center font-semibold w-16">#</th>
                                <th class="px-4 py-3 text-left font-semibold">Proveedor</th>
                                <th class="px-4 py-3 text-center font-semibold">Órdenes</th>
                                <th class="px-4 py-3 text-center font-semibold">Cumplimiento<br><span class="text-xs font-normal">Entrega (%)</span></th>
                                <th class="px-4 py-3 text-center font-semibold">Precisión<br><span class="text-xs font-normal">Cantidades (%)</span></th>
                                <th class="px-4 py-3 text-right font-semibold">Costo Prom<br><span class="text-xs font-normal">por Orden</span></th>
                                <th class="px-4 py-3 text-center font-semibold">Tiempo Prom<br><span class="text-xs font-normal">Entrega (días)</span></th>
                                <th class="px-4 py-3 text-center font-semibold">Puntuación<br><span class="text-xs font-normal">Global</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($analisisData['ranking'] as $proveedor)
                                @php
                                    $puntuacion = $proveedor['metricas']['puntuacion_global'];
                                    if ($puntuacion >= 80) {
                                        $badgeColor = 'success';
                                        $rowBg = 'bg-green-50 dark:bg-green-900/10';
                                    } elseif ($puntuacion >= 60) {
                                        $badgeColor = 'warning';
                                        $rowBg = 'bg-yellow-50 dark:bg-yellow-900/10';
                                    } else {
                                        $badgeColor = 'danger';
                                        $rowBg = 'bg-red-50 dark:bg-red-900/10';
                                    }
                                @endphp
                                <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/50 {{ $rowBg }}">
                                    <td class="px-4 py-3 text-center">
                                        @if($proveedor['ranking'] == 1)
                                            <span class="text-2xl">🥇</span>
                                        @elseif($proveedor['ranking'] == 2)
                                            <span class="text-2xl">🥈</span>
                                        @elseif($proveedor['ranking'] == 3)
                                            <span class="text-2xl">🥉</span>
                                        @else
                                            <span class="font-bold text-gray-600">{{ $proveedor['ranking'] }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $proveedor['proveedor_nombre'] }}
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $proveedor['proveedor_email'] }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-center font-medium">{{ $proveedor['metricas']['total_ordenes'] }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="font-semibold {{ $proveedor['metricas']['cumplimiento_entrega'] >= 80 ? 'text-green-600' : ($proveedor['metricas']['cumplimiento_entrega'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ number_format($proveedor['metricas']['cumplimiento_entrega'], 1) }}%
                                        </span>
                                        <div class="text-xs text-gray-500">
                                            {{ $proveedor['metricas']['ordenes_a_tiempo'] }}/{{ $proveedor['metricas']['ordenes_recibidas'] }} a tiempo
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="font-semibold {{ $proveedor['metricas']['precision_cantidades'] >= 95 ? 'text-green-600' : ($proveedor['metricas']['precision_cantidades'] >= 85 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ number_format($proveedor['metricas']['precision_cantidades'], 1) }}%
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold">
                                        ${{ number_format($proveedor['metricas']['costo_promedio_orden'], 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        {{ number_format($proveedor['metricas']['tiempo_promedio_entrega_dias'], 1) }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <x-filament::badge :color="$badgeColor" size="lg">
                                            {{ number_format($puntuacion, 1) }}/100
                                        </x-filament::badge>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    <x-filament::button 
                        wire:click="exportarPdf" 
                        color="danger" 
                        icon="heroicon-o-document-arrow-down"
                    >
                        Exportar Reporte PDF
                    </x-filament::button>
                </div>
            </x-filament::section>

            {{-- Leyenda de puntuación --}}
            <x-filament::section>
                <x-slot name="heading">Interpretación de Puntuación Global</x-slot>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 bg-green-500 rounded"></div>
                        <div>
                            <div class="font-semibold">Excelente (80-100)</div>
                            <div class="text-xs text-gray-600">Proveedor confiable y eficiente</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 bg-yellow-500 rounded"></div>
                        <div>
                            <div class="font-semibold">Bueno (60-79)</div>
                            <div class="text-xs text-gray-600">Desempeño aceptable, con margen de mejora</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 bg-red-500 rounded"></div>
                        <div>
                            <div class="font-semibold">Requiere Atención (&lt;60)</div>
                            <div class="text-xs text-gray-600">Evaluar alternativas o negociar mejoras</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-sm">
                    <strong>Fórmula de Puntuación:</strong> 
                    40% Cumplimiento Entrega + 30% Precisión Cantidades + 20% Sin Canceladas + 10% Rapidez
                </div>
            </x-filament::section>
        @else
            <x-filament::section>
                <div class="text-center py-12 text-gray-500">
                    <x-heroicon-o-trophy class="w-16 h-16 mx-auto mb-4 opacity-50" />
                    <p class="text-lg">Defina los criterios y analice el desempeño de proveedores</p>
                    <p class="text-sm mt-2">Seleccione el período y proveedores a evaluar arriba</p>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
