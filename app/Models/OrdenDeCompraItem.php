<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenDeCompraItem extends Model
{
    use HasFactory;

    protected $table = 'orden_de_compra_items';

    protected $fillable = [
        'orden_de_compra_id',
        'insumo_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    protected $casts = [
        'cantidad' => 'decimal:4',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function ordenDeCompra()
    {
        return $this->belongsTo(OrdenDeCompra::class, 'orden_de_compra_id');
    }

    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'insumo_id');
    }
}
