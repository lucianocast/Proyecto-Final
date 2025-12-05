<?php

namespace App\Filament\Admin\Resources;

use App\Enums\UnidadMedida;
use App\Filament\Admin\Resources\InsumoResource\Pages;
use App\Filament\Admin\Resources\InsumoResource\RelationManagers;
use App\Models\Insumo;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InsumoResource extends Resource
{
    protected static ?string $model = Insumo::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Stock e Insumos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('categoria_insumo_id')
                    ->relationship('categoria', 'nombre')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Categoría'),
                TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                Textarea::make('descripcion')
                    ->columnSpanFull(),
                Select::make('unidad_de_medida')
                    ->required()
                    ->options(UnidadMedida::class)
                    ->searchable()
                    ->label('Unidad de Medida'),
                TextInput::make('precio_unitario')
                    ->readonly()
                    ->label('Precio Unitario'),
                TextInput::make('stock_actual')
                    ->numeric()
                    ->hiddenOn('create')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn ($record) => $record ? $record->lotes()->sum('cantidad_actual') : 0),
                TextInput::make('stock_minimo')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable(),
                TextColumn::make('categoria.nombre')
                    ->sortable()
                    ->searchable()
                    ->label('Categoría'),
                TextColumn::make('unidad_de_medida')
                    ->label('Unidad'),
                TextColumn::make('stock_actual')
                    ->numeric()
                    ->label('Stock Actual')
                    ->state(fn ($record) => $record->lotes()->sum('cantidad_actual')),
                TextColumn::make('stock_minimo')
                    ->numeric()
                    ->label('Stock Mínimo'),
                TextColumn::make('precio')
                    ->money('ARS')
                    ->label('Precio'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInsumos::route('/'),
            'create' => Pages\CreateInsumo::route('/create'),
            'edit' => Pages\EditInsumo::route('/{record}/edit'),
        ];
    }
}
