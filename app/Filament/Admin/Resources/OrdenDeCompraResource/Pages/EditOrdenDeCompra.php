<?php

namespace App\Filament\Admin\Resources\OrdenDeCompraResource\Pages;

use App\Filament\Admin\Resources\OrdenDeCompraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrdenDeCompra extends EditRecord
{
    protected static string $resource = OrdenDeCompraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * UC-15: Validar que la OC no esté recibida o cancelada antes de editar
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (in_array($this->record->status, ['recibida_total', 'cancelada'])) {
            \Filament\Notifications\Notification::make()
                ->warning()
                ->title('Edición no permitida')
                ->body('No se pueden modificar órdenes de compra en estado "' . $this->record->status . '".')
                ->persistent()
                ->send();
            
            $this->redirect(OrdenDeCompraResource::getUrl('index'));
        }

        return $data;
    }

    /**
     * UC-15: Registrar cambios en auditoría automáticamente
     */
    protected function beforeSave(): void
    {
        $cambios = $this->record->getDirty();
        
        if (!empty($cambios)) {
            // Preparar datos para auditoría
            $datosAuditoria = [
                'campos_modificados' => array_keys($cambios),
                'valores_anteriores' => $this->record->getOriginal(),
                'valores_nuevos' => $cambios,
            ];
            
            // Identificar cambios sensibles
            if (isset($cambios['proveedor_id'])) {
                $datosAuditoria['cambio_critico'] = 'proveedor';
                $datosAuditoria['proveedor_anterior'] = $this->record->getOriginal('proveedor_id');
                $datosAuditoria['proveedor_nuevo'] = $cambios['proveedor_id'];
            }
            
            if (isset($cambios['total_calculado'])) {
                $datosAuditoria['cambio_critico'] = 'costo_total';
                $datosAuditoria['costo_anterior'] = $this->record->getOriginal('total_calculado');
                $datosAuditoria['costo_nuevo'] = $cambios['total_calculado'];
            }
            
            if (isset($cambios['fecha_entrega_esperada'])) {
                $datosAuditoria['cambio_fecha_entrega'] = true;
            }
            
            // Registrar en auditoría usando el trait Auditable
            $this->record->auditAction(
                action: 'modificar_orden_compra',
                justification: 'Modificación de orden de compra mediante panel administrativo',
                data: $datosAuditoria
            );
        }
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Orden de compra actualizada. Cambios registrados en auditoría.';
    }
}
