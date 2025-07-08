<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DetalleMaterial;
use App\Models\Materia;
use App\Models\Autor;
use App\Models\Editorial;
use App\Models\Serie;
use App\Models\Titulo;


class BusquedaSimpleController extends Controller
{
    public function buscarPorTitulo(Request $request)
    {
        $request->validate([
            'busqueda' => 'required|string|max:255',
        ]);
    
        $titulo = $request->input('busqueda');
        $palabras = explode(' ', $titulo);
        
        // Usar consulta similar a la búsqueda avanzada para obtener toda la información
        $query = DB::table('V_TITULO as vt')
            ->leftJoin('V_AUTOR as va', 'vt.nro_control', '=', 'va.nro_control')
            ->leftJoin('V_EDITORIAL as ve', 'vt.nro_control', '=', 've.nro_control')
            ->leftJoin('V_MATERIA as vm', 'vt.nro_control', '=', 'vm.nro_control')
            ->leftJoin('V_SERIE as vs', 'vt.nro_control', '=', 'vs.nro_control')
            ->leftJoin('EXISTENCIA as e', 'vt.nro_control', '=', 'e.nro_control')
            ->leftJoin('TB_CAMPUS as tc', 'e.campus_tb_campus', '=', 'tc.campus_tb_campus')
            ->select([
                'vt.nro_control',
                'vt.nombre_busqueda as titulo',
                'va.nombre_busqueda as autor',
                've.nombre_busqueda as editorial',
                'vm.nombre_busqueda as materia',
                'vs.nombre_busqueda as serie',
                'tc.nombre_tb_campus as biblioteca',
            ])
            ->distinct();

        // Aplicar filtro de búsqueda por título
        $query->where(function ($q) use ($palabras) {
            foreach ($palabras as $palabra) {
                $q->orWhere('vt.nombre_busqueda', 'LIKE', "%{$palabra}%");
            }
        });

        // Aplicar filtros adicionales si están presentes
        if ($request->filled('autor')) {
            $autores = is_array($request->autor) ? $request->autor : [$request->autor];
            $query->whereIn('va.nombre_busqueda', $autores);
        }

        if ($request->filled('editorial')) {
            $editoriales = is_array($request->editorial) ? $request->editorial : [$request->editorial];
            $query->whereIn('ve.nombre_busqueda', $editoriales);
        }

        if ($request->filled('materia')) {
            $materias = is_array($request->materia) ? $request->materia : [$request->materia];
            $query->whereIn('vm.nombre_busqueda', $materias);
        }

        if ($request->filled('serie')) {
            $series = is_array($request->serie) ? $request->serie : [$request->serie];
            $query->whereIn('vs.nombre_busqueda', $series);
        }

        if ($request->filled('campus')) {
            $campuses = is_array($request->campus) ? $request->campus : [$request->campus];
            $query->whereIn('tc.nombre_tb_campus', $campuses);
        }

        $titulos = $query->get();
        
        // Obtener datos para filtros
        $autores = collect($titulos)->pluck('autor')->filter()->unique()->sort()->values();
        $editoriales = collect($titulos)->pluck('editorial')->filter()->unique()->sort()->values();
        $materias = collect($titulos)->pluck('materia')->filter()->unique()->sort()->values();
        $series = collect($titulos)->pluck('serie')->filter()->unique()->sort()->values();
        $campuses = collect($titulos)->pluck('biblioteca')->filter()->unique()->sort()->values();
        
        // Paginación
        $pagina = $request->input('page', 1);
        $porPagina = 10;
        $paginados = $titulos->forPage($pagina, $porPagina);

        $resultados = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginados,
            $titulos->count(),
            $porPagina,
            $pagina,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('BusquedaSimpleResultados', [
            'criterio' => 'titulo',
            'busqueda' => $titulo,
            'resultados' => $resultados,
            'noResultados' => $resultados->isEmpty(),
            'mostrarTitulos' => true,
            'autores' => $autores,
            'editoriales' => $editoriales,
            'materias' => $materias,
            'series' => $series,
            'campuses' => $campuses,
        ]);
    }
    
