<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotaFinal extends Model
{
    protected $table = 'NOTA_FINAL';
    protected $primaryKey = 'id_nota_final';
    public $timestamps = false;

    protected $fillable = [
        'id_detalle_inscripcion',
        'nota',
        'estado',
        'id_usuario_registra',
        'fecha_registro',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_registro' => 'datetime',
        ];
    }

    public function detalleInscripcion(): BelongsTo
    {
        return $this->belongsTo(DetalleInscripcion::class, 'id_detalle_inscripcion', 'id_detalle_inscripcion');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario_registra', 'id_usuario');
    }
}
