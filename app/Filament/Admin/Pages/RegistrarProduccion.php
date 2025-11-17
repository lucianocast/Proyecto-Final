<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
// --- IMPORTACIONES NECESARIAS ---
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Models\Producto;
use App\Models\Lote;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
// --- FIN DE IMPORTACIONES ---

class RegistrarProduccion extends Page implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions; // Usamos los Traits

    protected static ?string $navigationIcon = 'heroicon-o-fire';
    protected static string $view = 'filament.admin.pages.registrar-produccion';
    protected static ?string $navigationGroup = 'Producción';
    protected static ?string $title = 'Registrar Producción';
    protected static ?string $slug = 'produccion/registrar';

    // Propiedad para mantener el estado del formulario
    public ?array $data = [];

    // Método Mount para inicializar el formulario
    public function mount(): void
    {
        $this->form->fill();
    }

    // Definición del Formulario (Paso 2)
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('producto_id')
                    ->label('Producto a Producir')
                    // Importante: Filtramos solo productos que tienen una receta
                    ->options(
                        Producto::whereHas('receta')->pluck('nombre', 'id')
                    )
                    ->searchable()
                    ->required(),
                TextInput::make('cantidad_producida')
                    ->label('Cantidad Producida')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->default(1),
            ])
            ->statePath('data'); // Conectamos el formulario a la propiedad $data
    }

    // Definición de la Acción (Botón)
    public function registrarProduccionAction(): Action
    {
        return Action::make('registrarProduccion')
            ->label('Registrar Producción y Consumir Stock')
            ->submit('registrarProduccion'); // Llama al método registrarProduccion() de abajo
    }

    // Lógica de Negocio (Paso 3 - El "Toque Senior")
    public function registrarProduccion(): void
    {
        // 1. Obtenemos los datos del formulario
        $data = $this->form->getState();
        $producto = Producto::with('receta.insumos')->find($data['producto_id']);
        $cantidadProducida = (int) $data['cantidad_producida'];

        if (!$producto->receta) {
            Notification::make()->title('Error')->body('Este producto no tiene una receta asociada.')->danger()->send();
            return;
        }

        $insumosRequeridos = [];
        
        // 2. VERIFICAMOS si hay stock SUFICIENTE para TODOS los insumos
        try {
            foreach ($producto->receta->insumos as $insumo) {
                // Calculamos cuánto necesitamos de este insumo
                $cantidadNecesaria = $insumo->pivot->cantidad * $cantidadProducida;
                
                // Sumamos el stock disponible en TODOS los lotes de este insumo
                $stockDisponible = Lote::where('insumo_id', $insumo->id)
                                      ->where('cantidad_actual', '>', 0)
                                      ->sum('cantidad_actual');
                
                if ($stockDisponible < $cantidadNecesaria) {
                    // Si falta UN solo insumo, lanzamos una excepción y detenemos todo
                    throw new \Exception("Stock insuficiente para {$insumo->nombre}. Necesario: {$cantidadNecesaria} {$insumo->unidad_de_medida}, Disponible: {$stockDisponible} {$insumo->unidad_de_medida}.");
                }
                
                // Si hay, lo guardamos para el próximo paso
                $insumosRequeridos[] = [
                    'insumo' => $insumo,
                    'cantidadNecesaria' => $cantidadNecesaria,
                ];
            }
        } catch (\Exception $e) {
            Notification::make()->title('Error de Stock')->body($e->getMessage())->danger()->send();
            return; // Detiene el proceso
        }

        // 3. Si llegamos aquí, HAY stock. Procedemos con el descuento (FIFO)
        try {
            DB::transaction(function () use ($insumosRequeridos) {
                
                foreach ($insumosRequeridos as $item) {
                    $insumo = $item['insumo'];
                    $cantidadADescontar = $item['cantidadNecesaria'];

                    // Obtenemos los lotes con stock, ordenados por fecha (FIFO)
                    $lotes = Lote::where('insumo_id', $insumo->id)
                                 ->where('cantidad_actual', '>', 0)
                                 ->orderBy('created_at', 'asc') // First-In, First-Out
                                 ->get();
                    
                    foreach ($lotes as $lote) {
                        if ($cantidadADescontar <= 0) break; // Ya descontamos todo lo necesario

                        // La cantidad a descontar de ESTE lote es o todo lo que tiene, o lo que nos falta
                        $descontarDeEsteLote = min($lote->cantidad_actual, $cantidadADescontar);
                        
                        $lote->cantidad_actual -= $descontarDeEsteLote;
                        $lote->save();
                        
                        // Reducimos lo que nos falta por descontar
                        $cantidadADescontar -= $descontarDeEsteLote;
                    }
                }
            });

            // Si la transacción fue exitosa
            Notification::make()->title('¡Producción Registrada!')->body('El stock de insumos ha sido descontado exitosamente.')->success()->send();
            $this->form->fill(); // Resetea el formulario

        } catch (\Exception $e) {
            // Esto podría pasar si hay un error de concurrencia
            Notification::make()->title('Error en Transacción')->body('No se pudo completar el descuento de stock. Intente de nuevo.')->danger()->send();
        }
    }
}