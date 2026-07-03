<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodoAcademico extends Model
{
    const ESTADO_ACTIVO = 'ACTIVO';
    const ESTADO_INACTIVO = 'INACTIVO';

    protected $table = 'PERIODO_ACADEMICO';
    protected $primaryKey = 'id_periodo';
    public $timestamps = false;

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    protected $fillable = [
        'codigo',
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'tipo',
        'estado',
    ];

    public function scopeActivos($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVO);
    }

    public function grupos(): HasMany
    {
        return $this->hasMany(Grupo::class, 'id_periodo', 'id_periodo');
    }
}
