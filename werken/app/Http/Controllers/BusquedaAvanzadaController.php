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
        // Aumentar timeout al inicio del método
        set_time_limit(300); // 5 minutos
        ini_set('max_execution_time', 300);
        
        // Recuperar los parámetros del request
        $criterio = $request->input('criterio');
        $valorCriterio = $request->input('valor_criterio');
        $titulo = $request->input('titulo');
        $orden = $request->input('orden', 'asc');
        $pagina = $request->input('page', 1);

        $autorFiltro = $request->input('autor', []);
        $editorialFiltro = $request->input('editorial', []);
        $campusFiltro = $request->input('campus', []);
        $materiaFiltro = $request->input('materia', []);
        $serieFiltro = $request->input('serie', []);
        $tipoMaterialFiltro = $request->input('tipo_material', []);

        // Procesar los filtros para manejar tanto arrays como strings separadas por comas
        $autorFiltro = $this->procesarFiltro($autorFiltro);
        $editorialFiltro = $this->procesarFiltro($editorialFiltro);
        $campusFiltro = $this->procesarFiltro($campusFiltro);
        $materiaFiltro = $this->procesarFiltro($materiaFiltro);
        $serieFiltro = $this->procesarFiltro($serieFiltro);
        $tipoMaterialFiltro = $this->procesarFiltro($tipoMaterialFiltro);

        // Procesar los parámetros de entrada para crear un texto procesado
        $filtros = [
            'autor' => $autorFiltro,
            'editorial' => $editorialFiltro,
            'campus' => $campusFiltro,
            'materia' => $materiaFiltro,
            'serie' => $serieFiltro,
            'tipo_material' => $tipoMaterialFiltro,
            'orden' => $orden
        ];
        $texto_procesado = $titulo . '|' . $valorCriterio . '|' . serialize($filtros);

        // Definir clave de cache para filtros (necesaria tanto para consultas nuevas como paginación)
        $filtros_cache_key = 'filtros_' . md5($texto_procesado . $criterio);

        // Verificar si necesitamos ejecutar nueva consulta con TTL mejorado
        $cache_timestamp = session('cache_timestamp', 0);
        $cache_ttl = config('app.cacshe_ttl', 1800); // 30 min
        $cache_expired = (time() - $cache_timestamp) > $cache_ttl;
        
        $ejecutar_nueva_consulta = (
            !session()->has('texto_busqueda') ||
            session('texto_busqueda') != $texto_procesado ||
            session('tipo_busqueda') != $criterio ||
            session('ind_busqueda') != 'avanzada' ||
            $cache_expired
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
                ->leftJoin('TB_CAMPUS as tc', 'e.campus_tb_campus', '=', 'tc.campus_tb_campus')
                ->leftJoin('DETALLE_MATERIAL as dm', 'vt.nombre_busqueda', '=', 'dm.DSM_TITULO');

            // Aplicar criterios de búsqueda solo si tienen valores
            $orderByField = 'vt.nombre_busqueda'; // Campo por defecto para ordenar
            
            switch ($criterio) {
                case 'autor':
                    if (!empty($valorCriterio)) {
                        $query->where(function($q) use ($valorCriterio) {
                            // Buscar la cadena completa primero (ej: "pablo neruda")
                            $q->where('va.nombre_busqueda', 'LIKE', "%{$valorCriterio}%");
                            
                            // Si tiene 2+ palabras, buscar que contenga TODAS las palabras significativas
                            $palabras = $this->extraerPalabrasSignificativas($valorCriterio);
                            
                            if (count($palabras) >= 2) {
                                // Usar AND para asegurar que TODAS las palabras estén presentes
                                $q->orWhere(function($andQ) use ($palabras) {
                                    foreach ($palabras as $palabra) {
                                        $andQ->where('va.nombre_busqueda', 'LIKE', "%{$palabra}%");
                                    }
                                });
                            }
                        });
                    }
                    $orderByField = 'va.nombre_busqueda';
                    break;
                case 'editorial':
                    if (!empty($valorCriterio)) {
                        $query->where(function($q) use ($valorCriterio) {
                            // Buscar la cadena completa primero (ej: "grupo editorial norma")
                            $q->where('ve.nombre_busqueda', 'LIKE', "%{$valorCriterio}%");
                            
                            // Si tiene 2+ palabras, buscar que contenga TODAS las palabras significativas
                            $palabras = $this->extraerPalabrasSignificativas($valorCriterio);
                            
                            if (count($palabras) >= 2) {
                                // Usar AND para asegurar que TODAS las palabras estén presentes
                                $q->orWhere(function($andQ) use ($palabras) {
                                    foreach ($palabras as $palabra) {
                                        $andQ->where('ve.nombre_busqueda', 'LIKE', "%{$palabra}%");
                                    }
                                });
                            }
                        });
                    }
                    $orderByField = 've.nombre_busqueda';
                    break;
                case 'materia':
                    if (!empty($valorCriterio)) {
                        $query->where(function($q) use ($valorCriterio) {
                            // Buscar la cadena completa primero (ej: "ciencias sociales")
                            $q->where('vm.nombre_busqueda', 'LIKE', "%{$valorCriterio}%");
                            
                            // Si tiene 2+ palabras, buscar que contenga TODAS las palabras significativas
                            $palabras = $this->extraerPalabrasSignificativas($valorCriterio);
                            
                            if (count($palabras) >= 2) {
                                // Usar AND para asegurar que TODAS las palabras estén presentes
                                $q->orWhere(function($andQ) use ($palabras) {
                                    foreach ($palabras as $palabra) {
                                        $andQ->where('vm.nombre_busqueda', 'LIKE', "%{$palabra}%");
                                    }
                                });
                            }
                        });
                    }
                    $orderByField = 'vm.nombre_busqueda';
                    break;
                case 'serie':
                    if (!empty($valorCriterio)) {
                        $query->where(function($q) use ($valorCriterio) {
                            // Buscar la cadena completa primero (ej: "series de matemáticas")
                            $q->where('vs.nombre_busqueda', 'LIKE', "%{$valorCriterio}%");
                            
                            // Si tiene 2+ palabras, buscar que contenga TODAS las palabras significativas
                            $palabras = $this->extraerPalabrasSignificativas($valorCriterio);
                            
                            if (count($palabras) >= 2) {
                                // Usar AND para asegurar que TODAS las palabras estén presentes
                                $q->orWhere(function($andQ) use ($palabras) {
                                    foreach ($palabras as $palabra) {
                                        $andQ->where('vs.nombre_busqueda', 'LIKE', "%{$palabra}%");
                                    }
                                });
                            }
                        });
                    }
                    $orderByField = 'vs.nombre_busqueda';
                    break;
                default:
                    $orderByField = 'vt.nombre_busqueda';
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
                'tc.nombre_tb_campus as biblioteca',
                'dm.DSM_TIPO_MATERIAL as tipo_material'
            ];

            // Solo agregar cálculo de relevancia si hay criterios de búsqueda
            if (!empty($titulo) || !empty($valorCriterio)) {
                $selectFields[] = DB::raw("(
                    (CASE WHEN vt.nombre_busqueda = '{$titulo}' THEN 5 ELSE 0 END) +
                    (CASE WHEN vt.nombre_busqueda LIKE '%{$titulo}%' THEN 3 ELSE 0 END) +
                    (CASE WHEN va.nombre_busqueda = '{$valorCriterio}' THEN 4 ELSE 0 END) +
                    (CASE WHEN va.nombre_busqueda LIKE '%{$valorCriterio}%' THEN 2 ELSE 0 END)
                ) as relevancia");
            } else {
                $selectFields[] = DB::raw("0 as relevancia");
            }

            // Agregar campos específicos según el criterio para poder ordenar
            if ($criterio === 'materia') {
                $selectFields[] = 'vm.nombre_busqueda as materia_orden';
            } elseif ($criterio === 'serie') {
                $selectFields[] = 'vs.nombre_busqueda as serie_orden';
            }

            $query->select($selectFields);

            // Filtros adicionales - usar comparación más robusta
            if (!empty($titulo)) {
                $query->where(function($q) use ($titulo) {
                    // Búsqueda más flexible en el título
                    $q->where('vt.nombre_busqueda', 'LIKE', "%{$titulo}%");
                });
            }

            if (!empty($autorFiltro) && count($autorFiltro) > 0) {
                $query->where(function($q) use ($autorFiltro) {
                    foreach ($autorFiltro as $autor) {
                        $q->orWhere(function($subQ) use ($autor) {
                            // Buscar la cadena completa primero (ej: "pablo neruda")
                            $subQ->where('va.nombre_busqueda', 'LIKE', "%{$autor}%")
                                 ->whereNotNull('va.nombre_busqueda');
                            
                            // También buscar coincidencia exacta después de trim para mayor precisión
                            $subQ->orWhere(function($exactQ) use ($autor) {
                                $exactQ->where('va.nombre_busqueda', '=', trim($autor))
                                       ->orWhereRaw('TRIM(va.nombre_busqueda) = ?', [trim($autor)])
                                       ->whereNotNull('va.nombre_busqueda');
                            });
                            
                            // Si tiene 2+ palabras, buscar que contenga TODAS las palabras significativas
                            $palabras = $this->extraerPalabrasSignificativas($autor);
                            
                            if (count($palabras) >= 2) {
                                // Usar AND para asegurar que TODAS las palabras estén presentes
                                $subQ->orWhere(function($andQ) use ($palabras) {
                                    foreach ($palabras as $palabra) {
                                        $andQ->where('va.nombre_busqueda', 'LIKE', "%{$palabra}%")
                                             ->whereNotNull('va.nombre_busqueda');
                                    }
                                });
                            }
                        });
                    }
                });
            }

            if (!empty($editorialFiltro) && count($editorialFiltro) > 0) {
                $query->where(function($q) use ($editorialFiltro) {
                    foreach ($editorialFiltro as $editorial) {
                        $q->orWhere(function($subQ) use ($editorial) {
                            // Buscar la cadena completa primero (ej: "grupo editorial norma")
                            $subQ->where('ve.nombre_busqueda', 'LIKE', "%{$editorial}%")
                                 ->whereNotNull('ve.nombre_busqueda');
                            
                            // También buscar coincidencia exacta después de trim para mayor precisión
                            $subQ->orWhere(function($exactQ) use ($editorial) {
                                $exactQ->where('ve.nombre_busqueda', '=', trim($editorial))
                                       ->orWhereRaw('TRIM(ve.nombre_busqueda) = ?', [trim($editorial)])
                                       ->whereNotNull('ve.nombre_busqueda');
                            });
                            
                            // Si tiene 2+ palabras, buscar que contenga TODAS las palabras significativas
                            $palabras = $this->extraerPalabrasSignificativas($editorial);
                            
                            if (count($palabras) >= 2) {
                                // Usar AND para asegurar que TODAS las palabras estén presentes
                                $subQ->orWhere(function($andQ) use ($palabras) {
                                    foreach ($palabras as $palabra) {
                                        $andQ->where('ve.nombre_busqueda', 'LIKE', "%{$palabra}%")
                                             ->whereNotNull('ve.nombre_busqueda');
                                    }
                                });
                            }
                        });
                    }
                });
            }

            if (!empty($materiaFiltro) && count($materiaFiltro) > 0) {
                $query->where(function($q) use ($materiaFiltro) {
                    foreach ($materiaFiltro as $materia) {
                        $q->orWhere(function($subQ) use ($materia) {
                            // Buscar la cadena completa primero (ej: "ciencias sociales")
                            $subQ->where('vm.nombre_busqueda', 'LIKE', "%{$materia}%")
                                 ->whereNotNull('vm.nombre_busqueda');
                            
                            // También buscar coincidencia exacta después de trim para mayor precisión
                            $subQ->orWhere(function($exactQ) use ($materia) {
                                $exactQ->where('vm.nombre_busqueda', '=', trim($materia))
                                       ->orWhereRaw('TRIM(vm.nombre_busqueda) = ?', [trim($materia)])
                                       ->whereNotNull('vm.nombre_busqueda');
                            });
                            
                            // Si tiene 2+ palabras, buscar que contenga TODAS las palabras significativas
                            $palabras = $this->extraerPalabrasSignificativas($materia);
                            
                            if (count($palabras) >= 2) {
                                // Usar AND para asegurar que TODAS las palabras estén presentes
                                $subQ->orWhere(function($andQ) use ($palabras) {
                                    foreach ($palabras as $palabra) {
                                        $andQ->where('vm.nombre_busqueda', 'LIKE', "%{$palabra}%")
                                             ->whereNotNull('vm.nombre_busqueda');
                                    }
                                });
                            }
                        });
                    }
                });
            }

            if (!empty($serieFiltro) && count($serieFiltro) > 0) {
                $query->where(function($q) use ($serieFiltro) {
                    foreach ($serieFiltro as $serie) {
                        $q->orWhere(function($subQ) use ($serie) {
                            // Buscar la cadena completa primero (ej: "series de matemáticas")
                            $subQ->where('vs.nombre_busqueda', 'LIKE', "%{$serie}%")
                                 ->whereNotNull('vs.nombre_busqueda');
                            
                            // También buscar coincidencia exacta después de trim para mayor precisión
                            $subQ->orWhere(function($exactQ) use ($serie) {
                                $exactQ->where('vs.nombre_busqueda', '=', trim($serie))
                                       ->orWhereRaw('TRIM(vs.nombre_busqueda) = ?', [trim($serie)])
                                       ->whereNotNull('vs.nombre_busqueda');
                            });
                            
                            // Si tiene 2+ palabras, buscar que contenga TODAS las palabras significativas
                            $palabras = $this->extraerPalabrasSignificativas($serie);
                            
                            if (count($palabras) >= 2) {
                                // Usar AND para asegurar que TODAS las palabras estén presentes
                                $subQ->orWhere(function($andQ) use ($palabras) {
                                    foreach ($palabras as $palabra) {
                                        $andQ->where('vs.nombre_busqueda', 'LIKE', "%{$palabra}%")
                                             ->whereNotNull('vs.nombre_busqueda');
                                    }
                                });
                            }
                        });
                    }
                });
            }

            if (!empty($campusFiltro) && count($campusFiltro) > 0) {
                $query->where(function($q) use ($campusFiltro) {
                    foreach ($campusFiltro as $campus) {
                        $q->orWhere(function($subQ) use ($campus) {
                            $subQ->where('tc.nombre_tb_campus', '=', trim($campus))
                                 ->orWhereRaw('TRIM(tc.nombre_tb_campus) = ?', [trim($campus)])
                                 ->whereNotNull('tc.nombre_tb_campus');
                        });
                    }
                });
            }

            if (!empty($tipoMaterialFiltro) && count($tipoMaterialFiltro) > 0) {
                $query->where(function($q) use ($tipoMaterialFiltro) {
                    foreach ($tipoMaterialFiltro as $descripcion) {
                        $codigosArray = $this->obtenerCodigoTipoMaterial($descripcion);
                        
                        if ($codigosArray && is_array($codigosArray)) {
                            foreach ($codigosArray as $codigo) {
                                $q->orWhere(function($subQ) use ($codigo) {
                                    if ($codigo === null || $codigo === '' || $codigo === 'NULL') {
                                        // Para "No especificado", buscar valores null, vacíos o 'NULL'
                                        $subQ->where(function($nullQ) {
                                            $nullQ->whereNull('dm.DSM_TIPO_MATERIAL')
                                                  ->orWhere('dm.DSM_TIPO_MATERIAL', '')
                                                  ->orWhere('dm.DSM_TIPO_MATERIAL', 'NULL');
                                        });
                                    } else {
                                        // Para códigos específicos
                                        $subQ->where('dm.DSM_TIPO_MATERIAL', '=', trim($codigo))
                                             ->whereNotNull('dm.DSM_TIPO_MATERIAL');
                                    }
                                });
                            }
                        }
                    }
                });
            }

            // Para búsquedas completamente generales (sin filtros ni criterios específicos),
            // aplicar filtros básicos para evitar registros con campos principales vacíos
            if (empty($valorCriterio) && empty($titulo) && empty($autorFiltro) && empty($editorialFiltro) && empty($materiaFiltro) && empty($serieFiltro) && empty($campusFiltro) && empty($tipoMaterialFiltro)) {
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

            $query->orderBy('relevancia', 'desc')
                  ->orderBy($orderByField, $orden);

            // Ejecutar consulta principal
            $allResults = $query->limit(10000)->get();
            
            // SOLUCIÓN AMPLIADA: Agregar materiales de DETALLE_MATERIAL que no aparecieron
            // porque sus títulos no coinciden exactamente con V_TITULO (incluye todos los tipos, no solo seriadas)
            $materialesAdicionales = DB::table('DETALLE_MATERIAL as dm')
                ->select(
                    DB::raw("'DM_' + CAST(ROW_NUMBER() OVER (ORDER BY dm.DSM_TITULO) AS VARCHAR) as nro_control"),
                    'dm.DSM_TITULO as titulo',
                    'dm.DSM_AUTOR_EDITOR as autor',
                    'dm.DSM_EDITORIAL as editorial',
                    DB::raw('NULL as materia'),
                    DB::raw('NULL as serie'),
                    DB::raw('NULL as dewey'),
                    DB::raw('NULL as biblioteca'),
                    'dm.DSM_TIPO_MATERIAL as tipo_material',
                    DB::raw('0 as relevancia')
                );
                
            // Aplicar los mismos filtros que la consulta principal
            if (!empty($titulo)) {
                $materialesAdicionales->where('dm.DSM_TITULO', 'LIKE', "%{$titulo}%");
            }
            
            if (!empty($valorCriterio)) {
                switch ($criterio) {
                    case 'autor':
                        $materialesAdicionales->where(function($q) use ($valorCriterio) {
                            // Buscar la cadena completa primero
                            $q->where('dm.DSM_AUTOR_EDITOR', 'LIKE', "%{$valorCriterio}%");
                            
                            // Si tiene 2+ palabras, buscar que contenga TODAS las palabras significativas
                            $palabras = $this->extraerPalabrasSignificativas($valorCriterio);
                            
                            if (count($palabras) >= 2) {
                                $q->orWhere(function($andQ) use ($palabras) {
                                    foreach ($palabras as $palabra) {
                                        $andQ->where('dm.DSM_AUTOR_EDITOR', 'LIKE', "%{$palabra}%");
                                    }
                                });
                            }
                        });
                        break;
                    case 'editorial':
                        $materialesAdicionales->where(function($q) use ($valorCriterio) {
                            // Buscar la cadena completa primero
                            $q->where('dm.DSM_EDITORIAL', 'LIKE', "%{$valorCriterio}%");
                            
                            // Si tiene 2+ palabras, buscar que contenga TODAS las palabras significativas
                            $palabras = $this->extraerPalabrasSignificativas($valorCriterio);
                            
                            if (count($palabras) >= 2) {
                                $q->orWhere(function($andQ) use ($palabras) {
                                    foreach ($palabras as $palabra) {
                                        $andQ->where('dm.DSM_EDITORIAL', 'LIKE', "%{$palabra}%");
                                    }
                                });
                            }
                        });
                        break;
                }
            }
            
            if (!empty($autorFiltro) && count($autorFiltro) > 0) {
                $materialesAdicionales->where(function($q) use ($autorFiltro) {
                    foreach ($autorFiltro as $autor) {
                        $q->orWhere(function($subQ) use ($autor) {
                            // Buscar la cadena completa primero
                            $subQ->where('dm.DSM_AUTOR_EDITOR', 'LIKE', "%{$autor}%");
                            
                            // Si tiene 2+ palabras, buscar que contenga TODAS las palabras significativas
                            $palabras = $this->extraerPalabrasSignificativas($autor);
                            
                            if (count($palabras) >= 2) {
                                $subQ->orWhere(function($andQ) use ($palabras) {
                                    foreach ($palabras as $palabra) {
                                        $andQ->where('dm.DSM_AUTOR_EDITOR', 'LIKE', "%{$palabra}%");
                                    }
                                });
                            }
                        });
                    }
                });
            }
            
            if (!empty($editorialFiltro) && count($editorialFiltro) > 0) {
                $materialesAdicionales->where(function($q) use ($editorialFiltro) {
                    foreach ($editorialFiltro as $editorial) {
                        $q->orWhere(function($subQ) use ($editorial) {
                            // Buscar la cadena completa primero
                            $subQ->where('dm.DSM_EDITORIAL', 'LIKE', "%{$editorial}%");
                            
                            // Si tiene 2+ palabras, buscar que contenga TODAS las palabras significativas
                            $palabras = $this->extraerPalabrasSignificativas($editorial);
                            
                            if (count($palabras) >= 2) {
                                $subQ->orWhere(function($andQ) use ($palabras) {
                                    foreach ($palabras as $palabra) {
                                        $andQ->where('dm.DSM_EDITORIAL', 'LIKE', "%{$palabra}%");
                                    }
                                });
                            }
                        });
                    }
                });
            }
            
            // Filtrar por tipo de material si es necesario
            if (!empty($tipoMaterialFiltro) && count($tipoMaterialFiltro) > 0) {
                $materialesAdicionales->where(function($q) use ($tipoMaterialFiltro) {
                    foreach ($tipoMaterialFiltro as $descripcion) {
                        $codigosArray = $this->obtenerCodigoTipoMaterial($descripcion);
                        
                        if ($codigosArray && is_array($codigosArray)) {
                            foreach ($codigosArray as $codigo) {
                                if ($codigo === null || $codigo === '' || $codigo === 'NULL') {
                                    // Para "No especificado", buscar valores null, vacíos o 'NULL'
                                    $q->orWhere(function($subQ) {
                                        $subQ->whereNull('dm.DSM_TIPO_MATERIAL')
                                             ->orWhere('dm.DSM_TIPO_MATERIAL', '')
                                             ->orWhere('dm.DSM_TIPO_MATERIAL', 'NULL');
                                    });
                                } else {
                                    // Para códigos específicos
                                    $q->orWhere('dm.DSM_TIPO_MATERIAL', '=', trim($codigo));
                                }
                            }
                        }
                    }
                });
            }
            
            $resultadosMateriales = $materialesAdicionales->limit(5000)->get();
            
            // Filtrar duplicados comparando por título y autor para evitar repetir materiales que ya están en allResults
            $titulosExistentes = $allResults->map(function($item) {
                return strtolower(trim($item->titulo ?? '')) . '|' . strtolower(trim($item->autor ?? ''));
            })->toArray();
            
            $materialesNuevos = $resultadosMateriales->filter(function($item) use ($titulosExistentes) {
                $clave = strtolower(trim($item->titulo ?? '')) . '|' . strtolower(trim($item->autor ?? ''));
                return !in_array($clave, $titulosExistentes);
            });
            
            // Combinar resultados
            $allResults = $allResults->merge($materialesNuevos);
            
            // Si no hay suficientes resultados y tenemos criterios de búsqueda, hacer búsqueda ampliada
            if ($allResults->count() < 10 && (!empty($titulo) || !empty($valorCriterio))) {
                $resultadosAmpliados = $this->busquedaAmpliada($titulo, $valorCriterio, $criterio);
                
                // Filtrar duplicados comparando por nro_control
                $controlesExistentes = $allResults->pluck('nro_control')->toArray();
                $resultadosNuevos = $resultadosAmpliados->filter(function($item) use ($controlesExistentes) {
                    return !in_array($item->nro_control, $controlesExistentes);
                });
                
                // Combinar resultados
                $allResults = $allResults->merge($resultadosNuevos);
            }
            
            // Procesar los resultados para mapear tipos de material
            $allResults = $allResults->map(function ($item) {
                // Mapear el tipo de material a descripción legible
                if (isset($item->tipo_material)) {
                    $item->tipo_material_descripcion = $this->mapearTipoMaterial($item->tipo_material);
                } else {
                    $item->tipo_material_descripcion = 'No especificado';
                }
                
                return $item;
            });

            // Construir filtros AL FINAL, después de toda la lógica de resultados
            $filtros_timestamp = session($filtros_cache_key . '_timestamp', 0);
            $filtros_ttl = 1800; // 30 min
            $filtros_expired = (time() - $filtros_timestamp) > $filtros_ttl;

            // SIEMPRE recalcular filtros para asegurar que reflejen los resultados actuales
            // Recalcular filtros usando los datos ya mapeados FINALES
            $autores = $allResults->pluck('autor')->filter()->unique()->sort()->values();
            $editoriales = $allResults->pluck('editorial')->filter()->unique()->sort()->values();
            $materias = $allResults->pluck('materia')->filter()->unique()->sort()->values();
            $series = $allResults->pluck('serie')->filter()->unique()->sort()->values();
            $campuses = $allResults->pluck('biblioteca')->filter()->unique()->sort()->values();
            
            // Guardar filtros en cache separado
            session([
                $filtros_cache_key => compact('autores', 'editoriales', 'materias', 'series', 'campuses'),
                $filtros_cache_key . '_timestamp' => time()
            ]);

            // Almacenar en sesión tras nueva búsqueda con metadatos mejorados
            session([
                'busqueda' => $allResults->all(),
                'tipo_busqueda' => $criterio,
                'texto_busqueda' => $texto_procesado,
                'nav_pagina' => $pagina,
                'busq_numrows' => $allResults->count(),
                'ind_busqueda' => 'avanzada',
                'cache_timestamp' => time(),
                'cache_version' => '1.0',
                'query_execution_time' => microtime(true) - (microtime(true) - 0.1) // Placeholder para tiempo real
            ]);
        } else {
            // Recuperar datos desde sesión y convertir arrays a objetos
            $sessionData = session('busqueda', []);
            $allResults = collect($sessionData)->map(function ($item) {
                return is_array($item) ? (object) $item : $item;
            });
            session(['nav_pagina' => $pagina]);
        }

        // Los filtros ya fueron construidos después del mapeo de tipos de material en el bloque anterior
        // Usar la misma clave de cache que se definió arriba
        $filtros_data = session($filtros_cache_key, []);
        if (!empty($filtros_data)) {
            extract($filtros_data);
        } else {
            // Fallback si por alguna razón no hay filtros en cache
            $autores = collect();
            $editoriales = collect();
            $materias = collect();
            $series = collect();
            $campuses = collect();
            $tiposMaterial = collect();
        }

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
            'materias',
            'series',
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
            // Ejecutar la consulta principal y almacenar en sesión
            $query = DetalleMaterial::query()
                ->select('DSM_TITULO', 'DSM_TIPO_MATERIAL', 'DSM_AUTOR_EDITOR', 'DSM_EDITORIAL')
                ->where('DSM_AUTOR_EDITOR', '=', urldecode($autor));

            if ($titulo) {
                $query->where('DSM_TITULO', 'LIKE', '%' . $titulo . '%');
            }

            $titulos = $query->get();
            
            // Mapear tipos de material para mostrar descripciones legibles
            $titulos = $titulos->map(function ($item) {
                $item->tipo_material_descripcion = $this->mapearTipoMaterial($item->DSM_TIPO_MATERIAL);
                return $item;
            });

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
            // Ejecutar la consulta principal y almacenar en sesión
            $query = DetalleMaterial::query()
                ->select('DSM_TITULO', 'DSM_TIPO_MATERIAL', 'DSM_AUTOR_EDITOR', 'DSM_EDITORIAL')
                ->where('DSM_EDITORIAL', '=', urldecode($editorial));

            if ($titulo) {
                $query->where('DSM_TITULO', 'LIKE', '%' . $titulo . '%');
            }

            $titulos = $query->get();
            
            // Mapear tipos de material para mostrar descripciones legibles
            $titulos = $titulos->map(function ($item) {
                $item->tipo_material_descripcion = $this->mapearTipoMaterial($item->DSM_TIPO_MATERIAL);
                return $item;
            });

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
            // Ejecutar la consulta principal con JOIN a V_MATERIA
            $query = DB::table('DETALLE_MATERIAL')
                ->join('V_MATERIA', 'DETALLE_MATERIAL.som_numero', '=', 'V_MATERIA.nro_control')
                ->select('DETALLE_MATERIAL.DSM_TITULO', 'DETALLE_MATERIAL.DSM_TIPO_MATERIAL', 
                        'DETALLE_MATERIAL.DSM_AUTOR_EDITOR', 'DETALLE_MATERIAL.DSM_EDITORIAL')
                ->where('V_MATERIA.nombre_busqueda', '=', urldecode($materia));

            if ($titulo) {
                $query->where('DETALLE_MATERIAL.DSM_TITULO', 'LIKE', '%' . $titulo . '%');
            }

            $titulos = $query->get();
            
            // Buscar también publicaciones seriadas que puedan estar relacionadas por título o contenido
            $publicacionesSeriadas = DB::table('DETALLE_MATERIAL')
                ->select('DSM_TITULO', 'DSM_TIPO_MATERIAL', 'DSM_AUTOR_EDITOR', 'DSM_EDITORIAL')
                ->where('DSM_TIPO_MATERIAL', 'S');
                
            if ($titulo) {
                $publicacionesSeriadas->where('DSM_TITULO', 'LIKE', '%' . $titulo . '%');
            }
            
            // Filtrar por materia en el título o contenido de las publicaciones seriadas
            $publicacionesSeriadas->where(function($q) use ($materia) {
                $materiaDecodificada = urldecode($materia);
                $q->where('DSM_TITULO', 'LIKE', "%{$materiaDecodificada}%")
                  ->orWhere('DSM_AUTOR_EDITOR', 'LIKE', "%{$materiaDecodificada}%");
            });
            
            $resultadosPublicaciones = $publicacionesSeriadas->limit(500)->get();
            
            // Combinar resultados evitando duplicados
            $titulosExistentes = $titulos->pluck('DSM_TITULO')->map('strtolower')->toArray();
            $publicacionesNuevas = $resultadosPublicaciones->filter(function($item) use ($titulosExistentes) {
                return !in_array(strtolower($item->DSM_TITULO), $titulosExistentes);
            });
            
            $titulos = $titulos->merge($publicacionesNuevas);
            
            // Mapear tipos de material para mostrar descripciones legibles
            $titulos = $titulos->map(function ($item) {
                $item->tipo_material_descripcion = $this->mapearTipoMaterial($item->DSM_TIPO_MATERIAL);
                return $item;
            });

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
            // Ejecutar la consulta principal con JOIN a V_SERIE
            $query = DB::table('DETALLE_MATERIAL')
                ->join('V_SERIE', 'DETALLE_MATERIAL.som_numero', '=', 'V_SERIE.nro_control')
                ->select('DETALLE_MATERIAL.DSM_TITULO', 'DETALLE_MATERIAL.DSM_TIPO_MATERIAL',
                        'DETALLE_MATERIAL.DSM_AUTOR_EDITOR', 'DETALLE_MATERIAL.DSM_EDITORIAL')
                ->where('V_SERIE.nombre_busqueda', '=', urldecode($serie));

            if ($titulo) {
                $query->where('DETALLE_MATERIAL.DSM_TITULO', 'LIKE', '%' . $titulo . '%');
            }

            $titulos = $query->get();
            
            // Buscar también publicaciones seriadas que puedan estar relacionadas por título o contenido
            $publicacionesSeriadas = DB::table('DETALLE_MATERIAL')
                ->select('DSM_TITULO', 'DSM_TIPO_MATERIAL', 'DSM_AUTOR_EDITOR', 'DSM_EDITORIAL')
                ->where('DSM_TIPO_MATERIAL', 'S');
                
            if ($titulo) {
                $publicacionesSeriadas->where('DSM_TITULO', 'LIKE', '%' . $titulo . '%');
            }
            
            // Filtrar por serie en el título o contenido de las publicaciones seriadas
            $publicacionesSeriadas->where(function($q) use ($serie) {
                $serieDecodificada = urldecode($serie);
                $q->where('DSM_TITULO', 'LIKE', "%{$serieDecodificada}%")
                  ->orWhere('DSM_AUTOR_EDITOR', 'LIKE', "%{$serieDecodificada}%");
            });
            
            $resultadosPublicaciones = $publicacionesSeriadas->limit(500)->get();
            
            // Combinar resultados evitando duplicados
            $titulosExistentes = $titulos->pluck('DSM_TITULO')->map('strtolower')->toArray();
            $publicacionesNuevas = $resultadosPublicaciones->filter(function($item) use ($titulosExistentes) {
                return !in_array(strtolower($item->DSM_TITULO), $titulosExistentes);
            });
            
            $titulos = $titulos->merge($publicacionesNuevas);
            
            // Mapear tipos de material para mostrar descripciones legibles
            $titulos = $titulos->map(function ($item) {
                $item->tipo_material_descripcion = $this->mapearTipoMaterial($item->DSM_TIPO_MATERIAL);
                return $item;
            });

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

    //Limpiar el caché de sesión de forma inteligente
    
    public function limpiarCacheInteligente($tipo = 'all', $forzar = false)
    {
        $cache_ttl = config('app.cache_ttl', 1800);
        $current_time = time();
        
        $session_keys = [
            'principal' => ['busqueda', 'tipo_busqueda', 'texto_busqueda', 'nav_pagina', 'busq_numrows', 'ind_busqueda', 'cache_timestamp', 'cache_version'],
            'autor' => ['busqueda_autor', 'texto_busqueda_autor', 'busq_numrows_autor'],
            'editorial' => ['busqueda_editorial', 'texto_busqueda_editorial', 'busq_numrows_editorial'],
            'materia' => ['busqueda_materia', 'texto_busqueda_materia', 'busq_numrows_materia'],
            'serie' => ['busqueda_serie', 'texto_busqueda_serie', 'busq_numrows_serie']
        ];
        
        $cleaned_keys = [];
        
        if ($tipo === 'all' || $forzar) {
            // Limpiar todo
            foreach ($session_keys as $categoria => $keys) {
                foreach ($keys as $key) {
                    if (session()->has($key)) {
                        session()->forget($key);
                        $cleaned_keys[] = $key;
                    }
                }
            }
            
            // Limpiar también filtros cacheados
            $this->limpiarFiltrosCache();
            
        } else {
            // Limpiar solo caché expirado
            $timestamp_key = $tipo === 'principal' ? 'cache_timestamp' : 'cache_timestamp_' . $tipo;
            $timestamp = session($timestamp_key, 0);
            
            if (($current_time - $timestamp) > $cache_ttl) {
                $keys_to_clean = $session_keys[$tipo] ?? [];
                foreach ($keys_to_clean as $key) {
                    if (session()->has($key)) {
                        session()->forget($key);
                        $cleaned_keys[] = $key;
                    }
                }
            }
        }
        
        return response()->json([
            'success' => true, 
            'message' => 'Cache inteligente limpiado exitosamente',
            'cleaned_keys' => $cleaned_keys,
            'timestamp' => $current_time
        ]);
    }
    
    //Limpiar filtros cacheados
    private function limpiarFiltrosCache()
    {
        $session_data = session()->all();
        foreach ($session_data as $key => $value) {
            if (strpos($key, 'filtros_') === 0) {
                session()->forget($key);
            }
        }
    }

    //Limpiar el caché de sesión para búsquedas avanzadas (método legacy)
    
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

    //Obtener estadísticas avanzadas del caché
    
    public function obtenerEstadisticasAvanzadas()
    {
        $current_time = time();
        $cache_ttl = config('app.cache_ttl', 1800);
        
        $stats = [
            'sistema' => [
                'cache_ttl_configurado' => $cache_ttl,
                'timestamp_actual' => $current_time,
                'fecha_actual' => date('Y-m-d H:i:s', $current_time)
            ],
            'busqueda_principal' => [
                'existe' => session()->has('busqueda'),
                'registros' => session('busq_numrows', 0),
                'tipo' => session('tipo_busqueda', 'N/A'),
                'pagina_actual' => session('nav_pagina', 1),
                'timestamp' => session('cache_timestamp', 0),
                'edad_cache' => $current_time - session('cache_timestamp', 0),
                'expirado' => ($current_time - session('cache_timestamp', 0)) > $cache_ttl,
                'version' => session('cache_version', 'legacy'),
                'tiempo_ejecucion' => session('query_execution_time', 0)
            ],
            'cache_filtros' => $this->obtenerEstadisticasFiltros(),
            'uso_memoria' => [
                'session_size' => strlen(serialize(session()->all())),
                'memory_usage' => memory_get_usage(true),
                'memory_peak' => memory_get_peak_usage(true)
            ]
        ];
        
        // Agregar estadísticas de subcaches
        $subcaches = ['autor', 'editorial', 'materia', 'serie'];
        foreach ($subcaches as $tipo) {
            $stats['busqueda_' . $tipo] = [
                'existe' => session()->has('busqueda_' . $tipo),
                'registros' => session('busq_numrows_' . $tipo, 0),
                'timestamp' => session('cache_timestamp_' . $tipo, 0),
                'edad_cache' => $current_time - session('cache_timestamp_' . $tipo, 0),
                'expirado' => ($current_time - session('cache_timestamp_' . $tipo, 0)) > $cache_ttl
            ];
        }
        
        return response()->json($stats);
    }
    
    //Obtener estadísticas de filtros cacheados
    private function obtenerEstadisticasFiltros()
    {
        $session_data = session()->all();
        $filtros_stats = [];
        
        foreach ($session_data as $key => $value) {
            if (strpos($key, 'filtros_') === 0 && !strpos($key, '_timestamp')) {
                $timestamp_key = $key . '_timestamp';
                $timestamp = session($timestamp_key, 0);
                $edad = time() - $timestamp;
                
                $filtros_stats[$key] = [
                    'existe' => true,
                    'timestamp' => $timestamp,
                    'edad_cache' => $edad,
                    'size' => is_array($value) ? count($value) : 0
                ];
            }
        }
        
        return $filtros_stats;
    }

    //Obtener estadísticas del caché de sesión (método legacy)
    
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
            // Aumentar el tiempo máximo de ejecución a 300 segundos (5 minutos)
            set_time_limit(300);
            ini_set('max_execution_time', 300);
            
            // Configurar timeout para consultas SQL (en segundos)
            DB::statement('SET SESSION wait_timeout = 900');
            DB::statement('SET SESSION interactive_timeout = 900');
            DB::statement('SET SESSION net_read_timeout = 300');
            DB::statement('SET SESSION net_write_timeout = 300');
            
            // Aumentar el límite de memoria para consultas grandes
            ini_set('memory_limit', '1024M');
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
                return !empty(trim($valor)) && $valor !== null && $valor !== '';
            });
            // Limpiar espacios y normalizar, eliminar duplicados
            $limpio = array_values(array_unique(array_map(function($valor) {
                return trim($valor);
            }, $resultado)));
            
            return $limpio;
        }

        if (is_string($filtro)) {
            // Si es un string, dividir por comas y filtrar valores vacíos
            $valores = explode(',', $filtro);
            $resultado = array_filter(array_map('trim', $valores), function($valor) {
                return !empty($valor) && $valor !== null && $valor !== '';
            });
            // Eliminar duplicados
            return array_values(array_unique($resultado));
        }

        return [];
    }

    /**
     * Mapea los códigos de tipo de material a sus descripciones legibles
     * @param string $codigo El código del tipo de material
     * @return string La descripción del tipo de material
     */
    private function mapearTipoMaterial($codigo)
    {
        $tipos = [
            'M' => 'Monografías/Libros',
            'S' => 'Publicaciones Seriadas',
        ];

        return $tipos[$codigo] ?? ($codigo ?? 'No especificado');
    }

    /**
     * Obtiene el código de tipo de material a partir de su descripción
     * @param string $descripcion La descripción del tipo de material
     * @return array|null Los códigos del tipo de material
     */
    private function obtenerCodigoTipoMaterial($descripcion)
    {
        $tipos = [
            'Monografías/Libros' => ['M'],
            'Publicaciones Seriadas' => ['S'],
            'No especificado' => [null, '', 'NULL'] // Para manejar valores vacíos, null o string 'NULL'
        ];

        return $tipos[$descripcion] ?? null;
    }

    /**
     * Filtra palabras para obtener solo las significativas para búsqueda
     * @param string $termino El término completo a procesar
     * @return array Array de palabras significativas
     */
    private function extraerPalabrasSignificativas($termino)
    {
        return array_filter(explode(' ', trim($termino)), function($palabra) {
            $palabra = trim($palabra);
            // Filtrar palabras muy cortas, números (años), caracteres especiales y palabras comunes
            return strlen($palabra) >= 2 && 
                   !preg_match('/^\d+$/', $palabra) && // No solo números
                   !preg_match('/^\d{4}-\d{4}$/', $palabra) && // No rangos de años (ej: 1904-1973)
                   !preg_match('/^\d{4}$/', $palabra) && // No años individuales
                   !preg_match('/^[^\w]+$/', $palabra) && // No solo caracteres especiales
                   preg_match('/^[a-záéíóúñüç]/i', $palabra) && // Debe empezar con letra
                   !in_array(strtolower($palabra), ['de', 'del', 'la', 'el', 'y', 'e', 'o', 'u', 'a', 'en', 'con', 'por', 'para']); // No palabras comunes
        });
    }

    /**
     * Procesa términos de búsqueda para manejar cadenas con múltiples palabras
     * @param string $termino El término de búsqueda
     * @param string $tipo El tipo de término (autor, editorial, materia, serie)
     * @return array Array con información sobre cómo buscar el término
     */
    private function procesarTerminoBusqueda($termino, $tipo = 'general')
    {
        if (empty($termino)) {
            return ['tipo' => 'vacio', 'buscar_completo' => false, 'palabras' => []];
        }

        $terminoLimpio = trim($termino);
        $palabras = $this->extraerPalabrasSignificativas($terminoLimpio);

        $resultado = [
            'termino_original' => $terminoLimpio,
            'palabras' => array_map('trim', $palabras),
            'es_multiple' => count($palabras) >= 2,
            'buscar_completo' => true,
            'tipo_campo' => $tipo,
            'tipo_busqueda' => count($palabras) >= 2 ? 'multiple' : 'simple'
        ];

        return $resultado;
    }

    /**
     * Procesa términos de búsqueda de autor para manejar cadenas con múltiples palabras
     * @param string $termino El término de búsqueda del autor
     * @return array Array con información sobre cómo buscar el término
     * @deprecated Usar procesarTerminoBusqueda() en su lugar
     */
    private function procesarTerminoAutor($termino)
    {
        return $this->procesarTerminoBusqueda($termino, 'autor');
    }

    /**
     * Normaliza y limpia términos de búsqueda
     * @param string $termino El término a normalizar
     * @return string El término normalizado
     */
    private function normalizarTermino($termino)
    {
        if (empty($termino)) return '';
        
        // Convertir a minúsculas y eliminar espacios extra
        $termino = trim(strtolower($termino));
        
        // Remover acentos y caracteres especiales
        $acentos = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'ñ' => 'n', 'ü' => 'u',
            'à' => 'a', 'è' => 'e', 'ì' => 'i', 'ò' => 'o', 'ù' => 'u'
        ];
        
        $termino = strtr($termino, $acentos);
        
        // Remover caracteres especiales excepto espacios y guiones
        $termino = preg_replace('/[^a-z0-9\s\-]/', '', $termino);
        
        // Limpiar espacios múltiples
        $termino = preg_replace('/\s+/', ' ', $termino);
        
        return trim($termino);
    }

    /**
     * Realiza una búsqueda ampliada cuando la búsqueda principal no encuentra suficientes resultados
     * @param string $titulo El título a buscar
     * @param string $valorCriterio El valor del criterio adicional
     * @param string $criterio El tipo de criterio (autor, editorial, etc.)
     * @return Collection Resultados adicionales encontrados
     */
    private function busquedaAmpliada($titulo, $valorCriterio, $criterio)
    {
        // Normalizar términos de búsqueda
        $tituloNormalizado = $this->normalizarTermino($titulo);
        $criterioNormalizado = $this->normalizarTermino($valorCriterio);

        // Crear una consulta más permisiva
        $query = DB::table('V_TITULO as vt')
            ->leftJoin('V_AUTOR as va', 'vt.nro_control', '=', 'va.nro_control')
            ->leftJoin('V_EDITORIAL as ve', 'vt.nro_control', '=', 've.nro_control')
            ->leftJoin('V_MATERIA as vm', 'vt.nro_control', '=', 'vm.nro_control')
            ->leftJoin('V_SERIE as vs', 'vt.nro_control', '=', 'vs.nro_control')
            ->leftJoin('V_DEWEY as vd', 'vt.nro_control', '=', 'vd.nro_control')
            ->leftJoin('DETALLE_MATERIAL as dm', 'vt.nombre_busqueda', '=', 'dm.DSM_TITULO')
            ->leftJoin('EXISTENCIA as e', 'vt.nro_control', '=', 'e.nro_control')
            ->leftJoin('TB_CAMPUS as tc', 'e.campus_tb_campus', '=', 'tc.campus_tb_campus');

        // Campos de selección
        $selectFields = [
            'vt.nro_control',
            'vt.nombre_busqueda as titulo',
            'va.nombre_busqueda as autor',
            've.nombre_busqueda as editorial',
            'vm.nombre_busqueda as materia',
            'vs.nombre_busqueda as serie',
            'vd.nombre_busqueda as dewey',
            'tc.nombre_tb_campus as biblioteca',
            'dm.DSM_TIPO_MATERIAL as tipo_material',
            DB::raw("1 as relevancia") // Relevancia baja para resultados ampliados
        ];

        $query->select($selectFields);

        // Búsqueda muy amplia - búsqueda por palabras individuales
        if (!empty($tituloNormalizado)) {
            $palabras = array_filter(explode(' ', $tituloNormalizado), function($palabra) {
                return strlen(trim($palabra)) > 2; // Solo palabras de más de 2 caracteres
            });
            
            if (!empty($palabras)) {
                $query->where(function($q) use ($palabras, $titulo) {
                    // Primero buscar el término completo
                    $q->where('vt.nombre_busqueda', 'LIKE', "%{$titulo}%");
                    
                    // Luego buscar por palabras individuales
                    foreach ($palabras as $palabra) {
                        $q->orWhere('vt.nombre_busqueda', 'LIKE', "%{$palabra}%")
                          ->orWhere('va.nombre_busqueda', 'LIKE', "%{$palabra}%")
                          ->orWhere('ve.nombre_busqueda', 'LIKE', "%{$palabra}%")
                          ->orWhere('vm.nombre_busqueda', 'LIKE', "%{$palabra}%")
                          ->orWhere('vs.nombre_busqueda', 'LIKE', "%{$palabra}%");
                    }
                });
            }
        }

        // Aplicar filtro por criterio si se especifica
        if (!empty($criterioNormalizado)) {
            $palabrasCriterio = array_filter(explode(' ', $criterioNormalizado), function($palabra) {
                return strlen(trim($palabra)) > 2;
            });
            
            if (!empty($palabrasCriterio)) {
                $query->where(function($q) use ($palabrasCriterio, $criterio, $valorCriterio) {
                    // Primero buscar el término completo
                    switch ($criterio) {
                        case 'autor':
                            $q->where('va.nombre_busqueda', 'LIKE', "%{$valorCriterio}%");
                            break;
                        case 'editorial':
                            $q->where('ve.nombre_busqueda', 'LIKE', "%{$valorCriterio}%");
                            break;
                        case 'materia':
                            $q->where('vm.nombre_busqueda', 'LIKE', "%{$valorCriterio}%");
                            break;
                        case 'serie':
                            $q->where('vs.nombre_busqueda', 'LIKE', "%{$valorCriterio}%");
                            break;
                    }
                    
                    // Luego buscar por palabras individuales
                    foreach ($palabrasCriterio as $palabra) {
                        switch ($criterio) {
                            case 'autor':
                                $q->orWhere('va.nombre_busqueda', 'LIKE', "%{$palabra}%");
                                break;
                            case 'editorial':
                                $q->orWhere('ve.nombre_busqueda', 'LIKE', "%{$palabra}%");
                                break;
                            case 'materia':
                                $q->orWhere('vm.nombre_busqueda', 'LIKE', "%{$palabra}%");
                                break;
                            case 'serie':
                                $q->orWhere('vs.nombre_busqueda', 'LIKE', "%{$palabra}%");
                                break;
                        }
                    }
                });
            }
        }

        $query->distinct()
              ->orderBy('vt.nombre_busqueda', 'asc')
              ->limit(3000); // Límite mayor para búsqueda ampliada

        $resultados = $query->get();
        
        return $resultados;
    }
}