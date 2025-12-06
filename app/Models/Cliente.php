<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'direccion',
        'user_id',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'cliente_id');
    }

    // Scope para clientes activos
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
