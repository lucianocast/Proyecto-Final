<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * Los atributos que se pueden asignar en masa.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'active', // 'role' FUE ELIMINADO
        'google_id',
    ];

    /**
     * Los atributos que deben estar ocultos para la serialización.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    /**
     * Scope to only active users
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Método requerido por Filament
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->active;
    }

    // --- MÉTODO isEncargado() ELIMINADO ---

    // Tus relaciones originales (¡importante mantenerlas!)
    public function ordenesDeCompraCreadas()
    {
        return $this->hasMany(\App\Models\OrdenDeCompra::class, 'user_id');
    }

    public function proveedor()
    {
        return $this->hasOne(\App\Models\Proveedor::class, 'user_id');
    }

    public function clienteProfile()
    {
        return $this->hasOne(\App\Models\Cliente::class, 'user_id');
    }
}