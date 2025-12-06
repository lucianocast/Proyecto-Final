<?php

namespace App\Filament\Admin\Resources\RecetaResource\Pages;

use App\Filament\Admin\Resources\RecetaResource;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditReceta extends EditRecord
{
    protected static string $resource = RecetaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalDescription('Esta acción eliminará permanentemente la receta.'),
            
            Actions\Action::make('recalcular_costo')
                ->icon('heroicon-o-calculator')
                ->color('info')
                ->action(function () {
                    $this->record->actualizarCosto();
                    
                    Notification::make()
                        ->title('Costo recalculado')
                        ->body('El costo primo se actualizó a $' . number_format($this->record->costo_total_calculado, 2))
                        ->success()
                        ->send();
                    
                    $this->refreshFormData(['costo_total_calculado']);
                }),
        ];
    }

    protected function beforeSave(): void
    {
        // Obtener los cambios
        $cambios = $this->record->getDirty();
        
        // Si hay cambios significativos, registrar en auditoría
        if (!empty($cambios)) {
            $camposSensibles = ['nombre', 'rendimiento', 'porciones', 'activo', 'producto_id'];
            $cambiosCriticos = array_intersect_key($cambios, array_flip($camposSensibles));
            
            if (!empty($cambiosCriticos)) {
                $valoresAnteriores = [];
                $valoresNuevos = [];
                
                foreach ($cambiosCriticos as $campo => $valorNuevo) {
                    $valoresAnteriores[$campo] = $this->record->getOriginal($campo);
                    $valoresNuevos[$campo] = $valorNuevo;
                }
                
                $datosAuditoria = [
                    'campos_modificados' => array_keys($cambiosCriticos),
                    'valores_anteriores' => $valoresAnteriores,
                    'valores_nuevos' => $valoresNuevos,
                    'nombre_receta' => $this->record->nombre,
                ];
                
                // Registrar auditoría
                $this->record->auditAction('modificar_receta', 'Modificación de receta', $datosAuditoria);
            }
        }
    }

    protected function afterSave(): void
    {
        // Recalcular costo si cambió la estructura de insumos
        if ($this->record->wasChanged(['porciones', 'rendimiento'])) {
            $this->record->actualizarCosto();
        }
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Receta actualizada correctamente. Los cambios han sido registrados en el log de auditoría.';
    }
}
