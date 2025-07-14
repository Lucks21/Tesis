<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>DEBUG - Resultados Simple</title>
</head>
<body>
    <h1>DEBUG - Vista Simplificada</h1>
    
    <p><strong>Termino:</strong> {{ $termino ?? 'NULL' }}</p>
    <p><strong>Tipo:</strong> {{ $tipo ?? 'NULL' }}</p>
    <p><strong>Titulos existe:</strong> {{ isset($titulos) ? 'SÍ' : 'NO' }}</p>
    <p><strong>Titulos count:</strong> {{ isset($titulos) ? $titulos->count() : 'N/A' }}</p>
    
    @if(isset($titulos) && $titulos->count() > 0)
        <h2>Títulos encontrados: {{ $titulos->count() }}</h2>
        @foreach($titulos as $titulo)
            <div>
                <h3>{{ $titulo->titulo ?? 'Sin título' }}</h3>
                <p>Autor: {{ $titulo->nombre_autor ?? 'N/A' }}</p>
            </div>
        @endforeach
    @else
        <p>No se encontraron títulos.</p>
    @endif
    
    <a href="{{ route('busqueda') }}">Volver a búsqueda</a>
</body>
</html>
