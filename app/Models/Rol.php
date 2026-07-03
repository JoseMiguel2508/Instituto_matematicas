<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rol extends Model
{
    protected $table = 'ROL';
    protected $primaryKey = 'id_rol';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'USUARIO_ROL', 'id_rol', 'id_usuario');
    }
}
