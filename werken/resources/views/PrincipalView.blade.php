<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WERKEN</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="center-screen">

    <!-- Contenedor principal centrado -->
    <div class="main-container">
        <h1 class="text-3xl font-bold mb-4">Bienvenidos a WERKEN</h1>

        <!-- Barra de navegación -->
        <div class="navbar">
            <a href="#busqueda-simple" class="navbar-link">Búsqueda Simple</a>
            <a href="#busqueda-avanzada" class="navbar-link">Búsqueda Avanzada</a>

            <!-- Ícono de usuario para "Cuenta Personal" -->
            <div class="dropdown mx-4">
                <a href="#cuenta-personal" class="dropbtn flex items-center space-x-1 navbar-link">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-3.86 0-8 1.92-8 5v1h16v-1c0-3.08-4.14-5-8-5z"/>
                    </svg>
                </a>
                <div class="dropdown-content">
                    <a href="#lista-de-deseos">Lista de deseos</a>
                    <a href="#leidos">Leídos</a>
                    <a href="#historial">Historial</a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
