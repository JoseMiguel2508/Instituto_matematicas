<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesionCaja extends Model
{
    const ESTADO_ABIERTA = 'ABIERTA';
    const ESTADO_CERRADA = 'CERRADA';

    protected $table = 'SESION_CAJA';
    protected $primaryKey = 'id_sesion_caja';
    public $timestamps = true;

    protected $casts = [
        'monto_inicial' => 'decimal:2',
        'monto_final_esperado' => 'decimal:2',
        'monto_final_real' => 'decimal:2',
        'diferencia' => 'decimal:2',
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    protected $fillable = [
        'id_caja',
        'id_usuario_apertura',
        'id_usuario_cierre',
        'monto_inicial',
        'monto_final_esperado',
        'monto_final_real',
        'diferencia',
        'fecha_apertura',
        'fecha_cierre',
        'observaciones_apertura',
        'observaciones_cierre',
        'estado'
    ];

    public function scopeAbiertas($query)
    {
        return $query->where('estado', self::ESTADO_ABIERTA);
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'id_caja', 'id_caja');
    }

    public function usuarioApertura()
    {
        return $this->belongsTo(User::class, 'id_usuario_apertura', 'id_usuario');
    }

    public function usuarioCierre()
    {
        return $this->belongsTo(User::class, 'id_usuario_cierre', 'id_usuario');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_sesion_caja', 'id_sesion_caja');
    }
}
