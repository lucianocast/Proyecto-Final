<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProveedorResource\Pages;
use App\Filament\Admin\Resources\ProveedorResource\RelationManagers;
use App\Models\AuditLog;
use App\Models\Proveedor;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProveedorResource extends Resource
{
    protected static ?string $model = Proveedor::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Compras y Proveedores';
    protected static ?string $navigationLabel = 'Proveedores';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Datos Fiscales y de Contacto')
                    ->description('Información fiscal y de contacto del proveedor')
                    ->schema([
                        TextInput::make('nombre_empresa')
                            ->required()
                            ->maxLength(255)
                            ->label('Razón Social / Nombre de la Empresa')
                            ->placeholder('Ej: Distribuidora San Martín S.A.')
                            ->columnSpan(2),
                        TextInput::make('cuit')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(13)
                            ->label('CUIT')
                            ->placeholder('Ej: 20-12345678-9')
                            ->helperText('Debe ser único en el sistema'),
                        TextInput::make('nombre_contacto')
                            ->maxLength(255)
                            ->label('Nombre del Contacto')
                            ->placeholder('Ej: Juan Pérez'),
                        TextInput::make('email_pedidos')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->label('Email para Pedidos')
                            ->placeholder('pedidos@proveedor.com'),
                        TextInput::make('telefono')
                            ->tel()
                            ->maxLength(255)
                            ->label('Teléfono')
                            ->placeholder('Ej: +54 9 11 1234-5678'),
                        Textarea::make('direccion')
                            ->maxLength(500)
                            ->rows(2)
                            ->label('Dirección')
                            ->placeholder('Calle, Número, Ciudad, Provincia')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Condiciones Comerciales')
                    ->description('Información sobre términos de compra y notas adicionales')
                    ->schema([
                        Textarea::make('notas')
                            ->label('Notas y Condiciones Comerciales')
                            ->placeholder('Ej: Plazo de pago 30 días, monto mínimo $50.000, entrega en 3-5 días hábiles')
                            ->maxLength(1000)
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                Section::make('Configuración')
                    ->schema([
                        Toggle::make('activo')
                            ->label('Proveedor Activo')
                            ->default(true)
                            ->helperText('Los proveedores inactivos no pueden ser seleccionados en nuevas órdenes de compra'),
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->label('Cuenta de Usuario Vinculada (Opcional)'),
                    ])
                    ->columns(2),
                Section::make('Justificación de Cambios (UC-46)')
                    ->description('Requerido por auditoría cuando se modifica información del proveedor.')
                    ->schema([
                        Textarea::make('justificacion_cambio')
                            ->label('Justificación del cambio')
                            ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord)
                            ->minLength(10)
                            ->maxLength(500)
                            ->rows(4)
                            ->placeholder('Indique el motivo por el cual se modifican los datos del proveedor...')
                            ->helperText('Mínimo 10 caracteres. Este registro quedará en el historial de auditoría.')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord)
                    ->collapsed(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nombre_empresa')
                    ->label('Razón Social')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('cuit')
                    ->label('CUIT')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-o-identification'),
                TextColumn::make('nombre_contacto')
                    ->label('Contacto')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email_pedidos')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),
                TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-phone'),
                IconColumn::make('activo')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextColumn::make('ordenesDeCompra_count')
                    ->counts('ordenesDeCompra')
                    ->label('N° OC')
                    ->badge()
                    ->color('info'),
                TextColumn::make('created_at')
                    ->label('Fecha Registro')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('activo')
                    ->label('Estado')
                    ->options([
                        '1' => 'Activo',
                        '0' => 'Inactivo',
                    ])
                    ->default('1'),
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Registrado desde'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Registrado hasta'),
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
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('anular')
                    ->label('Anular')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Proveedor $record): bool => $record->activo === true)
                    ->form([
                        Textarea::make('justificacion')
                            ->label('Justificación de anulación')
                            ->required()
                            ->minLength(10)
                            ->maxLength(500)
                            ->rows(3)
                            ->placeholder('Indique el motivo por el cual se anula este proveedor...')
                            ->helperText('Mínimo 10 caracteres. Este registro quedará en el historial de auditoría.'),
                    ])
                    ->action(function (Proveedor $record, array $data): void {
                        // UC-47: Validar que no tenga OC pendientes
                        $ocPendientes = $record->ordenesDeCompra()
                            ->where('status', 'Pendiente')
                            ->count();

                        if ($ocPendientes > 0) {
                            Notification::make()
                                ->title('No se puede anular el proveedor')
                                ->body("El proveedor tiene {$ocPendientes} órdenes de compra pendientes. Debe cancelarlas o recibirlas primero.")
                                ->danger()
                                ->persistent()
                                ->send();
                            return;
                        }

                        // Actualizar estado
                        $record->activo = false;
                        $record->save();

                        // Registrar en auditoría
                        AuditLog::create([
                            'user_id' => auth()->id(),
                            'event' => 'anular',
                            'auditable_type' => Proveedor::class,
                            'auditable_id' => $record->id,
                            'old_values' => ['activo' => true],
                            'new_values' => ['activo' => false],
                            'justificacion' => $data['justificacion'],
                        ]);

                        Notification::make()
                            ->title('Proveedor anulado')
                            ->body('El proveedor ha sido anulado correctamente.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('activar')
                    ->label('Activar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Proveedor $record): bool => $record->activo === false)
                    ->form([
                        Textarea::make('justificacion')
                            ->label('Justificación de activación')
                            ->required()
                            ->minLength(10)
                            ->maxLength(500)
                            ->rows(3)
                            ->placeholder('Indique el motivo por el cual se activa este proveedor...')
                            ->helperText('Mínimo 10 caracteres. Este registro quedará en el historial de auditoría.'),
                    ])
                    ->action(function (Proveedor $record, array $data): void {
                        // Actualizar estado
                        $record->activo = true;
                        $record->save();

                        // Registrar en auditoría
                        AuditLog::create([
                            'user_id' => auth()->id(),
                            'event' => 'activar',
                            'auditable_type' => Proveedor::class,
                            'auditable_id' => $record->id,
                            'old_values' => ['activo' => false],
                            'new_values' => ['activo' => true],
                            'justificacion' => $data['justificacion'],
                        ]);

                        Notification::make()
                            ->title('Proveedor activado')
                            ->body('El proveedor ha sido activado correctamente.')
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
            RelationManagers\CatalogoInsumosRelationManager::class,
            RelationManagers\OrdenesDeCompraRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProveedors::route('/'),
            'create' => Pages\CreateProveedor::route('/create'),
            'view' => Pages\ViewProveedor::route('/{record}'),
            'edit' => Pages\EditProveedor::route('/{record}/edit'),
        ];
    }
}
