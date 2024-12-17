<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleMaterial extends Model
{
    use HasFactory;
    protected $table = 'DETALLE_MATERIAL';
    protected $primaryKey = 'DSM_CORRELATIVO';
    public $timestamps = false;

    protected $fillable = [
        'DSM_CORRELATIVO',
        'DSM_CANTIDAD_ORIGINAL',
        'DSM_FEC_SOLICITUD',
        'DSM_PRIORIDAD',
        'DSM_ISBN_ISSN',
        'DSM_AUTOR_EDITOR',
        'DSM_TITULO',
        'DSM_EDITORIAL',
        'DSM_PUBLICACION',
        'DSM_TIPO_MATERIAL',
        'DSM_IND_SUSCRIPCION',
        'DSM_OBSERVACION',
        'DSM_FECHA',
        'DSM_USUARIO',
        'DSM_ESTACION',
    ];

}
