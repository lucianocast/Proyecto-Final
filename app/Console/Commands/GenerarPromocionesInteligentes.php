<?php

namespace App\Console\Commands;

use App\Services\PromocionesInteligentesService;
use Illuminate\Console\Command;

class GenerarPromocionesInteligentes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inteligente:generar-promociones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '🤖 Proceso Inteligente #2: Detectar días con baja producción y generar promociones automáticas';

    /**
     * Execute the console command.
     */
    public function handle(PromocionesInteligentesService $service)
    {
        $this->info('🤖 [Proceso Inteligente #2] Iniciando análisis de días con baja producción...');
        $this->newLine();

        $resultados = $service->ejecutarAnalisisPromociones();

        $this->info("✅ Análisis completado:");
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Días analizados', $resultados['dias_analizados']],
                ['Días con baja producción', $resultados['dias_baja_produccion']],
                ['Promociones creadas', $resultados['promociones_activadas']],
            ]
        );

        if ($resultados['promociones_activadas'] > 0) {
            $this->newLine();
            $this->info('🎉 Promociones creadas:');
            $this->table(
                ['ID', 'Descuento', 'Fecha', 'Productos'],
                collect($resultados['promociones_creadas'])->map(fn($promo) => [
                    $promo['promocion_id'],
                    $promo['descuento'] . '%',
                    $promo['fecha'],
                    count($promo['productos']) . ' productos',
                ])->toArray()
            );
        } else {
            $this->newLine();
            $this->comment('📈 No se detectaron días con baja producción en los próximos 15 días.');
        }

        return Command::SUCCESS;
    }
}
