<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Búsqueda - Sistema de Bibliotecas UBB</title>
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
        .form-checkbox {
            color: #003876;
            border-color: #003876;
        }
        .form-checkbox:checked {
            background-color: #003876;
        }
        .filter-button {
            background-color: #003876;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            transition: background-color 0.2s;
        }
        .filter-button:hover {
            background-color: #002b5c;
        }
        .remove-filter {
            background-color: #dc2626;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            transition: background-color 0.2s;
        }
        .remove-filter:hover {
            background-color: #b91c1c;
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

    <!-- Navegación principal -->    <nav class="bg-white shadow">
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
                <div class="lg:w-1/4 space-y-6">
                    <!-- Filtrar por Autor -->
                    <div class="filter-section p-4">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">
                            <i class="fas fa-user-edit mr-2"></i>Filtrar por Autor
                        </h2>
                        <form method="GET" action="{{ route('busqueda-avanzada-resultados') }}" class="space-y-3">
                            <input type="hidden" name="orden" value="{{ request('orden', 'asc') }}">
                            <input type="hidden" name="criterio" value="{{ request('criterio') }}">
                            <input type="hidden" name="valor_criterio" value="{{ request('valor_criterio') }}">
                            <input type="hidden" name="titulo" value="{{ request('titulo') }}">
                            <input type="hidden" name="editorial" value="{{ is_array(request('editorial')) ? implode(',', request('editorial')) : request('editorial') }}">
                            <input type="hidden" name="campus" value="{{ is_array(request('campus')) ? implode(',', request('campus')) : request('campus') }}">

                            @foreach ($autores as $autor)
                                <div class="flex items-center">
                                    <input type="checkbox" name="autor[]" id="autor_{{ $loop->index }}"
                                           value="{{ $autor }}" {{ is_array(request('autor')) && in_array($autor, request('autor')) ? 'checked' : '' }}
                                           class="form-checkbox rounded">
                                    <label for="autor_{{ $loop->index }}" class="ml-2 text-gray-700">{{ $autor }}</label>
                                </div>
                            @endforeach
                            <button type="submit" class="filter-button w-full mt-2">
                                <i class="fas fa-check mr-2"></i>Aplicar Filtro
                            </button>
                            @if(request()->filled('autor'))
                                <a href="{{ route('busqueda-avanzada-resultados', array_merge(request()->except('autor', 'page_autores'))) }}"
                                   class="remove-filter w-full text-center block mt-2">
                                    <i class="fas fa-times mr-2"></i>Quitar Filtro
                                </a>
                            @endif
                        </form>
                    </div>

                    <!-- Filtrar por Editorial -->
                    <div class="filter-section p-4">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">
                            <i class="fas fa-building mr-2"></i>Filtrar por Editorial
                        </h2>
                        <form method="GET" action="{{ route('busqueda-avanzada-resultados') }}" class="space-y-3">
                            <input type="hidden" name="orden" value="{{ request('orden', 'asc') }}">
                            <input type="hidden" name="criterio" value="{{ request('criterio') }}">
                            <input type="hidden" name="valor_criterio" value="{{ request('valor_criterio') }}">
                            <input type="hidden" name="titulo" value="{{ request('titulo') }}">
                            <input type="hidden" name="autor" value="{{ is_array(request('autor')) ? implode(',', request('autor')) : request('autor') }}">
                            <input type="hidden" name="campus" value="{{ is_array(request('campus')) ? implode(',', request('campus')) : request('campus') }}">

                            @foreach ($editoriales as $editorial)
                                <div class="flex items-center">
                                    <input type="checkbox" name="editorial[]" id="editorial_{{ $loop->index }}"
                                           value="{{ $editorial }}" {{ is_array(request('editorial')) && in_array($editorial, request('editorial')) ? 'checked' : '' }}
                                           class="form-checkbox rounded">
                                    <label for="editorial_{{ $loop->index }}" class="ml-2 text-gray-700">{{ $editorial }}</label>
                                </div>
                            @endforeach
                            <button type="submit" class="filter-button w-full mt-2">
                                <i class="fas fa-check mr-2"></i>Aplicar Filtro
                            </button>
                            @if(request()->filled('editorial'))
                                <a href="{{ route('busqueda-avanzada-resultados', array_merge(request()->except('editorial', 'page_editoriales'))) }}"
                                   class="remove-filter w-full text-center block mt-2">
                                    <i class="fas fa-times mr-2"></i>Quitar Filtro
                                </a>
                            @endif
                        </form>
                    </div>

                    <!-- Filtrar por Campus -->
                    <div class="filter-section p-4">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">
                            <i class="fas fa-university mr-2"></i>Filtrar por Campus
                        </h2>
                        <form method="GET" action="{{ route('busqueda-avanzada-resultados') }}" class="space-y-3">
                            <input type="hidden" name="orden" value="{{ request('orden', 'asc') }}">
                            <input type="hidden" name="criterio" value="{{ request('criterio') }}">
                            <input type="hidden" name="valor_criterio" value="{{ request('valor_criterio') }}">
                            <input type="hidden" name="titulo" value="{{ request('titulo') }}">
                            <input type="hidden" name="autor" value="{{ is_array(request('autor')) ? implode(',', request('autor')) : request('autor') }}">
                            <input type="hidden" name="editorial" value="{{ is_array(request('editorial')) ? implode(',', request('editorial')) : request('editorial') }}">

                            @foreach ($campuses as $campus)
                                <div class="flex items-center">
                                    <input type="checkbox" name="campus[]" id="campus_{{ $loop->index }}"
                                           value="{{ $campus }}" {{ is_array(request('campus')) && in_array($campus, request('campus')) ? 'checked' : '' }}
                                           class="form-checkbox rounded">
                                    <label for="campus_{{ $loop->index }}" class="ml-2 text-gray-700">{{ $campus }}</label>
                                </div>
                            @endforeach
                            <button type="submit" class="filter-button w-full mt-2">
                                <i class="fas fa-check mr-2"></i>Aplicar Filtro
                            </button>
                            @if(request()->filled('campus'))
                                <a href="{{ route('busqueda-avanzada-resultados', array_merge(request()->except('campus', 'page_campuses'))) }}"
                                   class="remove-filter w-full text-center block mt-2">
                                    <i class="fas fa-times mr-2"></i>Quitar Filtro
                                </a>
                            @endif
                        </form>
                    </div>
                </div>

                <!-- Resultados -->
                <div class="lg:w-3/4">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="mb-6 bg-blue-50 p-4 rounded-lg">
                            <form action="{{ route('busqueda-avanzada-resultados') }}" method="GET" class="flex items-center space-x-4">
                                <input type="hidden" name="criterio" value="{{ request('criterio') }}">
                                <input type="hidden" name="valor_criterio" value="{{ request('valor_criterio') }}">
                                <input type="hidden" name="titulo" value="{{ request('titulo') }}">
                                <input type="hidden" name="autor" value="{{ is_array(request('autor')) ? implode(',', request('autor')) : request('autor') }}">
                                <input type="hidden" name="editorial" value="{{ is_array(request('editorial')) ? implode(',', request('editorial')) : request('editorial') }}">
                                <input type="hidden" name="campus" value="{{ is_array(request('campus')) ? implode(',', request('campus')) : request('campus') }}">

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
                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="table-header">
                                        <tr>
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
                                                <td class="px-6 py-4 text-sm text-gray-900">{{ $resultado->titulo }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900">{{ $resultado->autor }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900">{{ $resultado->editorial }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900">{{ $resultado->biblioteca }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    <a href="{{ route('export.ris', ['nroControl' => $resultado->nro_control]) }}" 
                                                       class="filter-button inline-flex items-center py-1 px-3 text-sm">
                                                        <i class="fas fa-file-export mr-2"></i>RIS
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-6">
                                {{ $resultados->appends(request()->query())->links() }}
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
</body>
</html>
