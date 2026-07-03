<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DetalleInscripcion extends Model
{
    protected $table = 'DETALLE_INSCRIPCION';
    protected $primaryKey = 'id_detalle_inscripcion';
    public $timestamps = false;

    protected $fillable = [
        'id_inscripcion',
        'id_grupo',
        'estado',
    ];

    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class, 'id_inscripcion', 'id_inscripcion');
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class, 'id_grupo', 'id_grupo');
    }

    public function notaFinal(): HasOne
    {
        return $this->hasOne(NotaFinal::class, 'id_detalle_inscripcion', 'id_detalle_inscripcion');
    }
}
