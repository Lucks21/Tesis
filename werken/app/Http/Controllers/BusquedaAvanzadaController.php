<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Materia;
use App\Models\Serie;
use App\Models\Editorial;
use App\Models\Autor;
use App\Models\DetalleMaterial;

class BusquedaAvanzadaController extends Controller
{
    public function buscar(Request $request)
    {
        $request->validate([
            'criterio' => 'required|string|in:autor,materia,serie,editorial',
            'valor_criterio' => 'nullable|string|max:255',
            'titulo' => 'nullable|string|max:255',
        ]);

        $criterio = $request->input('criterio');
        $valorCriterio = $request->input('valor_criterio');
        $titulo = $request->input('titulo');

        $modelos = [
            'autor' => Autor::class,
            'materia' => Materia::class,
            'serie' => Serie::class,
            'editorial' => Editorial::class,
        ];

        if (!isset($modelos[$criterio])) {
            return response()->json([
                'message' => 'El criterio de búsqueda no es válido.',
            ], 400);
        }

        $modelo = $modelos[$criterio];

        // Consulta con paginación
        $resultados = $modelo::where('nombre_busqueda', 'LIKE', "%{$valorCriterio}%")
            ->with(['titulos' => function ($query) use ($titulo) {
                if ($titulo) {
                    $query->where('nombre_busqueda', 'LIKE', "%{$titulo}%");
                }
            }])
            ->paginate(10); // Paginación de 10 resultados por página

        if ($resultados->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron resultados para los criterios proporcionados.',
            ], 404);
        }

        return view('BusquedaAvanzadaResultados', compact('resultados', 'criterio', 'valorCriterio', 'titulo'));
    }
}
