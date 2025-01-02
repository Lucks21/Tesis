<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Títulos por Materia</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">

<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">Títulos de la materia: {{ $materia }}</h1>

    @if(isset($titulo) && $titulo)
        <p>Mostrando resultados que contienen: <strong>{{ $titulo }}</strong></p>
    @endif

    @if($titulos->isEmpty())
        <p class="text-red-500">No se encontraron títulos para la materia con los filtros aplicados.</p>
    @else
        <ul class="list-disc list-inside">
            @foreach($titulos as $titulo)
                <li>{{ $titulo->DSM_TITULO }}</li>
            @endforeach
        </ul>
    @endif

    <a href="{{ route('busqueda-avanzada') }}" class="mt-4 inline-block text-blue-500 hover:underline">Volver al formulario</a>
</div>

</body>
</html>
