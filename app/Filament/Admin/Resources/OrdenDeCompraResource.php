<?php

namespace App\Filament\Admin\Resources;

use App\Enums\UnidadMedida;
use App\Filament\Admin\Resources\OrdenDeCompraResource\Pages;
use App\Filament\Admin\Resources\OrdenDeCompraResource\RelationManagers;
use App\Helpers\ConversionHelper;
use App\Models\OrdenDeCompra;
use App\Models\Insumo;
use App\Models\Lote;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrdenDeCompraResource extends Resource
{
    protected static ?string $model = OrdenDeCompra::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Compras y Proveedores';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información de la Orden')
                    ->schema([
                        Select::make('proveedor_id')
                            ->relationship('proveedor', 'nombre_empresa')
                            ->required()
                            ->searchable(['nombre_empresa', 'nombre_contacto'])
                            ->preload()
                            ->live()
                            ->label('Proveedor')
                            ->afterStateUpdated(fn (Set $set) => $set('items', []))
                            ->disabled(fn (?Model $record) => $record && in_array($record->status, ['recibida_parcial', 'recibida_total', 'cancelada'])),
                        
                        Select::make('status')
                            ->required()
                            ->options([
                                'pendiente' => 'Pendiente',
                                'aprobada' => 'Aprobada',
                                'rechazada' => 'Rechazada',
                                'recibida_parcial' => 'Recibida Parcial',
                                'recibida_total' => 'Recibida Total',
                                'cancelada' => 'Cancelada',
                            ])
                            ->default('pendiente')
                            ->label('Estado')
                            ->disabled(fn (?Model $record) => $record && in_array($record->status, ['recibida_parcial', 'recibida_total', 'cancelada'])),
                        
                        DatePicker::make('fecha_emision')
                            ->required()
                            ->label('Fecha')
                            ->default(now())
                            ->disabled(fn (?Model $record) => $record && in_array($record->status, ['recibida_parcial', 'recibida_total', 'cancelada'])),
                    ])
                    ->columns(3),

                Section::make('Items de la Orden')
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Select::make('insumo_id')
                                    ->label('Insumo')
                                    ->options(function (Get $get) {
                                        $proveedorId = $get('../../proveedor_id');
                                        
                                        if (!$proveedorId) {
                                            return [];
                                        }

                                        return Insumo::whereHas('proveedores', function ($query) use ($proveedorId) {
                                            $query->where('proveedor_id', $proveedorId);
                                        })
                                        ->activos()
                                        ->pluck('nombre', 'id');
                                    })
                                    ->required()
                                    ->searchable()
                                    ->live()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                        $proveedorId = $get('../../proveedor_id');
                                        
                                        if (!$state || !$proveedorId) {
                                            $set('unidad_compra_display', null);
                                            return;
                                        }

                                        $insumo = Insumo::find($state);
                                        $proveedorData = $insumo->proveedores()
                                            ->where('proveedor_id', $proveedorId)
                                            ->first();

                                        $precio = $proveedorData->pivot->precio ?? 0;
                                        $unidadCompra = $proveedorData->pivot->unidad_compra ?? 'Unidad';
                                        
                                        $set('precio_unitario', $precio);
                                        $set('unidad_compra_display', $unidadCompra);
                                        
                                        $cantidad = $get('cantidad') ?? 1;
                                        $set('subtotal', $cantidad * $precio);
                                    }),

                                TextInput::make('cantidad')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(0.01)
                                    ->live(onBlur: true)
                                    ->helperText(function (Get $get) {
                                        $unidad = $get('unidad_compra_display');
                                        return $unidad ? "Unidad: {$unidad}" : null;
                                    })
                                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                        $precio = $get('precio_unitario') ?? 0;
                                        $subtotal = $state * $precio;
                                        $set('subtotal', $subtotal);
                                        
                                        // Recalcular total general
                                        self::recalcularTotal($set, $get);
                                    }),

                                TextInput::make('unidad_compra_display')
                                    ->label('Unidad')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->placeholder('Seleccione insumo'),

                                TextInput::make('precio_unitario')
                                    ->label('Precio Unit.')
                                    ->numeric()
                                    ->required()
                                    ->prefix('$')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                        $cantidad = $get('cantidad') ?? 1;
                                        $subtotal = $cantidad * $state;
                                        $set('subtotal', $subtotal);
                                        
                                        // Recalcular total general
                                        self::recalcularTotal($set, $get);
                                    }),

                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->prefix('$')
                                    ->readOnly()
                                    ->default(0)
                                    ->dehydrated(),
                            ])
                            ->columns(5)
                            ->defaultItems(0)
                            ->addActionLabel('+ Agregar Insumo')
                            ->reorderable(false)
                            ->collapsible()
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                self::recalcularTotal($set, $get);
                            })
                            ->disabled(fn (?Model $record) => $record && in_array($record->status, ['recibida_parcial', 'recibida_total', 'cancelada'])),
                    ])
                    ->description('Agregue los insumos a solicitar al proveedor seleccionado'),

                Section::make('Totales')
                    ->schema([
                        Placeholder::make('total_calculado_display')
                            ->label('Total General')
                            ->content(function (Get $get) {
                                $items = $get('items') ?? [];
                                $total = collect($items)->sum('subtotal');
                                return '$' . number_format($total, 2);
                            }),
                        
                        TextInput::make('total_calculado')
                            ->label('Total')
                            ->numeric()
                            ->readOnly()
                            ->prefix('$')
                            ->default(0)
                            ->dehydrated()
                            ->hidden(),
                    ])
                    ->columns(1),
            ])
            ->disabled(fn (?Model $record) => $record && in_array($record->status, ['recibida_parcial', 'recibida_total', 'cancelada']));
    }

    protected static function recalcularTotal(Set $set, Get $get): void
    {
        $items = $get('items') ?? [];
        $total = collect($items)->sum('subtotal');
        $set('total_calculado', $total);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('proveedor.nombre_empresa')
                    ->searchable(['nombre_empresa', 'nombre_contacto'])
                    ->sortable()
                    ->label('Proveedor')
                    ->wrap(),
                
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'gray',
                        'aprobada' => 'info',
                        'rechazada' => 'danger',
                        'recibida_parcial' => 'warning',
                        'recibida_total' => 'success',
                        'cancelada' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pendiente' => 'Pendiente',
                        'aprobada' => 'Aprobada',
                        'rechazada' => 'Rechazada',
                        'recibida_parcial' => 'Recibida Parcial',
                        'recibida_total' => 'Recibida Total',
                        'cancelada' => 'Cancelada',
                        default => ucfirst($state),
                    }),
                
                TextColumn::make('fecha_emision')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->alignCenter()
                    ->badge()
                    ->color('primary'),
                
                TextColumn::make('total_calculado')
                    ->label('Total')
                    ->money('ARS')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('ARS')
                            ->label('Total General'),
                    ]),
                
                TextColumn::make('user.name')
                    ->label('Creada por')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'aprobada' => 'Aprobada',
                        'rechazada' => 'Rechazada',
                        'recibida_parcial' => 'Recibida Parcial',
                        'recibida_total' => 'Recibida Total',
                        'cancelada' => 'Cancelada',
                    ])
                    ->multiple(),
                
                Tables\Filters\SelectFilter::make('proveedor')
                    ->relationship('proveedor', 'nombre_empresa')
                    ->searchable()
                    ->preload()
                    ->label('Proveedor'),
                
                Tables\Filters\Filter::make('fecha_emision')
                    ->form([
                        DatePicker::make('desde')->label('Desde'),
                        DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_emision', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_emision', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['desde'] ?? null) {
                            $indicators[] = 'Desde: ' . \Carbon\Carbon::parse($data['desde'])->format('d/m/Y');
                        }
                        if ($data['hasta'] ?? null) {
                            $indicators[] = 'Hasta: ' . \Carbon\Carbon::parse($data['hasta'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),

                Tables\Filters\Filter::make('fecha_entrega_esperada')
                    ->label('Fecha Entrega Esperada')
                    ->form([
                        DatePicker::make('desde')->label('Desde'),
                        DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_entrega_esperada', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_entrega_esperada', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['desde'] ?? null) {
                            $indicators[] = 'Entrega desde: ' . \Carbon\Carbon::parse($data['desde'])->format('d/m/Y');
                        }
                        if ($data['hasta'] ?? null) {
                            $indicators[] = 'Entrega hasta: ' . \Carbon\Carbon::parse($data['hasta'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),

                Tables\Filters\Filter::make('monto_total')
                    ->label('Monto Total')
                    ->form([
                        TextInput::make('minimo')
                            ->label('Monto Mínimo')
                            ->numeric()
                            ->prefix('$'),
                        TextInput::make('maximo')
                            ->label('Monto Máximo')
                            ->numeric()
                            ->prefix('$'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['minimo'],
                                fn (Builder $query, $monto): Builder => $query->where('total_calculado', '>=', $monto),
                            )
                            ->when(
                                $data['maximo'],
                                fn (Builder $query, $monto): Builder => $query->where('total_calculado', '<=', $monto),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['minimo'] ?? null) {
                            $indicators[] = 'Monto Min: $' . number_format($data['minimo'], 2);
                        }
                        if ($data['maximo'] ?? null) {
                            $indicators[] = 'Monto Max: $' . number_format($data['maximo'], 2);
                        }
                        return $indicators;
                    }),

                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Creada por'),
            ])
            ->actions([
                Tables\Actions\Action::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->url(fn (OrdenDeCompra $record): string => route('orden-compra.pdf', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('recibirStock')
                    ->label('Recibir Stock')
                    ->icon('heroicon-o-archive-box-arrow-down')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'aprobada')
                    ->modalHeading('Recibir Items de la Orden')
                    ->modalWidth('2xl')
                    
                    // Usamos un Wizard (pasos) para registrar los lotes
                    ->steps(function ($record): array {
                        $steps = [];

                        // Generamos un paso por CADA item en la orden
                        foreach ($record->items as $item) {
                            $steps[] = Wizard\Step::make($item->insumo->nombre)
                                ->description("Recibiendo {$item->cantidad} {$item->insumo->unidad_de_medida->value} de {$item->insumo->nombre}")
                                ->schema([
                                    // Pedimos los datos del lote
                                    DatePicker::make($item->id . '.fecha_vencimiento')
                                        ->label('Fecha de Vencimiento')
                                        ->nullable(),
                                    TextInput::make($item->id . '.codigo_lote')
                                        ->label('Código de Lote (Opcional)')
                                        ->nullable(),
                                ]);
                        }
                        return $steps;
                    })
                    
                    // Esta es la lógica que se ejecuta al confirmar el Wizard
                    ->action(function (array $data, OrdenDeCompra $record) {
                        try {
                            DB::transaction(function () use ($data, $record) {
                                
                                // 1. Creamos los lotes con conversión de unidades
                                foreach ($record->items as $item) {
                                    $loteData = $data[$item->id] ?? [];
                                    
                                    // Buscar datos de compra en tabla pivote
                                    $proveedorData = $item->insumo->proveedores()
                                        ->where('proveedor_id', $record->proveedor_id)
                                        ->first();
                                    
                                    $unidadCompra = UnidadMedida::from($proveedorData->pivot->unidad_compra);
                                    $unidadBase = $item->insumo->unidad_de_medida;
                                    
                                    // Validar compatibilidad de unidades
                                    if (!ConversionHelper::sonCompatibles($unidadCompra, $unidadBase)) {
                                        throw new \Exception(
                                            "Error de conversión para {$item->insumo->nombre}: " .
                                            "No se puede convertir {$unidadCompra->getLabel()} a {$unidadBase->getLabel()}"
                                        );
                                    }
                                    
                                    // La cantidad en la orden ya está en la unidad de compra correcta
                                    // Solo convertimos de unidad de compra a unidad base, SIN multiplicar por cantidad_por_bulto
                                    // porque $item->cantidad ya representa la cantidad total comprada
                                    $cantidadReal = ConversionHelper::convertirABase(
                                        cantidad: $item->cantidad,
                                        unidadCompra: $unidadCompra,
                                        unidadBase: $unidadBase
                                    );
                                    
                                    Lote::create([
                                        'insumo_id' => $item->insumo_id,
                                        'cantidad_inicial' => $cantidadReal,
                                        'cantidad_actual' => $cantidadReal,
                                        'fecha_vencimiento' => $loteData['fecha_vencimiento'] ?? null,
                                        'codigo_lote' => $loteData['codigo_lote'] ?? null,
                                    ]);
                                }
                                
                                // 2. Actualizamos el estado de la Orden de Compra
                                $record->update(['status' => 'recibida_total']);

                                Notification::make()
                                    ->title('¡Stock Recibido!')
                                    ->success()
                                    ->body('Los lotes se han creado y el stock ha sido actualizado.')
                                    ->send();

                            });
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error al recibir el stock')
                                ->danger()
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),

                // UC-16: Acción Cancelar Orden de Compra con justificación
                Tables\Actions\Action::make('cancelar')
                    ->label('Cancelar Orden')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Cancelar Orden de Compra')
                    ->modalDescription('⚠️ Esta acción cancelará la orden de compra de forma permanente. Debe proporcionar una justificación obligatoria.')
                    ->modalIcon('heroicon-o-exclamation-triangle')
                    ->form([
                        Forms\Components\Textarea::make('justification')
                            ->label('Justificación Obligatoria')
                            ->required()
                            ->placeholder('Ej: Proveedor sin stock, cambio de planificación, precio demasiado alto, etc.')
                            ->rows(4)
                            ->helperText('La justificación será registrada en el log de auditoría.'),
                    ])
                    ->action(function (OrdenDeCompra $record, array $data): void {
                        // Validar que la OC puede ser cancelada
                        if (in_array($record->status, ['recibida_total', 'cancelada'])) {
                            Notification::make()
                                ->title('Error')
                                ->body('No se puede cancelar una orden que ya fue recibida o está cancelada.')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Si está recibida parcialmente, advertir
                        if ($record->status === 'recibida_parcial') {
                            Notification::make()
                                ->title('Advertencia')
                                ->body('Esta orden tiene recepciones parciales. La cancelación no revertirá el stock ya recibido.')
                                ->warning()
                                ->send();
                        }

                        try {
                            DB::transaction(function () use ($record, $data) {
                                $oldStatus = $record->status;
                                
                                // Cambiar estado a cancelada
                                $record->status = 'cancelada';
                                $record->save();

                                // Registrar en auditoría (UC-16 crítico)
                                $record->auditAction(
                                    action: 'cancelled',
                                    justification: $data['justification'],
                                    data: [
                                        'old_status' => $oldStatus,
                                        'new_status' => 'cancelada',
                                        'proveedor_id' => $record->proveedor_id,
                                        'total_calculado' => $record->total_calculado,
                                        'fecha_cancelacion' => now()->toDateTimeString(),
                                    ]
                                );
                            });

                            Notification::make()
                                ->title('✅ Orden de Compra Cancelada')
                                ->body('La orden ha sido cancelada exitosamente y registrada en auditoría.')
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error al cancelar orden')
                                ->body('Ocurrió un error: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (OrdenDeCompra $record): bool => 
                        !in_array($record->status, ['recibida_total', 'cancelada'])
                    ),

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
            // Ya no necesitamos ItemsRelationManager porque usamos Repeater en el formulario
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrdenDeCompras::route('/'),
            'create' => Pages\CreateOrdenDeCompra::route('/create'),
            'edit' => Pages\EditOrdenDeCompra::route('/{record}/edit'),
        ];
    }
}
