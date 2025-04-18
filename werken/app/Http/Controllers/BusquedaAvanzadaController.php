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
        $criterio = $request->input('criterio');
        $valorCriterio = $request->input('valor_criterio');
        $titulo = $request->input('titulo');
        $orden = $request->input('orden', 'asc');

        $autorFiltro = $request->input('autor', []);
        $editorialFiltro = $request->input('editorial', []);
        $campusFiltro = $request->input('campus', []);

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

        switch ($criterio) {
            case 'autor':
                if ($valorCriterio) {
                    $query->where('va.nombre_busqueda', 'LIKE', "%{$valorCriterio}%")
                          ->orderBy('va.nombre_busqueda', $orden);
                }
                break;
            case 'editorial':
                if ($valorCriterio) {
                    $query->where('ve.nombre_busqueda', 'LIKE', "%{$valorCriterio}%")
                          ->orderBy('ve.nombre_busqueda', $orden);
                }
                break;
            case 'materia':
                if ($valorCriterio) {
                    $query->join('V_MATERIA', 'vt.nro_control', '=', 'V_MATERIA.nro_control')
                          ->where('V_MATERIA.nombre_busqueda', 'LIKE', "%{$valorCriterio}%")
                          ->orderBy('V_MATERIA.nombre_busqueda', $orden);
                }
                break;
            case 'serie':
                if ($valorCriterio) {
                    $query->join('V_SERIE', 'vt.nro_control', '=', 'V_SERIE.nro_control')
                          ->where('V_SERIE.nombre_busqueda', 'LIKE', "%{$valorCriterio}%")
                          ->orderBy('V_SERIE.nombre_busqueda', $orden);
                }
                break;
        }

        if ($titulo) {
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

        $allResults = (clone $query)->addBinding($bindings, 'select')->get();

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

        $resultados = $query->addBinding($bindings, 'select')->paginate(10);

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

        $query = DetalleMaterial::query()
            ->select('DSM_TITULO')
            ->where('DSM_AUTOR_EDITOR', '=', urldecode($autor));

        if ($titulo) {
            $query->where('DSM_TITULO', 'LIKE', '%' . $titulo . '%');
        }

        $titulos = $query->get();

        return view('TitulosPorAutor', compact('autor', 'titulos', 'titulo'));
    }

    public function mostrarTitulosPorEditorial($editorial, Request $request)
    {
        $titulo = $request->input('titulo');

        $query = DetalleMaterial::query()
            ->select('DSM_TITULO')
            ->where('DSM_EDITORIAL', '=', urldecode($editorial));

        if ($titulo) {
            $query->where('DSM_TITULO', 'LIKE', '%' . $titulo . '%');
        }

        $titulos = $query->get();

        return view('TitulosPorEditorial', compact('editorial', 'titulos', 'titulo'));
    }

    public function mostrarTitulosPorMateria($materia, Request $request)
    {
        $titulo = $request->input('titulo');

        $query = DB::table('DETALLE_MATERIAL')
            ->join('V_MATERIA', 'DETALLE_MATERIAL.som_numero', '=', 'V_MATERIA.nro_control')
            ->select('DETALLE_MATERIAL.DSM_TITULO')
            ->where('V_MATERIA.nombre_busqueda', '=', urldecode($materia));

        if ($titulo) {
            $query->where('DETALLE_MATERIAL.DSM_TITULO', 'LIKE', '%' . $titulo . '%');
        }

        $titulos = $query->get();

        return view('TitulosPorMateria', compact('materia', 'titulos', 'titulo'));
    }

    public function mostrarTitulosPorSerie($serie, Request $request)
    {
        $titulo = $request->input('titulo');

        $query = DB::table('DETALLE_MATERIAL')
            ->join('V_SERIE', 'DETALLE_MATERIAL.som_numero', '=', 'V_SERIE.nro_control')
            ->select('DETALLE_MATERIAL.DSM_TITULO')
            ->where('V_SERIE.nombre_busqueda', '=', urldecode($serie));

        if ($titulo) {
            $query->where('DETALLE_MATERIAL.DSM_TITULO', 'LIKE', '%' . $titulo . '%');
        }

        $titulos = $query->get();

        return view('TitulosPorSerie', compact('serie', 'titulos', 'titulo'));
    }
}
