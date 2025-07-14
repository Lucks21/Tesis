<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Búsqueda Simple - Sistema de Bibliotecas UBB</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @font-face {
            font-family: 'Tipo-UBB';
            src: url('{{ asset('fonts/Tipo-UBB-Black_Condensed.otf') }}') format('opentype');
            font-weight: 900;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Tipo-UBB';
            src: url('{{ asset('fonts/Tipo-UBB-Bold_Condensed.otf') }}') format('opentype');
            font-weight: bold;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Tipo-UBB';
            src: url('{{ asset('fonts/Tipo-UBB-Regular_Condensed.otf') }}') format('opentype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Tipo-UBB';
            src: url('{{ asset('fonts/Tipo-UBB-Light_Condensed.otf') }}') format('opentype');
            font-weight: 300;
            font-style: normal;
            font-display: swap;
        }

        /* Base styles */
        body {
            margin: 0;
            font-family: 'Tipo-UBB', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Institutional bar */
        .institutional-bar {
            background-color: #003876;
            color: white;
            font-size: 0.875rem;
            padding: 0.5rem 0;
        }
        .institutional-bar a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
        }
        .institutional-bar a:hover {
            text-decoration: underline;
        }

        /* Main header */
        .main-header {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem 0;
            text-align: center;
        }
        .logos-container {
            display: flex;
            justify-content: center;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            gap: 2rem;
        }
        .logo-group {
            display: flex;
            align-items: center;
            gap: 2rem;
            height: 64px;
            padding: 1rem;
        }
        .direccion-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }
        .direccion-img {
            height: 100%;
            width: auto;
            max-height: 64px;
            object-fit: contain;
        }

        /* Navigation */
        .nav-container {
            background-color: white;
            border-bottom: 1px solid #e5e7eb;
        }
        .nav-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-links {
            display: flex;
            gap: 2rem;
        }
        .nav-link {
            color: #4B5563;
            text-decoration: none;
            padding: 1rem;
            transition: color 0.2s;
        }
        .nav-link:hover {
            color: #003876;
        }

        /* Search buttons */
        .search-actions {
            display: flex;
            gap: 1rem;
        }
        .search-button {
            background-color: #003876;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: background-color 0.2s;
        }
        .search-button:hover {
            background-color: #002b5c;
        }

        /* Main content */
        .main-content {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        /* Results specific styles */
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .card-header {
            background-color: #003876;
            color: white;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #dee2e6;
        }

        .card-header h5 {
            margin: 0 0 0.5rem 0;
            font-size: 1.25rem;
            font-weight: bold;
        }

        .card-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .volver-btn {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .volver-btn:hover {
            background-color: #2980b9;
            text-decoration: none;
            color: white;
        }

        .libro-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #f9f9f9;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .libro-titulo {
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
            font-size: 1.1rem;
        }
        
        .libro-autor {
            color: #666;
            margin-bottom: 5px;
        }
        
        .libro-detalle {
            color: #888;
            font-size: 0.9em;
            margin-bottom: 3px;
        }
        
        .libro-ubicacion {
            background-color: #e9f7ef;
            padding: 8px;
            border-radius: 4px;
            margin-top: 10px;
            border-left: 4px solid #27ae60;
        }
        
        .no-resultados {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .seccion-titulo {
            background-color: #34495e;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            margin: 20px 0 15px 0;
            font-weight: bold;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        .h-16 {
            height: 4rem;
        }

        .container.mx-auto {
            margin-left: auto;
            margin-right: auto;
        }

        .px-4 {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .flex {
            display: flex;
        }

        .justify-center {
            justify-content: center;
        }

        .space-x-8 > * + * {
            margin-left: 2rem;
        }

        .mr-2 {
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <!-- Barra institucional -->
    <div class="institutional-bar">
        <div class="container mx-auto px-4">
            <div class="flex justify-center space-x-8">
                <a href="#">Web UBB</a>
                <a href="#">Intranet</a>
                <a href="#">Correo Institucional</a>
                <a href="#">Adecca UBB</a>
                <a href="#">Moodle UBB</a>
            </div>
        </div>
    </div>

    <!-- Cabecera principal -->
    <header class="main-header">
        <div class="logos-container">
            <div class="logo-group">
                <img src="{{ asset('img/logo-sistema-bibliotecas.png') }}" alt="Sistema de Bibliotecas" class="h-16">
                <div class="direccion-wrapper">
                    <img src="{{ asset('img/logo_direccion_bibliotecas.png') }}" alt="Dirección de Bibliotecas" class="direccion-img">
                </div>
            </div>
            <div class="logo-group">
                <img src="{{ asset('img/logo-ciencia-abierta.png') }}" alt="Ciencia Abierta" class="h-16">
                <img src="{{ asset('img/logo-ubb.png') }}" alt="Universidad del Bío-Bío" class="h-16">
            </div>
        </div>
    </header>

    <!-- Navegación principal -->
    <nav class="nav-container">
        <div class="nav-content">
            <div class="nav-links">
                <a href="/" class="nav-link">Inicio</a>
                <a href="#" class="nav-link">Quiénes somos</a>
                <a href="#" class="nav-link">Recursos</a>
                <a href="#" class="nav-link">Servicios</a>
                <a href="#" class="nav-link">Bibliotecas</a>
                <a href="#" class="nav-link">Galería</a>
                <a href="#" class="nav-link">Noticias</a>
                <a href="#" class="nav-link">Contacto</a>
            </div>
            <div class="search-actions">
                <a href="{{ route('busqueda') }}" class="search-button">
                    <i class="fas fa-search mr-2"></i>Búsqueda Simple
                </a>
                <a href="{{ route('busqueda-avanzada') }}" class="search-button">
                    <i class="fas fa-filter mr-2"></i>Búsqueda Avanzada
                </a>
                <a href="{{ route('perfil') }}" class="search-button" title="Cuenta personal">
                    <i class="fas fa-user-circle mr-2"></i>
                    Perfil{{ session('rut_usuario') ? ' (' . session('rut_usuario') . ')' : '' }}
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5>Resultados de Búsqueda Simple</h5>
                @if(isset($termino) && $termino)
                    <p class="mb-0">Término buscado: "{{ $termino }}"
                    @if(isset($tipo) && $tipo)
                        | Tipo: {{ $tipo }}
                    @endif
                    </p>
                @endif
            </div>

            <div class="card-body">
                <a href="{{ route('busqueda') }}" class="volver-btn">
                    ← Volver a la búsqueda simple
                </a>

                @if(isset($titulos) && $titulos->count() > 0)
                    <div class="seccion-titulo">
                        Títulos encontrados ({{ $titulos->count() }} resultado{{ $titulos->count() > 1 ? 's' : '' }})
                    </div>
                    
                    @foreach($titulos as $titulo)
                        <div class="libro-card">
                            <div class="libro-titulo">{{ $titulo->titulo ?? 'Sin título' }}</div>
                            
                            @if(isset($titulo->nombre_autor))
                                <div class="libro-autor">
                                    <strong>Autor:</strong> {{ $titulo->nombre_autor }}
                                </div>
                            @endif
                            
                            @if(isset($titulo->nombre_editorial))
                                <div class="libro-detalle">
                                    <strong>Editorial:</strong> {{ $titulo->nombre_editorial }}
                                </div>
                            @endif
                            
                            @if(isset($titulo->nombre_serie))
                                <div class="libro-detalle">
                                    <strong>Serie:</strong> {{ $titulo->nombre_serie }}
                                </div>
                            @endif
                            
                            @if(isset($titulo->anio_publicacion))
                                <div class="libro-detalle">
                                    <strong>Año:</strong> {{ $titulo->anio_publicacion }}
                                </div>
                            @endif
                            
                            @if(isset($titulo->isbn))
                                <div class="libro-detalle">
                                    <strong>ISBN:</strong> {{ $titulo->isbn }}
                                </div>
                            @endif
                            
                            @if(isset($titulo->signatura_topografica))
                                <div class="libro-ubicacion">
                                    <strong>Ubicación:</strong> {{ $titulo->signatura_topografica }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="no-resultados">
                        <h4>No se encontraron títulos</h4>
                        <p>No hay títulos disponibles para mostrar.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
