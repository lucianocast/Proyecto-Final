<?php

namespace App\Filament\Admin\Resources\OrdenDeCompraResource\Pages;

use App\Filament\Admin\Resources\OrdenDeCompraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrdenDeCompra extends EditRecord
{
    protected static string $resource = OrdenDeCompraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
