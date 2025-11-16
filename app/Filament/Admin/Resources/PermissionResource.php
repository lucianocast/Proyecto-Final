<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PermissionResource\Pages;
use App\Filament\Admin\Resources\PermissionResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// Importaciones necesarias
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Spatie\Permission\Models\Permission; // El modelo correcto

class PermissionResource extends Resource
{
    // Usa el modelo de Permiso de Spatie
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-key'; // Ícono
    protected static ?string $navigationGroup = 'Roles y Permisos'; // Agrupado

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre del Permiso')
                    ->required()
                    ->maxLength(255),
                Select::make('roles') // Selector para asignar a roles
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                TextInput::make('guard_name')
                    ->label('Guard')
                    ->default('web') // Valor por defecto
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('guard_name')
                    ->label('Guard')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Creado el')
                    ->dateTime('d-m-Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(), // Habilitamos borrar
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
            RelationManagers\RoleRelationManager::class, // Muestra roles
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermissions::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }    
}