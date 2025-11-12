<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
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
            ->withPivot(['precio', 'unidad_de_compra', 'factor_de_conversion', 'tiempo_entrega_dias'])
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
