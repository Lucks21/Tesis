<!DOCTYPE html>
<html lang="es">
<head>    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Búsqueda - Sistema de Bibliotecas UBB</title>
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
            padding: 1rem;
            transition: color 0.2s;
        }
        .nav-link:hover {
            color: #003876;
        }
        .results-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 56, 118, 0.1);
        }
        .filter-section {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            border: 1px solid rgba(0, 56, 118, 0.1);
            border-radius: 0.5rem;
        }
        .table-header {
            background: #003876;
            color: white;
        }
        .table-row:hover {
            background-color: #f0f7ff;
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
        }        .search-actions {
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
            transition: background-color 0.2s;
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: bold;
        }
        .remove-filter:hover {
            background-color: #b91c1c;
        }
        
        /* Main content styles */
        .results-container h1 {
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: 900;
        }
        
        .form-checkbox {
            accent-color: #003876;
        }
        
        .filter-section h2 {
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: bold;
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

        /* Pagination styles */
        .pagination-info {
            background-color: #f8fafc;
            border-radius: 0.5rem;
            padding: 1rem;
            border: 1px solid #e2e8f0;
            min-width: 280px;
        }

        .pagination-info p {
            margin: 0;
            line-height: 1.5;
        }

        .pagination-info .font-semibold {
            color: #003876;
        }

        .pagination-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        @media (min-width: 640px) {
            .pagination-container {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
        }

        /* Collapsible filter styles */
        .collapsible-filter {
            border: 1px solid rgba(0, 56, 118, 0.1);
            border-radius: 0.5rem;
            overflow: hidden;
            background: linear-gradient(145deg, #ffffff, #f8fafc);
        }

        .collapsible-header {
            background: #003876;
            color: white;
            padding: 1rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.2s;
            user-select: none;
        }

        .collapsible-header:hover {
            background: #002b5c;
        }

        .collapsible-header h2 {
            margin: 0;
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: bold;
            font-size: 1.125rem;
        }

        .collapsible-toggle {
            transition: transform 0.3s ease;
            font-size: 1.2rem;
        }

        .collapsible-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            background: white;
        }

        .collapsible-inner {
            padding: 1rem;
        }

        /* When expanded */
        .collapsible-filter.expanded .collapsible-content {
            max-height: 500px;
        }

        .collapsible-filter.expanded .collapsible-toggle {
            transform: rotate(180deg);
        }

        /* Active filter indicator */
        .collapsible-filter.has-active-filter .collapsible-header {
            background: #1d4ed8;
        }

        .collapsible-filter.has-active-filter .collapsible-header:hover {
            background: #1e40af;
        }

        /* Filter search styles */
        .filter-search-container {
            padding: 0.75rem;
            border-bottom: 1px solid #e5e7eb;
            background: #f8fafc;
        }

        .filter-search-input {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            transition: all 0.2s;
            background: white;
        }

        .filter-search-input:focus {
            outline: none;
            border-color: #003876;
            box-shadow: 0 0 0 2px rgba(0, 56, 118, 0.1);
        }

        .filter-options-container {
            max-height: 300px;
            overflow-y: auto;
            padding: 0.75rem;
        }

        .filter-options-container::-webkit-scrollbar {
            width: 6px;
        }

        .filter-options-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .filter-options-container::-webkit-scrollbar-thumb {
            background: #003876;
            border-radius: 3px;
        }

        .filter-options-container::-webkit-scrollbar-thumb:hover {
            background: #002b5c;
        }

        .filter-option {
            display: flex;
            align-items: center;
            padding: 0.375rem 0;
            margin-bottom: 0.25rem;
            transition: background-color 0.2s;
            border-radius: 0.25rem;
        }

        .filter-option:hover {
            background-color: #f3f4f6;
        }

        .filter-option.hidden {
            display: none;
        }

        .filter-count {
            font-size: 0.75rem;
            color: #6b7280;
            margin-left: 0.5rem;
        }

        .no-results-message {
            text-align: center;
            color: #6b7280;
            font-style: italic;
            padding: 1rem;
        }

        /* Enhanced results table styles for better space utilization */
        .results-table {
            width: 100%;
            font-size: 0.95rem;
        }

        .results-table th {
            padding: 1rem 1.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            font-size: 0.875rem;
        }

        .results-table td {
            padding: 1.25rem 1.5rem;
            vertical-align: top;
            line-height: 1.6;
        }

        .results-table tbody tr:hover {
            background-color: #ffffff;
        }

        /* Responsive column widths for better space distribution */
        .col-titulo {
            width: 39%;
            min-width: 200px;
        }

        .col-autor {
            width: 20%;
            min-width: 150px;
        }

        .col-editorial {
            width: 15%;
            min-width: 120px;
        }

        .col-materia {
            width: 12%;
            min-width: 120px;
        }

        .col-serie {
            width: 5%;
            min-width: 80px;
        }

        .col-dewey {
            width: 10%;
            min-width: 60px;
        }

        .col-biblioteca {
            width: 15%;
            min-width: 120px;
        }

        .col-exportar {
            width: 2%;
            min-width: 55px;
        }

        /* Better text wrapping and spacing */
        .results-table td {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .titulo-cell {
            font-weight: 600;
            color: #1f2937;
        }

        .autor-cell {
            color: #1f2937;
        }

        .editorial-cell {
            color: #1f2937;
            font-size: 0.9em;
        }

        .materia-cell {
            color: #1f2937;
            font-weight: 500;
        }

        .serie-cell {
            color: #1f2937;
        }

        .dewey-cell {
            font-weight: 600;
            color: #1f2937;
        }

        .dewey-number {
            cursor: pointer;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            transition: all 0.2s ease;
            user-select: text;
            position: relative;
        }

        .dewey-number:hover {
            text-decoration: underline;
            color: #003876;
        }

        .dewey-number:active {
            background-color: #f0f7ff;
            transform: scale(0.98);
        }

        .biblioteca-cell {
            color: #1f2937;
        }

        /* Enhanced pagination area */
        .pagination-enhanced {
            margin: 2rem 0;
            padding: 1.5rem;
            background: linear-gradient(145deg, #f8fafc, #e2e8f0);
            border-radius: 0.75rem;
            border: 1px solid #cbd5e1;
        }

        /* Compact RIS export button */
        .ris-button {
            background-color: #003876;
            color: white;
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s;
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: 600;
            font-size: 0.75rem;
            min-width: 45px;
            text-align: center;
        }

        .ris-button i {
            margin-right: 0.25rem;
            font-size: 0.7rem;
        }

        .ris-button:hover {
            background-color: #002b5c;
        }

        /* Custom container styles for better space utilization */
        .results-main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 0.5rem;
        }

        /* Export controls styles */
        .export-controls {
            background: linear-gradient(145deg, #f8fafc, #e2e8f0);
            border: 1px solid #cbd5e1;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .export-button {
            background-color: #059669;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: bold;
            min-width: 180px;
            text-align: center;
            opacity: 0.5;
            pointer-events: none;
        }

        .export-button.enabled {
            opacity: 1;
            pointer-events: auto;
        }

        .export-button:hover.enabled {
            background-color: #047857;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(5, 150, 105, 0.3);
        }

        .export-button i {
            margin-right: 0.5rem;
        }

        .select-all-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .select-all-checkbox {
            width: 1.25rem;
            height: 1.25rem;
            accent-color: #003876;
            cursor: pointer;
        }

        .select-all-label {
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
            user-select: none;
        }

        .selection-counter {
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: 600;
            color: #059669;
            background: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            border: 1px solid #d1d5db;
        }

        .resource-checkbox {
            width: 1.125rem;
            height: 1.125rem;
            accent-color: #003876;
            cursor: pointer;
        }

        .resource-checkbox:checked + td {
            background-color: #f0f7ff;
        }

        tr:has(.resource-checkbox:checked) {
            background-color: #f0f7ff;
            border-left: 3px solid #003876;
        }

        tr:has(.resource-checkbox:checked):hover {
            background-color: #e0f2fe;
        }

        .col-checkbox {
            width: 4%;
            min-width: 60px;
        }

        /* Adjust other column widths */
        .col-titulo {
            width: 35%;
            min-width: 200px;
        }

        .col-autor {
            width: 18%;
            min-width: 150px;
        }

        .col-editorial {
            width: 14%;
            min-width: 120px;
        }

        .col-materia {
            width: 11%;
            min-width: 120px;
        }

        .col-serie {
            width: 5%;
            min-width: 80px;
        }

        .col-dewey {
            width: 9%;
            min-width: 60px;
        }

        .col-biblioteca {
            width: 14%;
            min-width: 120px;
        }

        .col-exportar {
            width: 2%;
            min-width: 55px;
        }

        @media (min-width: 1024px) {
            .results-main-container {
                padding: 0 1rem;
            }
        }

        @media (min-width: 1280px) {
            .results-main-container {
                max-width: 1500px;
                padding: 0 1.5rem;
            }
        }

        @media (min-width: 1536px) {
            .results-main-container {
                max-width: 1600px;
                padding: 0 2rem;
            }
        }
    </style>
</head>
<body class="bg-gray-50">    <!-- Barra institucional -->
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
    </div>    <!-- Cabecera principal -->
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
    </header>    <!-- Navegación principal -->
    <nav class="nav-container">
        <div class="nav-content">
            <div class="nav-links">
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
    <main class="results-main-container py-8">
        <div class="results-container p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Resultados de la Búsqueda</h1>
            
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <p class="text-gray-700">
                    <i class="fas fa-filter mr-2"></i>
                    Resultados para "{{ request('criterio') }}" que contienen "{{ request('valor_criterio') }}"
                    @if(request('titulo')) y título que contiene "{{ request('titulo') }}" @endif
                </p>
            </div>

            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Filtros laterales -->
                <div class="lg:w-1/5 space-y-6">
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
                                <form method="GET" action="{{ route('busqueda-avanzada-resultados') }}" class="space-y-3">
                                    <input type="hidden" name="orden" value="{{ request('orden', 'asc') }}">
                                    <input type="hidden" name="criterio" value="{{ request('criterio') }}">
                                    <input type="hidden" name="valor_criterio" value="{{ request('valor_criterio') }}">
                                    <input type="hidden" name="titulo" value="{{ request('titulo') }}">
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
                                            <a href="{{ route('busqueda-avanzada-resultados', array_merge(request()->except('autor', 'page_autores'))) }}"
                                               class="remove-filter w-full text-center block mt-2">
                                                <i class="fas fa-times mr-2"></i>Quitar Filtro
                                            </a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

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
                                <form method="GET" action="{{ route('busqueda-avanzada-resultados') }}" class="space-y-3">
                                    <input type="hidden" name="orden" value="{{ request('orden', 'asc') }}">
                                    <input type="hidden" name="criterio" value="{{ request('criterio') }}">
                                    <input type="hidden" name="valor_criterio" value="{{ request('valor_criterio') }}">
                                    <input type="hidden" name="titulo" value="{{ request('titulo') }}">
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
                                            <a href="{{ route('busqueda-avanzada-resultados', array_merge(request()->except('editorial', 'page_editoriales'))) }}"
                                               class="remove-filter w-full text-center block mt-2">
                                                <i class="fas fa-times mr-2"></i>Quitar Filtro
                                            </a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Filtrar por Campus -->
                    <div class="collapsible-filter {{ request()->filled('campus') ? 'has-active-filter expanded' : '' }}">
                        <div class="collapsible-header">
                            <h2>
                                <i class="fas fa-university mr-2"></i>Filtrar por Campus
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
                                <form method="GET" action="{{ route('busqueda-avanzada-resultados') }}" class="space-y-3">
                                    <input type="hidden" name="orden" value="{{ request('orden', 'asc') }}">
                                    <input type="hidden" name="criterio" value="{{ request('criterio') }}">
                                    <input type="hidden" name="valor_criterio" value="{{ request('valor_criterio') }}">
                                    <input type="hidden" name="titulo" value="{{ request('titulo') }}">
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
                                            <a href="{{ route('busqueda-avanzada-resultados', array_merge(request()->except('campus', 'page_campuses'))) }}"
                                               class="remove-filter w-full text-center block mt-2">
                                                <i class="fas fa-times mr-2"></i>Quitar Filtro
                                            </a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

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
                                <form method="GET" action="{{ route('busqueda-avanzada-resultados') }}" class="space-y-3">
                                    <input type="hidden" name="orden" value="{{ request('orden', 'asc') }}">
                                    <input type="hidden" name="criterio" value="{{ request('criterio') }}">
                                    <input type="hidden" name="valor_criterio" value="{{ request('valor_criterio') }}">
                                    <input type="hidden" name="titulo" value="{{ request('titulo') }}">
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
                                            <a href="{{ route('busqueda-avanzada-resultados', array_merge(request()->except('materia', 'page_materias'))) }}"
                                               class="remove-filter w-full text-center block mt-2">
                                                <i class="fas fa-times mr-2"></i>Quitar Filtro
                                            </a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

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
                                <form method="GET" action="{{ route('busqueda-avanzada-resultados') }}" class="space-y-3">
                                    <input type="hidden" name="orden" value="{{ request('orden', 'asc') }}">
                                    <input type="hidden" name="criterio" value="{{ request('criterio') }}">
                                    <input type="hidden" name="valor_criterio" value="{{ request('valor_criterio') }}">
                                    <input type="hidden" name="titulo" value="{{ request('titulo') }}">
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
                                            <a href="{{ route('busqueda-avanzada-resultados', array_merge(request()->except('serie', 'page_series'))) }}"
                                               class="remove-filter w-full text-center block mt-2">
                                                <i class="fas fa-times mr-2"></i>Quitar Filtro
                                            </a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resultados -->
                <div class="lg:w-4/5">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="mb-6 bg-blue-50 p-4 rounded-lg">
                            <form action="{{ route('busqueda-avanzada-resultados') }}" method="GET" class="flex items-center space-x-4">
                                <input type="hidden" name="criterio" value="{{ request('criterio') }}">
                                <input type="hidden" name="valor_criterio" value="{{ request('valor_criterio') }}">
                                <input type="hidden" name="titulo" value="{{ request('titulo') }}">
                                <input type="hidden" name="autor" value="{{ is_array(request('autor')) ? implode(',', request('autor')) : request('autor') }}">
                                <input type="hidden" name="editorial" value="{{ is_array(request('editorial')) ? implode(',', request('editorial')) : request('editorial') }}">
                                <input type="hidden" name="campus" value="{{ is_array(request('campus')) ? implode(',', request('campus')) : request('campus') }}">
                                <input type="hidden" name="materia" value="{{ is_array(request('materia')) ? implode(',', request('materia')) : request('materia') }}">
                                <input type="hidden" name="serie" value="{{ is_array(request('serie')) ? implode(',', request('serie')) : request('serie') }}">

                                <label for="orden" class="text-gray-700 font-semibold">
                                    <i class="fas fa-sort mr-2"></i>Ordenar:
                                </label>
                                <select name="orden" id="orden" class="rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="asc" {{ request('orden') == 'asc' ? 'selected' : '' }}>Ascendente</option>
                                    <option value="desc" {{ request('orden') == 'desc' ? 'selected' : '' }}>Descendente</option>
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
                            <!-- Controles de exportación -->
                            <div class="export-controls">
                                <div class="select-all-container">
                                    <input type="checkbox" id="selectAll" class="select-all-checkbox">
                                    <label for="selectAll" class="select-all-label">
                                        <i class="fas fa-check-square mr-2"></i>Seleccionar todos
                                    </label>
                                </div>
                                
                                <div class="selection-counter">
                                    <i class="fas fa-list-check mr-2"></i>
                                    <span id="selectedCount">0</span> recursos seleccionados
                                </div>
                                
                                <form id="exportForm" method="POST" action="{{ route('export.ris.multiple') }}">
                                    @csrf
                                    <button type="submit" id="exportButton" class="export-button">
                                        <i class="fas fa-download"></i>Exportar seleccionados
                                    </button>
                                </form>
                            </div>

                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200 results-table">
                                    <thead class="table-header">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-white col-checkbox">
                                                <i class="fas fa-check mr-2"></i>Sel.
                                            </th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-white col-titulo">
                                                <i class="fas fa-book mr-2"></i>Título
                                            </th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-white col-autor">
                                                <i class="fas fa-user mr-2"></i>Autor
                                            </th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-white col-editorial">
                                                <i class="fas fa-building mr-2"></i>Editorial
                                            </th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-white col-materia">
                                                <i class="fas fa-book-open mr-2"></i>Materia
                                            </th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-white col-serie">
                                                <i class="fas fa-list-ol mr-2"></i>Serie
                                            </th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-white col-dewey">
                                                <i class="fas fa-sort-numeric-up mr-2"></i>Dewey
                                            </th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-white col-biblioteca">
                                                <i class="fas fa-university mr-2"></i>Biblioteca
                                            </th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-white col-exportar">
                                                <i class="fas fa-file-export mr-2"></i>Exportar
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($resultados as $resultado)
                                            <tr class="table-row">
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    <input type="checkbox" name="resource_checkbox" 
                                                           value="{{ $resultado->nro_control }}" 
                                                           class="resource-checkbox"
                                                           onchange="updateExportButton()">
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900 titulo-cell">
                                                    <div class="flex items-center">
                                                        <i class="fas fa-book mr-2 text-blue-600"></i>
                                                        <a href="{{ route('detalle-material', ['numero' => $resultado->nro_control]) }}" 
                                                           class="text-blue-600 hover:text-blue-800 hover:underline titulo-enlace">
                                                            {{ $resultado->titulo }}
                                                        </a>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900 autor-cell">{{ $resultado->autor }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900 editorial-cell">{{ $resultado->editorial }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900 materia-cell">{{ $resultado->materia }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900 serie-cell">{{ $resultado->serie }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900 dewey-cell">
                                                    @if($resultado->dewey)
                                                        <span class="dewey-number text-gray-700" onclick="selectDeweyText(this)" title="Haz clic para copiar el número de Dewey al portapapeles">
                                                            {{ $resultado->dewey }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-400 italic">Sin clasificación</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900 biblioteca-cell">{{ $resultado->biblioteca }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    <a href="{{ route('export.ris', ['nroControl' => $resultado->nro_control]) }}" 
                                                       class="ris-button">
                                                        <i class="fas fa-file-export mr-2"></i>RIS
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="pagination-enhanced">
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
                                        <p class="text-xs text-gray-500 mt-1">
                                            <i class="fas fa-file-alt mr-1 text-blue-500"></i>
                                            Página {{ $resultados->currentPage() }} de {{ $resultados->lastPage() }}
                                        </p>
                                    </div>
                                    
                                    <!-- Enlaces de paginación -->
                                    <div class="flex items-center justify-center sm:justify-end">
                                        {{ $resultados->appends(request()->query())->links() }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="text-center mt-8">
                <a href="{{ route('busqueda-avanzada') }}" class="filter-button inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Volver al formulario de búsqueda
                </a>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Debug para enlaces de título
            console.log('DOM loaded, looking for titulo-enlace elements...');
            const enlaces = document.querySelectorAll('.titulo-enlace');
            console.log('Found', enlaces.length, 'titulo-enlace elements');
            
            enlaces.forEach(function(enlace, index) {
                console.log('Setting up link', index, ':', enlace.href);
                enlace.addEventListener('click', function(e) {
                    console.log('Link clicked:', this.href);
                    console.log('Event:', e);
                    // Permitir navegación normal
                    return true;
                });
            });
            
            // Toggle collapsible filters
            document.querySelectorAll('.collapsible-header').forEach(header => {
                header.addEventListener('click', function() {
                    const filter = this.closest('.collapsible-filter');
                    filter.classList.toggle('expanded');
                });
            });
        });

        // Función para filtrar opciones dentro de cada filtro
        function filterOptions(filterType, searchValue) {
            const optionsContainer = document.getElementById(`options-${filterType}`);
            const filterOptions = optionsContainer.querySelectorAll('.filter-option');
            const noResultsMessage = document.getElementById(`no-results-${filterType}`);
            
            let visibleCount = 0;
            const normalizedSearch = searchValue.toLowerCase().trim();
            
            filterOptions.forEach(option => {
                const optionText = option.getAttribute('data-value');
                const shouldShow = optionText.includes(normalizedSearch);
                
                if (shouldShow) {
                    option.classList.remove('hidden');
                    visibleCount++;
                } else {
                    option.classList.add('hidden');
                }
            });
            
            // Mostrar/ocultar mensaje de "no resultados"
            if (visibleCount === 0 && normalizedSearch !== '') {
                noResultsMessage.style.display = 'block';
            } else {
                noResultsMessage.style.display = 'none';
            }
        }

        // Función para limpiar todas las búsquedas de filtros
        function clearAllFilterSearches() {
            const filterTypes = ['autor', 'editorial', 'campus', 'materia', 'serie'];
            
            filterTypes.forEach(filterType => {
                const searchInput = document.getElementById(`search-${filterType}`);
                if (searchInput) {
                    searchInput.value = '';
                    filterOptions(filterType, '');
                }
            });
        }

        // Función para mostrar estadísticas de filtros
        function showFilterStats() {
            const filterTypes = ['autor', 'editorial', 'campus', 'materia', 'serie'];
            let stats = 'Estadísticas de filtros:\n\n';
            
            filterTypes.forEach(filterType => {
                const optionsContainer = document.getElementById(`options-${filterType}`);
                if (optionsContainer) {
                    const totalOptions = optionsContainer.querySelectorAll('.filter-option').length;
                    const visibleOptions = optionsContainer.querySelectorAll('.filter-option:not(.hidden)').length;
                    const checkedOptions = optionsContainer.querySelectorAll('.filter-option input[type="checkbox"]:checked').length;
                    
                    stats += `${filterType.charAt(0).toUpperCase() + filterType.slice(1)}:\n`;
                    stats += `  - Total: ${totalOptions}\n`;
                    stats += `  - Visibles: ${visibleOptions}\n`;
                    stats += `  - Seleccionados: ${checkedOptions}\n\n`;
                }
            });
            
            alert(stats);
        }

        // Función para debug de filtros (temporal)
        function debugFiltros() {
            const params = new URLSearchParams(window.location.search);
            const debugUrl = '{{ route("debug-filtros-busqueda") }}?' + params.toString();
            
            fetch(debugUrl)
                .then(response => response.json())
                .then(data => {
                    console.log('Debug de filtros:', data);
                    alert('Debug de filtros enviado a la consola. Presiona F12 para ver los detalles.');
                })
                .catch(error => {
                    console.error('Error en debug:', error);
                    alert('Error al hacer debug de filtros');
                });
        }

        // Agregar botón de debug temporal (remover en producción)
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.search.includes('debug=true')) {
                const debugButton = document.createElement('button');
                debugButton.textContent = 'Debug Filtros';
                debugButton.className = 'fixed top-4 right-4 bg-red-500 text-white p-2 rounded z-50';
                debugButton.onclick = debugFiltros;
                document.body.appendChild(debugButton);
            }
        });

        // Función para mostrar u ocultar filtros en dispositivos móviles
        function toggleMobileFilters() {
            const filterSection = document.getElementById('mobile-filters');
            filterSection.classList.toggle('hidden');
         }

        // Agregar event listeners para mejorar la experiencia del usuario
        document.addEventListener('DOMContentLoaded', function() {
            // Enfocar automáticamente el campo de búsqueda cuando se abre un filtro
            document.querySelectorAll('.collapsible-header').forEach(header => {
                header.addEventListener('click', function() {
                    const filter = this.closest('.collapsible-filter');
                    setTimeout(() => {
                        if (filter.classList.contains('expanded')) {
                            const searchInput = filter.querySelector('.filter-search-input');
                            if (searchInput) {
                                searchInput.focus();
                            }
                        }
                    }, 300); // Esperar a que termine la animación de expansión
                });
            });

            // Permitir usar Enter para buscar
            document.querySelectorAll('.filter-search-input').forEach(input => {
                input.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        // El filtrado ya se aplica automáticamente con onkeyup
                    }
                });
            });

            // Mostrar contador de opciones visibles en tiempo real
            document.querySelectorAll('.filter-search-input').forEach(input => {
                input.addEventListener('input', function() {
                    const filterType = this.id.replace('search-', '');
                    const optionsContainer = document.getElementById(`options-${filterType}`);
                    const visibleCount = optionsContainer.querySelectorAll('.filter-option:not(.hidden)').length;
                    
                    // Actualizar el placeholder dinámicamente
                    const originalPlaceholder = this.getAttribute('placeholder');
                    if (this.value.trim() !== '') {
                        this.setAttribute('placeholder', `${visibleCount} resultado(s) encontrado(s)`);
                    } else {
                        this.setAttribute('placeholder', originalPlaceholder);
                    }
                });
            });
        });

        // Función para copiar el número de Dewey al portapapeles al hacer clic
        function selectDeweyText(element) {
            const deweyNumber = element.textContent.trim();
            
            // Intentar copiar usando la API moderna del portapapeles
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(deweyNumber).then(function() {
                    // Feedback visual de éxito
                    showCopyFeedback(element, '✓ Copiado');
                    console.log('Número de Dewey copiado al portapapeles:', deweyNumber);
                }).catch(function(err) {
                    // Si falla, usar el método de fallback
                    fallbackCopyText(element, deweyNumber);
                });
            } else {
                // Usar método de fallback para navegadores más antiguos o contextos no seguros
                fallbackCopyText(element, deweyNumber);
            }
        }

        // Función de fallback para copiar texto
        function fallbackCopyText(element, text) {
            // Crear un elemento temporal para la selección
            const tempInput = document.createElement('textarea');
            tempInput.style.position = 'absolute';
            tempInput.style.left = '-9999px';
            tempInput.style.top = '0';
            tempInput.value = text;
            document.body.appendChild(tempInput);
            
            // Seleccionar y copiar
            tempInput.select();
            tempInput.setSelectionRange(0, 99999); // Para móviles
            
            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    showCopyFeedback(element, '✓ Copiado');
                    console.log('Número de Dewey copiado al portapapeles (fallback):', text);
                } else {
                    showCopyFeedback(element, '✗ Error', true);
                    console.error('Error al copiar el número de Dewey');
                }
            } catch (err) {
                showCopyFeedback(element, '✗ Error', true);
                console.error('Error al copiar:', err);
            }
            
            // Limpiar el elemento temporal
            document.body.removeChild(tempInput);
        }

        // Función para mostrar feedback visual
        function showCopyFeedback(element, message, isError = false) {
            // Crear elemento de feedback más sutil
            const feedback = document.createElement('span');
            feedback.textContent = message;
            feedback.style.position = 'absolute';
            feedback.style.background = isError ? '#dc2626' : '#16a34a';
            feedback.style.color = 'white';
            feedback.style.padding = '3px 8px';
            feedback.style.borderRadius = '4px';
            feedback.style.fontSize = '0.75em';
            feedback.style.fontWeight = '500';
            feedback.style.marginLeft = '8px';
            feedback.style.zIndex = '1000';
            feedback.style.opacity = '0';
            feedback.style.transition = 'opacity 0.2s ease';
            feedback.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
            
            element.parentNode.style.position = 'relative';
            element.parentNode.appendChild(feedback);
            
            // Mostrar feedback con animación suave
            setTimeout(() => {
                feedback.style.opacity = '1';
            }, 10);
            
            // Ocultar feedback
            setTimeout(() => {
                feedback.style.opacity = '0';
                
                setTimeout(() => {
                    if (feedback.parentNode) {
                        feedback.parentNode.removeChild(feedback);
                    }
                }, 200);
            }, 1200);
        }

        // Funciones para manejo de exportación múltiple
        function updateExportButton() {
            const checkboxes = document.querySelectorAll('input[name="resource_checkbox"]:checked');
            const exportButton = document.getElementById('exportButton');
            const selectedCount = document.getElementById('selectedCount');
            const selectAllCheckbox = document.getElementById('selectAll');
            const totalCheckboxes = document.querySelectorAll('input[name="resource_checkbox"]');
            
            // Actualizar contador
            selectedCount.textContent = checkboxes.length;
            
            // Habilitar/deshabilitar botón de exportación
            if (checkboxes.length > 0) {
                exportButton.classList.add('enabled');
            } else {
                exportButton.classList.remove('enabled');
            }
            
            // Actualizar estado del checkbox "Seleccionar todos"
            if (checkboxes.length === totalCheckboxes.length) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else if (checkboxes.length > 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
        }

        function toggleSelectAll() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const resourceCheckboxes = document.querySelectorAll('input[name="resource_checkbox"]');
            
            resourceCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            
            updateExportButton();
        }

        function handleExportSubmit(event) {
            const checkboxes = document.querySelectorAll('input[name="resource_checkbox"]:checked');
            
            if (checkboxes.length === 0) {
                event.preventDefault();
                alert('Por favor, selecciona al menos un recurso para exportar.');
                return false;
            }
            
            // Agregar los números de control seleccionados al formulario
            const form = document.getElementById('exportForm');
            
            // Limpiar inputs anteriores
            form.querySelectorAll('input[name="nro_controles[]"]').forEach(input => {
                input.remove();
            });
            
            // Agregar nuevos inputs
            checkboxes.forEach(checkbox => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'nro_controles[]';
                hiddenInput.value = checkbox.value;
                form.appendChild(hiddenInput);
            });
            
            // Mostrar mensaje de carga
            const exportButton = document.getElementById('exportButton');
            const originalText = exportButton.innerHTML;
            exportButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generando archivos...';
            exportButton.disabled = true;
            
            // Restaurar botón después de un tiempo
            setTimeout(() => {
                exportButton.innerHTML = originalText;
                exportButton.disabled = false;
            }, 5000);
            
            return true;
        }

        // Event listeners para exportación múltiple
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const exportForm = document.getElementById('exportForm');
            
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', toggleSelectAll);
            }
            
            if (exportForm) {
                exportForm.addEventListener('submit', handleExportSubmit);
            }
            
            // Inicializar estado del botón
            updateExportButton();
        });
    </script>
</body>
</html>
