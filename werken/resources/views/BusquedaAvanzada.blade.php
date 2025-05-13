<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Búsqueda Avanzada - Sistema de Bibliotecas UBB</title>
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
        .search-form {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 56, 118, 0.1);
        }
        .form-input {
            border: 2px solid #003876;
            border-radius: 4px;
            padding: 0.75rem 1rem;
            width: 100%;
            font-size: 1rem;
            transition: all 0.2s;
        }
        .form-input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 56, 118, 0.2);
        }
        .form-select {
            border: 2px solid #003876;
            border-radius: 4px;
            padding: 0.75rem 1rem;
            width: 100%;
            font-size: 1rem;
            background-color: white;
        }
        .form-button {
            background-color: #003876;
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .form-button:hover {
            background-color: #002b5c;
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
            <div class="flex justify-between items-center">
                <div class="flex space-x-6">
                    <a href="/" class="nav-link">Inicio</a>
                    <a href="#" class="nav-link">Quiénes somos</a>
                    <a href="#" class="nav-link">Recursos</a>
                    <a href="#" class="nav-link">Servicios</a>
                    <a href="#" class="nav-link">Bibliotecas</a>
                    <a href="#" class="nav-link">Galería</a>
                    <a href="#" class="nav-link">Noticias</a>
                    <a href="#" class="nav-link">Contacto</a>
                </div>                <div class="flex space-x-4">
                    <a href="{{ route('busqueda') }}" class="search-button flex items-center">
                        <i class="fas fa-search mr-2"></i>Búsqueda Simple
                    </a>
                    <a href="{{ route('busqueda-avanzada') }}" class="search-button flex items-center">
                        <i class="fas fa-filter mr-2"></i>Búsqueda Avanzada
                    </a>
                </div>
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
                    </div>

                    <div class="flex justify-center space-x-4 pt-6">
                        <button type="submit" class="form-button">
                            <i class="fas fa-search mr-2"></i>Buscar
                        </button>
                        <a href="/" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200">
                            <i class="fas fa-home mr-2"></i>Volver
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
