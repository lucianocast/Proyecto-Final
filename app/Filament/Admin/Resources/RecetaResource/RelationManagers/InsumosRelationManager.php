<?php

namespace App\Filament\Admin\Resources\RecetaResource\RelationManagers;

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

class InsumosRelationManager extends RelationManager
{
    protected static string $relationship = 'insumos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // No se necesita formulario para crear insumos desde aquí
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
            ->columns([
                TextColumn::make('nombre')
                    ->searchable()
                    ->label('Insumo'),
                TextColumn::make('unidad_medida')
                    ->label('Unidad'),
                TextInputColumn::make('cantidad')
                    ->rules(['required', 'numeric', 'min:0'])
                    ->label('Cantidad'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        TextInput::make('cantidad')
                            ->required()
                            ->numeric()
                            ->label('Cantidad'),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
