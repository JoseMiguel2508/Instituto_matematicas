<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Matricula extends Model
{
    const ESTADO_ACTIVA = 'ACTIVA';
    const ESTADO_ANULADA = 'ANULADA';
    const TIPO_REGULAR = 'REGULAR';
    const TIPO_EXTEMPORANEA = 'EXTEMPORANEA';

    protected $table = 'MATRICULA';
    protected $primaryKey = 'id_matricula';
    public $timestamps = false;

    protected $casts = [
        'fecha_matricula' => 'datetime',
    ];

    protected $fillable = [
        'id_estudiante',
        'id_periodo',
        'id_especialidad',
        'fecha_matricula',
        'tipo',
        'estado',
        'observaciones',
        'id_usuario_registra',
    ];

    public function scopeActivas($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVA);
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'id_estudiante', 'id_estudiante');
    }

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(PeriodoAcademico::class, 'id_periodo', 'id_periodo');
    }

    public function especialidad(): BelongsTo
    {
        return $this->belongsTo(Especialidad::class, 'id_especialidad', 'id_especialidad');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario_registra', 'id_usuario');
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class, 'id_matricula', 'id_matricula');
    }
}
