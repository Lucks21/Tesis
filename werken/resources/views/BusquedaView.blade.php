<div id="busqueda-simple" class="bg-white p-6 rounded shadow-lg">
    <h2 class="text-2xl font-bold mb-4">Búsqueda Simple</h2>
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
</div>

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