<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PedidoResource\Pages;
use App\Filament\Admin\Resources\PedidoResource\RelationManagers;
use App\Models\Pedido;
use App\Models\ProductoVariante;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\ExportAction;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PedidoResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Ventas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('cliente_id')
                    ->relationship('cliente', 'nombre', fn ($query) => $query->where('activo', true))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Cliente')
                    ->helperText('Solo se muestran clientes activos'),
                Select::make('status')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'en_produccion' => 'En Producción',
                        'listo' => 'Listo',
                        'entregado' => 'Entregado',
                        'cancelado' => 'Cancelado',
                        'devuelto' => 'Devuelto',
                    ])
                    ->required()
                    ->default('pendiente'),
                DateTimePicker::make('fecha_entrega')
                    ->required()
                    ->label('Fecha y Hora de Entrega'),
                Radio::make('forma_entrega')
                    ->options([
                        'retiro' => 'Retiro en local',
                        'envio' => 'Envío a domicilio',
                    ])
                    ->required()
                    ->default('retiro')
                    ->inline()
                    ->reactive(),
                Textarea::make('direccion_envio')
                    ->label('Dirección de Envío')
                    ->nullable()
                    ->visible(fn (Get $get) => $get('forma_entrega') === 'envio'),
                
                // Sección de productos/items (nuevo Repeater)
                Repeater::make('items')
                    ->relationship('items')
                    ->label('Productos del Pedido')
                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                        // CRÍTICO: Calcular subtotal antes de insertar en DB
                        $cantidad = (float) ($data['cantidad'] ?? 0);
                        $precioUnitario = (float) ($data['precio_unitario'] ?? 0);
                        $data['subtotal'] = $cantidad * $precioUnitario;
                        return $data;
                    })
                    ->schema([
                        Select::make('producto_variante_id')
                            ->label('Producto (Variante)')
                            ->options(function () {
                                return ProductoVariante::with('producto')
                                    ->get()
                                    ->mapWithKeys(function ($variante) {
                                        return [$variante->id => $variante->producto->nombre . ' - ' . $variante->descripcion];
                                    });
                            })
                            ->required()
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                if (!$state) {
                                    return;
                                }
                                $variante = ProductoVariante::find($state);
                                if ($variante) {
                                    $set('precio_unitario', $variante->precio);
                                    // Calcular subtotal con el nuevo precio
                                    $cantidad = (float) ($get('cantidad') ?? 1);
                                    $set('subtotal', $cantidad * $variante->precio);
                                }
                            }),
                        
                        TextInput::make('cantidad')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(1)
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                // Calcular subtotal cuando cambia la cantidad
                                $cantidad = (float) ($get('cantidad') ?? 0);
                                $precioUnitario = (float) ($get('precio_unitario') ?? 0);
                                $set('subtotal', $cantidad * $precioUnitario);
                            }),
                            
                        TextInput::make('precio_unitario')
                            ->numeric()
                            ->required()
                            ->prefix('$')
                            ->label('Precio Unitario')
                            ->readOnly()
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                // Calcular subtotal cuando cambia el precio
                                $cantidad = (float) ($get('cantidad') ?? 0);
                                $precioUnitario = (float) ($get('precio_unitario') ?? 0);
                                $set('subtotal', $cantidad * $precioUnitario);
                            }),
                        
                        // Campo oculto para guardar el subtotal en DB
                        TextInput::make('subtotal')
                            ->numeric()
                            ->hidden()
                            ->default(0)
                            ->dehydrated(true),
                            
                        Placeholder::make('subtotal_placeholder')
                            ->label('Subtotal')
                            ->content(function (Get $get): string {
                                $cantidad = (float) ($get('cantidad') ?? 0);
                                $precioUnitario = (float) ($get('precio_unitario') ?? 0);
                                $subtotal = $cantidad * $precioUnitario;
                                
                                return '$ ' . number_format($subtotal, 2, ',', '.');
                            }),
                    ])
                    ->columns(4)
                    ->defaultItems(1)
                    ->addActionLabel('Agregar Producto')
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        // Calcular el total sumando todos los items
                        $items = $get('items') ?? [];
                        $total = 0;
                        
                        // Solo calcular si hay items con datos válidos
                        foreach ($items as $item) {
                            // Ignorar items vacíos o sin producto seleccionado
                            if (empty($item['producto_variante_id'])) {
                                continue;
                            }
                            
                            $cantidad = (float) ($item['cantidad'] ?? 0);
                            $precioUnitario = (float) ($item['precio_unitario'] ?? 0);
                            $total += $cantidad * $precioUnitario;
                        }
                        
                        $set('total_calculado', $total);
                        
                        // Recalcular saldo pendiente (no puede ser negativo)
                        $montoAbonado = (float) ($get('monto_abonado') ?? 0);
                        $saldo = max(0, $total - $montoAbonado);
                        $set('saldo_pendiente', $saldo);
                    })
                    ->columnSpanFull(),
                
                TextInput::make('total_calculado')
                    ->numeric()
                    ->prefix('$')
                    ->label('Total del Pedido')
                    ->readOnly()
                    ->default(0)
                    ->live(),
                Select::make('metodo_pago')
                    ->options([
                        'total' => 'Pago Total',
                        'seña' => 'Seña',
                    ])
                    ->required()
                    ->default('total')
                    ->reactive(),
                TextInput::make('monto_abonado')
                    ->numeric()
                    ->prefix('$')
                    ->required()
                    ->default(0)
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        $total = (float) ($get('total_calculado') ?? 0);
                        $abonado = (float) ($get('monto_abonado') ?? 0);
                        
                        // Validar que el monto abonado no exceda el total
                        if ($total > 0 && $abonado > $total) {
                            $set('monto_abonado', $total);
                            $abonado = $total;
                        }
                        
                        // El saldo no puede ser negativo
                        $saldo = max(0, $total - $abonado);
                        $set('saldo_pendiente', $saldo);
                    })
                    ->helperText('El monto abonado no puede exceder el total del pedido'),
                TextInput::make('saldo_pendiente')
                    ->numeric()
                    ->prefix('$')
                    ->label('Saldo Pendiente')
                    ->readOnly()
                    ->default(0),
                Textarea::make('observaciones')
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID Pedido')
                    ->sortable(),
                TextColumn::make('cliente.nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'en_produccion' => 'primary',
                        'listo' => 'info',
                        'entregado' => 'success',
                        'cancelado' => 'danger',
                        'devuelto' => 'gray',
                        default => 'secondary',
                    }),
                TextColumn::make('fecha_entrega')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('total_calculado')
                    ->money('ARS')
                    ->sortable(),
                TextColumn::make('saldo_pendiente')
                    ->money('ARS')
                    ->label('Saldo Pendiente')
                    ->sortable(),
                TextColumn::make('vendedor.name')
                    ->label('Vendedor')
                    ->sortable(),
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
                SelectFilter::make('status')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'en_produccion' => 'En Producción',
                        'listo' => 'Listo',
                        'entregado' => 'Entregado',
                        'cancelado' => 'Cancelado',
                        'devuelto' => 'Devuelto',
                    ])
                    ->multiple()
                    ->label('Estado'),

                SelectFilter::make('forma_entrega')
                    ->options([
                        'retiro' => 'Retiro en local',
                        'envio' => 'Envío a domicilio',
                    ])
                    ->label('Forma de Entrega'),

                SelectFilter::make('metodo_pago')
                    ->options([
                        'total' => 'Pago Total',
                        'seña' => 'Seña',
                    ])
                    ->label('Método de Pago'),

                Filter::make('fecha_entrega')
                    ->form([
                        DatePicker::make('fecha_desde')
                            ->label('Desde')
                            ->placeholder('Seleccione fecha'),
                        DatePicker::make('fecha_hasta')
                            ->label('Hasta')
                            ->placeholder('Seleccione fecha'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['fecha_desde'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_entrega', '>=', $date),
                            )
                            ->when(
                                $data['fecha_hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_entrega', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['fecha_desde'] ?? null) {
                            $indicators[] = 'Desde: ' . \Carbon\Carbon::parse($data['fecha_desde'])->format('d/m/Y');
                        }
                        if ($data['fecha_hasta'] ?? null) {
                            $indicators[] = 'Hasta: ' . \Carbon\Carbon::parse($data['fecha_hasta'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),

                Filter::make('monto')
                    ->form([
                        TextInput::make('monto_minimo')
                            ->label('Monto Mínimo')
                            ->numeric()
                            ->prefix('$'),
                        TextInput::make('monto_maximo')
                            ->label('Monto Máximo')
                            ->numeric()
                            ->prefix('$'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['monto_minimo'],
                                fn (Builder $query, $monto): Builder => $query->where('total_calculado', '>=', $monto),
                            )
                            ->when(
                                $data['monto_maximo'],
                                fn (Builder $query, $monto): Builder => $query->where('total_calculado', '<=', $monto),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['monto_minimo'] ?? null) {
                            $indicators[] = 'Monto Min: $' . number_format($data['monto_minimo'], 2);
                        }
                        if ($data['monto_maximo'] ?? null) {
                            $indicators[] = 'Monto Max: $' . number_format($data['monto_maximo'], 2);
                        }
                        return $indicators;
                    }),

                Filter::make('con_saldo')
                    ->label('Con Saldo Pendiente')
                    ->query(fn (Builder $query): Builder => $query->where('saldo_pendiente', '>', 0))
                    ->toggle(),

                SelectFilter::make('cliente')
                    ->relationship('cliente', 'nombre')
                    ->searchable()
                    ->preload()
                    ->label('Cliente'),

                SelectFilter::make('vendedor')
                    ->relationship('vendedor', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Vendedor'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                // Acción: Generar PDF
                Tables\Actions\Action::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->url(fn (Pedido $record): string => route('pedido.pdf', $record))
                    ->openUrlInNewTab(),
                
                // UC-13: Acción Registrar Devolución/Reintegro
                Tables\Actions\Action::make('devolver')
                    ->label('Registrar Devolución')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Registrar Devolución/Reintegro')
                    ->modalDescription('⚠️ Esta acción anulará la venta, los pagos asociados y registrará el reverso financiero.')
                    ->modalIcon('heroicon-o-exclamation-circle')
                    ->form([
                        Select::make('tipo_devolucion')
                            ->label('Tipo de Devolución')
                            ->options([
                                'total' => 'Devolución Total',
                                'parcial' => 'Devolución Parcial',
                            ])
                            ->required()
                            ->default('total'),
                        TextInput::make('monto_reintegro')
                            ->label('Monto a Reintegrar')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->default(fn (Pedido $record) => $record->monto_abonado),
                        Textarea::make('motivo_devolucion')
                            ->label('Motivo de la Devolución (Obligatorio)')
                            ->required()
                            ->placeholder('Ej: Producto dañado, error en el pedido, insatisfacción del cliente, etc.')
                            ->rows(4),
                        Select::make('reingresar_stock')
                            ->label('¿Reingresar Productos a Stock?')
                            ->options([
                                'si' => 'Sí (Producto en buen estado)',
                                'no' => 'No (Producto desechado/dañado)',
                            ])
                            ->required()
                            ->default('no'),
                    ])
                    ->action(function (Pedido $record, array $data): void {
                        // Validar que la venta/pedido esté entregado
                        if ($record->status !== 'entregado') {
                            \Filament\Notifications\Notification::make()
                                ->title('Error')
                                ->body('Solo se pueden devolver pedidos en estado "Entregado".')
                                ->danger()
                                ->send();
                            return;
                        }

                        try {
                            \Illuminate\Support\Facades\DB::transaction(function () use ($record, $data) {
                                $montoReintegro = $data['monto_reintegro'];
                                
                                // Paso 1: Anular todos los pagos del pedido (UC-11)
                                foreach ($record->pagos as $pago) {
                                    if ($pago->estado !== 'anulado') {
                                        $pago->estado = 'anulado';
                                        $pago->save();
                                        
                                        $pago->auditAction(
                                            action: 'cancelled_by_return',
                                            justification: 'Anulado por devolución: ' . $data['motivo_devolucion'],
                                            data: ['pedido_id' => $record->id]
                                        );
                                    }
                                }

                                // Paso 2: Actualizar estado del pedido a "Devuelto"
                                $oldStatus = $record->status;
                                $record->status = 'devuelto';
                                $record->monto_abonado = 0;
                                $record->saldo_pendiente = 0;
                                $record->observaciones = ($record->observaciones ?? '') . 
                                    "\n\n[DEVOLUCIÓN] " . now()->format('d/m/Y H:i') . 
                                    ": " . $data['motivo_devolucion'];
                                $record->save();

                                // Paso 3: Registrar en auditoría (UC-13 crítico)
                                $record->auditAction(
                                    action: 'returned',
                                    justification: $data['motivo_devolucion'],
                                    data: [
                                        'tipo_devolucion' => $data['tipo_devolucion'],
                                        'monto_reintegro' => $montoReintegro,
                                        'reingresar_stock' => $data['reingresar_stock'],
                                        'old_status' => $oldStatus,
                                        'pagos_anulados' => $record->pagos->pluck('id')->toArray(),
                                    ]
                                );
                                
                                // TODO: Si reingresar_stock = 'si', implementar lógica de reingreso
                                // (requiere gestión de producción inversa)
                            });

                            \Filament\Notifications\Notification::make()
                                ->title('✅ Devolución Registrada')
                                ->body('La devolución ha sido procesada. Los pagos fueron anulados y el pedido marcado como devuelto.')
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Error al procesar devolución')
                                ->body('Ocurrió un error: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (Pedido $record): bool => $record->status === 'entregado'),

                // Acción: Cancelar Pedido
                Tables\Actions\Action::make('cancelar')
                    ->label('Cancelar Pedido')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Cancelar Pedido')
                    ->modalDescription('Esta acción cancelará el pedido. Debe proporcionar una justificación.')
                    ->modalIcon('heroicon-o-exclamation-triangle')
                    ->form([
                        Textarea::make('justification')
                            ->label('Justificación')
                            ->required()
                            ->placeholder('Ej: Cliente solicitó cancelación, producto no disponible, etc.')
                            ->rows(3),
                    ])
                    ->action(function (Pedido $record, array $data): void {
                        // Validar que el pedido puede ser cancelado
                        if (in_array($record->status, ['entregado', 'cancelado', 'devuelto'])) {
                            \Filament\Notifications\Notification::make()
                                ->title('Error')
                                ->body('No se puede cancelar un pedido que ya está entregado o cancelado.')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Cambiar estado y registrar en auditoría
                        $oldStatus = $record->status;
                        $record->status = 'cancelado';
                        $record->save();

                        // Registrar en auditoría con justificación
                        $record->auditAction(
                            action: 'cancelled',
                            justification: $data['justification'],
                            data: [
                                'old_status' => $oldStatus,
                                'new_status' => 'cancelado',
                            ]
                        );

                        \Filament\Notifications\Notification::make()
                            ->title('Pedido Cancelado')
                            ->body('El pedido ha sido cancelado exitosamente.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Pedido $record): bool => 
                        !in_array($record->status, ['entregado', 'cancelado'])
                    ),

                // Acción: Registrar Venta/Entrega
                Tables\Actions\Action::make('entregar')
                    ->label('Registrar Entrega')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar Entrega del Pedido')
                    ->modalDescription('Esta acción marcará el pedido como entregado y cerrará la venta.')
                    ->modalIcon('heroicon-o-shopping-bag')
                    ->form([
                        Select::make('metodo_pago_final')
                            ->label('Método de Pago Final')
                            ->options([
                                'efectivo' => 'Efectivo',
                                'tarjeta' => 'Tarjeta de Crédito/Débito',
                                'transferencia' => 'Transferencia Bancaria',
                                'mercado_pago' => 'Mercado Pago',
                            ])
                            ->required(),
                        TextInput::make('monto_final')
                            ->label('Monto Final Cobrado')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->default(fn (Pedido $record) => $record->saldo_pendiente),
                        Textarea::make('observaciones_entrega')
                            ->label('Observaciones de Entrega')
                            ->placeholder('Opcional: notas sobre la entrega')
                            ->rows(2),
                    ])
                    ->action(function (Pedido $record, array $data): void {
                        // Validar que el pedido está listo para entregar
                        if ($record->status !== 'listo') {
                            \Filament\Notifications\Notification::make()
                                ->title('Error')
                                ->body('Solo se pueden entregar pedidos en estado "Listo".')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Registrar el pago final si hay saldo pendiente
                        if ($record->saldo_pendiente > 0) {
                            \App\Models\Pago::create([
                                'pedido_id' => $record->id,
                                'monto' => $data['monto_final'],
                                'metodo' => $data['metodo_pago_final'],
                                'estado' => 'confirmado',
                                'fecha_pago' => now(),
                            ]);

                            // Actualizar monto abonado
                            $record->monto_abonado += $data['monto_final'];
                            $record->saldo_pendiente = $record->total_calculado - $record->monto_abonado;
                        }

                        // Cambiar estado a entregado
                        $oldStatus = $record->status;
                        $record->status = 'entregado';
                        if (!empty($data['observaciones_entrega'])) {
                            $record->observaciones = ($record->observaciones ?? '') . "\n\nEntrega: " . $data['observaciones_entrega'];
                        }
                        $record->save();

                        // Registrar en auditoría
                        $record->auditAction(
                            action: 'delivered',
                            data: [
                                'old_status' => $oldStatus,
                                'new_status' => 'entregado',
                                'metodo_pago_final' => $data['metodo_pago_final'],
                                'monto_final' => $data['monto_final'],
                            ]
                        );

                        \Filament\Notifications\Notification::make()
                            ->title('Pedido Entregado')
                            ->body('El pedido ha sido marcado como entregado exitosamente.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Pedido $record): bool => 
                        $record->status === 'listo'
                    ),
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
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPedidos::route('/'),
            'create' => Pages\CreatePedido::route('/create'),
            'edit' => Pages\EditPedido::route('/{record}/edit'),
        ];
    }
}
