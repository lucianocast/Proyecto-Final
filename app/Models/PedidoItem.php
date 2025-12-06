<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id',
        'producto_variante_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function productoVariante()
    {
        return $this->belongsTo(ProductoVariante::class, 'producto_variante_id');
    }

    /**
     * Relación directa con Producto a través de ProductoVariante
     * Útil para simplificar queries y eager loading
     */
    public function producto()
    {
        return $this->hasOneThrough(
            Producto::class,
            ProductoVariante::class,
            'id', // Foreign key en producto_variantes
            'id', // Foreign key en productos
            'producto_variante_id', // Local key en pedido_items
            'producto_id' // Local key en producto_variantes
        );
    }
}
