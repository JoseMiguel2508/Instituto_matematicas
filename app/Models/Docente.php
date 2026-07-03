<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Docente extends Model
{
    protected $table = 'DOCENTE';
    protected $primaryKey = 'id_docente';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_docente',
        'codigo_docente',
        'grado_academico',
        'fecha_contratacion',
        'estado',
    ];

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'id_docente', 'id_persona');
    }

    public function grupos(): HasMany
    {
        return $this->hasMany(Grupo::class, 'id_docente', 'id_docente');
    }
}
