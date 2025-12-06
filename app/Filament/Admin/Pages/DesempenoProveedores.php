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
use App\Services\ProveedorPerformanceService;
use App\Models\Proveedor;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * UC-20: Consultar Desempeño de Proveedores
 * 
 * Página para análisis de proveedores con métricas de desempeño:
 * - Cumplimiento de entrega (%)
 * - Precisión de cantidades (%)
 * - Costo promedio
 * - Puntuación global
 */
class DesempenoProveedores extends Page implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static string $view = 'filament.admin.pages.desempeno-proveedores';
    protected static ?string $navigationGroup = 'Compras y Proveedores';
    protected static ?string $navigationLabel = 'Desempeño de Proveedores';
    protected static ?string $title = 'Análisis de Desempeño de Proveedores';
    protected static ?string $slug = 'proveedores/desempeno';
    protected static ?int $navigationSort = 4;

    public ?array $data = [];
    public ?array $analisisData = null;
    public ?int $proveedorSeleccionado = null;

    public function mount(): void
    {
        $this->form->fill([
            'fecha_desde' => now()->subMonths(3)->startOfMonth(),
            'fecha_hasta' => now()->endOfMonth(),
            'criterio_ranking' => 'puntuacion_global',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Criterios de Análisis')
                    ->description('Defina el período y proveedores para analizar el desempeño')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                DatePicker::make('fecha_desde')
                                    ->label('Fecha Desde')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->default(now()->subMonths(3)->startOfMonth()),
                                
                                DatePicker::make('fecha_hasta')
                                    ->label('Fecha Hasta')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->default(now()->endOfMonth()),
                                
                                Select::make('criterio_ranking')
                                    ->label('Criterio de Ranking')
                                    ->options([
                                        'puntuacion_global' => 'Puntuación Global',
                                        'cumplimiento_entrega' => 'Cumplimiento de Entrega',
                                        'precision_cantidades' => 'Precisión de Cantidades',
                                        'costo_promedio_orden' => 'Mejor Costo Promedio',
                                    ])
                                    ->default('puntuacion_global')
                                    ->required()
                                    ->helperText('Define el orden del ranking'),
                            ]),
                        
                        Select::make('proveedores')
                            ->label('Proveedores a Analizar (Opcional)')
                            ->placeholder('Todos los proveedores')
                            ->options(Proveedor::where('activo', true)->pluck('nombre_empresa', 'id'))
                            ->searchable()
                            ->multiple()
                            ->helperText('Deje vacío para incluir todos los proveedores activos'),
                    ])
                    ->collapsible(),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('analizar')
                ->label('Analizar Desempeño')
                ->icon('heroicon-o-chart-bar-square')
                ->color('primary')
                ->action('analizarDesempeno'),
            
            Action::make('exportar_pdf')
                ->label('Exportar PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->visible(fn () => $this->analisisData !== null)
                ->action('exportarPdf'),
        ];
    }

    public function analizarDesempeno(): void
    {
        $data = $this->form->getState();
        
        $service = new ProveedorPerformanceService();
        
        try {
            $resultados = $service->calcularDesempeno(
                $data['fecha_desde'],
                $data['fecha_hasta'],
                $data['proveedores'] ?? null
            );

            if ($resultados->isEmpty()) {
                Notification::make()
                    ->warning()
                    ->title('Sin datos')
                    ->body('No se encontraron órdenes de compra para los proveedores y período seleccionados.')
                    ->send();
                
                $this->analisisData = null;
                return;
            }

            // Generar ranking
            $ranking = $service->generarRanking($resultados, $data['criterio_ranking']);

            // Calcular promedios generales
            $promedios = [
                'cumplimiento_entrega' => $ranking->avg('metricas.cumplimiento_entrega'),
                'precision_cantidades' => $ranking->avg('metricas.precision_cantidades'),
                'costo_promedio_orden' => $ranking->avg('metricas.costo_promedio_orden'),
                'puntuacion_global' => $ranking->avg('metricas.puntuacion_global'),
                'tiempo_promedio_entrega' => $ranking->avg('metricas.tiempo_promedio_entrega_dias'),
            ];

            $this->analisisData = [
                'criterios' => $data,
                'periodo' => [
                    'desde' => \Carbon\Carbon::parse($data['fecha_desde'])->format('d/m/Y'),
                    'hasta' => \Carbon\Carbon::parse($data['fecha_hasta'])->format('d/m/Y'),
                ],
                'ranking' => $ranking->values()->toArray(),
                'promedios' => $promedios,
                'total_proveedores' => $ranking->count(),
                'generado_en' => now()->format('d/m/Y H:i:s'),
            ];

            Notification::make()
                ->success()
                ->title('Análisis completado')
                ->body("Se analizaron {$ranking->count()} proveedor(es).")
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error en el análisis')
                ->body('Ocurrió un error al procesar los datos: ' . $e->getMessage())
                ->send();
        }
    }

    public function verDetalleProveedor(int $proveedorId): void
    {
        $this->proveedorSeleccionado = $proveedorId;
        
        $data = $this->form->getState();
        $service = new ProveedorPerformanceService();
        
        $detalle = $service->obtenerDetalleOrdenes(
            $proveedorId,
            $data['fecha_desde'],
            $data['fecha_hasta']
        );

        Notification::make()
            ->info()
            ->title('Detalle cargado')
            ->body("Mostrando {$detalle->count()} orden(es) del proveedor.")
            ->send();
    }

    public function exportarPdf()
    {
        if ($this->analisisData === null) {
            Notification::make()
                ->warning()
                ->title('Sin datos')
                ->body('Debe generar el análisis antes de exportarlo.')
                ->send();
            return;
        }

        $pdf = Pdf::loadView('pdf.desempeno-proveedores', [
            'analisis' => $this->analisisData,
        ]);

        $nombreArchivo = 'desempeno-proveedores-' . now()->format('Y-m-d_His') . '.pdf';

        Notification::make()
            ->success()
            ->title('PDF generado')
            ->body('El reporte se está descargando.')
            ->send();

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $nombreArchivo);
    }

    public function getClasePuntuacion(float $puntuacion): string
    {
        if ($puntuacion >= 80) return 'success';
        if ($puntuacion >= 60) return 'warning';
        return 'danger';
    }
}
