<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetalleMaterial;

class BusquedaAvanzadaController extends Controller
{
    public function buscar(Request $request)
    {
        // Validación
        $request->validate([
            'autor' => 'nullable|string|max:255',
            'titulo' => 'nullable|string|max:255',
        ]);

        //obtencion
        $autor = $request->input('autor');
        $titulo = $request->input('titulo');

        //consulta
        $query = DetalleMaterial::query();

        if ($autor) {
            $query->where('DSM_AUTOR_EDITOR', 'LIKE', "%$autor%");
        }

        if ($titulo) {
            $query->where('DSM_TITULO', 'LIKE', "%$titulo%");
        }

        // Ordenamiento
        $resultados = $query->select(
            'DSM_TITULO AS Titulo',
            'DSM_AUTOR_EDITOR AS Autor',
            'DSM_EDITORIAL AS Editorial',
            'DSM_PUBLICACION AS Fecha_Publicacion',
            'DSM_ISBN_ISSN AS ISBN_ISSN'
        )
        ->orderBy('DSM_TITULO', 'ASC')
        ->get();

        //resultados como JSON
        if ($resultados->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron resultados para los criterios proporcionados.',
            ], 404);
        }

        return response()->json($resultados);
    }
}

