<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inscripcion extends Model
{
    protected $table = 'INSCRIPCION';
    protected $primaryKey = 'id_inscripcion';
    public $timestamps = false;

    protected $fillable = [
        'id_matricula',
        'fecha_inscripcion',
        'estado',
        'id_usuario_registra',
    ];

    public function matricula(): BelongsTo
    {
        return $this->belongsTo(Matricula::class, 'id_matricula', 'id_matricula');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleInscripcion::class, 'id_inscripcion', 'id_inscripcion');
    }
}
