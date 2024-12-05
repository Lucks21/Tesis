<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Titulo extends Model
{
    protected $table = 'V_TITULO'; 
    protected $primaryKey = 'nro_control';
    public $timestamps = false;

    protected $fillable = [
        'nro_control',
        'nombre_busqueda',
    ];
}
