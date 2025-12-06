<?php

namespace App\Console\Commands;

use App\Services\AnalisisComercialService;
use Illuminate\Console\Command;

class AnalizarActividadComercial extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inteligente:analizar-comercial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '🤖 Proceso Inteligente #3: Analizar actividad comercial y ejecutar acciones automáticas';

    /**
     * Execute the console command.
     */
    public function handle(AnalisisComercialService $service)
    {
        $this->info('🤖 [Proceso Inteligente #3] Iniciando análisis proactivo de actividad comercial...');
        $this->newLine();

        $resultados = $service->ejecutarAnalisisComercial();

        $this->info("✅ Análisis completado:");
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Productos analizados', $resultados['productos_analizados']],
                ['Acciones ejecutadas', count($resultados['acciones'])],
                ['Productos destacados', $resultados['productos_destacados']],
                ['Productos ocultos', $resultados['productos_ocultos']],
                ['Promociones creadas', $resultados['productos_con_promocion']],
                ['Sugerencias de precio', $resultados['sugerencias_precio']],
            ]
        );

        if (count($resultados['acciones']) > 0) {
            $this->newLine();
            $this->info('📊 Desglose de acciones automáticas:');
            
            if ($resultados['productos_destacados'] > 0) {
                $this->line("• ⭐ {$resultados['productos_destacados']} productos destacados (alto rendimiento)");
            }
            
            if ($resultados['productos_ocultos'] > 0) {
                $this->line("• 🚫 {$resultados['productos_ocultos']} productos ocultos del catálogo (baja rotación)");
            }
            
            if ($resultados['productos_con_promocion'] > 0) {
                $this->line("• 🎉 {$resultados['productos_con_promocion']} promociones creadas (mejora de ventas)");
            }
            
            if ($resultados['sugerencias_precio'] > 0) {
                $this->line("• 💰 {$resultados['sugerencias_precio']} sugerencias de ajuste de precio (margen bajo)");
            }
        } else {
            $this->newLine();
            $this->comment('👍 Todos los productos tienen buen desempeño, no se requieren acciones.');
        }

        return Command::SUCCESS;
    }
}
