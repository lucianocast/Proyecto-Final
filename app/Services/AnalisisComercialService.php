<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\Pedido;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Servicio para el Proceso Automatizado Inteligente #3
 * Análisis Proactivo de Actividad Comercial
 * 
 * Funcionalidades:
 * - Revisión periódica de ventas, valoraciones y márgenes por producto
 * - Cálculo dinámico de umbrales de desempeño mínimo
 * - Identificación de productos estrella y de baja rotación
 * - Acciones automáticas: destacar, ocultar, promover
 * - Sugerencias de ajuste de precio para productos poco rentables
 * - Registro completo en historial de auditoría
 */
class AnalisisComercialService
{
    /**
     * Ejecutar análisis completo de actividad comercial
     * Este método se ejecuta automáticamente vía comando programado (semanal)
     */
    public function ejecutarAnalisisComercial(): array
    {
        Log::info('🤖 [Proceso Inteligente #3] Iniciando análisis de actividad comercial');

        $resultados = [
            'productos_analizados' => 0,
            'productos_destacados' => 0,
            'productos_ocultos' => 0,
            'productos_con_promocion' => 0,
            'sugerencias_precio' => 0,
            'acciones' => [],
            'errores' => [],
        ];

        try {
            $productos = Producto::where('activo', true)->with('receta')->get();
            $resultados['productos_analizados'] = $productos->count();

            foreach ($productos as $producto) {
                try {
                    // 1. Calcular métricas de desempeño
                    $metricas = $this->calcularMetricasProducto($producto);

                    // 2. Calcular umbrales dinámicos por categoría
                    $umbrales = $this->calcularUmbralesDinamicos($producto);

                    // 3. Analizar desempeño y tomar acciones
                    $accion = $this->analizarYActuar($producto, $metricas, $umbrales);

                    if ($accion) {
                        $resultados['acciones'][] = $accion;
                        
                        // Contabilizar por tipo
                        switch ($accion['tipo']) {
                            case 'destacar':
                                $resultados['productos_destacados']++;
                                break;
                            case 'ocultar':
                                $resultados['productos_ocultos']++;
                                break;
                            case 'promocionar':
                                $resultados['productos_con_promocion']++;
                                break;
                            case 'sugerencia_precio':
                                $resultados['sugerencias_precio']++;
                                break;
                        }
                    }

                } catch (\Exception $e) {
                    $resultados['errores'][] = "Error analizando producto {$producto->nombre}: {$e->getMessage()}";
                    Log::error("Error analizando producto {$producto->id}", ['error' => $e->getMessage()]);
                }
            }

            // 4. Registrar en auditoría
            $this->registrarEnAuditoria($resultados);

            Log::info('✅ [Proceso Inteligente #3] Análisis completado', $resultados);

        } catch (\Exception $e) {
            Log::error('❌ [Proceso Inteligente #3] Error crítico', ['error' => $e->getMessage()]);
            $resultados['errores'][] = "Error crítico: {$e->getMessage()}";
        }

        return $resultados;
    }

    /**
     * Calcular métricas de desempeño de un producto
     */
    protected function calcularMetricasProducto(Producto $producto): array
    {
        $ultimos30Dias = now()->subDays(30);

        // 1. Ventas en últimos 30 días
        $ventas = DB::table('pedido_items')
            ->join('pedidos', 'pedido_items.pedido_id', '=', 'pedidos.id')
            ->where('pedido_items.producto_id', $producto->id)
            ->where('pedidos.created_at', '>=', $ultimos30Dias)
            ->whereIn('pedidos.status', ['entregado', 'completado'])
            ->sum('pedido_items.cantidad');

        // 2. Ingresos generados
        $ingresos = DB::table('pedido_items')
            ->join('pedidos', 'pedido_items.pedido_id', '=', 'pedidos.id')
            ->where('pedido_items.producto_id', $producto->id)
            ->where('pedidos.created_at', '>=', $ultimos30Dias)
            ->whereIn('pedidos.status', ['entregado', 'completado'])
            ->sum('pedido_items.subtotal');

        // 3. Margen de utilidad (precio venta - costo primo)
        $precioVenta = $producto->precio_venta ?? 0;
        $costoPrimo = $producto->receta ? $producto->receta->costo_primo : 0;
        $margen = $precioVenta > 0 ? (($precioVenta - $costoPrimo) / $precioVenta) * 100 : 0;

        // 4. Frecuencia de pedidos (cuántos pedidos diferentes lo incluyeron)
        $frecuencia = DB::table('pedido_items')
            ->join('pedidos', 'pedido_items.pedido_id', '=', 'pedidos.id')
            ->where('pedido_items.producto_id', $producto->id)
            ->where('pedidos.created_at', '>=', $ultimos30Dias)
            ->whereIn('pedidos.status', ['entregado', 'completado'])
            ->distinct('pedidos.id')
            ->count('pedidos.id');

        return [
            'ventas_unidades' => $ventas,
            'ingresos_totales' => $ingresos,
            'margen_porcentaje' => $margen,
            'frecuencia_pedidos' => $frecuencia,
            'precio_venta' => $precioVenta,
            'costo_primo' => $costoPrimo,
        ];
    }

