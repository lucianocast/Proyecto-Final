<?php

namespace App\Filament\Admin\Resources\ClienteResource\RelationManagers;

use App\Models\Pedido;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PedidosRelationManager extends RelationManager
{
    protected static string $relationship = 'pedidos';
    protected static ?string $title = 'Historial de Pedidos';
    protected static ?string $recordTitleAttribute = 'id';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // El formulario se gestiona desde PedidoResource
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID Pedido')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha Pedido')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_entrega')
                    ->label('Fecha Entrega')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'confirmado' => 'info',
                        'en_produccion' => 'primary',
                        'listo_para_entrega' => 'success',
                        'entregado' => 'success',
                        'cancelado' => 'danger',
                        'devuelto' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state)))
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Monto Total')
                    ->money('ARS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('saldo_pendiente')
                    ->label('Saldo Pendiente')
                    ->money('ARS')
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('metodo_pago')
                    ->label('Método Pago')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Items')
                    ->badge()
                    ->color('info'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'confirmado' => 'Confirmado',
                        'en_produccion' => 'En Producción',
                        'listo_para_entrega' => 'Listo para Entrega',
                        'entregado' => 'Entregado',
                        'cancelado' => 'Cancelado',
                        'devuelto' => 'Devuelto',
                    ])
                    ->multiple(),
                Filter::make('fecha_pedido')
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
                    }),
            ])
            ->headerActions([
                // No permitir crear pedidos directamente desde aquí
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (Pedido $record): string => route('filament.admin.resources.pedidos.view', ['record' => $record])),
            ])
            ->bulkActions([
                // Sin acciones en masa
            ])
            ->emptyStateHeading('Sin pedidos registrados')
            ->emptyStateDescription('Este cliente aún no ha realizado ningún pedido.');
    }
}
