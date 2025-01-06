<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recursos Asociados</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Recursos Asociados</h1>

        <!-- Formulario de Búsqueda -->
        <form id="form-busqueda" method="GET" action="{{ route('resultados') }}" class="mb-6">
            <div class="mb-4">
                <label for="criterio" class="block text-sm font-medium text-gray-700">Seleccione un criterio:</label>
                <select id="criterio" name="criterio" class="form-select mt-1 block w-full">
                    <option value="autor" {{ $criterio === 'autor' ? 'selected' : '' }}>Autor</option>
                    <option value="editorial" {{ $criterio === 'editorial' ? 'selected' : '' }}>Editorial</option>
                    <option value="serie" {{ $criterio === 'serie' ? 'selected' : '' }}>Serie</option>
                    <option value="materia" {{ $criterio === 'materia' ? 'selected' : '' }}>Materia</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="busqueda" class="block text-sm font-medium text-gray-700">Ingrese el término:</label>
                <input type="text" id="busqueda" name="busqueda" class="form-input mt-1 block w-full" value="{{ old('busqueda', $valor) }}" required>
            </div>
            <button type="submit" class="bg-blue-800 text-white px-4 py-2 rounded">Buscar</button>
        </form>

        <!-- Resultados -->
        @if($recursos->isEmpty())
            <p class="text-red-500">No se encontraron recursos asociados.</p>
        @else
            <ul class="list-disc pl-5">
                @foreach($recursos as $recurso)
                    <li>{{ $recurso->nombre_busqueda }}</li>
                @endforeach
            </ul>

            <!-- Paginación -->
            <div class="mt-4">
                {{ $recursos->links() }}
            </div>
        @endif

        <div class="mt-4">
            <a href="{{ url()->previous() }}" class="text-blue-500 underline">Volver</a>
        </div>
    </div>
</body>
</html>
