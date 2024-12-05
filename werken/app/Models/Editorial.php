<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Editorial extends Model
{
    protected $table = 'V_EDITORIAL';
    protected $primaryKey = 'nro_control';
    public $timestamps = false; 

    protected $fillable = [
        'nro_control',
        'nombre_busqueda',
    ];
}
