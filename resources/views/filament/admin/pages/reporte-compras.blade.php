<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Formulario de filtros --}}
        <div>
            <x-filament::section>
                <form wire:submit.prevent="generarReporte">
                    {{ $this->form }}
                    
                    <div class="mt-6">
                        <x-filament::button type="submit" color="primary" icon="heroicon-o-document-chart-bar">
                            Generar Reporte
                        </x-filament::button>
                    </div>
                </form>
            </x-filament::section>
        </div>

        {{-- Vista previa del reporte --}}
        @if($reporteData)
            <x-filament::section>
                <x-slot name="heading">
                    Vista Previa del Reporte
                </x-slot>
                
                <x-slot name="description">
                    Período: {{ $reporteData['periodo']['desde'] }} - {{ $reporteData['periodo']['hasta'] }}
                    <br>
                    Generado: {{ $reporteData['generado_en'] }}
                </x-slot>

                {{-- Métricas principales --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-primary-50 dark:bg-primary-900/20 rounded-lg p-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Costo Total</div>
                        <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                            ${{ number_format($reporteData['metricas']['costo_total'], 2) }}
                        </div>
                    </div>

                    <div class="bg-success-50 dark:bg-success-900/20 rounded-lg p-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Total Órdenes</div>
                        <div class="text-2xl font-bold text-success-600 dark:text-success-400">
                            {{ $reporteData['metricas']['total_ordenes'] }}
                        </div>
                    </div>

                    <div class="bg-warning-50 dark:bg-warning-900/20 rounded-lg p-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Insumos Únicos</div>
                        <div class="text-2xl font-bold text-warning-600 dark:text-warning-400">
                            {{ $reporteData['metricas']['insumos_unicos'] }}
                        </div>
                    </div>

                    <div class="bg-info-50 dark:bg-info-900/20 rounded-lg p-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Costo Promedio/Orden</div>
                        <div class="text-2xl font-bold text-info-600 dark:text-info-400">
                            ${{ number_format($reporteData['metricas']['costo_promedio_orden'], 2) }}
                        </div>
                    </div>
                </div>

                {{-- Tabla de datos agrupados --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                <th class="px-4 py-3 text-left font-semibold">
                                    {{ ucfirst($reporteData['criterios']['agrupar_por']) }}
                                </th>
                                <th class="px-4 py-3 text-right font-semibold">Total Órdenes</th>
                                <th class="px-4 py-3 text-right font-semibold">Costo Total</th>
                                @if(isset($reporteData['datos_agrupados'][0]['costo_promedio']))
                                    <th class="px-4 py-3 text-right font-semibold">Costo Promedio</th>
                                @endif
                                @if(isset($reporteData['datos_agrupados'][0]['cantidad_total']))
                                    <th class="px-4 py-3 text-right font-semibold">Cantidad Total</th>
                                @endif
                                <th class="px-4 py-3 text-right font-semibold">% del Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reporteData['datos_agrupados'] as $fila)
                                <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="px-4 py-3 font-medium">{{ $fila['nombre'] }}</td>
                                    <td class="px-4 py-3 text-right">{{ $fila['total_ordenes'] ?? '-' }}</td>
                                    <td class="px-4 py-3 text-right font-semibold text-primary-600 dark:text-primary-400">
                                        ${{ number_format($fila['costo_total'], 2) }}
                                    </td>
                                    @if(isset($fila['costo_promedio']))
                                        <td class="px-4 py-3 text-right">
                                            ${{ number_format($fila['costo_promedio'], 2) }}
                                        </td>
                                    @endif
                                    @if(isset($fila['cantidad_total']))
                                        <td class="px-4 py-3 text-right">
                                            {{ number_format($fila['cantidad_total'], 2) }}
                                        </td>
                                    @endif
                                    <td class="px-4 py-3 text-right">
                                        {{ number_format(($fila['costo_total'] / $reporteData['metricas']['costo_total']) * 100, 1) }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-100 dark:bg-gray-900 font-bold">
                                <td class="px-4 py-3">TOTAL</td>
                                <td class="px-4 py-3 text-right">{{ $reporteData['metricas']['total_ordenes'] }}</td>
                                <td class="px-4 py-3 text-right text-primary-600 dark:text-primary-400">
                                    ${{ number_format($reporteData['metricas']['costo_total'], 2) }}
                                </td>
                                <td colspan="10"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Botones de exportación --}}
                <div class="mt-6 flex gap-3">
                    <x-filament::button 
                        wire:click="exportarPdf" 
                        color="danger" 
                        icon="heroicon-o-document-arrow-down"
                    >
                        Exportar PDF
                    </x-filament::button>

                    <x-filament::button 
                        wire:click="exportarExcel" 
                        color="success" 
                        icon="heroicon-o-arrow-down-tray"
                    >
                        Exportar Excel
                    </x-filament::button>
                </div>
            </x-filament::section>
        @else
            <x-filament::section>
                <div class="text-center py-12 text-gray-500">
                    <x-heroicon-o-document-chart-bar class="w-16 h-16 mx-auto mb-4 opacity-50" />
                    <p class="text-lg">Defina los criterios y genere el reporte</p>
                    <p class="text-sm mt-2">Seleccione el período, filtros y criterio de agrupación arriba</p>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
