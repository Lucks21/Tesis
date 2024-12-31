<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Títulos de {{ $autor }}</title>
</head>
<body>
    <div class="container mx-auto">
        <h1 class="text-2xl font-bold mb-4">Títulos de {{ $autor }}</h1>

        @if($titulos->isEmpty())
            <p class="text-red-500">No se encontraron títulos para este autor.</p>
        @else
            <ul class="list-disc pl-6">
                @foreach ($titulos as $titulo)
                    <li>{{ $titulo }}</li>
                @endforeach
            </ul>
        @endif
        <a href="{{ route('busqueda-avanzada') }}" 
        class="mt-4 inline-block text-blue-500 hover:underline">Volver al formulario</a>
    </div>
</body>
</html>
