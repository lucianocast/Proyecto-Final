<?php

namespace App\Filament\Admin\Resources\InsumoResource\Pages;

use App\Filament\Admin\Resources\InsumoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInsumos extends ListRecords
{
    protected static string $resource = InsumoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
