<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de la Búsqueda</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">Resultados de la Búsqueda</h1>

    <p>Resultados para "{{ request('criterio') }}" que contienen "{{ request('valor_criterio') }}" y título que contienen "{{ request('titulo') }}".</p>

    <!-- Contenedor principal -->
    <div class="flex">
        <!-- Filtro lateral -->
        <div class="w-1/4 bg-white shadow-md p-4 mr-4">
            <!-- Filtro por Autor -->
            <h2 class="text-xl font-bold mb-4">Filtrar por Autor</h2>
            <form method="GET" action="{{ route('busqueda-avanzada-resultados') }}">
                <!-- Mantener los parámetros de búsqueda -->
                <input type="hidden" name="orden" value="{{ request('orden', 'asc') }}">
                <input type="hidden" name="criterio" value="{{ request('criterio') }}">
                <input type="hidden" name="valor_criterio" value="{{ request('valor_criterio') }}">
                <input type="hidden" name="titulo" value="{{ request('titulo') }}">
                <input type="hidden" name="editorial" value="{{ request('editorial') }}">

                @foreach ($autores as $autor)
                    <div>
                        <label>
                            <input type="radio" name="autor" value="{{ $autor }}" 
                                   {{ request('autor') === $autor ? 'checked' : '' }}>
                            {{ $autor }}
                        </label>
                    </div>
                @endforeach

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md mt-4">Aplicar Filtro</button>
            </form>

            <hr class="my-4">

<!-- Filtro por Editorial -->
<div class="w-1/4 bg-white shadow-md p-4 mr-4">
    <h2 class="text-xl font-bold mb-4">Filtrar por Editorial</h2>
    <form method="GET" action="{{ route('busqueda-avanzada-resultados') }}">
        <!-- Mantener los parámetros de búsqueda -->
        <input type="hidden" name="orden" value="{{ request('orden', 'asc') }}">
        <input type="hidden" name="criterio" value="{{ request('criterio') }}">
        <input type="hidden" name="valor_criterio" value="{{ request('valor_criterio') }}">
        <input type="hidden" name="titulo" value="{{ request('titulo') }}">
        <input type="hidden" name="autor" value="{{ request('autor') }}">

        @foreach ($editoriales as $editorial)
            <div>
                <label>
                    <input type="radio" name="editorial" value="{{ $editorial }}" 
                           {{ request('editorial') === $editorial ? 'checked' : '' }}>
                    {{ $editorial }}
                </label>
            </div>
        @endforeach

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md mt-4">Aplicar Filtro</button>
    </form>
</div>
        <!-- Resultados -->
        <div class="w-3/4">
            <!-- Formulario para cambiar el orden de los resultados -->
            <form action="{{ route('busqueda-avanzada-resultados') }}" method="GET" class="mb-4">
                <input type="hidden" name="criterio" value="{{ request('criterio') }}">
                <input type="hidden" name="valor_criterio" value="{{ request('valor_criterio') }}">
                <input type="hidden" name="titulo" value="{{ request('titulo') }}">
                <input type="hidden" name="autor" value="{{ request('autor') }}">
                <input type="hidden" name="editorial" value="{{ request('editorial') }}">

                <label for="orden" class="font-bold">Ordenar:</label>
                <select name="orden" id="orden" class="border border-gray-300 rounded-md p-2">
                    <option value="asc" {{ request('orden') == 'asc' ? 'selected' : '' }}>Ascendente</option>
                    <option value="desc" {{ request('orden') == 'desc' ? 'selected' : '' }}>Descendente</option>
                </select>
                <button type="submit" class="ml-2 bg-blue-600 text-white px-4 py-2 rounded-md">Aplicar</button>
            </form>

            <!-- Mostrar resultados -->
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

                <!-- Controles de paginación -->
                <div class="mt-4">
                    {{ $resultados->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Enlace para volver al formulario -->
    <a href="{{ route('busqueda-avanzada') }}" class="mt-4 inline-block text-blue-500 hover:underline">Volver al formulario</a>
</div>
</body>
</html>
