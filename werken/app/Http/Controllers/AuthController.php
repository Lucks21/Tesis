<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        // Validar el formulario de inicio de sesión
        $request->validate([
            'rut_usuario' => 'required|string',
            'password' => 'required|string',
        ]);

        // Obtener datos del formulario
        $rut_usuario = trim($request->input('rut_usuario'));
        $password = trim($request->input('password'));

        // Verificar credenciales en la base de datos
        $user = DB::table('usuario')
                    ->where('rut_usuario', $rut_usuario)
                    ->where('rut', $password) 
                    ->first();

        if ($user) {
            // Si las credenciales son correctas, iniciar sesión
            Session::put('rut_usuario', $user->rut_usuario);
            return response()->json(['success' => true]); // Respuesta JSON para éxito
        } else {
            return response()->json(['success' => false, 'message' => 'RUT o contraseña incorrectos.']);
        }
    }

    public function logout(Request $request)
    {
        Session::flush();
        return redirect('/principal');
    }
}
