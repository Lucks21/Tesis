<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetalleMaterial;
use App\Models\Materia;
use App\Models\Autor;
use App\Models\Editorial;
use App\Models\Serie;
use App\Models\Titulo;


class BusquedaSimpleController extends Controller
{
    public function buscarPorTitulo(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
        ]);
    
        $titulo = $request->input('titulo');
        $palabras = explode(' ', $titulo);
    
        $resultados = Titulo::where(function ($query) use ($palabras) {
            foreach ($palabras as $palabra) {
                $query->orWhere('nombre_busqueda', 'LIKE', "%{$palabra}%");
            }
        })->paginate(10);
    
    
        if ($resultados->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron resultados para el título proporcionado.',
            ], 404);
        }
    
        return response()->json($resultados);
    }
    
    public function buscar(Request $request)
    {
        $request->validate([
            'criterio' => 'required|string|in:autor,editorial,serie,materia',
            'busqueda' => 'required|string|max:255',
        ]);
    
        $criterio = $request->input('criterio');
        $busqueda = $request->input('busqueda');
        $palabras = explode(' ', $busqueda);
    
        $modelos = [
            'autor' => Autor::class,
            'editorial' => Editorial::class,
            'serie' => Serie::class,
            'materia' => Materia::class,
        ];
    
        $modelo = $modelos[$criterio];
    
        $resultadosSinAgrupar = $modelo::where(function ($query) use ($palabras) {
            foreach ($palabras as $palabra) {
                $query->where('nombre_busqueda', 'LIKE', "%{$palabra}%");
            }
        })->with('titulos')->get();
    
        $resultadosAgrupados = $resultadosSinAgrupar->groupBy('nombre_busqueda')->map(function ($group) {
            return [
                'nombre' => $group->first()->nombre_busqueda,
                'titulos' => $group->flatMap->titulos->map(function ($titulo) {
                    return $titulo->nombre_busqueda;
                }),
            ];
        })->values();
    
        $pagina = $request->input('page', 1);
        $porPagina = 10;
        $resultadosPaginados = $resultadosAgrupados->slice(($pagina - 1) * $porPagina, $porPagina);
    
        return view('ResultadosViewBS', [
            'resultados' => new \Illuminate\Pagination\LengthAwarePaginator(
                $resultadosPaginados,
                $resultadosAgrupados->count(),
                $porPagina,
                $pagina,
                ['path' => $request->url(), 'query' => $request->query()]
            ),
            'busqueda' => $busqueda,
            'criterio' => $criterio,
        ]);
    }
    public function recursosAsociados($criterio, $valor)
    {
        $modelos = [
            'autor' => Autor::class,
            'editorial' => Editorial::class,
            'serie' => Serie::class,
            'materia' => Materia::class,
        ];
    
        if (!array_key_exists($criterio, $modelos)) {
            abort(404, 'Criterio no válido.');
        }
    
        $modelo = $modelos[$criterio];
    
        $recursos = $modelo::where('nombre_busqueda', $valor)->with('titulos')->get();
    
        if ($recursos->isEmpty()) {
            abort(404, 'No se encontraron recursos asociados.');
        }
    
        $titulos = $recursos->flatMap->titulos;
    
        $pagina = request()->input('page', 1);
        $porPagina = 10;
        $paginados = $titulos->forPage($pagina, $porPagina);
    
        return view('RecursosAsociadosView', [
            'criterio' => ucfirst($criterio),
            'valor' => $valor,
            'recursos' => new \Illuminate\Pagination\LengthAwarePaginator(
                $paginados,
                $titulos->count(),
                $porPagina,
                $pagina,
                ['path' => request()->url(), 'query' => request()->query()]
            ),
        ]);
    }    
}
