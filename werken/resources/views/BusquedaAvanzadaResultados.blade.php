<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de la Búsqueda</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        .quitar-filtro {
            background-color: #dc2626;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            text-align: center;
            display: block;
            width: 100%;
            margin-top: 0.5rem;
            text-decoration: none;
        }
        .quitar-filtro:hover {
            background-color: #b91c1c;
        }
    </style>
</head>
<body class="bg-gray-100 p-6">
<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">Resultados de la Búsqueda</h1>
    <p>Resultados para "{{ request('criterio') }}" que contienen "{{ request('valor_criterio') }}" y título que contienen "{{ request('titulo') }}".</p>

    <div class="flex">
        <!-- Filtro lateral -->
        <div class="w-1/4 bg-white shadow-md p-4 mr-4">
            <!-- Filtrar por Autor -->
            <h2 class="text-xl font-bold mb-4">Filtrar por Autor</h2>
            <form method="GET" action="{{ route('busqueda-avanzada-resultados') }}">
                <input type="hidden" name="orden" value="{{ request('orden', 'asc') }}">
                <input type="hidden" name="criterio" value="{{ request('criterio') }}">
                <input type="hidden" name="valor_criterio" value="{{ request('valor_criterio') }}">
                <input type="hidden" name="titulo" value="{{ request('titulo') }}">
                <input type="hidden" name="editorial" value="{{ request('editorial') }}">
                <input type="hidden" name="campus" value="{{ request('campus') }}">

                @foreach ($autores as $autor)
                    <div>
                        <label>
                            <input type="radio" name="autor" value="{{ $autor }}" {{ request('autor') === $autor ? 'checked' : '' }}>
                            {{ $autor }}
                        </label>
                    </div>
                @endforeach

                <div class="flex flex-col gap-2 mt-4">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md w-full">Aplicar Filtro</button>
                    @if(request()->filled('autor'))
                        <a href="{{ route('busqueda-avanzada-resultados', array_merge(request()->except('autor', 'page_autores'))) }}" class="quitar-filtro">
                            Quitar Filtro
                        </a>
                    @endif
                </div>
            </form>
            <div class="mt-2">
                {{ $autores->appends(request()->except('page_autores'))->links() }}
            </div>

            <!-- Filtrar por Editorial -->
            <h2 class="text-xl font-bold mb-4 mt-6">Filtrar por Editorial</h2>
            <form method="GET" action="{{ route('busqueda-avanzada-resultados') }}">
                <input type="hidden" name="orden" value="{{ request('orden', 'asc') }}">
                <input type="hidden" name="criterio" value="{{ request('criterio') }}">
                <input type="hidden" name="valor_criterio" value="{{ request('valor_criterio') }}">
                <input type="hidden" name="titulo" value="{{ request('titulo') }}">
                <input type="hidden" name="autor" value="{{ request('autor') }}">
                <input type="hidden" name="campus" value="{{ request('campus') }}">

                @foreach ($editoriales as $editorial)
                    <div>
                        <label>
                            <input type="radio" name="editorial" value="{{ $editorial }}" {{ request('editorial') === $editorial ? 'checked' : '' }}>
                            {{ $editorial }}
                        </label>
                    </div>
                @endforeach

                <div class="flex flex-col gap-2 mt-4">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md w-full">Aplicar Filtro</button>
                    @if(request()->filled('editorial'))
                        <a href="{{ route('busqueda-avanzada-resultados', array_merge(request()->except('editorial', 'page_editoriales'))) }}" class="quitar-filtro">
                            Quitar Filtro
                        </a>
                    @endif
                </div>
            </form>
            <div class="mt-2">
                {{ $editoriales->appends(request()->except('page_editoriales'))->links() }}
            </div>

            <!-- Filtrar por Campus -->
            <h2 class="text-xl font-bold mb-4 mt-6">Filtrar por Campus</h2>
            <form method="GET" action="{{ route('busqueda-avanzada-resultados') }}">
                <input type="hidden" name="orden" value="{{ request('orden', 'asc') }}">
                <input type="hidden" name="criterio" value="{{ request('criterio') }}">
                <input type="hidden" name="valor_criterio" value="{{ request('valor_criterio') }}">
                <input type="hidden" name="titulo" value="{{ request('titulo') }}">
                <input type="hidden" name="autor" value="{{ request('autor') }}">
                <input type="hidden" name="editorial" value="{{ request('editorial') }}">

                @foreach ($campuses as $campus)
                    <div>
                        <label>
                            <input type="radio" name="campus" value="{{ $campus }}" {{ request('campus') === $campus ? 'checked' : '' }}>
                            {{ $campus }}
                        </label>
                    </div>
                @endforeach

                <div class="flex flex-col gap-2 mt-4">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md w-full">Aplicar Filtro</button>
                    @if(request()->filled('campus'))
                        <a href="{{ route('busqueda-avanzada-resultados', array_merge(request()->except('campus', 'page_campuses'))) }}" class="quitar-filtro">
                            Quitar Filtro
                        </a>
                    @endif
                </div>
            </form>
            <div class="mt-2">
                {{ $campuses->appends(request()->except('page_campuses'))->links() }}
            </div>
        </div>

        <!-- Resultados -->
        <div class="w-3/4">
            <form action="{{ route('busqueda-avanzada-resultados') }}" method="GET" class="mb-4">
                <input type="hidden" name="criterio" value="{{ request('criterio') }}">
                <input type="hidden" name="valor_criterio" value="{{ request('valor_criterio') }}">
                <input type="hidden" name="titulo" value="{{ request('titulo') }}">
                <input type="hidden" name="autor" value="{{ request('autor') }}">
                <input type="hidden" name="editorial" value="{{ request('editorial') }}">
                <input type="hidden" name="campus" value="{{ request('campus') }}">

                <label for="orden" class="font-bold">Ordenar:</label>
                <select name="orden" id="orden" class="border border-gray-300 rounded-md p-2">
                    <option value="asc" {{ request('orden') == 'asc' ? 'selected' : '' }}>Ascendente</option>
                    <option value="desc" {{ request('orden') == 'desc' ? 'selected' : '' }}>Descendente</option>
                </select>
                <button type="submit" class="ml-2 bg-blue-600 text-white px-4 py-2 rounded-md">Aplicar</button>
            </form>

            @if($resultados->isEmpty())
                <p class="text-red-500">No se encontraron resultados.</p>
            @else
                <table class="table-auto w-full border-collapse border border-gray-400">
                    <thead>
                        <tr>
                            <th class="border border-gray-400 px-4 py-2">Título</th>
                            <th class="border border-gray-400 px-4 py-2">Autor</th>
                            <th class="border border-gray-400 px-4 py-2">Editorial</th>
                            <th class="border border-gray-400 px-4 py-2">Biblioteca</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($resultados as $resultado)
                            <tr>
                                <td class="border border-gray-400 px-4 py-2">{{ $resultado->titulo }}</td>
                                <td class="border border-gray-400 px-4 py-2">{{ $resultado->autor }}</td>
                                <td class="border border-gray-400 px-4 py-2">{{ $resultado->editorial }}</td>
                                <td class="border border-gray-400 px-4 py-2">{{ $resultado->biblioteca }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $resultados->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    <a href="{{ route('busqueda-avanzada') }}" class="mt-4 inline-block text-blue-500 hover:underline">Volver al formulario</a>
</div>
</body>
</html>
