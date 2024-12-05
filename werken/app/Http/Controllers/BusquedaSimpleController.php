<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetalleMaterial;


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

        $resultados = DetalleMaterial::where('DSM_TITULO', 'LIKE', "%{$titulo}%")
            ->get();

        return response()->json($resultados);
    }

    public function buscarPorAutor(Request $request)
    {
        $request->validate([
            'autor' => 'required|string|max:255',
        ]);

        $autor = $request->input('autor');

        $resultados = DetalleMaterial::where('DSM_AUTOR_EDITOR', 'LIKE', "%{$autor}%")
            ->get();

        return response()->json($resultados);
    }

}
