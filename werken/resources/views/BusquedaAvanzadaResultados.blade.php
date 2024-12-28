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

        @if($resultados->isEmpty())
            <p class="text-red-500">No se encontraron resultados para los criterios proporcionados.</p>
        @else
            <table class="table-auto w-full bg-white rounded shadow-lg">
                <thead>
                    <tr class="bg-blue-800 text-white">
                        <th class="px-4 py-2">Título</th>
                        <th class="px-4 py-2">Autor</th>
                        <th class="px-4 py-2">Editorial</th>
                        <th class="px-4 py-2">Fecha de Publicación</th>
                        <th class="px-4 py-2">ISBN/ISSN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resultados as $resultado)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $resultado->Titulo }}</td>
                            <td class="px-4 py-2">{{ $resultado->Autor }}</td>
                            <td class="px-4 py-2">{{ $resultado->Editorial ?? 'N/A' }}</td>
                            <td class="px-4 py-2">{{ $resultado->Fecha_Publicacion ?? 'N/A' }}</td>
                            <td class="px-4 py-2">{{ $resultado->ISBN_ISSN ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Enlaces de paginación -->
            <div class="mt-6 flex justify-center">
                {{ $resultados->links() }}
            </div>
        @endif

        <a href="{{ route('busqueda-avanzada') }}" class="mt-4 inline-block text-blue-500 hover:underline">Volver al formulario</a>
    </div>

</body>
</html>
