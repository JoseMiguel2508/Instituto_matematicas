<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const ESTADO_ACTIVO = 'ACTIVO';
    const ESTADO_INACTIVO = 'INACTIVO';
    const ESTADO_BLOQUEADO = 'BLOQUEADO';

    protected $table = 'USUARIO';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $casts = [
        'ultimo_acceso' => 'datetime',
    ];

    protected $fillable = [
        'id_persona',
        'username',
        'password_hash',
        'estado',
        'ultimo_acceso',
        'intentos_fallidos',
    ];

    public function scopeActivos($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVO);
    }

    protected $hidden = [
        'password_hash',
    ];

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Relationship with Persona
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'id_persona', 'id_persona');
    }

    /**
     * Relationship with Rol
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Rol::class, 'USUARIO_ROL', 'id_usuario', 'id_rol', 'id_usuario', 'id_rol');
    }

    /**
     * Check if user has a specific role name
     */
    public function hasRole($roleName)
    {
        return $this->roles()->where('nombre', $roleName)->exists();
    }

    /**
     * Check if user has any of the given role names
     */
    public function hasAnyRole(array $roleNames): bool
    {
        return $this->roles()->whereIn('nombre', $roleNames)->exists();
    }
}
