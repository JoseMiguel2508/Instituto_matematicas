<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConceptoPago extends Model
{
    protected $table = 'CONCEPTO_PAGO';
    protected $primaryKey = 'id_concepto';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nombre',
        'monto_base',
        'tipo',
        'es_obligatorio',
        'estado',
    ];
    protected function casts(): array
    {
        return [
            'es_obligatorio' => 'boolean',
            'monto_base' => 'decimal:2',
        ];
    }
}
