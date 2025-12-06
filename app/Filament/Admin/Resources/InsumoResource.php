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
                    ->state(fn ($record) => $record->stock_total)
                    ->color(fn ($record) => match (true) {
                        $record->stock_total <= $record->stock_minimo => 'danger',
                        $record->stock_total <= ($record->stock_minimo * 1.5) => 'warning',
                        default => 'success',
                    })
                    ->weight('bold'),
                TextColumn::make('stock_minimo')
                    ->numeric()
                    ->label('Stock Mínimo'),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->state(fn ($record) => match (true) {
                        $record->stock_total <= $record->stock_minimo => 'Crítico',
                        $record->stock_total <= ($record->stock_minimo * 1.5) => 'Bajo',
                        default => 'Normal',
                    })
                    ->color(fn ($record) => match (true) {
                        $record->stock_total <= $record->stock_minimo => 'danger',
                        $record->stock_total <= ($record->stock_minimo * 1.5) => 'warning',
                        default => 'success',
                    }),
                TextColumn::make('ubicacion')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categoria_insumo_id')
                    ->relationship('categoria', 'nombre')
                    ->label('Categoría')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('ubicacion')
                    ->form([
                        Forms\Components\TextInput::make('ubicacion')
                            ->label('Ubicación'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['ubicacion'],
                                fn (Builder $query, $ubicacion): Builder => $query->where('ubicacion', 'like', "%{$ubicacion}%"),
                            );
                    }),
                Tables\Filters\Filter::make('stock_critico')
                    ->label('Stock Crítico')
                    ->query(fn (Builder $query): Builder => $query->whereRaw('(SELECT SUM(cantidad_actual) FROM lotes WHERE lotes.insumo_id = insumos.id) <= stock_minimo'))
                    ->toggle(),
                Tables\Filters\Filter::make('stock_bajo')
                    ->label('Stock Bajo')
                    ->query(fn (Builder $query): Builder => $query->whereRaw('(SELECT SUM(cantidad_actual) FROM lotes WHERE lotes.insumo_id = insumos.id) <= stock_minimo * 1.5'))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ExportBulkAction::make()
                        ->exporter(\App\Filament\Exports\InsumoExporter::class),
                ]),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->exporter(\App\Filament\Exports\InsumoExporter::class),
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
