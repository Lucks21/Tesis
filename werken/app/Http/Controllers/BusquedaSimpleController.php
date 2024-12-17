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
        $registros = DetalleMaterial::limit(30)->get();

        return response()->json($registros);
    }
    public function buscarPorTitulo(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
        ]);
    
        $titulo = $request->input('titulo');
    
        $resultados = Titulo::where('nombre_busqueda', 'LIKE', "%{$titulo}%")
            ->get();
    
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
    
        $resultados = Autor::where('nombre_busqueda', 'LIKE', "%{$autor}%")
            ->with(['titulos'])
            ->get()
            ->groupBy('nombre_busqueda')
            ->map(function ($group) {
                return [
                    'autor' => $group->first()->nombre_busqueda,
                    'titulos' => $group->flatMap->titulos,
                ];
            })
            ->values();
    
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

        $resultados = Materia::where('nombre_busqueda', 'LIKE', "%{$materia}%")
            ->with(['titulos'])
            ->get()
            ->groupBy('nombre_busqueda')
            ->map(function ($group) {
                return [
                    'materia' => $group->first()->nombre_busqueda,
                    'titulos' => $group->flatMap->titulos,
                ];
            })
            ->values();

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
    
        $resultados = Editorial::where('nombre_busqueda', 'LIKE', "%{$editorial}%")
            ->with(['titulos'])
            ->get()
            ->groupBy('nombre_busqueda')
            ->map(function ($group) {
                return [
                    'editorial' => $group->first()->nombre_busqueda,
                    'titulos' => $group->flatMap->titulos,
                ];
            })
            ->values();
    
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
    
        $resultados = Serie::where('nombre_busqueda', 'LIKE', "%{$serie}%")
            ->with(['titulos'])
            ->get()
            ->groupBy('nombre_busqueda')
            ->map(function ($group) {
                return [
                    'serie' => $group->first()->nombre_busqueda,
                    'titulos' => $group->flatMap->titulos,
                ];
            })
            ->values();
    
        if ($resultados->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron resultados para la serie proporcionada.',
            ], 404);
        }
    
        return response()->json($resultados);
    }
    
        
}
