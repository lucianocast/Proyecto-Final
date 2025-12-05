<?php

namespace App\Filament\Admin\Resources\ProveedorResource\RelationManagers;

use App\Enums\UnidadMedida;
use App\Helpers\ConversionHelper;
use App\Models\Insumo;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CatalogoInsumosRelationManager extends RelationManager
{
    protected static string $relationship = 'insumos';
    
    protected static ?string $inverseRelationship = 'proveedores';

    public function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
            ->columns([
                TextColumn::make('nombre')
                    ->searchable()
                    ->label('Insumo')
                    ->sortable(),
                TextColumn::make('unidad_de_medida')
                    ->label('Unidad Base')
                    ->badge()
                    ->color('gray'),
                TextInputColumn::make('precio')
                    ->rules(['required', 'numeric', 'min:0'])
                    ->label('Precio'),
                TextColumn::make('unidad_compra')
                    ->label('Unidad Venta')
                    ->badge(),
                TextInputColumn::make('cantidad_por_bulto')
                    ->rules(['required', 'numeric', 'min:0.01'])
                    ->label('Cantidad/Bulto')
                    ->default(1),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->recordSelectOptionsQuery(function (Builder $query) {
                        $proveedorId = $this->getOwnerRecord()->id;
                        return $query->whereNotExists(function ($subquery) use ($proveedorId) {
                            $subquery->select('*')
                                ->from('insumo_proveedor')
                                ->whereColumn('insumo_proveedor.insumo_id', 'insumos.id')
                                ->where('insumo_proveedor.proveedor_id', $proveedorId);
                        });
                    })
                    ->recordSelectSearchColumns(['nombre', 'descripcion'])
                    ->preloadRecordSelect()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $insumo = Insumo::find($state);
                                    $set('unidad_base_info', $insumo?->unidad_de_medida?->getLabel() ?? '');
                                }
                            }),
                        
                        TextInput::make('unidad_base_info')
                            ->label('Unidad Base del Insumo')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Esta es la unidad en que se medirá el stock'),
                        
                        TextInput::make('precio')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->label('Precio Unitario')
                            ->helperText('Precio por unidad de venta del proveedor'),
                        
                        Select::make('unidad_compra')
                            ->required()
                            ->options(UnidadMedida::class)
                            ->searchable()
                            ->live()
                            ->label('Unidad de Compra')
                            ->helperText('Unidad en que el proveedor vende este insumo')
                            ->afterStateUpdated(function ($state, Get $get, $set) {
                                if (!$state) return;
                                
                                $insumoId = $get('recordId');
                                if (!$insumoId) return;
                                
                                $insumo = Insumo::find($insumoId);
                                $unidadCompra = UnidadMedida::from($state);
                                $unidadBase = $insumo->unidad_de_medida;
                                
                                // Validar compatibilidad
                                if (!ConversionHelper::sonCompatibles($unidadCompra, $unidadBase)) {
                                    Notification::make()
                                        ->warning()
                                        ->title('Unidades incompatibles')
                                        ->body("No se puede convertir {$unidadCompra->getLabel()} a {$unidadBase->getLabel()}")
                                        ->send();
                                    
                                    $set('unidad_compra', null);
                                    return;
                                }
                                
                                // Calcular factor automático si son diferentes
                                if ($unidadCompra !== $unidadBase) {
                                    $factor = ConversionHelper::calcularFactorConversion($unidadCompra, $unidadBase);
                                    $set('cantidad_por_bulto', $factor);
                                }
                            }),
                        
                        TextInput::make('cantidad_por_bulto')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(0.01)
                            ->label('Cantidad por Bulto')
                            ->helperText(function (Get $get) {
                                $unidadCompra = $get('unidad_compra');
                                $insumoId = $get('recordId');
                                
                                if ($unidadCompra && $insumoId) {
                                    $insumo = Insumo::find($insumoId);
                                    return "Ej: Si compras 1 {$unidadCompra}, cuántos {$insumo->unidad_de_medida->value} contiene";
                                }
                                
                                return 'Define cuántas unidades base contiene cada bulto/paquete';
                            }),
                    ])
                    ->before(function (array $data) {
                        // Validación final antes de guardar
                        $insumo = Insumo::find($data['recordId']);
                        $unidadCompra = UnidadMedida::from($data['unidad_compra']);
                        
                        if (!ConversionHelper::sonCompatibles($unidadCompra, $insumo->unidad_de_medida)) {
                            Notification::make()
                                ->danger()
                                ->title('Error de Validación')
                                ->body('Las unidades de compra y base no son compatibles')
                                ->send();
                            
                            $this->halt();
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
