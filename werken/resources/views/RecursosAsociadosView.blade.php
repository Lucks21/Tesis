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
        <form method="GET" id="form-busqueda" class="mb-6">
            <div class="mb-4">
                <label for="criterio" class="block text-sm font-medium text-gray-700">Seleccione un criterio:</label>
                <select id="criterio" name="criterio" class="form-select mt-1 block w-full" onchange="updateFormAction()">
                    <option value="autor" {{ request('criterio') === 'autor' ? 'selected' : '' }}>Autor</option>
                    <option value="editorial" {{ request('criterio') === 'editorial' ? 'selected' : '' }}>Editorial</option>
                    <option value="serie" {{ request('criterio') === 'serie' ? 'selected' : '' }}>Serie</option>
                    <option value="materia" {{ request('criterio') === 'materia' ? 'selected' : '' }}>Materia</option>
                    <option value="titulo" {{ request('criterio') === 'titulo' ? 'selected' : '' }}>Título</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="busqueda" class="block text-sm font-medium text-gray-700">Ingrese el término:</label>
                <input type="text" id="busqueda" name="busqueda" class="form-input mt-1 block w-full" value="{{ request('busqueda') }}" required>
            </div>

            <button type="submit" class="bg-blue-800 text-white px-4 py-2 rounded">Buscar</button>
        </form>

        <!-- Resultados -->
        <p>Resultados para el criterio "{{ ucfirst($criterio) }}": "{{ $valor }}"</p>

        @if($recursos->isEmpty())
            <p class="text-red-500">No se encontraron recursos asociados.</p>
        @else
            <ul class="list-decimal pl-5">
                @foreach($recursos as $recurso)
                    <li>
                        <span class="font-bold">
                            {{ (($recursos->currentPage() - 1) * $recursos->perPage()) + $loop->iteration }}.
                        </span>
                        {{ $recurso->nombre_busqueda }}
                    </li>
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

<script>
    function updateFormAction() {
        const criterio = document.getElementById('criterio').value;
        const form = document.getElementById('form-busqueda');

        if (criterio === 'titulo') {
            form.action = "{{ route('buscar.titulo') }}";
        } else {
            form.action = "{{ route('resultados') }}";
        }
    }
    document.addEventListener('DOMContentLoaded', updateFormAction);
</script>
