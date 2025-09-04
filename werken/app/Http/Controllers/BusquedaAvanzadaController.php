<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $pagina = $request->input('page', 1);

        // Limpiar el texto de entrada eliminando signos de puntuación
        $valorCriterio = $this->limpiarTexto($valorCriterio);
        $titulo = $this->limpiarTexto($titulo);

        // Parámetros de ordenamiento
        $sortBy = $request->input('sort_by', 'relevancia');
        $sortDirection = $request->input('sort_direction', 'desc');

        $autorFiltro = $request->input('autor', []);
        $editorialFiltro = $request->input('editorial', []);
        $campusFiltro = $request->input('campus', []);
        $materiaFiltro = $request->input('materia', []);
        $serieFiltro = $request->input('serie', []);

        // Procesar los filtros para manejar tanto arrays como strings separadas por comas
        $autorFiltro = $this->procesarFiltro($autorFiltro);
        $editorialFiltro = $this->procesarFiltro($editorialFiltro);
        $campusFiltro = $this->procesarFiltro($campusFiltro);
        $materiaFiltro = $this->procesarFiltro($materiaFiltro);
        $serieFiltro = $this->procesarFiltro($serieFiltro);

        // Procesar los parámetros de entrada para crear un texto procesado
        $filtros = [
            'autor' => $autorFiltro,
            'editorial' => $editorialFiltro,
            'campus' => $campusFiltro,
            'materia' => $materiaFiltro,
            'serie' => $serieFiltro,
            'sort_by' => $sortBy,
            'sort_direction' => $sortDirection
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

            $query = DB::table('V_TITULO as vt')
                ->leftJoin('V_AUTOR as va', 'vt.nro_control', '=', 'va.nro_control')
                ->leftJoin('V_EDITORIAL as ve', 'vt.nro_control', '=', 've.nro_control')
                ->leftJoin('V_MATERIA as vm', 'vt.nro_control', '=', 'vm.nro_control')
                ->leftJoin('V_SERIE as vs', 'vt.nro_control', '=', 'vs.nro_control')
                ->leftJoin('V_DEWEY as vd', 'vt.nro_control', '=', 'vd.nro_control')
                ->leftJoin('EXISTENCIA as e', 'vt.nro_control', '=', 'e.nro_control')
                ->leftJoin('TB_CAMPUS as tc', 'e.campus_tb_campus', '=', 'tc.campus_tb_campus');

            // Aplicar criterios de búsqueda solo si tienen valores
            switch ($criterio) {
                case 'autor':
                    if (!empty($valorCriterio)) {
                        $this->aplicarBusquedaFlexible($query, 'va.nombre_busqueda', $valorCriterio);
                    }
                    break;
                case 'editorial':
                    if (!empty($valorCriterio)) {
                        $this->aplicarBusquedaFlexible($query, 've.nombre_busqueda', $valorCriterio);
                    }
                    break;
                case 'materia':
                    if (!empty($valorCriterio)) {
                        $this->aplicarBusquedaFlexible($query, 'vm.nombre_busqueda', $valorCriterio);
                    }
                    break;
                case 'serie':
                    if (!empty($valorCriterio)) {
                        $this->aplicarBusquedaFlexible($query, 'vs.nombre_busqueda', $valorCriterio);
                    }
                    break;
                default:
                    break;
            }

            // Definir campos de selección con cálculo de relevancia
            $selectFields = [
                'vt.nro_control',
                'vt.nombre_busqueda as titulo',
                'va.nombre_busqueda as autor',
                've.nombre_busqueda as editorial',
                'vm.nombre_busqueda as materia',
                'vs.nombre_busqueda as serie',
                'vd.nombre_busqueda as dewey',
                'tc.nombre_tb_campus as biblioteca'
            ];

            // Solo agregar cálculo de relevancia si hay criterios de búsqueda
            if (!empty($titulo) || !empty($valorCriterio)) {
                // Dividir criterios en palabras para cálculo de relevancia (removiendo signos de puntuación)
                $palabrasTitulo = !empty($titulo) ? array_filter(preg_split('/[\s,]+/', $titulo)) : [];
                $palabrasCriterio = !empty($valorCriterio) ? array_filter(preg_split('/[\s,]+/', $valorCriterio)) : [];
                
                $relevanciaSQL = "( 0";
                
                // Relevancia para título exacto
                if (!empty($titulo)) {
                    $tituloLimpio = implode(' ', $palabrasTitulo);
                    $relevanciaSQL .= " + (CASE WHEN vt.nombre_busqueda = '{$titulo}' THEN 10 ELSE 0 END)";
                    $relevanciaSQL .= " + (CASE WHEN vt.nombre_busqueda = '{$tituloLimpio}' THEN 9 ELSE 0 END)";
                    $relevanciaSQL .= " + (CASE WHEN vt.nombre_busqueda LIKE '%{$titulo}%' THEN 5 ELSE 0 END)";
                    $relevanciaSQL .= " + (CASE WHEN vt.nombre_busqueda LIKE '%{$tituloLimpio}%' THEN 4 ELSE 0 END)";
                }
                
                // Relevancia para valor criterio exacto
                if (!empty($valorCriterio)) {
                    $criterioLimpio = implode(' ', $palabrasCriterio);
                    switch ($criterio) {
                        case 'autor':
                            $relevanciaSQL .= " + (CASE WHEN va.nombre_busqueda = '{$valorCriterio}' THEN 10 ELSE 0 END)";
                            $relevanciaSQL .= " + (CASE WHEN va.nombre_busqueda = '{$criterioLimpio}' THEN 9 ELSE 0 END)";
                            $relevanciaSQL .= " + (CASE WHEN va.nombre_busqueda LIKE '%{$valorCriterio}%' THEN 6 ELSE 0 END)";
                            $relevanciaSQL .= " + (CASE WHEN va.nombre_busqueda LIKE '%{$criterioLimpio}%' THEN 5 ELSE 0 END)";
                            
                            // Buscar formato "Apellido, Nombre" si hay 2 palabras
                            if (count($palabrasCriterio) == 2) {
                                $formatoInverso = $palabrasCriterio[1] . ', ' . $palabrasCriterio[0];
                                $formatoInverso2 = $palabrasCriterio[1] . ' ' . $palabrasCriterio[0];
                                $relevanciaSQL .= " + (CASE WHEN va.nombre_busqueda LIKE '%{$formatoInverso}%' THEN 7 ELSE 0 END)";
                                $relevanciaSQL .= " + (CASE WHEN va.nombre_busqueda LIKE '%{$formatoInverso2}%' THEN 6 ELSE 0 END)";
                            }
                            break;
                        case 'editorial':
                            $relevanciaSQL .= " + (CASE WHEN ve.nombre_busqueda = '{$valorCriterio}' THEN 8 ELSE 0 END)";
                            $relevanciaSQL .= " + (CASE WHEN ve.nombre_busqueda LIKE '%{$valorCriterio}%' THEN 4 ELSE 0 END)";
                            break;
                        case 'materia':
                            $relevanciaSQL .= " + (CASE WHEN vm.nombre_busqueda = '{$valorCriterio}' THEN 8 ELSE 0 END)";
                            $relevanciaSQL .= " + (CASE WHEN vm.nombre_busqueda LIKE '%{$valorCriterio}%' THEN 4 ELSE 0 END)";
                            break;
                        case 'serie':
                            $relevanciaSQL .= " + (CASE WHEN vs.nombre_busqueda = '{$valorCriterio}' THEN 8 ELSE 0 END)";
                            $relevanciaSQL .= " + (CASE WHEN vs.nombre_busqueda LIKE '%{$valorCriterio}%' THEN 4 ELSE 0 END)";
                            break;
                    }
                }
                
                // Relevancia adicional por palabras individuales
                foreach ($palabrasTitulo as $palabra) {
                    if (strlen(trim($palabra)) > 2) { // Solo palabras de más de 2 caracteres
                        $palabra = trim($palabra);
                        $relevanciaSQL .= " + (CASE WHEN vt.nombre_busqueda LIKE '%{$palabra}%' THEN 1 ELSE 0 END)";
                    }
                }
                
                foreach ($palabrasCriterio as $palabra) {
                    if (strlen(trim($palabra)) > 2) { // Solo palabras de más de 2 caracteres
                        $palabra = trim($palabra);
                        switch ($criterio) {
                            case 'autor':
                                $relevanciaSQL .= " + (CASE WHEN va.nombre_busqueda LIKE '%{$palabra}%' THEN 2 ELSE 0 END)";
                                break;
                            case 'editorial':
                                $relevanciaSQL .= " + (CASE WHEN ve.nombre_busqueda LIKE '%{$palabra}%' THEN 2 ELSE 0 END)";
                                break;
                            case 'materia':
                                $relevanciaSQL .= " + (CASE WHEN vm.nombre_busqueda LIKE '%{$palabra}%' THEN 2 ELSE 0 END)";
                                break;
                            case 'serie':
                                $relevanciaSQL .= " + (CASE WHEN vs.nombre_busqueda LIKE '%{$palabra}%' THEN 2 ELSE 0 END)";
                                break;
                        }
                    }
                }
                
                $relevanciaSQL .= " ) as relevancia";
                $selectFields[] = DB::raw($relevanciaSQL);
            } else {
                $selectFields[] = DB::raw("0 as relevancia");
            }

            $query->select($selectFields);

            // Filtros adicionales - usar comparación más robusta
            if (!empty($titulo)) {
                $this->aplicarBusquedaFlexible($query, 'vt.nombre_busqueda', $titulo);
            }

            if (!empty($autorFiltro) && count($autorFiltro) > 0) {
                $query->where(function($q) use ($autorFiltro) {
                    foreach ($autorFiltro as $autor) {
                        $q->orWhere(function($subQ) use ($autor) {
                            $this->aplicarBusquedaFlexible($subQ, 'va.nombre_busqueda', $autor);
                            $subQ->whereNotNull('va.nombre_busqueda');
                        });
                    }
                });
            }

            if (!empty($editorialFiltro) && count($editorialFiltro) > 0) {
                $query->where(function($q) use ($editorialFiltro) {
                    foreach ($editorialFiltro as $editorial) {
                        $q->orWhere(function($subQ) use ($editorial) {
                            $this->aplicarBusquedaFlexible($subQ, 've.nombre_busqueda', $editorial);
                            $subQ->whereNotNull('ve.nombre_busqueda');
                        });
                    }
                });
            }

            if (!empty($materiaFiltro) && count($materiaFiltro) > 0) {
                $query->where(function($q) use ($materiaFiltro) {
                    foreach ($materiaFiltro as $materia) {
                        $q->orWhere(function($subQ) use ($materia) {
                            $this->aplicarBusquedaFlexible($subQ, 'vm.nombre_busqueda', $materia);
                            $subQ->whereNotNull('vm.nombre_busqueda');
                        });
                    }
                });
            }

            if (!empty($serieFiltro) && count($serieFiltro) > 0) {
                $query->where(function($q) use ($serieFiltro) {
                    foreach ($serieFiltro as $serie) {
                        $q->orWhere(function($subQ) use ($serie) {
                            $this->aplicarBusquedaFlexible($subQ, 'vs.nombre_busqueda', $serie);
                            $subQ->whereNotNull('vs.nombre_busqueda');
                        });
                    }
                });
            }

            if (!empty($campusFiltro) && count($campusFiltro) > 0) {
                $query->where(function($q) use ($campusFiltro) {
                    foreach ($campusFiltro as $campus) {
                        $q->orWhere(function($subQ) use ($campus) {
                            $this->aplicarBusquedaFlexible($subQ, 'tc.nombre_tb_campus', $campus);
                            $subQ->whereNotNull('tc.nombre_tb_campus');
                        });
                    }
                });
            }

            // Excluir registros con campos principales vacíos para búsquedas amplias
            if (empty($valorCriterio) && empty($titulo) && empty($autorFiltro) && empty($editorialFiltro) && empty($materiaFiltro) && empty($serieFiltro) && empty($campusFiltro)) {
                switch ($criterio) {
                    case 'autor':
                        $query->whereNotNull('va.nombre_busqueda')
                              ->where('va.nombre_busqueda', '!=', '');
                        break;
                    case 'editorial':
                        $query->whereNotNull('ve.nombre_busqueda')
                              ->where('ve.nombre_busqueda', '!=', '');
                        break;
                    case 'materia':
                        $query->whereNotNull('vm.nombre_busqueda')
                              ->where('vm.nombre_busqueda', '!=', '');
                        break;
                    case 'serie':
                        $query->whereNotNull('vs.nombre_busqueda')
                              ->where('vs.nombre_busqueda', '!=', '');
                        break;
                }
            }

            // Aplicar DISTINCT al final para evitar duplicados
            $query->distinct();

            // Aplicar ordenamiento
            $this->aplicarOrdenamiento($query, $sortBy, $sortDirection, $titulo, $valorCriterio);

            // Ejecutar consulta
            $allResults = $query->limit(5000)->get();

            // Almacenar en sesión tras nueva búsqueda
            session([
                'busqueda' => $allResults->toArray(),
                'tipo_busqueda' => $criterio,
                'texto_busqueda' => $texto_procesado,
                'nav_pagina' => $pagina,
                'busq_numrows' => $allResults->count(),
                'ind_busqueda' => 'avanzada',
                'sort_by' => $sortBy,
                'sort_direction' => $sortDirection
            ]);
        } else {
            // Recuperar datos desde sesión
            $allResults = collect(session('busqueda', []));
            
            // Aplicar ordenamiento a los datos de sesión si es diferente al anterior
            $sessionSortBy = session('sort_by', 'relevancia');
            $sessionSortDirection = session('sort_direction', 'desc');
            
            if ($sortBy !== $sessionSortBy || $sortDirection !== $sessionSortDirection) {
                $allResults = $this->aplicarOrdenamientoColeccion($allResults, $sortBy, $sortDirection);
                
                // Actualizar sesión con nuevo ordenamiento
                session([
                    'busqueda' => $allResults->toArray(),
                    'sort_by' => $sortBy,
                    'sort_direction' => $sortDirection
                ]);
            }
            
            session(['nav_pagina' => $pagina]);
        }

        // Obtener todos los valores únicos para los filtros (sin paginación)
        $autores = $allResults->pluck('autor')->filter()->unique()->sort()->values();
        $editoriales = $allResults->pluck('editorial')->filter()->unique()->sort()->values();
        $materias = $allResults->pluck('materia')->filter()->unique()->sort()->values();
        $series = $allResults->pluck('serie')->filter()->unique()->sort()->values();
        $campuses = $allResults->pluck('biblioteca')->filter()->unique()->sort()->values();

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

        // Definir la ruta de acción para los filtros
        $filtros_action_route = route('busqueda-avanzada-resultados');

        return view('BusquedaAvanzadaResultados', compact(
            'resultados',
            'criterio',
            'valorCriterio',
            'titulo',
            'autores',
            'editoriales',
            'materias',
            'series',
            'campuses',
            'filtros_action_route',
            'sortBy',
            'sortDirection'
        ));
    }

    public function mostrarTitulosPorAutor($autor, Request $request)
    {
        $titulo = $request->input('titulo');
        $pagina = $request->input('page', 1);
        
        // Limpiar el texto de entrada eliminando signos de puntuación
        $autor = $this->limpiarTexto(urldecode($autor));
        $titulo = $this->limpiarTexto($titulo);
        
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
                ->where('DSM_AUTOR_EDITOR', '=', $autor);

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
        
        // Limpiar el texto de entrada eliminando signos de puntuación
        $editorial = $this->limpiarTexto(urldecode($editorial));
        $titulo = $this->limpiarTexto($titulo);
        
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
                ->where('DSM_EDITORIAL', '=', $editorial);

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
        
        // Limpiar el texto de entrada eliminando signos de puntuación
        $materia = $this->limpiarTexto(urldecode($materia));
        $titulo = $this->limpiarTexto($titulo);
        
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
                ->where('V_MATERIA.nombre_busqueda', '=', $materia);

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
        
        // Limpiar el texto de entrada eliminando signos de puntuación
        $serie = $this->limpiarTexto(urldecode($serie));
        $titulo = $this->limpiarTexto($titulo);
        
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
                ->where('V_SERIE.nombre_busqueda', '=', $serie);

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

    /**
     * Método para depurar problemas con filtros
     * Ayuda a identificar por qué un filtro no funciona
     */
    public function depurarFiltros(Request $request)
    {
        $criterio = $request->input('criterio', 'autor');
        $filtroSeleccionado = $request->input('filtro_seleccionado', '');
        
        if (empty($filtroSeleccionado)) {
            return response()->json(['error' => 'Debe proporcionar un valor de filtro para depurar']);
        }

        // Procesar el filtro
        $filtroProcessado = $this->procesarFiltro([$filtroSeleccionado]);
        
        // Obtener algunos registros de la tabla correspondiente para comparar
        $query = DB::table('V_TITULO as vt');
        
        switch ($criterio) {
            case 'autor':
                $query->leftJoin('V_AUTOR as va', 'vt.nro_control', '=', 'va.nro_control')
                      ->select('va.nombre_busqueda as campo_valor')
                      ->whereNotNull('va.nombre_busqueda')
                      ->where('va.nombre_busqueda', '!=', '');
                break;
            case 'editorial':
                $query->leftJoin('V_EDITORIAL as ve', 'vt.nro_control', '=', 've.nro_control')
                      ->select('ve.nombre_busqueda as campo_valor')
                      ->whereNotNull('ve.nombre_busqueda')
                      ->where('ve.nombre_busqueda', '!=', '');
                break;
            case 'materia':
                $query->leftJoin('V_MATERIA as vm', 'vt.nro_control', '=', 'vm.nro_control')
                      ->select('vm.nombre_busqueda as campo_valor')
                      ->whereNotNull('vm.nombre_busqueda')
                      ->where('vm.nombre_busqueda', '!=', '');
                break;
            case 'serie':
                $query->leftJoin('V_SERIE as vs', 'vt.nro_control', '=', 'vs.nro_control')
                      ->select('vs.nombre_busqueda as campo_valor')
                      ->whereNotNull('vs.nombre_busqueda')
                      ->where('vs.nombre_busqueda', '!=', '');
                break;
            default:
                return response()->json(['error' => 'Criterio no válido']);
        }

        // Obtener valores similares
        $valoresSimilares = $query->where('campo_valor', 'LIKE', '%' . $filtroSeleccionado . '%')
                                 ->distinct()
                                 ->limit(20)
                                 ->get()
                                 ->pluck('campo_valor')
                                 ->toArray();

        // Buscar coincidencias exactas con diferentes comparaciones
        $coincidenciasExactas = $query->where(function($q) use ($filtroSeleccionado) {
            $q->where('campo_valor', '=', $filtroSeleccionado)
              ->orWhereRaw('TRIM(campo_valor) = ?', [trim($filtroSeleccionado)])
              ->orWhere('campo_valor', 'LIKE', '%' . $filtroSeleccionado . '%');
        })->distinct()->limit(10)->get()->pluck('campo_valor')->toArray();

        return response()->json([
            'filtro_original' => $filtroSeleccionado,
            'filtro_procesado' => $filtroProcessado,
            'criterio' => $criterio,
            'coincidencias_exactas' => $coincidenciasExactas,
            'valores_similares' => $valoresSimilares,
            'longitud_filtro' => strlen($filtroSeleccionado),
            'caracteres_especiales' => preg_match('/[^\w\s\p{L}]/u', $filtroSeleccionado) ? 'SÍ' : 'NO',
            'espacios_inicio_fin' => ($filtroSeleccionado !== trim($filtroSeleccionado)) ? 'SÍ' : 'NO'
        ]);
    }

    /**
     * Obtiene información detallada sobre los filtros disponibles para depuración
     */
    public function analizarFiltrosDisponibles(Request $request)
    {
        $criterio = $request->input('criterio', 'autor');
        $limite = $request->input('limite', 50);
        
        // Configurar timeout para la consulta
        $this->configurarTimeoutBD();

        $query = DB::table('V_TITULO as vt');
        
        switch ($criterio) {
            case 'autor':
                $query->leftJoin('V_AUTOR as va', 'vt.nro_control', '=', 'va.nro_control')
                      ->select(
                          'va.nombre_busqueda as valor',
                          DB::raw('COUNT(*) as cantidad'),
                          DB::raw('LENGTH(va.nombre_busqueda) as longitud'),
                          DB::raw('CASE WHEN va.nombre_busqueda REGEXP "[^a-zA-Z0-9\s\p{L}]" THEN "SÍ" ELSE "NO" END as tiene_especiales')
                      )
                      ->whereNotNull('va.nombre_busqueda')
                      ->where('va.nombre_busqueda', '!=', '')
                      ->groupBy('va.nombre_busqueda');
                break;
            case 'editorial':
                $query->leftJoin('V_EDITORIAL as ve', 'vt.nro_control', '=', 've.nro_control')
                      ->select(
                          've.nombre_busqueda as valor',
                          DB::raw('COUNT(*) as cantidad'),
                          DB::raw('LENGTH(ve.nombre_busqueda) as longitud'),
                          DB::raw('CASE WHEN ve.nombre_busqueda REGEXP "[^a-zA-Z0-9\s\p{L}]" THEN "SÍ" ELSE "NO" END as tiene_especiales')
                      )
                      ->whereNotNull('ve.nombre_busqueda')
                      ->where('ve.nombre_busqueda', '!=', '')
                      ->groupBy('ve.nombre_busqueda');
                break;
            case 'materia':
                $query->leftJoin('V_MATERIA as vm', 'vt.nro_control', '=', 'vm.nro_control')
                      ->select(
                          'vm.nombre_busqueda as valor',
                          DB::raw('COUNT(*) as cantidad'),
                          DB::raw('LENGTH(vm.nombre_busqueda) as longitud'),
                          DB::raw('CASE WHEN vm.nombre_busqueda REGEXP "[^a-zA-Z0-9\s\p{L}]" THEN "SÍ" ELSE "NO" END as tiene_especiales')
                      )
                      ->whereNotNull('vm.nombre_busqueda')
                      ->where('vm.nombre_busqueda', '!=', '')
                      ->groupBy('vm.nombre_busqueda');
                break;
            case 'serie':
                $query->leftJoin('V_SERIE as vs', 'vt.nro_control', '=', 'vs.nro_control')
                      ->select(
                          'vs.nombre_busqueda as valor',
                          DB::raw('COUNT(*) as cantidad'),
                          DB::raw('LENGTH(vs.nombre_busqueda) as longitud'),
                          DB::raw('CASE WHEN vs.nombre_busqueda REGEXP "[^a-zA-Z0-9\s\p{L}]" THEN "SÍ" ELSE "NO" END as tiene_especiales')
                      )
                      ->whereNotNull('vs.nombre_busqueda')
                      ->where('vs.nombre_busqueda', '!=', '')
                      ->groupBy('vs.nombre_busqueda');
                break;
            default:
                return response()->json(['error' => 'Criterio no válido']);
        }

        $resultados = $query->orderBy('cantidad', 'desc')
                           ->limit($limite)
                           ->get();

        // Análisis adicional
        $estadisticas = [
            'total_valores' => $resultados->count(),
            'valores_con_espacios_extra' => $resultados->filter(function($item) {
                return $item->valor !== trim($item->valor);
            })->count(),
            'valores_con_caracteres_especiales' => $resultados->where('tiene_especiales', 'SÍ')->count(),
            'longitud_promedio' => $resultados->avg('longitud'),
            'longitud_minima' => $resultados->min('longitud'),
            'longitud_maxima' => $resultados->max('longitud')
        ];

        return response()->json([
            'criterio' => $criterio,
            'estadisticas' => $estadisticas,
            'muestra_valores' => $resultados->toArray()
        ]);
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

    /**
     * Procesa un filtro que puede venir como array o string separado por comas
     * @param mixed $filtro El filtro a procesar
     * @return array Array con los valores del filtro
     */
    private function procesarFiltro($filtro)
    {
        if (empty($filtro)) {
            return [];
        }

        if (is_array($filtro)) {
            // Si ya es un array, filtrar valores vacíos y limpiar
            $resultado = array_filter($filtro, function($valor) {
                return !empty(trim($valor)) && $valor !== null;
            });
            // Limpiar espacios y normalizar PERO NO aplicar limpiarTexto para mantener compatibilidad
            return array_values(array_map(function($valor) {
                return trim($valor);
            }, $resultado));
        }

        if (is_string($filtro)) {
            // Si es un string, dividir por comas y filtrar valores vacíos
            $valores = explode(',', $filtro);
            $resultado = array_filter(array_map('trim', $valores), function($valor) {
                return !empty($valor) && $valor !== null;
            });
            // Solo limpiar espacios, no aplicar limpiarTexto
            return array_values($resultado);
        }

        return [];
    }

    /**
     * Aplica ordenamiento a la query de base de datos
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $sortBy
     * @param string $sortDirection
     * @param string $titulo
     * @param string $valorCriterio
     */
    private function aplicarOrdenamiento($query, $sortBy, $sortDirection, $titulo = '', $valorCriterio = '')
    {
        // Validar dirección de ordenamiento
        $sortDirection = in_array(strtolower($sortDirection), ['asc', 'desc']) ? strtolower($sortDirection) : 'desc';
        
        // Definir los campos válidos para ordenamiento (solo título y autor)
        $camposValidos = [
            'titulo' => 'vt.nombre_busqueda',
            'autor' => 'va.nombre_busqueda',
            'relevancia' => 'relevancia'
        ];

        // Si se ordena por relevancia y hay criterios de búsqueda, usar relevancia
        if ($sortBy === 'relevancia' && (!empty($titulo) || !empty($valorCriterio))) {
            $query->orderBy('relevancia', $sortDirection);
        } 
        // Si se ordena por título o autor
        elseif (in_array($sortBy, ['titulo', 'autor']) && array_key_exists($sortBy, $camposValidos)) {
            $campo = $camposValidos[$sortBy];
            $query->orderBy($campo, $sortDirection);
            
            // Agregar ordenamiento secundario por título para consistencia (si no se está ordenando por título)
            if ($sortBy !== 'titulo') {
                $query->orderBy('vt.nombre_busqueda', 'asc');
            }
        }
        // Fallback al ordenamiento por relevancia si no hay campo válido
        else {
            if (!empty($titulo) || !empty($valorCriterio)) {
                $query->orderBy('relevancia', 'desc');
            } else {
                $query->orderBy('vt.nombre_busqueda', 'asc');
            }
        }
    }

    /**
     * Aplica ordenamiento a una colección de resultados
     * @param \Illuminate\Support\Collection $collection
     * @param string $sortBy
     * @param string $sortDirection
     * @return \Illuminate\Support\Collection
     */
    private function aplicarOrdenamientoColeccion($collection, $sortBy, $sortDirection)
    {
        // Validar dirección de ordenamiento
        $sortDirection = in_array(strtolower($sortDirection), ['asc', 'desc']) ? strtolower($sortDirection) : 'desc';
        
        // Definir los campos válidos para ordenamiento (solo título y autor)
        $camposValidos = [
            'titulo' => 'titulo',
            'autor' => 'autor',
            'relevancia' => 'relevancia'
        ];

        if (in_array($sortBy, ['titulo', 'autor', 'relevancia']) && array_key_exists($sortBy, $camposValidos)) {
            $campo = $camposValidos[$sortBy];
            
            if ($sortDirection === 'asc') {
                return $collection->sortBy(function($item) use ($campo) {
                    if (is_array($item)) {
                        return strtolower($item[$campo] ?? '');
                    } elseif (is_object($item)) {
                        return strtolower($item->{$campo} ?? '');
                    }
                    return '';
                })->values();
            } else {
                return $collection->sortByDesc(function($item) use ($campo) {
                    if (is_array($item)) {
                        return strtolower($item[$campo] ?? '');
                    } elseif (is_object($item)) {
                        return strtolower($item->{$campo} ?? '');
                    }
                    return '';
                })->values();
            }
        }

        // Fallback al ordenamiento por título si no hay campo válido
        return $collection->sortBy(function($item) {
            if (is_array($item)) {
                return strtolower($item['titulo'] ?? '');
            } elseif (is_object($item)) {
                return strtolower($item->titulo ?? '');
            }
            return '';
        })->values();
    }

    /**
     * Limpia el texto de entrada eliminando signos de puntuación
     * Mantiene solo letras, números y espacios
     * @param string $texto El texto a limpiar
     * @return string El texto limpio
     */
    private function limpiarTexto($texto)
    {
        if (empty($texto)) {
            return '';
        }

        // Eliminar todos los caracteres que no sean letras, números o espacios
        // Mantener caracteres acentuados y especiales del español
        $textoLimpio = preg_replace('/[^\w\s\p{L}]/u', '', $texto);
        
        // Eliminar espacios múltiples y limpiar espacios al inicio y final
        $textoLimpio = preg_replace('/\s+/', ' ', trim($textoLimpio));
        
        return $textoLimpio;
    }

    /**
     * Aplica búsqueda flexible que encuentra coincidencias independientemente del orden de las palabras
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $campo El campo de la base de datos
     * @param string $valorBusqueda El valor a buscar
     */
    private function aplicarBusquedaFlexible($query, $campo, $valorBusqueda)
    {
        if (empty($valorBusqueda)) {
            return;
        }

        // Dividir el valor de búsqueda en palabras (removiendo comas y otros signos)
        $palabras = array_filter(
            preg_split('/[\s,]+/', $valorBusqueda), 
            function($palabra) {
                return strlen(trim($palabra)) > 1; // Solo palabras de más de 1 carácter
            }
        );

        if (empty($palabras)) {
            return;
        }

        $query->where(function($subQuery) use ($campo, $valorBusqueda, $palabras) {
            // 1. Búsqueda exacta (sin limpiar para mantener compatibilidad)
            $subQuery->where($campo, '=', $valorBusqueda);
            
            // 2. Búsqueda exacta limpiando espacios
            $subQuery->orWhereRaw("TRIM({$campo}) = ?", [trim($valorBusqueda)]);
            
            // 3. Búsqueda exacta con texto limpio (sin signos de puntuación)
            $valorLimpio = implode(' ', $palabras);
            if ($valorLimpio !== $valorBusqueda) {
                $subQuery->orWhere($campo, '=', $valorLimpio);
                $subQuery->orWhereRaw("TRIM({$campo}) = ?", [trim($valorLimpio)]);
            }
            
            // 4. Búsqueda con LIKE para coincidencias parciales
            $subQuery->orWhere($campo, 'LIKE', "%{$valorBusqueda}%");
            
            // 5. Búsqueda con LIKE para texto limpio
            if ($valorLimpio !== $valorBusqueda) {
                $subQuery->orWhere($campo, 'LIKE', "%{$valorLimpio}%");
            }
            
            // 6. Búsqueda que contenga todas las palabras individuales
            $subQuery->orWhere(function($wordQuery) use ($campo, $palabras) {
                foreach ($palabras as $palabra) {
                    $palabra = trim($palabra);
                    if (strlen($palabra) > 1) {
                        $wordQuery->where($campo, 'LIKE', "%{$palabra}%");
                    }
                }
            });
            
            // 7. Para nombres de 2 palabras, probar diferentes formatos comunes
            if (count($palabras) == 2) {
                $palabra1 = trim($palabras[0]);
                $palabra2 = trim($palabras[1]);
                
                // Formato: "Apellido, Nombre"
                $subQuery->orWhere($campo, 'LIKE', "%{$palabra2}, {$palabra1}%");
                
                // Formato: "Apellido Nombre" (orden inverso)
                $subQuery->orWhere($campo, 'LIKE', "%{$palabra2} {$palabra1}%");
                
                // Formato: "Apellido,Nombre" (sin espacio después de coma)
                $subQuery->orWhere($campo, 'LIKE', "%{$palabra2},{$palabra1}%");
                
                // También buscar con cualquier signo de puntuación entre las palabras
                $subQuery->orWhere(function($punctQuery) use ($campo, $palabra1, $palabra2) {
                    $punctQuery->where($campo, 'LIKE', "%{$palabra1}%")
                               ->where($campo, 'LIKE', "%{$palabra2}%");
                });
            }
            
            // 8. Para nombres de 3 o más palabras, buscar todas las combinaciones posibles
            if (count($palabras) > 2) {
                // Buscar que contenga todas las palabras en cualquier orden
                $subQuery->orWhere(function($multiWordQuery) use ($campo, $palabras) {
                    foreach ($palabras as $palabra) {
                        $palabra = trim($palabra);
                        if (strlen($palabra) > 1) {
                            $multiWordQuery->where($campo, 'LIKE', "%{$palabra}%");
                        }
                    }
                });
            }
        });
    }
}