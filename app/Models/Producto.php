<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Producto extends Model
{
    use HasFactory, Auditable;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria_producto_id',
        'precio_venta',
        'activo',
        'imagen_url',
        'tamaños',
        'etiquetas',
        'visible_en_catalogo',
    ];

    protected $casts = [
        'precio_venta' => 'decimal:2',
        'activo' => 'boolean',
        'visible_en_catalogo' => 'boolean',
        'tamaños' => 'array',
        'etiquetas' => 'array',
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaProducto::class, 'categoria_producto_id');
    }

    public function receta()
    {
        return $this->hasOne(Receta::class, 'producto_id');
    }

    public function variantes()
    {
        return $this->hasMany(ProductoVariante::class, 'producto_id')->orderBy('orden');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeVisibleEnCatalogo($query)
    {
        return $query->where('visible_en_catalogo', true);
    }
}
