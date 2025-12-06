<?php

namespace App\Filament\Admin\Resources\ClienteResource\Pages;

use App\Filament\Admin\Resources\ClienteResource;
use App\Models\AuditLog;
use App\Models\Cliente;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Textarea;

class EditCliente extends EditRecord
{
    protected static string $resource = ClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        // Detectar cambios importantes que requieren auditoría
        $cambiosImportantes = false;
        $camposImportantes = ['nombre', 'email', 'telefono', 'direccion', 'activo'];
        
        foreach ($camposImportantes as $campo) {
            if ($this->record->isDirty($campo)) {
                $cambiosImportantes = true;
                break;
            }
        }

        // Si no hay cambios importantes, permitir guardar
        if (!$cambiosImportantes) {
            return;
        }

        // Si hay cambios pero no hay justificación (validación redundante por seguridad)
        if (empty($this->data['justificacion_cambio'])) {
            Notification::make()
                ->danger()
                ->title('Justificación requerida')
                ->body('Debe proporcionar una justificación de al menos 10 caracteres para modificar los datos del cliente.')
                ->persistent()
                ->send();
                
            $this->halt();
        }
    }

    protected function afterSave(): void
    {
        // El trait Auditable ya registra automáticamente los cambios
        // Si hay justificación, actualizar el último registro de auditoría
        if ($justificacion = $this->data['justificacion_cambio'] ?? null) {
            AuditLog::where('auditable_type', Cliente::class)
                ->where('auditable_id', $this->record->id)
                ->where('user_id', auth()->id())
                ->latest()
                ->first()
                ?->update(['justification' => $justificacion]);
        }
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Agregar campo temporal para justificación
        $data['justificacion_cambio'] = '';
        return $data;
    }
}
