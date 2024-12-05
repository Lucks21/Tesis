<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Titulo extends Model
{
    protected $table = 'V_TITULO'; // Nombre de la vista
    protected $primaryKey = 'nro_control'; // Llave primaria, si aplica
    public $timestamps = false; // Asumimos que la vista no tiene timestamps

    protected $fillable = [
        'nro_control',
        'nombre_busqueda', // Reemplaza con los nombres exactos de las columnas en la vista
    ];
}
