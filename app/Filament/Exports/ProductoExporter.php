<?php

namespace App\Filament\Exports;

use App\Models\Producto;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductoExporter extends Exporter
{
    protected static ?string $model = Producto::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('nombre')
                ->label('Nombre'),
            ExportColumn::make('descripcion')
                ->label('Descripción'),
            ExportColumn::make('categoria.nombre')
                ->label('Categoría'),
            ExportColumn::make('precio_base')
                ->label('Precio Base')
                ->formatStateUsing(fn ($state) => '$' . number_format($state, 2)),
            ExportColumn::make('activo')
                ->label('Activo')
                ->formatStateUsing(fn ($state) => $state ? 'Sí' : 'No'),
            ExportColumn::make('imagen_url')
                ->label('URL Imagen'),
            ExportColumn::make('tiempo_preparacion')
                ->label('Tiempo de Preparación (min)'),
            ExportColumn::make('created_at')
                ->label('Fecha de Creación')
                ->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y H:i') : ''),
            ExportColumn::make('updated_at')
                ->label('Última Actualización')
                ->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y H:i') : ''),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Se ha completado la exportación de productos y ' . number_format($export->successful_rows) . ' ' . str('fila')->plural($export->successful_rows) . ' exportadas.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('fila')->plural($failedRowsCount) . ' fallaron.';
        }

        return $body;
    }
}
