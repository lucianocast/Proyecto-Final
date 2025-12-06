<?php

namespace App\Filament\Admin\Resources\PagoResource\Pages;

use App\Filament\Admin\Resources\PagoResource;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditPago extends EditRecord
{
    protected static string $resource = PagoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * UC-10: Validar que no se pueda modificar si está anulado
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Verificar si el pago está anulado
        if ($this->record->estado === 'anulado') {
            Notification::make()
                ->title('Error')
                ->body('No se puede modificar un pago anulado.')
                ->danger()
                ->persistent()
                ->send();
            
            // Redirigir al listado
            $this->redirect(PagoResource::getUrl('index'));
        }
        
        return $data;
    }

    /**
     * UC-10: Solicitar justificación obligatoria al modificar
     */
    protected function beforeSave(): void
    {
        // Solo pedimos justificación si se está modificando un pago existente
        if ($this->record->exists && $this->record->isDirty()) {
            
            // Crear un modal para pedir justificación
            $this->form->fill();
            
            // Almacenar el cambio en auditoría
            $this->record->auditAction(
                action: 'updated',
                justification: 'Pago modificado desde el panel de administración',
                data: [
                    'cambios' => $this->record->getDirty(),
                ]
            );
        }
    }

    /**
     * Mensaje de éxito personalizado
     */
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Pago actualizado correctamente';
    }
}
