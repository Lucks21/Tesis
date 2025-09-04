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
        \Log::info('buscarConStoredProcedure: Request completo', [
            'all_inputs' => $request->all(),
            'has_autor' => $request->has('autor'),
            'autor_value' => $request->input('autor', 'NOT_SET'),
            'has_editorial' => $request->has('editorial'),
            'editorial_value' => $request->input('editorial', 'NOT_SET'),
            'method' => $request->method(),
            'url' => $request->url(),
            'query_params' => $request->query()
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

        // Verificar que al menos uno de los parámetros de búsqueda esté presente
        if (!$request->filled('busqueda') && !$request->filled('termino')) {
            return redirect()->back()->withErrors(['error' => 'Debe proporcionar un término de búsqueda']);
        }
        
        try {
            // Usar textoBusqueda si no hay termino específico
            $busquedaFinal = $termino ?: $textoBusqueda;
            $tipoFinal = $tipo ?: $tipoBusqueda;
            
            // CASO 1: Si es búsqueda por título (tipo 3) y es búsqueda inicial, mostrar títulos directamente
            if ($tipoFinal == 3 && !$verTitulos && !$valorSeleccionado) {
                return $this->buscarTitulos($busquedaFinal, $tipoFinal, $request);
            }
            
            // CASO 2: Clic en "Ver títulos" - viene con termino, tipo y ver_titulos
            if (($verTitulos && $termino && $tipo) || ($termino && $tipo && !$textoBusqueda && $valorSeleccionado)) {
                return $this->mostrarTitulosAsociados($termino, $tipo, $request);
            }
            
            // CASO 3: Si hay un valor seleccionado, mostrar títulos asociados
            if ($valorSeleccionado && !$verTitulos) {
                return $this->mostrarTitulosAsociados($valorSeleccionado, $tipoBusqueda, $request);
            }
            
            // CASO 4: Para otros tipos (autor, materia, editorial, serie, dewey), buscar elementos primero
            if ($tipoFinal != 3 && !$verTitulos && !$valorSeleccionado) {
                return $this->buscarElementos($busquedaFinal, $tipoFinal, $request);
            }
            
            // CASO 5: Fallback para título si algo sale mal
            return $this->buscarTitulos($busquedaFinal, $tipoFinal, $request);

        } catch (\Exception $e) {
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
        
        // Verificar si hay filtros activos
        $hayFiltrosActivos = $this->hayFiltrosActivos($request);
        
        \Log::info('buscarTitulos: Decidiendo estrategia', [
            'hayFiltrosActivos' => $hayFiltrosActivos,
            'autor_input' => $request->input('autor', []),
            'editorial_input' => $request->input('editorial', []),
            'materia_input' => $request->input('materia', []),
            'serie_input' => $request->input('serie', []),
            'campus_input' => $request->input('campus', [])
        ]);
        
        try {
            if ($hayFiltrosActivos) {
                // CASO CON FILTROS: Obtener todos los datos, filtrar, luego paginar
                return $this->buscarTitulosConFiltros($textoBusqueda, $tipoBusqueda, $request);
            } else {
                // CASO SIN FILTROS: Usar paginación optimizada a nivel de BD
                return $this->buscarTitulosSinFiltros($textoBusqueda, $tipoBusqueda, $request);
            }
        } catch (\Exception $e) {            
            // Fallback a método anterior en caso de error
            return $this->buscarTitulosLegacy($textoBusqueda, $tipoBusqueda, $request);
        }
    }

    /**
     * Búsqueda sin filtros - Usa paginación a nivel de BD (más eficiente)
     */
    private function buscarTitulosSinFiltros($textoBusqueda, $tipoBusqueda, $request)
    {
        $pagina = $request->input('page', 1);
        $porPagina = 10;
        
        // Usar método optimizado para obtener solo la página actual
        if ($tipoBusqueda == 3) {
            $resultadosBrutos = $this->buscarTitulosPaginados($textoBusqueda, $pagina, $porPagina);
        } else {
            // Para otros tipos, obtener resultados del SP y paginar en memoria
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

        // Enriquecer solo los datos de la página actual
        if (!empty($resultadosBrutos['datos'])) {
            $datosEnriquecidos = $this->enriquecerDatosEnLote($resultadosBrutos['datos']);
            $resultadosBrutos['datos'] = $datosEnriquecidos;
        }

        // Procesar resultados para la vista
        $datosProcesados = collect($resultadosBrutos['datos'] ?? [])->map(function ($item) {
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

        // Obtener todos los filtros disponibles para la búsqueda completa
        $filtrosCompletos = $this->obtenerFiltrosCompletos($textoBusqueda, $tipoBusqueda);
        $autores = $filtrosCompletos['autores'];
        $editoriales = $filtrosCompletos['editoriales'];
        $materias = $filtrosCompletos['materias'];
        $series = $filtrosCompletos['series'];
        $campuses = $filtrosCompletos['campuses'];

        // Crear paginación
        $resultados = new \Illuminate\Pagination\LengthAwarePaginator(
            $datosProcesados,
            $resultadosBrutos['total'] ?? 0,
            $porPagina,
            $pagina,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );

        // Preparar datos para la vista
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
     * Búsqueda con filtros - Obtiene todos los datos, filtra, luego pagina
     */
    private function buscarTitulosConFiltros($textoBusqueda, $tipoBusqueda, $request)
    {
        $pagina = $request->input('page', 1);
        $porPagina = 10;
        
        // Obtener TODOS los resultados (sin paginación)
        if ($tipoBusqueda == 3) {
            // Para títulos, usar consulta directa sin límite de página
            $resultadosCompletos = $this->obtenerTodosTitulos($textoBusqueda);
        } else {
            // Para otros tipos, usar SP completo
            $resultadosCompletos = DB::select('EXEC sp_WEB_detalle_busqueda ?, ?', [
                $textoBusqueda,
                $tipoBusqueda
            ]);
            
            // Limitar a 3000 registros para evitar timeout en filtrado
            if (count($resultadosCompletos) > 3000) {
                $resultadosCompletos = array_slice($resultadosCompletos, 0, 3000);
            }
        }

        // Enriquecer todos los datos
        if (!empty($resultadosCompletos)) {
            $resultadosCompletos = $this->enriquecerDatosEnLote($resultadosCompletos);
        }

        // Procesar todos los resultados
        $todosLosDatos = collect($resultadosCompletos ?? [])->map(function ($item) {
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

        // Aplicar filtros a TODOS los datos
        $datosFiltrados = $this->aplicarFiltros($todosLosDatos, $request);

        // Aplicar ordenamiento
        $orden = $request->input('orden', 'asc');
        $datosFiltrados = $this->aplicarOrdenamiento($datosFiltrados, $orden);

        // Aplicar paginación sobre los datos filtrados
        $totalFiltrados = $datosFiltrados->count();
        $inicio = ($pagina - 1) * $porPagina;
        $datosPaginados = $datosFiltrados->slice($inicio, $porPagina)->values();

        // Obtener todos los filtros disponibles para la búsqueda completa
        $filtrosCompletos = $this->obtenerFiltrosCompletos($textoBusqueda, $tipoBusqueda);
        $autores = $filtrosCompletos['autores'];
        $editoriales = $filtrosCompletos['editoriales'];
        $materias = $filtrosCompletos['materias'];
        $series = $filtrosCompletos['series'];
        $campuses = $filtrosCompletos['campuses'];

        // Crear paginación con el total filtrado
        $resultados = new \Illuminate\Pagination\LengthAwarePaginator(
            $datosPaginados,
            $totalFiltrados,
            $porPagina,
            $pagina,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );

        // Preparar datos para la vista
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
     * Obtener todos los títulos que coincidan con la búsqueda (sin paginación)
     */
    private function obtenerTodosTitulos($textoBusqueda)
    {
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
        
        // Obtener títulos (limitados a 3000 para evitar timeout)
        $sqlTitulos = "
            SELECT TOP 3000 nro_control, nombre_busqueda 
            FROM V_TITULO 
            WHERE " . implode(' AND ', $condiciones) . " 
            ORDER BY nombre_busqueda
        ";
        
        $titulos = DB::select($sqlTitulos, $parametros);
        
        // Convertir a formato esperado
        return array_map(function($titulo) {
            $obj = new \stdClass();
            $obj->nro_control = $titulo->nro_control;
            $obj->nombre_busqueda = $titulo->nombre_busqueda;
            $obj->autor = null;
            $obj->editorial = null;
            $obj->materia = null;
            $obj->serie = null;
            $obj->biblioteca = null;
            $obj->publicacion = null;
            $obj->tipo = null;
            $obj->isbn = null;
            $obj->signatura = null;
            return $obj;
        }, $titulos);
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
            
            // Si hay demasiados resultados, implementar límite de seguridad
            if ($totalResultados > 10000) {
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
        // Código simplificado del método anterior
        $pagina = $request->input('page', 1);
        $porPagina = 10;
        
        $resultadosBrutos = DB::select('EXEC sp_WEB_detalle_busqueda ?, ?', [
            $textoBusqueda,
            $tipoBusqueda
        ]);
        
        // Limitar a máximo 1000 registros para evitar timeouts
        if (count($resultadosBrutos) > 1000) {
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
            // Verificar si hay filtros activos
            $hayFiltrosActivos = $this->hayFiltrosActivos($request);
            
            if ($hayFiltrosActivos) {
                // CASO CON FILTROS: Obtener todos los datos, filtrar, luego paginar
                return $this->mostrarTitulosAsociadosConFiltros($valorSeleccionado, $tipoBusqueda, $request);
            } else {
                // CASO SIN FILTROS: Usar paginación optimizada
                return $this->mostrarTitulosAsociadosSinFiltros($valorSeleccionado, $tipoBusqueda, $request);
            }
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
     * Mostrar títulos asociados sin filtros - Usa paginación optimizada
     */
    private function mostrarTitulosAsociadosSinFiltros($valorSeleccionado, $tipoBusqueda, $request)
    {
        $pagina = $request->input('page', 1);
        $porPagina = 10;
        
        // Ejecutar el stored procedure con el valor exacto seleccionado
        $resultadosBrutos = DB::select('EXEC sp_WEB_detalle_busqueda ?, ?', [
            $valorSeleccionado,
            $tipoBusqueda
        ]);
        
        // Si hay demasiados resultados, aplicar límite
        $totalResultados = count($resultadosBrutos);
        if ($totalResultados > 1000) {
            $resultadosBrutos = array_slice($resultadosBrutos, 0, 1000);
            $totalResultados = 1000;
        }
        
        // Aplicar paginación antes del enriquecimiento
        $inicio = ($pagina - 1) * $porPagina;
        $datosPaginados = array_slice($resultadosBrutos, $inicio, $porPagina);
        
        // Enriquecer solo los datos de la página actual
        if (!empty($datosPaginados)) {
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

        // Obtener todos los filtros disponibles para la búsqueda completa
        $filtrosCompletos = $this->obtenerFiltrosCompletos($valorSeleccionado, $tipoBusqueda);
        $autores = $filtrosCompletos['autores'];
        $editoriales = $filtrosCompletos['editoriales'];
        $materias = $filtrosCompletos['materias'];
        $series = $filtrosCompletos['series'];
        $campuses = $filtrosCompletos['campuses'];

        // Crear paginación
        $resultados = new \Illuminate\Pagination\LengthAwarePaginator(
            $resultadosProcesados,
            $totalResultados,
            $porPagina,
            $pagina,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );

        // Preparar datos para la vista
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
    }

    /**
     * Mostrar títulos asociados con filtros - Obtiene todos los datos, filtra, luego pagina
     */
    private function mostrarTitulosAsociadosConFiltros($valorSeleccionado, $tipoBusqueda, $request)
    {
        $pagina = $request->input('page', 1);
        $porPagina = 10;
        
        // Ejecutar el stored procedure para obtener TODOS los resultados
        $resultadosCompletos = DB::select('EXEC sp_WEB_detalle_busqueda ?, ?', [
            $valorSeleccionado,
            $tipoBusqueda
        ]);
        
        // Limitar a 3000 registros máximo para evitar timeout
        if (count($resultadosCompletos) > 3000) {
            $resultadosCompletos = array_slice($resultadosCompletos, 0, 3000);
        }
        
        // Enriquecer todos los datos
        if (!empty($resultadosCompletos)) {
            $resultadosCompletos = $this->enriquecerDatosEnLote($resultadosCompletos);
        }
        
        // Procesar todos los resultados
        $todosLosDatos = collect($resultadosCompletos)->map(function ($item) {
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

        // Aplicar filtros a TODOS los datos
        $datosFiltrados = $this->aplicarFiltros($todosLosDatos, $request);

        // Aplicar ordenamiento
        $orden = $request->input('orden', 'asc');
        $datosFiltrados = $this->aplicarOrdenamiento($datosFiltrados, $orden);

        // Aplicar paginación sobre los datos filtrados
        $totalFiltrados = $datosFiltrados->count();
        $inicio = ($pagina - 1) * $porPagina;
        $datosPaginados = $datosFiltrados->slice($inicio, $porPagina)->values();

        // Obtener todos los filtros disponibles para la búsqueda completa
        $filtrosCompletos = $this->obtenerFiltrosCompletos($valorSeleccionado, $tipoBusqueda);
        $autores = $filtrosCompletos['autores'];
        $editoriales = $filtrosCompletos['editoriales'];
        $materias = $filtrosCompletos['materias'];
        $series = $filtrosCompletos['series'];
        $campuses = $filtrosCompletos['campuses'];

        // Crear paginación con el total filtrado
        $resultados = new \Illuminate\Pagination\LengthAwarePaginator(
            $datosPaginados,
            $totalFiltrados,
            $porPagina,
            $pagina,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );

        // Preparar datos para la vista
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

        // Debug: Ver datos antes del filtrado
        \Log::info('Aplicando filtros', [
            'total_inicial' => $filtrados->count(),
            'autor_filtro' => $autorFiltro,
            'editorial_filtro' => $editorialFiltro,
            'materia_filtro' => $materiaFiltro,
            'serie_filtro' => $serieFiltro,
            'campus_filtro' => $campusFiltro,
            'muestra_datos_antes_filtro' => $filtrados->take(3)->map(function($item) {
                return [
                    'titulo' => substr($item->nombre_busqueda ?? 'N/A', 0, 40),
                    'autor_raw' => $item->autor ?? $item->nombre_autor ?? 'N/A',
                    'materia_raw' => $item->materia ?? $item->nombre_materia ?? 'N/A',
                    'editorial_raw' => $item->editorial ?? $item->nombre_editorial ?? 'N/A'
                ];
            })->toArray(),
            'tiene_aenor_en_datos' => !empty($autorFiltro) && in_array('AENOR (España)', $autorFiltro) ? 
                $filtrados->filter(function($item) {
                    $autor = $item->autor ?? $item->nombre_autor ?? '';
                    return strpos($autor, 'AENOR') !== false;
                })->count() : 'N/A'
        ]);

        // Filtrar por autor - usar comparación exacta como en búsqueda avanzada
        if (!empty($autorFiltro) && count($autorFiltro) > 0) {
            $antesFiltro = $filtrados->count();
            $filtrados = $filtrados->filter(function ($item) use ($autorFiltro) {
                $autor = $item->nombre_autor ?? $item->autor ?? null;
                
                // Saltar si el autor está vacío o es "NULL"
                if (!$autor || $autor === 'NULL' || trim($autor) === '') {
                    return false;
                }
                
                // Normalizar el autor del item
                $autorNormalizado = $this->normalizarTexto(trim($autor));
                
                // Comparación con cada filtro normalizado
                foreach ($autorFiltro as $filtroAutor) {
                    $filtroNormalizado = $this->normalizarTexto(trim($filtroAutor));
                    
                    // Log detallado para debugging
                    if ($filtroAutor === 'AENOR (España)') {
                        \Log::info('Debug comparación autor AENOR', [
                            'autor_original' => $autor,
                            'autor_normalizado' => $autorNormalizado,
                            'filtro_original' => $filtroAutor,
                            'filtro_normalizado' => $filtroNormalizado,
                            'coincide_exacto' => ($autorNormalizado === $filtroNormalizado),
                            'coincide_contains' => (strpos($autorNormalizado, $filtroNormalizado) !== false)
                        ]);
                    }
                    
                    // Intentar coincidencia exacta primero
                    if ($autorNormalizado === $filtroNormalizado) {
                        return true;
                    }
                    
                    // Si no hay coincidencia exacta, probar si el filtro está contenido en el autor
                    if (strpos($autorNormalizado, $filtroNormalizado) !== false || 
                        strpos($filtroNormalizado, $autorNormalizado) !== false) {
                        return true;
                    }
                }
                return false;
            });
            
            $despuesFiltro = $filtrados->count();
            \Log::info('Filtro autor aplicado', [
                'antes' => $antesFiltro,
                'despues' => $despuesFiltro,
                'filtro_aplicado' => $autorFiltro,
                'muestra_datos' => $filtrados->take(2)->map(function($item) {
                    return [
                        'autor' => $item->autor ?? $item->nombre_autor ?? 'N/A',
                        'titulo' => substr($item->nombre_busqueda ?? 'N/A', 0, 50)
                    ];
                })->toArray()
            ]);
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
            $antesFiltro = $filtrados->count();
            $filtrados = $filtrados->filter(function ($item) use ($materiaFiltro) {
                $materia = $item->nombre_materia ?? $item->materia ?? null;
                
                // Saltar si la materia está vacía o es "NULL"
                if (!$materia || $materia === 'NULL' || trim($materia) === '') {
                    return false;
                }
                
                // Normalizar la materia del item
                $materiaNormalizada = $this->normalizarTexto(trim($materia));
                
                // Comparación con cada filtro normalizado
                foreach ($materiaFiltro as $filtroMateria) {
                    $filtroNormalizado = $this->normalizarTexto(trim($filtroMateria));
                    
                    // Intentar coincidencia exacta primero
                    if ($materiaNormalizada === $filtroNormalizado) {
                        return true;
                    }
                    
                    // Si no hay coincidencia exacta, probar si el filtro está contenido en la materia
                    // Esto es porque los filtros pueden venir de V_MATERIA que puede tener textos diferentes
                    if (strpos($materiaNormalizada, $filtroNormalizado) !== false || 
                        strpos($filtroNormalizado, $materiaNormalizada) !== false) {
                        return true;
                    }
                }
                return false;
            });
            
            $despuesFiltro = $filtrados->count();
            \Log::info('Filtro materia aplicado', [
                'antes' => $antesFiltro,
                'despues' => $despuesFiltro,
                'filtro_aplicado' => $materiaFiltro,
                'muestra_datos' => $filtrados->take(2)->map(function($item) {
                    return [
                        'materia' => $item->materia ?? $item->nombre_materia ?? 'N/A',
                        'titulo' => substr($item->nombre_busqueda ?? 'N/A', 0, 50)
                    ];
                })->toArray()
            ]);
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

        \Log::info('enriquecerDatosEnLote: Iniciando', [
            'total_registros' => count($detalles),
            'primer_registro_antes' => [
                'nro_control' => $detalles[0]->nro_control ?? 'NULL',
                'nombre_busqueda' => $detalles[0]->nombre_busqueda ?? 'NULL',
                'autor' => $detalles[0]->autor ?? 'NULL',
                'editorial' => $detalles[0]->editorial ?? 'NULL',
                'materia' => $detalles[0]->materia ?? 'NULL'
            ]
        ]);

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

        \Log::info('enriquecerDatosEnLote: Números de control', [
            'count' => count($nrosControl),
            'primeros_3' => array_slice($nrosControl, 0, 3)
        ]);

        try {
            // Crear placeholders para la consulta IN
            $placeholders = str_repeat('?,', count($nrosControl) - 1) . '?';
            
            // Obtener datos de autor para todos los registros de una vez
            $autores = DB::select("
                SELECT nro_control, nombre_busqueda 
                FROM V_AUTOR 
                WHERE nro_control IN ($placeholders)
            ", $nrosControl);
            $autoresMap = collect($autores)->keyBy('nro_control');
            
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

            \Log::info('enriquecerDatosEnLote: Datos consultados', [
                'autores_encontrados' => $autoresMap->count(),
                'editoriales_encontradas' => $editorialesMap->count(),
                'materias_encontradas' => $materiasMap->count(),
                'series_encontradas' => $seriesMap->count(),
                'deweys_encontrados' => $deweysMap->count(),
                'bibliotecas_encontradas' => $bibliotecasMap->count()
            ]);

            // Enriquecer cada detalle con los datos obtenidos
            foreach ($detalles as $detalle) {
                if (!isset($detalle->nro_control)) {
                    continue;
                }

                $nroControl = $detalle->nro_control;

                // Enriquecer solo si el campo está vacío o es "NULL"
                if ((empty($detalle->autor) || $detalle->autor === 'NULL') && $autoresMap->has($nroControl)) {
                    $detalle->autor = $autoresMap[$nroControl]->nombre_busqueda;
                }

                if ((empty($detalle->editorial) || $detalle->editorial === 'NULL') && $editorialesMap->has($nroControl)) {
                    $detalle->editorial = $editorialesMap[$nroControl]->nombre_busqueda;
                }

                if ((empty($detalle->materia) || $detalle->materia === 'NULL') && $materiasMap->has($nroControl)) {
                    $detalle->materia = $materiasMap[$nroControl]->nombre_busqueda;
                }

                if ((empty($detalle->serie) || $detalle->serie === 'NULL') && $seriesMap->has($nroControl)) {
                    $detalle->serie = $seriesMap[$nroControl]->nombre_busqueda;
                }

                if (empty($detalle->dewey) && $deweysMap->has($nroControl)) {
                    $detalle->dewey = $deweysMap[$nroControl]->nombre_busqueda;
                }

                if (empty($detalle->biblioteca) && $bibliotecasMap->has($nroControl)) {
                    $detalle->biblioteca = $bibliotecasMap[$nroControl]->nombre_tb_campus;
                }
            }

            \Log::info('enriquecerDatosEnLote: Primer registro después del enriquecimiento', [
                'nro_control' => $detalles[0]->nro_control ?? 'NULL',
                'autor' => $detalles[0]->autor ?? 'NULL',
                'editorial' => $detalles[0]->editorial ?? 'NULL',
                'materia' => $detalles[0]->materia ?? 'NULL',
                'serie' => $detalles[0]->serie ?? 'NULL',
                'biblioteca' => $detalles[0]->biblioteca ?? 'NULL'
            ]);

            return $detalles;

        } catch (\Exception $e) {
            \Log::error('Error en enriquecerDatosEnLote', [
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
            return $detalle;
        }

        // Para casos individuales, usar el método en lote que es más eficiente
        $resultados = $this->enriquecerDatosEnLote([$detalle]);
        return !empty($resultados) ? $resultados[0] : $detalle;
    }

    /**
     * Obtener todos los filtros disponibles para una búsqueda específica
     */
    private function obtenerFiltrosCompletos($textoBusqueda, $tipoBusqueda)
    {
        \Log::info('obtenerFiltrosCompletos: Iniciando', [
            'textoBusqueda' => $textoBusqueda,
            'tipoBusqueda' => $tipoBusqueda
        ]);
        
        try {
            // Para títulos, usar método específico que sí funciona
            if ($tipoBusqueda == 3) {
                \Log::info('obtenerFiltrosCompletos: Usando método específico para títulos');
                return $this->obtenerFiltrosPorTituloEficaz($textoBusqueda);
            } else {
                \Log::info('obtenerFiltrosCompletos: Usando SP para tipo', ['tipo' => $tipoBusqueda]);
                // Para otros tipos, usar el SP
                return $this->obtenerFiltrosPorSP($textoBusqueda, $tipoBusqueda);
            }
        } catch (\Exception $e) {
            \Log::error('obtenerFiltrosCompletos: Error', ['error' => $e->getMessage()]);
            // En caso de error, retornar filtros vacíos
            return [
                'autores' => collect(),
                'editoriales' => collect(),
                'materias' => collect(),
                'series' => collect(),
                'campuses' => collect()
            ];
        }
    }

    /**
     * Método eficaz para obtener filtros de títulos - SINCRONIZADO con resultados reales
     * USA EXACTAMENTE los mismos datos que se muestran en pantalla
     */
    private function obtenerFiltrosPorTituloEficaz($textoBusqueda)
    {
        \Log::info('obtenerFiltrosPorTituloEficaz: Iniciando', [
            'textoBusqueda' => $textoBusqueda
        ]);

        try {
            // PASO 1: Obtener exactamente los mismos títulos que obtenerTodosTitulos
            $titulosReales = $this->obtenerTodosTitulos($textoBusqueda);
            
            if (empty($titulosReales)) {
                \Log::warning('No hay títulos reales para generar filtros');
                return [
                    'autores' => collect(),
                    'editoriales' => collect(),
                    'materias' => collect(),
                    'series' => collect(),
                    'campuses' => collect()
                ];
            }
            
            // PASO 2: Enriquecer estos títulos exactamente como se hace en el proceso normal
            $titulosEnriquecidos = $this->enriquecerDatosEnLote($titulosReales);
            
            // PASO 3: Extraer filtros de estos datos enriquecidos (los mismos que se van a mostrar)
            $autores = collect();
            $editoriales = collect();
            $materias = collect();
            $series = collect();
            $campuses = collect();
            
            foreach ($titulosEnriquecidos as $titulo) {
                // Extraer autor
                if (!empty($titulo->autor) && $titulo->autor !== 'NULL') {
                    $autores->push($titulo->autor);
                }
                
                // Extraer editorial
                if (!empty($titulo->editorial) && $titulo->editorial !== 'NULL') {
                    $editoriales->push($titulo->editorial);
                }
                
                // Extraer materia
                if (!empty($titulo->materia) && $titulo->materia !== 'NULL') {
                    $materias->push($titulo->materia);
                }
                
                // Extraer serie
                if (!empty($titulo->serie) && $titulo->serie !== 'NULL') {
                    $series->push($titulo->serie);
                }
                
                // Extraer campus
                if (!empty($titulo->biblioteca)) {
                    $campuses->push($titulo->biblioteca);
                }
            }
            
            $filtros = [
                'autores' => $autores->filter()->unique()->sort()->values(),
                'editoriales' => $editoriales->filter()->unique()->sort()->values(),
                'materias' => $materias->filter()->unique()->sort()->values(),
                'series' => $series->filter()->unique()->sort()->values(),
                'campuses' => $campuses->filter()->unique()->sort()->values()
            ];
            
            \Log::info('obtenerFiltrosPorTituloEficaz: Resultado EXACTAMENTE SINCRONIZADO', [
                'titulos_procesados' => count($titulosEnriquecidos),
                'autores_count' => $filtros['autores']->count(),
                'editoriales_count' => $filtros['editoriales']->count(),
                'materias_count' => $filtros['materias']->count(),
                'series_count' => $filtros['series']->count(),
                'campuses_count' => $filtros['campuses']->count(),
                'primeros_3_autores' => $filtros['autores']->take(3)->toArray(),
                'tiene_aenor' => $filtros['autores']->contains('AENOR (España)'),
                'muestra_autores' => $filtros['autores']->take(10)->toArray()
            ]);
            
            return $filtros;
            
        } catch (\Exception $e) {
            \Log::error('Error en obtenerFiltrosPorTituloEficaz', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'autores' => collect(),
                'editoriales' => collect(),
                'materias' => collect(),
                'series' => collect(),
                'campuses' => collect()
            ];
        }
    }

    /**
     * Obtener filtros para búsqueda por título usando consultas optimizadas
     */
    private function obtenerFiltrosPorTitulo($textoBusqueda)
    {
        try {
            // Para títulos, también usar el SP para obtener datos completos
            // Esto asegura que obtengamos todos los autores y materias asociados
            $resultadosCompletos = DB::select('EXEC sp_WEB_detalle_busqueda ?, ?', [
                $textoBusqueda,
                3 // Tipo 3 = título
            ]);
            
            // Limitar a 2000 registros máximo para evitar timeout
            if (count($resultadosCompletos) > 2000) {
                $resultadosCompletos = array_slice($resultadosCompletos, 0, 2000);
            }
            
            // Enriquecer datos en lote para completar información faltante
            $resultadosEnriquecidos = $this->enriquecerDatosEnLote($resultadosCompletos);
            
            // Usar los mismos filtros que para SP
            return [
                'autores' => collect($resultadosEnriquecidos)->pluck('autor')->filter()->unique()->sort()->values(),
                'editoriales' => collect($resultadosEnriquecidos)->pluck('editorial')->filter()->unique()->sort()->values(),
                'materias' => collect($resultadosEnriquecidos)->pluck('materia')->filter()->unique()->sort()->values(),
                'series' => collect($resultadosEnriquecidos)->pluck('serie')->filter()->unique()->sort()->values(),
                'campuses' => collect($resultadosEnriquecidos)->pluck('biblioteca')->filter()->unique()->sort()->values()
            ];
            
        } catch (\Exception $e) {
            // Fallback: intentar con JOINs directos (método original)
            return $this->obtenerFiltrosPorTituloConJoins($textoBusqueda);
        }
    }
    
    /**
     * Método fallback para obtener filtros por título usando JOINs directos
     */
    private function obtenerFiltrosPorTituloConJoins($textoBusqueda)
    {
        // Construir condiciones de búsqueda (igual que en buscarTitulosPaginados)
        $palabras = explode(' ', trim($textoBusqueda));
        $condiciones = [];
        $parametros = [];
        
        foreach ($palabras as $palabra) {
            if (strlen(trim($palabra)) > 2) {
                $condiciones[] = "vt.nombre_busqueda LIKE ?";
                $parametros[] = "%{$palabra}%";
            }
        }
        
        if (empty($condiciones)) {
            $condiciones[] = "vt.nombre_busqueda LIKE ?";
            $parametros[] = "%{$textoBusqueda}%";
        }
        
        $whereClause = implode(' AND ', $condiciones);
        
        try {
            // Obtener autores únicos
            $autores = DB::select("
                SELECT DISTINCT va.nombre_busqueda
                FROM V_TITULO vt
                INNER JOIN V_AUTOR va ON vt.nro_control = va.nro_control
                WHERE {$whereClause}
                ORDER BY va.nombre_busqueda
            ", $parametros);
            
            // Obtener editoriales únicas
            $editoriales = DB::select("
                SELECT DISTINCT ve.nombre_busqueda
                FROM V_TITULO vt
                INNER JOIN V_EDITORIAL ve ON vt.nro_control = ve.nro_control
                WHERE {$whereClause}
                ORDER BY ve.nombre_busqueda
            ", $parametros);
            
            // Obtener materias únicas
            $materias = DB::select("
                SELECT DISTINCT vm.nombre_busqueda
                FROM V_TITULO vt
                INNER JOIN V_MATERIA vm ON vt.nro_control = vm.nro_control
                WHERE {$whereClause}
                ORDER BY vm.nombre_busqueda
            ", $parametros);
            
            // Obtener series únicas
            $series = DB::select("
                SELECT DISTINCT vs.nombre_busqueda
                FROM V_TITULO vt
                INNER JOIN V_SERIE vs ON vt.nro_control = vs.nro_control
                WHERE {$whereClause}
                ORDER BY vs.nombre_busqueda
            ", $parametros);
            
            // Obtener campus únicos
            $campuses = DB::select("
                SELECT DISTINCT tc.nombre_tb_campus
                FROM V_TITULO vt
                INNER JOIN EXISTENCIA e ON vt.nro_control = e.nro_control
                INNER JOIN TB_CAMPUS tc ON e.campus_tb_campus = tc.campus_tb_campus
                WHERE {$whereClause}
                ORDER BY tc.nombre_tb_campus
            ", $parametros);
            
            return [
                'autores' => collect($autores)->pluck('nombre_busqueda')->filter()->unique()->sort()->values(),
                'editoriales' => collect($editoriales)->pluck('nombre_busqueda')->filter()->unique()->sort()->values(),
                'materias' => collect($materias)->pluck('nombre_busqueda')->filter()->unique()->sort()->values(),
                'series' => collect($series)->pluck('nombre_busqueda')->filter()->unique()->sort()->values(),
                'campuses' => collect($campuses)->pluck('nombre_tb_campus')->filter()->unique()->sort()->values()
            ];
        } catch (\Exception $e) {
            // Si falla todo, retornar filtros vacíos
            return [
                'autores' => collect(),
                'editoriales' => collect(),
                'materias' => collect(),
                'series' => collect(),
                'campuses' => collect()
            ];
        }
    }

    /**
     * Obtener filtros usando el stored procedure (para todos los tipos)
     */
    private function obtenerFiltrosPorSP($textoBusqueda, $tipoBusqueda)
    {
        // Log temporal para diagnóstico
        \Log::info('obtenerFiltrosPorSP: Iniciando', [
            'textoBusqueda' => $textoBusqueda,
            'tipoBusqueda' => $tipoBusqueda
        ]);

        // Ejecutar SP una sola vez para obtener todos los resultados
        $resultadosCompletos = DB::select('EXEC sp_WEB_detalle_busqueda ?, ?', [
            $textoBusqueda,
            $tipoBusqueda
        ]);
        
        \Log::info('obtenerFiltrosPorSP: Resultados SP', [
            'total_registros' => count($resultadosCompletos),
            'primer_registro' => !empty($resultadosCompletos) ? (array)$resultadosCompletos[0] : 'N/A'
        ]);
        
        // Limitar a 2000 registros máximo para evitar timeout
        if (count($resultadosCompletos) > 2000) {
            $resultadosCompletos = array_slice($resultadosCompletos, 0, 2000);
        }
        
        if (empty($resultadosCompletos)) {
            \Log::warning('obtenerFiltrosPorSP: No hay resultados del SP');
            return [
                'autores' => collect(),
                'editoriales' => collect(),
                'materias' => collect(),
                'series' => collect(),
                'campuses' => collect()
            ];
        }
        
        // Enriquecer datos en lote
        $resultadosEnriquecidos = $this->enriquecerDatosEnLote($resultadosCompletos);
        
        \Log::info('obtenerFiltrosPorSP: Después del enriquecimiento', [
            'primer_registro_enriquecido' => !empty($resultadosEnriquecidos) ? [
                'autor' => $resultadosEnriquecidos[0]->autor ?? 'NULL',
                'editorial' => $resultadosEnriquecidos[0]->editorial ?? 'NULL',
                'materia' => $resultadosEnriquecidos[0]->materia ?? 'NULL',
                'serie' => $resultadosEnriquecidos[0]->serie ?? 'NULL',
                'biblioteca' => $resultadosEnriquecidos[0]->biblioteca ?? 'NULL'
            ] : 'N/A'
        ]);
        
        // Extraer filtros únicos
        $autores = collect($resultadosEnriquecidos)->pluck('autor')->filter()->unique()->sort()->values();
        $editoriales = collect($resultadosEnriquecidos)->pluck('editorial')->filter()->unique()->sort()->values();
        $materias = collect($resultadosEnriquecidos)->pluck('materia')->filter()->unique()->sort()->values();
        $series = collect($resultadosEnriquecidos)->pluck('serie')->filter()->unique()->sort()->values();
        $campuses = collect($resultadosEnriquecidos)->pluck('biblioteca')->filter()->unique()->sort()->values();
        
        \Log::info('obtenerFiltrosPorSP: Filtros extraídos', [
            'autores_count' => $autores->count(),
            'editoriales_count' => $editoriales->count(),
            'materias_count' => $materias->count(),
            'series_count' => $series->count(),
            'campuses_count' => $campuses->count(),
            'primeros_autores' => $autores->take(3)->toArray(),
            'primeras_editoriales' => $editoriales->take(3)->toArray()
        ]);
        
        return [
            'autores' => $autores,
            'editoriales' => $editoriales,
            'materias' => $materias,
            'series' => $series,
            'campuses' => $campuses
        ];
    }
    
    /**
     * Normaliza texto para comparaciones más robustas
     */
    private function normalizarTexto($texto)
    {
        if (!$texto || $texto === 'NULL') {
            return '';
        }
        
        // Convertir a UTF-8 si no lo está
        $texto = mb_convert_encoding($texto, 'UTF-8', 'UTF-8');
        
        // Limpiar espacios extra
        $texto = trim($texto);
        
        // Normalizar caracteres especiales comunes
        $reemplazos = [
            'á' => 'á', 'é' => 'é', 'í' => 'í', 'ó' => 'ó', 'ú' => 'ú',
            'à' => 'à', 'è' => 'è', 'ì' => 'ì', 'ò' => 'ò', 'ù' => 'ù',
            'ã' => 'ã', 'õ' => 'õ', 'ñ' => 'ñ', 'ç' => 'ç',
            'Á' => 'Á', 'É' => 'É', 'Í' => 'Í', 'Ó' => 'Ó', 'Ú' => 'Ú',
            'À' => 'À', 'È' => 'È', 'Ì' => 'Ì', 'Ò' => 'Ò', 'Ù' => 'Ù',
            'Ã' => 'Ã', 'Õ' => 'Õ', 'Ñ' => 'Ñ', 'Ç' => 'Ç'
        ];
        
        foreach ($reemplazos as $buscar => $reemplazar) {
            $texto = str_replace($buscar, $reemplazar, $texto);
        }
        
        return $texto;
    }
}