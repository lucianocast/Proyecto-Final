<?php

namespace App\Filament\Admin\Resources\ProductoResource\Pages;

use App\Filament\Admin\Resources\ProductoResource;
use App\Filament\Exports\ProductoExporter;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductos extends ListRecords
{
    protected static string $resource = ProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ExportAction::make()
                ->exporter(ProductoExporter::class)
                ->label('Exportar a Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success'),
            Actions\CreateAction::make(),
        ];
    }
}
