<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    use HasFactory;

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
    ];

    protected $casts = [
        'costo_total_calculado' => 'decimal:2',
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
}
