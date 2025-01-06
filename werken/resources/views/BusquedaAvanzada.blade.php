<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Búsqueda Avanzada</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <form action="{{ route('busqueda-avanzada-resultados') }}" method="GET" class="space-y-4">
    <div>
        <label for="criterio" class="block font-bold">Buscar por:</label>
        <select id="criterio" name="criterio" class="border-gray-300 rounded-md w-full p-2">
            <option value="autor" {{ request('criterio') == 'autor' ? 'selected' : '' }}>Autor</option>
            <option value="materia" {{ request('criterio') == 'materia' ? 'selected' : '' }}>Materia</option>
            <option value="serie" {{ request('criterio') == 'serie' ? 'selected' : '' }}>Serie</option>
            <option value="editorial" {{ request('criterio') == 'editorial' ? 'selected' : '' }}>Editorial</option>
        </select>
    </div>

    <div>
        <label for="valor_criterio" class="block font-bold">Nombre del criterio:</label>
        <input type="text" id="valor_criterio" name="valor_criterio" value="{{ request('valor_criterio') }}" class="border-gray-300 rounded-md w-full p-2">
    </div>

    <div>
        <label for="titulo" class="block font-bold">Título:</label>
        <input type="text" id="titulo" name="titulo" value="{{ request('titulo') }}" class="border-gray-300 rounded-md w-full p-2">
    </div>

    <button type="submit" class="bg-blue-600 text-white rounded-md px-4 py-2">Buscar</button>
</form>



        <a href="/" class="mt-4 text-blue-500 hover:underline block">Volver a la página principal</a>
    </div>

</body>
</html>
