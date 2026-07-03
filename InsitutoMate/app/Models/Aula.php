<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    protected $table = 'AULA';
    protected $primaryKey = 'id_aula';
    public $timestamps = false;

    protected $fillable = [
        'codigo_aula',
        'capacidad',
        'ubicacion',
        'tipo',
        'estado',
    ];
}
