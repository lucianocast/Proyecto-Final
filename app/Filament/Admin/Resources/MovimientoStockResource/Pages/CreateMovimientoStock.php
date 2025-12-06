<?php

namespace App\Filament\Admin\Resources\MovimientoStockResource\Pages;

use App\Filament\Admin\Resources\MovimientoStockResource;
use App\Models\Insumo;
use App\Models\Lote;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateMovimientoStock extends CreateRecord
{
    protected static string $resource = MovimientoStockResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Obtener el insumo
        $insumo = Insumo::findOrFail($data['insumo_id']);
        
        // Calcular stock actual
        $stockActual = $insumo->stock_total;
        $data['cantidad_anterior'] = $stockActual;
        
        // Calcular cantidad nueva según el tipo
        switch ($data['tipo']) {
            case 'entrada':
                $data['cantidad_nueva'] = $stockActual + $data['cantidad'];
                break;
            case 'salida':
                $data['cantidad_nueva'] = $stockActual - $data['cantidad'];
                break;
            case 'ajuste':
                $data['cantidad_nueva'] = $data['cantidad']; // El ajuste ES la cantidad nueva
                break;
        }
        
        // Agregar usuario autenticado
        $data['user_id'] = auth()->id();
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $movimiento = $this->record;
        $insumo = $movimiento->insumo;
        
        // Actualizar o crear lote
        switch ($movimiento->tipo) {
            case 'entrada':
                // Crear nuevo lote con la entrada
                Lote::create([
                    'insumo_id' => $insumo->id,
                    'cantidad_actual' => $movimiento->cantidad,
                    'cantidad_inicial' => $movimiento->cantidad,
                    'fecha_vencimiento' => now()->addMonths(6), // Default 6 meses
                    'proveedor' => 'Stock general',
                ]);
                break;
                
            case 'salida':
            case 'ajuste':
                // Ajustar lotes existentes (FIFO - First In First Out)
                $cantidadAjustar = abs($movimiento->cantidad_nueva - $movimiento->cantidad_anterior);
                $lotes = $insumo->lotes()->where('cantidad_actual', '>', 0)->oldest()->get();
                
                foreach ($lotes as $lote) {
                    if ($cantidadAjustar <= 0) break;
                    
                    if ($lote->cantidad_actual >= $cantidadAjustar) {
                        $lote->cantidad_actual -= $cantidadAjustar;
                        $lote->save();
                        $cantidadAjustar = 0;
                    } else {
                        $cantidadAjustar -= $lote->cantidad_actual;
                        $lote->cantidad_actual = 0;
                        $lote->save();
                    }
                }
                
                // Notificar si queda stock negativo pendiente de ajustar
                if ($cantidadAjustar > 0 && $movimiento->cantidad_nueva < 0) {
                    Notification::make()
                        ->warning()
                        ->title('Stock negativo')
                        ->body("El movimiento dejó un stock negativo de {$cantidadAjustar} unidades. Considere hacer un ajuste manual.")
                        ->persistent()
                        ->send();
                }
                break;
        }
        
        // Notificación de éxito
        Notification::make()
            ->success()
            ->title('Movimiento registrado')
            ->body("Stock de {$insumo->nombre} actualizado correctamente.")
            ->send();
    }
}
