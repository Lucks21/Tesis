<div id="busqueda-simple" class="bg-white p-6 rounded shadow-lg">
    <h2 class="text-2xl font-bold mb-4">Búsqueda Simple</h2>
    <form id="form-busqueda" method="GET" action="{{ route('resultados') }}">
        <div class="mb-4">
            <label for="criterio" class="block text-sm font-medium text-gray-700">Seleccione un criterio:</label>
            <select id="criterio" name="criterio" class="form-select mt-1 block w-full">
                <option value="autor">Autor</option>
                <option value="editorial">Editorial</option>
                <option value="serie">Serie</option>
                <option value="materia">Materia</option>
                <option value="titulo">Título</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="busqueda" class="block text-sm font-medium text-gray-700">Ingrese el término:</label>
            <input type="text" id="busqueda" name="busqueda" class="form-input mt-1 block w-full" required>
        </div>
        <button type="submit" class="bg-blue-800 text-white px-4 py-2 rounded">Buscar</button>
    </form>
</div>
