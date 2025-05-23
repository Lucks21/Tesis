<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Búsqueda Avanzada - Sistema de Bibliotecas UBB</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @font-face {
            font-family: 'Tipo-UBB';
            src: url('{{ asset('fonts/Tipo-UBB-Black_Condensed.otf') }}') format('opentype');
            font-weight: 900;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Tipo-UBB';
            src: url('{{ asset('fonts/Tipo-UBB-Bold_Condensed.otf') }}') format('opentype');
            font-weight: bold;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Tipo-UBB';
            src: url('{{ asset('fonts/Tipo-UBB-Regular_Condensed.otf') }}') format('opentype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Tipo-UBB';
            src: url('{{ asset('fonts/Tipo-UBB-Light_Condensed.otf') }}') format('opentype');
            font-weight: 300;
            font-style: normal;
            font-display: swap;
        }

        /* Preload critical fonts */
        head {
            link[rel=preload][as=font] {
                href: url('{{ asset('fonts/Tipo-UBB-Regular_Condensed.otf') }}');
                type: 'font/otf';
                crossorigin: 'anonymous';
            }
        }

        /* Base styles */
        body {
            margin: 0;
            font-family: 'Tipo-UBB', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Institutional bar */
        .institutional-bar {
            background-color: #003876;
            color: white;
            font-size: 0.875rem;
            padding: 0.5rem 0;
        }
        .institutional-bar a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
        }
        .institutional-bar a:hover {
            text-decoration: underline;
        }

        /* Main header */
        .main-header {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem 0;
            text-align: center;
        }
        .logos-container {
            display: flex;
            justify-content: center;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            gap: 2rem;
        }
        .logo-group {
            display: flex;
            align-items: center;
            gap: 2rem;
            height: 64px;
            padding: 1rem;
        }
        .direccion-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }
        .direccion-img {
            height: 100%;
            width: auto;
            max-height: 64px;
            object-fit: contain;
        }
        img.h-16 {
            height: 64px;
            width: auto;
            object-fit: contain;
        }

        /* Navigation */
        .nav-container {
            background-color: white;
            border-bottom: 1px solid #e5e7eb;
        }
        .nav-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-links {
            display: flex;
            gap: 2rem;
        }
        .nav-link {
            color: #4B5563;
            text-decoration: none;
            padding: 1rem;
            transition: color 0.2s;
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: normal;
        }
        .nav-link:hover {
            color: #003876;
        }

        /* Search buttons */
        .search-actions {
            display: flex;
            gap: 1rem;
        }
        .search-button {
            background-color: #003876;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: background-color 0.2s;
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: bold;
        }
        .search-button:hover {
            background-color: #002b5c;
        }

        /* Main content */
        .main-content {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .page-title {
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: 900;
            font-size: 2.5rem;
            color: #1a202c;
            margin-bottom: 2rem;
            text-align: center;
        }
        .search-form {
            margin-bottom: 2rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #4a5568;
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: bold;
        }
        .form-input, .form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #003876;
            border-radius: 4px;
            font-size: 1rem;
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: normal;
        }
    </style>
</head>
<body>    <!-- Barra institucional -->
    <div class="institutional-bar">
        <div class="container mx-auto px-4">
            <div class="flex justify-center space-x-8">
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
        <div class="logos-container">
            <div class="logo-group">
                <img src="{{ asset('img/logo-sistema-bibliotecas.png') }}" alt="Sistema de Bibliotecas" class="h-16">
                <div class="direccion-wrapper">
                    <img src="{{ asset('img/logo_direccion_bibliotecas.png') }}" alt="Dirección de Bibliotecas" class="direccion-img">
                </div>
            </div>
            <div class="logo-group">
                <img src="{{ asset('img/logo-ciencia-abierta.png') }}" alt="Ciencia Abierta" class="h-16">
                <img src="{{ asset('img/logo-ubb.png') }}" alt="Universidad del Bío-Bío" class="h-16">
            </div>
        </div>
    </header>

    <!-- Navegación principal -->
    <nav class="nav-container">
        <div class="nav-content">            <div class="nav-links">
                <a href="{{ url('/') }}" class="nav-link">Inicio</a>
                <a href="#" class="nav-link">Quiénes somos</a>
                <a href="#" class="nav-link">Recursos</a>
                <a href="#" class="nav-link">Servicios</a>
                <a href="#" class="nav-link">Bibliotecas</a>
                <a href="#" class="nav-link">Galería</a>
                <a href="#" class="nav-link">Noticias</a>
                <a href="#" class="nav-link">Contacto</a>
            </div>
            <div class="search-actions">
                <a href="{{ route('busqueda') }}" class="search-button">
                    <i class="fas fa-search mr-2"></i>Búsqueda Simple
                </a>
                <a href="{{ route('busqueda-avanzada') }}" class="search-button">
                    <i class="fas fa-filter mr-2"></i>Búsqueda Avanzada
                </a>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Búsqueda Avanzada</h1>
            
            <div class="search-form p-8">
                <form action="{{ route('busqueda-avanzada-resultados') }}" method="GET" class="space-y-6">
                    <div class="space-y-4">
                        <label for="criterio" class="block text-lg font-semibold text-gray-700">
                            <i class="fas fa-filter mr-2"></i>Buscar por:
                        </label>
                        <select id="criterio" name="criterio" class="form-select">
                            <option value="autor" {{ request('criterio') == 'autor' ? 'selected' : '' }}>Autor</option>
                            <option value="materia" {{ request('criterio') == 'materia' ? 'selected' : '' }}>Materia</option>
                            <option value="serie" {{ request('criterio') == 'serie' ? 'selected' : '' }}>Serie</option>
                            <option value="editorial" {{ request('criterio') == 'editorial' ? 'selected' : '' }}>Editorial</option>
                        </select>
                    </div>

                    <div class="space-y-4">
                        <label for="valor_criterio" class="block text-lg font-semibold text-gray-700">
                            <i class="fas fa-search mr-2"></i>Nombre del criterio:
                        </label>
                        <input type="text" id="valor_criterio" name="valor_criterio" 
                               value="{{ request('valor_criterio') }}"
                               class="form-input"
                               placeholder="Ingrese el valor a buscar...">
                    </div>

                    <div class="space-y-4">
                        <label for="titulo" class="block text-lg font-semibold text-gray-700">
                            <i class="fas fa-book mr-2"></i>Título:
                        </label>
                        <input type="text" id="titulo" name="titulo" 
                               value="{{ request('titulo') }}"
                               class="form-input"
                               placeholder="Ingrese el título...">
                    </div>                    <div class="flex justify-between">
                        <a href="{{ url('/') }}" class="search-button">
                            <i class="fas fa-home mr-2"></i>Volver
                        </a>
                        <button type="submit" class="search-button">
                            <i class="fas fa-search mr-2"></i>Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>

