<?php

namespace App\Filament\Admin\Resources\RecetaResource\Pages;

use App\Filament\Admin\Resources\RecetaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateReceta extends CreateRecord
{
    protected static string $resource = RecetaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Receta creada')
            ->body('Ahora puede agregar los insumos necesarios en la pestaña "Insumos".')
            ->success()
            ->send();
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Receta registrada correctamente. Agregue los insumos para calcular el costo primo.';
    }
}
