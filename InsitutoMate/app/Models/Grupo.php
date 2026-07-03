<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grupo extends Model
{
    protected $table = 'GRUPO';
    protected $primaryKey = 'id_grupo';
    public $timestamps = false;

    protected $fillable = [
        'id_curso',
        'id_docente',
        'id_aula',
        'id_periodo',
        'numero_grupo',
        'cupo_maximo',
        'estado',
    ];

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'id_curso', 'id_curso');
    }

    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class, 'id_docente', 'id_docente');
    }

    public function aula(): BelongsTo
    {
        return $this->belongsTo(Aula::class, 'id_aula', 'id_aula');
    }

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(PeriodoAcademico::class, 'id_periodo', 'id_periodo');
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class, 'id_grupo', 'id_grupo');
    }
}
