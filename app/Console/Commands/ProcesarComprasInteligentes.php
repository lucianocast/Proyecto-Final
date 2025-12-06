<?php

namespace App\Console\Commands;

use App\Services\PlanificacionComprasService;
use Illuminate\Console\Command;

class ProcesarComprasInteligentes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inteligente:procesar-compras';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '🤖 Proceso Inteligente #1: Analizar stock crítico y generar órdenes de compra automáticas';

    /**
     * Execute the console command.
     */
    public function handle(PlanificacionComprasService $service)
    {
        $this->info('🤖 [Proceso Inteligente #1] Iniciando análisis de planificación de compras...');
        $this->newLine();

        $resultados = $service->ejecutarAnalisisAutomatico();

        $this->info("✅ Análisis completado:");
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Insumos analizados', $resultados['insumos_analizados']],
                ['Insumos en nivel crítico', $resultados['insumos_criticos']],
                ['Órdenes de compra generadas', $resultados['ordenes_generadas']],
                ['Errores encontrados', count($resultados['errores'])],
            ]
        );

        if ($resultados['ordenes_generadas'] > 0) {
            $this->newLine();
            $this->info('📋 Órdenes de compra creadas:');
            $this->table(
                ['ID', 'Insumo', 'Cantidad', 'Proveedor', 'Total'],
                collect($resultados['ordenes_creadas'])->map(fn($oc) => [
                    $oc['orden_id'],
                    $oc['insumo'],
                    $oc['cantidad'],
                    $oc['proveedor'],
                    '$' . number_format($oc['total'], 2),
                ])->toArray()
            );
        }

        if (!empty($resultados['errores'])) {
            $this->newLine();
            $this->error('❌ Errores:');
            foreach ($resultados['errores'] as $error) {
                $this->line("  • {$error}");
            }
        }

        $this->newLine();
        $this->comment('📧 Notificaciones enviadas vía Mailtrap a los administradores.');

        return Command::SUCCESS;
    }
}
