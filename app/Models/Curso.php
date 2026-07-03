<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curso extends Model
{
    const ESTADO_ACTIVO = 'ACTIVO';
    const ESTADO_INACTIVO = 'INACTIVO';

    protected $table = 'CURSO';
    protected $primaryKey = 'id_curso';
    public $timestamps = false;

    protected $fillable = [
        'id_especialidad',
        'id_nivel',
        'codigo_curso',
        'nombre_curso',
        'creditos',
        'duracion_horas',
        'estado',
    ];

    public function scopeActivos($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVO);
    }

    public function especialidad(): BelongsTo
    {
        return $this->belongsTo(Especialidad::class, 'id_especialidad', 'id_especialidad');
    }

    public function nivel(): BelongsTo
    {
        return $this->belongsTo(Nivel::class, 'id_nivel', 'id_nivel');
    }

    public function grupos(): HasMany
    {
        return $this->hasMany(Grupo::class, 'id_curso', 'id_curso');
    }

    /**
     * Prerrequisitos que este curso exige.
     */
    public function prerrequisitos(): HasMany
    {
        return $this->hasMany(CursoPrerrequisito::class, 'id_curso', 'id_curso');
    }
}
