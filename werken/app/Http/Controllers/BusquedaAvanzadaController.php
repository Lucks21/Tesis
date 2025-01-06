<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DetalleMaterial;

class BusquedaAvanzadaController extends Controller
{
    public function buscar(Request $request)
    {
        $criterio = $request->input('criterio');
        $valorCriterio = $request->input('valor_criterio');
        $titulo = $request->input('titulo');
        $orden = $request->input('orden', 'asc');
        $autorFiltro = $request->input('autor');
        $editorialFiltro = $request->input('editorial');
        $campusFiltro = $request->input('campus');
    
        // Construcción de la consulta
        $query = DB::table('V_TITULO as vt')
            ->leftJoin('V_AUTOR as va', 'vt.nro_control', '=', 'va.nro_control')
            ->leftJoin('V_EDITORIAL as ve', 'vt.nro_control', '=', 've.nro_control')
            ->leftJoin('EXISTENCIA as e', 'vt.nro_control', '=', 'e.nro_control')
            ->leftJoin('TB_CAMPUS as tc', 'e.campus_tb_campus', '=', 'tc.campus_tb_campus')
            ->select(
                'vt.nro_control',
                'vt.nombre_busqueda as titulo',
                'va.nombre_busqueda as autor',
                've.nombre_busqueda as editorial',
                'tc.nombre_tb_campus as biblioteca',
                DB::raw("
                    (
                        (CASE WHEN vt.nombre_busqueda = '{$titulo}' THEN 5 ELSE 0 END) + 
                        (CASE WHEN vt.nombre_busqueda LIKE '%{$titulo}%' THEN 3 ELSE 0 END) + 
                        (CASE WHEN va.nombre_busqueda = '{$valorCriterio}' THEN 4 ELSE 0 END) + 
                        (CASE WHEN va.nombre_busqueda LIKE '%{$valorCriterio}%' THEN 2 ELSE 0 END)
                    ) as relevancia
                ")
                
            );

        // Aplicar filtros según el criterio seleccionado
        if ($criterio === 'autor' && $valorCriterio) {
            $query->where('va.nombre_busqueda', 'LIKE', "%{$valorCriterio}%")
                  ->orderBy('va.nombre_busqueda', $orden);
        } elseif ($criterio === 'editorial' && $valorCriterio) {
            $query->where('ve.nombre_busqueda', 'LIKE', "%{$valorCriterio}%")
                  ->orderBy('ve.nombre_busqueda', $orden);
        } elseif ($criterio === 'materia' && $valorCriterio) {
            $query->join('V_MATERIA', 'vt.nro_control', '=', 'V_MATERIA.nro_control')
                  ->where('V_MATERIA.nombre_busqueda', 'LIKE', "%{$valorCriterio}%")
                  ->orderBy('V_MATERIA.nombre_busqueda', $orden);
        } elseif ($criterio === 'serie' && $valorCriterio) {
            $query->join('V_SERIE', 'vt.nro_control', '=', 'V_SERIE.nro_control')
                  ->where('V_SERIE.nombre_busqueda', 'LIKE', "%{$valorCriterio}%")
                  ->orderBy('V_SERIE.nombre_busqueda', $orden);
        }

    // Filtro por título si se proporciona
    if ($titulo) {
        $query->where('vt.nombre_busqueda', 'LIKE', "%{$titulo}%");
    }

    // Filtro por autor seleccionado
    if ($autorFiltro) {
        $query->where('va.nombre_busqueda', '=', $autorFiltro);
    }

    // Filtro por editorial seleccionada
    if ($editorialFiltro) {
        $query->where('ve.nombre_busqueda', '=', $editorialFiltro);
    }

    // Filtro por campus seleccionado
    if ($campusFiltro) {
        $query->where('tc.nombre_tb_campus', '=', $campusFiltro);
    }

    // Ordenar por relevancia primero y luego por título
    $query->orderBy('relevancia', 'desc')
          ->orderBy('vt.nombre_busqueda', $orden);

    // Obtener todos los resultados sin paginar para calcular filtros
    $allResults = $query->get();

    // Obtener lista de autores únicos de todos los resultados
    $autores = $allResults->pluck('autor')->unique()->sort()->take(10);

    // Obtener lista de editoriales únicas de todos los resultados
    $editoriales = $allResults->pluck('editorial')->unique()->sort()->take(10);

    // Obtener lista de campus únicos de todos los resultados
    $campuses = $allResults->pluck('biblioteca')->unique()->sort()->take(10);

    // Aplicar paginación
    $resultados = $query->paginate(10);

    // Retornar vista con resultados y filtros
    return view('BusquedaAvanzadaResultados', compact(
        'resultados', 
        'criterio', 
        'valorCriterio', 
        'titulo', 
        'orden', 
        'autores', 
        'autorFiltro', 
        'editoriales', 
        'editorialFiltro', 
        'campuses', 
        'campusFiltro'
    ));
}    
    
    public function mostrarTitulosPorAutor($autor, Request $request)
    {
        $titulo = $request->input('titulo');

        $query = DetalleMaterial::query()
            ->select('DSM_TITULO')
            ->where('DSM_AUTOR_EDITOR', '=', urldecode($autor));

        if ($titulo) {
            $query->where('DSM_TITULO', 'LIKE', '%' . $titulo . '%');
        }

        $titulos = $query->get();

        return view('TitulosPorAutor', compact('autor', 'titulos', 'titulo'));
    }

    public function mostrarTitulosPorEditorial($editorial, Request $request)
    {
        $titulo = $request->input('titulo');

        $query = DetalleMaterial::query()
            ->select('DSM_TITULO')
            ->where('DSM_EDITORIAL', '=', urldecode($editorial));

        if ($titulo) {
            $query->where('DSM_TITULO', 'LIKE', '%' . $titulo . '%');
        }

        $titulos = $query->get();

        return view('TitulosPorEditorial', compact('editorial', 'titulos', 'titulo'));
    }

    public function mostrarTitulosPorMateria($materia, Request $request)
    {
        $titulo = $request->input('titulo');
    
        $query = DB::table('DETALLE_MATERIAL')
            ->join('V_MATERIA', 'DETALLE_MATERIAL.som_numero', '=', 'V_MATERIA.nro_control')
            ->select('DETALLE_MATERIAL.DSM_TITULO')
            ->where('V_MATERIA.nombre_busqueda', '=', urldecode($materia));
    
        if ($titulo) {
            $query->where('DETALLE_MATERIAL.DSM_TITULO', 'LIKE', '%' . $titulo . '%');
        }
    
        $titulos = $query->get();
    
        return view('TitulosPorMateria', compact('materia', 'titulos', 'titulo'));
    }

    public function mostrarTitulosPorSerie($serie, Request $request)
    {
        $titulo = $request->input('titulo');

        $query = DB::table('DETALLE_MATERIAL')
            ->join('V_SERIE', 'DETALLE_MATERIAL.som_numero', '=', 'V_SERIE.nro_control')
            ->select('DETALLE_MATERIAL.DSM_TITULO')
            ->where('V_SERIE.nombre_busqueda', '=', urldecode($serie));

        if ($titulo) {
            $query->where('DETALLE_MATERIAL.DSM_TITULO', 'LIKE', '%' . $titulo . '%');
        }

        $titulos = $query->get();

        return view('TitulosPorSerie', compact('serie', 'titulos', 'titulo'));
    }
}
