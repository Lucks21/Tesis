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

        /* Responsive column widths for better space distribution */
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

        .col-checkbox {
            width: 2%;
            min-width: 55px;
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
            background-color: #f0f7ff;
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

        /* Enhanced pagination area */
        .pagination-enhanced {
            margin: 2rem 0;
            padding: 1.5rem;
            background: #f8fafc;
            border-radius: 0.75rem;
            border: 1px solid #cbd5e1;
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

        /* Custom container styles for better space utilization */
        .results-main-container {
            max-width: 100%;
            margin: 0 auto;
            padding: 0 0.5rem;
        }

        .results-main {
            flex: 1;
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

        @media (min-width: 1024px) {
            .results-main-container {
                padding: 0 1rem;
            }
        }

        @media (min-width: 1280px) {
            .results-main-container {
                padding: 0 1.5rem;
            }
        }

        @media (min-width: 1536px) {
            .results-main-container {
                padding: 0 2rem;
            }
        }

        /* Filtros sidebar */
        .filters-sidebar {
            width: 100%;
            max-width: 300px;
            min-width: 280px;
        }

        @media (min-width: 1024px) {
            .filters-sidebar {
                width: 280px;
                flex-shrink: 0;
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
    
    {{-- Incluir estilos específicos de filtros --}}
    @include('partials.filtros.filtros-estilos')
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
    <main class="results-main-container py-8">
        <div class="results-container p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Resultados de la Búsqueda Simple</h1>
            
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <p class="text-gray-700">
                    <i class="fas fa-filter mr-2"></i>
                    Resultados para "{{ $criterio }}" que contienen "{{ $valorCriterio }}"
                </p>
            </div>

            <div class="flex flex-col lg:flex-row gap-3">
                {{-- Incluir filtros unificados desde archivo separado --}}
                @include('partials.filtros.filtros-busqueda-unificado')

                <!-- Resultados -->
                <div class="results-main">
                    <div class="bg-white rounded-xl p-6">
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
                                    <option value="asc" {{ request('orden') == 'asc' || request('orden') == 'titulo_asc' || request('orden') == '' ? 'selected' : '' }}>Ascendente</option>
                                    <option value="desc" {{ request('orden') == 'desc' || request('orden') == 'titulo_desc' ? 'selected' : '' }}>Descendente</option>
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
                                                    @if(isset($resultado->nro_control) && is_numeric($resultado->nro_control))
                                                        <input type="checkbox" name="resource_checkbox" 
                                                               value="{{ $resultado->nro_control }}" 
                                                               class="resource-checkbox"
                                                               onchange="updateExportButton()">
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900 titulo-cell">
                                                    @if(isset($resultado->nro_control) && is_numeric($resultado->nro_control))
                                                        <a href="{{ route('detalle-material', ['numero' => $resultado->nro_control]) }}" 
                                                           class="text-blue-600 hover:text-blue-800 hover:underline titulo-enlace">
                                                            {{ $resultado->titulo ?? 'Sin título' }}
                                                        </a>
                                                    @else
                                                        <span class="text-gray-500">{{ $resultado->titulo ?? 'Sin título' }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900 autor-cell">
                                                    {{ $resultado->nombre_autor ?? 'Sin autor' }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900 editorial-cell">
                                                    {{ $resultado->nombre_editorial ?? 'Sin editorial' }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900 materia-cell">
                                                    {{ $resultado->nombre_materia ?? 'Sin materia' }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900 serie-cell">
                                                    {{ $resultado->nombre_serie ?? 'Sin serie' }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900 biblioteca-cell">
                                                    {{ $resultado->biblioteca ?? 'Sin ubicación' }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    @if(isset($resultado->nro_control) && is_numeric($resultado->nro_control))
                                                        <a href="{{ route('export.ris', ['nroControl' => $resultado->nro_control]) }}" 
                                                           class="ris-button">
                                                            <i class="fas fa-file-export"></i>RIS
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
                                            <i class="fas fa-filter mr-1"></i>
                                            Resultados filtrados y paginados para mejor rendimiento
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
                <a href="{{ route('busqueda') }}" class="filter-button inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Volver al formulario de búsqueda
                </a>
            </div>
        </div>
    </main>

    {{-- Scripts de filtros unificados --}}
    @include('partials.filtros.filtros-scripts')

    <script>
        // Script adicional para funcionalidades específicas si es necesario
    </script>
</body>
</html>