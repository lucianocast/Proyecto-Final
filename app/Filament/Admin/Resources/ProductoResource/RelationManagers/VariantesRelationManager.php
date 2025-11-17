<?php

namespace App\Filament\Admin\Resources\ProductoResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VariantesRelationManager extends RelationManager
{
    protected static string $relationship = 'variantes';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('descripcion')
                    ->label('Descripción (Ej: 18cm, 1kg, Porción)')
                    ->required()
                    ->maxLength(255),
                TextInput::make('precio')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
                TextInput::make('orden')
                    ->label('Orden')
                    ->numeric()
                    ->default(0)
                    ->helperText('Un número más bajo se muestra primero.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('descripcion')
            ->columns([
                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->searchable(),
                TextColumn::make('precio')
                    ->money('ARS')
                    ->sortable(),
                TextColumn::make('orden')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('orden');
    }
}
