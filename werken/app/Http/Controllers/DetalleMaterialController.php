<?php

namespace App\Http\Controllers;

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
        
        // Obtener información adicional desde las vistas principales
        $autor = DB::table('V_AUTOR')
            ->where('nro_control', $numero)
            ->value('nombre_busqueda');
            
        $editorial = DB::table('V_EDITORIAL')
            ->where('nro_control', $numero)
            ->value('nombre_busqueda');
            
        $materia = DB::table('V_MATERIA')
            ->where('nro_control', $numero)
            ->value('nombre_busqueda');
            
        $serie = DB::table('V_SERIE')
            ->where('nro_control', $numero)
            ->value('nombre_busqueda');
            
        $dewey = DB::table('V_DEWEY')
            ->where('nro_control', $numero)
            ->value('nombre_busqueda');
        
        // Consulta todas las existencias para este material (solo si el SP funcionó)
        if (!empty(DB::select("EXEC sp_WEB_detalle_existencias ?, ?", [$numero, 'con_reserva']))) {
            $existencias = DB::select("EXEC sp_WEB_detalle_existencias ?, ?", [$numero, 'con_reserva']);
            $detalleMaterial->existencias = $existencias;
        }
        
        // Agregar campos adicionales con información de las vistas
        $detalleMaterial->nro_control = $numero;
        $detalleMaterial->nro_pedido = $numero;
        $detalleMaterial->titulo = $detalleMaterial->titulo ?? $existeEnVTitulo->nombre_busqueda;
        $detalleMaterial->autor = $autor ?? $detalleMaterial->autor ?? 'No disponible';
        $detalleMaterial->editorial = $editorial ?? $detalleMaterial->editorial ?? 'No disponible';
        $detalleMaterial->materia = $materia ?? 'No disponible';
        $detalleMaterial->serie = $serie ?? 'No disponible';
        $detalleMaterial->dewey = $dewey ?? 'No disponible';
        $detalleMaterial->edicion = $detalleMaterial->edicion ?? 'No disponible';
        $detalleMaterial->datos_publicacion = $detalleMaterial->datos_publicacion ?? 'No disponible';
        $detalleMaterial->descripcion = $detalleMaterial->descripcion ?? 'No disponible';
        $detalleMaterial->materiales = $detalleMaterial->materiales ?? 'No disponible';
        $detalleMaterial->existencias = $detalleMaterial->existencias ?? [];
        
        return view('detalle-material', [
            'detalleMaterial' => $detalleMaterial
        ]);
    }
}
