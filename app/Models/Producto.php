<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria_producto_id',
        'precio_venta',
        'activo',
    ];

    protected $casts = [
        'precio_venta' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaProducto::class, 'categoria_producto_id');
    }

    public function receta()
    {
        return $this->hasOne(Receta::class, 'producto_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
