<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OrdenDeCompraResource\Pages;
use App\Filament\Admin\Resources\OrdenDeCompraResource\RelationManagers;
use App\Models\OrdenDeCompra;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                Select::make('estado')
                    ->required()
                    ->options([
                        'pendiente' => 'Pendiente',
                        'aprobada' => 'Aprobada',
                        'recibida' => 'Recibida',
                        'cancelada' => 'Cancelada',
                    ])
                    ->default('pendiente'),
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
                TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'aprobada' => 'primary',
                        'recibida' => 'success',
                        'cancelada' => 'danger',
                    }),
                TextColumn::make('total_calculado')
                    ->money('ARS')
                    ->sortable(),
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
            'index' => Pages\ListOrdenDeCompras::route('/'),
            'create' => Pages\CreateOrdenDeCompra::route('/create'),
            'edit' => Pages\EditOrdenDeCompra::route('/{record}/edit'),
        ];
    }
}
