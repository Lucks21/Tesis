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
        $autor = $request->input('autor');
        $titulo = $request->input('titulo');

        //obtencion
        $resultados = DetalleMaterial::query()
        ->when($autor, function ($query, $autor) {
            return $query->where('DSM_AUTOR_EDITOR', 'LIKE', "%$autor%");
        })
        ->when($titulo, function ($query, $titulo) {
            return $query->where('DSM_TITULO', 'LIKE', "%$titulo%");
        })
        ->select(
            'DSM_TITULO AS Titulo',
            'DSM_AUTOR_EDITOR AS Autor',
            'DSM_EDITORIAL AS Editorial',
            'DSM_PUBLICACION AS Fecha_Publicacion',
            'DSM_ISBN_ISSN AS ISBN_ISSN'
        )
        ->orderBy('DSM_TITULO', 'ASC')
        ->paginate(25);

        //resultados 
        return view('BusquedaAvanzadaResultados', compact('resultados', 'autor', 'titulo'));
    }
}

