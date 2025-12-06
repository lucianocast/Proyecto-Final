<?php

namespace App\Filament\Admin\Resources\OrdenDeCompraResource\Pages;

use App\Filament\Admin\Resources\OrdenDeCompraResource;
use App\Filament\Exports\OrdenDeCompraExporter;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrdenDeCompras extends ListRecords
{
    protected static string $resource = OrdenDeCompraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ExportAction::make()
                ->exporter(OrdenDeCompraExporter::class)
                ->label('Exportar a Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success'),
            Actions\CreateAction::make(),
        ];
    }
}
