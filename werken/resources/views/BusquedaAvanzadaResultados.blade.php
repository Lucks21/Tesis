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

    <p>Resultados para "{{ $criterio }}" que contienen "{{ $valorCriterio }}".</p>
    
    <!-- Filtros para orden -->
    <div class="mb-4">
        <form action="{{ route('busqueda-avanzada-resultados') }}" method="GET" class="inline-block">
            <input type="hidden" name="criterio" value="{{ request('criterio') }}">
            <input type="hidden" name="valor_criterio" value="{{ request('valor_criterio') }}">
            <input type="hidden" name="titulo" value="{{ request('titulo') }}">
            <input type="hidden" name="orden" value="asc">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">
                Ordenar Ascendente
            </button>
        </form>

        <form action="{{ route('busqueda-avanzada-resultados') }}" method="GET" class="inline-block">
            <input type="hidden" name="criterio" value="{{ request('criterio') }}">
            <input type="hidden" name="valor_criterio" value="{{ request('valor_criterio') }}">
            <input type="hidden" name="titulo" value="{{ request('titulo') }}">
            <input type="hidden" name="orden" value="desc">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">
                Ordenar Descendente
            </button>
        </form>
    </div>

    @if($resultados->isEmpty())
        <p class="text-red-500">No se encontraron resultados.</p>
    @else
        <table class="table-auto w-full bg-white rounded shadow-lg">
            <thead>
                <tr class="bg-blue-800 text-white">
                    <th class="px-4 py-2">Resultado</th>
                    <th class="px-4 py-2">Autor</th>
                    <th class="px-4 py-2">Editorial</th>
                    <th class="px-4 py-2">Año de Publicación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resultados as $resultado)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $resultado->Titulo }}</td>
                        <td class="px-4 py-2">{{ $resultado->Autor }}</td>
                        <td class="px-4 py-2">{{ $resultado->Editorial }}</td>
                        <td class="px-4 py-2">{{ $resultado->Año_Publicacion }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Paginación -->
        <div class="mt-6 flex justify-center">
            {{ $resultados->appends([
                'criterio' => request('criterio'),
                'valor_criterio' => request('valor_criterio'),
                'titulo' => request('titulo'),
                'orden' => request('orden'),
            ])->links() }}
        </div>
    @endif

    <a href="{{ route('busqueda-avanzada') }}" class="mt-4 inline-block text-blue-500 hover:underline">Volver al formulario</a>
</div>

</body>
</html>
