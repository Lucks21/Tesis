<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusquedaSimpleController extends Controller
{
    public function mostrarFormulario(Request $request)
    {
        // Mostrar el formulario de búsqueda simple
        return view('BusquedaView');
    }

    /**
     * Búsqueda usando el stored procedure sp_WEB_detalle_busqueda
     */
    public function buscarConStoredProcedure(Request $request)
    {
        // Log para debug
        \Log::info('Iniciando búsqueda con stored procedure', [
            'request_all' => $request->all(),
            'method' => $request->method()
        ]);

        // Validación flexible para diferentes fuentes de parámetros
        $request->validate([
            'busqueda' => 'nullable|string|max:255',
            'termino' => 'nullable|string|max:255',
            'tipo_busqueda' => 'nullable|integer|min:1|max:6',
            'tipo' => 'nullable|integer|min:1|max:6',
        ]);

        $textoBusqueda = $request->input('busqueda');
        $termino = $request->input('termino');
        $tipoBusqueda = $request->input('tipo_busqueda', 3);
        $tipo = $request->input('tipo');
        $valorSeleccionado = $request->input('valor_seleccionado');
        $verTitulos = $request->input('ver_titulos');
        $pagina = $request->input('page', 1);
        $porPagina = 10;

        // Log de parámetros procesados
        \Log::info('Parámetros procesados', [
            'textoBusqueda' => $textoBusqueda,
            'termino' => $termino,
            'tipoBusqueda' => $tipoBusqueda,
            'tipo' => $tipo,
            'valorSeleccionado' => $valorSeleccionado,
            'verTitulos' => $verTitulos
        ]);

        // Verificar que al menos uno de los parámetros de búsqueda esté presente
        if (!$request->filled('busqueda') && !$request->filled('termino')) {
            \Log::warning('No se proporcionó término de búsqueda');
            return redirect()->back()->withErrors(['error' => 'Debe proporcionar un término de búsqueda']);
        }
        
        try {
            // Usar textoBusqueda si no hay termino específico
            $busquedaFinal = $termino ?: $textoBusqueda;
            $tipoFinal = $tipo ?: $tipoBusqueda;
            
            \Log::info('Parámetros finales', [
                'busquedaFinal' => $busquedaFinal,
                'tipoFinal' => $tipoFinal,
                'verTitulos' => $verTitulos,
                'valorSeleccionado' => $valorSeleccionado
            ]);
            
            // CASO 1: Si es búsqueda por título (tipo 3) y es búsqueda inicial, mostrar títulos directamente
            if ($tipoFinal == 3 && !$verTitulos && !$valorSeleccionado) {
                \Log::info('Ejecutando buscarTitulos (tipo 3) - búsqueda inicial por título');
                return $this->buscarTitulos($busquedaFinal, $tipoFinal, $request);
            }
            
            // CASO 2: Clic en "Ver títulos" - viene con termino, tipo y ver_titulos
            if (($verTitulos && $termino && $tipo) || ($termino && $tipo && !$textoBusqueda && $valorSeleccionado)) {
                \Log::info('Ejecutando mostrarTitulosAsociados - clic en ver títulos');
                return $this->mostrarTitulosAsociados($termino, $tipo, $request);
            }
            
            // CASO 3: Si hay un valor seleccionado, mostrar títulos asociados
            if ($valorSeleccionado && !$verTitulos) {
                \Log::info('Ejecutando mostrarTitulosAsociados con valor seleccionado');
                return $this->mostrarTitulosAsociados($valorSeleccionado, $tipoBusqueda, $request);
            }
            
            // CASO 4: Para otros tipos (autor, materia, editorial, serie, dewey), buscar elementos primero
            if ($tipoFinal != 3) {
                \Log::info('Ejecutando buscarElementos - tipos 1,2,4,5,6');
                return $this->buscarElementos($busquedaFinal, $tipoFinal, $request);
            }
            
            // CASO 5: Fallback para título si algo sale mal
            \Log::info('Fallback: Ejecutando buscarTitulos');
            return $this->buscarTitulos($busquedaFinal, $tipoFinal, $request);

        } catch (\Exception $e) {
            \Log::error('Error en buscarConStoredProcedure', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // En caso de error, retornar vista con resultados vacíos
            $busquedaFinal = $termino ?: $textoBusqueda;
            $tipoFinal = $tipo ?: $tipoBusqueda;
            return $this->retornarVistaError($e->getMessage(), $busquedaFinal, $tipoFinal, $request);
        }
    }

    /**
     * Buscar títulos directamente (para búsqueda por título)
     */
    private function buscarTitulos($textoBusqueda, $tipoBusqueda, $request)
    {
        $pagina = $request->input('page', 1);
        $porPagina = 10;
        
        // PROCESAR FILTROS - Reutilizar lógica de búsqueda avanzada
        $autorFiltro = $this->procesarFiltro($request->input('autor', []));
        $editorialFiltro = $this->procesarFiltro($request->input('editorial', []));
        $campusFiltro = $this->procesarFiltro($request->input('campus', []));
        $materiaFiltro = $this->procesarFiltro($request->input('materia', []));
        $serieFiltro = $this->procesarFiltro($request->input('serie', []));
        
        // Primero intentar el stored procedure
        $resultadosBrutos = DB::select('EXEC sp_WEB_detalle_busqueda ?, ?', [
            $textoBusqueda,
            $tipoBusqueda
        ]);
        
        \Log::info('buscarTitulos: Resultados del SP', [
            'count' => count($resultadosBrutos)
        ]);
        
        // Enriquecer los datos si vienen del SP
        if (!empty($resultadosBrutos)) {
            \Log::info('buscarTitulos: Enriqueciendo datos del SP');
            $resultadosEnriquecidos = [];
            foreach ($resultadosBrutos as $resultado) {
                $enriquecido = $this->enriquecerDatosDetalle($resultado);
                $resultadosEnriquecidos[] = $enriquecido;
            }
            $resultadosBrutos = $resultadosEnriquecidos;
        }
        
        // Si el SP no devuelve resultados, usar consulta directa para títulos
        if (empty($resultadosBrutos) && $tipoBusqueda == 3) {
            
            // Buscar directamente en la vista V_TITULO
            $palabras = explode(' ', trim($textoBusqueda));
            $condiciones = [];
            $parametros = [];
            
            foreach ($palabras as $palabra) {
                if (strlen(trim($palabra)) > 2) {
                    $condiciones[] = "nombre_busqueda LIKE ?";
                    $parametros[] = "%{$palabra}%";
                }
            }
            
            if (empty($condiciones)) {
                $condiciones[] = "nombre_busqueda LIKE ?";
                $parametros[] = "%{$textoBusqueda}%";
            }
            
            // Primero obtener los nro_control que coinciden
            $sqlTitulos = "
                SELECT nro_control, nombre_busqueda 
                FROM V_TITULO 
                WHERE " . implode(' AND ', $condiciones) . " 
                ORDER BY nombre_busqueda
            ";
            
            try {
                $titulosEncontrados = DB::select($sqlTitulos, $parametros);
                \Log::info('buscarTitulos: Títulos encontrados', [
                    'count' => count($titulosEncontrados)
                ]);
                
                // Ahora obtener detalles completos usando el SP para cada título encontrado
                $resultadosBrutos = [];
                foreach ($titulosEncontrados as $titulo) {
                    // Intentar obtener detalles con el SP usando el nombre exacto del título
                    $detalles = DB::select('EXEC sp_WEB_detalle_busqueda ?, ?', [
                        $titulo->nombre_busqueda,
                        3
                    ]);
                    
                    if (!empty($detalles)) {
                        // Enriquecer los datos con información adicional de las vistas
                        foreach ($detalles as $detalle) {
                            $detalleEnriquecido = $this->enriquecerDatosDetalle($detalle);
                            $resultadosBrutos[] = $detalleEnriquecido;
                        }
                    } else {
                        // Si el SP no funciona, crear un registro básico y enriquecerlo
                        $registroBasico = new \stdClass();
                        $registroBasico->nro_control = $titulo->nro_control;
                        $registroBasico->nombre_busqueda = $titulo->nombre_busqueda;
                        $registroBasico->autor = null;
                        $registroBasico->editorial = null;
                        $registroBasico->materia = null;
                        $registroBasico->serie = null;
                        $registroBasico->biblioteca = null;
                        $registroBasico->publicacion = null;
                        $registroBasico->tipo = null;
                        $registroBasico->isbn = null;
                        $registroBasico->signatura = null;
                        
                        $registroEnriquecido = $this->enriquecerDatosDetalle($registroBasico);
                        $resultadosBrutos[] = $registroEnriquecido;
                    }
                }
                
                \Log::info('buscarTitulos: Resultados finales procesados', [
                    'count' => count($resultadosBrutos)
                ]);
                
            } catch (\Exception $e) {
                \Log::error('buscarTitulos: Error en consulta directa', [
                    'error' => $e->getMessage()
                ]);
                $resultadosBrutos = [];
            }
        }

        // Procesar los resultados para el formato esperado por la vista
        $resultadosProcesados = collect($resultadosBrutos)->map(function ($item) {
            return (object) [
                'nro_control' => $item->nro_control,
                'titulo' => $item->nombre_busqueda,
                'nombre_autor' => $item->autor,
                'nombre_editorial' => $item->editorial ?? null,
                'nombre_materia' => $item->materia ?? null,
                'nombre_serie' => $item->serie ?? null,
                'dewey' => $item->dewey ?? null,
                'biblioteca' => $item->biblioteca ?? null,
                'anio_publicacion' => $item->publicacion,
                'tipo_material' => $item->tipo,
                'isbn' => $item->isbn ?? null,
                'signatura_topografica' => $item->signatura ?? null,
            ];
        });

        // Obtener datos únicos para filtros
        $autores = collect($resultadosBrutos)->pluck('autor')->filter()->unique()->sort()->values();
        $años = collect($resultadosBrutos)->pluck('publicacion')->filter()->unique()->sort()->values();
        $tipos = collect($resultadosBrutos)->pluck('tipo')->filter()->unique()->sort()->values();
        
        // Obtener editoriales, materias y series desde los resultados
        $editoriales = collect($resultadosBrutos)->pluck('editorial')->filter()->unique()->sort()->values();
        $materias = collect($resultadosBrutos)->pluck('materia')->filter()->unique()->sort()->values();
        $series = collect($resultadosBrutos)->pluck('serie')->filter()->unique()->sort()->values();      
        $campuses = collect($resultadosBrutos)->pluck('biblioteca')->filter()->unique()->sort()->values();

        // Debug: Log de datos de filtros
        \Log::info('Datos de filtros cargados', [
            'autores_count' => $autores->count(),
            'editoriales_count' => $editoriales->count(),
            'materias_count' => $materias->count(),
            'series_count' => $series->count(),
            'campuses_count' => $campuses->count(),
            'autores_sample' => $autores->take(3)->toArray(),
            'editoriales_sample' => $editoriales->take(3)->toArray()
        ]);

        // Aplicar filtros si están presentes
        $resultadosProcesados = $this->aplicarFiltros($resultadosProcesados, $request);

        // Aplicar ordenamiento
        $orden = $request->input('orden', 'asc');
        $resultadosProcesados = $this->aplicarOrdenamiento($resultadosProcesados, $orden);

        // Crear paginación
        $totalResultados = $resultadosProcesados->count();
        $resultadosPaginados = $resultadosProcesados->slice(($pagina - 1) * $porPagina, $porPagina);

        $resultados = new \Illuminate\Pagination\LengthAwarePaginator(
            $resultadosPaginados,
            $totalResultados,
            $porPagina,
            $pagina,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );

        // Preparar datos para la vista (compatible con vista avanzada)
        // $resultados ya contiene la paginación de títulos
        $criterio = 'busqueda_simple';
        $valorCriterio = $textoBusqueda;
        $titulo = $textoBusqueda;
        $orden = $request->input('orden', 'asc');
        $filtros_action_route = route('busqueda.sp');

        return view('BusquedaSimpleResultados', compact(
            'resultados',
            'criterio',
            'valorCriterio',
            'titulo',
            'orden',
            'autores',
            'editoriales',
            'materias',
            'series',
            'campuses',
            'filtros_action_route'
        ));
    }

    /**
     * Buscar elementos (autores, materias, etc.) - Primer paso
     */
    private function buscarElementos($textoBusqueda, $tipoBusqueda, $request)
    {
        $pagina = $request->input('page', 1);
        $porPagina = 10;
        
        $vista = $this->getVistaPorTipo($tipoBusqueda);
        
        if (!$vista) {
            throw new \Exception('Tipo de búsqueda no válido');
        }

        // Buscar elementos que coincidan con el texto (búsqueda más flexible)
        // Dividir el término de búsqueda en palabras para buscar coincidencias parciales
        $palabras = explode(' ', trim($textoBusqueda));
        $condiciones = [];
        $parametros = [];
        
        foreach ($palabras as $palabra) {
            if (strlen(trim($palabra)) > 2) { // Solo buscar palabras de más de 2 caracteres
                $condiciones[] = "nombre_busqueda LIKE ?";
                $parametros[] = "%{$palabra}%";
            }
        }
        
        if (empty($condiciones)) {
            // Si no hay palabras válidas, usar búsqueda simple
            $condiciones[] = "nombre_busqueda LIKE ?";
            $parametros[] = "%{$textoBusqueda}%";
        }
        
        $sql = "
            SELECT DISTINCT nombre_busqueda 
            FROM {$vista} 
            WHERE " . implode(' AND ', $condiciones) . " 
            ORDER BY nombre_busqueda
        ";
        
        $elementos = DB::select($sql, $parametros);

        // Convertir a objetos para la vista
        $elementosProcesados = collect($elementos)->map(function ($item) use ($tipoBusqueda) {
            return (object) [
                'nombre' => $item->nombre_busqueda,
                'tipo' => $tipoBusqueda,
                'url_titulos' => route('busqueda.sp', [
                    'busqueda' => request('busqueda'),
                    'tipo_busqueda' => $tipoBusqueda,
                    'valor_seleccionado' => $item->nombre_busqueda
                ])
            ];
        });

        // Crear paginación
        $totalResultados = $elementosProcesados->count();
        $resultadosPaginados = $elementosProcesados->slice(($pagina - 1) * $porPagina, $porPagina);

        $resultados = new \Illuminate\Pagination\LengthAwarePaginator(
            $resultadosPaginados,
            $totalResultados,
            $porPagina,
            $pagina,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );

        // Datos para la vista de elementos
        $criterio = $this->getTipoBusquedaNombre($tipoBusqueda);
        $valorCriterio = $textoBusqueda;

        return view('BusquedaSimpleElementos', compact(
            'resultados',
            'criterio',
            'valorCriterio',
            'tipoBusqueda'
        ));
    }

    /**
     * Mostrar títulos asociados a un elemento seleccionado - Segundo paso
     */
    private function mostrarTitulosAsociados($valorSeleccionado, $tipoBusqueda, $request)
    {
        try {
            $pagina = $request->input('page', 1);
            $porPagina = 10;
            
            // Ejecutar el stored procedure con el valor exacto seleccionado
            $resultadosBrutos = DB::select('EXEC sp_WEB_detalle_busqueda ?, ?', [
                $valorSeleccionado,
                $tipoBusqueda
            ]);
            
            // DEBUG: Agregar información de debug para ver la estructura real
            if (count($resultadosBrutos) > 0) {
                $primerElemento = $resultadosBrutos[0];
                \Log::info('DEBUG: Estructura del resultado del SP:', [
                    'campos' => get_object_vars($primerElemento),
                    'valor_seleccionado' => $valorSeleccionado,
                    'tipo_busqueda' => $tipoBusqueda
                ]);
            }
            
            // Enriquecer los datos del SP con información adicional
            if (!empty($resultadosBrutos)) {
                \Log::info('mostrarTitulosAsociados: Enriqueciendo datos del SP');
                foreach ($resultadosBrutos as &$resultado) {
                    \Log::info('mostrarTitulosAsociados: Antes de enriquecer', [
                        'nro_control' => $resultado->nro_control ?? 'null',
                        'editorial' => $resultado->editorial ?? 'null'
                    ]);
                    $enriquecido = $this->enriquecerDatosDetalle($resultado);
                    \Log::info('mostrarTitulosAsociados: Después de enriquecer', [
                        'nro_control' => $enriquecido->nro_control ?? 'null',
                        'editorial' => $enriquecido->editorial ?? 'null'
                    ]);
                    $resultado = $enriquecido;
                }
                unset($resultado); // Romper la referencia
            }
            
            $resultadosProcesados = collect($resultadosBrutos)->map(function ($item) {
                return (object) [
                    'nro_control' => $this->getValue($item, ['nro_control', 'numero_control']),
                    'titulo' => $this->getValue($item, ['nombre_busqueda', 'titulo']),
                    'nombre_autor' => $this->getValue($item, ['autor', 'nombre_autor']),
                    'nombre_editorial' => $this->getValue($item, ['editorial', 'nombre_editorial']),
                    'nombre_materia' => $this->getValue($item, ['materia', 'nombre_materia']),
                    'nombre_serie' => $this->getValue($item, ['serie', 'nombre_serie']),
                    'dewey' => $this->getValue($item, ['dewey', 'numero_dewey']),
                    'biblioteca' => $this->getValue($item, ['biblioteca', 'nombre_biblioteca']),
                    'anio_publicacion' => $this->getValue($item, ['publicacion', 'anio_publicacion', 'año_publicacion']),
                    'tipo_material' => $this->getValue($item, ['tipo', 'tipo_material']),
                    'isbn' => $this->getValue($item, ['isbn']),
                    'signatura_topografica' => $this->getValue($item, ['signatura', 'signatura_topografica']),
                ];
            });

            // Obtener datos únicos para filtros desde los datos enriquecidos
            $autores = collect($resultadosBrutos)->pluck('autor')->filter()->unique()->sort()->values();
            $editoriales = collect($resultadosBrutos)->pluck('editorial')->filter()->unique()->sort()->values();
            $materias = collect($resultadosBrutos)->pluck('materia')->filter()->unique()->sort()->values();
            $series = collect($resultadosBrutos)->pluck('serie')->filter()->unique()->sort()->values();
            $campuses = collect($resultadosBrutos)->pluck('biblioteca')->filter()->unique()->sort()->values();

            // Aplicar filtros si están presentes
            $resultadosProcesados = $this->aplicarFiltros($resultadosProcesados, $request);

            // Aplicar ordenamiento
            $orden = $request->input('orden', 'asc');
            $resultadosProcesados = $this->aplicarOrdenamiento($resultadosProcesados, $orden);

            // Crear paginación
            $totalResultados = $resultadosProcesados->count();
            $resultadosPaginados = $resultadosProcesados->slice(($pagina - 1) * $porPagina, $porPagina);

            $resultados = new \Illuminate\Pagination\LengthAwarePaginator(
                $resultadosPaginados,
                $totalResultados,
                $porPagina,
                $pagina,
                [
                    'path' => $request->url(),
                    'query' => $request->query()
                ]
            );

            // Preparar datos para la vista (compatible con vista avanzada)
            // $resultados ya contiene la paginación de títulos
            $criterio = 'busqueda_simple';
            $valorCriterio = $valorSeleccionado;
            $titulo = $valorSeleccionado;
            $orden = $request->input('orden', 'asc');
            $filtros_action_route = route('busqueda.sp');

            return view('BusquedaSimpleResultados', compact(
                'resultados',
                'criterio',
                'valorCriterio',
                'titulo',
                'orden',
                'autores',
                'editoriales',
                'materias',
                'series',
                'campuses',
                'filtros_action_route'
            ));
        } catch (\Exception $e) {
            // Crear vista de error simple
            $criterio = $this->getTipoBusquedaNombre($tipoBusqueda);
            $valorCriterio = $valorSeleccionado;
            $titulo = $valorSeleccionado;
            $orden = $request->input('orden', 'asc');
            $resultados = collect();
            $autores = collect();
            $editoriales = collect();
            $materias = collect();
            $series = collect();
            $años = collect();
            $tipos = collect();
            $campuses = collect();
            $filtros_action_route = route('busqueda.sp');
            
            session()->flash('error', 'Error en la búsqueda: ' . $e->getMessage());
            
            return view('BusquedaSimpleResultados', compact(
                'resultados',
                'autores',
                'editoriales',
                'materias', 
                'series',
                'años',
                'tipos',
                'campuses',
                'criterio',
                'valorCriterio',
                'titulo',
                'orden',
                'filtros_action_route'
            ));
        }
    }

    /**
     * Aplicar filtros a los resultados
     */
    private function aplicarFiltros($resultados, $request)
    {
        $filtrados = $resultados;

        // Procesar filtros para manejar tanto arrays como strings separadas por comas
        $autorFiltro = $this->procesarFiltro($request->input('autor', []));
        $editorialFiltro = $this->procesarFiltro($request->input('editorial', []));
        $materiaFiltro = $this->procesarFiltro($request->input('materia', []));
        $serieFiltro = $this->procesarFiltro($request->input('serie', []));
        $campusFiltro = $this->procesarFiltro($request->input('campus', []));

        \Log::info('aplicarFiltros: Filtros recibidos', [
            'autor' => $autorFiltro,
            'editorial' => $editorialFiltro,
            'materia' => $materiaFiltro,
            'serie' => $serieFiltro,
            'campus' => $campusFiltro,
            'total_antes' => $resultados->count()
        ]);

        // Filtrar por autor - usar comparación exacta como en búsqueda avanzada
        if (!empty($autorFiltro) && count($autorFiltro) > 0) {
            $filtrados = $filtrados->filter(function ($item) use ($autorFiltro) {
                $autor = $item->nombre_autor ?? $item->autor ?? null;
                if (!$autor) return false;
                
                // Comparación exacta tras limpiar espacios
                foreach ($autorFiltro as $filtroAutor) {
                    if (trim($autor) === trim($filtroAutor)) {
                        return true;
                    }
                }
                return false;
            });
        }

        // Filtrar por editorial - usar comparación exacta como en búsqueda avanzada
        if (!empty($editorialFiltro) && count($editorialFiltro) > 0) {
            $filtrados = $filtrados->filter(function ($item) use ($editorialFiltro) {
                $editorial = $item->nombre_editorial ?? $item->editorial ?? null;
                if (!$editorial) return false;
                
                // Comparación exacta tras limpiar espacios
                foreach ($editorialFiltro as $filtroEditorial) {
                    if (trim($editorial) === trim($filtroEditorial)) {
                        return true;
                    }
                }
                return false;
            });
        }

        // Filtrar por materia - usar comparación exacta como en búsqueda avanzada
        if (!empty($materiaFiltro) && count($materiaFiltro) > 0) {
            $filtrados = $filtrados->filter(function ($item) use ($materiaFiltro) {
                $materia = $item->nombre_materia ?? $item->materia ?? null;
                if (!$materia) return false;
                
                // Comparación exacta tras limpiar espacios
                foreach ($materiaFiltro as $filtroMateria) {
                    if (trim($materia) === trim($filtroMateria)) {
                        return true;
                    }
                }
                return false;
            });
        }

        // Filtrar por serie - usar comparación exacta como en búsqueda avanzada
        if (!empty($serieFiltro) && count($serieFiltro) > 0) {
            $filtrados = $filtrados->filter(function ($item) use ($serieFiltro) {
                $serie = $item->nombre_serie ?? $item->serie ?? null;
                if (!$serie) return false;
                
                // Comparación exacta tras limpiar espacios
                foreach ($serieFiltro as $filtroSerie) {
                    if (trim($serie) === trim($filtroSerie)) {
                        return true;
                    }
                }
                return false;
            });
        }

        // Filtrar por campus/biblioteca - usar comparación exacta como en búsqueda avanzada
        if (!empty($campusFiltro) && count($campusFiltro) > 0) {
            $filtrados = $filtrados->filter(function ($item) use ($campusFiltro) {
                $biblioteca = $item->biblioteca ?? null;
                if (!$biblioteca) return false;
                
                // Comparación exacta tras limpiar espacios
                foreach ($campusFiltro as $filtroCampus) {
                    if (trim($biblioteca) === trim($filtroCampus)) {
                        return true;
                    }
                }
                return false;
            });
        }

        \Log::info('aplicarFiltros: Resultado', [
            'total_despues' => $filtrados->count()
        ]);

        return $filtrados;
    }

    /**
     * Retornar vista con error
     */
    private function retornarVistaError($mensaje, $textoBusqueda, $tipoBusqueda, $request)
    {
        $pagina = $request->input('page', 1);
        $porPagina = 10;
        
        $resultados = new \Illuminate\Pagination\LengthAwarePaginator(
            collect(),
            0,
            $porPagina,
            $pagina,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );

        $criterio = $this->getTipoBusquedaNombre($tipoBusqueda);
        $valorCriterio = $textoBusqueda;
        $titulo = $textoBusqueda;
        $orden = $request->input('orden', 'asc');
        $autores = collect();
        $editoriales = collect();
        $materias = collect();
        $series = collect();
        $campuses = collect();
        $filtros_action_route = route('busqueda.sp');

        session()->flash('error', 'Error en la búsqueda: ' . $mensaje);

        return view('BusquedaSimpleResultados', compact(
            'resultados',
            'criterio',
            'valorCriterio',
            'titulo',
            'orden',
            'autores',
            'editoriales',
            'materias',
            'series',
            'campuses',
            'filtros_action_route'
        ));
    }

    /**
     * Obtener el nombre del tipo de búsqueda
     */
    private function getTipoBusquedaNombre($tipo)
    {
        $tipos = [
            1 => 'Autor',
            2 => 'Materia',
            3 => 'Título',
            4 => 'Editorial',
            5 => 'Serie'
        ];

        return $tipos[$tipo] ?? 'Desconocido';
    }

    /**
     * Método para obtener sugerencias de búsqueda según el tipo
     */
    public function obtenerSugerenciasBusqueda(Request $request)
    {
        $tipoBusqueda = $request->input('tipo', 3);
        $limite = $request->input('limite', 10);

        $vista = $this->getVistaPorTipo($tipoBusqueda);
        
        if (!$vista) {
            return response()->json(['sugerencias' => []]);
        }

        try {
            $sugerencias = DB::select("
                SELECT TOP {$limite} nombre_busqueda 
                FROM {$vista} 
                ORDER BY nombre_busqueda
            ");

            return response()->json([
                'sugerencias' => collect($sugerencias)->pluck('nombre_busqueda')
            ]);

        } catch (\Exception $e) {
            return response()->json(['sugerencias' => []]);
        }
    }

    /**
     * Obtener la vista correspondiente según el tipo de búsqueda
     */
    private function getVistaPorTipo($tipo)
    {
        $vistas = [
            1 => 'V_AUTOR',
            2 => 'V_MATERIA',
            3 => 'V_TITULO',
            4 => 'V_EDITORIAL',
            5 => 'V_SERIE',
            6 => 'V_DEWEY'
        ];

        return $vistas[$tipo] ?? null;
    }

    /**
     * Función robusta para obtener valores de objetos con diferentes nombres de campo
     */
    private function getValue($item, $fields)
    {
        if (is_string($fields)) {
            $fields = [$fields];
        }
        
        foreach ($fields as $field) {
            if (property_exists($item, $field) && $item->$field !== null) {
                return $item->$field;
            }
        }
        
        return null;
    }

    /**
     * Aplicar ordenamiento a los resultados
     */
    private function aplicarOrdenamiento($resultados, $orden)
    {
        // Simplificado: solo ordenamiento por título
        if ($orden === 'desc' || $orden === 'titulo_desc') {
            return $resultados->sortByDesc('titulo');
        } else {
            // Por defecto: ascendente (asc, titulo_asc, o cualquier otro valor)
            return $resultados->sortBy('titulo');
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
            // Limpiar espacios y normalizar
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
            return array_values($resultado);
        }

        return [];
    }

    /**
     * Enriquecer datos del detalle con información adicional de las vistas
     */
    private function enriquecerDatosDetalle($detalle)
    {
        if (!isset($detalle->nro_control)) {
            \Log::warning('enriquecerDatosDetalle: detalle sin nro_control');
            return $detalle;
        }

        try {
            $nroControl = $detalle->nro_control;
            
            \Log::info('enriquecerDatosDetalle: Procesando', [
                'nro_control' => $nroControl,
                'editorial_antes' => $detalle->editorial ?? 'null',
                'materia_antes' => $detalle->materia ?? 'null',
                'serie_antes' => $detalle->serie ?? 'null',
                'dewey_antes' => $detalle->dewey ?? 'null',
                'biblioteca_antes' => $detalle->biblioteca ?? 'null'
            ]);
            
            // Obtener datos adicionales si no están presentes
            if (empty($detalle->editorial)) {
                $editorial = DB::select("SELECT TOP 1 nombre_busqueda FROM V_EDITORIAL WHERE nro_control = ?", [$nroControl]);
                $detalle->editorial = !empty($editorial) ? $editorial[0]->nombre_busqueda : null;
                \Log::info('enriquecerDatosDetalle: Editorial obtenida', ['editorial' => $detalle->editorial]);
            }
            
            if (empty($detalle->materia)) {
                $materia = DB::select("SELECT TOP 1 nombre_busqueda FROM V_MATERIA WHERE nro_control = ?", [$nroControl]);
                $detalle->materia = !empty($materia) ? $materia[0]->nombre_busqueda : null;
                \Log::info('enriquecerDatosDetalle: Materia obtenida', ['materia' => $detalle->materia]);
            }
            
            if (empty($detalle->serie)) {
                $serie = DB::select("SELECT TOP 1 nombre_busqueda FROM V_SERIE WHERE nro_control = ?", [$nroControl]);
                $detalle->serie = !empty($serie) ? $serie[0]->nombre_busqueda : null;
                \Log::info('enriquecerDatosDetalle: Serie obtenida', ['serie' => $detalle->serie]);
            }
            
            if (empty($detalle->dewey)) {
                $dewey = DB::select("SELECT TOP 1 nombre_busqueda FROM V_DEWEY WHERE nro_control = ?", [$nroControl]);
                $detalle->dewey = !empty($dewey) ? $dewey[0]->nombre_busqueda : null;
                \Log::info('enriquecerDatosDetalle: Dewey obtenido', ['dewey' => $detalle->dewey]);
            }
            
            if (empty($detalle->biblioteca)) {
                $biblioteca = DB::select("
                    SELECT TOP 1 tc.nombre_tb_campus 
                    FROM EXISTENCIA e 
                    INNER JOIN TB_CAMPUS tc ON e.campus_tb_campus = tc.campus_tb_campus 
                    WHERE e.nro_control = ?
                ", [$nroControl]);
                $detalle->biblioteca = !empty($biblioteca) ? $biblioteca[0]->nombre_tb_campus : null;
                \Log::info('enriquecerDatosDetalle: Biblioteca obtenida', ['biblioteca' => $detalle->biblioteca]);
            }
            
            \Log::info('enriquecerDatosDetalle: Completado', [
                'nro_control' => $nroControl,
                'editorial_despues' => $detalle->editorial,
                'materia_despues' => $detalle->materia,
                'serie_despues' => $detalle->serie,
                'dewey_despues' => $detalle->dewey,
                'biblioteca_despues' => $detalle->biblioteca
            ]);
            
            return $detalle;
            
        } catch (\Exception $e) {
            \Log::error('Error enriqueciendo datos del detalle', [
                'nro_control' => $detalle->nro_control ?? 'null',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $detalle;
        }
    }
}