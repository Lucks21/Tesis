<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Bibliotecas UBB</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .institutional-bar {
            background-color: #003876;
            color: white;
            font-size: 0.875rem;
        }
        .institutional-bar a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
        }
        .institutional-bar a:hover {
            text-decoration: underline;
        }
        .main-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
        }
        .nav-link {
            color: #4B5563;
            text-decoration: none;
            padding: 1rem;
            transition: color 0.2s;
        }
        .nav-link:hover {
            color: #003876;
        }
        .search-container {
            background: linear-gradient(rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.9)), url('/img/biblioteca-bg.jpg');
            background-size: cover;
            background-position: center;
            padding: 4rem 0;
        }
        .search-box {
            max-width: 800px;
            margin: 0 auto;
        }
        .search-input {
            border: 2px solid #003876;
            border-radius: 4px;
            padding: 0.75rem 1rem;
            width: 100%;
            font-size: 1rem;
        }
        .search-button {
            background-color: #003876;
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .search-button:hover {
            background-color: #002b5c;
        }
        .welcome-text {
            color: #4B5563;
            max-width: 800px;
            margin: 2rem auto;
            text-align: center;
            line-height: 1.6;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Barra institucional -->
    <div class="institutional-bar">
        <div class="container mx-auto px-4">
            <div class="flex justify-end space-x-4 py-1">
                <a href="#">Web UBB</a>
                <a href="#">Intranet</a>
                <a href="#">Correo Institucional</a>
                <a href="#">Adecca UBB</a>
                <a href="#">Moodle UBB</a>
            </div>
        </div>
    </div>

    <!-- Cabecera principal -->
    <header class="main-header">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-8">
                    <img src="{{ asset('img/logo-sistema-bibliotecas.png') }}" alt="Sistema de Bibliotecas" class="h-16">
                    <img src="{{ asset('img/logo-direccion-bibliotecas.png') }}" alt="Dirección de Bibliotecas" class="h-16">
                </div>
                <div class="flex items-center">
                    <img src="{{ asset('img/logo-ciencia-abierta.png') }}" alt="Ciencia Abierta" class="h-16">
                    <img src="{{ asset('img/logo-ubb.png') }}" alt="Universidad del Bío-Bío" class="h-16 ml-8">
                </div>
            </div>
        </div>
    </header>

    <!-- Navegación principal -->
    <nav class="bg-white shadow">
        <div class="container mx-auto px-4">
            <div class="flex space-x-6">
                <a href="#" class="nav-link">Inicio</a>
                <a href="#" class="nav-link">Quiénes somos</a>
                <a href="#" class="nav-link">Recursos</a>
                <a href="#" class="nav-link">Servicios</a>
                <a href="#" class="nav-link">Bibliotecas</a>
                <a href="#" class="nav-link">Galería</a>
                <a href="#" class="nav-link">Noticias</a>
                <a href="#" class="nav-link">Contacto</a>
            </div>
        </div>
    </nav>

    <!-- Contenedor principal -->
    <main>
        <div class="search-container">
            <div class="container mx-auto px-4">
                <h1 class="text-4xl font-bold text-center text-gray-800 mb-8">Sistema de Bibliotecas UBB</h1>
                
                <div class="welcome-text">
                    <h2 class="text-2xl font-semibold text-blue-900 mb-4">¡Bienvenid@s!</h2>
                    <p>Descubre un mundo de conocimiento y cultura en nuestro nuevo sitio web. Aquí, encontrarás toda la información que necesitas sobre nuestras bibliotecas, colecciones, servicios y eventos. Nos hemos renovado para ofrecerte una experiencia en línea más intuitiva, dinámica y enriquecedora.</p>
                </div>

                <div class="flex justify-center space-x-4 mb-8">
                    <a href="{{ route('busqueda') }}" class="search-button flex items-center">
                        <i class="fas fa-search mr-2"></i>
                        Búsqueda Simple
                    </a>
                    <a href="{{ route('busqueda-avanzada') }}" class="search-button flex items-center">
                        <i class="fas fa-filter mr-2"></i>
                        Búsqueda Avanzada
                    </a>
                </div>

                <div class="search-box">
                    <form action="{{ route('busqueda') }}" method="GET" class="flex space-x-4">
                        <input type="text" 
                               name="query" 
                               class="search-input" 
                               placeholder="Búsqueda interna en la web Sistema de Biblioteca UBB"
                               value="{{ request('query') }}">
                        <button type="submit" class="search-button">BÚSQUEDA</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
