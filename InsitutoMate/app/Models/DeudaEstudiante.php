<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeudaEstudiante extends Model
{
    protected $table = 'DEUDA_ESTUDIANTE';
    protected $primaryKey = 'id_deuda';
    public $timestamps = false;

    protected $fillable = [
        'id_estudiante',
        'id_periodo',
        'id_concepto',
        'monto',
        'estado',
        'fecha_generacion',
    ];

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'id_estudiante', 'id_estudiante');
    }

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(PeriodoAcademico::class, 'id_periodo', 'id_periodo');
    }

    public function concepto(): BelongsTo
    {
        return $this->belongsTo(ConceptoPago::class, 'id_concepto', 'id_concepto');
    }

    public function detallesPago(): HasMany
    {
        return $this->hasMany(DetallePago::class, 'id_deuda', 'id_deuda');
    }
}
