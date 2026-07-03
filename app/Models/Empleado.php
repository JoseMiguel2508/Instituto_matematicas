<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Empleado extends Model
{
    protected $table = 'EMPLEADO';
    protected $primaryKey = 'id_empleado';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_empleado',
        'codigo_empleado',
        'id_cargo',
        'fecha_contratacion',
        'tipo_contrato',
        'estado',
    ];

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'id_empleado', 'id_persona');
    }

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class, 'id_cargo', 'id_cargo');
    }
}
