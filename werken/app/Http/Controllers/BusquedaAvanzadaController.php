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
            'orden' => 'nullable|string|in:asc,desc', // Validar el orden ascendente o descendente
        ]);
    
        $criterio = $request->input('criterio');
        $valorCriterio = $request->input('valor_criterio');
        $titulo = $request->input('titulo');
        $orden = $request->input('orden', 'asc'); // Orden predeterminado: ascendente
    
        // Construir la consulta base
        $query = DetalleMaterial::query()
            ->select(
                'DSM_TITULO AS Titulo',
                'DSM_AUTOR_EDITOR AS Autor',
                'DSM_EDITORIAL AS Editorial',
                'DSM_PUBLICACION AS Año_Publicacion'
            );
    
        // Aplicar filtros
        if ($valorCriterio) {
            $query->where('DSM_AUTOR_EDITOR', 'LIKE', "%{$valorCriterio}%");
        }
    
        if ($titulo) {
            $query->where('DSM_TITULO', 'LIKE', "%{$titulo}%");
        }
    
        // Aplicar el orden en el título
        $query->orderBy('DSM_TITULO', $orden);
    
        // Paginación
        $resultados = $query->paginate(10);
    
        return view('BusquedaAvanzadaResultados', compact(
            'resultados', 'criterio', 'valorCriterio', 'titulo', 'orden'
        ));
    }
    
}
