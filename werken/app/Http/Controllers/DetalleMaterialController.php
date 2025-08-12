<?php

namespace App\Http\Controllers;

use App\Models\DetalleMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DetalleMaterialController extends Controller
{
    public function show($numero)
    {
        // Validar que el número sea numérico
        if (!is_numeric($numero)) {
            return redirect()->route('busqueda')->with('error', 'Número de control inválido. Debe ser un número.');
        }
        
        // Convertir a entero para asegurar formato correcto
        $numero = (int) $numero;
        
        // Primero verificar si el número existe en V_TITULO
        $existeEnVTitulo = DB::table('V_TITULO')
            ->where('nro_control', $numero)
            ->first();
        
        if (!$existeEnVTitulo) {
            return redirect()->route('resultados')->with('error', 'No se encontró el material solicitado.');
        }
        
        // Inicializar objeto base del material
        $detalleMaterial = (object) [
            'nro_control' => $numero,
            'titulo' => $existeEnVTitulo->nombre_busqueda,
            'autor' => 'No disponible',
            'editorial' => 'No disponible',
            'nro_pedido' => $numero,
            'edicion' => 'No disponible',
            'datos_publicacion' => 'No disponible',
            'descripcion' => 'No disponible',
            'materiales' => 'No disponible',
            'existencias' => [],
            'isbn_issn' => 'No disponible',
            'tipo' => 'No disponible',
            'copias_registradas' => 'No disponible',
            'suscripcion' => 'No',
            'catalogador' => 'No disponible',
            'fecha_ingreso' => 'No disponible',
            'dewey' => 'No disponible',
            'titulo_normalizado' => 'No disponible'
        ];
        
        // 1. Consultar existencias usando sp_WEB_detalle_existencias
        try {
            \Log::info('Intentando consultar existencias para nro_control: ' . $numero);
            $existencias = DB::select("EXEC sp_WEB_detalle_existencias ?, ?", [$numero, 'con_reserva']);
            
            \Log::info('Resultado de sp_WEB_detalle_existencias:', [
                'nro_control' => $numero,
                'existencias_encontradas' => count($existencias),
                'existencias_data' => $existencias
            ]);
            
            if (!empty($existencias)) {
                $detalleMaterial->existencias = $existencias;
            } else {
                \Log::warning('sp_WEB_detalle_existencias devolvió un array vacío para nro_control: ' . $numero);
                $detalleMaterial->existencias = [];
            }
        } catch (\Exception $e) {
            // Si falla el SP, continuar con existencias vacías pero loggear el error
            \Log::error('Error ejecutando sp_WEB_detalle_existencias:', [
                'nro_control' => $numero,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $detalleMaterial->existencias = [];
        }
        
        // 2. Consultar información completa desde DETALLE_MATERIAL
        $infoDetalle = DB::table('DETALLE_MATERIAL as dm')
            ->select(
                'dm.DSM_CORRELATIVO as nro_pedido',
                'dm.DSM_AUTOR_EDITOR as autor',
                'dm.DSM_TITULO as titulo',
                'dm.DSM_EDITORIAL as editorial',
                'dm.DSM_ISBN_ISSN as isbn_issn',
                'dm.DSM_PUBLICACION as datos_publicacion',
                'dm.DSM_OBSERVACION as descripcion',
                'dm.DSM_REPRESENTACION as materiales',
                'dm.DSM_TIPO_MATERIAL as tipo',
                'dm.DSM_CANTIDAD_ORIGINAL as copias_registradas',
                'dm.DSM_IND_SUSCRIPCION as suscripcion',
                'dm.DSM_USUARIO as catalogador',
                'dm.DSM_FECHA as fecha_ingreso'
            )
            ->where('dm.SOM_NUMERO', $numero)
            ->first();
        
        // Debug: Log de la consulta SQL y resultado
        \Log::info('Consulta DETALLE_MATERIAL:', [
            'sql' => 'SELECT * FROM DETALLE_MATERIAL WHERE SOM_NUMERO = ?',
            'parametro' => $numero,
            'resultado_encontrado' => $infoDetalle ? 'SÍ' : 'NO',
            'datos_raw' => $infoDetalle
        ]);
        
        // Agregar información de DETALLE_MATERIAL si existe
        if ($infoDetalle) {
            // Usar trim y verificar si no está vacío después del trim
            $detalleMaterial->nro_pedido = !empty(trim($infoDetalle->nro_pedido ?? '')) ? trim($infoDetalle->nro_pedido) : $detalleMaterial->nro_pedido;
            $detalleMaterial->autor = !empty(trim($infoDetalle->autor ?? '')) ? trim($infoDetalle->autor) : $detalleMaterial->autor;
            $detalleMaterial->titulo = !empty(trim($infoDetalle->titulo ?? '')) ? trim($infoDetalle->titulo) : $detalleMaterial->titulo;
            $detalleMaterial->editorial = !empty(trim($infoDetalle->editorial ?? '')) ? trim($infoDetalle->editorial) : $detalleMaterial->editorial;
            $detalleMaterial->isbn_issn = !empty(trim($infoDetalle->isbn_issn ?? '')) ? trim($infoDetalle->isbn_issn) : $detalleMaterial->isbn_issn;
            $detalleMaterial->datos_publicacion = !empty(trim($infoDetalle->datos_publicacion ?? '')) ? trim($infoDetalle->datos_publicacion) : $detalleMaterial->datos_publicacion;
            $detalleMaterial->descripcion = !empty(trim($infoDetalle->descripcion ?? '')) ? trim($infoDetalle->descripcion) : $detalleMaterial->descripcion;
            $detalleMaterial->materiales = !empty(trim($infoDetalle->materiales ?? '')) ? trim($infoDetalle->materiales) : $detalleMaterial->materiales;
            $detalleMaterial->tipo = !empty(trim($infoDetalle->tipo ?? '')) ? trim($infoDetalle->tipo) : $detalleMaterial->tipo;
            $detalleMaterial->copias_registradas = !empty(trim($infoDetalle->copias_registradas ?? '')) ? trim($infoDetalle->copias_registradas) : $detalleMaterial->copias_registradas;
            $detalleMaterial->suscripcion = ($infoDetalle->suscripcion == 'S' || $infoDetalle->suscripcion == '1') ? 'Sí' : 'No';
            $detalleMaterial->catalogador = !empty(trim($infoDetalle->catalogador ?? '')) ? trim($infoDetalle->catalogador) : $detalleMaterial->catalogador;
            $detalleMaterial->fecha_ingreso = !empty(trim($infoDetalle->fecha_ingreso ?? '')) ? trim($infoDetalle->fecha_ingreso) : $detalleMaterial->fecha_ingreso;
            
            // Debug: Log de los valores obtenidos de DETALLE_MATERIAL
            \Log::info('Datos obtenidos de DETALLE_MATERIAL:', [
                'nro_control' => $numero,
                'autor' => $infoDetalle->autor,
                'titulo' => $infoDetalle->titulo,
                'editorial' => $infoDetalle->editorial,
                'isbn_issn' => $infoDetalle->isbn_issn,
                'datos_publicacion' => $infoDetalle->datos_publicacion,
                'descripcion' => $infoDetalle->descripcion,
                'materiales' => $infoDetalle->materiales,
                'tipo' => $infoDetalle->tipo,
                'copias_registradas' => $infoDetalle->copias_registradas,
                'catalogador' => $infoDetalle->catalogador,
                'fecha_ingreso' => $infoDetalle->fecha_ingreso
            ]);
        } else {
            \Log::info('No se encontraron datos en DETALLE_MATERIAL para nro_control: ' . $numero);
            
            // Si no hay datos en DETALLE_MATERIAL, usar el título de V_TITULO para buscar más información
            $tituloParaBusqueda = $existeEnVTitulo->nombre_busqueda;
            \Log::info('Intentando búsqueda con título de V_TITULO:', [
                'titulo' => $tituloParaBusqueda
            ]);
            
            // Buscar usando el título en sp_WEB_detalle_busqueda
            try {
                $resultadoBusquedaTitulo = DB::select("EXEC sp_WEB_detalle_busqueda ?, ?", [$tituloParaBusqueda, 3]); // 3 = búsqueda por título
                
                if (!empty($resultadoBusquedaTitulo)) {
                    \Log::info('Resultados encontrados con sp_WEB_detalle_busqueda por título:', [
                        'total_resultados' => count($resultadoBusquedaTitulo),
                        'primer_resultado' => $resultadoBusquedaTitulo[0] ?? 'N/A'
                    ]);
                    
                    // Buscar el material específico por nro_control
                    $materialEncontradoPorTitulo = collect($resultadoBusquedaTitulo)->first(function ($item) use ($numero) {
                        return (int) trim($item->nro_control) === (int) $numero;
                    });
                    
                    if ($materialEncontradoPorTitulo) {
                        // Llenar información obtenida del SP
                        $detalleMaterial->tipo = $materialEncontradoPorTitulo->tipo ?? $detalleMaterial->tipo;
                        $detalleMaterial->dewey = $materialEncontradoPorTitulo->dewey ?? $detalleMaterial->dewey;
                        $detalleMaterial->autor = $materialEncontradoPorTitulo->autor ?? $detalleMaterial->autor;
                        $detalleMaterial->datos_publicacion = $materialEncontradoPorTitulo->publicacion ?? $detalleMaterial->datos_publicacion;
                        $detalleMaterial->titulo_normalizado = $materialEncontradoPorTitulo->nombre_busqueda ?? $detalleMaterial->titulo_normalizado;
                        
                        \Log::info('Material encontrado por título:', [
                            'nro_control' => $materialEncontradoPorTitulo->nro_control,
                            'autor' => $materialEncontradoPorTitulo->autor,
                            'tipo' => $materialEncontradoPorTitulo->tipo,
                            'dewey' => $materialEncontradoPorTitulo->dewey,
                            'publicacion' => $materialEncontradoPorTitulo->publicacion
                        ]);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Error ejecutando sp_WEB_detalle_busqueda por título:', [
                    'error' => $e->getMessage(),
                    'titulo' => $tituloParaBusqueda
                ]);
            }
            
            // Intentar obtener información adicional de otras tablas relacionadas usando el nro_control
            $this->buscarInformacionAdicional($numero, $detalleMaterial);
        }
        
        // 3. Consultar información adicional usando sp_WEB_detalle_busqueda
        $textoBusqueda = session('texto_busqueda');
        $tipoBusqueda = session('tipo_busqueda');
        
        // Debug: Verificar valores de sesión
        \Log::info('Valores de sesión para búsqueda:', [
            'texto_busqueda' => $textoBusqueda,
            'tipo_busqueda' => $tipoBusqueda,
            'numero_material' => $numero,
            'sesion_completa' => session()->all()
        ]);
        
        // Mapeo de tipos de búsqueda a índices numéricos
        $tiposBusqueda = [
            'autor' => 1,
            'materia' => 2,
            'titulo' => 3,
            'editorial' => 4,
            'serie' => 5,
            'dewey' => 6
        ];
        
        if ($textoBusqueda && $tipoBusqueda && isset($tiposBusqueda[$tipoBusqueda])) {
            try {
                $indice = $tiposBusqueda[$tipoBusqueda];
                $resultadoBusqueda = DB::select("EXEC sp_WEB_detalle_busqueda ?, ?", [$textoBusqueda, $indice]);
                
                // Debug: Log para verificar los valores de sesión y resultados
                \Log::info('Valores de búsqueda:', [
                    'texto_busqueda' => $textoBusqueda,
                    'tipo_busqueda' => $tipoBusqueda,
                    'indice' => $indice,
                    'numero_buscado' => $numero,
                    'total_resultados' => count($resultadoBusqueda)
                ]);
                
                // Debug: Log de algunos nro_control de los resultados para comparar
                if (!empty($resultadoBusqueda)) {
                    $nrosControl = collect($resultadoBusqueda)->take(5)->pluck('nro_control')->toArray();
                    \Log::info('Primeros 5 nro_control encontrados: ' . implode(', ', $nrosControl));
                }
                
                // Buscar el material específico en los resultados con cast y trim
                $materialEncontrado = collect($resultadoBusqueda)->first(function ($item) use ($numero) {
                    return (int) trim($item->nro_control) === (int) $numero;
                });
                
                // Debug: Log del resultado de la búsqueda específica
                \Log::info('Material encontrado:', [
                    'encontrado' => $materialEncontrado ? 'SÍ' : 'NO',
                    'nro_control_encontrado' => $materialEncontrado->nro_control ?? 'N/A'
                ]);
                
                if ($materialEncontrado) {
                    $detalleMaterial->tipo = $materialEncontrado->tipo ?? $detalleMaterial->tipo;
                    $detalleMaterial->dewey = $materialEncontrado->dewey ?? $detalleMaterial->dewey;
                    $detalleMaterial->titulo_normalizado = $materialEncontrado->nombre_busqueda ?? $detalleMaterial->titulo_normalizado;
                    
                    // Si no tenemos autor desde DETALLE_MATERIAL, usar el de la búsqueda
                    if ($detalleMaterial->autor == 'No disponible' && isset($materialEncontrado->autor)) {
                        $detalleMaterial->autor = $materialEncontrado->autor;
                    }
                    
                    // Si no tenemos datos de publicación, usar los de la búsqueda
                    if ($detalleMaterial->datos_publicacion == 'No disponible' && isset($materialEncontrado->publicacion)) {
                        $detalleMaterial->datos_publicacion = $materialEncontrado->publicacion;
                    }
                } else {
                    // Si no se encuentra con los valores de sesión, intentar con el título de V_TITULO
                    \Log::info('Material no encontrado con valores de sesión, intentando con título de V_TITULO');
                    $this->buscarPorTituloYNroControl($existeEnVTitulo->nombre_busqueda, $numero, $detalleMaterial);
                }
            } catch (\Exception $e) {
                // Si falla el SP, continuar con valores por defecto
                \Log::error('Error ejecutando sp_WEB_detalle_busqueda:', [
                    'error' => $e->getMessage(),
                    'texto_busqueda' => $textoBusqueda,
                    'indice' => $indice ?? 'N/A'
                ]);
            }
        } else {
            // Si no hay valores de sesión, intentar buscar usando el número de control directamente
            \Log::info('No hay valores de sesión, intentando búsqueda alternativa por nro_control');
            
            // Intentar con cada tipo de búsqueda usando el número de control
            foreach ($tiposBusqueda as $tipo => $indice) {
                try {
                    $resultadoBusqueda = DB::select("EXEC sp_WEB_detalle_busqueda ?, ?", [$numero, $indice]);
                    
                    $materialEncontrado = collect($resultadoBusqueda)->first(function ($item) use ($numero) {
                        return (int) trim($item->nro_control) === (int) $numero;
                    });
                    
                    if ($materialEncontrado) {
                        $detalleMaterial->tipo = $materialEncontrado->tipo ?? $detalleMaterial->tipo;
                        $detalleMaterial->dewey = $materialEncontrado->dewey ?? $detalleMaterial->dewey;
                        $detalleMaterial->titulo_normalizado = $materialEncontrado->nombre_busqueda ?? $detalleMaterial->titulo_normalizado;
                        
                        \Log::info('Material encontrado en búsqueda alternativa:', [
                            'tipo_busqueda' => $tipo,
                            'nro_control' => $materialEncontrado->nro_control
                        ]);
                        break; // Salir del loop una vez encontrado
                    }
                } catch (\Exception $e) {
                    // Continuar con el siguiente tipo de búsqueda
                    continue;
                }
            }
        }
        
        // Asegurar que todos los campos tengan valores por defecto solo si están realmente vacíos
        $campos = [
            'nro_pedido', 'autor', 'titulo', 'editorial', 'isbn_issn', 
            'datos_publicacion', 'descripcion', 'materiales', 'tipo',
            'copias_registradas', 'catalogador', 'fecha_ingreso', 'dewey', 
            'titulo_normalizado'
        ];
        
        foreach ($campos as $campo) {
            // Solo reemplazar si es null, cadena vacía después de trim, o no está definido
            if (!isset($detalleMaterial->$campo) || 
                $detalleMaterial->$campo === null || 
                $detalleMaterial->$campo === '' || 
                trim($detalleMaterial->$campo) === '') {
                $detalleMaterial->$campo = 'No disponible';
            }
        }
        
        // Log final de todos los campos para debug
        \Log::info('Campos finales del detalle material:', [
            'nro_control' => $detalleMaterial->nro_control,
            'nro_pedido' => $detalleMaterial->nro_pedido,
            'autor' => $detalleMaterial->autor,
            'titulo' => $detalleMaterial->titulo,
            'editorial' => $detalleMaterial->editorial,
            'isbn_issn' => $detalleMaterial->isbn_issn,
            'datos_publicacion' => $detalleMaterial->datos_publicacion,
            'descripcion' => $detalleMaterial->descripcion,
            'materiales' => $detalleMaterial->materiales,
            'tipo' => $detalleMaterial->tipo,
            'copias_registradas' => $detalleMaterial->copias_registradas,
            'catalogador' => $detalleMaterial->catalogador,
            'fecha_ingreso' => $detalleMaterial->fecha_ingreso,
            'dewey' => $detalleMaterial->dewey,
            'titulo_normalizado' => $detalleMaterial->titulo_normalizado
        ]);
        
        // Campos especiales
        if (!isset($detalleMaterial->suscripcion)) {
            $detalleMaterial->suscripcion = 'No';
        }
        if (!isset($detalleMaterial->existencias)) {
            $detalleMaterial->existencias = [];
        }
        
        return view('detalle-material', [
            'detalleMaterial' => $detalleMaterial
        ]);
    }

    public function resumen($numero)
    {
        // Validar que el número sea numérico
        if (!is_numeric($numero)) {
            return redirect()->route('busqueda')->with('error', 'Número de control inválido. Debe ser un número.');
        }
        
        // Convertir a entero para asegurar formato correcto
        $numero = (int) $numero;
        
        // Consulta información completa del material para resumen
        $detalleMaterial = DB::table('DETALLE_MATERIAL as dm')
            ->select(
                'dm.SOM_NUMERO as nro_control',
                'dm.DSM_CORRELATIVO as nro_pedido',
                'dm.DSM_AUTOR_EDITOR as autor',
                'dm.DSM_TITULO as titulo',
                'dm.DSM_EDITORIAL as editorial',
                'dm.DSM_ISBN_ISSN as isbn_issn',
                'dm.DSM_PUBLICACION as datos_publicacion',
                'dm.DSM_CANTIDAD_ORIGINAL as copias',
                'dm.DSM_OBSERVACION as descripcion',
                'dm.DSM_TIPO_MATERIAL as tipo_material',
                'dm.DSM_IND_SUSCRIPCION',
                'dm.DSM_REPRESENTACION as materiales',
                'dm.DSM_USUARIO as catalogador',
                'dm.DSM_FECHA as fecha_catalogacion'
            )
            ->where('dm.SOM_NUMERO', $numero)
            ->first();

        if (!$detalleMaterial) {
            return redirect()->route('resultados')->with('error', 'No se encontró el material solicitado.');
        }

        // Agregar campos adicionales para compatibilidad
        $detalleMaterial->edicion = 'No disponible';
        $detalleMaterial->serie = null;
        $detalleMaterial->materia = null;
        $detalleMaterial->notas = $detalleMaterial->descripcion;
        $detalleMaterial->clasificacion_dewey = 'No disponible';
        $detalleMaterial->encabezamientos_materia = 'No disponible';
        
        return view('resumen-material', [
            'detalleMaterial' => $detalleMaterial
        ]);
    }
    
    /**
     * Método auxiliar para probar la conexión con sp_WEB_detalle_busqueda
     * y verificar los datos de sesión
     */
    public function testBusqueda($numero)
    {
        // Validar que el número sea numérico
        if (!is_numeric($numero)) {
            return response()->json(['error' => 'Número de control inválido. Debe ser un número.'], 400);
        }
        
        // Convertir a entero para asegurar formato correcto
        $numero = (int) $numero;
        
        $textoBusqueda = session('texto_busqueda');
        $tipoBusqueda = session('tipo_busqueda');
        
        $tiposBusqueda = [
            'autor' => 1,
            'materia' => 2,
            'titulo' => 3,
            'editorial' => 4,
            'serie' => 5,
            'dewey' => 6
        ];
        
        $resultado = [
            'numero_buscado' => $numero,
            'texto_busqueda' => $textoBusqueda,
            'tipo_busqueda' => $tipoBusqueda,
            'indice_numerico' => $tiposBusqueda[$tipoBusqueda] ?? 'No válido',
            'sesion_completa' => session()->all(),
            'resultados_sp' => []
        ];
        
        if ($textoBusqueda && $tipoBusqueda && isset($tiposBusqueda[$tipoBusqueda])) {
            try {
                $indice = $tiposBusqueda[$tipoBusqueda];
                $resultadoBusqueda = DB::select("EXEC sp_WEB_detalle_busqueda ?, ?", [$textoBusqueda, $indice]);
                
                $resultado['total_resultados'] = count($resultadoBusqueda);
                $resultado['primeros_5_resultados'] = collect($resultadoBusqueda)->take(5)->toArray();
                
                $materialEncontrado = collect($resultadoBusqueda)->first(function ($item) use ($numero) {
                    return (int) trim($item->nro_control) === (int) $numero;
                });
                
                $resultado['material_encontrado'] = $materialEncontrado ? $materialEncontrado : 'No encontrado';
                $resultado['comparaciones'] = [];
                
                // Mostrar comparaciones para debug
                foreach (collect($resultadoBusqueda)->take(10) as $item) {
                    $resultado['comparaciones'][] = [
                        'nro_control_original' => $item->nro_control,
                        'nro_control_trimmed' => trim($item->nro_control),
                        'nro_control_int' => (int) trim($item->nro_control),
                        'numero_buscado_int' => (int) $numero,
                        'coincide' => (int) trim($item->nro_control) === (int) $numero
                    ];
                }
                
            } catch (\Exception $e) {
                $resultado['error'] = $e->getMessage();
            }
        }
        
        return response()->json($resultado, 200, [], JSON_PRETTY_PRINT);
    }
    
    /**
     * Método auxiliar para probar la consulta DETALLE_MATERIAL
     */
    public function testDetalleMaterial($numero)
    {
        // Validar que el número sea numérico
        if (!is_numeric($numero)) {
            return response()->json(['error' => 'Número de control inválido. Debe ser un número.'], 400);
        }
        
        // Convertir a entero para asegurar formato correcto
        $numero = (int) $numero;
        
        // Consulta directa a DETALLE_MATERIAL para ver qué datos existen
        $detalleMaterialRaw = DB::table('DETALLE_MATERIAL')
            ->where('SOM_NUMERO', $numero)
            ->first();
        
        // Consulta con los alias que usamos en el controlador
        $detalleMaterialConAlias = DB::table('DETALLE_MATERIAL as dm')
            ->select(
                'dm.DSM_CORRELATIVO as nro_pedido',
                'dm.DSM_AUTOR_EDITOR as autor',
                'dm.DSM_TITULO as titulo',
                'dm.DSM_EDITORIAL as editorial',
                'dm.DSM_ISBN_ISSN as isbn_issn',
                'dm.DSM_PUBLICACION as datos_publicacion',
                'dm.DSM_OBSERVACION as descripcion',
                'dm.DSM_REPRESENTACION as materiales',
                'dm.DSM_TIPO_MATERIAL as tipo',
                'dm.DSM_CANTIDAD_ORIGINAL as copias_registradas',
                'dm.DSM_IND_SUSCRIPCION as suscripcion',
                'dm.DSM_USUARIO as catalogador',
                'dm.DSM_FECHA as fecha_ingreso'
            )
            ->where('dm.SOM_NUMERO', $numero)
            ->first();
        
        // También verificar si existe en V_TITULO
        $existeEnVTitulo = DB::table('V_TITULO')
            ->where('nro_control', $numero)
            ->first();
        
        // Buscar por diferentes variaciones del número
        $variacionesNumero = [
            $numero,
            (string) $numero,
            (int) $numero,
            str_pad($numero, 10, '0', STR_PAD_LEFT), // Con ceros a la izquierda
            trim($numero)
        ];
        
        $resultadosVariaciones = [];
        foreach ($variacionesNumero as $variacion) {
            $resultado = DB::table('DETALLE_MATERIAL')
                ->where('SOM_NUMERO', $variacion)
                ->first();
            if ($resultado) {
                $resultadosVariaciones[$variacion] = $resultado;
            }
        }
        
        // Buscar registros similares al número buscado
        $registrosSimilares = DB::table('DETALLE_MATERIAL')
            ->where('SOM_NUMERO', 'LIKE', '%' . $numero . '%')
            ->take(5)
            ->get();
        
        // Obtener una muestra más variada de SOM_NUMERO
        $muestraVariada = DB::table('DETALLE_MATERIAL')
            ->select('SOM_NUMERO')
            ->groupBy('SOM_NUMERO')
            ->take(20)
            ->pluck('SOM_NUMERO')
            ->toArray();
        
        // Estadísticas de la tabla
        $estadisticas = [
            'total_registros' => DB::table('DETALLE_MATERIAL')->count(),
            'som_numero_unicos' => DB::table('DETALLE_MATERIAL')->distinct('SOM_NUMERO')->count('SOM_NUMERO'),
            'som_numero_mas_comunes' => DB::table('DETALLE_MATERIAL')
                ->select('SOM_NUMERO', DB::raw('COUNT(*) as cantidad'))
                ->groupBy('SOM_NUMERO')
                ->orderBy('cantidad', 'DESC')
                ->take(10)
                ->get()
                ->toArray()
        ];
        
        return response()->json([
            'numero_buscado' => $numero,
            'existe_en_v_titulo' => $existeEnVTitulo ? 'SÍ' : 'NO',
            'datos_v_titulo' => $existeEnVTitulo,
            'existe_en_detalle_material_raw' => $detalleMaterialRaw ? 'SÍ' : 'NO',
            'datos_detalle_material_raw' => $detalleMaterialRaw,
            'existe_en_detalle_material_con_alias' => $detalleMaterialConAlias ? 'SÍ' : 'NO',
            'datos_detalle_material_con_alias' => $detalleMaterialConAlias,
            'variaciones_encontradas' => $resultadosVariaciones,
            'registros_similares' => $registrosSimilares,
            'muestra_variada_som_numero' => $muestraVariada,
            'estadisticas_tabla' => $estadisticas
        ], 200, [], JSON_PRETTY_PRINT);
    }
    
    /**
     * Método auxiliar para buscar información adicional en otras tablas usando nro_control
     */
    private function buscarInformacionAdicional($numero, &$detalleMaterial)
    {
        \Log::info('Buscando información adicional para nro_control: ' . $numero);
        
        // Lista de tablas a consultar con sus posibles campos
        $tablasConsulta = [
            'TITULO' => [
                'campos' => ['autor', 'editorial', 'isbn', 'ano_publicacion', 'descripcion'],
                'condicion' => 'nro_control'
            ],
            'AUTOR' => [
                'campos' => ['nombre_autor', 'autor_principal'],
                'condicion' => 'nro_control'
            ],
            'EDITORIAL' => [
                'campos' => ['nombre_editorial', 'lugar_publicacion'],
                'condicion' => 'nro_control'
            ],
            'MATERIA' => [
                'campos' => ['descriptor', 'materia_principal'],
                'condicion' => 'nro_control'
            ]
        ];
        
        foreach ($tablasConsulta as $tabla => $config) {
            try {
                $query = DB::table($tabla)->where($config['condicion'], $numero);
                
                // Verificar si la tabla existe y tiene registros
                $existe = DB::select("SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = ?", [$tabla]);
                
                if ($existe[0]->count > 0) {
                    $resultado = $query->first();
                    
                    if ($resultado) {
                        \Log::info("Información encontrada en tabla {$tabla}: " . json_encode($resultado));
                        
                        // Mapear campos según la tabla
                        $this->mapearCamposTabla($tabla, $resultado, $detalleMaterial);
                    }
                }
            } catch (\Exception $e) {
                \Log::debug("No se pudo consultar tabla {$tabla}: " . $e->getMessage());
                continue;
            }
        }
        
        // También intentar buscar en tablas con prefijos o sufijos comunes
        $variacionesTablas = [
            'TB_TITULO',
            'TITULO_DETALLE', 
            'MATERIAL_TITULO',
            'OBRA'
        ];
        
        foreach ($variacionesTablas as $tabla) {
            try {
                $existe = DB::select("SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = ?", [$tabla]);
                
                if ($existe[0]->count > 0) {
                    $resultado = DB::table($tabla)->where('nro_control', $numero)->first();
                    
                    if ($resultado) {
                        \Log::info("Información encontrada en tabla adicional {$tabla}: " . json_encode($resultado));
                        $this->mapearCamposTabla($tabla, $resultado, $detalleMaterial);
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }
    }
    
    /**
     * Mapear campos de diferentes tablas al objeto detalleMaterial
     */
    private function mapearCamposTabla($tabla, $resultado, &$detalleMaterial)
    {
        // Convertir objeto a array para facilitar el acceso
        $datos = (array) $resultado;
        
        foreach ($datos as $campo => $valor) {
            if (!empty(trim($valor ?? ''))) {
                $valor = trim($valor);
                
                // Mapeo inteligente de campos según nombres comunes
                switch (strtolower($campo)) {
                    case 'autor':
                    case 'nombre_autor':
                    case 'autor_principal':
                        if ($detalleMaterial->autor == 'No disponible') {
                            $detalleMaterial->autor = $valor;
                        }
                        break;
                        
                    case 'editorial':
                    case 'nombre_editorial':
                        if ($detalleMaterial->editorial == 'No disponible') {
                            $detalleMaterial->editorial = $valor;
                        }
                        break;
                        
                    case 'isbn':
                    case 'isbn_issn':
                        if ($detalleMaterial->isbn_issn == 'No disponible') {
                            $detalleMaterial->isbn_issn = $valor;
                        }
                        break;
                        
                    case 'descripcion':
                    case 'observacion':
                        if ($detalleMaterial->descripcion == 'No disponible') {
                            $detalleMaterial->descripcion = $valor;
                        }
                        break;
                        
                    case 'materia':
                    case 'descriptor':
                    case 'materia_principal':
                        if ($detalleMaterial->materiales == 'No disponible') {
                            $detalleMaterial->materiales = $valor;
                        }
                        break;
                }
            }
        }
    }
    
    /**
     * Método auxiliar para buscar por título y nro_control
     */
    private function buscarPorTituloYNroControl($titulo, $numero, &$detalleMaterial)
    {
        try {
            $resultadoBusqueda = DB::select("EXEC sp_WEB_detalle_busqueda ?, ?", [$titulo, 3]); // 3 = búsqueda por título
            
            if (!empty($resultadoBusqueda)) {
                \Log::info('Búsqueda por título ejecutada:', [
                    'titulo' => $titulo,
                    'total_resultados' => count($resultadoBusqueda)
                ]);
                
                // Buscar el material específico por nro_control
                $materialEncontrado = collect($resultadoBusqueda)->first(function ($item) use ($numero) {
                    return (int) trim($item->nro_control) === (int) $numero;
                });
                
                if ($materialEncontrado) {
                    // Llenar información obtenida del SP
                    $detalleMaterial->tipo = $materialEncontrado->tipo ?? $detalleMaterial->tipo;
                    $detalleMaterial->dewey = $materialEncontrado->dewey ?? $detalleMaterial->dewey;
                    $detalleMaterial->autor = $materialEncontrado->autor ?? $detalleMaterial->autor;
                    $detalleMaterial->datos_publicacion = $materialEncontrado->publicacion ?? $detalleMaterial->datos_publicacion;
                    $detalleMaterial->titulo_normalizado = $materialEncontrado->nombre_busqueda ?? $detalleMaterial->titulo_normalizado;
                    
                    \Log::info('Material encontrado por título y nro_control:', [
                        'nro_control' => $materialEncontrado->nro_control,
                        'autor' => $materialEncontrado->autor,
                        'tipo' => $materialEncontrado->tipo,
                        'dewey' => $materialEncontrado->dewey
                    ]);
                } else {
                    \Log::info('Material no encontrado en resultados de búsqueda por título');
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error en búsqueda por título y nro_control:', [
                'error' => $e->getMessage(),
                'titulo' => $titulo,
                'numero' => $numero
            ]);
        }
    }
    
    /**
     * Método de prueba para verificar las existencias directamente  
     */
    public function testExistencias($numero)
    {
        // Validar que el número sea numérico
        if (!is_numeric($numero)) {
            return response()->json(['error' => 'Número de control inválido'], 400);
        }
        
        $numero = (int) $numero;
        
        try {
            // Ejecutar sp_WEB_detalle_existencias directamente
            $existencias = DB::select("EXEC sp_WEB_detalle_existencias ?, ?", [$numero, 'con_reserva']);
            
            // También obtener información básica del material 
            $existeEnVTitulo = DB::table('V_TITULO')
                ->where('nro_control', $numero)
                ->first();
                
            return response()->json([
                'numero_buscado' => $numero,
                'existe_en_v_titulo' => $existeEnVTitulo ? 'SÍ' : 'NO',
                'datos_v_titulo' => $existeEnVTitulo,
                'existencias_total' => count($existencias),
                'existencias_datos' => $existencias
            ], 200, [], JSON_PRETTY_PRINT);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error ejecutando SP: ' . $e->getMessage()
            ], 500);
        }
    }
}
