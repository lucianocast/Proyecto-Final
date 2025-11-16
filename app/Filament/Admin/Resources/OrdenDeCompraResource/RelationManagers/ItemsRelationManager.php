<?php

namespace App\Filament\Admin\Resources\OrdenDeCompraResource\RelationManagers;

use App\Models\Proveedor;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('insumo_id')
                    ->label('Insumo')
                    ->options(function (Get $get, RelationManager $livewire): array {
                        $proveedorId = $livewire->getOwnerRecord()->proveedor_id;
                        
                        if (!$proveedorId) {
                            return [];
                        }
                        
                        $proveedor = Proveedor::find($proveedorId);
                        
                        if (!$proveedor) {
                            return [];
                        }
                        
                        return $proveedor->insumos()
                            ->pluck('nombre', 'insumos.id')
                            ->toArray();
                    })
                    ->required()
                    ->live()
                    ->reactive()
                    ->afterStateUpdated(function (Set $set, ?string $state, RelationManager $livewire) {
                        if (!$state) {
                            return;
                        }
                        
                        $proveedorId = $livewire->getOwnerRecord()->proveedor_id;
                        $insumoId = $state;
                        
                        // Buscar el precio en la tabla pivot insumo_proveedor
                        $precio = \DB::table('insumo_proveedor')
                            ->where('proveedor_id', $proveedorId)
                            ->where('insumo_id', $insumoId)
                            ->value('precio');
                        
                        if ($precio) {
                            $set('precio_unitario', $precio);
                        }
                    }),
                    
                TextInput::make('cantidad')
                    ->numeric()
                    ->required()
                    ->default(1)
                    ->reactive()
                    ->minValue(0.01),
                    
                TextInput::make('precio_unitario')
                    ->numeric()
                    ->required()
                    ->reactive()
                    ->prefix('$')
                    ->label('Precio Unitario'),
                    
                Placeholder::make('subtotal')
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
            ->recordTitleAttribute('insumo.nombre')
            ->columns([
                TextColumn::make('insumo.nombre')
                    ->label('Insumo')
                    ->searchable(),
                TextColumn::make('cantidad')
                    ->numeric(),
                TextColumn::make('precio_unitario')
                    ->money('ARS')
                    ->label('Precio Unitario'),
                TextColumn::make('subtotal')
                    ->label('Subtotal')
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
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['subtotal'] = $data['cantidad'] * $data['precio_unitario'];
                        return $data;
                    })
                    ->after(fn (RelationManager $livewire) => static::updateTotal($livewire)),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['subtotal'] = $data['cantidad'] * $data['precio_unitario'];
                        return $data;
                    })
                    ->after(fn (RelationManager $livewire) => static::updateTotal($livewire)),
                Tables\Actions\DeleteAction::make()
                    ->after(fn (RelationManager $livewire) => static::updateTotal($livewire)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(fn (RelationManager $livewire) => static::updateTotal($livewire)),
                ]),
            ]);
    }
    
    public static function updateTotal(RelationManager $livewire): void
    {
        // Obtenemos la OrdenDeCompra dueña de esta relación
        $orden = $livewire->ownerRecord;

        // Recalculamos el total sumando los subtotales de sus items
        // (cantidad * precio_unitario)
        $total = $orden->items()->sum(
            DB::raw('cantidad * precio_unitario')
        );

        // Actualizamos el campo 'total_calculado' en la OrdenDeCompra
        $orden->total_calculado = $total;
        $orden->save();
    }
}
