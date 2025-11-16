<?php

namespace App\Filament\Admin\Resources\CategoriaInsumoResource\Pages;

use App\Filament\Admin\Resources\CategoriaInsumoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategoriaInsumo extends EditRecord
{
    protected static string $resource = CategoriaInsumoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
