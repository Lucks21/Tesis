<div id="busqueda-simple" class="bg-white p-6 rounded shadow-lg">
    <h2 class="text-2xl font-bold mb-4">Búsqueda Simple</h2>
    <form id="form-busqueda" method="GET">
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
        <button type="button" id="buscar-btn" class="bg-blue-800 text-white px-4 py-2 rounded">Buscar</button>
    </form>

    <div id="resultados" class="mt-6">
        <h3 class="text-xl font-bold">Resultados:</h3>
        <div id="resultados-container" class="mt-4 bg-gray-50 p-4 rounded shadow"></div>
    </div>
</div>

<script>
    let currentPage = 1;

    async function buscar() {
        const criterio = document.getElementById('criterio').value;
        const busqueda = document.getElementById('busqueda').value;

        const url = `/buscar?criterio=${criterio}&busqueda=${encodeURIComponent(busqueda)}&page=${currentPage}`;

        try {
            console.log('Enviando solicitud a:', url);
            const response = await fetch(url);
            const json = await response.json();

            console.log('Datos recibidos del backend:', json);

            const resultadosContainer = document.getElementById('resultados-container');
            resultadosContainer.innerHTML = '';

            const data = json.data || json;

            if (!data.length) {
                resultadosContainer.innerHTML = `<p class="text-red-500">No se encontraron resultados.</p>`;
                return;
            }

            data.forEach(item => {
                const autor = item.nombre;
                const titulos = item.titulos.map(titulo => `<li>${titulo}</li>`).join('');

                resultadosContainer.innerHTML += `
                    <div class="mb-4 p-4 border rounded bg-white shadow">
                        <h4 class="font-bold">${autor}</h4>
                        <ul>${titulos}</ul>
                    </div>
                `;
            });

            if (json.current_page || json.last_page) {
                const pagination = `
                    <div class="mt-4 flex justify-center space-x-2">
                        ${json.current_page > 1 ? `<button onclick="changePage(${currentPage - 1})" class="bg-gray-300 px-3 py-1 rounded">Anterior</button>` : ''}
                        ${json.current_page < json.last_page ? `<button onclick="changePage(${currentPage + 1})" class="bg-gray-300 px-3 py-1 rounded">Siguiente</button>` : ''}
                    </div>
                `;
                resultadosContainer.innerHTML += pagination;
            }
        } catch (error) {
            console.error('Error en el frontend:', error);
            const resultadosContainer = document.getElementById('resultados-container');
            resultadosContainer.innerHTML = `<p class="text-red-500">Ocurrió un error al procesar la solicitud.</p>`;
        }
    }

    function changePage(page) {
        currentPage = parseInt(page);
        buscar();
    }

    document.getElementById('buscar-btn').addEventListener('click', () => {
        currentPage = 1;
        buscar();
    });

</script>
