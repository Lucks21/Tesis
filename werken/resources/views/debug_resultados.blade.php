<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>DEBUG - Resultados Simple</title>
</head>
<body>
    <h1>DEBUG - Vista de resultados</h1>
    
    <h2>Datos de entrada:</h2>
    <p><strong>criterio:</strong> {{ $criterio ?? 'NULL' }}</p>
    <p><strong>valorCriterio:</strong> {{ $valorCriterio ?? 'NULL' }}</p>
    <p><strong>titulo:</strong> {{ $titulo ?? 'NULL' }}</p>
    <p><strong>orden:</strong> {{ $orden ?? 'NULL' }}</p>
    
    <h2>Resultados:</h2>
    <p><strong>resultados existe:</strong> {{ isset($resultados) ? 'SÍ' : 'NO' }}</p>
    <p><strong>resultados count:</strong> {{ isset($resultados) ? $resultados->count() : 'N/A' }}</p>
    <p><strong>resultados isEmpty:</strong> {{ isset($resultados) ? ($resultados->isEmpty() ? 'SÍ' : 'NO') : 'N/A' }}</p>
    
    <h2>Filtros:</h2>
    <p><strong>autores count:</strong> {{ isset($autores) ? count($autores) : 'N/A' }}</p>
    <p><strong>editoriales count:</strong> {{ isset($editoriales) ? count($editoriales) : 'N/A' }}</p>
    <p><strong>materias count:</strong> {{ isset($materias) ? count($materias) : 'N/A' }}</p>
    <p><strong>series count:</strong> {{ isset($series) ? count($series) : 'N/A' }}</p>
    <p><strong>campuses count:</strong> {{ isset($campuses) ? count($campuses) : 'N/A' }}</p>
    
    @if(isset($resultados) && $resultados->count() > 0)
        <h2>Primeros 3 resultados:</h2>
        @foreach($resultados->take(3) as $index => $resultado)
            <div style="border: 1px solid #ccc; margin: 10px; padding: 10px;">
                <h3>Resultado {{ $index + 1 }}:</h3>
                <p><strong>nro_control:</strong> {{ $resultado->nro_control ?? 'NULL' }}</p>
                <p><strong>titulo:</strong> {{ $resultado->titulo ?? 'NULL' }}</p>
                <p><strong>nombre_autor:</strong> {{ $resultado->nombre_autor ?? 'NULL' }}</p>
                <p><strong>nombre_editorial:</strong> {{ $resultado->nombre_editorial ?? 'NULL' }}</p>
                <p><strong>biblioteca:</strong> {{ $resultado->biblioteca ?? 'NULL' }}</p>
                <hr>
                <strong>Objeto completo:</strong>
                <pre>{{ print_r($resultado, true) }}</pre>
            </div>
        @endforeach
    @else
        <p>No hay resultados para mostrar.</p>
    @endif
    
    <h2>Request parameters:</h2>
    <pre>{{ print_r(request()->all(), true) }}</pre>
    
    <a href="{{ route('busqueda') }}">Volver a búsqueda</a>
</body>
</html>
