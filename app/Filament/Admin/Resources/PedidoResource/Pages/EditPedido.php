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
}
