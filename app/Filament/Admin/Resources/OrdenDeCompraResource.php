<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OrdenDeCompraResource\Pages;
use App\Filament\Admin\Resources\OrdenDeCompraResource\RelationManagers;
use App\Models\OrdenDeCompra;
use App\Models\Lote;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
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
                Select::make('proveedor_id')
                    ->relationship('proveedor', 'nombre_empresa')
                    ->required()
                    ->searchable(['nombre_empresa', 'nombre_contacto'])
                    ->preload()
                    ->label('Proveedor'),
                Select::make('status')
                    ->required()
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'Aprobada' => 'Aprobada',
                        'Recibida' => 'Recibida',
                        'Cancelada' => 'Cancelada',
                    ])
                    ->default('Pendiente'),
                DatePicker::make('fecha_emision')
                    ->required()
                    ->label('Fecha de Emisión')
                    ->default(now()),
                TextInput::make('total_calculado')
                    ->required()
                    ->numeric()
                    ->readOnly()
                    ->prefix('$')
                    ->label('Total')
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('proveedor.nombre_empresa')
                    ->searchable(['nombre_empresa', 'nombre_contacto'])
                    ->sortable()
                    ->label('Proveedor'),
                TextColumn::make('user.name')
                    ->label('Creada por'),
                TextColumn::make('fecha_emision')
                    ->date()
                    ->sortable()
                    ->label('Fecha'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pendiente' => 'warning',
                        'Aprobada' => 'primary',
                        'Recibida' => 'success',
                        'Cancelada' => 'danger',
                    }),
                TextColumn::make('total_calculado')
                    ->money('ARS')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('recibirStock')
                    ->label('Recibir Stock')
                    ->icon('heroicon-o-archive-box-arrow-down')
                    ->color('success')
                    // La acción solo es visible si el estado es 'Aprobada'
                    ->visible(fn ($record) => $record->status === 'Aprobada')
                    ->modalHeading('Recibir Items de la Orden')
                    ->modalWidth('2xl')
                    
                    // Usamos un Wizard (pasos) para registrar los lotes
                    ->steps(function ($record): array {
                        $steps = [];

                        // Generamos un paso por CADA item en la orden
                        foreach ($record->items as $item) {
                            $steps[] = Wizard\Step::make($item->insumo->nombre)
                                ->description("Recibiendo {$item->cantidad} {$item->insumo->unidad_de_medida} de {$item->insumo->nombre}")
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
                                
                                // 1. Creamos los lotes
                                foreach ($record->items as $item) {
                                    $loteData = $data[$item->id] ?? []; // Obtenemos los datos de este item
                                    
                                    Lote::create([
                                        'insumo_id' => $item->insumo_id,
                                        'cantidad_inicial' => $item->cantidad,
                                        'cantidad_actual' => $item->cantidad,
                                        'fecha_vencimiento' => $loteData['fecha_vencimiento'] ?? null,
                                        'codigo_lote' => $loteData['codigo_lote'] ?? null,
                                    ]);
                                }
                                
                                // 2. Actualizamos el estado de la Orden de Compra
                                $record->update(['status' => 'Recibida']);

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
            RelationManagers\ItemsRelationManager::class,
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
