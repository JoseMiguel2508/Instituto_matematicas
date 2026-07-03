<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $table = 'CAJA';
    protected $primaryKey = 'id_caja';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'estado'
    ];

    public function sesiones()
    {
        return $this->hasMany(SesionCaja::class, 'id_caja', 'id_caja');
    }
}
