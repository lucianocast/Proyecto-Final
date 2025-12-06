<?php

namespace App\Filament\Exports;

use App\Models\Pedido;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PedidoExporter extends Exporter
{
    protected static ?string $model = Pedido::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('cliente.nombre')
                ->label('Cliente'),
            ExportColumn::make('cliente.email')
                ->label('Email Cliente'),
            ExportColumn::make('cliente.telefono')
                ->label('Teléfono Cliente'),
            ExportColumn::make('status')
                ->label('Estado'),
            ExportColumn::make('fecha_entrega')
                ->label('Fecha Entrega')
                ->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y H:i') : ''),
            ExportColumn::make('forma_entrega')
                ->label('Forma de Entrega'),
            ExportColumn::make('direccion_envio')
                ->label('Dirección de Envío'),
            ExportColumn::make('metodo_pago')
                ->label('Método de Pago'),
            ExportColumn::make('total_calculado')
                ->label('Total')
                ->formatStateUsing(fn ($state) => '$' . number_format($state, 2)),
            ExportColumn::make('monto_abonado')
                ->label('Monto Abonado')
                ->formatStateUsing(fn ($state) => '$' . number_format($state, 2)),
            ExportColumn::make('saldo_pendiente')
                ->label('Saldo Pendiente')
                ->formatStateUsing(fn ($state) => '$' . number_format($state, 2)),
            ExportColumn::make('vendedor.name')
                ->label('Vendedor'),
            ExportColumn::make('observaciones')
                ->label('Observaciones'),
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
        $body = 'Se ha completado la exportación de pedidos y ' . number_format($export->successful_rows) . ' ' . str('fila')->plural($export->successful_rows) . ' exportadas.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('fila')->plural($failedRowsCount) . ' fallaron.';
        }

        return $body;
    }
}
