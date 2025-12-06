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
        
        // Calcular el subtotal para cada item antes de guardar (CRÍTICO - asegura que siempre exista)
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $key => $item) {
                $cantidad = (float) ($item['cantidad'] ?? 0);
                $precioUnitario = (float) ($item['precio_unitario'] ?? 0);
                // FORZAR el cálculo del subtotal - no confiar en el valor del formulario
                $data['items'][$key]['subtotal'] = $cantidad * $precioUnitario;
            }
        }
        
        return $data;
    }
    
    protected function afterCreate(): void
    {
        // Log para debugging si es necesario
        \Log::info('Pedido creado', [
            'pedido_id' => $this->record->id,
            'items_count' => $this->record->items()->count()
        ]);
    }
}
