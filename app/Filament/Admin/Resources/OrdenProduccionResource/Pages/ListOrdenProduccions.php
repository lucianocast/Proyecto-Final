<?php

namespace App\Filament\Admin\Resources\OrdenProduccionResource\Pages;

use App\Filament\Admin\Resources\OrdenProduccionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrdenProduccions extends ListRecords
{
    protected static string $resource = OrdenProduccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
