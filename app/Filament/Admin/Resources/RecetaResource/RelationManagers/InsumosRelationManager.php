<?php

namespace App\Filament\Admin\Resources\RecetaResource\RelationManagers;

use App\Enums\UnidadMedida;
use App\Models\Insumo;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\EditAction;
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
                    ->label('Insumo')
                    ->sortable(),
                TextColumn::make('unidad_de_medida')
                    ->label('Unidad')
                    ->badge()
                    ->color('success'),
                TextInputColumn::make('cantidad')
                    ->rules(['required', 'numeric', 'min:0'])
                    ->label('Cantidad Necesaria'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(fn ($query) => $query->orderBy('nombre'))
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->live()
                            ->afterStateUpdated(function (Set $set, $state) {
                                if ($state) {
                                    $insumo = Insumo::find($state);
                                    $set('unidad_display', $insumo?->unidad_de_medida?->value ?? '');
                                }
                            }),
                        
                        TextInput::make('cantidad')
                            ->required()
                            ->numeric()
                            ->minValue(0.01)
                            ->label('Cantidad')
                            ->suffix(function (Get $get) {
                                $recordId = $get('recordId');
                                if ($recordId) {
                                    $insumo = Insumo::find($recordId);
                                    return $insumo?->unidad_de_medida?->value ?? '';
                                }
                                return '';
                            })
                            ->helperText('Ingrese la cantidad en la unidad base del insumo'),
                    ]),
            ])
            ->actions([
                EditAction::make()
                    ->fillForm(function ($record): array {
                        $insumo = Insumo::find($record->id);
                        return [
                            'insumo_nombre' => $insumo?->nombre ?? '',
                            'unidad' => $insumo?->unidad_de_medida?->value ?? '',
                            'cantidad' => $record->pivot->cantidad,
                        ];
                    })
                    ->form([
                        TextInput::make('insumo_nombre')
                            ->label('Insumo')
                            ->disabled()
                            ->dehydrated(false),
                        
                        TextInput::make('cantidad')
                            ->required()
                            ->numeric()
                            ->minValue(0.01)
                            ->label('Cantidad')
                            ->suffix(function (Get $get) {
                                return $get('unidad') ?? '';
                            })
                            ->helperText('Ingrese la cantidad en la unidad base del insumo'),
                        
                        Hidden::make('unidad'),
                    ])
                    ->using(function ($record, array $data): void {
                        $record->pivot->cantidad = $data['cantidad'];
                        $record->pivot->save();
                    }),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
