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
            background-color: #f8f9fa;
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
            margin: 0 1rem;
        }
        .institutional-bar a:hover {
            text-decoration: underline;
        }

        /* Main header */
        .main-header {
            background-color: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem 0;
        }
        .logos-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo-group {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .direccion-wrapper {
            display: flex;
            align-items: center;
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

        /* Results container */
        .results-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 2rem 0;
        }

        /* Table styles */
        .results-table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-header {
            background: #003876;
        }

        .table-header th {
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: bold;
            font-family: 'Tipo-UBB', sans-serif;
        }

        .results-table tbody tr {
            border-bottom: 1px solid #e5e7eb;
            transition: background-color 0.2s;
        }

        .results-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .results-table td {
            padding: 1rem;
            font-size: 0.875rem;
            color: #374151;
        }

        .titulo-enlace {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }

        .titulo-enlace:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        .tipo-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        .tipo-monografia {
            background: #dcfce7;
            color: #166534;
        }

        .tipo-seriada {
            background: #dbeafe;
            color: #1e40af;
        }

        .tipo-articulo {
            background: #fef3c7;
            color: #92400e;
        }

        .action-btn {
            background: #003876;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            margin-right: 0.5rem;
            transition: background-color 0.2s;
        }

        .action-btn:hover {
            background: #002b5c;
        }

        /* Pagination */
        .pagination-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 2rem;
        }

        @media (min-width: 640px) {
            .pagination-container {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
        }

        .pagination-info {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .pagination-info .font-semibold {
            color: #003876;
        }

        /* No results */
        .no-results {
            text-align: center;
            padding: 4rem 2rem;
            color: #6b7280;
        }

        .no-results i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #d1d5db;
        }

        .no-results h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #374151;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .mx-auto {
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

        .text-3xl {
            font-size: 1.875rem;
            line-height: 2.25rem;
        }

        .font-bold {
            font-weight: 700;
        }

        .text-gray-800 {
            color: #1f2937;
        }

        .mb-6 {
            margin-bottom: 1.5rem;
        }

        .bg-blue-50 {
            background-color: #eff6ff;
        }

        .rounded-lg {
            border-radius: 0.5rem;
        }

        .p-4 {
            padding: 1rem;
        }

        .p-6 {
            padding: 1.5rem;
        }

        .text-gray-700 {
            color: #374151;
        }

        .mr-2 {
            margin-right: 0.5rem;
        }

        .flex {
            display: flex;
        }

        .justify-center {
            justify-content: center;
        }

        .items-center {
            align-items: center;
        }
    </style>
</head>
<body>
    <!-- Barra institucional -->
    <div class="institutional-bar">
        <div class="container mx-auto px-4">
            <div class="flex justify-center">
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
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Resultados de la Búsqueda Simple</h1>
            
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <p class="text-gray-700">
                    <i class="fas fa-search mr-2"></i>
                    Búsqueda por <strong>{{ ucfirst($criterio) }}</strong>: "{{ $busqueda }}"
                    @if(!$noResultados)
                        | {{ $resultados->total() }} resultado(s) encontrado(s)
                    @endif
                </p>
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
                <div class="results-table-container">
                    <table class="results-table">
                        <thead class="table-header">
                            <tr>
                                @switch($criterio)
                                    @case('autor')
                                        <th>
                                            <i class="fas fa-user mr-2"></i>Autor
                                        </th>
                                        <th>
                                            <i class="fas fa-info mr-2"></i>Información Adicional
                                        </th>
                                        @break
                                    @case('materia')
                                        <th>
                                            <i class="fas fa-book-open mr-2"></i>Materia
                                        </th>
                                        <th>
                                            <i class="fas fa-info mr-2"></i>Información Adicional
                                        </th>
                                        @break
                                    @case('titulo')
                                        <th>
                                            <i class="fas fa-book mr-2"></i>Título
                                        </th>
                                        <th>
                                            <i class="fas fa-user mr-2"></i>Autor
                                        </th>
                                        <th>
                                            <i class="fas fa-building mr-2"></i>Publicación
                                        </th>
                                        <th>
                                            <i class="fas fa-tag mr-2"></i>Tipo
                                        </th>
                                        @break
                                    @case('editorial')
                                        <th>
                                            <i class="fas fa-building mr-2"></i>Editorial
                                        </th>
                                        <th>
                                            <i class="fas fa-info mr-2"></i>Información Adicional
                                        </th>
                                        @break
                                    @case('serie')
                                        <th>
                                            <i class="fas fa-list-ol mr-2"></i>Serie
                                        </th>
                                        <th>
                                            <i class="fas fa-info mr-2"></i>Información Adicional
                                        </th>
                                        @break
                                    @default
                                        <th>
                                            <i class="fas fa-book mr-2"></i>Resultado
                                        </th>
                                        <th>
                                            <i class="fas fa-info mr-2"></i>Información
                                        </th>
                                @endswitch
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($resultados as $resultado)
                                <tr>
                                    @switch($criterio)
                                        @case('autor')
                                            <td>
                                                <div class="flex items-center">
                                                    <i class="fas fa-user mr-2 text-blue-600"></i>
                                                    {{ $resultado->nombre_busqueda ?? 'Sin autor' }}
                                                </div>
                                            </td>
                                            <td>
                                                @if(isset($resultado->nro_control))
                                                    <small class="text-gray-500">Nº Control: {{ $resultado->nro_control }}</small><br>
                                                @endif
                                                @if(isset($resultado->autor))
                                                    <strong>Autor:</strong> {{ $resultado->autor }}<br>
                                                @endif
                                                @if(isset($resultado->publicacion))
                                                    <strong>Publicación:</strong> {{ $resultado->publicacion }}
                                                @endif
                                            </td>
                                            @break
                                        @case('materia')
                                            <td>
                                                <div class="flex items-center">
                                                    <i class="fas fa-book-open mr-2 text-green-600"></i>
                                                    {{ $resultado->nombre_busqueda ?? 'Sin materia' }}
                                                </div>
                                            </td>
                                            <td>
                                                @if(isset($resultado->nro_control))
                                                    <small class="text-gray-500">Nº Control: {{ $resultado->nro_control }}</small><br>
                                                @endif
                                                @if(isset($resultado->autor))
                                                    <strong>Autor:</strong> {{ $resultado->autor }}<br>
                                                @endif
                                                @if(isset($resultado->publicacion))
                                                    <strong>Publicación:</strong> {{ $resultado->publicacion }}
                                                @endif
                                            </td>
                                            @break
                                        @case('titulo')
                                            <td>
                                                <div class="flex items-center">
                                                    <i class="fas fa-book mr-2 text-blue-600"></i>
                                                    @if(isset($resultado->nro_control))
                                                        <a href="{{ route('detalle-material', ['numero' => $resultado->nro_control]) }}" 
                                                           class="titulo-enlace">
                                                            {{ $resultado->nombre_busqueda ?? 'Sin título' }}
                                                        </a>
                                                    @else
                                                        {{ $resultado->nombre_busqueda ?? 'Sin título' }}
                                                    @endif
                                                </div>
                                                @if(isset($resultado->nro_control))
                                                    <small class="text-gray-500">Nº Control: {{ $resultado->nro_control }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $resultado->autor ?? 'N/A' }}</td>
                                            <td>{{ $resultado->publicacion ?? 'N/A' }}</td>
                                            <td>
                                                @if(isset($resultado->tipo))
                                                    @php
                                                        $tipo = strtolower($resultado->tipo);
                                                    @endphp
                                                    @switch($tipo)
                                                        @case('am')
                                                        @case('m')
                                                            <span class="tipo-badge tipo-monografia">Monografía</span>
                                                            @break
                                                        @case('s')
                                                            <span class="tipo-badge tipo-seriada">Seriada</span>
                                                            @break
                                                        @case('b')
                                                        @case('a')
                                                            <span class="tipo-badge tipo-articulo">Artículo</span>
                                                            @break
                                                        @default
                                                            <span class="tipo-badge">{{ ucfirst($resultado->tipo) }}</span>
                                                    @endswitch
                                                @else
                                                    <span class="tipo-badge">N/A</span>
                                                @endif
                                            </td>
                                            @break
                                        @case('editorial')
                                            <td>
                                                <div class="flex items-center">
                                                    <i class="fas fa-building mr-2 text-orange-600"></i>
                                                    {{ $resultado->nombre_busqueda ?? 'Sin editorial' }}
                                                </div>
                                            </td>
                                            <td>
                                                @if(isset($resultado->nro_control))
                                                    <small class="text-gray-500">Nº Control: {{ $resultado->nro_control }}</small><br>
                                                @endif
                                                @if(isset($resultado->autor))
                                                    <strong>Autor:</strong> {{ $resultado->autor }}<br>
                                                @endif
                                                @if(isset($resultado->publicacion))
                                                    <strong>Publicación:</strong> {{ $resultado->publicacion }}
                                                @endif
                                            </td>
                                            @break
                                        @case('serie')
                                            <td>
                                                <div class="flex items-center">
                                                    <i class="fas fa-list-ol mr-2 text-purple-600"></i>
                                                    {{ $resultado->nombre_busqueda ?? 'Sin serie' }}
                                                </div>
                                            </td>
                                            <td>
                                                @if(isset($resultado->nro_control))
                                                    <small class="text-gray-500">Nº Control: {{ $resultado->nro_control }}</small><br>
                                                @endif
                                                @if(isset($resultado->autor))
                                                    <strong>Autor:</strong> {{ $resultado->autor }}<br>
                                                @endif
                                                @if(isset($resultado->publicacion))
                                                    <strong>Publicación:</strong> {{ $resultado->publicacion }}
                                                @endif
                                            </td>
                                            @break
                                        @default
                                            <td>
                                                <div class="flex items-center">
                                                    <i class="fas fa-book mr-2 text-blue-600"></i>
                                                    {{ $resultado->nombre_busqueda ?? 'Sin información' }}
                                                </div>
                                            </td>
                                            <td>
                                                @if(isset($resultado->nro_control))
                                                    <small class="text-gray-500">Nº Control: {{ $resultado->nro_control }}</small><br>
                                                @endif
                                                Información adicional disponible
                                            </td>
                                    @endswitch
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pagination-container">
                    <div class="pagination-info">
                        <p class="text-sm text-gray-700">
                            <i class="fas fa-info-circle mr-2"></i>
                            Mostrando 
                            <span class="font-semibold">{{ $resultados->firstItem() ?: 0 }}</span> 
                            a 
                            <span class="font-semibold">{{ $resultados->lastItem() ?: 0 }}</span> 
                            de 
                            <span class="font-semibold">{{ $resultados->total() }}</span> 
                            resultados
                        </p>
                    </div>
                    <div>
                        {{ $resultados->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </main>

    @if($errors->any())
        <script>
            alert('Error: {{ $errors->first() }}');
        </script>
    @endif
</body>
</html>
