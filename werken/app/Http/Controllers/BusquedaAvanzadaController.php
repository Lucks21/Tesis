<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DetalleMaterial;

class BusquedaAvanzadaController extends Controller
{
    public function buscar(Request $request)
    {
        $criterio = $request->input('criterio');
        $valorCriterio = $request->input('valor_criterio');
        $titulo = $request->input('titulo');
        $orden = $request->input('orden', 'asc');

        $autoresQuery = DB::table('DETALLE_MATERIAL')
            ->select('DSM_AUTOR_EDITOR as autor')
            ->distinct();

        if ($criterio === 'autor' && $valorCriterio) {
            $autoresQuery->where('DSM_AUTOR_EDITOR', 'LIKE', "%{$valorCriterio}%");
        }

        if ($titulo) {
            $autoresQuery->where('DSM_TITULO', 'LIKE', "%{$titulo}%");
        }

        $autoresQuery->orderBy('DSM_AUTOR_EDITOR', $orden);
        $autores = $autoresQuery->get();

        return view('BusquedaAvanzadaResultados', compact('autores', 'criterio', 'valorCriterio', 'titulo', 'orden'));
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
}
