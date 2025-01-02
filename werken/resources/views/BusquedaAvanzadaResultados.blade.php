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

    <p>Resultados para {{ request('criterio') }} que contienen "{{ request('valor_criterio') }}" y título que contienen "{{ request('titulo') }}".</p>

    <form action="{{ route('busqueda-avanzada-resultados') }}" method="GET" class="mb-4">
        <input type="hidden" name="criterio" value="{{ request('criterio') }}">
        <input type="hidden" name="valor_criterio" value="{{ request('valor_criterio') }}">
        <input type="hidden" name="titulo" value="{{ request('titulo') }}">

        <!-- Ordenar resultados -->
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
        <p class="font-bold mb-2">Resultados encontrados:</p>
        <ul class="list-disc list-inside">
            @foreach($resultados as $resultado)
                @if(request('criterio') === 'autor')
                    <li>
                        <a href="{{ route('mostrar-titulos-por-autor', ['autor' => urlencode($resultado->autor), 'titulo' => request('titulo')]) }}"
                           class="text-blue-500 hover:underline">
                            {{ $resultado->autor }}
                        </a>
                    </li>
                @elseif(request('criterio') === 'editorial')
                    <li>
                        <a href="{{ route('mostrar-titulos-por-editorial', ['editorial' => urlencode($resultado->editorial), 'titulo' => request('titulo')]) }}"
                           class="text-blue-500 hover:underline">
                            {{ $resultado->editorial }}
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    @endif

    <a href="{{ route('busqueda-avanzada') }}" class="mt-4 inline-block text-blue-500 hover:underline">Volver al formulario</a>
</div>

</body>
</html>
