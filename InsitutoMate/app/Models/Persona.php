<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Persona extends Model
{
    protected $table = 'PERSONA';
    protected $primaryKey = 'id_persona';
    public $timestamps = false;

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    protected $fillable = [
        'tipo_documento',
        'numero_documento',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'direccion',
        'telefono',
        'email',
    ];

    public function estudiante(): HasOne
    {
        return $this->hasOne(Estudiante::class, 'id_estudiante', 'id_persona');
    }

    public function docente(): HasOne
    {
        return $this->hasOne(Docente::class, 'id_docente', 'id_persona');
    }

    public function empleado(): HasOne
    {
        return $this->hasOne(Empleado::class, 'id_empleado', 'id_persona');
    }

    public function usuario(): HasOne
    {
        return $this->hasOne(User::class, 'id_persona', 'id_persona');
    }

    public function getNombreCompletoAttribute()
    {
        return "{$this->nombres} {$this->apellidos}";
    }
}
