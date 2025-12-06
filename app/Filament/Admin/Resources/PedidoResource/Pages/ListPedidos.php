<?php

namespace App\Filament\Admin\Resources\PedidoResource\Pages;

use App\Filament\Admin\Resources\PedidoResource;
use App\Filament\Exports\PedidoExporter;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPedidos extends ListRecords
{
    protected static string $resource = PedidoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ExportAction::make()
                ->exporter(PedidoExporter::class)
                ->label('Exportar a Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success'),
            Actions\CreateAction::make(),
        ];
    }
}
