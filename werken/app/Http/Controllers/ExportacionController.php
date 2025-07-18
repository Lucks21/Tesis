<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExportacionController extends Controller
{
    public function exportRIS($nroControl)
    {        // Obtener los datos del recurso
        $recurso = DB::table('V_TITULO as vt')
            ->leftJoin('V_AUTOR as va', 'vt.nro_control', '=', 'va.nro_control')
            ->leftJoin('V_EDITORIAL as ve', 'vt.nro_control', '=', 've.nro_control')
            ->leftJoin('V_IDIOMA as vi', 'vt.nro_control', '=', 'vi.nro_control')
            ->select(
                'vt.nombre_busqueda as titulo',
                'va.nombre_busqueda as autor',
                've.nombre_busqueda as editorial',
                'vi.nombre_busqueda as idioma'
            )
            ->where('vt.nro_control', '=', $nroControl)
            ->first();

        if (!$recurso) {
            abort(404);
        }        // Generar contenido RIS
        $risContent = "TY  - BOOK\r\n"; //tengo que buscar aun donde encontrar el tipo de recurso
        $risContent .= "TI  - " . $recurso->titulo . "\r\n";
        $risContent .= "AU  - " . $recurso->autor . "\r\n";
        $risContent .= "PB  - " . $recurso->editorial . "\r\n";
        $risContent .= "LA  - " . $recurso->idioma . "\r\n";
        $risContent .= "ER  - \r\n"; // End of record marker    

        // Generar respuesta para descarga
        $filename = 'referencia_' . $nroControl . '.ris';
        $headers = [
            'Content-Type' => 'application/x-research-info-systems',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response($risContent, 200, $headers);
    }

    public function exportMultipleRIS(Request $request)
    {
        $nroControles = $request->input('nro_controles', []);
        
        if (empty($nroControles)) {
            return response()->json(['error' => 'No se seleccionaron recursos para exportar'], 400);
        }

        // Crear un archivo temporal para el ZIP
        $zipFileName = 'referencias_' . date('Y-m-d_H-i-s') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);
        
        // Crear directorio temporal si no existe
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        // Crear el archivo ZIP
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) !== TRUE) {
            return response()->json(['error' => 'No se pudo crear el archivo ZIP'], 500);
        }

        // Generar un archivo RIS por cada recurso seleccionado
        foreach ($nroControles as $nroControl) {
            $recurso = DB::table('V_TITULO as vt')
                ->leftJoin('V_AUTOR as va', 'vt.nro_control', '=', 'va.nro_control')
                ->leftJoin('V_EDITORIAL as ve', 'vt.nro_control', '=', 've.nro_control')
                ->leftJoin('V_IDIOMA as vi', 'vt.nro_control', '=', 'vi.nro_control')
                ->select(
                    'vt.nombre_busqueda as titulo',
                    'va.nombre_busqueda as autor',
                    've.nombre_busqueda as editorial',
                    'vi.nombre_busqueda as idioma'
                )
                ->where('vt.nro_control', '=', $nroControl)
                ->first();

            if ($recurso) {
                // Generar contenido RIS
                $risContent = "TY  - BOOK\r\n";
                $risContent .= "TI  - " . $recurso->titulo . "\r\n";
                $risContent .= "AU  - " . $recurso->autor . "\r\n";
                $risContent .= "PB  - " . $recurso->editorial . "\r\n";
                $risContent .= "LA  - " . $recurso->idioma . "\r\n";
                $risContent .= "ER  - \r\n"; // End of record marker

                // Limpiar el título para usarlo como nombre de archivo
                $tituloLimpio = preg_replace('/[^A-Za-z0-9\-_]/', '_', $recurso->titulo);
                $tituloLimpio = substr($tituloLimpio, 0, 50); // Limitar longitud
                $fileName = $tituloLimpio . '_' . $nroControl . '.ris';

                // Agregar al ZIP
                $zip->addFromString($fileName, $risContent);
            }
        }

        $zip->close();

        // Verificar que el archivo se creó correctamente
        if (!file_exists($zipPath)) {
            return response()->json(['error' => 'Error al crear el archivo ZIP'], 500);
        }

        // Enviar el archivo ZIP como descarga
        return response()->download($zipPath, $zipFileName, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }
}