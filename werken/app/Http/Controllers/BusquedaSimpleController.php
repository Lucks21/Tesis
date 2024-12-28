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
    
    public function buscarPorAutor(Request $request)
    {
        $request->validate([
            'autor' => 'required|string|max:255',
        ]);
    
        $autor = $request->input('autor');

        $palabras = explode(' ', $autor);
    
        $resultados = Autor::where(function ($query) use ($palabras) {
            foreach ($palabras as $palabra) {
                $query->where('nombre_busqueda', 'LIKE', "%{$palabra}%");
            }
        })->with('titulos')->paginate(10);
            
    
        if ($resultados->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron resultados para el autor proporcionado.',
            ], 404);
        }
    
        return response()->json($resultados);
    }
    

    public function buscarPorMateria(Request $request)
    {
        $request->validate([
            'materia' => 'required|string|max:255',
        ]);

        $materia = $request->input('materia');
        $palabras = explode(' ', $materia);

        $resultados = Materia::where(function ($query) use ($palabras) {
            foreach ($palabras as $palabra) {
                $query->where('nombre_busqueda', 'LIKE', "%{$palabra}%");
            }
        })->with('titulos')->paginate(10);
        
        
        if ($resultados->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron resultados para la materia proporcionada.',
            ], 404);
        }

        return response()->json($resultados);
    }

    public function buscarPorEditorial(Request $request)
    {
        $request->validate([
            'editorial' => 'required|string|max:255',
        ]);
    
        $editorial = $request->input('editorial');
        $palabras = explode(' ', $editorial);
    
        $resultados = Serie::where(function ($query) use ($palabras) {
            foreach ($palabras as $palabra) {
                $query->where('nombre_busqueda', 'LIKE', "%{$palabra}%");
            }
        })->with('titulos')->paginate(10);
    
        if ($resultados->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron resultados para la editorial proporcionada.',
            ], 404);
        }
    
        return response()->json($resultados);
    }
    

    public function buscarPorSerie(Request $request)
    {
        $request->validate([
            'serie' => 'required|string|max:255',
        ]);
    
        $serie = $request->input('serie');
        $palabras = explode(' ', $serie);
    
        $resultados = Serie::where(function ($query) use ($palabras) {
            foreach ($palabras as $palabra) {
                $query->where('nombre_busqueda', 'LIKE', "%{$palabra}%");
            }
        })->with('titulos')->paginate(10);
    
        if ($resultados->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron resultados para la serie proporcionada.',
            ], 404);
        }
    
        return response()->json($resultados);
    }
    
        
}
