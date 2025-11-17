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
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ToggleColumn;
use App\Models\CategoriaProducto;

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
                    ->relationship(name: 'categoria', titleAttribute: 'nombre')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Categoría'),

                TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),

                Textarea::make('descripcion')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                
                Toggle::make('activo')
                    ->required(),
                
                // --- CAMPOS DE CATÁLOGO ---
                Toggle::make('visible_en_catalogo')
                    ->label('Visible en Catálogo')
                    ->default(true),
                
                FileUpload::make('imagen_url')
                    ->label('Imagen')
                    ->directory('productos')
                    ->image()
                    ->columnSpanFull(),
                
                TagsInput::make('etiquetas')
                    ->label('Etiquetas')
                    ->placeholder('Ej: Sin TACC, Destacado, Vegano')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable(),
                
                // --- COLUMNA DE CATEGORÍA MEJORADA ---
                TextColumn::make('categoria.nombre')
                    ->numeric()
                    ->sortable()
                    ->searchable(),

                IconColumn::make('activo')
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
            RelationManagers\VariantesRelationManager::class,
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