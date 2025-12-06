<?php

namespace App\Filament\Exports;

use App\Models\OrdenDeCompra;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class OrdenDeCompraExporter extends Exporter
{
    protected static ?string $model = OrdenDeCompra::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('proveedor.nombre_empresa')
                ->label('Proveedor'),
            ExportColumn::make('proveedor.nombre_contacto')
                ->label('Contacto'),
            ExportColumn::make('proveedor.email')
                ->label('Email Proveedor'),
            ExportColumn::make('proveedor.telefono')
                ->label('Teléfono Proveedor'),
            ExportColumn::make('status')
                ->label('Estado'),
            ExportColumn::make('fecha_emision')
                ->label('Fecha de Emisión')
                ->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y') : ''),
            ExportColumn::make('fecha_entrega_esperada')
                ->label('Fecha Entrega Esperada')
                ->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y') : ''),
            ExportColumn::make('total_calculado')
                ->label('Total')
                ->formatStateUsing(fn ($state) => '$' . number_format($state, 2)),
            ExportColumn::make('user.name')
                ->label('Creada por'),
            ExportColumn::make('created_at')
                ->label('Fecha de Creación')
                ->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y H:i') : ''),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Se ha completado la exportación de órdenes de compra y ' . number_format($export->successful_rows) . ' ' . str('fila')->plural($export->successful_rows) . ' exportadas.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('fila')->plural($failedRowsCount) . ' fallaron.';
        }

        return $body;
    }
}
