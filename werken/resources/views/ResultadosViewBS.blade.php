<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de la Búsqueda</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Resultados de la Búsqueda</h1>
        <p>Resultados para "{{ $busqueda }}" en "{{ ucfirst($criterio) }}"</p>

        @if($resultados->isEmpty())
            <p class="text-red-500">No se encontraron resultados.</p>
        @else
            <ul class="list-disc pl-5">
                @foreach($resultados as $resultado)
                    <li>
                        <strong>{{ $resultado['nombre'] }}</strong>
                        <ul class="pl-5">
                            @foreach($resultado['titulos'] as $titulo)
                                <li>{{ $titulo }}</li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>

            <!-- Navegación de Paginación -->
            <div class="mt-4">
                {{ $resultados->links() }}
            </div>
        @endif


        <div class="mt-4">
            <a href="/" class="text-blue-500 underline">Volver a la página principal</a>
        </div>
    </div>
</body>
</html>
