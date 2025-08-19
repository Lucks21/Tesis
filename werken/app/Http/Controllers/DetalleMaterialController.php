<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DetalleMaterialController extends Controller
{    
    public function show($numero)
    {
        // Verificar cache primero (TTL 30 minutos)
        $cacheKey = "detalle_material_{$numero}";
        $detalleMaterial = Cache::get($cacheKey);
        
        if ($detalleMaterial) {
            return view('detalle-material', [
                'detalleMaterial' => $detalleMaterial
            ]);
        }
        
        // Validar existencia en V_TITULO y obtener título
        $existeEnVTitulo = DB::table('V_TITULO')
            ->where('nro_control', $numero)
            ->first();
        
        if (!$existeEnVTitulo) {
            return redirect()->route('resultados')->with('error', 'No se encontró el material solicitado.');
        }
        
        // Obtener título desde V_TITULO
        $titulo = trim($existeEnVTitulo->nombre_busqueda ?? 'Sin título');
        
        // Obtener autores (1-a-N) - consulta separada, limpiar, deduplicar
        $autores = DB::table('V_AUTOR')
            ->where('nro_control', $numero)
            ->pluck('nombre_busqueda')
            ->map(function($item) {
                return trim($item);
            })
            ->filter(function($item) {
                return !empty($item);
            })
            ->unique()
            ->values()
            ->toArray();
        
        // Obtener editoriales (1-a-N) - consulta separada, limpiar, deduplicar
        $editoriales = DB::table('V_EDITORIAL')
            ->where('nro_control', $numero)
            ->pluck('nombre_busqueda')
            ->map(function($item) {
                return trim($item);
            })
            ->filter(function($item) {
                return !empty($item);
            })
            ->unique()
            ->values()
            ->toArray();
        
        // Obtener materias (1-a-N) - consulta separada, limpiar, deduplicar
        $materias = DB::table('V_MATERIA')
            ->where('nro_control', $numero)
            ->pluck('nombre_busqueda')
            ->map(function($item) {
                return trim($item);
            })
            ->filter(function($item) {
                return !empty($item);
            })
            ->unique()
            ->values()
            ->toArray();
        
        // Obtener series (1-a-N) - consulta separada, limpiar, deduplicar
        $series = DB::table('V_SERIE')
            ->where('nro_control', $numero)
            ->pluck('nombre_busqueda')
            ->map(function($item) {
                return trim($item);
            })
            ->filter(function($item) {
                return !empty($item);
            })
            ->unique()
            ->values()
            ->toArray();
        
        // Obtener clasificación Dewey (1-a-N) - consulta separada, limpiar, deduplicar
        $dewey = DB::table('V_DEWEY')
            ->where('nro_control', $numero)
            ->pluck('nombre_busqueda')
            ->map(function($item) {
                return trim($item);
            })
            ->filter(function($item) {
                return !empty($item);
            })
            ->unique()
            ->values()
            ->toArray();
        
        // Obtener idiomas (1-a-N) - consulta separada, limpiar, deduplicar
        $idiomas = DB::table('V_IDIOMA')
            ->where('nro_control', $numero)
            ->pluck('nombre_busqueda')
            ->map(function($item) {
                return trim($item);
            })
            ->filter(function($item) {
                return !empty($item);
            })
            ->unique()
            ->values()
            ->toArray();
        
        // Obtener información adicional desde el stored procedure sp_WEB_resumen
        $notas = [];
        $otrosTitulos = [];
        $edicion = '';
        $datosPublicacion = '';
        $descripcion = '';
        $isbn = [];
        
        try {
            // Ejecutar el stored procedure con SET NOCOUNT ON y esquema completo
            $resultadoResumen = DB::select(
                'SET NOCOUNT ON; EXEC dbo.sp_WEB_resumen @int_nro_control = :nro',
                ['nro' => (int) $numero]
            );
            
            if (!empty($resultadoResumen)) {
                // Procesar cada resultado del SP
                foreach ($resultadoResumen as $item) {
                    // Procesar según el campo titulo_resumen que indica el tipo de datos
                    if (isset($item->titulo_resumen) && isset($item->nombre_resumen)) {
                        $tipoResumen = trim($item->titulo_resumen);
                        $valorResumen = trim($item->nombre_resumen);
                        
                        if (!empty($valorResumen)) {
                            // Buscar específicamente "Nota(s)"
                            if (stripos($tipoResumen, 'Nota(s)') !== false) {
                                $notas[] = $valorResumen;
                            }
                            
                            // Buscar específicamente "Otro(s) Título(s)"
                            if (stripos($tipoResumen, 'Otro(s) Título(s)') !== false) {
                                $otrosTitulos[] = $valorResumen;
                            }
                            
                            // Buscar "Edición"
                            if (stripos($tipoResumen, 'Edición') !== false) {
                                $edicion = $valorResumen;
                            }
                            
                            // Buscar "Datos de Publicación"
                            if (stripos($tipoResumen, 'Datos de Publicación') !== false) {
                                $datosPublicacion = $valorResumen;
                            }
                            
                            // Buscar "Descripción"
                            if (stripos($tipoResumen, 'Descripción') !== false) {
                                $descripcion = $valorResumen;
                            }
                            
                            // Buscar "ISBN"
                            if (stripos($tipoResumen, 'ISBN') !== false) {
                                $isbn[] = $valorResumen;
                            }
                        }
                    }
                    
                    // Fallback: si solo hay nombre_resumen sin titulo_resumen, procesarlo por contenido
                    if (isset($item->nombre_resumen) && !empty(trim($item->nombre_resumen)) && 
                        (empty($item->titulo_resumen) || is_null($item->titulo_resumen))) {
                        
                        $valorLimpio = trim($item->nombre_resumen);
                        
                        // Si contiene "Título original:" es probablemente una nota
                        if (preg_match('/^Título original:/i', $valorLimpio)) {
                            $notas[] = $valorLimpio;
                        }
                        
                        // Si contiene patrones de otros títulos
                        if (preg_match('/^(También conocido como|Otro título|Título alternativo):/i', $valorLimpio)) {
                            $otrosTitulos[] = $valorLimpio;
                        }
                    }
                }
                
                // Limpiar y deduplicar arrays
                $notas = array_values(array_unique(array_filter($notas)));
                $otrosTitulos = array_values(array_unique(array_filter($otrosTitulos)));
                $isbn = array_values(array_unique(array_filter($isbn)));
                
            } else {
                // Si no hay resultados del SP, mantener valores vacíos
            }
            
        } catch (\Exception $e) {
            // En caso de error, usar valores vacíos
            $notas = [];
            $otrosTitulos = [];
            $edicion = '';
            $datosPublicacion = '';
            $descripcion = '';
            $isbn = [];
        }
        
        // Construir objeto de detalle con título y arrays
        $detalleMaterial = (object) [
            'nro_control' => $numero,
            'titulo' => $titulo,
            'autores' => $autores,
            'editoriales' => $editoriales,
            'materias' => $materias,
            'series' => $series,
            'dewey' => $dewey,
            'idiomas' => $idiomas,
            'notas' => $notas,
            'otros_titulos' => $otrosTitulos,
            'edicion' => $edicion,
            'datos_publicacion' => $datosPublicacion,
            'descripcion' => $descripcion,
            'isbn' => $isbn
        ];
        
        // Cachear resultado por 30 minutos
        Cache::put($cacheKey, $detalleMaterial, now()->addMinutes(30));
        
        return view('detalle-material', [
            'detalleMaterial' => $detalleMaterial
        ]);
    }
}