    /**
     * Calcular umbrales dinámicos por categoría
     */
    protected function calcularUmbralesDinamicos(Producto $producto): array
    {
        // Promedios de la categoría del producto (últimos 30 días)
        $categoriaId = $producto->categoria_producto_id;

        $promedioVentas = DB::table('pedido_items')
            ->join('productos', 'pedido_items.producto_id', '=', 'productos.id')
            ->join('pedidos', 'pedido_items.pedido_id', '=', 'pedidos.id')
            ->where('productos.categoria_producto_id', $categoriaId)
            ->where('pedidos.created_at', '>=', now()->subDays(30))
            ->whereIn('pedidos.status', ['entregado', 'completado'])
            ->avg('pedido_items.cantidad') ?? 5;

        return [
            'ventas_minimas' => $promedioVentas * 0.3, // 30% del promedio
            'ventas_altas' => $promedioVentas * 2,      // 200% del promedio
            'margen_minimo' => 25, // 25% margen mínimo aceptable
            'frecuencia_minima' => 3, // Al menos 3 pedidos en el mes
        ];
    }

    /**
     * Analizar desempeño y ejecutar acciones automáticas
     */
    protected function analizarYActuar(Producto $producto, array $metricas, array $umbrales): ?array
    {
        // ACCIÓN 1: Destacar productos estrella (alto rendimiento)
        if ($metricas['ventas_unidades'] >= $umbrales['ventas_altas'] && 
            $metricas['margen_porcentaje'] >= $umbrales['margen_minimo']) {
            
            $this->destacarProducto($producto);
            
            return [
                'tipo' => 'destacar',
                'producto' => $producto->nombre,
                'razon' => "Alto rendimiento: {$metricas['ventas_unidades']} unidades vendidas con {$metricas['margen_porcentaje']}% de margen",
                'metricas' => $metricas,
            ];
        }

        // ACCIÓN 2: Ocultar productos de baja rotación
        if ($metricas['ventas_unidades'] < $umbrales['ventas_minimas'] && 
            $metricas['frecuencia_pedidos'] < $umbrales['frecuencia_minima']) {
            
            $this->ocultarProducto($producto);
            
            return [
                'tipo' => 'ocultar',
                'producto' => $producto->nombre,
                'razon' => "Baja rotación: solo {$metricas['ventas_unidades']} unidades en {$metricas['frecuencia_pedidos']} pedidos",
                'metricas' => $metricas,
            ];
        }

        // ACCIÓN 3: Activar promoción para mejorar rotación
        if ($metricas['ventas_unidades'] < $umbrales['ventas_minimas'] * 1.5 && 
            $metricas['margen_porcentaje'] >= 30) { // Tiene margen para descontar
            
            $this->crearPromocionMejora($producto, $metricas);
            
            return [
                'tipo' => 'promocionar',
                'producto' => $producto->nombre,
                'razon' => "Rotación moderada con margen suficiente para promoción",
                'metricas' => $metricas,
            ];
        }

        // ACCIÓN 4: Sugerencia de ajuste de precio (margen insuficiente)
        if ($metricas['margen_porcentaje'] < $umbrales['margen_minimo']) {
            
            $this->generarSugerenciaPrecio($producto, $metricas, $umbrales);
            
            return [
                'tipo' => 'sugerencia_precio',
                'producto' => $producto->nombre,
                'razon' => "Margen insuficiente: {$metricas['margen_porcentaje']}% (mínimo {$umbrales['margen_minimo']}%)",
                'metricas' => $metricas,
                'sugerencia' => $this->calcularPrecioSugerido($producto, $umbrales['margen_minimo']),
            ];
        }

        return null; // Producto con desempeño normal, sin acciones
    }

