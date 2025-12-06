<?php

namespace App\Filament\Exports;

use App\Models\Insumo;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InsumoExporter extends Exporter
{
    protected static ?string $model = Insumo::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('nombre'),
            ExportColumn::make('categoria.nombre')->label('Categoría'),
            ExportColumn::make('unidad_de_medida')->label('Unidad'),
            ExportColumn::make('stock_total')
                ->label('Stock Actual')
                ->state(fn (Insumo $record): float => $record->stock_total),
            ExportColumn::make('stock_minimo')->label('Stock Mínimo'),
            ExportColumn::make('estado')
                ->state(fn (Insumo $record) => match (true) {
                    $record->stock_total <= $record->stock_minimo => 'Crítico',
                    $record->stock_total <= ($record->stock_minimo * 1.5) => 'Bajo',
                    default => 'Normal',
                }),
            ExportColumn::make('ubicacion'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Exportación de insumos completada: ' . number_format($export->successful_rows) . ' ' . str('fila')->plural($export->successful_rows) . ' exportadas.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('fila')->plural($failedRowsCount) . ' fallaron.';
        }

        return $body;
    }
}
