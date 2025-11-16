<?php

namespace App\Filament\Admin\Resources\CategoriaProductoResource\Pages;

use App\Filament\Admin\Resources\CategoriaProductoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategoriaProducto extends EditRecord
{
    protected static string $resource = CategoriaProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
