<?php

namespace App\Filament\Exports;

use App\Models\MovimientoStock;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class MovimientoStockExporter extends Exporter
{
    protected static ?string $model = MovimientoStock::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('created_at')->label('Fecha'),
            ExportColumn::make('tipo')->label('Tipo'),
            ExportColumn::make('insumo.nombre')->label('Insumo'),
            ExportColumn::make('cantidad')->label('Cantidad'),
            ExportColumn::make('cantidad_anterior')->label('Stock Anterior'),
            ExportColumn::make('cantidad_nueva')->label('Stock Nuevo'),
            ExportColumn::make('usuario.name')->label('Usuario'),
            ExportColumn::make('referencia')->label('Referencia'),
            ExportColumn::make('tipo_referencia')->label('Tipo Referencia'),
            ExportColumn::make('justificacion')->label('Justificación'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Exportación de movimientos completada: ' . number_format($export->successful_rows) . ' ' . str('fila')->plural($export->successful_rows) . ' exportadas.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('fila')->plural($failedRowsCount) . ' fallaron.';
        }

        return $body;
    }
}
