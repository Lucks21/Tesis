<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WERKEN</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('js/loginDropdown.js') }}" defer></script>
</head>
<body class="center-screen bg-gray-100">

    <!-- Contenedor principal centrado -->
    <div class="main-container">
        <h1 class="text-3xl font-bold mb-4">Bienvenidos a WERKEN</h1>

        <!-- Barra de navegación -->
        <div class="navbar bg-blue-800 p-4 rounded">
            <a href="#busqueda-simple" class="navbar-link text-white px-4">Búsqueda Simple</a>
            <a href="#busqueda-avanzada" class="navbar-link text-white px-4">Búsqueda Avanzada</a>

            <!-- Ícono de usuario con menú desplegable para iniciar sesión -->
            <div class="relative ml-4">
                <a href="javascript:void(0);" id="user-icon" class="flex items-center space-x-1 cursor-pointer text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-3.86 0-8 1.92-8 5v1h16v-1c0-3.08-4.14-5-8-5z"/>
                    </svg>
                </a>

                <!-- Formulario de inicio de sesión desplegable -->
                <div id="login-dropdown" class="absolute right-0 mt-2 w-64 bg-white border rounded-lg shadow-lg hidden">
                    <form id="login-form" action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="rut_usuario" class="block text-sm font-medium text-gray-700">RUT Usuario:</label>
                            <input type="text" name="rut_usuario" id="rut_usuario" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700">Contraseña:</label>
                            <input type="password" name="password" id="password" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Iniciar Sesión</button>
                        <div id="login-error" class="text-red-500 mt-2 hidden">RUT o contraseña incorrectos.</div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
