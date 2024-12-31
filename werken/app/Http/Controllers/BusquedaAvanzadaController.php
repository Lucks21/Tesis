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
            'orden' => 'nullable|string|in:ascendente,descendente',
        ]);
    
        $criterio = $request->input('criterio');
        $valorCriterio = $request->input('valor_criterio');
        $orden = $request->input('orden', 'ascendente');
        $titulo = $request->input('titulo');
    
        if ($criterio === 'autor') {
            $query = DetalleMaterial::query()
                ->select('DSM_AUTOR_EDITOR as autor')
                ->distinct();
    
            // Si hay un valor de criterio, filtrar
            if (!empty($valorCriterio)) {
                $query->where('DSM_AUTOR_EDITOR', 'LIKE', '%' . $valorCriterio . '%');
            }
    
            // Ordenar resultados
            $query->orderBy('DSM_AUTOR_EDITOR', $orden === 'ascendente' ? 'asc' : 'desc');
    
            $resultados = $query->paginate(10);
    
            return view('BusquedaAvanzadaResultados', [
                'resultados' => $resultados,
                'criterio' => $criterio,
                'valorCriterio' => $valorCriterio,
                'titulo' => $titulo,
                'orden' => $orden,
            ]);
        }
    
        // Si no es autor, manejar otros casos como materia, serie, editorial
        return back()->withErrors(['message' => 'Criterio no válido o no implementado.']);
    }
    

    public function mostrarTitulosPorAutor($autor)
    {
        // Decodificar el nombre del autor si fue codificado en el enlace
        $autor = urldecode($autor);
    
        // Obtener los títulos relacionados con el autor
        $titulos = DetalleMaterial::where('DSM_AUTOR_EDITOR', $autor)->pluck('DSM_TITULO');
    
        return view('TitulosPorAutor', compact('autor', 'titulos'));
    }
    
    
}
