<?php

namespace App\Filament\Exports;

use App\Models\Insumo;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class StockCriticoExporter extends Exporter
{
    protected static ?string $model = Insumo::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('nombre')->label('Insumo'),
            ExportColumn::make('categoria.nombre')->label('Categoría'),
            ExportColumn::make('unidad_de_medida')->label('Unidad'),
            ExportColumn::make('stock_total')
                ->label('Stock Disponible')
                ->state(fn (Insumo $record): float => $record->stock_total),
            ExportColumn::make('stock_minimo')->label('Stock Mínimo'),
            ExportColumn::make('diferencia')
                ->label('Diferencia')
                ->state(fn (Insumo $record): float => $record->stock_total - $record->stock_minimo),
            ExportColumn::make('cantidad_a_comprar')
                ->label('Cantidad Sugerida a Comprar')
                ->state(fn (Insumo $record): float => max(0, ($record->stock_minimo * 2) - $record->stock_total)),
            ExportColumn::make('ultimo_proveedor')
                ->label('Último Proveedor')
                ->state(function (Insumo $record) {
                    $ultimoLote = $record->lotes()->latest()->first();
                    return $ultimoLote ? $ultimoLote->proveedor : 'N/A';
                }),
            ExportColumn::make('ubicacion')->label('Ubicación'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Exportación de stock crítico completada: ' . number_format($export->successful_rows) . ' ' . str('insumo')->plural($export->successful_rows) . ' exportados.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('fila')->plural($failedRowsCount) . ' fallaron.';
        }

        return $body;
    }
}
