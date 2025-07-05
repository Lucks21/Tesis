<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DetalleMaterial;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BusquedaAvanzadaController extends Controller
{
    public function buscar(Request $request)
    {
        // Recuperar los parámetros del request
        $criterio = $request->input('criterio');
        $valorCriterio = $request->input('valor_criterio');
        $titulo = $request->input('titulo');
        $orden = $request->input('orden', 'asc');
        $pagina = $request->input('page', 1);

        $autorFiltro = $request->input('autor', []);
        $editorialFiltro = $request->input('editorial', []);
        $campusFiltro = $request->input('campus', []);



        // Procesar los parámetros de entrada para crear un texto procesado
        $filtros = [
            'autor' => $autorFiltro,
            'editorial' => $editorialFiltro,
            'campus' => $campusFiltro,
            'orden' => $orden
        ];
        $texto_procesado = $titulo . '|' . $valorCriterio . '|' . serialize($filtros);

        // Verificar si necesitamos ejecutar nueva consulta
        $ejecutar_nueva_consulta = (
            !session()->has('texto_busqueda') ||
            session('texto_busqueda') != $texto_procesado ||
            session('tipo_busqueda') != $criterio ||
            session('ind_busqueda') != 'avanzada'
        );

        if ($ejecutar_nueva_consulta) {
            // Configurar timeout para la consulta
            $this->configurarTimeoutBD();

            // Ejecutar la consulta y almacenar en sesión
            $bindings = [$titulo, "%$titulo%", $valorCriterio, "%$valorCriterio%"];

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
                    DB::raw("(
                        (CASE WHEN vt.nombre_busqueda = ? THEN 5 ELSE 0 END) +
                        (CASE WHEN vt.nombre_busqueda LIKE ? THEN 3 ELSE 0 END) +
                        (CASE WHEN va.nombre_busqueda = ? THEN 4 ELSE 0 END) +
                        (CASE WHEN va.nombre_busqueda LIKE ? THEN 2 ELSE 0 END)
                    ) as relevancia")
                )
                ->distinct();

            // Aplicar criterios de búsqueda solo si tienen valores
            switch ($criterio) {
                case 'autor':
                    if (!empty($valorCriterio)) {
                        $query->where('va.nombre_busqueda', 'LIKE', "%{$valorCriterio}%");
                    }
                    $query->orderBy('va.nombre_busqueda', $orden);
                    break;
                case 'editorial':
                    if (!empty($valorCriterio)) {
                        $query->where('ve.nombre_busqueda', 'LIKE', "%{$valorCriterio}%");
                    }
                    $query->orderBy('ve.nombre_busqueda', $orden);
                    break;
                case 'materia':
                    if (!empty($valorCriterio)) {
                        $query->join('V_MATERIA', 'vt.nro_control', '=', 'V_MATERIA.nro_control')
                              ->where('V_MATERIA.nombre_busqueda', 'LIKE', "%{$valorCriterio}%");
                    }
                    $query->orderBy('V_MATERIA.nombre_busqueda', $orden);
                    break;
                case 'serie':
                    if (!empty($valorCriterio)) {
                        $query->join('V_SERIE', 'vt.nro_control', '=', 'V_SERIE.nro_control')
                              ->where('V_SERIE.nombre_busqueda', 'LIKE', "%{$valorCriterio}%");
                    }
                    $query->orderBy('V_SERIE.nombre_busqueda', $orden);
                    break;
                default:
                    $query->orderBy('vt.nombre_busqueda', $orden);
                    break;
            }

            if (!empty($titulo)) {
                $query->where('vt.nombre_busqueda', 'LIKE', "%{$titulo}%");
            }

            if (!empty($autorFiltro)) {
                $query->whereIn('va.nombre_busqueda', (array) $autorFiltro);
            }

            if (!empty($editorialFiltro)) {
                $query->whereIn('ve.nombre_busqueda', (array) $editorialFiltro);
            }

            if (!empty($campusFiltro)) {
                $query->whereIn('tc.nombre_tb_campus', (array) $campusFiltro);
            }

            $query->orderBy('relevancia', 'desc')
                  ->orderBy('vt.nombre_busqueda', $orden);

            // Limitar la consulta para evitar timeout - aumentamos el límite para búsquedas amplias
            $allResults = (clone $query)->addBinding($bindings, 'select')->limit(5000)->get();

            // Almacenar en sesión tras nueva búsqueda
            session([
                'busqueda' => $allResults->toArray(),
                'tipo_busqueda' => $criterio,
                'texto_busqueda' => $texto_procesado,
                'nav_pagina' => $pagina,
                'busq_numrows' => $allResults->count(),
                'ind_busqueda' => 'avanzada'
            ]);
        } else {
            // Recuperar datos desde sesión
            $allResults = collect(session('busqueda', []));
            session(['nav_pagina' => $pagina]);
        }

        $paginateCollection = function (Collection $items, int $perPage, string $pageName): LengthAwarePaginator {
            $currentPage = request()->input($pageName, 1);
            $items = $items->filter()->unique()->sort()->values();
            $currentItems = $items->slice(($currentPage - 1) * $perPage, $perPage);
            return new LengthAwarePaginator($currentItems, $items->count(), $perPage, $currentPage, [
                'pageName' => $pageName,
                'path' => request()->url(),
                'query' => request()->query(),
            ]);
        };

        $autores = $paginateCollection($allResults->pluck('autor'), 10, 'page_autores');
        $editoriales = $paginateCollection($allResults->pluck('editorial'), 10, 'page_editoriales');
        $campuses = $paginateCollection($allResults->pluck('biblioteca'), 10, 'page_campuses');

        // Crear paginación para resultados principales
        $porPagina = 10;
        $totalResultados = session('busq_numrows', $allResults->count());
        $currentItems = $allResults->slice(($pagina - 1) * $porPagina, $porPagina);
        
        $resultados = new LengthAwarePaginator(
            $currentItems,
            $totalResultados,
            $porPagina,
            $pagina,
            [
                'path' => request()->url(),
                'query' => request()->query()
            ]
        );

        return view('BusquedaAvanzadaResultados', compact(
            'resultados',
            'criterio',
            'valorCriterio',
            'titulo',
            'orden',
            'autores',
            'editoriales',
            'campuses'
        ));
    }

    public function mostrarTitulosPorAutor($autor, Request $request)
    {
        $titulo = $request->input('titulo');
        $pagina = $request->input('page', 1);
        
        // Crear texto procesado para identificar la consulta
        $texto_procesado = $autor . '|' . $titulo;
        
        // Verificar si necesitamos ejecutar nueva consulta
        $ejecutar_nueva_consulta = (
            !session()->has('texto_busqueda_autor') ||
            session('texto_busqueda_autor') != $texto_procesado ||
            session('tipo_busqueda') != 'titulos_por_autor'
        );

        if ($ejecutar_nueva_consulta) {
            // Ejecutar la consulta y almacenar en sesión
            $query = DetalleMaterial::query()
                ->select('DSM_TITULO')
                ->where('DSM_AUTOR_EDITOR', '=', urldecode($autor));

            if ($titulo) {
                $query->where('DSM_TITULO', 'LIKE', '%' . $titulo . '%');
            }

            $titulos = $query->get();

            // Almacenar en sesión tras nueva búsqueda
            session([
                'busqueda_autor' => $titulos->toArray(),
                'tipo_busqueda' => 'titulos_por_autor',
                'texto_busqueda_autor' => $texto_procesado,
                'nav_pagina' => $pagina,
                'busq_numrows_autor' => $titulos->count(),
                'ind_busqueda' => 'titulos_por_autor'
            ]);
        } else {
            // Recuperar datos desde sesión
            $titulos = collect(session('busqueda_autor', []));
            session(['nav_pagina' => $pagina]);
        }

        return view('TitulosPorAutor', compact('autor', 'titulos', 'titulo'));
    }

    public function mostrarTitulosPorEditorial($editorial, Request $request)
    {
        $titulo = $request->input('titulo');
        $pagina = $request->input('page', 1);
        
        // Crear texto procesado para identificar la consulta
        $texto_procesado = $editorial . '|' . $titulo;
        
        // Verificar si necesitamos ejecutar nueva consulta
        $ejecutar_nueva_consulta = (
            !session()->has('texto_busqueda_editorial') ||
            session('texto_busqueda_editorial') != $texto_procesado ||
            session('tipo_busqueda') != 'titulos_por_editorial'
        );

        if ($ejecutar_nueva_consulta) {
            // Ejecutar la consulta y almacenar en sesión
            $query = DetalleMaterial::query()
                ->select('DSM_TITULO')
                ->where('DSM_EDITORIAL', '=', urldecode($editorial));

            if ($titulo) {
                $query->where('DSM_TITULO', 'LIKE', '%' . $titulo . '%');
            }

            $titulos = $query->get();

            // Almacenar en sesión tras nueva búsqueda
            session([
                'busqueda_editorial' => $titulos->toArray(),
                'tipo_busqueda' => 'titulos_por_editorial',
                'texto_busqueda_editorial' => $texto_procesado,
                'nav_pagina' => $pagina,
                'busq_numrows_editorial' => $titulos->count(),
                'ind_busqueda' => 'titulos_por_editorial'
            ]);
        } else {
            // Recuperar datos desde sesión
            $titulos = collect(session('busqueda_editorial', []));
            session(['nav_pagina' => $pagina]);
        }

        return view('TitulosPorEditorial', compact('editorial', 'titulos', 'titulo'));
    }

    public function mostrarTitulosPorMateria($materia, Request $request)
    {
        $titulo = $request->input('titulo');
        $pagina = $request->input('page', 1);
        
        // Crear texto procesado para identificar la consulta
        $texto_procesado = $materia . '|' . $titulo;
        
        // Verificar si necesitamos ejecutar nueva consulta
        $ejecutar_nueva_consulta = (
            !session()->has('texto_busqueda_materia') ||
            session('texto_busqueda_materia') != $texto_procesado ||
            session('tipo_busqueda') != 'titulos_por_materia'
        );

        if ($ejecutar_nueva_consulta) {
            // Ejecutar la consulta y almacenar en sesión
            $query = DB::table('DETALLE_MATERIAL')
                ->join('V_MATERIA', 'DETALLE_MATERIAL.som_numero', '=', 'V_MATERIA.nro_control')
                ->select('DETALLE_MATERIAL.DSM_TITULO')
                ->where('V_MATERIA.nombre_busqueda', '=', urldecode($materia));

            if ($titulo) {
                $query->where('DETALLE_MATERIAL.DSM_TITULO', 'LIKE', '%' . $titulo . '%');
            }

            $titulos = $query->get();

            // Almacenar en sesión tras nueva búsqueda
            session([
                'busqueda_materia' => $titulos->toArray(),
                'tipo_busqueda' => 'titulos_por_materia',
                'texto_busqueda_materia' => $texto_procesado,
                'nav_pagina' => $pagina,
                'busq_numrows_materia' => $titulos->count(),
                'ind_busqueda' => 'titulos_por_materia'
            ]);
        } else {
            // Recuperar datos desde sesión
            $titulos = collect(session('busqueda_materia', []));
            session(['nav_pagina' => $pagina]);
        }

        return view('TitulosPorMateria', compact('materia', 'titulos', 'titulo'));
    }

    public function mostrarTitulosPorSerie($serie, Request $request)
    {
        $titulo = $request->input('titulo');
        $pagina = $request->input('page', 1);
        
        // Crear texto procesado para identificar la consulta
        $texto_procesado = $serie . '|' . $titulo;
        
        // Verificar si necesitamos ejecutar nueva consulta
        $ejecutar_nueva_consulta = (
            !session()->has('texto_busqueda_serie') ||
            session('texto_busqueda_serie') != $texto_procesado ||
            session('tipo_busqueda') != 'titulos_por_serie'
        );

        if ($ejecutar_nueva_consulta) {
            // Ejecutar la consulta y almacenar en sesión
            $query = DB::table('DETALLE_MATERIAL')
                ->join('V_SERIE', 'DETALLE_MATERIAL.som_numero', '=', 'V_SERIE.nro_control')
                ->select('DETALLE_MATERIAL.DSM_TITULO')
                ->where('V_SERIE.nombre_busqueda', '=', urldecode($serie));

            if ($titulo) {
                $query->where('DETALLE_MATERIAL.DSM_TITULO', 'LIKE', '%' . $titulo . '%');
            }

            $titulos = $query->get();

            // Almacenar en sesión tras nueva búsqueda
            session([
                'busqueda_serie' => $titulos->toArray(),
                'tipo_busqueda' => 'titulos_por_serie',
                'texto_busqueda_serie' => $texto_procesado,
                'nav_pagina' => $pagina,
                'busq_numrows_serie' => $titulos->count(),
                'ind_busqueda' => 'titulos_por_serie'
            ]);
        } else {
            // Recuperar datos desde sesión
            $titulos = collect(session('busqueda_serie', []));
            session(['nav_pagina' => $pagina]);
        }

        return view('TitulosPorSerie', compact('serie', 'titulos', 'titulo'));
    }

    //Limpiar el caché de sesión para búsquedas avanzadas
    
    public function limpiarCacheSession()
    {
        $session_keys = [
            'busqueda', 'tipo_busqueda', 'texto_busqueda', 'nav_pagina', 'busq_numrows', 'ind_busqueda',
            'busqueda_autor', 'texto_busqueda_autor', 'busq_numrows_autor',
            'busqueda_editorial', 'texto_busqueda_editorial', 'busq_numrows_editorial',
            'busqueda_materia', 'texto_busqueda_materia', 'busq_numrows_materia',
            'busqueda_serie', 'texto_busqueda_serie', 'busq_numrows_serie'
        ];

        foreach ($session_keys as $key) {
            session()->forget($key);
        }

        return response()->json(['success' => true, 'message' => 'Cache de sesión limpiado exitosamente']);
    }

    //Obtener estadísticas del caché de sesión
    
    public function obtenerEstadisticasCache()
    {
        $stats = [
            'busqueda_principal' => [
                'existe' => session()->has('busqueda'),
                'registros' => session('busq_numrows', 0),
                'tipo' => session('tipo_busqueda', 'N/A'),
                'pagina_actual' => session('nav_pagina', 1)
            ],
            'busqueda_autor' => [
                'existe' => session()->has('busqueda_autor'),
                'registros' => session('busq_numrows_autor', 0)
            ],
            'busqueda_editorial' => [
                'existe' => session()->has('busqueda_editorial'),
                'registros' => session('busq_numrows_editorial', 0)
            ],
            'busqueda_materia' => [
                'existe' => session()->has('busqueda_materia'),
                'registros' => session('busq_numrows_materia', 0)
            ],
            'busqueda_serie' => [
                'existe' => session()->has('busqueda_serie'),
                'registros' => session('busq_numrows_serie', 0)
            ]
        ];

        return response()->json($stats);
    }

    //Método para probar el funcionamiento del caché de sesión
    public function testSessionCache(Request $request)
    {
        // Simulamos una búsqueda simple para probar
        $criterio = $request->input('criterio', 'autor');
        $valorCriterio = $request->input('valor_criterio', 'test');
        $titulo = $request->input('titulo', '');
        
        // Creamos el texto procesado
        $filtros = ['orden' => 'asc'];
        $texto_procesado = $titulo . '|' . $valorCriterio . '|' . serialize($filtros);
        
        // Verificamos si existe en sesión
        $existe_en_session = session()->has('texto_busqueda') && 
                           session('texto_busqueda') == $texto_procesado && 
                           session('tipo_busqueda') == $criterio;
        
        $resultado = [
            'test_parameters' => [
                'criterio' => $criterio,
                'valor_criterio' => $valorCriterio,
                'titulo' => $titulo,
                'texto_procesado' => $texto_procesado
            ],
            'session_status' => [
                'existe_en_session' => $existe_en_session,
                'session_texto_busqueda' => session('texto_busqueda', 'No existe'),
                'session_tipo_busqueda' => session('tipo_busqueda', 'No existe'),
                'session_ind_busqueda' => session('ind_busqueda', 'No existe')
            ],
            'action' => $existe_en_session ? 'Usaría datos de sesión' : 'Ejecutaría nueva consulta'
        ];
        
        // Si no existe en sesión, simulamos guardar datos
        if (!$existe_en_session) {
            session([
                'texto_busqueda' => $texto_procesado,
                'tipo_busqueda' => $criterio,
                'ind_busqueda' => 'avanzada',
                'busq_numrows' => 0,
                'nav_pagina' => 1,
                'busqueda' => []
            ]);
            
            $resultado['action'] .= ' - Datos guardados en sesión';
        }
        
        return response()->json($resultado);
    }


    //Configura el timeout para consultas SQL
    private function configurarTimeoutBD()
    {
        try {
            // Configurar timeout para consultas SQL (en segundos)
            DB::statement('SET SESSION wait_timeout = 600');
            DB::statement('SET SESSION interactive_timeout = 600');
            // Aumentar el límite de memoria para consultas grandes
            ini_set('memory_limit', '512M');
        } catch (\Exception $e) {
            // Si falla la configuración, continuamos sin error
        }
    }