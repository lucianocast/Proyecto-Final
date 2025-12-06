<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-700 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-warning-600 dark:text-warning-400" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-warning-800 dark:text-warning-200">
                        Reporte de Stock Crítico
                    </h3>
                    <div class="mt-2 text-sm text-warning-700 dark:text-warning-300">
                        <p>Este reporte muestra todos los insumos con stock igual o menor al stock mínimo establecido.</p>
                        <p class="mt-1">Use los filtros para refinar la búsqueda y exporte a Excel para generar órdenes de compra.</p>
                    </div>
                </div>
            </div>
        </div>

        <x-filament::section>
            <x-slot name="heading">
                Filtros de Búsqueda
            </x-slot>
            {{ $this->form }}
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                Insumos con Stock Crítico
            </x-slot>
            {{ $this->table }}
        </x-filament::section>
    </div>
</x-filament-panels::page>
