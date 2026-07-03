<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogActividad extends Model
{
    protected $table = 'LOG_ACTIVIDAD';
    protected $primaryKey = 'id_log';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'accion',
        'tabla_afectada',
        'id_registro_afectado',
        'datos_anteriores',
        'datos_nuevos',
        'fecha_hora',
        'direccion_ip',
        'modulo',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }
}
