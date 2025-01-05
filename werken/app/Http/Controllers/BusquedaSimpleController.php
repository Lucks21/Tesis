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
    public function index()
    {
        $registros = DetalleMaterial::limit(30)->paginate(10);;

        return response()->json($registros);
    }
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

        $resultados = $modelo::where(function ($query) use ($palabras) {
            foreach ($palabras as $palabra) {
                $query->where('nombre_busqueda', 'LIKE', "%{$palabra}%");
            }
        })->with('titulos')->paginate(10)->withQueryString();

        return view('ResultadosViewBS', [
            'resultados' => $resultados,
            'busqueda' => $busqueda,
            'criterio' => $criterio,
        ]);
    }
}
