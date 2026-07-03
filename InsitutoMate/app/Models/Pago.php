<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pago extends Model
{
    const ESTADO_COMPLETADO = 'COMPLETADO';
    const ESTADO_ANULADO = 'ANULADO';
    const ESTADO_PENDIENTE = 'PENDIENTE';

    protected $table = 'PAGO';
    protected $primaryKey = 'id_pago';
    public $timestamps = false;

    protected $casts = [
        'fecha_pago' => 'datetime',
        'monto_total' => 'decimal:2',
    ];

    protected $fillable = [
        'id_estudiante',
        'id_matricula',
        'id_usuario_registra',
        'id_sesion_caja',
        'numero_comprobante',
        'tipo_comprobante',
        'monto_total',
        'fecha_pago',
        'metodo_pago',
        'estado',
        'observaciones',
    ];

    public function scopeCompletados($query)
    {
        return $query->where('estado', self::ESTADO_COMPLETADO);
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'id_estudiante', 'id_estudiante');
    }

    public function matricula(): BelongsTo
    {
        return $this->belongsTo(Matricula::class, 'id_matricula', 'id_matricula');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario_registra', 'id_usuario');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetallePago::class, 'id_pago', 'id_pago');
    }

    public function sesionCaja(): BelongsTo
    {
        return $this->belongsTo(SesionCaja::class, 'id_sesion_caja', 'id_sesion_caja');
    }
}
