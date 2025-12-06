<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;
use App\Models\OrdenDeCompra;
use App\Models\Proveedor;
use App\Models\CategoriaInsumo;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * UC-19: Emitir Reporte de Compras por Período
 * 
 * Página personalizada para generar reportes consolidados de compras
 * con métricas y agrupaciones configurables.
 */
class ReporteCompras extends Page implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.admin.pages.reporte-compras';
    protected static ?string $navigationGroup = 'Compras y Proveedores';
    protected static ?string $navigationLabel = 'Reporte de Compras';
    protected static ?string $title = 'Reporte de Compras por Período';
    protected static ?string $slug = 'reportes/compras';
    protected static ?int $navigationSort = 3;

    // Propiedades para mantener estado
    public ?array $data = [];
    public ?array $reporteData = null;

    public function mount(): void
    {
        $this->form->fill([
            'fecha_desde' => now()->startOfMonth(),
            'fecha_hasta' => now()->endOfMonth(),
            'agrupar_por' => 'proveedor',
            'estado_oc' => ['recibida_total', 'recibida_parcial'],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Criterios de Filtrado')
                    ->description('Defina el período y criterios para generar el reporte de compras')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('fecha_desde')
                                    ->label('Fecha Desde')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->maxDate(fn ($get) => $get('fecha_hasta'))
                                    ->default(now()->startOfMonth()),
                                
                                DatePicker::make('fecha_hasta')
                                    ->label('Fecha Hasta')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->minDate(fn ($get) => $get('fecha_desde'))
                                    ->default(now()->endOfMonth()),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                Select::make('proveedor_id')
                                    ->label('Proveedor (Opcional)')
                                    ->placeholder('Todos los proveedores')
                                    ->options(Proveedor::pluck('nombre_empresa', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Deje vacío para incluir todos'),
                                
                                Select::make('categoria_insumo_id')
                                    ->label('Categoría de Insumo (Opcional)')
                                    ->placeholder('Todas las categorías')
                                    ->options(CategoriaInsumo::pluck('nombre', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Deje vacío para incluir todas'),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                Select::make('estado_oc')
                                    ->label('Estado de Órdenes de Compra')
                                    ->options([
                                        'pendiente' => 'Pendiente',
                                        'aprobada' => 'Aprobada',
                                        'recibida_parcial' => 'Recibida Parcial',
                                        'recibida_total' => 'Recibida Total',
                                        'cancelada' => 'Cancelada',
                                    ])
                                    ->multiple()
                                    ->default(['recibida_total', 'recibida_parcial'])
                                    ->required()
                                    ->helperText('Solo órdenes recibidas impactan en costos reales'),
                                
                                Select::make('agrupar_por')
                                    ->label('Agrupar Resultados Por')
                                    ->options([
                                        'proveedor' => 'Proveedor',
                                        'categoria' => 'Categoría de Insumo',
                                        'mes' => 'Mes',
                                        'insumo' => 'Insumo',
                                    ])
                                    ->default('proveedor')
                                    ->required()
                                    ->helperText('Criterio principal de agrupación del reporte'),
                            ]),
                    ])
                    ->collapsible(),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generar_reporte')
                ->label('Generar Reporte')
                ->icon('heroicon-o-document-chart-bar')
                ->color('primary')
                ->action('generarReporte'),
            
            Action::make('exportar_excel')
                ->label('Exportar Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->visible(fn () => $this->reporteData !== null)
                ->action('exportarExcel'),
            
            Action::make('exportar_pdf')
                ->label('Exportar PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->visible(fn () => $this->reporteData !== null)
                ->action('exportarPdf'),
        ];
    }

    public function generarReporte(): void
    {
        $data = $this->form->getState();
        
        // Validar que el período no sea excesivo (más de 1 año)
        $fechaDesde = \Carbon\Carbon::parse($data['fecha_desde']);
        $fechaHasta = \Carbon\Carbon::parse($data['fecha_hasta']);
        $diasDiferencia = $fechaDesde->diffInDays($fechaHasta);
        
        if ($diasDiferencia > 365) {
            Notification::make()
                ->warning()
                ->title('Período muy amplio')
                ->body('El período seleccionado supera 1 año. El procesamiento puede ser lento.')
                ->persistent()
                ->send();
        }

        // Construir consulta base
        $query = OrdenDeCompra::query()
            ->whereBetween('fecha_emision', [$data['fecha_desde'], $data['fecha_hasta']])
            ->whereIn('status', $data['estado_oc']);
        
        // Aplicar filtros opcionales
        if (!empty($data['proveedor_id'])) {
            $query->where('proveedor_id', $data['proveedor_id']);
        }
        
        if (!empty($data['categoria_insumo_id'])) {
            $query->whereHas('items.insumo', function ($q) use ($data) {
                $q->where('categoria_insumo_id', $data['categoria_insumo_id']);
            });
        }

        // Obtener órdenes
        $ordenes = $query->with(['proveedor', 'items.insumo.categoria'])->get();
        
        if ($ordenes->isEmpty()) {
            Notification::make()
                ->warning()
                ->title('Sin resultados')
                ->body('No se encontraron órdenes de compra para los criterios seleccionados.')
                ->send();
            
            $this->reporteData = null;
            return;
        }

        // Calcular métricas generales
        $costoTotal = $ordenes->sum('total_calculado');
        $totalOrdenes = $ordenes->count();
        $insumosUnicos = $ordenes->flatMap(fn($oc) => $oc->items->pluck('insumo_id'))->unique()->count();
        
        // Agrupar según criterio
        $datosAgrupados = $this->agruparDatos($ordenes, $data['agrupar_por']);

        // Guardar datos del reporte
        $this->reporteData = [
            'criterios' => $data,
            'periodo' => [
                'desde' => $fechaDesde->format('d/m/Y'),
                'hasta' => $fechaHasta->format('d/m/Y'),
            ],
            'metricas' => [
                'costo_total' => $costoTotal,
                'total_ordenes' => $totalOrdenes,
                'insumos_unicos' => $insumosUnicos,
                'costo_promedio_orden' => $totalOrdenes > 0 ? $costoTotal / $totalOrdenes : 0,
            ],
            'datos_agrupados' => $datosAgrupados,
            'generado_en' => now()->format('d/m/Y H:i:s'),
        ];

        Notification::make()
            ->success()
            ->title('Reporte generado')
            ->body("Se procesaron {$totalOrdenes} órdenes de compra.")
            ->send();
    }

    protected function agruparDatos($ordenes, string $criterio): array
    {
        $resultado = [];

        switch ($criterio) {
            case 'proveedor':
                $agrupado = $ordenes->groupBy('proveedor_id');
                foreach ($agrupado as $proveedorId => $ordenesProveedor) {
                    $proveedor = $ordenesProveedor->first()->proveedor;
                    $resultado[] = [
                        'nombre' => $proveedor->nombre_empresa,
                        'total_ordenes' => $ordenesProveedor->count(),
                        'costo_total' => $ordenesProveedor->sum('total_calculado'),
                        'costo_promedio' => $ordenesProveedor->avg('total_calculado'),
                    ];
                }
                break;

            case 'categoria':
                $insumosPorCategoria = [];
                foreach ($ordenes as $orden) {
                    foreach ($orden->items as $item) {
                        $categoriaId = $item->insumo->categoria_insumo_id ?? 0;
                        $categoriaNombre = $item->insumo->categoria->nombre ?? 'Sin categoría';
                        
                        if (!isset($insumosPorCategoria[$categoriaId])) {
                            $insumosPorCategoria[$categoriaId] = [
                                'nombre' => $categoriaNombre,
                                'total_ordenes' => 0,
                                'costo_total' => 0,
                            ];
                        }
                        
                        $insumosPorCategoria[$categoriaId]['costo_total'] += $item->precio_unitario * $item->cantidad;
                    }
                }
                
                foreach ($ordenes->groupBy('proveedor_id') as $ordenesProveedor) {
                    foreach ($insumosPorCategoria as $catId => $cat) {
                        $cat['total_ordenes'] = $ordenesProveedor->count();
                    }
                }
                
                $resultado = array_values($insumosPorCategoria);
                break;

            case 'mes':
                $agrupado = $ordenes->groupBy(fn($oc) => \Carbon\Carbon::parse($oc->fecha_emision)->format('Y-m'));
                foreach ($agrupado as $mes => $ordenesMes) {
                    $fecha = \Carbon\Carbon::createFromFormat('Y-m', $mes);
                    $resultado[] = [
                        'nombre' => $fecha->locale('es')->translatedFormat('F Y'),
                        'total_ordenes' => $ordenesMes->count(),
                        'costo_total' => $ordenesMes->sum('total_calculado'),
                        'costo_promedio' => $ordenesMes->avg('total_calculado'),
                    ];
                }
                break;

            case 'insumo':
                $insumosDatos = [];
                foreach ($ordenes as $orden) {
                    foreach ($orden->items as $item) {
                        $insumoId = $item->insumo_id;
                        $insumoNombre = $item->insumo->nombre ?? 'Desconocido';
                        
                        if (!isset($insumosDatos[$insumoId])) {
                            $insumosDatos[$insumoId] = [
                                'nombre' => $insumoNombre,
                                'total_ordenes' => 0,
                                'costo_total' => 0,
                                'cantidad_total' => 0,
                            ];
                        }
                        
                        $insumosDatos[$insumoId]['costo_total'] += $item->precio_unitario * $item->cantidad;
                        $insumosDatos[$insumoId]['cantidad_total'] += $item->cantidad;
                    }
                }
                
                $resultado = array_values($insumosDatos);
                break;
        }

        // Ordenar por costo total descendente
        usort($resultado, fn($a, $b) => $b['costo_total'] <=> $a['costo_total']);

        return $resultado;
    }

    public function exportarExcel(): void
    {
        // TODO: Implementar exportación Excel usando maatwebsite/excel
        Notification::make()
            ->info()
            ->title('Función en desarrollo')
            ->body('La exportación a Excel se implementará próximamente.')
            ->send();
    }

    public function exportarPdf()
    {
        if ($this->reporteData === null) {
            Notification::make()
                ->warning()
                ->title('Sin datos')
                ->body('Debe generar el reporte antes de exportarlo.')
                ->send();
            return;
        }

        // Generar PDF usando dompdf
        $pdf = Pdf::loadView('pdf.reporte-compras', [
            'reporte' => $this->reporteData,
        ]);

        $nombreArchivo = 'reporte-compras-' . now()->format('Y-m-d_His') . '.pdf';

        Notification::make()
            ->success()
            ->title('PDF generado')
            ->body('El reporte se está descargando.')
            ->send();

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $nombreArchivo);
    }
}
