<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Búsqueda - Sistema de Bibliotecas UBB</title>
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

        body {
            margin: 0;
            font-family: 'Tipo-UBB', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.5;
            background-color: #f8f9fa;
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
            margin: 0 1rem;
        }
        .institutional-bar a:hover {
            text-decoration: underline;
        }

        /* Main header */
        .main-header {
            background-color: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem 0;
        }
        .logos-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo-group {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .direccion-wrapper {
            display: flex;
            align-items: center;
            height: 100%;
        }
        .direccion-img {
            height: 100%;
            width: auto;
            max-height: 64px;
            object-fit: contain;
        }
        img.h-16 {
            height: 64px;
            width: auto;
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
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: normal;
        }
        .nav-link:hover {
            color: #003876;
        }

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
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: bold;
        }
        .search-button:hover {
            background-color: #002b5c;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .detail-header {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .detail-title {
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: 900;
            font-size: 2rem;
            color: #003876;
            margin-bottom: 1rem;
        }
        
        .detail-info {
            color: #666;
            font-size: 1.1rem;
        }
        
        .details-container {
            display: grid;
            gap: 2rem;
        }
        
        .detail-item {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .detail-item h3 {
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: bold;
            color: #003876;
            margin-bottom: 1rem;
            border-bottom: 2px solid #003876;
            padding-bottom: 0.5rem;
        }
        
        .detail-field {
            margin-bottom: 1rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .detail-field:last-child {
            border-bottom: none;
        }
        
        .field-label {
            font-weight: bold;
            color: #333;
            margin-bottom: 0.25rem;
        }
        
        .field-value {
            color: #666;
        }
        
        .no-details {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        
        .no-details i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #ccc;
        }
        
        .back-btn {
            background: #003876;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: background-color 0.2s;
            margin-bottom: 2rem;
        }
        
        .back-btn:hover {
            background: #002b5c;
        }

        .flex {
            display: flex;
        }

        .justify-center {
            justify-content: center;
        }

        .mx-auto {
            margin-left: auto;
            margin-right: auto;
        }

        .px-4 {
            padding-left: 1rem;
            padding-right: 1rem;
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
            <div class="flex justify-center">
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
                <a href="{{ url('/') }}" class="nav-link">Inicio</a>
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
            </div>
        </div>
    </nav>

    <div class="container">
        <a href="javascript:history.back()" class="back-btn">
            <i class="fas fa-arrow-left mr-2"></i>Volver
        </a>
        
        <div class="detail-header">
            <h1 class="detail-title">Detalles de Búsqueda</h1>
            <div class="detail-info">
                Detalles para: "{{ $texto_busqueda }}"
                <br>Tipo de criterio: {{ $tipo_criterio }}
            </div>
        </div>
        
        <div class="details-container">
            @if(empty($detalles))
                <div class="detail-item">
                    <div class="no-details">
                        <i class="fas fa-info-circle"></i>
                        <h3>No hay detalles disponibles</h3>
                        <p>No se encontraron detalles para esta búsqueda.</p>
                    </div>
                </div>
            @else
                @foreach($detalles as $detalle)
                    <div class="detail-item">
                        <h3>Material Bibliográfico</h3>
                        
                        @foreach(get_object_vars($detalle) as $campo => $valor)
                            @if($valor)
                                <div class="detail-field">
                                    <div class="field-label">{{ ucfirst(str_replace('_', ' ', $campo)) }}:</div>
                                    <div class="field-value">{{ $valor }}</div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    
    @if($errors->any())
        <script>
            alert('Error: {{ $errors->first() }}');
        </script>
    @endif
</body>
</html>
