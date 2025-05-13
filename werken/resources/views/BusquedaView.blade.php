<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Búsqueda Simple - Sistema de Bibliotecas UBB</title>
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
        .form-input, .form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
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
                <a href="/" class="nav-link">Inicio</a>
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

    <!-- Contenido principal -->
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Búsqueda Simple</h1>
            
            <div class="bg-white rounded-xl shadow-lg p-8">
                <form method="GET" action="{{ route('resultados') }}" id="form-busqueda" class="space-y-6">
                    <div class="space-y-4">
                        <label for="criterio" class="block text-lg font-semibold text-gray-700">
                            <i class="fas fa-filter mr-2"></i>Buscar por:
                        </label>
                        <select id="criterio" name="criterio" class="form-select" onchange="updateFormAction()">
                            <option value="autor" {{ request('criterio') === 'autor' ? 'selected' : '' }}>Autor</option>
                            <option value="editorial" {{ request('criterio') === 'editorial' ? 'selected' : '' }}>Editorial</option>
                            <option value="serie" {{ request('criterio') === 'serie' ? 'selected' : '' }}>Serie</option>
                            <option value="materia" {{ request('criterio') === 'materia' ? 'selected' : '' }}>Materia</option>
                            <option value="titulo" {{ request('criterio') === 'titulo' ? 'selected' : '' }}>Título</option>
                        </select>
                    </div>

                    <div class="space-y-4">
                        <label for="busqueda" class="block text-lg font-semibold text-gray-700">
                            <i class="fas fa-search mr-2"></i>Término de búsqueda:
                        </label>
                        <input type="text" id="busqueda" name="busqueda" 
                               class="form-input"
                               placeholder="Ingrese el término a buscar..."
                               value="{{ request('busqueda') }}" required>
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

<script>
    function updateFormAction() {
        const criterio = document.getElementById('criterio').value;
        const form = document.getElementById('form-busqueda');

        if (criterio === 'titulo') {
            form.action = "{{ route('buscar.titulo') }}";
        } else {
            form.action = "{{ route('resultados') }}";
        }
    }
    document.addEventListener('DOMContentLoaded', updateFormAction);
</script>