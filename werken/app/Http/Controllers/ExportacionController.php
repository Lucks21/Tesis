<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExportacionController extends Controller
{
    public function exportRIS($nroControl)
    {
        // Obtener los datos del recurso
        $recurso = DB::table('V_TITULO as vt')
            ->leftJoin('V_AUTOR as va', 'vt.nro_control', '=', 'va.nro_control')
            ->leftJoin('V_EDITORIAL as ve', 'vt.nro_control', '=', 've.nro_control')
            ->select(
                'vt.nombre_busqueda as titulo',
                'va.nombre_busqueda as autor',
                've.nombre_busqueda as editorial'
            )
            ->where('vt.nro_control', '=', $nroControl)
            ->first();

        if (!$recurso) {
            abort(404);
        }

        // Generar contenido RIS
        $risContent = "TY  - BOOK\r\n";
        $risContent .= "TI  - " . $recurso->titulo . "\r\n";
        $risContent .= "AU  - " . $recurso->autor . "\r\n";
        $risContent .= "PB  - " . $recurso->editorial . "\r\n";
        $risContent .= "ER  - \r\n";

        // Generar respuesta para descarga
        $filename = 'referencia_' . $nroControl . '.ris';
        $headers = [
            'Content-Type' => 'application/x-research-info-systems',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response($risContent, 200, $headers);
    }
}