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
        // Log para debug (reducido para mejor rendimiento)
        \Log::info('Iniciando búsqueda con stored procedure', [
            'busqueda' => $request->input('busqueda'),
            'termino' => $request->input('termino'),
            'tipo_busqueda' => $request->input('tipo_busqueda')
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
        $tipoBusqueda = $request->input('tipo_busqueda', 3); // Valor por defecto 3 para compatibilidad
        $tipo = $request->input('tipo');
        $valorSeleccionado = $request->input('valor_seleccionado');
        $verTitulos = $request->input('ver_titulos');
        $pagina = $request->input('page', 1);
        $porPagina = 10;

        // Log de parámetros procesados (reducido)
        \Log::info('Parámetros procesados', [
            'textoBusqueda' => $textoBusqueda,
            'tipoBusqueda' => $tipoBusqueda,
            'tipo' => $tipo
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
                'valorSeleccionado' => $valorSeleccionado,
                'textoBusqueda' => $textoBusqueda,
                'termino' => $termino
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
            if ($tipoFinal != 3 && !$verTitulos && !$valorSeleccionado) {
                \Log::info('Ejecutando buscarElementos - tipos 1,2,4,5,6', [
                    'tipoFinal' => $tipoFinal,
                    'verTitulos' => $verTitulos,
                    'valorSeleccionado' => $valorSeleccionado
                ]);
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
     * Buscar títulos directamente (para búsqueda por título) - OPTIMIZADO
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
        
        // *** NUEVO ENFOQUE OPTIMIZADO ***
        \Log::info('buscarTitulos: Iniciando búsqueda optimizada', [
            'textoBusqueda' => $textoBusqueda,
            'pagina' => $pagina,
            'porPagina' => $porPagina
        ]);

        try {
            // OPCIÓN 1: Intentar usar stored procedure con límite
            $resultadosBrutos = [];
            
            // Para títulos, intentar primero consulta directa más eficiente
            if ($tipoBusqueda == 3) {
                $resultadosBrutos = $this->buscarTitulosPaginados($textoBusqueda, $pagina, $porPagina);
                
                if (empty($resultadosBrutos['datos'])) {
                    // Fallback al stored procedure solo si la consulta directa falla
                    \Log::info('buscarTitulos: Fallback al stored procedure');
                    $spResults = DB::select('EXEC sp_WEB_detalle_busqueda ?, ?', [
                        $textoBusqueda,
                        $tipoBusqueda
                    ]);
                    
                    if (!empty($spResults)) {
                        // Simular paginación con los resultados del SP
                        $totalResultados = count($spResults);
                        $inicio = ($pagina - 1) * $porPagina;
                        $datosPaginados = array_slice($spResults, $inicio, $porPagina);
                        
                        $resultadosBrutos = [
                            'datos' => $datosPaginados,
                            'total' => $totalResultados
                        ];
                    }
                }
            } else {
                // Para otros tipos, usar el stored procedure normalmente
                $spResults = DB::select('EXEC sp_WEB_detalle_busqueda ?, ?', [
                    $textoBusqueda,
                    $tipoBusqueda
                ]);
                
                $totalResultados = count($spResults);
                $inicio = ($pagina - 1) * $porPagina;
                $datosPaginados = array_slice($spResults, $inicio, $porPagina);
                
                $resultadosBrutos = [
                    'datos' => $datosPaginados,
                    'total' => $totalResultados
                ];
            }
            
            \Log::info('buscarTitulos: Resultados obtenidos', [
                'total' => $resultadosBrutos['total'] ?? 0,
                'pagina_actual' => count($resultadosBrutos['datos'] ?? [])
            ]);
            
            // Enriquecer solo los datos de la página actual
            if (!empty($resultadosBrutos['datos'])) {
                \Log::info('buscarTitulos: Enriqueciendo solo datos de la página actual');
                $datosEnriquecidos = $this->enriquecerDatosEnLote($resultadosBrutos['datos']);
                $resultadosBrutos['datos'] = $datosEnriquecidos;
            }
            
        } catch (\Exception $e) {
            \Log::error('Error en búsqueda optimizada', [
                'error' => $e->getMessage()
            ]);
            
            // Fallback a método anterior en caso de error
            return $this->buscarTitulosLegacy($textoBusqueda, $tipoBusqueda, $request);
        }
        // Procesar los resultados para el formato esperado por la vista
        if (!empty($resultadosBrutos['datos'])) {
            $datosProcesados = collect($resultadosBrutos['datos'])->map(function ($item) {
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

            // Obtener datos únicos para filtros SOLO de la página actual
            $autores = collect($resultadosBrutos['datos'])->pluck('autor')->filter()->unique()->sort()->values();
            $editoriales = collect($resultadosBrutos['datos'])->pluck('editorial')->filter()->unique()->sort()->values();
            $materias = collect($resultadosBrutos['datos'])->pluck('materia')->filter()->unique()->sort()->values();
            $series = collect($resultadosBrutos['datos'])->pluck('serie')->filter()->unique()->sort()->values();      
            $campuses = collect($resultadosBrutos['datos'])->pluck('biblioteca')->filter()->unique()->sort()->values();
        } else {
            // Inicializar con colecciones vacías cuando no hay datos
            $datosProcesados = collect();
            $autores = collect();
            $editoriales = collect();
            $materias = collect();
            $series = collect();
            $campuses = collect();
        }

        // Aplicar filtros si están presentes (solo sobre datos de la página actual)
        $resultadosFiltrados = $this->aplicarFiltros($datosProcesados, $request);

        // Aplicar ordenamiento
        $orden = $request->input('orden', 'asc');
        $resultadosFiltrados = $this->aplicarOrdenamiento($resultadosFiltrados, $orden);

        // Usar el total después de aplicar filtros, no el total original de la BD
        $totalDespuesDeFiltros = $resultadosFiltrados->count();
        
        // Si hay filtros activos, usar el total filtrado; si no, usar el total de BD
        $hayFiltrosActivos = $this->hayFiltrosActivos($request);
        $totalParaPaginacion = $hayFiltrosActivos ? $totalDespuesDeFiltros : ($resultadosBrutos['total'] ?? 0);
        
        \Log::info('Paginación calculada', [
            'total_original' => $resultadosBrutos['total'] ?? 0,
            'total_despues_filtros' => $totalDespuesDeFiltros,
            'hay_filtros_activos' => $hayFiltrosActivos,
            'total_para_paginacion' => $totalParaPaginacion
        ]);
        
        $resultados = new \Illuminate\Pagination\LengthAwarePaginator(
            $resultadosFiltrados,
            $totalParaPaginacion,
            $porPagina,
            $pagina,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );

        // Preparar datos para la vista (compatible con vista avanzada)
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
     * Buscar títulos con paginación a nivel de base de datos
     */
    private function buscarTitulosPaginados($textoBusqueda, $pagina, $porPagina)
    {
        try {
            // Construir condiciones de búsqueda
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
            
            // Calcular OFFSET para la paginación
            $offset = ($pagina - 1) * $porPagina;
            
            // Contar total de registros
            $sqlCount = "
                SELECT COUNT(*) as total 
                FROM V_TITULO 
                WHERE " . implode(' AND ', $condiciones);
            
            $totalResult = DB::select($sqlCount, $parametros);
            $totalResultados = $totalResult[0]->total;
            
            \Log::info('buscarTitulosPaginados: Total encontrados', [
                'total' => $totalResultados,
                'textoBusqueda' => $textoBusqueda
            ]);
            
            // Si hay demasiados resultados, implementar límite de seguridad
            if ($totalResultados > 10000) {
                \Log::warning('buscarTitulosPaginados: Demasiados resultados, limitando', [
                    'total' => $totalResultados,
                    'limite' => 10000
                ]);
                $totalResultados = 10000; // Limitar a 10k para rendimiento
            }
            
            // Obtener registros paginados
            $sqlTitulos = "
                SELECT nro_control, nombre_busqueda 
                FROM V_TITULO 
                WHERE " . implode(' AND ', $condiciones) . " 
                ORDER BY nombre_busqueda
                OFFSET ? ROWS
                FETCH NEXT ? ROWS ONLY
            ";
            
            $parametrosPaginados = array_merge($parametros, [$offset, $porPagina]);
            $titulosEncontrados = DB::select($sqlTitulos, $parametrosPaginados);
            
            \Log::info('buscarTitulosPaginados: Registros de página obtenidos', [
                'count' => count($titulosEncontrados),
                'pagina' => $pagina
            ]);
            
            // Enriquecer con datos básicos del SP para cada título de la página
            $resultadosEnriquecidos = [];
            foreach ($titulosEncontrados as $titulo) {
                // Intentar obtener detalles con el SP usando el nombre exacto del título
                $detalles = DB::select('EXEC sp_WEB_detalle_busqueda ?, ?', [
                    $titulo->nombre_busqueda,
                    3
                ]);
                
                if (!empty($detalles)) {
                    // Tomar solo el primer registro del SP (pueden ser varios ejemplares)
                    $resultadosEnriquecidos[] = $detalles[0];
                } else {
                    // Si el SP no funciona, crear un registro básico
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
                    
                    $resultadosEnriquecidos[] = $registroBasico;
                }
            }
            
            return [
                'datos' => $resultadosEnriquecidos,
                'total' => $totalResultados
            ];
            
        } catch (\Exception $e) {
            \Log::error('Error en buscarTitulosPaginados', [
                'error' => $e->getMessage(),
                'textoBusqueda' => $textoBusqueda
            ]);
            
            return [
                'datos' => [],
                'total' => 0
            ];
        }
    }

    /**
     * Método legacy de búsqueda de títulos (fallback en caso de error)
     */
    private function buscarTitulosLegacy($textoBusqueda, $tipoBusqueda, $request)
    {
        \Log::info('buscarTitulosLegacy: Usando método fallback');
        
        // Código simplificado del método anterior
        $pagina = $request->input('page', 1);
        $porPagina = 10;
        
        $resultadosBrutos = DB::select('EXEC sp_WEB_detalle_busqueda ?, ?', [
            $textoBusqueda,
            $tipoBusqueda
        ]);
        
        // Limitar a máximo 1000 registros para evitar timeouts
        if (count($resultadosBrutos) > 1000) {
            \Log::warning('buscarTitulosLegacy: Limitando resultados a 1000');
            $resultadosBrutos = array_slice($resultadosBrutos, 0, 1000);
        }
        
        // Procesar resultados básicos sin enriquecimiento extenso
        $resultadosProcesados = collect($resultadosBrutos)->map(function ($item) {
            return (object) [
                'nro_control' => $item->nro_control ?? null,
                'titulo' => $item->nombre_busqueda ?? 'Sin título',
                'nombre_autor' => $item->autor ?? null,
                'nombre_editorial' => $item->editorial ?? null,
                'nombre_materia' => $item->materia ?? null,
                'nombre_serie' => $item->serie ?? null,
                'dewey' => $item->dewey ?? null,
                'biblioteca' => $item->biblioteca ?? null,
                'anio_publicacion' => $item->publicacion ?? null,
                'tipo_material' => $item->tipo ?? null,
                'isbn' => $item->isbn ?? null,
                'signatura_topografica' => $item->signatura ?? null,
            ];
        });

        // Datos de filtros básicos
        $autores = collect();
        $editoriales = collect();
        $materias = collect();
        $series = collect();
        $campuses = collect();

        // Aplicar paginación en memoria
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
     * Mostrar títulos asociados a un elemento seleccionado - Segundo paso - OPTIMIZADO
     */
    private function mostrarTitulosAsociados($valorSeleccionado, $tipoBusqueda, $request)
    {
        try {
            $pagina = $request->input('page', 1);
            $porPagina = 10;
            
            \Log::info('mostrarTitulosAsociados: Iniciando búsqueda optimizada', [
                'valorSeleccionado' => $valorSeleccionado,
                'tipoBusqueda' => $tipoBusqueda,
                'pagina' => $pagina
            ]);
            
            // Ejecutar el stored procedure con el valor exacto seleccionado
            $resultadosBrutos = DB::select('EXEC sp_WEB_detalle_busqueda ?, ?', [
                $valorSeleccionado,
                $tipoBusqueda
            ]);
            
            \Log::info('mostrarTitulosAsociados: Resultados del SP', [
                'total_registros' => count($resultadosBrutos)
            ]);
            
            // Si hay demasiados resultados, aplicar límite y paginación optimizada
            $totalResultados = count($resultadosBrutos);
            if ($totalResultados > 1000) {
                \Log::warning('mostrarTitulosAsociados: Demasiados resultados, limitando', [
                    'total' => $totalResultados,
                    'limite' => 1000
                ]);
                $resultadosBrutos = array_slice($resultadosBrutos, 0, 1000);
                $totalResultados = 1000;
            }
            
            // Aplicar paginación antes del enriquecimiento
            $inicio = ($pagina - 1) * $porPagina;
            $datosPaginados = array_slice($resultadosBrutos, $inicio, $porPagina);
            
            \Log::info('mostrarTitulosAsociados: Datos paginados', [
                'registros_pagina' => count($datosPaginados),
                'inicio' => $inicio,
                'por_pagina' => $porPagina
            ]);
            
            // Enriquecer solo los datos de la página actual
            if (!empty($datosPaginados)) {
                \Log::info('mostrarTitulosAsociados: Enriqueciendo datos de la página actual');
                $datosEnriquecidos = $this->enriquecerDatosEnLote($datosPaginados);
                $datosPaginados = $datosEnriquecidos;
            }
            
            $resultadosProcesados = collect($datosPaginados)->map(function ($item) {
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

            // Obtener datos únicos para filtros SOLO desde los datos de la página actual
            $autores = collect($datosPaginados)->pluck('autor')->filter()->unique()->sort()->values();
            $editoriales = collect($datosPaginados)->pluck('editorial')->filter()->unique()->sort()->values();
            $materias = collect($datosPaginados)->pluck('materia')->filter()->unique()->sort()->values();
            $series = collect($datosPaginados)->pluck('serie')->filter()->unique()->sort()->values();
            $campuses = collect($datosPaginados)->pluck('biblioteca')->filter()->unique()->sort()->values();

            // Aplicar filtros si están presentes (los filtros se aplicarán sobre toda la colección en futuras versiones)
            $resultadosFilteredOrdered = $this->aplicarFiltros($resultadosProcesados, $request);

            // Aplicar ordenamiento
            $orden = $request->input('orden', 'asc');
            $resultadosFilteredOrdered = $this->aplicarOrdenamiento($resultadosFilteredOrdered, $orden);

            // Usar el total después de aplicar filtros para una paginación correcta
            $totalDespuesDeFiltros = $resultadosFilteredOrdered->count();
            $hayFiltrosActivos = $this->hayFiltrosActivos($request);
            $totalParaPaginacion = $hayFiltrosActivos ? $totalDespuesDeFiltros : $totalResultados;
            
            \Log::info('Paginación mostrarTitulosAsociados', [
                'total_original' => $totalResultados,
                'total_despues_filtros' => $totalDespuesDeFiltros,
                'hay_filtros_activos' => $hayFiltrosActivos,
                'total_para_paginacion' => $totalParaPaginacion
            ]);

            // Crear paginación con el total correcto
            $resultados = new \Illuminate\Pagination\LengthAwarePaginator(
                $resultadosFilteredOrdered,
                $totalParaPaginacion,
                $porPagina,
                $pagina,
                [
                    'path' => $request->url(),
                    'query' => $request->query()
                ]
            );

            // Preparar datos para la vista (compatible con vista avanzada)
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
        // Asegurar que $resultados sea una colección
        if (is_array($resultados)) {
            $resultados = collect($resultados);
        }
        
        $filtrados = $resultados;

        // Procesar filtros para manejar tanto arrays como strings separadas por comas
        $autorFiltro = $this->procesarFiltro($request->input('autor', []));
        $editorialFiltro = $this->procesarFiltro($request->input('editorial', []));
        $materiaFiltro = $this->procesarFiltro($request->input('materia', []));
        $serieFiltro = $this->procesarFiltro($request->input('serie', []));
        $campusFiltro = $this->procesarFiltro($request->input('campus', []));

        \Log::info('aplicarFiltros: Filtros recibidos', [
            'filtros_activos' => [
                'autor' => count($autorFiltro),
                'editorial' => count($editorialFiltro),
                'materia' => count($materiaFiltro),
                'serie' => count($serieFiltro),
                'campus' => count($campusFiltro)
            ],
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
        // Asegurar que $resultados sea una colección
        if (is_array($resultados)) {
            $resultados = collect($resultados);
        }
        
        // Simplificado: solo ordenamiento por título
        if ($orden === 'desc' || $orden === 'titulo_desc') {
            return $resultados->sortByDesc('titulo');
        } else {
            // Por defecto: ascendente (asc, titulo_asc, o cualquier otro valor)
            return $resultados->sortBy('titulo');
        }
    }

    /**
     * Verificar si hay filtros activos en la solicitud
     */
    private function hayFiltrosActivos($request)
    {
        $autorFiltro = $this->procesarFiltro($request->input('autor', []));
        $editorialFiltro = $this->procesarFiltro($request->input('editorial', []));
        $materiaFiltro = $this->procesarFiltro($request->input('materia', []));
        $serieFiltro = $this->procesarFiltro($request->input('serie', []));
        $campusFiltro = $this->procesarFiltro($request->input('campus', []));
        
        return !empty($autorFiltro) || 
               !empty($editorialFiltro) || 
               !empty($materiaFiltro) || 
               !empty($serieFiltro) || 
               !empty($campusFiltro);
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
     * Enriquecer datos en lote para mejorar el rendimiento
     */
    private function enriquecerDatosEnLote($detalles)
    {
        if (empty($detalles)) {
            return $detalles;
        }

        $startTime = microtime(true);
        \Log::info('enriquecerDatosEnLote: Iniciando', ['total_registros' => count($detalles)]);

        // Recopilar todos los nro_control únicos
        $nrosControl = collect($detalles)
            ->pluck('nro_control')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        if (empty($nrosControl)) {
            \Log::warning('enriquecerDatosEnLote: No hay números de control válidos');
            return $detalles;
        }

        \Log::info('enriquecerDatosEnLote: Números de control únicos', ['count' => count($nrosControl)]);

        try {
            // Crear placeholders para la consulta IN
            $placeholders = str_repeat('?,', count($nrosControl) - 1) . '?';
            
            // Obtener datos de editorial para todos los registros de una vez
            $editoriales = DB::select("
                SELECT nro_control, nombre_busqueda 
                FROM V_EDITORIAL 
                WHERE nro_control IN ($placeholders)
            ", $nrosControl);
            $editorialesMap = collect($editoriales)->keyBy('nro_control');

            // Obtener datos de materia para todos los registros de una vez
            $materias = DB::select("
                SELECT nro_control, nombre_busqueda 
                FROM V_MATERIA 
                WHERE nro_control IN ($placeholders)
            ", $nrosControl);
            $materiasMap = collect($materias)->keyBy('nro_control');

            // Obtener datos de serie para todos los registros de una vez
            $series = DB::select("
                SELECT nro_control, nombre_busqueda 
                FROM V_SERIE 
                WHERE nro_control IN ($placeholders)
            ", $nrosControl);
            $seriesMap = collect($series)->keyBy('nro_control');

            // Obtener datos de dewey para todos los registros de una vez
            $deweys = DB::select("
                SELECT nro_control, nombre_busqueda 
                FROM V_DEWEY 
                WHERE nro_control IN ($placeholders)
            ", $nrosControl);
            $deweysMap = collect($deweys)->keyBy('nro_control');

            // Obtener datos de biblioteca para todos los registros de una vez
            $bibliotecas = DB::select("
                SELECT e.nro_control, tc.nombre_tb_campus 
                FROM EXISTENCIA e 
                INNER JOIN TB_CAMPUS tc ON e.campus_tb_campus = tc.campus_tb_campus 
                WHERE e.nro_control IN ($placeholders)
            ", $nrosControl);
            $bibliotecasMap = collect($bibliotecas)->keyBy('nro_control');

            \Log::info('enriquecerDatosEnLote: Datos obtenidos', [
                'editoriales' => $editorialesMap->count(),
                'materias' => $materiasMap->count(),
                'series' => $seriesMap->count(),
                'deweys' => $deweysMap->count(),
                'bibliotecas' => $bibliotecasMap->count()
            ]);

            // Enriquecer cada detalle con los datos obtenidos
            foreach ($detalles as $detalle) {
                if (!isset($detalle->nro_control)) {
                    continue;
                }

                $nroControl = $detalle->nro_control;

                // Enriquecer solo si el campo está vacío
                if (empty($detalle->editorial) && $editorialesMap->has($nroControl)) {
                    $detalle->editorial = $editorialesMap[$nroControl]->nombre_busqueda;
                }

                if (empty($detalle->materia) && $materiasMap->has($nroControl)) {
                    $detalle->materia = $materiasMap[$nroControl]->nombre_busqueda;
                }

                if (empty($detalle->serie) && $seriesMap->has($nroControl)) {
                    $detalle->serie = $seriesMap[$nroControl]->nombre_busqueda;
                }

                if (empty($detalle->dewey) && $deweysMap->has($nroControl)) {
                    $detalle->dewey = $deweysMap[$nroControl]->nombre_busqueda;
                }

                if (empty($detalle->biblioteca) && $bibliotecasMap->has($nroControl)) {
                    $detalle->biblioteca = $bibliotecasMap[$nroControl]->nombre_tb_campus;
                }
            }

            $endTime = microtime(true);
            $duration = round(($endTime - $startTime) * 1000, 2);
            
            \Log::info('enriquecerDatosEnLote: Completado', [
                'total_registros' => count($detalles),
                'duration_ms' => $duration
            ]);

            return $detalles;

        } catch (\Exception $e) {
            \Log::error('Error enriqueciendo datos en lote', [
                'total_registros' => count($detalles),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $detalles;
        }
    }

    /**
     * Enriquecer datos del detalle con información adicional de las vistas (método legacy - mantenido para compatibilidad)
     */
    private function enriquecerDatosDetalle($detalle)
    {
        if (!isset($detalle->nro_control)) {
            \Log::warning('enriquecerDatosDetalle: detalle sin nro_control');
            return $detalle;
        }

        // Para casos individuales, usar el método en lote que es más eficiente
        $resultados = $this->enriquecerDatosEnLote([$detalle]);
        return !empty($resultados) ? $resultados[0] : $detalle;
    }
}