<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Búsqueda</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">

<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">Resultados de la Búsqueda</h1>

    <p>Resultados para {{ $criterio }} que contienen "{{ $valorCriterio }}".</p>

    <!-- Ordenar títulos -->
    <form action="{{ route('busqueda-avanzada-resultados') }}" method="GET" class="mb-4">
        <input type="hidden" name="criterio" value="{{ request('criterio') }}">
        <input type="hidden" name="valor_criterio" value="{{ request('valor_criterio') }}">
        <input type="hidden" name="titulo" value="{{ request('titulo') }}">

        <label for="ordenar" class="font-bold">Ordenar títulos:</label>
        <select name="ordenar" id="ordenar" class="ml-2 border rounded-md px-2 py-1">
            <option value="asc" {{ request('ordenar') === 'asc' ? 'selected' : '' }}>Ascendente</option>
            <option value="desc" {{ request('ordenar') === 'desc' ? 'selected' : '' }}>Descendente</option>
        </select>
        <button type="submit" class="ml-2 bg-blue-600 text-white px-4 py-2 rounded-md">Aplicar</button>
    </form>

    @if($resultados->isEmpty())
        <p class="text-red-500">No se encontraron resultados.</p>
    @else
        <p class="font-bold">Autores encontrados:</p>
        <ul>
            @foreach($resultados as $resultado)
                <li>
                    <a href="{{ route('mostrar-titulos-por-autor', ['autor' => $resultado->autor]) }}" class="text-blue-500 hover:underline">
                        {{ $resultado->autor }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    <a href="{{ route('busqueda-avanzada') }}" class="mt-4 inline-block text-blue-500 hover:underline">Volver al formulario</a>
</div>

</body>
</html>
