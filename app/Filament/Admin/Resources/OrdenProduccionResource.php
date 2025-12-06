<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OrdenProduccionResource\Pages;
use App\Filament\Admin\Resources\OrdenProduccionResource\RelationManagers;
use App\Models\OrdenProduccion;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Receta;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class OrdenProduccionResource extends Resource
{
    protected static ?string $model = OrdenProduccion::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Producción';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Órdenes de Producción';
    protected static ?string $modelLabel = 'Orden de Producción';
    protected static ?string $pluralModelLabel = 'Órdenes de Producción';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Selección de Producto y Receta')
                    ->schema([
                        Select::make('producto_id')
                            ->label('Producto a Elaborar')
                            ->options(Producto::activos()->orderBy('nombre')->pluck('nombre', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set, $state) {
                                if ($state) {
                                    $producto = Producto::find($state);
                                    if ($producto && $producto->receta) {
                                        $set('receta_id', $producto->receta->id);
                                    } else {
                                        $set('receta_id', null);
                                        Notification::make()
                                            ->title('Atención')
                                            ->body('Este producto no tiene receta asociada.')
                                            ->warning()
                                            ->send();
                                    }
                                }
                            })
                            ->helperText('Seleccione el producto que se va a producir'),
                        
                        Select::make('receta_id')
                            ->label('Receta')
                            ->options(Receta::activas()->orderBy('nombre')->pluck('nombre', 'id'))
                            ->searchable()
                            ->preload()
                            ->helperText('Se selecciona automáticamente si el producto tiene receta asociada')
                            ->disabled(fn (?Model $record) => $record && $record->estado !== 'pendiente'),
                        
                        TextInput::make('cantidad_a_producir')
                            ->label('Cantidad a Producir')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->live()
                            ->helperText('Número de unidades del producto')
                            ->disabled(fn (?Model $record) => $record && in_array($record->estado, ['terminada', 'cancelada'])),
                    ])
                    ->columns(3),
                
                Section::make('Vinculación con Pedidos')
                    ->schema([
                        Select::make('pedidos')
                            ->label('Pedidos Asociados')
                            ->multiple()
                            ->relationship('pedidos', 'id')
                            ->getOptionLabelFromRecordUsing(fn (Pedido $record) => 
                                "Pedido #{$record->id} - {$record->cliente->nombre} - {$record->fecha_entrega->format('d/m/Y')}")
                            ->searchable()
                            ->preload()
                            ->helperText('Seleccione los pedidos que se cubrirán con esta orden')
                            ->columnSpanFull()
                            ->disabled(fn (?Model $record) => $record && in_array($record->estado, ['terminada', 'cancelada'])),
                    ])
                    ->collapsible(),
                
                Section::make('Planificación')
                    ->schema([
                        DatePicker::make('fecha_inicio')
                            ->label('Fecha de Inicio')
                            ->required()
                            ->default(now())
                            ->minDate(now()->subDays(1))
                            ->disabled(fn (?Model $record) => $record && $record->estado === 'terminada'),
                        
                        DatePicker::make('fecha_limite')
                            ->label('Fecha Límite de Terminación')
                            ->required()
                            ->minDate(fn (Get $get) => $get('fecha_inicio') ?? now())
                            ->helperText('Fecha máxima para terminar la producción')
                            ->disabled(fn (?Model $record) => $record && $record->estado === 'terminada'),
                        
                        Select::make('estado')
                            ->options([
                                'pendiente' => 'Pendiente',
                                'en_proceso' => 'En Proceso',
                                'terminada' => 'Terminada',
                                'cancelada' => 'Cancelada',
                            ])
                            ->default('pendiente')
                            ->required()
                            ->label('Estado')
                            ->helperText('Estado actual de la orden'),
                    ])
                    ->columns(3),
                
                Section::make('Información de Producción')
                    ->schema([
                        Placeholder::make('insumos_info')
                            ->label('Insumos Requeridos')
                            ->content(function (?Model $record): string {
                                if (!$record || !$record->receta) {
                                    return 'Seleccione un producto con receta para ver los insumos';
                                }
                                
                                $insumos = $record->estimarInsumos();
                                if (empty($insumos)) {
                                    return 'La receta no tiene insumos definidos';
                                }
                                
                                $html = '<div class="space-y-2">';
                                foreach ($insumos as $insumo) {
                                    $suficiente = $insumo['stock_disponible'] >= $insumo['cantidad_total'];
                                    $clase = $suficiente ? 'text-green-600' : 'text-red-600';
                                    $html .= "<div class='text-sm {$clase}'>";
                                    $html .= "<strong>{$insumo['nombre']}</strong>: ";
                                    $html .= "{$insumo['cantidad_total']} {$insumo['unidad']} ";
                                    $html .= "(" . ($suficiente ? '✓' : '✗ Insuficiente') . ")";
                                    $html .= "</div>";
                                }
                                $html .= '</div>';
                                
                                return $html;
                            })
                            ->columnSpanFull(),
                        
                        Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Notas adicionales sobre esta orden de producción'),
                    ])
                    ->visible(fn (?Model $record) => $record !== null),
                
                Section::make('Finalización')
                    ->schema([
                        TextInput::make('cantidad_producida')
                            ->label('Cantidad Producida')
                            ->numeric()
                            ->minValue(0)
                            ->helperText('Cantidad realmente producida')
                            ->required(fn (Get $get) => $get('estado') === 'terminada'),
                        
                        DatePicker::make('fecha_finalizacion')
                            ->label('Fecha de Finalización')
                            ->maxDate(now())
                            ->required(fn (Get $get) => $get('estado') === 'terminada'),
                        
                        TextInput::make('costo_total')
                            ->label('Costo Total')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->helperText('Se calcula automáticamente'),
                    ])
                    ->columns(3)
                    ->visible(fn (Get $get) => in_array($get('estado'), ['terminada', 'en_proceso'])),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID OP')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                
                TextColumn::make('producto.nombre')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->wrap(),
                
                TextColumn::make('receta.nombre')
                    ->label('Receta')
                    ->searchable()
                    ->toggleable()
                    ->default('Sin receta'),
                
                TextColumn::make('cantidad_a_producir')
                    ->label('Cantidad')
                    ->alignCenter()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'en_proceso' => 'primary',
                        'terminada' => 'success',
                        'cancelada' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->sortable(),
                
                TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('fecha_limite')
                    ->label('Límite')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(function ($record) {
                        if ($record->estado === 'terminada') return 'success';
                        if ($record->fecha_limite < now()) return 'danger';
                        if ($record->fecha_limite < now()->addDays(2)) return 'warning';
                        return 'gray';
                    })
                    ->icon(function ($record) {
                        return $record->estado !== 'terminada' && $record->fecha_limite < now() 
                            ? 'heroicon-o-exclamation-triangle' 
                            : '';
                    }),
                
                TextColumn::make('pedidos_count')
                    ->counts('pedidos')
                    ->label('Pedidos')
                    ->badge()
                    ->color('primary')
                    ->alignCenter(),
                
                TextColumn::make('usuario.name')
                    ->label('Creado por')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('costo_total')
                    ->label('Costo')
                    ->money('ARS')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'en_proceso' => 'En Proceso',
                        'terminada' => 'Terminada',
                        'cancelada' => 'Cancelada',
                    ])
                    ->label('Estado'),
                
                SelectFilter::make('producto_id')
                    ->relationship('producto', 'nombre')
                    ->searchable()
                    ->preload()
                    ->label('Producto'),
                
                TernaryFilter::make('atrasadas')
                    ->label('Órdenes Atrasadas')
                    ->placeholder('Todas')
                    ->trueLabel('Solo atrasadas')
                    ->falseLabel('Sin atrasos')
                    ->queries(
                        true: fn (Builder $query) => $query->where('fecha_limite', '<', now())
                            ->whereNotIn('estado', ['terminada', 'cancelada']),
                        false: fn (Builder $query) => $query->where(function ($q) {
                            $q->where('fecha_limite', '>=', now())
                              ->orWhereIn('estado', ['terminada', 'cancelada']);
                        }),
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('iniciar')
                    ->icon('heroicon-o-play')
                    ->color('primary')
                    ->visible(fn (OrdenProduccion $record): bool => $record->estado === 'pendiente')
                    ->requiresConfirmation()
                    ->action(function (OrdenProduccion $record): void {
                        // Verificar stock antes de iniciar
                        $faltantes = $record->verificarStock();
                        
                        if (!empty($faltantes)) {
                            $mensaje = "Alerta: Faltan insumos:\n";
                            foreach ($faltantes as $f) {
                                $mensaje .= "- {$f['insumo']}: Falta {$f['faltante']} {$f['unidad']}\n";
                            }
                            
                            Notification::make()
                                ->title('Stock Insuficiente')
                                ->body($mensaje)
                                ->warning()
                                ->persistent()
                                ->send();
                        }
                        
                        $record->estado = 'en_proceso';
                        $record->save();
                        
                        $record->auditAction('iniciar_orden_produccion', 'Orden iniciada', [
                            'orden_id' => $record->id,
                            'producto' => $record->producto->nombre,
                            'faltantes' => $faltantes,
                        ]);
                        
                        Notification::make()
                            ->title('Orden Iniciada')
                            ->success()
                            ->send();
                    }),
                
                Tables\Actions\Action::make('finalizar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (OrdenProduccion $record): bool => $record->estado === 'en_proceso')
                    ->form([
                        TextInput::make('cantidad_producida')
                            ->label('Cantidad Producida')
                            ->required()
                            ->numeric()
                            ->minValue(1),
                        Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->helperText('Detalles sobre la producción, mermas, etc.'),
                    ])
                    ->action(function (OrdenProduccion $record, array $data): void {
                        DB::beginTransaction();
                        try {
                            // Actualizar OP
                            $record->cantidad_producida = $data['cantidad_producida'];
                            $record->observaciones = ($record->observaciones ?? '') . "\n" . ($data['observaciones'] ?? '');
                            $record->estado = 'terminada';
                            $record->fecha_finalizacion = now();
                            
                            // Calcular y descontar insumos del stock
                            if ($record->receta) {
                                $consumoReal = $record->estimarInsumos();
                                $record->insumos_consumidos = $consumoReal;
                                
                                // TODO: Aquí iría la lógica de descuento de stock
                                // Por ahora solo registramos
                            }
                            
                            $record->save();
                            
                            // Actualizar pedidos asociados a "listo"
                            foreach ($record->pedidos as $pedido) {
                                if ($pedido->status !== 'entregado') {
                                    $pedido->status = 'listo';
                                    $pedido->save();
                                }
                            }
                            
                            // Auditoría
                            $record->auditAction('finalizar_orden_produccion', 'Orden finalizada', [
                                'orden_id' => $record->id,
                                'cantidad_producida' => $data['cantidad_producida'],
                                'cantidad_planificada' => $record->cantidad_a_producir,
                                'observaciones' => $data['observaciones'] ?? '',
                            ]);
                            
                            DB::commit();
                            
                            Notification::make()
                                ->title('Orden Finalizada')
                                ->body('Los pedidos asociados han sido marcados como "Listos para Entrega".')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            DB::rollBack();
                            Notification::make()
                                ->title('Error')
                                ->body('Hubo un error al finalizar la orden: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                
                Tables\Actions\Action::make('cancelar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (OrdenProduccion $record): bool => in_array($record->estado, ['pendiente', 'en_proceso']))
                    ->requiresConfirmation()
                    ->modalDescription('¿Está seguro de cancelar esta orden de producción?')
                    ->form([
                        Textarea::make('motivo_cancelacion')
                            ->label('Motivo de Cancelación')
                            ->required(),
                    ])
                    ->action(function (OrdenProduccion $record, array $data): void {
                        $record->estado = 'cancelada';
                        $record->observaciones = ($record->observaciones ?? '') . "\nCANCELADA: " . $data['motivo_cancelacion'];
                        $record->save();
                        
                        $record->auditAction('cancelar_orden_produccion', $data['motivo_cancelacion'], [
                            'orden_id' => $record->id,
                        ]);
                        
                        Notification::make()
                            ->title('Orden Cancelada')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListOrdenProduccions::route('/'),
            'create' => Pages\CreateOrdenProduccion::route('/create'),
            'edit' => Pages\EditOrdenProduccion::route('/{record}/edit'),
        ];
    }
}
