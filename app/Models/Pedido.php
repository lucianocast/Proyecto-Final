<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'cliente_id',
        'user_id',
        'status',
        'fecha_entrega',
        'forma_entrega',
        'direccion_envio',
        'metodo_pago',
        'monto_abonado',
        'total',
        'total_calculado',
        'saldo_pendiente',
        'observaciones',
    ];

    protected $casts = [
        'fecha_entrega' => 'datetime',
        'monto_abonado' => 'decimal:2',
        'total' => 'decimal:2',
        'total_calculado' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(PedidoItem::class, 'pedido_id');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'pedido_id');
    }

    public function ordenesProduccion()
    {
        return $this->belongsToMany(OrdenProduccion::class, 'orden_produccion_pedido', 'pedido_id', 'orden_produccion_id')
            ->withTimestamps();
    }
}
