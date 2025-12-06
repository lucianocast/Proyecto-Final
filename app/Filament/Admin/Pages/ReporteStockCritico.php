<?php

namespace App\Filament\Admin\Pages;

use App\Models\Insumo;
use App\Models\CategoriaInsumo;
use Filament\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Actions\ExportAction;
use Illuminate\Database\Eloquent\Builder;

class ReporteStockCritico extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationGroup = 'Stock e Insumos';
    protected static ?string $navigationLabel = 'Stock Crítico';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.admin.pages.reporte-stock-critico';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('categoria_id')
                    ->label('Categoría')
                    ->options(\App\Models\CategoriaInsumo::pluck('nombre', 'id'))
                    ->searchable()
                    ->preload(),
                TextInput::make('ubicacion')
                    ->label('Ubicación'),
            ])
            ->statePath('data')
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Insumo::query()
                    ->with(['categoria', 'lotes'])
                    ->whereRaw('(SELECT COALESCE(SUM(cantidad_actual), 0) FROM lotes WHERE lotes.insumo_id = insumos.id) <= stock_minimo')
                    ->when(
                        $this->data['categoria_id'] ?? null,
                        fn (Builder $query, $categoria): Builder => $query->where('categoria_insumo_id', $categoria)
                    )
                    ->when(
                        $this->data['ubicacion'] ?? null,
                        fn (Builder $query, $ubicacion): Builder => $query->where('ubicacion', 'like', "%{$ubicacion}%")
                    )
            )
            ->columns([
                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable()
                    ->label('Insumo'),
                TextColumn::make('categoria.nombre')
                    ->sortable()
                    ->label('Categoría'),
                TextColumn::make('unidad_de_medida')
                    ->label('Unidad'),
                TextColumn::make('stock_total')
                    ->label('Stock Disponible')
                    ->state(fn (Insumo $record) => $record->stock_total)
                    ->numeric(decimalPlaces: 2)
                    ->color(fn (Insumo $record) => $record->stock_total < 0 ? 'danger' : 'warning')
                    ->weight('bold'),
                TextColumn::make('stock_minimo')
                    ->numeric(decimalPlaces: 2)
                    ->label('Stock Mínimo'),
                TextColumn::make('diferencia')
                    ->label('Diferencia')
                    ->state(fn (Insumo $record) => $record->stock_total - $record->stock_minimo)
                    ->numeric(decimalPlaces: 2)
                    ->color('danger')
                    ->weight('bold'),
                TextColumn::make('ultimo_proveedor')
                    ->label('Último Proveedor')
                    ->state(function (Insumo $record) {
                        $ultimoLote = $record->lotes()->latest()->first();
                        return $ultimoLote ? $ultimoLote->proveedor : 'N/A';
                    }),
                TextColumn::make('ubicacion')
                    ->searchable()
                    ->toggleable(),
            ])
            ->defaultSort('stock_minimo', 'desc')
            ->striped()
            ->headerActions([
                ExportAction::make()
                    ->exporter(\App\Filament\Exports\StockCriticoExporter::class)
                    ->label('Exportar a Excel')
                    ->icon('heroicon-o-arrow-down-tray'),
            ])
            ->paginated([10, 25, 50, 100]);
    }
}
