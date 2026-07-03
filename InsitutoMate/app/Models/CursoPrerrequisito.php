<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CursoPrerrequisito extends Model
{
    protected $table = 'CURSO_PRERREQUISITO';
    protected $primaryKey = 'id_prerequisito';
    public $timestamps = false;

    protected $fillable = [
        'id_curso',
        'id_curso_prerequisito',
        'nota_minima',
        'tipo',
        'condicion',
    ];

    /**
     * The course that REQUIRES this prerequisite.
     */
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'id_curso', 'id_curso');
    }

    /**
     * The course that IS the prerequisite.
     */
    public function cursoPrerrequisito(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'id_curso_prerequisito', 'id_curso');
    }
}
