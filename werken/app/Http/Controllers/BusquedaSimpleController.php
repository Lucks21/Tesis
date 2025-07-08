<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class BusquedaSimpleController extends Controller
{
    /**
     * Ejecuta búsqueda usando stored procedure sp_WEB_busqueda_palabras_claves
     */
    public function buscar(Request $request)
    {
        $request->validate([
            'searchType' => 'required|string|in:autor,titulo,materia,editorial,serie',
            'query' => 'required|string|max:255',
        ]);

        $searchType = $request->input('searchType');
        $query = $request->input('query');
        
        // Mapeo de tipos de búsqueda a números según especificación
        $tiposCriterio = [
            'autor' => 1,
            'materia' => 2,
            'titulo' => 3,
            'editorial' => 4,
            'serie' => 5
        ];
        
        $tipoCriterio = $tiposCriterio[$searchType];
        
        try {
            // Ejecutar stored procedure sp_WEB_busqueda_palabras_claves
            $resultados = DB::select('EXEC sp_WEB_busqueda_palabras_claves ?, ?', [
                $query,
                $tipoCriterio
            ]);
            
            // Convertir resultados a colección y paginar
            $collection = collect($resultados);
            $pagina = $request->input('page', 1);
            $porPagina = 10;
            $resultadosPaginados = $collection->slice(($pagina - 1) * $porPagina, $porPagina);
            
            $paginacion = new \Illuminate\Pagination\LengthAwarePaginator(
                $resultadosPaginados,
                $collection->count(),
                $porPagina,
                $pagina,
                ['path' => $request->url(), 'query' => $request->query()]
            );
            
            return view('BusquedaResultados', [
                'resultados' => $paginacion,
                'busqueda' => $query,
                'criterio' => $searchType,
                'tipoCriterio' => $tipoCriterio,
                'noResultados' => $collection->isEmpty(),
            ]);
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error en la búsqueda: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Obtiene detalles usando stored procedure sp_WEB_detalle_busqueda
     */
    public function detallesBusqueda(Request $request)
    {
        $request->validate([
            'texto_busqueda' => 'required|string|max:255',
            'tipo_criterio' => 'required|integer|min:1|max:5',
        ]);
        
        $textoBusqueda = $request->input('texto_busqueda');
        $tipoCriterio = $request->input('tipo_criterio');
        
        try {
            // Ejecutar stored procedure sp_WEB_detalle_busqueda
            $detalles = DB::select('EXEC sp_WEB_detalle_busqueda ?, ?', [
                $textoBusqueda,
                $tipoCriterio
            ]);
            
            return view('DetallesBusqueda', [
                'detalles' => $detalles,
                'texto_busqueda' => $textoBusqueda,
                'tipo_criterio' => $tipoCriterio,
            ]);
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al obtener detalles: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Obtiene existencias usando stored procedure sp_WEB_detalle_existencias
     */
    public function detalleExistencias(Request $request)
    {
        $request->validate([
            'numero_control' => 'required|string|max:255',
            'tipo_consulta' => 'required|integer',
        ]);
        
        $numeroControl = $request->input('numero_control');
        $tipoConsulta = $request->input('tipo_consulta');
        
        try {
            // Ejecutar stored procedure sp_WEB_detalle_existencias
            $existencias = DB::select('EXEC sp_WEB_detalle_existencias ?, ?', [
                $numeroControl,
                $tipoConsulta
            ]);
            
            return view('DetalleExistencias', [
                'existencias' => $existencias,
                'numero_control' => $numeroControl,
                'tipo_consulta' => $tipoConsulta,
            ]);
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al obtener existencias: ' . $e->getMessage()]);
        }
    }
}
