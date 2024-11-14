<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenidos</title>
    <!-- Agregar estilos básicos para la navegación -->
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .navbar {
            display: flex;
            background-color: #00304E	;
            padding: 10px;
            align-items: center; 
        }
        .navbar a {
            color: white;
            padding: 14px 20px;
            text-decoration: none;
            text-align: center;
        }
        .navbar a:hover, .dropdown:hover .dropbtn {
            background-color: #575757;
        }
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left;
        }
        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }
        .dropdown:hover .dropdown-content {
            display: block;
        }
    </style>
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
