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
        }

        /* Institutional bar */
        .institutional-bar {
            background-color: #003876;
            color: white;
            font-size: 0.875rem;
            padding: 0.5rem 0;
        }
        
        /* Main header */
        .main-header {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem 0;
            text-align: center;
        }

        .header-title h1 {
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: 900;
            color: #003876;
            margin: 0;
        }

        .header-title p {
            font-size: 0.875rem;
            color: #6b7280;
            margin: 0;
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
            color: white;
        }

        /* Results container */
        .results-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 56, 118, 0.1);
        }

        .results-container h1 {
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: 900;
        }

        /* Table styles */
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

        /* No results */
        .no-results {
            text-align: center;
            padding: 3rem;
            color: #6b7280;
        }
        
        .no-results i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #d1d5db;
        }
        
        .no-results h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: #374151;
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: bold;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-content {
                flex-direction: column;
                gap: 1rem;
            }
            
            .nav-links {
                display: none;
            }
            
            .search-actions {
                flex-direction: column;
                width: 100%;
                gap: 0.5rem;
            }
            
            .search-button, .filter-button {
                min-width: auto;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Barra institucional -->
    <div class="institutional-bar">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between py-2">
                <div class="institutional-logo">
                    <img src="{{ asset('img/logo-ubb-white.png') }}" alt="UBB" style="height: 40px;">
                </div>
                <div class="institutional-title">
                    <span>Sistema de Bibliotecas Universidad del Bío-Bío</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Encabezado principal -->
    <header class="main-header">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between py-6">
                <div class="header-logo">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('img/logo-biblioteca.png') }}" alt="Biblioteca UBB" style="height: 60px;">
                    </a>
                </div>
                <div class="header-title">
                    <h1>Sistema de Bibliotecas</h1>
                    <p>Universidad del Bío-Bío</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Navegación principal -->
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
    <main class="container mx-auto px-4 py-8">
        <div class="results-container p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">
                @if(isset($mostrarTitulos) && $mostrarTitulos)
                    @if(isset($valorSeleccionado))
                        Títulos relacionados con: "{{ $valorSeleccionado }}"
                    @else
                        Resultados de la Búsqueda Simple
                    @endif
                @else
                    Coincidencias encontradas
                @endif
            </h1>
            
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <p class="text-gray-700">
                    <i class="fas fa-search mr-2"></i>
                    Búsqueda por <strong>{{ ucfirst($criterio) }}</strong>: "{{ $busqueda }}"
                    @if(!$noResultados)
                        | {{ $resultados->total() }} resultado(s) encontrado(s)
                    @endif
                </p>
                
                @if(isset($mostrarTitulos) && !$mostrarTitulos && !$noResultados)
                    <div class="mt-3 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                        <p class="text-sm text-yellow-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            Seleccione una opción para ver los títulos relacionados
                        </p>
                    </div>
                @endif
            </div>

            @if($noResultados)
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h3>No se encontraron resultados</h3>
                    <p>Intente con otros términos de búsqueda o revise la ortografía.</p>
                    <a href="{{ route('busqueda') }}" class="search-button" style="margin-top: 1rem;">
                        <i class="fas fa-arrow-left mr-2"></i>Volver a buscar
                    </a>
                </div>
            @else
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="table-header">
                            <tr>
                                @if(isset($mostrarTitulos) && $mostrarTitulos)
                                    {{-- Encabezados para mostrar títulos (igual a búsqueda avanzada) --}}
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
                                @else
                                    {{-- Encabezados para mostrar criterios de búsqueda --}}
                                    @switch($criterio)
                                        @case('autor')
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">
                                                <i class="fas fa-user mr-2"></i>Autor
                                            </th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">
                                                <i class="fas fa-eye mr-2"></i>Acción
                                            </th>
                                            @break
                                        @case('materia')
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">
                                                <i class="fas fa-book-open mr-2"></i>Materia
                                            </th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">
                                                <i class="fas fa-eye mr-2"></i>Acción
                                            </th>
                                            @break
                                        @case('editorial')
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">
                                                <i class="fas fa-building mr-2"></i>Editorial
                                            </th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">
                                                <i class="fas fa-eye mr-2"></i>Acción
                                            </th>
                                            @break
                                        @case('serie')
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">
                                                <i class="fas fa-list-ol mr-2"></i>Serie
                                            </th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-white">
                                                <i class="fas fa-eye mr-2"></i>Acción
                                            </th>
                                            @break
                                        @case('titulo')
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
                                            @break
                                    @endswitch
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($resultados as $resultado)
                                <tr class="table-row">
                                    @if(isset($mostrarTitulos) && $mostrarTitulos)
                                        {{-- Mostrar títulos con el formato exacto de la búsqueda avanzada --}}
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <div class="flex items-center">
                                                <i class="fas fa-book mr-2 text-blue-600"></i>
                                                @if(isset($resultado->nro_control))
                                                    <a href="{{ route('detalle-material', ['numero' => $resultado->nro_control]) }}" 
                                                       class="text-blue-600 hover:text-blue-800 hover:underline titulo-enlace">
                                                        {{ $resultado->titulo ?? $resultado->nombre_busqueda ?? 'Sin título' }}
                                                    </a>
                                                @else
                                                    <span class="font-semibold">{{ $resultado->titulo ?? $resultado->nombre_busqueda ?? 'Sin título' }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $resultado->autor ?? 'Sin autor' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $resultado->editorial ?? 'Sin editorial' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $resultado->materia ?? 'Sin materia' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $resultado->serie ?? 'Sin serie' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $resultado->biblioteca ?? 'UBB' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            @if(isset($resultado->nro_control))
                                                <a href="{{ route('export.ris', ['nroControl' => $resultado->nro_control]) }}" 
                                                   class="filter-button inline-flex items-center py-1 px-3 text-sm">
                                                    <i class="fas fa-file-export mr-2"></i>RIS
                                                </a>
                                            @else
                                                <span class="text-gray-400 text-sm">N/A</span>
                                            @endif
                                        </td>
                                    @else
                                        {{-- Mostrar criterios de búsqueda --}}
                                        @switch($criterio)
                                            @case('autor')
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    <div class="flex items-center">
                                                        <i class="fas fa-user mr-2 text-blue-600"></i>
                                                        <strong>{{ $resultado->nombre_busqueda ?? 'Sin autor' }}</strong>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    <a href="{{ route('busqueda.titulos-relacionados', ['criterio' => 'autor', 'valor' => $resultado->nombre_busqueda]) }}" 
                                                       class="filter-button inline-flex items-center py-1 px-3 text-sm">
                                                        <i class="fas fa-books mr-2"></i>Ver Títulos
                                                    </a>
                                                </td>
                                                @break
                                            @case('materia')
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    <div class="flex items-center">
                                                        <i class="fas fa-book-open mr-2 text-green-600"></i>
                                                        <strong>{{ $resultado->nombre_busqueda ?? 'Sin materia' }}</strong>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    <a href="{{ route('busqueda.titulos-relacionados', ['criterio' => 'materia', 'valor' => $resultado->nombre_busqueda]) }}" 
                                                       class="filter-button inline-flex items-center py-1 px-3 text-sm">
                                                        <i class="fas fa-books mr-2"></i>Ver Títulos
                                                    </a>
                                                </td>
                                                @break
                                            @case('editorial')
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    <div class="flex items-center">
                                                        <i class="fas fa-building mr-2 text-orange-600"></i>
                                                        <strong>{{ $resultado->nombre_busqueda ?? 'Sin editorial' }}</strong>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    <a href="{{ route('busqueda.titulos-relacionados', ['criterio' => 'editorial', 'valor' => $resultado->nombre_busqueda]) }}" 
                                                       class="filter-button inline-flex items-center py-1 px-3 text-sm">
                                                        <i class="fas fa-books mr-2"></i>Ver Títulos
                                                    </a>
                                                </td>
                                                @break
                                            @case('serie')
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    <div class="flex items-center">
                                                        <i class="fas fa-list-ol mr-2 text-purple-600"></i>
                                                        <strong>{{ $resultado->nombre_busqueda ?? 'Sin serie' }}</strong>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    <a href="{{ route('busqueda.titulos-relacionados', ['criterio' => 'serie', 'valor' => $resultado->nombre_busqueda]) }}" 
                                                       class="filter-button inline-flex items-center py-1 px-3 text-sm">
                                                        <i class="fas fa-books mr-2"></i>Ver Títulos
                                                    </a>
                                                </td>
                                                @break
                                            @case('titulo')
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    <div class="flex items-center">
                                                        <i class="fas fa-book mr-2 text-blue-600"></i>
                                                        @if(isset($resultado->nro_control))
                                                            <a href="{{ route('detalle-material', ['numero' => $resultado->nro_control]) }}" 
                                                               class="text-blue-600 hover:text-blue-800 hover:underline titulo-enlace">
                                                                {{ $resultado->nombre_busqueda ?? 'Sin título' }}
                                                            </a>
                                                        @else
                                                            <strong>{{ $resultado->nombre_busqueda ?? 'Sin título' }}</strong>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900">{{ $resultado->autor ?? 'Sin autor' }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900">{{ $resultado->editorial ?? 'Sin editorial' }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900">{{ $resultado->materia ?? 'Sin materia' }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900">{{ $resultado->serie ?? 'Sin serie' }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900">{{ $resultado->biblioteca ?? 'UBB' }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    @if(isset($resultado->nro_control))
                                                        <a href="{{ route('export.ris', ['nroControl' => $resultado->nro_control]) }}" 
                                                           class="filter-button inline-flex items-center py-1 px-3 text-sm">
                                                            <i class="fas fa-file-export mr-2"></i>RIS
                                                        </a>
                                                    @else
                                                        <span class="text-gray-400 text-sm">N/A</span>
                                                    @endif
                                                </td>
                                                @break
                                        @endswitch
                                    @endif
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
                                @if(isset($mostrarTitulos) && $mostrarTitulos)
                                    | Página {{ $resultados->currentPage() }} de {{ $resultados->lastPage() }}
                                @endif
                            </p>
                        </div>
                        
                        <!-- Enlaces de paginación -->
                        <div class="flex items-center justify-center sm:justify-end">
                            {{ $resultados->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif

            <div class="text-center mt-8">
            </div>
        </div>
    </main>
</body>
</html>
