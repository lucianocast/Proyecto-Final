<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RecetaResource\Pages;
use App\Filament\Admin\Resources\RecetaResource\RelationManagers;
use App\Models\Receta;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RecetaResource extends Resource
{
    protected static ?string $model = Receta::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Producción';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        TextInput::make('nombre')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->label('Nombre de la Receta')
                            ->helperText('Ejemplo: Torta de Chocolate Clásica'),
                        
                        Textarea::make('descripcion')
                            ->maxLength(65535)
                            ->rows(3)
                            ->label('Descripción')
                            ->columnSpanFull(),
                        
                        Select::make('categoria')
                            ->options([
                                'tortas' => 'Tortas',
                                'tartas' => 'Tartas',
                                'pasteles' => 'Pasteles',
                                'postres' => 'Postres',
                                'masas' => 'Masas',
                                'rellenos' => 'Rellenos',
                                'coberturas' => 'Coberturas',
                                'otros' => 'Otros',
                            ])
                            ->searchable()
                            ->label('Categoría'),
                        
                        Toggle::make('activo')
                            ->default(true)
                            ->label('Estado Activo')
                            ->helperText('Solo las recetas activas pueden usarse en producción'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Rendimiento y Producción')
                    ->schema([
                        TextInput::make('rendimiento')
                            ->required()
                            ->maxLength(255)
                            ->label('Rendimiento')
                            ->helperText('Ejemplo: 1 torta de 2kg, 10 porciones, 500g de masa')
                            ->placeholder('1 torta'),
                        
                        TextInput::make('porciones')
                            ->numeric()
                            ->minValue(1)
                            ->label('Porciones (opcional)')
                            ->helperText('Número de porciones que produce esta receta'),
                        
                        TextInput::make('tiempo_preparacion')
                            ->maxLength(255)
                            ->label('Tiempo de Preparación')
                            ->helperText('Ejemplo: 2 horas, 45 minutos')
                            ->placeholder('1 hora'),
                    ])
                    ->columns(3),
                
                Forms\Components\Section::make('Instrucciones')
                    ->schema([
                        RichEditor::make('instrucciones')
                            ->columnSpanFull()
                            ->label('Instrucciones de Preparación')
                            ->toolbarButtons([
                                'bold',
                                'bulletList',
                                'orderedList',
                                'italic',
                                'redo',
                                'undo',
                            ]),
                    ]),
                
                Forms\Components\Section::make('Costo')
                    ->schema([
                        Forms\Components\Placeholder::make('costo_total_calculado')
                            ->label('Costo Primo Actual')
                            ->content(fn (?Receta $record): string => 
                                $record ? '$' . number_format($record->costo_total_calculado, 2) : 'Se calculará automáticamente'
                            ),
                    ])
                    ->collapsible(),
                
                Select::make('producto_id')
                    ->relationship('producto', 'nombre')
                    ->searchable()
                    ->preload()
                    ->label('Producto Asociado (opcional)')
                    ->helperText('Puedes vincular esta receta a un producto del catálogo'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->label('Nombre'),
                
                TextColumn::make('categoria')
                    ->badge()
                    ->searchable()
                    ->color(fn (string $state): string => match ($state) {
                        'tortas' => 'success',
                        'tartas' => 'warning',
                        'pasteles' => 'info',
                        'postres' => 'danger',
                        'masas' => 'gray',
                        default => 'primary',
                    })
                    ->label('Categoría'),
                
                TextColumn::make('rendimiento')
                    ->searchable()
                    ->label('Rendimiento')
                    ->toggleable(),
                
                TextColumn::make('porciones')
                    ->numeric()
                    ->sortable()
                    ->label('Porciones')
                    ->toggleable(),
                
                TextColumn::make('costo_total_calculado')
                    ->money('ARS')
                    ->sortable()
                    ->label('Costo Primo')
                    ->weight('semibold')
                    ->color('primary'),
                
                TextColumn::make('insumos_count')
                    ->counts('insumos')
                    ->label('Insumos')
                    ->badge()
                    ->color('info'),
                
                TextColumn::make('producto.nombre')
                    ->sortable()
                    ->searchable()
                    ->label('Producto Asociado')
                    ->toggleable()
                    ->default('Sin vincular'),
                
                Tables\Columns\IconColumn::make('activo')
                    ->boolean()
                    ->label('Activa')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Creada')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categoria')
                    ->options([
                        'tortas' => 'Tortas',
                        'tartas' => 'Tartas',
                        'pasteles' => 'Pasteles',
                        'postres' => 'Postres',
                        'masas' => 'Masas',
                        'rellenos' => 'Rellenos',
                        'coberturas' => 'Coberturas',
                        'otros' => 'Otros',
                    ])
                    ->label('Categoría'),
                
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Estado')
                    ->placeholder('Todas las recetas')
                    ->trueLabel('Solo activas')
                    ->falseLabel('Solo desactivadas')
                    ->default(true),
                
                Tables\Filters\Filter::make('con_producto')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('producto_id'))
                    ->label('Con producto asociado'),
                
                Tables\Filters\Filter::make('sin_insumos')
                    ->query(fn (Builder $query): Builder => $query->doesntHave('insumos'))
                    ->label('Sin insumos definidos'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('desactivar')
                    ->icon('heroicon-o-no-symbol')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (Receta $record): bool => $record->activo)
                    ->modalHeading('Desactivar Receta')
                    ->modalDescription('¿Está seguro de desactivar esta receta? No podrá usarse en nuevas órdenes de producción.')
                    ->form([
                        Textarea::make('justificacion')
                            ->required()
                            ->label('Justificación')
                            ->helperText('Motivo de la desactivación (ej., obsoleta, descontinuada, falla de calidad)'),
                    ])
                    ->action(function (Receta $record, array $data): void {
                        // Validar que no esté vinculada a producto activo
                        if ($record->producto && $record->producto->activo) {
                            \Filament\Notifications\Notification::make()
                                ->title('No se puede desactivar')
                                ->body('Esta receta está vinculada a un producto activo. Primero desvincule o desactive el producto.')
                                ->danger()
                                ->send();
                            return;
                        }
                        
                        // Validar que no esté en órdenes de producción pendientes
                        $opsPendientes = $record->ordenesProduccion()
                            ->whereIn('estado', ['pendiente', 'en_proceso'])
                            ->count();
                        
                        if ($opsPendientes > 0) {
                            \Filament\Notifications\Notification::make()
                                ->title('No se puede desactivar')
                                ->body("Esta receta tiene {$opsPendientes} órdenes de producción pendientes.")
                                ->danger()
                                ->send();
                            return;
                        }
                        
                        $record->activo = false;
                        $record->save();
                        
                        // Registrar en auditoría
                        $record->auditAction('desactivar_receta', $data['justificacion'], [
                            'nombre_receta' => $record->nombre,
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Receta desactivada')
                            ->body('La receta ha sido desactivada correctamente.')
                            ->success()
                            ->send();
                    }),
                
                Tables\Actions\Action::make('activar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Receta $record): bool => !$record->activo)
                    ->requiresConfirmation()
                    ->action(function (Receta $record): void {
                        $record->activo = true;
                        $record->save();
                        
                        $record->auditAction('activar_receta', 'Receta reactivada', [
                            'nombre_receta' => $record->nombre,
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Receta activada')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('exportar')
                        ->icon('heroicon-o-document-arrow-down')
                        ->action(fn () => \Filament\Notifications\Notification::make()
                            ->title('Exportación en desarrollo')
                            ->info()
                            ->send()),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\InsumosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecetas::route('/'),
            'create' => Pages\CreateReceta::route('/create'),
            'edit' => Pages\EditReceta::route('/{record}/edit'),
        ];
    }
}
