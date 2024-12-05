<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Autor extends Model
{
    protected $table = 'V_AUTOR';
    protected $primaryKey = 'nro_control';
    public $timestamps = false;

    protected $fillable = [
        'nro_control',
        'nombre_busqueda',
    ];
}
