<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoVariante extends Model
{
    use HasFactory;

    protected $fillable = [
        'producto_id',
        'descripcion',
        'precio',
        'orden',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'orden' => 'integer',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
