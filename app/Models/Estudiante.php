<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estudiante extends Model
{
    const ESTADO_ACTIVO = 'ACTIVO';
    const ESTADO_INACTIVO = 'INACTIVO';
    const ESTADO_RETIRADO = 'RETIRADO';

    protected $table = 'ESTUDIANTE';
    protected $primaryKey = 'id_estudiante';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'fecha_ingreso' => 'date',
    ];

    protected $fillable = [
        'id_estudiante',
        'codigo_estudiante',
        'fecha_ingreso',
        'estado',
    ];

    public function scopeActivos($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVO);
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'id_estudiante', 'id_persona');
    }

    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class, 'id_estudiante', 'id_estudiante');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'id_estudiante', 'id_estudiante');
    }
}
