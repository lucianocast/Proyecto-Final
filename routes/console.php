<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ===========================
// PROCESOS INTELIGENTES AUTÓNOMOS
// ===========================

// Proceso #1: Planificación de Compras Inteligente
// Analiza stock crítico y genera órdenes de compra automáticas
// Ejecuta cada 6 horas (4 veces al día) para monitoreo continuo
Schedule::command('inteligente:procesar-compras')
    ->everySixHours()
    ->withoutOverlapping()
    ->runInBackground()
    ->onSuccess(fn () => info('✅ Proceso Inteligente #1 ejecutado correctamente'))
    ->onFailure(fn () => error('❌ Error en Proceso Inteligente #1'));

// Proceso #2: Generación de Promociones Inteligentes
// Detecta días con baja producción y crea promociones automáticas
// Ejecuta diariamente a las 6:00 AM para planificación del día
Schedule::command('inteligente:generar-promociones')
    ->dailyAt('06:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->onSuccess(fn () => info('✅ Proceso Inteligente #2 ejecutado correctamente'))
    ->onFailure(fn () => error('❌ Error en Proceso Inteligente #2'));

// Proceso #3: Análisis Comercial Proactivo
// Analiza rendimiento de productos y ejecuta acciones automáticas
// Ejecuta semanalmente los lunes a las 2:00 AM para análisis semanal
Schedule::command('inteligente:analizar-comercial')
    ->weeklyOn(1, '02:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->onSuccess(fn () => info('✅ Proceso Inteligente #3 ejecutado correctamente'))
    ->onFailure(fn () => error('❌ Error en Proceso Inteligente #3'));
