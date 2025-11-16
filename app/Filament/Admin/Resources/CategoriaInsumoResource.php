<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CategoriaInsumoResource\Pages;
use App\Filament\Admin\Resources\CategoriaInsumoResource\RelationManagers;
use App\Models\CategoriaInsumo;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoriaInsumoResource extends Resource
{
    protected static ?string $model = CategoriaInsumo::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Stock e Insumos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                Textarea::make('descripcion')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable(),
                TextColumn::make('descripcion')
                    ->searchable(),
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
            'index' => Pages\ListCategoriaInsumos::route('/'),
            'create' => Pages\CreateCategoriaInsumo::route('/create'),
            'edit' => Pages\EditCategoriaInsumo::route('/{record}/edit'),
        ];
    }
}
