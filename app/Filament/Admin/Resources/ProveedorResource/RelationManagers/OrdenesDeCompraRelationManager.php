<?php

namespace App\Filament\Admin\Resources\ProveedorResource\RelationManagers;

use App\Filament\Admin\Resources\OrdenDeCompraResource;
use App\Models\OrdenDeCompra;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdenesDeCompraRelationManager extends RelationManager
{
    protected static string $relationship = 'ordenesDeCompra';

    protected static ?string $title = 'Historial de Órdenes de Compra';

    protected static ?string $recordTitleAttribute = 'id';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID OC')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_emision')
                    ->label('Fecha Emisión')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_entrega_esperada')
                    ->label('Fecha Entrega Esperada')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state)))
                    ->colors([
                        'warning' => 'Pendiente',
                        'success' => 'Recibida',
                        'danger' => 'Cancelada',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_calculado')
                    ->label('Monto Total')
                    ->money('ARS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Items')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Creado por')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'Recibida' => 'Recibida',
                        'Cancelada' => 'Cancelada',
                    ])
                    ->multiple(),
                Filter::make('fecha_emision')
                    ->form([
                        Forms\Components\DatePicker::make('emision_from')
                            ->label('Emisión desde'),
                        Forms\Components\DatePicker::make('emision_until')
                            ->label('Emisión hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['emision_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_emision', '>=', $date),
                            )
                            ->when(
                                $data['emision_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_emision', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->url(fn (OrdenDeCompra $record): string => OrdenDeCompraResource::getUrl('view', ['record' => $record->id])),
            ])
            ->defaultSort('fecha_emision', 'desc')
            ->emptyStateHeading('Sin órdenes de compra registradas')
            ->emptyStateDescription('Este proveedor aún no tiene órdenes de compra asociadas');
    }
}

