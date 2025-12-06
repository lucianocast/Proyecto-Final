<?php

namespace App\Filament\Admin\Resources\PedidoResource\Pages;

use App\Filament\Admin\Resources\PedidoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Livewire\Attributes\On;

class EditPedido extends EditRecord
{
    protected static string $resource = PedidoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    #[On('pedidoTotalActualizado')] 
    public function refreshFormTotals(): void
    {
        $this->fillForm();
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Calcular el subtotal para cada item antes de guardar
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $key => $item) {
                $cantidad = (float) ($item['cantidad'] ?? 0);
                $precioUnitario = (float) ($item['precio_unitario'] ?? 0);
                $data['items'][$key]['subtotal'] = $cantidad * $precioUnitario;
            }
        }
        
        return $data;
    }
}
