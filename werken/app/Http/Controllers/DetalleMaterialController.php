<?php

namespace App\Http\Controllers;

use App\Models\DetalleMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetalleMaterialController extends Controller
{    public function show($numero)
    {
        // Primero verificar si el número existe en V_TITULO
        $existeEnVTitulo = DB::table('V_TITULO')
            ->where('nro_control', $numero)
            ->first();
        
        if (!$existeEnVTitulo) {
            return redirect()->route('resultados')->with('error', 'No se encontró el material solicitado.');
        }
        
        // Consulta información básica del material usando el stored procedure
        $detalleMaterial = DB::select("EXEC sp_WEB_detalle_existencias ?, ?", [$numero, 'con_reserva']);
        
        if (empty($detalleMaterial)) {
            // Si no existe en el stored procedure, crear un objeto básico con la información de V_TITULO
            $detalleMaterial = (object) [
                'nro_control' => $numero,
                'titulo' => $existeEnVTitulo->nombre_busqueda,
                'autor' => 'No disponible',
                'editorial' => 'No disponible',
                'nro_pedido' => $numero,
                'edicion' => 'No disponible',
                'datos_publicacion' => 'No disponible',
                'descripcion' => 'No disponible',
                'materiales' => 'No disponible',
                'existencias' => []
            ];
        } else {
            // Convertir el resultado a objeto para facilitar el acceso a las propiedades
            $detalleMaterial = $detalleMaterial[0];
        }
        
        // Consulta información adicional del material desde DETALLE_MATERIAL
        $infoAdicional = DB::table('DETALLE_MATERIAL as dm')
            ->select(
                'dm.DSM_CORRELATIVO as nro_pedido',
                'dm.DSM_AUTOR_EDITOR as autor',
                'dm.DSM_TITULO as titulo',
                'dm.DSM_EDITORIAL as editorial',
                'dm.DSM_ISBN_ISSN as isbn_issn',
                'dm.DSM_PUBLICACION as datos_publicacion',
                'dm.DSM_OBSERVACION as descripcion',
                'dm.DSM_REPRESENTACION as materiales'
            )
            ->where('dm.SOM_NUMERO', $numero)
            ->first();
        
        // Agregar información adicional al objeto principal
        if ($infoAdicional) {
            $detalleMaterial->nro_pedido = $infoAdicional->nro_pedido;
            $detalleMaterial->autor = $infoAdicional->autor;
            $detalleMaterial->titulo = $infoAdicional->titulo;
            $detalleMaterial->editorial = $infoAdicional->editorial;
            $detalleMaterial->isbn_issn = $infoAdicional->isbn_issn;
            $detalleMaterial->datos_publicacion = $infoAdicional->datos_publicacion;
            $detalleMaterial->descripcion = $infoAdicional->descripcion;
            $detalleMaterial->materiales = $infoAdicional->materiales;
        }
        
        // Consulta todas las existencias para este material (solo si el SP funcionó)
        if (!empty(DB::select("EXEC sp_WEB_detalle_existencias ?, ?", [$numero, 'con_reserva']))) {
            $existencias = DB::select("EXEC sp_WEB_detalle_existencias ?, ?", [$numero, 'con_reserva']);
            $detalleMaterial->existencias = $existencias;
        }
        
        // Agregar campos adicionales si no existen
        if (!isset($detalleMaterial->nro_control)) {
            $detalleMaterial->nro_control = $numero;
        }
        if (!isset($detalleMaterial->nro_pedido)) {
            $detalleMaterial->nro_pedido = $numero;
        }
        if (!isset($detalleMaterial->autor)) {
            $detalleMaterial->autor = 'No disponible';
        }
        if (!isset($detalleMaterial->titulo)) {
            $detalleMaterial->titulo = $existeEnVTitulo->nombre_busqueda;
        }
        if (!isset($detalleMaterial->edicion)) {
            $detalleMaterial->edicion = 'No disponible';
        }
        if (!isset($detalleMaterial->datos_publicacion)) {
            $detalleMaterial->datos_publicacion = 'No disponible';
        }
        if (!isset($detalleMaterial->descripcion)) {
            $detalleMaterial->descripcion = 'No disponible';
        }
        if (!isset($detalleMaterial->materiales)) {
            $detalleMaterial->materiales = 'No disponible';
        }
        if (!isset($detalleMaterial->existencias)) {
            $detalleMaterial->existencias = [];
        }
        
        return view('detalle-material', [
            'detalleMaterial' => $detalleMaterial
        ]);
    }

    public function resumen($numero)
    {
        // Consulta información completa del material para resumen
        $detalleMaterial = DB::table('DETALLE_MATERIAL as dm')
            ->select(
                'dm.SOM_NUMERO as nro_control',
                'dm.DSM_CORRELATIVO as nro_pedido',
                'dm.DSM_AUTOR_EDITOR as autor',
                'dm.DSM_TITULO as titulo',
                'dm.DSM_EDITORIAL as editorial',
                'dm.DSM_ISBN_ISSN as isbn_issn',
                'dm.DSM_PUBLICACION as datos_publicacion',
                'dm.DSM_CANTIDAD_ORIGINAL as copias',
                'dm.DSM_OBSERVACION as descripcion',
                'dm.DSM_TIPO_MATERIAL as tipo_material',
                'dm.DSM_IND_SUSCRIPCION',
                'dm.DSM_REPRESENTACION as materiales',
                'dm.DSM_USUARIO as catalogador',
                'dm.DSM_FECHA as fecha_catalogacion'
            )
            ->where('dm.SOM_NUMERO', $numero)
            ->first();

        if (!$detalleMaterial) {
            return redirect()->route('resultados')->with('error', 'No se encontró el material solicitado.');
        }

        // Agregar campos adicionales para compatibilidad
        $detalleMaterial->edicion = 'No disponible';
        $detalleMaterial->serie = null;
        $detalleMaterial->materia = null;
        $detalleMaterial->notas = $detalleMaterial->descripcion;
        $detalleMaterial->clasificacion_dewey = 'No disponible';
        $detalleMaterial->encabezamientos_materia = 'No disponible';
        
        return view('resumen-material', [
            'detalleMaterial' => $detalleMaterial
        ]);
    }

    public function detalleCompleto($numero)
    {
        // Consulta información bibliográfica completa
        $detalleMaterial = DB::table('DETALLE_MATERIAL as dm')
            ->select(
                'dm.SOM_NUMERO as nro_control',
                'dm.DSM_CORRELATIVO as nro_pedido',
                'dm.DSM_AUTOR_EDITOR as autor',
                'dm.DSM_TITULO as titulo',
                'dm.DSM_EDITORIAL as editorial',
                'dm.DSM_ISBN_ISSN as isbn_issn',
                'dm.DSM_PUBLICACION as datos_publicacion',
                'dm.DSM_CANTIDAD_ORIGINAL as copias',
                'dm.DSM_OBSERVACION as descripcion',
                'dm.DSM_TIPO_MATERIAL as tipo_material',
                'dm.DSM_IND_SUSCRIPCION',
                'dm.DSM_REPRESENTACION as materiales',
                'dm.DSM_USUARIO as catalogador',
                'dm.DSM_FECHA as fecha_catalogacion',
                'dm.DSM_ESTACION as estacion_trabajo'
            )
            ->where('dm.SOM_NUMERO', $numero)
            ->first();

        if (!$detalleMaterial) {
            return redirect()->route('resultados')->with('error', 'No se encontró el material solicitado.');
        }

        // Agregar campos adicionales para compatibilidad
        $detalleMaterial->edicion = 'No disponible';
        $detalleMaterial->serie = null;
        $detalleMaterial->materia = null;
        $detalleMaterial->notas = $detalleMaterial->descripcion;
        $detalleMaterial->clasificacion_dewey = 'No disponible';
        $detalleMaterial->encabezamientos_materia = 'No disponible';
        $detalleMaterial->biblioteca = 'Sistema de Bibliotecas UBB';
        $detalleMaterial->estado = 'Disponible';

        // Información adicional simulada
        $informacionCompleta = (object)[
            'campos_adicionales' => 'Información adicional del sistema de bibliotecas',
            'enlaces_relacionados' => 'Vínculos con otros materiales relacionados',
            'historia_catalogacion' => 'Catalogado por: ' . $detalleMaterial->catalogador . ' en ' . $detalleMaterial->fecha_catalogacion
        ];

        return view('detalle-material-completo', [
            'detalleMaterial' => $detalleMaterial,
            'informacionCompleta' => $informacionCompleta
        ]);
    }
}