    public function buscar(Request $request)
    {
        $request->validate([
            'criterio' => 'required|string|in:autor,editorial,serie,materia',
            'busqueda' => 'required|string|max:255',
        ]);
    
        $criterio = $request->input('criterio');
        $busqueda = $request->input('busqueda');
        $palabras = explode(' ', $busqueda);
    
        $modelos = [
            'autor' => Autor::class,
            'editorial' => Editorial::class,
            'serie' => Serie::class,
            'materia' => Materia::class,
        ];
    
        if (!array_key_exists($criterio, $modelos)) {
            abort(404, 'Criterio no válido.');
        }
    
        $modelo = $modelos[$criterio];
    
        $resultadosSinPaginar = $modelo::where(function ($query) use ($palabras) {
            foreach ($palabras as $palabra) {
                $query->where('nombre_busqueda', 'LIKE', "%{$palabra}%");
            }
        })->select('nombre_busqueda')->distinct()->get();
    
        $pagina = $request->input('page', 1);
        $porPagina = 10;
        $resultadosPaginados = $resultadosSinPaginar->slice(($pagina - 1) * $porPagina, $porPagina);
    
        $resultados = new \Illuminate\Pagination\LengthAwarePaginator(
            $resultadosPaginados,
            $resultadosSinPaginar->count(),
            $porPagina,
            $pagina,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Obtener datos para filtros (vacíos para búsquedas que no son por título)
        $autores = collect();
        $editoriales = collect();
        $materias = collect();
        $series = collect();
        $campuses = collect();

        return view('BusquedaSimpleResultados', [
            'resultados' => $resultados,
            'busqueda' => $busqueda,
            'criterio' => $criterio,
            'noResultados' => $resultados->isEmpty(),
            'mostrarTitulos' => false,
            'autores' => $autores,
            'editoriales' => $editoriales,
            'materias' => $materias,
            'series' => $series,
            'campuses' => $campuses,
        ]);
    }
    
        public function recursosAsociados($criterio, $valor)
    {
        $modelos = [
            'autor' => Autor::class,
            'editorial' => Editorial::class,
            'serie' => Serie::class,
            'materia' => Materia::class,
        ];
    
        if (!array_key_exists($criterio, $modelos)) {
            abort(404, 'Criterio no válido.');
        }
    
        $modelo = $modelos[$criterio];
    
        $recursos = $modelo::where('nombre_busqueda', $valor)->with('titulos')->get();
    
        if ($recursos->isEmpty()) {
            abort(404, 'No se encontraron recursos asociados.');
        }
    
        $titulos = $recursos->flatMap->titulos;
    
        $pagina = request()->input('page', 1);
        $porPagina = 10;
        $paginados = $titulos->forPage($pagina, $porPagina);
    
        return view('RecursosAsociadosView', [
            'criterio' => ucfirst($criterio),
            'valor' => $valor,
            'recursos' => new \Illuminate\Pagination\LengthAwarePaginator(
                $paginados,
                $titulos->count(),
                $porPagina,
                $pagina,
                ['path' => request()->url(), 'query' => request()->query()]
            ),
        ]);
    }    
    
    public function titulosRelacionados($criterio, $valor)
    {
        // Configurar timeout para la consulta
        ini_set('max_execution_time', 60);
        
        $modelos = [
            'autor' => Autor::class,
            'editorial' => Editorial::class,
            'serie' => Serie::class,
            'materia' => Materia::class,
        ];

        if (!array_key_exists($criterio, $modelos)) {
            abort(404, 'Criterio no válido.');
        }

        // Buscar títulos usando la misma lógica que la búsqueda avanzada
        $query = DB::table('V_TITULO as vt')
            ->leftJoin('V_AUTOR as va', 'vt.nro_control', '=', 'va.nro_control')
            ->leftJoin('V_EDITORIAL as ve', 'vt.nro_control', '=', 've.nro_control')
            ->leftJoin('V_MATERIA as vm', 'vt.nro_control', '=', 'vm.nro_control')
            ->leftJoin('V_SERIE as vs', 'vt.nro_control', '=', 'vs.nro_control')
            ->leftJoin('EXISTENCIA as e', 'vt.nro_control', '=', 'e.nro_control')
            ->leftJoin('TB_CAMPUS as tc', 'e.campus_tb_campus', '=', 'tc.campus_tb_campus')
            ->select([
                'vt.nro_control',
                'vt.nombre_busqueda as titulo',
                'va.nombre_busqueda as autor',
                've.nombre_busqueda as editorial',
                'vm.nombre_busqueda as materia',
                'vs.nombre_busqueda as serie',
                'tc.nombre_tb_campus as biblioteca',
            ])
            ->distinct();

        // Aplicar filtro según el criterio
        switch ($criterio) {
            case 'autor':
                $query->where('va.nombre_busqueda', '=', $valor);
                break;
            case 'editorial':
                $query->where('ve.nombre_busqueda', '=', $valor);
                break;
            case 'materia':
                $query->where('vm.nombre_busqueda', '=', $valor);
                break;
            case 'serie':
                $query->where('vs.nombre_busqueda', '=', $valor);
                break;
        }

        $titulos = $query->get();

        if ($titulos->isEmpty()) {
            return view('BusquedaSimpleResultados', [
                'criterio' => $criterio,
                'busqueda' => $valor,
                'resultados' => collect(),
                'noResultados' => true,
                'mostrarTitulos' => true,
                'valorSeleccionado' => $valor,
                'autores' => collect(),
                'editoriales' => collect(),
                'materias' => collect(),
                'series' => collect(),
                'campuses' => collect(),
            ]);
        }

        // Obtener datos para filtros
        $autores = collect($titulos)->pluck('autor')->filter()->unique()->sort()->values();
        $editoriales = collect($titulos)->pluck('editorial')->filter()->unique()->sort()->values();
        $materias = collect($titulos)->pluck('materia')->filter()->unique()->sort()->values();
        $series = collect($titulos)->pluck('serie')->filter()->unique()->sort()->values();
        $campuses = collect($titulos)->pluck('biblioteca')->filter()->unique()->sort()->values();

        // Paginación
        $pagina = request()->input('page', 1);
        $porPagina = 10;
        $paginados = $titulos->forPage($pagina, $porPagina);

        $resultados = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginados,
            $titulos->count(),
            $porPagina,
            $pagina,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('BusquedaSimpleResultados', [
            'criterio' => $criterio,
            'busqueda' => $valor,
            'resultados' => $resultados,
            'noResultados' => $titulos->isEmpty(),
            'mostrarTitulos' => true,
            'valorSeleccionado' => $valor,
            'autores' => $autores,
            'editoriales' => $editoriales,
            'materias' => $materias,
            'series' => $series,
            'campuses' => $campuses,
        ]);
    }
    
    public function mostrarFormulario(Request $request)
    {
        // Si hay parámetros de búsqueda, procesar la búsqueda
        if ($request->has('searchType') && $request->has('query')) {
            $criterio = $request->input('searchType');
            $busqueda = $request->input('query');
            
            // Crear un nuevo request con los parámetros correctos
            if ($criterio === 'titulo') {
                // Para títulos, usar buscarPorTitulo con parámetro 'busqueda'
                $newRequest = new Request(['busqueda' => $busqueda]);
                $newRequest->setMethod('GET');
                return $this->buscarPorTitulo($newRequest);
            } else {
                // Para otros criterios, usar buscar con parámetros 'criterio' y 'busqueda'
                $newRequest = new Request(['criterio' => $criterio, 'busqueda' => $busqueda]);
                $newRequest->setMethod('GET');
                return $this->buscar($newRequest);
            }
        }
        
        // Si no hay parámetros, mostrar el formulario
        return view('BusquedaView');
    }
}
