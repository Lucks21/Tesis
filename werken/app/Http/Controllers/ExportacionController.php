<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExportacionController extends Controller
{
    public function exportRIS($nroControl)
    {        
        // Obtener los datos del recurso incluyendo tipo de material
        $recurso = DB::table('V_TITULO as vt')
            ->leftJoin('V_AUTOR as va', 'vt.nro_control', '=', 'va.nro_control')
            ->leftJoin('V_EDITORIAL as ve', 'vt.nro_control', '=', 've.nro_control')
            ->leftJoin('V_IDIOMA as vi', 'vt.nro_control', '=', 'vi.nro_control')
            ->leftJoin('DETALLE_MATERIAL as dm', function($join) {
                $join->on('vt.nombre_busqueda', '=', 'dm.DSM_TITULO')
                     ->orWhere(function($query) {
                         $query->where('dm.DSM_TIPO_MATERIAL', '=', 'S')
                               ->whereRaw('dm.DSM_TITULO LIKE vt.nombre_busqueda + \'%\'');
                     });
            })
            ->select(
                'vt.nombre_busqueda as titulo',
                'va.nombre_busqueda as autor',
                've.nombre_busqueda as editorial',
                'vi.nombre_busqueda as idioma',
                'dm.DSM_TIPO_MATERIAL as tipo_material'
            )
            ->where('vt.nro_control', '=', $nroControl)
            ->first();

        if (!$recurso) {
            abort(404);
        }        
        
        // Mapear tipo de material a tipo RIS
        $tipoRIS = $this->mapearTipoMaterialAFormato($recurso->tipo_material);
        
        // Generar contenido RIS
        $risContent = "TY  - " . $tipoRIS . "\r\n";
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

        // Eliminar duplicados por nro_control
        $nroControles = array_unique($nroControles);

        // Inicializar contenido RIS combinado
        $risContentCombinado = "";

        // Generar contenido RIS combinado para todos los recursos
        foreach ($nroControles as $nroControl) {
            $recurso = DB::table('V_TITULO as vt')
                ->leftJoin('V_AUTOR as va', 'vt.nro_control', '=', 'va.nro_control')
                ->leftJoin('V_EDITORIAL as ve', 'vt.nro_control', '=', 've.nro_control')
                ->leftJoin('V_IDIOMA as vi', 'vt.nro_control', '=', 'vi.nro_control')
                ->leftJoin('DETALLE_MATERIAL as dm', function($join) {
                    $join->on('vt.nombre_busqueda', '=', 'dm.DSM_TITULO')
                         ->orWhere(function($query) {
                             $query->where('dm.DSM_TIPO_MATERIAL', '=', 'S')
                                   ->whereRaw('dm.DSM_TITULO LIKE vt.nombre_busqueda + \'%\'');
                         });
                })
                ->select(
                    'vt.nombre_busqueda as titulo',
                    'va.nombre_busqueda as autor',
                    've.nombre_busqueda as editorial',
                    'vi.nombre_busqueda as idioma',
                    'dm.DSM_TIPO_MATERIAL as tipo_material'
                )
                ->where('vt.nro_control', '=', $nroControl)
                ->first();

            if ($recurso) {
                // Mapear tipo de material a tipo RIS
                $tipoRIS = $this->mapearTipoMaterialAFormato($recurso->tipo_material);
                
                // Agregar contenido RIS al contenido combinado
                $risContentCombinado .= "TY  - " . $tipoRIS . "\r\n";
                $risContentCombinado .= "TI  - " . $recurso->titulo . "\r\n";
                $risContentCombinado .= "AU  - " . $recurso->autor . "\r\n";
                $risContentCombinado .= "PB  - " . $recurso->editorial . "\r\n";
                $risContentCombinado .= "LA  - " . $recurso->idioma . "\r\n";
                $risContentCombinado .= "ER  - \r\n"; // End of record marker
                $risContentCombinado .= "\r\n"; // Línea en blanco entre registros para mejor legibilidad
            }
        }

        // Verificar que se generó contenido
        if (empty($risContentCombinado)) {
            return response()->json(['error' => 'No se encontraron recursos válidos para exportar'], 404);
        }

        // Generar nombre de archivo único
        $fileName = 'referencias_multiples_' . date('Y-m-d_H-i-s') . '.ris';
        
        // Generar respuesta para descarga del archivo RIS único
        $headers = [
            'Content-Type' => 'application/x-research-info-systems; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Content-Length' => strlen($risContentCombinado),
        ];

        return response($risContentCombinado, 200, $headers);
    }

    /**
     * Mapear tipo de material del sistema a tipo de formato RIS
     */
    private function mapearTipoMaterialAFormato($tipoMaterial)
    {
        // Mapeo de tipos de material a tipos RIS estándar
        $mapeoTipos = [
            'M' => 'BOOK',        // Monografías/Libros
            'S' => 'JFULL',       // Publicaciones Seriadas/Revistas
            'A' => 'JOUR',        // Artículos
            'R' => 'RPRT',        // Reportes
            'T' => 'THES',        // Tesis
            'C' => 'CONF',        // Conferencias
            'P' => 'PAT',         // Patentes
            'V' => 'VIDEO',       // Videos
            'E' => 'ELEC',        // Recursos electrónicos
            'D' => 'DATA',        // Bases de datos
            'F' => 'FIGURE',      // Figuras/Imágenes
            'U' => 'UNPB',        // No publicado
            'G' => 'GEN'          // General
        ];

        // Si no se especifica tipo o no está en el mapeo, usar BOOK como default
        if (empty($tipoMaterial) || !isset($mapeoTipos[$tipoMaterial])) {
            return 'BOOK';
        }

        return $mapeoTipos[$tipoMaterial];
    }
}