    /**
     * Destacar producto en catálogo
     */
    protected function destacarProducto(Producto $producto): void
    {
        $producto->update(['destacado' => true]);

        Log::info("⭐ Producto destacado automáticamente: {$producto->nombre}");
    }

    /**
     * Ocultar producto temporalmente
     */
    protected function ocultarProducto(Producto $producto): void
    {
        $producto->update(['visible_catalogo' => false]);

        Log::info("👁️ Producto ocultado automáticamente: {$producto->nombre}");
    }

    /**
     * Crear promoción automática para mejorar rotación
     */
    protected function crearPromocionMejora(Producto $producto, array $metricas): void
    {
        try {
            $promocion = Promocion::create([
                'nombre' => "Oferta Especial - {$producto->nombre}",
                'descripcion' => "Promoción automática para mejorar rotación del producto",
                'tipo_descuento' => 'porcentaje',
                'valor_descuento' => 15, // 15% descuento
                'fecha_inicio' => now(),
                'fecha_fin' => now()->addDays(7),
                'activo' => true,
                'condiciones' => json_encode([
                    'tipo' => 'mejora_rotacion',
                    'ventas_actuales' => $metricas['ventas_unidades'],
                    'objetivo' => 'aumentar_frecuencia',
                ]),
                'generada_automaticamente' => true,
            ]);

            $promocion->productos()->attach($producto->id);

            Log::info("🎁 Promoción automática creada para: {$producto->nombre}");

        } catch (\Exception $e) {
            Log::error("Error creando promoción para {$producto->nombre}", ['error' => $e->getMessage()]);
        }
    }

    /**
     * Generar sugerencia de ajuste de precio
     */
    protected function generarSugerenciaPrecio(Producto $producto, array $metricas, array $umbrales): void
    {
        $precioSugerido = $this->calcularPrecioSugerido($producto, $umbrales['margen_minimo']);

        AuditLog::create([
            'user_id' => 1,
            'action' => 'sugerencia_ajuste_precio',
            'auditable_type' => Producto::class,
            'auditable_id' => $producto->id,
            'old_values' => [
                'precio_actual' => $metricas['precio_venta'],
                'costo_primo' => $metricas['costo_primo'],
                'margen_actual' => $metricas['margen_porcentaje'],
            ],
            'new_values' => [
                'precio_sugerido' => $precioSugerido,
                'margen_objetivo' => $umbrales['margen_minimo'],
            ],
            'justification' => "El sistema detectó un margen de utilidad insuficiente ({$metricas['margen_porcentaje']}%). Se sugiere revisar el precio de venta para alcanzar un margen mínimo de {$umbrales['margen_minimo']}%.",
        ]);

        Log::warning("💰 Sugerencia de precio para {$producto->nombre}: \${$precioSugerido} (actual: \${$metricas['precio_venta']})");
    }

    /**
     * Calcular precio sugerido para alcanzar margen objetivo
     */
    protected function calcularPrecioSugerido(Producto $producto, float $margenObjetivo): float
    {
        $costoPrimo = $producto->receta ? $producto->receta->costo_primo : 0;

        if ($costoPrimo == 0) {
            return $producto->precio_venta ?? 0;
        }

        // Precio = Costo / (1 - Margen%)
        $precioSugerido = $costoPrimo / (1 - ($margenObjetivo / 100));

        // Redondear a múltiplo de 50 (ej: 1250, 1300, 1350)
        return ceil($precioSugerido / 50) * 50;
    }

    /**
     * Registrar resumen en auditoría
     */
    protected function registrarEnAuditoria(array $resultados): void
    {
        AuditLog::create([
            'user_id' => 1,
            'action' => 'proceso_automatico_analisis_comercial',
            'auditable_type' => null,
            'auditable_id' => null,
            'old_values' => null,
            'new_values' => $resultados,
            'justification' => 'Ejecución automática del proceso inteligente de análisis comercial',
        ]);
    }
}
