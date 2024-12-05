<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $table = 'V_MATERIA';
    protected $primaryKey = 'nro_control';
    public $timestamps = false;

    protected $fillable = [
        'nro_control',
        'nombre_busqueda',
    ];
    public function titulos()
    {
        return $this->hasMany(Titulo::class, 'nro_control', 'nro_control');
    }
    
}
