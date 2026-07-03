<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetallePago extends Model
{
    protected $table = 'DETALLE_PAGO';
    protected $primaryKey = 'id_detalle_pago';
    public $timestamps = false;

    protected $fillable = [
        'id_pago',
        'id_concepto',
        'id_deuda',
        'monto_aplicado',
        'descripcion',
    ];

    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class, 'id_pago', 'id_pago');
    }

    public function concepto(): BelongsTo
    {
        return $this->belongsTo(ConceptoPago::class, 'id_concepto', 'id_concepto');
    }

    public function deuda(): BelongsTo
    {
        return $this->belongsTo(DeudaEstudiante::class, 'id_deuda', 'id_deuda');
    }
}
