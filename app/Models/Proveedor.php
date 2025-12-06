<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory, Auditable;

    protected $table = 'proveedores';

    protected $fillable = [
        'user_id',
        'nombre_empresa',
        'cuit',
        'nombre_contacto',
        'email_pedidos',
        'telefono',
        'direccion',
        'notas',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function insumos()
    {
        return $this->belongsToMany(Insumo::class, 'insumo_proveedor', 'proveedor_id', 'insumo_id')
            ->withPivot(['precio', 'unidad_compra', 'cantidad_por_bulto', 'tiempo_entrega_dias'])
            ->withTimestamps();
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function ordenesDeCompra()
    {
        return $this->hasMany(\App\Models\OrdenDeCompra::class, 'proveedor_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
