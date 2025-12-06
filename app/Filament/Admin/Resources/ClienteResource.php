<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ClienteResource\Pages;
use App\Filament\Admin\Resources\ClienteResource\RelationManagers;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Ventas';
    protected static ?string $navigationLabel = 'Clientes';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        TextInput::make('nombre')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre Completo'),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('El email debe ser único'),
                        TextInput::make('telefono')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('direccion')
                            ->nullable()
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Select::make('user_id')
                            ->relationship(
                                name: 'user',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'cliente'))
                            )
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->label('Cuenta de Usuario Vinculada (Opcional)'),
                        Toggle::make('activo')
                            ->default(true)
                            ->label('Cliente Activo')
                            ->helperText('Los clientes inactivos no pueden generar nuevos pedidos'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Justificación de Cambios (UC-40)')
                    ->description('Requerido por auditoría cuando se modifica información del cliente.')
                    ->schema([
                        Textarea::make('justificacion_cambio')
                            ->label('Justificación del cambio')
                            ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord)
                            ->minLength(10)
                            ->maxLength(500)
                            ->rows(4)
                            ->placeholder('Indique el motivo por el cual se modifican los datos del cliente...')
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
                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),
                TextColumn::make('telefono')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-o-phone'),
                IconColumn::make('activo')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
                TextColumn::make('pedidos_count')
                    ->counts('pedidos')
                    ->label('Total Pedidos')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->label('Fecha Registro')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('activo')
                    ->label('Estado')
                    ->options([
                        1 => 'Activo',
                        0 => 'Inactivo',
                    ])
                    ->default(1),
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
                    ->visible(fn (Cliente $record): bool => $record->activo)
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('justificacion')
                            ->required()
                            ->minLength(10)
                            ->maxLength(500)
                            ->rows(3)
                            ->label('Justificación')
                            ->helperText('Mínimo 10 caracteres. Explique el motivo de la anulación.'),
                    ])
                    ->action(function (Cliente $record, array $data): void {
                        // Verificar que no tenga pedidos pendientes o confirmados
                        $pedidosPendientes = $record->pedidos()
                            ->whereIn('status', ['pendiente', 'confirmado'])
                            ->count();

                        if ($pedidosPendientes > 0) {
                            Notification::make()
                                ->danger()
                                ->title('No se puede anular el cliente')
                                ->body("El cliente tiene {$pedidosPendientes} pedido(s) pendiente(s) o confirmado(s). Debe finalizar esos pedidos primero.")
                                ->persistent()
                                ->send();
                            return;
                        }

                        $record->activo = false;
                        $record->save();

                        // Registrar en auditoría
                        \App\Models\AuditLog::create([
                            'user_id' => auth()->id(),
                            'event' => 'anular_cliente',
                            'auditable_type' => Cliente::class,
                            'auditable_id' => $record->id,
                            'old_values' => ['activo' => true],
                            'new_values' => ['activo' => false],
                            'justificacion' => $data['justificacion'],
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Cliente anulado')
                            ->body('El cliente ha sido marcado como inactivo.')
                            ->send();
                    }),
                Tables\Actions\Action::make('activar')
                    ->label('Activar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Cliente $record): bool => !$record->activo)
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('justificacion')
                            ->required()
                            ->minLength(10)
                            ->maxLength(500)
                            ->rows(3)
                            ->label('Justificación')
                            ->helperText('Mínimo 10 caracteres. Explique el motivo de la activación.'),
                    ])
                    ->action(function (Cliente $record, array $data): void {
                        $record->activo = true;
                        $record->save();

                        // Registrar en auditoría
                        \App\Models\AuditLog::create([
                            'user_id' => auth()->id(),
                            'event' => 'activar_cliente',
                            'auditable_type' => Cliente::class,
                            'auditable_id' => $record->id,
                            'old_values' => ['activo' => false],
                            'new_values' => ['activo' => true],
                            'justificacion' => $data['justificacion'],
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Cliente activado')
                            ->body('El cliente ha sido marcado como activo.')
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
            RelationManagers\PedidosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'edit' => Pages\EditCliente::route('/{record}/edit'),
            'view' => Pages\ViewCliente::route('/{record}'),
        ];
    }
}
