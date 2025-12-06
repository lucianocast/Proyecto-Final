<?php

namespace App\Filament\Admin\Resources\ProductoResource\Pages;

use App\Filament\Admin\Resources\ProductoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProducto extends EditRecord
{
    protected static string $resource = ProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * UC-23: Registrar cambios sensibles en auditoría
     * Se auditan automáticamente todos los cambios en campos del producto
     */
    protected function beforeSave(): void
    {
        // Obtener campos modificados
        $cambios = $this->record->getDirty();
        
        if (!empty($cambios)) {
            // Campos sensibles que requieren auditoría especial
            $camposSensibles = ['categoria_producto_id', 'activo', 'nombre', 'visible_en_catalogo'];
            $cambiosSensibles = array_intersect_key($cambios, array_flip($camposSensibles));
            
            if (!empty($cambiosSensibles)) {
                // Preparar datos para auditoría
                $datosAuditoria = [
                    'campos_modificados' => array_keys($cambios),
                    'valores_anteriores' => $this->record->getOriginal(),
                    'valores_nuevos' => $cambios,
                ];
                
                // Si cambió el estado activo, es crítico
                if (isset($cambios['activo'])) {
                    $datosAuditoria['cambio_critico'] = 'estado_activo';
                    $datosAuditoria['activo_anterior'] = $this->record->getOriginal('activo') ? 'Activo' : 'Inactivo';
                    $datosAuditoria['activo_nuevo'] = $cambios['activo'] ? 'Activo' : 'Inactivo';
                }
                
                if (isset($cambios['categoria_producto_id'])) {
                    $datosAuditoria['cambio_critico'] = 'categoria';
                    $datosAuditoria['categoria_anterior'] = $this->record->getOriginal('categoria_producto_id');
                    $datosAuditoria['categoria_nueva'] = $cambios['categoria_producto_id'];
                }
                
                // Registrar en auditoría
                $this->record->auditAction(
                    action: 'modificar_producto',
                    justification: 'Modificación de producto mediante panel administrativo',
                    data: $datosAuditoria
                );
            }
        }
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Producto actualizado correctamente. Cambios registrados en auditoría.';
    }
}
