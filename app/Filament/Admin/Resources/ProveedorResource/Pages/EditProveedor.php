<?php

namespace App\Filament\Admin\Resources\ProveedorResource\Pages;

use App\Filament\Admin\Resources\ProveedorResource;
use App\Models\AuditLog;
use App\Models\Proveedor;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProveedor extends EditRecord
{
    protected static string $resource = ProveedorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        // UC-46: Detectar cambios importantes que requieren auditoría
        $cambiosImportantes = false;
        $camposImportantes = ['nombre_empresa', 'cuit', 'email_pedidos', 'telefono', 'direccion', 'notas', 'activo'];
        
        foreach ($camposImportantes as $campo) {
            if ($this->record->isDirty($campo)) {
                $cambiosImportantes = true;
                break;
            }
        }

        if (!$cambiosImportantes) {
            return;
        }

        // Solicitar justificación
        if (!$this->data['justificacion_cambio'] ?? false) {
            Notification::make()
                ->danger()
                ->title('Justificación requerida')
                ->body('Los cambios en datos del proveedor requieren una justificación.')
                ->send();
                
            $this->halt();
        }
    }

    protected function afterSave(): void
    {
        // UC-46: Registrar cambios en auditoría si hubo justificación
        if ($justificacion = $this->data['justificacion_cambio'] ?? null) {
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'update',
                'auditable_type' => Proveedor::class,
                'auditable_id' => $this->record->id,
                'old_values' => $this->record->getOriginal(),
                'new_values' => $this->record->getAttributes(),
                'justification' => $justificacion,
            ]);
        }
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Agregar campo temporal para justificación
        $data['justificacion_cambio'] = '';
        return $data;
    }
}
