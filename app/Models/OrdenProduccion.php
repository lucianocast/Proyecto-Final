<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenProduccion extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'receta_id',
        'producto_id',
        'user_id',
        'cantidad_a_producir',
        'cantidad_producida',
        'estado',
        'fecha_inicio',
        'fecha_limite',
        'fecha_finalizacion',
        'insumos_estimados',
        'insumos_consumidos',
        'observaciones',
        'costo_total',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_limite' => 'date',
        'fecha_finalizacion' => 'date',
        'insumos_estimados' => 'array',
        'insumos_consumidos' => 'array',
        'costo_total' => 'decimal:2',
        'cantidad_a_producir' => 'integer',
        'cantidad_producida' => 'integer',
    ];

    public function receta()
    {
        return $this->belongsTo(Receta::class, 'receta_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pedidos()
    {
        return $this->belongsToMany(Pedido::class, 'orden_produccion_pedido', 'orden_produccion_id', 'pedido_id')
            ->withTimestamps();
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeEnProceso($query)
    {
        return $query->where('estado', 'en_proceso');
    }

    public function scopeTerminadas($query)
    {
        return $query->where('estado', 'terminada');
    }

    /**
     * Estima los insumos necesarios basándose en la receta
     */
    public function estimarInsumos(): array
    {
        if (!$this->receta) {
            return [];
        }

        $insumosEstimados = [];
        foreach ($this->receta->insumos as $insumo) {
            $insumosEstimados[] = [
                'insumo_id' => $insumo->id,
                'nombre' => $insumo->nombre,
                'cantidad_por_unidad' => $insumo->pivot->cantidad,
                'cantidad_total' => $insumo->pivot->cantidad * $this->cantidad_a_producir,
                'unidad' => $insumo->unidad_de_medida->value,
                'stock_disponible' => $insumo->stock_disponible,
            ];
        }

        return $insumosEstimados;
    }

    /**
     * Verifica si hay stock suficiente
     */
    public function verificarStock(): array
    {
        $insumos = $this->estimarInsumos();
        $faltantes = [];

        foreach ($insumos as $insumo) {
            if ($insumo['stock_disponible'] < $insumo['cantidad_total']) {
                $faltantes[] = [
                    'insumo' => $insumo['nombre'],
                    'requerido' => $insumo['cantidad_total'],
                    'disponible' => $insumo['stock_disponible'],
                    'faltante' => $insumo['cantidad_total'] - $insumo['stock_disponible'],
                    'unidad' => $insumo['unidad'],
                ];
            }
        }

        return $faltantes;
    }
}
