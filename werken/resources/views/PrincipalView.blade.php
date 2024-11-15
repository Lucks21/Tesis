<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Werken</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>

    <h1>Bienvenidos a WERKEN</h1>

    <!-- Barra de navegación -->
    <div class="navbar">
        <a href="#busqueda-simple">Búsqueda Simple</a>
        <a href="#busqueda-avanzada">Búsqueda Avanzada</a>

        <!-- Menú desplegable para "Cuenta Personal" -->
        <div class="dropdown">
            <a href="#cuenta-personal" class="dropbtn">Cuenta Personal</a>
            <div class="dropdown-content">
                <a href="#lista-de-deseos">Lista de deseos</a>
                <a href="#leidos">Leídos</a>
                <a href="#historial">Historial</a>
            </div>
        </div>
    </div>

</body>
</html>
