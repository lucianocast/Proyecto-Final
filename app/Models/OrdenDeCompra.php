<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenDeCompra extends Model
{
    use HasFactory, Auditable;

    protected $table = 'ordenes_de_compra';

    protected $fillable = [
        'proveedor_id',
        'user_id',
        'status',
        'fecha_emision',
        'fecha_entrega_esperada',
        'total_calculado',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_entrega_esperada' => 'date',
        'total_calculado' => 'decimal:2',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(OrdenDeCompraItem::class, 'orden_de_compra_id');
    }
}
