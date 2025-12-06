<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PagoResource\Pages;
use App\Filament\Admin\Resources\PagoResource\RelationManagers;
use App\Models\Pago;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PagoResource extends Resource
{
    protected static ?string $model = Pago::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Ventas';
    protected static ?string $navigationLabel = 'Pagos';
    protected static ?string $pluralModelLabel = 'Pagos';
    protected static ?string $modelLabel = 'Pago';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('pedido_id')
                    ->relationship('pedido', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => 
                        "Pedido #{$record->id} - {$record->cliente->nombre}"
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Pedido'),
                Forms\Components\TextInput::make('monto')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0.01)
                    ->label('Monto'),
                Forms\Components\Select::make('metodo')
                    ->required()
                    ->options([
                        'efectivo' => 'Efectivo',
                        'tarjeta' => 'Tarjeta de Crédito/Débito',
                        'transferencia' => 'Transferencia Bancaria',
                        'mercado_pago' => 'Mercado Pago',
                    ])
                    ->label('Método de Pago'),
                Forms\Components\Select::make('estado')
                    ->required()
                    ->options([
                        'pendiente' => 'Pendiente',
                        'confirmado' => 'Confirmado',
                        'anulado' => 'Anulado',
                    ])
                    ->default('confirmado')
                    ->label('Estado'),
                Forms\Components\TextInput::make('referencia_externa')
                    ->maxLength(255)
                    ->label('Referencia Externa')
                    ->placeholder('ID de transacción externa (ej: Mercado Pago)'),
                Forms\Components\DateTimePicker::make('fecha_pago')
                    ->default(now())
                    ->required()
                    ->label('Fecha de Pago'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pedido.id')
                    ->label('Pedido')
                    ->formatStateUsing(fn ($state) => "Pedido #{$state}")
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('pedido.cliente.nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('monto')
                    ->money('ARS')
                    ->sortable()
                    ->label('Monto'),
                Tables\Columns\TextColumn::make('metodo')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'efectivo' => 'success',
                        'tarjeta' => 'info',
                        'transferencia' => 'warning',
                        'mercado_pago' => 'primary',
                        default => 'gray',
                    })
                    ->label('Método'),
                Tables\Columns\TextColumn::make('estado')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'confirmado' => 'success',
                        'pendiente' => 'warning',
                        'anulado' => 'danger',
                        default => 'gray',
                    })
                    ->label('Estado'),
                Tables\Columns\TextColumn::make('referencia_externa')
                    ->searchable()
                    ->label('Ref. Externa')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('fecha_pago')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Fecha de Pago'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'confirmado' => 'Confirmado',
                        'anulado' => 'Anulado',
                    ])
                    ->label('Estado'),
                Tables\Filters\SelectFilter::make('metodo')
                    ->options([
                        'efectivo' => 'Efectivo',
                        'tarjeta' => 'Tarjeta',
                        'transferencia' => 'Transferencia',
                        'mercado_pago' => 'Mercado Pago',
                    ])
                    ->label('Método de Pago'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                // UC-11: Acción Anular Pago con justificación y reversión
                Tables\Actions\Action::make('anular')
                    ->label('Anular Pago')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Anular Pago')
                    ->modalDescription('⚠️ Esta acción anulará el pago y revertirá el saldo del pedido. Debe proporcionar una justificación obligatoria.')
                    ->modalIcon('heroicon-o-exclamation-triangle')
                    ->form([
                        Forms\Components\Textarea::make('justification')
                            ->label('Justificación Obligatoria')
                            ->required()
                            ->placeholder('Ej: Error de transferencia, devolución al cliente, pago duplicado, etc.')
                            ->rows(4)
                            ->helperText('La justificación será registrada en el log de auditoría.'),
                    ])
                    ->action(function (Pago $record, array $data): void {
                        // Validación 1: Verificar que no esté ya anulado
                        if ($record->estado === 'anulado') {
                            \Filament\Notifications\Notification::make()
                                ->title('Error')
                                ->body('Este pago ya está anulado.')
                                ->danger()
                                ->send();
                            return;
                        }

                        try {
                            \Illuminate\Support\Facades\DB::transaction(function () use ($record, $data) {
                                $oldEstado = $record->estado;
                                $pedido = $record->pedido;
                                $montoAnulado = $record->monto;

                                // Paso 1: Anular el registro de Pago
                                $record->estado = 'anulado';
                                $record->save();

                                // Paso 2: Revertir el saldo del pedido
                                $pedido->monto_abonado -= $montoAnulado;
                                $pedido->saldo_pendiente = $pedido->total_calculado - $pedido->monto_abonado;
                                
                                // Si el pago era el saldo final, revertir estado del pedido
                                if ($pedido->status === 'entregado' && $pedido->saldo_pendiente > 0) {
                                    $pedido->status = 'listo';
                                }
                                
                                $pedido->save();

                                // Paso 3: Registrar en auditoría (UC-11 crítico)
                                $record->auditAction(
                                    action: 'cancelled',
                                    justification: $data['justification'],
                                    data: [
                                        'old_estado' => $oldEstado,
                                        'new_estado' => 'anulado',
                                        'monto_anulado' => $montoAnulado,
                                        'pedido_id' => $pedido->id,
                                        'nuevo_saldo_pendiente' => $pedido->saldo_pendiente,
                                        'nuevo_monto_abonado' => $pedido->monto_abonado,
                                        'pedido_status_revertido' => $pedido->status,
                                    ]
                                );
                            });

                            \Filament\Notifications\Notification::make()
                                ->title('✅ Pago Anulado')
                                ->body('El pago ha sido anulado exitosamente. El saldo del pedido ha sido revertido.')
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Error al anular pago')
                                ->body('Ocurrió un error: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (Pago $record): bool => $record->estado !== 'anulado'),
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
            'index' => Pages\ListPagos::route('/'),
            'create' => Pages\CreatePago::route('/create'),
            'edit' => Pages\EditPago::route('/{record}/edit'),
        ];
    }
}
