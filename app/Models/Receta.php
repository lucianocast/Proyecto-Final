<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    use HasFactory, Auditable;

    protected $table = 'recetas';

    protected $fillable = [
        'nombre',
        'producto_id',
        'instrucciones',
        'costo_total_calculado',
        'descripcion',
        'porciones',
        'tiempo_preparacion',
        'activo',
        'rendimiento',
        'categoria',
    ];

    protected $casts = [
        'costo_total_calculado' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function insumos()
    {
        return $this->belongsToMany(Insumo::class, 'insumo_receta', 'receta_id', 'insumo_id')
            ->withPivot(['cantidad'])
            ->withTimestamps();
    }

    public function ordenesProduccion()
    {
        return $this->hasMany(OrdenProduccion::class, 'receta_id');
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Calcula el costo primo de la receta basado en los insumos
     */
    public function calcularCostoPrimo(): float
    {
        $costo = 0;
        foreach ($this->insumos as $insumo) {
            $costo += $insumo->precio_costo * $insumo->pivot->cantidad;
        }
        return round($costo, 2);
    }

    /**
     * Actualiza el costo total calculado
     */
    public function actualizarCosto(): void
    {
        $this->costo_total_calculado = $this->calcularCostoPrimo();
        $this->save();
    }
}
