<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Horario extends Model
{
    protected $table = 'HORARIO';
    protected $primaryKey = 'id_horario';
    public $timestamps = false;

    protected $fillable = [
        'id_grupo',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
    ];

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class, 'id_grupo', 'id_grupo');
    }
}
