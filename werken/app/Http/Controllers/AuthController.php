<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    // Mostrar el formulario de inicio de sesión
    public function showLogin()
    {
        return view('loginView');
    }

    // Procesar el inicio de sesión
    public function login(Request $request)
    {
        try {
            // Validar el formulario de inicio de sesión
            $request->validate([
                'rut_usuario' => 'required|string',
                'password' => 'required|string',
            ]);

            // Obtener datos del formulario y limpiar el RUT
            $rut_usuario = trim(str_replace(['.', '-'], '', $request->input('rut_usuario')));
            $password = trim($request->input('password'));

            // Verificar credenciales usando el procedimiento almacenado
            $result = DB::select('EXEC sp_WEB_usuario ?', [$rut_usuario]);
            \Log::info('Resultado de sp_WEB_usuario:', [
                'rut_usuario' => $rut_usuario,
                'result' => $result
            ]);

            if (!empty($result)) {
                $user = $result[0];
                \Log::info('Datos del usuario:', [
                    'campos_disponibles' => array_keys(get_object_vars($user)),
                    'datos_completos' => get_object_vars($user)
                ]);

                // Verificar si el campo cc coincide con la contraseña
                if (property_exists($user, 'cc') && trim($user->cc) === $password) {
                    // Si las credenciales son correctas, guardar en sesión
                    session([
                        'rut_usuario' => $rut_usuario,
                        'clave' => $password,
                        'nombre_usuario' => trim($user->nombre_usuario),
                        'departamento' => trim($user->departamento)
                    ]);
                    
                    return redirect('/');
                }
            }

            // Si las credenciales son incorrectas, redirigir de vuelta con error
            return back()
                ->withErrors(['login_error' => 'RUT o contraseña incorrectos.'])
                ->withInput($request->except('password'));

        } catch (\Exception $e) {
            \Log::error('Error en el login: ' . $e->getMessage());
            return back()
                ->withErrors(['login_error' => 'Ocurrió un error al intentar iniciar sesión. Por favor, intente nuevamente.'])
                ->withInput($request->except('password'));
        }
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        Session::flush();
        return redirect()->route('login');
    }
}
