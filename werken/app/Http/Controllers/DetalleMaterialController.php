<?php

namespace App\Http\Controllers;

use App\Models\DetalleMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetalleMaterialController extends Controller
{    public function show($numero)
    {
        // Consulta detalle del material
        $detalleMaterial = DB::select("EXEC sp_WEB_detalle_existencias ?", [$numero]);
        if (empty($detalleMaterial)) {
            return redirect()->route('resultados')->with('error', 'No se encontró el material solicitado.');
        }
        
        // Convertir el resultado a objeto para facilitar el acceso a las propiedades
        $detalleMaterial = $detalleMaterial[0];
        
        return view('detalle-material', [
            'detalleMaterial' => $detalleMaterial
        ]);
    }
}
