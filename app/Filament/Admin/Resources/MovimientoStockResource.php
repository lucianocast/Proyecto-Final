<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MovimientoStockResource\Pages;
use App\Filament\Admin\Resources\MovimientoStockResource\RelationManagers;
use App\Models\MovimientoStock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MovimientoStockResource extends Resource
{
    protected static ?string $model = MovimientoStock::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = 'Stock e Insumos';
    protected static ?string $navigationLabel = 'Movimientos de Stock';
    protected static ?string $modelLabel = 'Movimiento de Stock';
    protected static ?string $pluralModelLabel = 'Movimientos de Stock';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('insumo_id')
                    ->relationship('insumo', 'nombre')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Insumo')
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $insumo = \App\Models\Insumo::find($state);
                            $set('stock_actual_display', $insumo ? $insumo->stock_total : 0);
                        }
                    }),
                Forms\Components\TextInput::make('stock_actual_display')
                    ->label('Stock Actual')
                    ->disabled()
                    ->dehydrated(false)
                    ->suffix('unidades'),
                Forms\Components\Radio::make('tipo')
                    ->required()
                    ->options([
                        'entrada' => 'Entrada (+)',
                        'salida' => 'Salida (-)',
                        'ajuste' => 'Ajuste (±)',
                    ])
                    ->inline()
                    ->label('Tipo de Movimiento'),
                Forms\Components\TextInput::make('cantidad')
                    ->required()
                    ->numeric()
                    ->minValue(0.01)
                    ->step(0.01)
                    ->label('Cantidad')
                    ->helperText('Para ajustes negativos, usar tipo Salida o Ajuste'),
                Forms\Components\Textarea::make('justificacion')
                    ->required()
                    ->minLength(10)
                    ->maxLength(500)
                    ->rows(3)
                    ->label('Justificación')
                    ->helperText('Mínimo 10 caracteres. Describa el motivo del movimiento.')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Fecha')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'entrada' => 'success',
                        'salida' => 'danger',
                        'ajuste' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'entrada' => 'Entrada',
                        'salida' => 'Salida',
                        'ajuste' => 'Ajuste',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('insumo.nombre')
                    ->searchable()
                    ->sortable()
                    ->label('Insumo'),
                Tables\Columns\TextColumn::make('cantidad')
                    ->numeric(decimalPlaces: 2)
                    ->formatStateUsing(fn ($record) => match ($record->tipo) {
                        'entrada' => '+' . number_format($record->cantidad, 2),
                        'salida' => '-' . number_format($record->cantidad, 2),
                        'ajuste' => number_format($record->cantidad, 2),
                        default => number_format($record->cantidad, 2),
                    })
                    ->color(fn ($record) => match ($record->tipo) {
                        'entrada' => 'success',
                        'salida' => 'danger',
                        default => 'warning',
                    })
                    ->sortable()
                    ->label('Cantidad'),
                Tables\Columns\TextColumn::make('usuario.name')
                    ->searchable()
                    ->sortable()
                    ->label('Usuario')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('referencia')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($record) => $record->referencia ? ($record->tipo_referencia ? ucfirst($record->tipo_referencia) . ' #' . $record->referencia : $record->referencia) : '-')
                    ->label('Referencia')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('justificacion')
                    ->limit(30)
                    ->tooltip(fn ($record): string => $record->justificacion ?? '')
                    ->searchable()
                    ->label('Justificación')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('cantidad_anterior')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->label('Stock Anterior')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('cantidad_nueva')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->label('Stock Nuevo')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('tipo')
                    ->options([
                        'entrada' => 'Entrada',
                        'salida' => 'Salida',
                        'ajuste' => 'Ajuste',
                    ])
                    ->label('Tipo de Movimiento'),
                SelectFilter::make('insumo_id')
                    ->relationship('insumo', 'nombre')
                    ->searchable()
                    ->preload()
                    ->label('Insumo'),
                SelectFilter::make('user_id')
                    ->relationship('usuario', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Usuario'),
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators[] = 'Desde: ' . \Carbon\Carbon::parse($data['created_from'])->format('d/m/Y');
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators[] = 'Hasta: ' . \Carbon\Carbon::parse($data['created_until'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),
                Filter::make('referencia')
                    ->form([
                        Forms\Components\TextInput::make('referencia')
                            ->label('Nº Referencia'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['referencia'],
                                fn (Builder $query, $ref): Builder => $query->where('referencia', 'like', "%{$ref}%"),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ExportBulkAction::make()
                        ->exporter(\App\Filament\Exports\MovimientoStockExporter::class),
                ]),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->exporter(\App\Filament\Exports\MovimientoStockExporter::class),
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
            'index' => Pages\ListMovimientoStocks::route('/'),
            'create' => Pages\CreateMovimientoStock::route('/create'),
            'edit' => Pages\EditMovimientoStock::route('/{record}/edit'),
        ];
    }
}
