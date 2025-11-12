<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    use HasFactory;

    protected $table = 'lotes';

    protected $fillable = [
        'insumo_id',
        'cantidad_inicial',
        'cantidad_actual',
        'fecha_vencimiento',
        'codigo_lote',
    ];

    protected $casts = [
        'cantidad_inicial' => 'decimal:4',
        'cantidad_actual' => 'decimal:4',
        'fecha_vencimiento' => 'date',
    ];

    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'insumo_id');
    }
}
