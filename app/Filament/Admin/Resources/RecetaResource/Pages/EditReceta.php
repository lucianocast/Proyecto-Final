<?php

namespace App\Filament\Admin\Resources\RecetaResource\Pages;

use App\Filament\Admin\Resources\RecetaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReceta extends EditRecord
{
    protected static string $resource = RecetaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
