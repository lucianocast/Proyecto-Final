<?php

namespace App\Filament\Admin\Resources\CategoriaProductoResource\Pages;

use App\Filament\Admin\Resources\CategoriaProductoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategoriaProductos extends ListRecords
{
    protected static string $resource = CategoriaProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
