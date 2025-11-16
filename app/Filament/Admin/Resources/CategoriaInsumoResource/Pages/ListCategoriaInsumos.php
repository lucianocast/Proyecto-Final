<?php

namespace App\Filament\Admin\Resources\CategoriaInsumoResource\Pages;

use App\Filament\Admin\Resources\CategoriaInsumoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategoriaInsumos extends ListRecords
{
    protected static string $resource = CategoriaInsumoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
