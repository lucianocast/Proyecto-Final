<?php

namespace App\Filament\Admin\Resources\ProveedorResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
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
                    ->label('Insumo'),
                TextColumn::make('unidad_de_medida')
                    ->label('Unidad'),
                TextInputColumn::make('precio')
                    ->rules(['required', 'numeric', 'min:0'])
                    ->label('Precio'),
                TextInputColumn::make('unidad_de_compra')
                    ->rules(['required', 'max:255'])
                    ->label('Unidad de Compra'),
                TextInputColumn::make('factor_de_conversion')
                    ->rules(['required', 'numeric', 'min:0'])
                    ->label('Factor Conversión'),
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
                        $action->getRecordSelect(),
                        TextInput::make('precio')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->label('Precio'),
                        TextInput::make('unidad_de_compra')
                            ->required()
                            ->maxLength(255)
                            ->label('Unidad de Compra'),
                        TextInput::make('factor_de_conversion')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->label('Factor de Conversión a Stock'),
                    ]),
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
