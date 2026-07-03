<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cargo extends Model
{
    protected $table = 'CARGO';
    protected $primaryKey = 'id_cargo';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'nivel_jerarquico',
    ];

    public function empleados(): HasMany
    {
        return $this->hasMany(Empleado::class, 'id_cargo', 'id_cargo');
    }
}
