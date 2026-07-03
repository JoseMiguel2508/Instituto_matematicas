<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Especialidad extends Model
{
    protected $table = 'ESPECIALIDAD';
    protected $primaryKey = 'id_especialidad';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'estado',
    ];

    public function cursos(): HasMany
    {
        return $this->hasMany(Curso::class, 'id_especialidad', 'id_especialidad');
    }
}
