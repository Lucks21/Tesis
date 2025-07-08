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
            
            // Convertir resultados a colección
            $collection = collect($resultados);
            
            // Si es búsqueda por título, mostrar directamente los títulos con detalles
            if ($searchType === 'titulo') {
                // Paginar resultados de títulos
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
                    'mostrarTitulos' => true,
                ]);
            } else {
                // Para otros criterios, mostrar lista de coincidencias para seleccionar
                // Agrupar por nombre_busqueda para evitar duplicados
                $coincidencias = $collection->groupBy('nombre_busqueda')
                    ->map(function ($items) {
                        return $items->first(); // Tomar el primer elemento de cada grupo
                    })
                    ->values(); // Reindexar
                
                // Paginar coincidencias
                $pagina = $request->input('page', 1);
                $porPagina = 15;
                $coincidenciasPaginadas = $coincidencias->slice(($pagina - 1) * $porPagina, $porPagina);
                
                $paginacion = new \Illuminate\Pagination\LengthAwarePaginator(
                    $coincidenciasPaginadas,
                    $coincidencias->count(),
                    $porPagina,
                    $pagina,
                    ['path' => $request->url(), 'query' => $request->query()]
                );
                
                return view('BusquedaResultados', [
                    'resultados' => $paginacion,
                    'busqueda' => $query,
                    'criterio' => $searchType,
                    'tipoCriterio' => $tipoCriterio,
                    'noResultados' => $coincidencias->isEmpty(),
                    'mostrarTitulos' => false,
                ]);
            }
            
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
    
    /**
     * Muestra títulos relacionados a un criterio específico seleccionado
     */
    public function mostrarTitulosRelacionados(Request $request)
    {
        $request->validate([
            'criterio' => 'required|string|in:autor,materia,editorial,serie',
            'valor' => 'required|string|max:255',
        ]);

        $criterio = $request->input('criterio');
        $valor = $request->input('valor');
        
        // Mapeo de tipos de búsqueda a números según especificación
        $tiposCriterio = [
            'autor' => 1,
            'materia' => 2,
            'editorial' => 4,
            'serie' => 5
        ];
        
        $tipoCriterio = $tiposCriterio[$criterio];
        
        try {
            // Ejecutar stored procedure sp_WEB_busqueda_palabras_claves con el valor específico
            $resultados = DB::select('EXEC sp_WEB_busqueda_palabras_claves ?, ?', [
                $valor,
                $tipoCriterio
            ]);
            
            // Convertir resultados a colección y filtrar solo los que coinciden exactamente
            $collection = collect($resultados);
            
            // Si es para mostrar títulos relacionados, obtener todos los resultados relacionados
            if ($request->has('mostrar_titulos') && $request->input('mostrar_titulos') === 'true') {
                // Filtrar todos los elementos que coinciden con el valor seleccionado
                $titulosRelacionados = $collection->filter(function ($item) use ($valor) {
                    return isset($item->nombre_busqueda) && 
                           strtolower(trim($item->nombre_busqueda)) === strtolower(trim($valor));
                });
            } else {
                // Para la vista de selección, solo filtrar los que coinciden exactamente
                $titulosRelacionados = $collection->filter(function ($item) use ($valor) {
                    return isset($item->nombre_busqueda) && 
                           strtolower(trim($item->nombre_busqueda)) === strtolower(trim($valor));
                });
            }
            
            // Paginar resultados
            $pagina = $request->input('page', 1);
            $porPagina = 10;
            $resultadosPaginados = $titulosRelacionados->slice(($pagina - 1) * $porPagina, $porPagina);
            
            $paginacion = new \Illuminate\Pagination\LengthAwarePaginator(
                $resultadosPaginados,
                $titulosRelacionados->count(),
                $porPagina,
                $pagina,
                ['path' => $request->url(), 'query' => $request->query()]
            );
            
            return view('BusquedaResultados', [
                'resultados' => $paginacion,
                'busqueda' => $valor,
                'criterio' => $criterio,
                'tipoCriterio' => $tipoCriterio,
                'noResultados' => $titulosRelacionados->isEmpty(),
                'mostrarTitulos' => true,
                'valorSeleccionado' => $valor,
            ]);
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al obtener títulos relacionados: ' . $e->getMessage()]);
        }
    }
}
