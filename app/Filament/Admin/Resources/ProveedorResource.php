<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProveedorResource\Pages;
use App\Filament\Admin\Resources\ProveedorResource\RelationManagers;
use App\Models\Proveedor;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProveedorResource extends Resource
{
    protected static ?string $model = Proveedor::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Compras y Proveedores';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre_empresa')
                    ->required()
                    ->maxLength(255)
                    ->label('Nombre de la Empresa'),
                TextInput::make('nombre_contacto')
                    ->maxLength(255)
                    ->label('Nombre del Contacto'),
                TextInput::make('cuit')
                    ->required()
                    ->maxLength(255)
                    ->label('CUIT'),
                TextInput::make('email_pedidos')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->label('Email para Pedidos'),
                TextInput::make('telefono')
                    ->tel()
                    ->maxLength(255),
                Textarea::make('direccion')
                    ->columnSpanFull(),
                Textarea::make('notas')
                    ->columnSpanFull(),
                Toggle::make('activo')
                    ->required()
                    ->default(true),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Cuenta de Usuario (Opcional)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre_empresa')
                    ->searchable()
                    ->label('Empresa'),
                TextColumn::make('nombre_contacto')
                    ->searchable()
                    ->label('Contacto'),
                TextColumn::make('email_pedidos')
                    ->label('Email'),
                IconColumn::make('activo')
                    ->boolean(),
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
            RelationManagers\CatalogoInsumosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProveedors::route('/'),
            'create' => Pages\CreateProveedor::route('/create'),
            'edit' => Pages\EditProveedor::route('/{record}/edit'),
        ];
    }
}
