<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductoResource\Pages;
use App\Filament\Admin\Resources\ProductoResource\RelationManagers;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// --- IMPORTACIONES MEJORADAS ---
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ToggleColumn; // Opcional, si quieres editarlo en la tabla

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static ?string $navigationIcon = 'heroicon-o-cake'; // <- Ícono temático
    protected static ?string $navigationGroup = 'Producción'; // <- Agrupado

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // --- CAMPO DE CATEGORÍA MEJORADO ---
                Select::make('categoria_producto_id')
                    ->relationship(name: 'categoria', titleAttribute: 'nombre') // Usa la relación
                    ->searchable()
                    ->preload() // Carga las categorías al abrir
                    ->required()
                    ->label('Categoría'),

                TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),

                Textarea::make('descripcion') // <- Mejorado a Textarea
                    ->maxLength(65535)
                    ->columnSpanFull(), // Ocupa todo el ancho

                TextInput::make('precio') // <- Campo numérico
                    ->required()
                    ->numeric()
                    ->prefix('$') // Prefijo de moneda
                    ->step('0.01'),

                TextInput::make('stock_minimo')
                    ->required()
                    ->numeric()
                    ->minValue(0),
                
                Toggle::make('activo') // <- Mejorado a Toggle
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable(),
                
                // --- COLUMNA DE CATEGORÍA MEJORADA ---
                TextColumn::make('categoria.nombre') // Muestra el nombre de la categoría
                    ->numeric()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('precio') // <- Columna de precio mejorada
                    ->money('ARS') // Formato de moneda
                    ->sortable(),

                TextColumn::make('stock_minimo')
                    ->numeric()
                    ->sortable(),

                IconColumn::make('activo') // <- Columna de activo mejorada
                    ->boolean(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProducto::route('/create'),
            'edit' => Pages\EditProducto::route('/{record}/edit'),
        ];
    }    
}