<?php

namespace App\Filament\Admin\Resources\PedidoResource\Pages;

use App\Filament\Admin\Resources\PedidoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePedido extends CreateRecord
{
    protected static string $resource = PedidoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Asigna el ID del usuario autenticado (el vendedor) al campo user_id
        $data['user_id'] = auth()->id();
        
        return $data;
    }
}
