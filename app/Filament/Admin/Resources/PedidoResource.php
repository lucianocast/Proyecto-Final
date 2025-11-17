<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PedidoResource\Pages;
use App\Filament\Admin\Resources\PedidoResource\RelationManagers;
use App\Models\Pedido;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                    ->relationship('cliente', 'nombre')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Cliente'),
                Select::make('status')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'en_produccion' => 'En Producción',
                        'listo' => 'Listo',
                        'entregado' => 'Entregado',
                        'cancelado' => 'Cancelado',
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
                TextInput::make('total_calculado')
                    ->numeric()
                    ->prefix('$')
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
                    ->afterStateUpdated(fn (Set $set, Get $get) => $set('saldo_pendiente', $get('total_calculado') - $get('monto_abonado'))),
                TextInput::make('saldo_pendiente')
                    ->numeric()
                    ->prefix('$')
                    ->label('Saldo Pendiente')
                    ->readOnly(),
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
                //
            ])
            ->actions([
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
            'index' => Pages\ListPedidos::route('/'),
            'create' => Pages\CreatePedido::route('/create'),
            'edit' => Pages\EditPedido::route('/{record}/edit'),
        ];
    }
}
