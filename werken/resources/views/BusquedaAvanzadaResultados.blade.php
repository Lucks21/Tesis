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

    <p>Resultados para autor que contienen "{{ request('valor_criterio') }}" y título que contienen "{{ request('titulo') }}".</p>

    <form action="{{ route('busqueda-avanzada-resultados') }}" method="GET" class="mb-4">
        <input type="hidden" name="criterio" value="{{ request('criterio') }}">
        <input type="hidden" name="valor_criterio" value="{{ request('valor_criterio') }}">
        <input type="hidden" name="titulo" value="{{ request('titulo') }}">

        <!-- Ordenar títulos -->
        <label for="orden" class="font-bold">Ordenar títulos:</label>
        <select name="orden" id="orden" class="border border-gray-300 rounded-md p-2">
            <option value="asc" {{ request('orden') == 'asc' ? 'selected' : '' }}>Ascendente</option>
            <option value="desc" {{ request('orden') == 'desc' ? 'selected' : '' }}>Descendente</option>
        </select>
        <button type="submit" class="ml-2 bg-blue-600 text-white px-4 py-2 rounded-md">Aplicar</button>
    </form>

    @if($autores->isEmpty())
        <p class="text-red-500">No se encontraron resultados.</p>
    @else
        <p class="font-bold mb-2">Autores encontrados:</p>
        <ul class="list-disc list-inside">
            @foreach($autores as $autor)
                <li>
                    <a href="{{ route('mostrar-titulos-por-autor', ['autor' => urlencode($autor->autor), 'titulo' => request('titulo')]) }}"
                       class="text-blue-500 hover:underline">
                        {{ $autor->autor }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    <a href="{{ route('busqueda-avanzada') }}" class="mt-4 inline-block text-blue-500 hover:underline">Volver al formulario</a>
</div>

</body>
</html>
