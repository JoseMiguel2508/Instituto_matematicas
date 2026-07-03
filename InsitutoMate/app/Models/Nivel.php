<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nivel extends Model
{
    protected $table = 'NIVEL';
    protected $primaryKey = 'id_nivel';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'orden',
        'descripcion',
    ];

    public function cursos(): HasMany
    {
        return $this->hasMany(Curso::class, 'id_nivel', 'id_nivel');
    }
}
