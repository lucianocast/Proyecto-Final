<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RecetaResource\Pages;
use App\Filament\Admin\Resources\RecetaResource\RelationManagers;
use App\Models\Receta;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RecetaResource extends Resource
{
    protected static ?string $model = Receta::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Producción';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('producto_id')
                    ->relationship('producto', 'nombre')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Producto'),
                TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                Textarea::make('descripcion')
                    ->columnSpanFull(),
                RichEditor::make('instrucciones')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('porciones')
                    ->required()
                    ->numeric(),
                TextInput::make('tiempo_preparacion')
                    ->required()
                    ->maxLength(255),
                Toggle::make('activo')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable(),
                TextColumn::make('producto.nombre')
                    ->sortable()
                    ->searchable()
                    ->label('Producto'),
                IconColumn::make('activo')
                    ->boolean(),
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
            RelationManagers\InsumosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecetas::route('/'),
            'create' => Pages\CreateReceta::route('/create'),
            'edit' => Pages\EditReceta::route('/{record}/edit'),
        ];
    }
}
