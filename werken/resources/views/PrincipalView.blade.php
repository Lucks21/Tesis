<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WERKEN</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="{{ asset('js/loginDropdown.js') }}" defer></script>
</head>
<body class="center-screen bg-gray-100">

    <div class="main-container">
        <h1 class="text-3xl font-bold mb-4">Bienvenidos a WERKEN</h1>

        <div class="navbar bg-blue-800 p-4 rounded">
            <a href="{{ route('busqueda-avanzada') }}" class="navbar-link text-white px-4">Búsqueda Avanzada</a>
            <div class="relative ml-4">
                <a href="{{ route('login') }}" class="flex items-center space-x-1 cursor-pointer text-white">
                    <i class="fas fa-user"></i>
                </a>
            </div>
        </div>
        @include('BusquedaView')
    </div>

</body>
</html>
