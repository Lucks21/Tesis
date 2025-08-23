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
        .search-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
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
            justify-content: center;
            transition: background-color 0.2s;
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: bold;
            min-width: 160px;
            text-align: center;
        }

        .search-button i {
            margin-right: 0.5rem;
        }

        .search-button:hover {
            background-color: #002b5c;
        }
        
        /* Main content styles */
        .results-container h1 {
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: 900;
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
            width: 51%;
            min-width: 200px;
        }

        .col-autor {
            width: 20%;
            min-width: 150px;
        }

        .col-editorial {
            width: 14%;
            min-width: 120px;
        }

        .col-materia {
            width: 12%;
            min-width: 120px;
        }

        .col-serie {
            width: 4%;
            min-width: 70px;
        }

        .col-dewey {
            width: 3%;
            min-width: 40px;
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
            background: #f8fafc;
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
            max-width: 100%;
            margin: 0 auto;
            padding: 0 0.5rem;
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
                padding: 0 1.5rem;
            }
        }

        @media (min-width: 1536px) {
            .results-main-container {
                padding: 0 2rem;
            }
        }
    </style>
    
    {{-- Incluir estilos específicos de filtros --}}
    @include('partials.filtros.filtros-estilos')
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

            <!-- Control de ordenación -->
            <div class="mb-6 bg-blue-50 p-4 rounded-lg">
                <form action="{{ route('busqueda-avanzada-resultados') }}" method="GET" class="flex items-center space-x-4">
                    <input type="hidden" name="criterio" value="{{ request('criterio') }}">
                    <input type="hidden" name="valor_criterio" value="{{ request('valor_criterio') }}">
                    <input type="hidden" name="titulo" value="{{ request('titulo') }}">
                    <input type="hidden" name="autor" value="{{ is_array(request('autor')) ? implode(',', array_filter(request('autor'), function($v) { return !empty(trim($v)); })) : request('autor') }}">
                    <input type="hidden" name="editorial" value="{{ is_array(request('editorial')) ? implode(',', array_filter(request('editorial'), function($v) { return !empty(trim($v)); })) : request('editorial') }}">
                    <input type="hidden" name="campus" value="{{ is_array(request('campus')) ? implode(',', array_filter(request('campus'), function($v) { return !empty(trim($v)); })) : request('campus') }}">
                    <input type="hidden" name="materia" value="{{ is_array(request('materia')) ? implode(',', array_filter(request('materia'), function($v) { return !empty(trim($v)); })) : request('materia') }}">
                    <input type="hidden" name="serie" value="{{ is_array(request('serie')) ? implode(',', array_filter(request('serie'), function($v) { return !empty(trim($v)); })) : request('serie') }}">

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

            <div class="flex flex-col lg:flex-row gap-3">
                {{-- Incluir filtros desde archivo separado --}}
                @include('partials.filtros.filtros-busqueda')

                <!-- Resultados -->
                <div class="results-main">
                    <div class="bg-white rounded-xl p-6">
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
                                            @if(is_object($resultado) && isset($resultado->nro_control))
                                            <tr class="table-row">
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    @if(is_numeric($resultado->nro_control ?? ''))
                                                        <input type="checkbox" name="resource_checkbox" 
                                                               value="{{ $resultado->nro_control }}" 
                                                               class="resource-checkbox"
                                                               onchange="updateExportButton()">
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900 titulo-cell">
                                                    <div class="flex items-center">
                                                        @if(is_numeric($resultado->nro_control ?? ''))
                                                            <a href="{{ route('detalle-material', ['numero' => $resultado->nro_control]) }}" 
                                                               class="text-blue-600 hover:text-blue-800 hover:underline titulo-enlace">
                                                                {{ $resultado->titulo ?? 'Sin título' }}
                                                            </a>
                                                        @else
                                                            <span class="text-gray-500">{{ $resultado->titulo ?? 'Sin título' }}</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900 autor-cell">{{ $resultado->autor ?? 'Sin autor' }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900 editorial-cell">{{ $resultado->editorial ?? 'Sin editorial' }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900 materia-cell">{{ $resultado->materia ?? 'Sin materia' }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900 serie-cell">{{ $resultado->serie ?? 'Sin serie' }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900 dewey-cell">
                                                    @if(isset($resultado->dewey) && !empty($resultado->dewey))
                                                        <span class="dewey-number text-gray-700" onclick="selectDeweyText(this)" title="Haz clic para copiar el número de Dewey al portapapeles">
                                                            {{ $resultado->dewey }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-400 italic">Sin clasificación</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900 biblioteca-cell">{{ $resultado->biblioteca ?? 'Sin ubicación' }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    @if(is_numeric($resultado->nro_control ?? ''))
                                                        <a href="{{ route('export.ris', ['nroControl' => $resultado->nro_control]) }}" 
                                                           class="ris-button">
                                                            <i class="fas fa-file-export mr-2"></i>RIS
                                                        </a>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endif
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

    {{-- Scripts de filtros --}}
    @include('partials.filtros.filtros-scripts')

    {{-- Scripts específicos de la vista --}}
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
    </script>
</body>
</html>