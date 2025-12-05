<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    use HasFactory;

    protected $table = 'insumos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria_insumo_id',
        'unidad_de_medida',
        'stock_minimo',
        'ubicacion',
        'activo',
    ];

    protected $casts = [
        'stock_minimo' => 'decimal:4',
        'activo' => 'boolean',
        'unidad_de_medida' => \App\Enums\UnidadMedida::class,
    ];

    public function lotes()
    {
        return $this->hasMany(Lote::class, 'insumo_id');
    }

    public function categoria()
    {
        return $this->belongsTo(CategoriaInsumo::class, 'categoria_insumo_id');
    }

    // Accessor: stock_total
    public function getStockTotalAttribute()
    {
        return $this->lotes()->sum('cantidad_actual');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function proveedores()
    {
        return $this->belongsToMany(Proveedor::class, 'insumo_proveedor', 'insumo_id', 'proveedor_id')
            ->withPivot(['precio', 'unidad_compra', 'cantidad_por_bulto', 'tiempo_entrega_dias'])
            ->withTimestamps();
    }

    public function recetas()
    {
        return $this->belongsToMany(Receta::class, 'insumo_receta', 'insumo_id', 'receta_id')
            ->withPivot(['cantidad'])
            ->withTimestamps();
    }

    public function itemsDeCompra()
    {
        return $this->hasMany(\App\Models\OrdenDeCompraItem::class, 'insumo_id');
    }
}
