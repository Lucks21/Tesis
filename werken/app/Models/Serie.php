<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    protected $table = 'V_SERIE';
    protected $primaryKey = 'nro_control';
    public $timestamps = false; // Asumimos que la vista no tiene timestamps

    protected $fillable = [
        'nro_control',
        'nombre_busqueda',
    ];
}
