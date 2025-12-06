<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductoResource\Pages;
use App\Filament\Admin\Resources\ProductoResource\RelationManagers;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// --- IMPORTACIONES MEJORADAS ---
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ToggleColumn;
use App\Models\CategoriaProducto;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static ?string $navigationIcon = 'heroicon-o-cake'; // <- Ícono temático
    protected static ?string $navigationGroup = 'Producción'; // <- Agrupado

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // --- CAMPO DE CATEGORÍA MEJORADO ---
                Select::make('categoria_producto_id')
                    ->relationship(name: 'categoria', titleAttribute: 'nombre')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Categoría'),

                TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),

                Textarea::make('descripcion')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                
                Toggle::make('activo')
                    ->required(),
                
                // --- CAMPOS DE CATÁLOGO ---
                Toggle::make('visible_en_catalogo')
                    ->label('Visible en Catálogo')
                    ->default(true),
                
                FileUpload::make('imagen_url')
                    ->label('Imagen')
                    ->directory('productos')
                    ->image()
                    ->columnSpanFull(),
                
                TagsInput::make('etiquetas')
                    ->label('Etiquetas')
                    ->placeholder('Ej: Sin TACC, Destacado, Vegano')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable(),
                
                // --- COLUMNA DE CATEGORÍA MEJORADA ---
                TextColumn::make('categoria.nombre')
                    ->numeric()
                    ->sortable()
                    ->searchable(),

                IconColumn::make('activo')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categoria')
                    ->relationship('categoria', 'nombre')
                    ->searchable()
                    ->preload()
                    ->label('Categoría'),
                
                Tables\Filters\SelectFilter::make('activo')
                    ->label('Estado')
                    ->options([
                        1 => 'Activo',
                        0 => 'Inactivo',
                    ])
                    ->default(1),
                
                Tables\Filters\SelectFilter::make('visible_en_catalogo')
                    ->label('Visible en Catálogo')
                    ->options([
                        1 => 'Visible',
                        0 => 'Oculto',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                // UC-24: Desactivar producto con justificación
                Tables\Actions\Action::make('desactivar')
                    ->label('Desactivar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Producto $record): bool => $record->activo)
                    ->requiresConfirmation()
                    ->modalHeading('Desactivar Producto')
                    ->modalDescription('Al desactivar este producto, no estará disponible para nuevos pedidos.')
                    ->form([
                        Textarea::make('justificacion')
                            ->label('Justificación (Obligatorio)')
                            ->required()
                            ->placeholder('Ej: Producto discontinuado, falta de insumos, cambio de proveedor, etc.')
                            ->rows(4)
                            ->helperText('Describa el motivo por el cual se desactiva este producto.'),
                    ])
                    ->action(function (Producto $record, array $data): void {
                        DB::transaction(function () use ($record, $data) {
                            // Validar que no hay pedidos pendientes/en producción con este producto
                            $pedidosPendientes = DB::table('pedido_items')
                                ->join('pedidos', 'pedido_items.pedido_id', '=', 'pedidos.id')
                                ->join('producto_variantes', 'pedido_items.producto_variante_id', '=', 'producto_variantes.id')
                                ->where('producto_variantes.producto_id', $record->id)
                                ->whereIn('pedidos.status', ['pendiente', 'en_produccion'])
                                ->count();
                            
                            if ($pedidosPendientes > 0) {
                                Notification::make()
                                    ->title('No se puede desactivar el producto')
                                    ->body("Hay {$pedidosPendientes} pedido(s) pendiente(s) o en producción con este producto. Complete o cancele esos pedidos primero.")
                                    ->danger()
                                    ->send();
                                return;
                            }
                            
                            // Guardar estado anterior
                            $estadoAnterior = $record->activo;
                            
                            // Desactivar producto
                            $record->activo = false;
                            $record->save();
                            
                            // Registrar en auditoría
                            $record->auditAction(
                                action: 'desactivar_producto',
                                justification: $data['justificacion'],
                                data: [
                                    'producto_id' => $record->id,
                                    'producto_nombre' => $record->nombre,
                                    'categoria' => $record->categoria?->nombre,
                                    'estado_anterior' => $estadoAnterior ? 'Activo' : 'Inactivo',
                                    'estado_nuevo' => 'Inactivo',
                                    'fecha_desactivacion' => now()->toDateTimeString(),
                                ]
                            );
                            
                            Notification::make()
                                ->title('Producto desactivado correctamente')
                                ->body('El producto ya no estará disponible para nuevos pedidos.')
                                ->success()
                                ->send();
                        });
                    }),
                
                // UC-24: Activar producto con justificación
                Tables\Actions\Action::make('activar')
                    ->label('Activar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Producto $record): bool => !$record->activo)
                    ->requiresConfirmation()
                    ->modalHeading('Activar Producto')
                    ->modalDescription('Al activar este producto, estará disponible para nuevos pedidos.')
                    ->form([
                        Textarea::make('justificacion')
                            ->label('Justificación (Obligatorio)')
                            ->required()
                            ->placeholder('Ej: Insumos disponibles nuevamente, reactivación por demanda, nuevo proveedor, etc.')
                            ->rows(4)
                            ->helperText('Describa el motivo por el cual se reactiva este producto.'),
                    ])
                    ->action(function (Producto $record, array $data): void {
                        DB::transaction(function () use ($record, $data) {
                            // Guardar estado anterior
                            $estadoAnterior = $record->activo;
                            
                            // Activar producto
                            $record->activo = true;
                            $record->save();
                            
                            // Registrar en auditoría
                            $record->auditAction(
                                action: 'activar_producto',
                                justification: $data['justificacion'],
                                data: [
                                    'producto_id' => $record->id,
                                    'producto_nombre' => $record->nombre,
                                    'categoria' => $record->categoria?->nombre,
                                    'estado_anterior' => $estadoAnterior ? 'Activo' : 'Inactivo',
                                    'estado_nuevo' => 'Activo',
                                    'fecha_activacion' => now()->toDateTimeString(),
                                ]
                            );
                            
                            Notification::make()
                                ->title('Producto activado correctamente')
                                ->body('El producto está disponible para nuevos pedidos.')
                                ->success()
                                ->send();
                        });
                    }),
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
            RelationManagers\VariantesRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProducto::route('/create'),
            'edit' => Pages\EditProducto::route('/{record}/edit'),
        ];
    }    
}