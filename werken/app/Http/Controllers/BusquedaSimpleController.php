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
            // CASO 1: Clic en "Ver títulos" - viene con termino, tipo y ver_titulos
            if (($verTitulos && $termino && $tipo) || ($termino && $tipo && !$textoBusqueda)) {
                \Log::info('Ejecutando mostrarTitulosAsociados');
                return $this->mostrarTitulosAsociados($termino, $tipo, $request);
            }
            
            // Si hay un valor seleccionado, mostrar títulos asociados
            if ($valorSeleccionado) {
                \Log::info('Ejecutando mostrarTitulosAsociados con valor seleccionado');
                return $this->mostrarTitulosAsociados($valorSeleccionado, $tipoBusqueda, $request);
            }
            
            // Usar textoBusqueda si no hay termino específico
            $busquedaFinal = $termino ?: $textoBusqueda;
            $tipoFinal = $tipo ?: $tipoBusqueda;
            
            \Log::info('Parámetros finales', [
                'busquedaFinal' => $busquedaFinal,
                'tipoFinal' => $tipoFinal
            ]);
            
            // Si es búsqueda por título (tipo 3), usar el SP directamente
            if ($tipoFinal == 3) {
                \Log::info('Ejecutando buscarTitulos (tipo 3)');
                return $this->buscarTitulos($busquedaFinal, $tipoFinal, $request);
            }
            
            // Para otros tipos (autor, materia, editorial, serie, dewey), buscar elementos
            \Log::info('Ejecutando buscarElementos');
            return $this->buscarElementos($busquedaFinal, $tipoFinal, $request);

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
        
        // Ejecutar el stored procedure para títulos
        $resultadosBrutos = DB::select('EXEC sp_WEB_detalle_busqueda ?, ?', [
            $textoBusqueda,
            $tipoBusqueda
        ]);

        // Procesar los resultados para el formato esperado por la vista
        $resultadosProcesados = collect($resultadosBrutos)->map(function ($item) {
            return (object) [
                'nro_control' => $item->nro_control,
                'titulo' => $item->nombre_busqueda,
                'nombre_autor' => $item->autor,
                'nombre_editorial' => $item->editorial ?? null,
                'nombre_materia' => $item->materia ?? null,
                'nombre_serie' => $item->serie ?? null,
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
        $campuses = collect();    

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
        $orden = $request->input('orden', 'titulo_asc');

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
            'campuses'
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
            
            $resultadosProcesados = collect($resultadosBrutos)->map(function ($item) {
                return (object) [
                    'nro_control' => $this->getValue($item, ['nro_control', 'numero_control']),
                    'titulo' => $this->getValue($item, ['nombre_busqueda', 'titulo']),
                    'nombre_autor' => $this->getValue($item, ['autor', 'nombre_autor']),
                    'nombre_editorial' => $this->getValue($item, ['editorial', 'nombre_editorial']),
                    'nombre_materia' => $this->getValue($item, ['materia', 'nombre_materia']),
                    'nombre_serie' => $this->getValue($item, ['serie', 'nombre_serie']),
                    'biblioteca' => $this->getValue($item, ['biblioteca', 'nombre_biblioteca']),
                    'anio_publicacion' => $this->getValue($item, ['publicacion', 'anio_publicacion', 'año_publicacion']),
                    'tipo_material' => $this->getValue($item, ['tipo', 'tipo_material']),
                    'isbn' => $this->getValue($item, ['isbn']),
                    'signatura_topografica' => $this->getValue($item, ['signatura', 'signatura_topografica']),
                ];
            });

            // Obtener datos únicos para filtros
            $autores = collect($resultadosBrutos)->map(function($item) {
                return property_exists($item, 'autor') ? $item->autor : 
                       (property_exists($item, 'nombre_autor') ? $item->nombre_autor : null);
            })->filter()->unique()->sort()->values();
            
            $editoriales = collect($resultadosBrutos)->map(function($item) {
                return property_exists($item, 'editorial') ? $item->editorial : 
                       (property_exists($item, 'nombre_editorial') ? $item->nombre_editorial : null);
            })->filter()->unique()->sort()->values();
            
            $materias = collect($resultadosBrutos)->map(function($item) {
                return property_exists($item, 'materia') ? $item->materia : 
                       (property_exists($item, 'nombre_materia') ? $item->nombre_materia : null);
            })->filter()->unique()->sort()->values();
            
            $series = collect($resultadosBrutos)->map(function($item) {
                return property_exists($item, 'serie') ? $item->serie : 
                       (property_exists($item, 'nombre_serie') ? $item->nombre_serie : null);
            })->filter()->unique()->sort()->values();
            
            $campuses = collect();

            // Aplicar filtros si están presentes
            $resultadosProcesados = $this->aplicarFiltros($resultadosProcesados, $request);

            // Aplicar ordenamiento
            $orden = $request->input('orden', 'titulo_asc');
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
            $orden = $request->input('orden', 'titulo_asc');

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
                'campuses'
            ));
        } catch (\Exception $e) {
            // Crear vista de error simple
            $criterio = $this->getTipoBusquedaNombre($tipoBusqueda);
            $valorCriterio = $valorSeleccionado;
            $resultados = collect();
            $autores = collect();
            $editoriales = collect();
            $materias = collect();
            $series = collect();
            $años = collect();
            $tipos = collect();
            $campuses = collect();
            
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
                'valorCriterio'
            ));
        }
    }

    /**
     * Aplicar filtros a los resultados
     */
    private function aplicarFiltros($resultados, $request)
    {
        $filtrados = $resultados;

        // Filtrar por autor
        if ($request->filled('autor')) {
            $autoresFiltro = is_array($request->input('autor')) ? $request->input('autor') : [$request->input('autor')];
            $filtrados = $filtrados->filter(function ($item) use ($autoresFiltro) {
                return in_array($item->nombre_autor, $autoresFiltro);
            });
        }

        // Filtrar por editorial
        if ($request->filled('editorial')) {
            $editorialesFiltro = is_array($request->input('editorial')) ? $request->input('editorial') : [$request->input('editorial')];
            $filtrados = $filtrados->filter(function ($item) use ($editorialesFiltro) {
                return $item->nombre_editorial && in_array($item->nombre_editorial, $editorialesFiltro);
            });
        }

        // Filtrar por materia
        if ($request->filled('materia')) {
            $materiasFiltro = is_array($request->input('materia')) ? $request->input('materia') : [$request->input('materia')];
            $filtrados = $filtrados->filter(function ($item) use ($materiasFiltro) {
                return $item->nombre_materia && in_array($item->nombre_materia, $materiasFiltro);
            });
        }

        // Filtrar por serie
        // Filtrar por serie
        if ($request->filled('serie')) {
            $seriesFiltro = is_array($request->input('serie')) ? $request->input('serie') : [$request->input('serie')];
            $filtrados = $filtrados->filter(function ($item) use ($seriesFiltro) {
                return $item->nombre_serie && in_array($item->nombre_serie, $seriesFiltro);
            });
        }

        // Filtrar por campus/biblioteca
        if ($request->filled('campus')) {
            $campusFiltro = is_array($request->input('campus')) ? $request->input('campus') : [$request->input('campus')];
            $filtrados = $filtrados->filter(function ($item) use ($campusFiltro) {
                return $item->biblioteca && in_array($item->biblioteca, $campusFiltro);
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
        $orden = 'titulo_asc';
        $autores = collect();
        $editoriales = collect();
        $materias = collect();
        $series = collect();
        $campuses = collect();

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
            'campuses'
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
        switch ($orden) {
            case 'titulo_asc':
                return $resultados->sortBy('titulo');
            case 'titulo_desc':
                return $resultados->sortByDesc('titulo');
            case 'autor_asc':
                return $resultados->sortBy('nombre_autor');
            case 'autor_desc':
                return $resultados->sortByDesc('nombre_autor');
            case 'editorial_asc':
                return $resultados->sortBy('nombre_editorial');
            case 'editorial_desc':
                return $resultados->sortByDesc('nombre_editorial');
            case 'año_asc':
                return $resultados->sortBy('anio_publicacion');
            case 'año_desc':
                return $resultados->sortByDesc('anio_publicacion');
            default:
                return $resultados->sortBy('titulo');
        }
    }
}
