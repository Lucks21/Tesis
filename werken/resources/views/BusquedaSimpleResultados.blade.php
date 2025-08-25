<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Búsqueda Simple - Sistema de Bibliotecas UBB</title>
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

        /* Base styles */
        body {
            margin: 0;
            font-family: 'Tipo-UBB', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background-color: #f8fafc;
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

        .nav-container {
            background-color: white;
            border-bottom: 1px solid #e5e7eb;
        }

        .results-container {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .filter-section {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .titulo-enlace {
            cursor: pointer !important;
            z-index: 10;
            position: relative;
        }
        .titulo-enlace:hover {
            text-decoration: underline !important;
        }
        .form-checkbox {
            color: #003876;
            border-color: #003876;
        }
        .form-checkbox:checked {
            background-color: #003876;
        }

        .search-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .search-button, .filter-button {
            background-color: #003876;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s;
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: bold;
            min-width: 160px;
            text-align: center;
        }

        .search-button i, .filter-button i {
            margin-right: 0.5rem;
        }

        .search-button:hover, .filter-button:hover {
            background-color: #002b5c;
        }
        .remove-filter {
            background-color: #dc2626;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s;
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: bold;
            min-width: 160px;
            text-align: center;
        }

        .remove-filter:hover {
            background-color: #b91c1c;
        }

        .results-container h1 {
            color: #1f2937;
            font-size: 1.875rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
        }

        .filter-section h2 {
            color: #374151;
            font-size: 1.125rem;
            font-weight: 600;
            margin: 0;
            padding: 1rem;
            background-color: #f3f4f6;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
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

        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
        }

        @media (max-width: 768px) {
            .pagination-container {
                flex-direction: column;
                gap: 1rem;
            }
        }

        .table-header {
            background-color: #003876;
        }

        .table-row:hover {
            background-color: #f8fafc;
        }

        /* Collapsible filters */
        .collapsible-filter {
            margin-bottom: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            overflow: hidden;
            background: white;
        }

        .collapsible-header {
            background-color: #f8fafc;
            padding: 1rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
            transition: background-color 0.2s;
        }

        .collapsible-header:hover {
            background-color: #f1f5f9;
        }

        .collapsible-header h2 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: #374151;
            display: flex;
            align-items: center;
            background: none;
            border: none;
            padding: 0;
        }

        .collapsible-toggle {
            transition: transform 0.2s;
            color: #6b7280;
        }

        .collapsible-filter.expanded .collapsible-toggle {
            transform: rotate(180deg);
        }

        .collapsible-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .collapsible-filter.expanded .collapsible-content {
            max-height: 400px;
        }

        .collapsible-inner {
            padding: 1rem;
        }

        .filter-search-container {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }

        .filter-search-input {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            background-color: white;
        }

        .filter-search-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 1px #3b82f6;
        }

        .filter-options-container {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            background-color: white;
        }

        .filter-options-container::-webkit-scrollbar {
            width: 8px;
        }

        .filter-options-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .filter-options-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .filter-options-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .filter-option {
            padding: 0.5rem 0.75rem;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .filter-option:last-child {
            border-bottom: none;
        }

        .filter-option:hover {
            background-color: #f9fafb;
        }

        .filter-option input[type="checkbox"] {
            margin-right: 0.5rem;
        }

        .filter-option label {
            cursor: pointer;
            flex: 1;
            font-size: 0.875rem;
            color: #374151;
        }

        .no-results-message {
            padding: 1rem;
            text-align: center;
            color: #6b7280;
            font-style: italic;
        }

        .has-active-filter .collapsible-header {
            background-color: #dbeafe;
            border-color: #3b82f6;
        }

        .filter-count {
            font-weight: normal;
            color: #6b7280;
            font-size: 0.875rem;
        }

        .h-16 {
            height: 4rem;
        }

        .container.mx-auto {
            margin-left: auto;
            margin-right: auto;
        }

        .px-4 {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .py-8 {
            padding-top: 2rem;
            padding-bottom: 2rem;
        }

        .flex {
            display: flex;
        }

        .justify-center {
            justify-content: center;
        }

        .space-x-8 > * + * {
            margin-left: 2rem;
        }

        .mr-2 {
            margin-right: 0.5rem;
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
        }
        .nav-link:hover {
            color: #003876;
        }
    </style>
</head>
<body>
    <!-- Barra institucional -->
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
        <div class="nav-content">
            <div class="nav-links">
                <a href="/" class="nav-link">Inicio</a>
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
                <a href="{{ route('perfil') }}" class="search-button" title="Cuenta personal">
                    <i class="fas fa-user-circle mr-2"></i>
                    Perfil{{ session('rut_usuario') ? ' (' . session('rut_usuario') . ')' : '' }}
                </a>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main class="container mx-auto px-4 py-8">
        <div class="results-container p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Resultados de la Búsqueda Simple</h1>
            
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <p class="text-gray-700">
                    <i class="fas fa-filter mr-2"></i>
                    Resultados para "{{ $criterio }}" que contienen "{{ $valorCriterio }}"
                </p>
            </div>

            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Filtros laterales -->
                <div class="lg:w-1/4 space-y-6">
                    
                    @if(count($autores) > 0)
                    <!-- Filtrar por Autor -->
                    <div class="collapsible-filter {{ request()->filled('autor') ? 'has-active-filter expanded' : '' }}">
                        <div class="collapsible-header">
                            <h2>
                                <i class="fas fa-user-edit mr-2"></i>Filtrar por Autor
                                <span class="filter-count">({{ count($autores) }} opciones)</span>
                                @if(request()->filled('autor'))
                                    <span class="ml-2 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                        {{ count((array) request('autor')) }} activo(s)
                                    </span>
                                @endif
                            </h2>
                            <i class="fas fa-chevron-down collapsible-toggle"></i>
                        </div>
                        <div class="collapsible-content">
                            <div class="filter-search-container">
                                <input type="text" 
                                       class="filter-search-input" 
                                       placeholder="Buscar autor..." 
                                       id="search-autor"
                                       onkeyup="filterOptions('autor', this.value)">
                            </div>
                            <div class="collapsible-inner">
                                <form method="GET" action="{{ route('busqueda.sp') }}" class="space-y-3">
                                    <!-- Campos ocultos para mantener contexto de búsqueda -->
                                    <input type="hidden" name="busqueda" value="{{ request('busqueda') }}">
                                    <input type="hidden" name="tipo_busqueda" value="{{ request('tipo_busqueda') }}">
                                    <input type="hidden" name="termino" value="{{ request('termino') }}">
                                    <input type="hidden" name="tipo" value="{{ request('tipo') }}">
                                    <input type="hidden" name="ver_titulos" value="{{ request('ver_titulos') }}">
                                    <input type="hidden" name="orden" value="{{ request('orden', 'asc') }}">
                                    <input type="hidden" name="editorial" value="{{ is_array(request('editorial')) ? implode(',', request('editorial')) : request('editorial') }}">
                                    <input type="hidden" name="campus" value="{{ is_array(request('campus')) ? implode(',', request('campus')) : request('campus') }}">
                                    <input type="hidden" name="materia" value="{{ is_array(request('materia')) ? implode(',', request('materia')) : request('materia') }}">
                                    <input type="hidden" name="serie" value="{{ is_array(request('serie')) ? implode(',', request('serie')) : request('serie') }}">

                                    <div class="filter-options-container" id="options-autor">
                                        @foreach ($autores as $autor)
                                            <div class="filter-option" data-value="{{ strtolower($autor) }}">
                                                <input type="checkbox" name="autor[]" id="autor_{{ $loop->index }}"
                                                       value="{{ $autor }}" {{ is_array(request('autor')) && in_array($autor, request('autor')) ? 'checked' : '' }}
                                                       class="form-checkbox rounded">
                                                <label for="autor_{{ $loop->index }}" class="ml-2 text-gray-700 cursor-pointer flex-1">
                                                    {{ $autor }}
                                                </label>
                                            </div>
                                        @endforeach
                                        <div class="no-results-message" id="no-results-autor" style="display: none;">
                                            No se encontraron autores con ese criterio.
                                        </div>
                                    </div>
                                    <div class="pt-2 border-t border-gray-200">
                                        <button type="submit" class="filter-button w-full">
                                            <i class="fas fa-check mr-2"></i>Aplicar Filtro
                                        </button>
                                        @if(request()->filled('autor'))
                                            <a href="{{ route('busqueda.sp', array_merge(request()->except('autor'), ['termino' => request('termino'), 'tipo' => request('tipo'), 'ver_titulos' => request('ver_titulos')])) }}"
                                               class="remove-filter w-full text-center block mt-2">
                                                <i class="fas fa-times mr-2"></i>Quitar Filtro
                                            </a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(count($editoriales) > 0)
                    <!-- Filtrar por Editorial -->
                    <div class="collapsible-filter {{ request()->filled('editorial') ? 'has-active-filter expanded' : '' }}">
                        <div class="collapsible-header">
                            <h2>
                                <i class="fas fa-building mr-2"></i>Filtrar por Editorial
                                <span class="filter-count">({{ count($editoriales) }} opciones)</span>
                                @if(request()->filled('editorial'))
                                    <span class="ml-2 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                        {{ count((array) request('editorial')) }} activo(s)
                                    </span>
                                @endif
                            </h2>
                            <i class="fas fa-chevron-down collapsible-toggle"></i>
                        </div>
                        <div class="collapsible-content">
                            <div class="filter-search-container">
                                <input type="text" 
                                       class="filter-search-input" 
                                       placeholder="Buscar editorial..." 
                                       id="search-editorial"
                                       onkeyup="filterOptions('editorial', this.value)">
                            </div>
                            <div class="collapsible-inner">
                                <form method="GET" action="{{ route('busqueda.sp') }}" class="space-y-3">
                                    <!-- Campos ocultos para mantener contexto de búsqueda -->
                                    <input type="hidden" name="busqueda" value="{{ request('busqueda') }}">
                                    <input type="hidden" name="tipo_busqueda" value="{{ request('tipo_busqueda') }}">
                                    <input type="hidden" name="termino" value="{{ request('termino') }}">
                                    <input type="hidden" name="tipo" value="{{ request('tipo') }}">
                                    <input type="hidden" name="ver_titulos" value="{{ request('ver_titulos') }}">
                                    <input type="hidden" name="orden" value="{{ request('orden', 'asc') }}">
                                    <input type="hidden" name="autor" value="{{ is_array(request('autor')) ? implode(',', request('autor')) : request('autor') }}">
                                    <input type="hidden" name="campus" value="{{ is_array(request('campus')) ? implode(',', request('campus')) : request('campus') }}">
                                    <input type="hidden" name="materia" value="{{ is_array(request('materia')) ? implode(',', request('materia')) : request('materia') }}">
                                    <input type="hidden" name="serie" value="{{ is_array(request('serie')) ? implode(',', request('serie')) : request('serie') }}">

                                    <div class="filter-options-container" id="options-editorial">
                                        @foreach ($editoriales as $editorial)
                                            <div class="filter-option" data-value="{{ strtolower($editorial) }}">
                                                <input type="checkbox" name="editorial[]" id="editorial_{{ $loop->index }}"
                                                       value="{{ $editorial }}" {{ is_array(request('editorial')) && in_array($editorial, request('editorial')) ? 'checked' : '' }}
                                                       class="form-checkbox rounded">
                                                <label for="editorial_{{ $loop->index }}" class="ml-2 text-gray-700 cursor-pointer flex-1">
                                                    {{ $editorial }}
                                                </label>
                                            </div>
                                        @endforeach
                                        <div class="no-results-message" id="no-results-editorial" style="display: none;">
                                            No se encontraron editoriales con ese criterio.
                                        </div>
                                    </div>
                                    <div class="pt-2 border-t border-gray-200">
                                        <button type="submit" class="filter-button w-full">
                                            <i class="fas fa-check mr-2"></i>Aplicar Filtro
                                        </button>
                                        @if(request()->filled('editorial'))
                                            <a href="{{ route('busqueda.sp', array_merge(request()->except('editorial'), ['termino' => request('termino'), 'tipo' => request('tipo'), 'ver_titulos' => request('ver_titulos')])) }}"
                                               class="remove-filter w-full text-center block mt-2">
                                                <i class="fas fa-times mr-2"></i>Quitar Filtro
                                            </a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(count($materias) > 0)
                    <!-- Filtrar por Materia -->
                    <div class="collapsible-filter {{ request()->filled('materia') ? 'has-active-filter expanded' : '' }}">
                        <div class="collapsible-header">
                            <h2>
                                <i class="fas fa-book-open mr-2"></i>Filtrar por Materia
                                <span class="filter-count">({{ count($materias) }} opciones)</span>
                                @if(request()->filled('materia'))
                                    <span class="ml-2 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                        {{ count((array) request('materia')) }} activo(s)
                                    </span>
                                @endif
                            </h2>
                            <i class="fas fa-chevron-down collapsible-toggle"></i>
                        </div>
                        <div class="collapsible-content">
                            <div class="filter-search-container">
                                <input type="text" 
                                       class="filter-search-input" 
                                       placeholder="Buscar materia..." 
                                       id="search-materia"
                                       onkeyup="filterOptions('materia', this.value)">
                            </div>
                            <div class="collapsible-inner">
                                <form method="GET" action="{{ route('busqueda.sp') }}" class="space-y-3">
                                    <!-- Campos ocultos para mantener contexto de búsqueda -->
                                    <input type="hidden" name="busqueda" value="{{ request('busqueda') }}">
                                    <input type="hidden" name="tipo_busqueda" value="{{ request('tipo_busqueda') }}">
                                    <input type="hidden" name="termino" value="{{ request('termino') }}">
                                    <input type="hidden" name="tipo" value="{{ request('tipo') }}">
                                    <input type="hidden" name="ver_titulos" value="{{ request('ver_titulos') }}">
                                    <input type="hidden" name="orden" value="{{ request('orden', 'asc') }}">
                                    <input type="hidden" name="autor" value="{{ is_array(request('autor')) ? implode(',', request('autor')) : request('autor') }}">
                                    <input type="hidden" name="editorial" value="{{ is_array(request('editorial')) ? implode(',', request('editorial')) : request('editorial') }}">
                                    <input type="hidden" name="campus" value="{{ is_array(request('campus')) ? implode(',', request('campus')) : request('campus') }}">
                                    <input type="hidden" name="serie" value="{{ is_array(request('serie')) ? implode(',', request('serie')) : request('serie') }}">

                                    <div class="filter-options-container" id="options-materia">
                                        @foreach ($materias as $materia)
                                            <div class="filter-option" data-value="{{ strtolower($materia) }}">
                                                <input type="checkbox" name="materia[]" id="materia_{{ $loop->index }}"
                                                       value="{{ $materia }}" {{ is_array(request('materia')) && in_array($materia, request('materia')) ? 'checked' : '' }}
                                                       class="form-checkbox rounded">
                                                <label for="materia_{{ $loop->index }}" class="ml-2 text-gray-700 cursor-pointer flex-1">
                                                    {{ $materia }}
                                                </label>
                                            </div>
                                        @endforeach
                                        <div class="no-results-message" id="no-results-materia" style="display: none;">
                                            No se encontraron materias con ese criterio.
                                        </div>
                                    </div>
                                    <div class="pt-2 border-t border-gray-200">
                                        <button type="submit" class="filter-button w-full">
                                            <i class="fas fa-check mr-2"></i>Aplicar Filtro
                                        </button>
                                        @if(request()->filled('materia'))
                                            <a href="{{ route('busqueda.sp', array_merge(request()->except('materia'), ['termino' => request('termino'), 'tipo' => request('tipo'), 'ver_titulos' => request('ver_titulos')])) }}"
                                               class="remove-filter w-full text-center block mt-2">
                                                <i class="fas fa-times mr-2"></i>Quitar Filtro
                                            </a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(count($series) > 0)
                    <!-- Filtrar por Serie -->
                    <div class="collapsible-filter {{ request()->filled('serie') ? 'has-active-filter expanded' : '' }}">
                        <div class="collapsible-header">
                            <h2>
                                <i class="fas fa-list-ol mr-2"></i>Filtrar por Serie
                                <span class="filter-count">({{ count($series) }} opciones)</span>
                                @if(request()->filled('serie'))
                                    <span class="ml-2 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                        {{ count((array) request('serie')) }} activo(s)
                                    </span>
                                @endif
                            </h2>
                            <i class="fas fa-chevron-down collapsible-toggle"></i>
                        </div>
                        <div class="collapsible-content">
                            <div class="filter-search-container">
                                <input type="text" 
                                       class="filter-search-input" 
                                       placeholder="Buscar serie..." 
                                       id="search-serie"
                                       onkeyup="filterOptions('serie', this.value)">
                            </div>
                            <div class="collapsible-inner">
                                <form method="GET" action="{{ route('busqueda.sp') }}" class="space-y-3">
                                    <!-- Campos ocultos para mantener contexto de búsqueda -->
                                    <input type="hidden" name="busqueda" value="{{ request('busqueda') }}">
                                    <input type="hidden" name="tipo_busqueda" value="{{ request('tipo_busqueda') }}">
                                    <input type="hidden" name="termino" value="{{ request('termino') }}">
                                    <input type="hidden" name="tipo" value="{{ request('tipo') }}">
                                    <input type="hidden" name="ver_titulos" value="{{ request('ver_titulos') }}">
                                    <input type="hidden" name="orden" value="{{ request('orden', 'asc') }}">
                                    <input type="hidden" name="autor" value="{{ is_array(request('autor')) ? implode(',', request('autor')) : request('autor') }}">
                                    <input type="hidden" name="editorial" value="{{ is_array(request('editorial')) ? implode(',', request('editorial')) : request('editorial') }}">
                                    <input type="hidden" name="campus" value="{{ is_array(request('campus')) ? implode(',', request('campus')) : request('campus') }}">
                                    <input type="hidden" name="materia" value="{{ is_array(request('materia')) ? implode(',', request('materia')) : request('materia') }}">

                                    <div class="filter-options-container" id="options-serie">
                                        @foreach ($series as $serie)
                                            <div class="filter-option" data-value="{{ strtolower($serie) }}">
                                                <input type="checkbox" name="serie[]" id="serie_{{ $loop->index }}"
                                                       value="{{ $serie }}" {{ is_array(request('serie')) && in_array($serie, request('serie')) ? 'checked' : '' }}
                                                       class="form-checkbox rounded">
                                                <label for="serie_{{ $loop->index }}" class="ml-2 text-gray-700 cursor-pointer flex-1">
                                                    {{ $serie }}
                                                </label>
                                            </div>
                                        @endforeach
                                        <div class="no-results-message" id="no-results-serie" style="display: none;">
                                            No se encontraron series con ese criterio.
                                        </div>
                                    </div>
                                    <div class="pt-2 border-t border-gray-200">
                                        <button type="submit" class="filter-button w-full">
                                            <i class="fas fa-check mr-2"></i>Aplicar Filtro
                                        </button>
                                        @if(request()->filled('serie'))
                                            <a href="{{ route('busqueda.sp', array_merge(request()->except('serie'), ['termino' => request('termino'), 'tipo' => request('tipo'), 'ver_titulos' => request('ver_titulos')])) }}"
                                               class="remove-filter w-full text-center block mt-2">
                                                <i class="fas fa-times mr-2"></i>Quitar Filtro
                                            </a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(count($campuses) > 0)
                    <!-- Filtrar por Campus/Biblioteca -->
                    <div class="collapsible-filter {{ request()->filled('campus') ? 'has-active-filter expanded' : '' }}">
                        <div class="collapsible-header">
                            <h2>
                                <i class="fas fa-map-marker-alt mr-2"></i>Filtrar por Campus/Biblioteca
                                <span class="filter-count">({{ count($campuses) }} opciones)</span>
                                @if(request()->filled('campus'))
                                    <span class="ml-2 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                        {{ count((array) request('campus')) }} activo(s)
                                    </span>
                                @endif
                            </h2>
                            <i class="fas fa-chevron-down collapsible-toggle"></i>
                        </div>
                        <div class="collapsible-content">
                            <div class="filter-search-container">
                                <input type="text" 
                                       class="filter-search-input" 
                                       placeholder="Buscar campus..." 
                                       id="search-campus"
                                       onkeyup="filterOptions('campus', this.value)">
                            </div>
                            <div class="collapsible-inner">
                                <form method="GET" action="{{ route('busqueda.sp') }}" class="space-y-3">
                                    <!-- Campos ocultos para mantener contexto de búsqueda -->
                                    <input type="hidden" name="busqueda" value="{{ request('busqueda') }}">
                                    <input type="hidden" name="tipo_busqueda" value="{{ request('tipo_busqueda') }}">
                                    <input type="hidden" name="termino" value="{{ request('termino') }}">
                                    <input type="hidden" name="tipo" value="{{ request('tipo') }}">
                                    <input type="hidden" name="ver_titulos" value="{{ request('ver_titulos') }}">
                                    <input type="hidden" name="orden" value="{{ request('orden', 'asc') }}">
                                    <input type="hidden" name="autor" value="{{ is_array(request('autor')) ? implode(',', request('autor')) : request('autor') }}">
                                    <input type="hidden" name="editorial" value="{{ is_array(request('editorial')) ? implode(',', request('editorial')) : request('editorial') }}">
                                    <input type="hidden" name="materia" value="{{ is_array(request('materia')) ? implode(',', request('materia')) : request('materia') }}">
                                    <input type="hidden" name="serie" value="{{ is_array(request('serie')) ? implode(',', request('serie')) : request('serie') }}">

                                    <div class="filter-options-container" id="options-campus">
                                        @foreach ($campuses as $campus)
                                            <div class="filter-option" data-value="{{ strtolower($campus) }}">
                                                <input type="checkbox" name="campus[]" id="campus_{{ $loop->index }}"
                                                       value="{{ $campus }}" {{ is_array(request('campus')) && in_array($campus, request('campus')) ? 'checked' : '' }}
                                                       class="form-checkbox rounded">
                                                <label for="campus_{{ $loop->index }}" class="ml-2 text-gray-700 cursor-pointer flex-1">
                                                    {{ $campus }}
                                                </label>
                                            </div>
                                        @endforeach
                                        <div class="no-results-message" id="no-results-campus" style="display: none;">
                                            No se encontraron campus con ese criterio.
                                        </div>
                                    </div>
                                    <div class="pt-2 border-t border-gray-200">
                                        <button type="submit" class="filter-button w-full">
                                            <i class="fas fa-check mr-2"></i>Aplicar Filtro
                                        </button>
                                        @if(request()->filled('campus'))
                                            <a href="{{ route('busqueda.sp', array_merge(request()->except('campus'), ['termino' => request('termino'), 'tipo' => request('tipo'), 'ver_titulos' => request('ver_titulos')])) }}"
                                               class="remove-filter w-full text-center block mt-2">
                                                <i class="fas fa-times mr-2"></i>Quitar Filtro
                                            </a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>

                <!-- Resultados -->
                <div class="lg:w-3/4">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="mb-6 bg-blue-50 p-4 rounded-lg">
                            <form action="{{ route('busqueda.sp') }}" method="GET" class="flex items-center space-x-4">
                                <input type="hidden" name="termino" value="{{ request('termino') }}">
                                <input type="hidden" name="tipo" value="{{ request('tipo') }}">
                                <input type="hidden" name="ver_titulos" value="{{ request('ver_titulos') }}">
                                <input type="hidden" name="autor" value="{{ is_array(request('autor')) ? implode(',', request('autor')) : request('autor') }}">
                                <input type="hidden" name="editorial" value="{{ is_array(request('editorial')) ? implode(',', request('editorial')) : request('editorial') }}">
                                <input type="hidden" name="campus" value="{{ is_array(request('campus')) ? implode(',', request('campus')) : request('campus') }}">
                                <input type="hidden" name="materia" value="{{ is_array(request('materia')) ? implode(',', request('materia')) : request('materia') }}">
                                <input type="hidden" name="serie" value="{{ is_array(request('serie')) ? implode(',', request('serie')) : request('serie') }}">

                                <label for="orden" class="text-gray-700 font-semibold">
                                    <i class="fas fa-sort mr-2"></i>Ordenar:
                                </label>
                                <select name="orden" id="orden" class="rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="titulo_asc" {{ request('orden') == 'titulo_asc' ? 'selected' : '' }}>Título A-Z</option>
                                    <option value="titulo_desc" {{ request('orden') == 'titulo_desc' ? 'selected' : '' }}>Título Z-A</option>
                                    <option value="autor_asc" {{ request('orden') == 'autor_asc' ? 'selected' : '' }}>Autor A-Z</option>
                                    <option value="autor_desc" {{ request('orden') == 'autor_desc' ? 'selected' : '' }}>Autor Z-A</option>
                                    <option value="editorial_asc" {{ request('orden') == 'editorial_asc' ? 'selected' : '' }}>Editorial A-Z</option>
                                    <option value="editorial_desc" {{ request('orden') == 'editorial_desc' ? 'selected' : '' }}>Editorial Z-A</option>
                                    <option value="anio_desc" {{ request('orden') == 'anio_desc' ? 'selected' : '' }}>Año más reciente</option>
                                    <option value="anio_asc" {{ request('orden') == 'anio_asc' ? 'selected' : '' }}>Año más antiguo</option>
                                </select>
                                <button type="submit" class="filter-button">
                                    Aplicar
                                </button>
                            </form>
                        </div>

                        @if($resultados->isEmpty())
                            <div class="text-center py-8">
                                <i class="fas fa-search text-gray-400 text-5xl mb-4"></i>
                                <p class="text-gray-500 text-xl">No se encontraron resultados.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="table-header">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">
                                                <i class="fas fa-check mr-2"></i>Sel.
                                            </th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">
                                                <i class="fas fa-book mr-2"></i>Título
                                            </th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">
                                                <i class="fas fa-user mr-2"></i>Autor
                                            </th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">
                                                <i class="fas fa-building mr-2"></i>Editorial
                                            </th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">
                                                <i class="fas fa-book-open mr-2"></i>Materia
                                            </th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">
                                                <i class="fas fa-list-ol mr-2"></i>Serie
                                            </th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">
                                                <i class="fas fa-university mr-2"></i>Biblioteca
                                            </th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">
                                                <i class="fas fa-file-export mr-2"></i>Exportar
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($resultados as $resultado)
                                            <tr class="table-row">
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    @if(isset($resultado->nro_control) && is_numeric($resultado->nro_control))
                                                        <input type="checkbox" name="resource_checkbox" 
                                                               value="{{ $resultado->nro_control }}" 
                                                               class="resource-checkbox form-checkbox"
                                                               onchange="updateExportButton()">
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    <div class="flex items-center">
                                                        <i class="fas fa-book mr-2 text-blue-600"></i>
                                                        @if(isset($resultado->nro_control) && is_numeric($resultado->nro_control))
                                                            <a href="{{ route('detalle-material', ['numero' => $resultado->nro_control]) }}" 
                                                               class="text-blue-600 hover:text-blue-800 hover:underline titulo-enlace">
                                                                {{ $resultado->titulo ?? 'Sin título' }}
                                                            </a>
                                                        @else
                                                            <span class="text-gray-500">{{ $resultado->titulo ?? 'Sin título' }}</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900">{{ $resultado->nombre_autor ?? 'Sin autor' }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900">{{ $resultado->nombre_editorial ?? 'Sin editorial' }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900">{{ $resultado->nombre_materia ?? 'Sin materia' }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900">{{ $resultado->nombre_serie ?? 'Sin serie' }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900">{{ $resultado->biblioteca ?? 'Sin ubicación' }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    @if(isset($resultado->nro_control) && is_numeric($resultado->nro_control))
                                                        <a href="{{ route('export.ris', ['nroControl' => $resultado->nro_control]) }}" 
                                                           class="filter-button inline-flex items-center py-1 px-3 text-sm">
                                                            <i class="fas fa-file-export mr-2"></i>RIS
                                                        </a>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-6">
                                <div class="pagination-container">
                                    <!-- Información de paginación -->
                                    <div class="pagination-info">
                                        <p class="text-sm text-gray-700">
                                            <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                                            Mostrando 
                                            <span class="font-semibold">{{ $resultados->firstItem() ?: 0 }}</span> 
                                            a 
                                            <span class="font-semibold">{{ $resultados->lastItem() ?: 0 }}</span> 
                                            de 
                                            <span class="font-semibold">{{ $resultados->total() }}</span> 
                                            resultados
                                        </p>
                                    </div>
                                    
                                    <!-- Enlaces de paginación -->
                                    <div class="pagination-links">
                                        {{ $resultados->links() }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Funcionalidad de filtros colapsables
        document.addEventListener('DOMContentLoaded', function() {
            const collapsibleHeaders = document.querySelectorAll('.collapsible-header');
            
            collapsibleHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const filter = this.closest('.collapsible-filter');
                    filter.classList.toggle('expanded');
                });
            });
        });

        // Función para filtrar opciones en tiempo real
        function filterOptions(filterType, searchValue) {
            const options = document.querySelectorAll(`#options-${filterType} .filter-option`);
            const noResultsMessage = document.querySelector(`#no-results-${filterType}`);
            let hasVisibleOptions = false;

            options.forEach(option => {
                const text = option.textContent.toLowerCase();
                const matches = text.includes(searchValue.toLowerCase());
                option.style.display = matches ? 'flex' : 'none';
                if (matches) hasVisibleOptions = true;
            });

            noResultsMessage.style.display = hasVisibleOptions ? 'none' : 'block';
        }
    </script>
</body>
</html>
