<?php

namespace App\Filament\Admin\Resources\MovimientoStockResource\Pages;

use App\Filament\Admin\Resources\MovimientoStockResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMovimientoStock extends EditRecord
{
    protected static string $resource = MovimientoStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
