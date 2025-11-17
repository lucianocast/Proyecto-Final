<?php

namespace App\Filament\Admin\Resources\PedidoResource\RelationManagers;

// --- IMPORTACIONES NECESARIAS ---
use App\Models\ProductoVariante;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
// --- FIN DE IMPORTACIONES ---

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('producto_variante_id')
                    ->label('Producto (Variante)')
                    ->options(function () {
                        // Creamos una lista legible: "Torta de Chocolate - 18cm"
                        return ProductoVariante::with('producto')
                            ->get()
                            ->mapWithKeys(function ($variante) {
                                return [$variante->id => $variante->producto->nombre . ' - ' . $variante->descripcion];
                            });
                    })
                    ->required()
                    ->searchable()
                    ->live() // Reactivo
                    ->reactive()
                    // Lógica para autocompletar el precio
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        if (!$state) {
                            return;
                        }
                        $variante = ProductoVariante::find($state);
                        if ($variante) {
                            $set('precio_unitario', $variante->precio);
                        }
                    }),
                
                TextInput::make('cantidad')
                    ->numeric()
                    ->required()
                    ->default(1)
                    ->reactive()
                    ->minValue(1),
                    
                TextInput::make('precio_unitario')
                    ->numeric()
                    ->required()
                    ->prefix('$')
                    ->label('Precio Unitario')
                    ->readOnly() // El precio es automático, no editable
                    ->reactive(),
                    
                Placeholder::make('subtotal_placeholder')
                    ->label('Subtotal')
                    ->content(function (Get $get): string {
                        $cantidad = (float) ($get('cantidad') ?? 0);
                        $precioUnitario = (float) ($get('precio_unitario') ?? 0);
                        $subtotal = $cantidad * $precioUnitario;
                        
                        return '$ ' . number_format($subtotal, 2, ',', '.');
                    }),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id') // Ajustado, ya que el item no tiene nombre propio
            ->columns([
                TextColumn::make('productoVariante.producto.nombre')
                    ->label('Producto')
                    ->searchable(),
                TextColumn::make('productoVariante.descripcion')
                    ->label('Variante'),
                TextColumn::make('cantidad'),
                TextColumn::make('precio_unitario')
                    ->money('ARS')
                    ->label('P. Unitario'),
                TextColumn::make('subtotal')
                    ->money('ARS')
                    ->state(function ($record): float {
                        return $record->cantidad * $record->precio_unitario;
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    // Calculamos subtotal en backend para 'Crear'
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['subtotal'] = $data['cantidad'] * $data['precio_unitario'];
                        return $data;
                    })
                    // Actualizamos el total del pedido después de crear
                    ->after(fn (RelationManager $livewire) => static::updatePedidoTotal($livewire)),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    // Calculamos subtotal en backend para 'Editar'
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['subtotal'] = $data['cantidad'] * $data['precio_unitario'];
                        return $data;
                    })
                    // Actualizamos el total del pedido después de editar
                    ->after(fn (RelationManager $livewire) => static::updatePedidoTotal($livewire)),
                Tables\Actions\DeleteAction::make()
                    // Actualizamos el total del pedido después de borrar
                    ->after(fn (RelationManager $livewire) => static::updatePedidoTotal($livewire)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        // Actualizamos el total del pedido después de borrar en masa
                        ->after(fn (RelationManager $livewire) => static::updatePedidoTotal($livewire)),
                ]),
            ]);
    }

    /**
     * Función para recalcular y guardar el total en el Pedido (cabecera).
     */
    public static function updatePedidoTotal(RelationManager $livewire): void
    {
        $pedido = $livewire->ownerRecord;

        // 1. Recalculamos el total sumando los subtotales
        $total = $pedido->items()->sum(
            DB::raw('cantidad * precio_unitario')
        );

        // 2. Obtenemos el monto abonado actual
        $monto_abonado = $pedido->monto_abonado;

        // 3. Calculamos y actualizamos los tres campos
        $pedido->total_calculado = $total;
        $pedido->saldo_pendiente = $total - $monto_abonado;
        $pedido->save();

        // 4. Emitimos el evento
        $livewire->dispatch('pedidoTotalActualizado');
    }
}
