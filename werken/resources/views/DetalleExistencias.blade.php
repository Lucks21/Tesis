<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Existencias - Sistema de Bibliotecas UBB</title>
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
        
        .existencias-header {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .existencias-title {
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: 900;
            font-size: 2rem;
            color: #003876;
            margin-bottom: 1rem;
        }
        
        .existencias-info {
            color: #666;
            font-size: 1.1rem;
        }
        
        .existencias-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .existencias-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .existencias-table th {
            background: #003876;
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: bold;
        }
        
        .existencias-table td {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .existencias-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-disponible {
            background: #d4edda;
            color: #155724;
        }
        
        .status-prestado {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-reservado {
            background: #fff3cd;
            color: #856404;
        }
        
        .no-existencias {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        
        .no-existencias i {
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
        
        <div class="existencias-header">
            <h1 class="existencias-title">Detalles de Existencias</h1>
            <div class="existencias-info">
                Número de Control: <strong>{{ $numero_control }}</strong>
                <br>Tipo de Consulta: {{ $tipo_consulta }}
            </div>
        </div>
        
        <div class="existencias-container">
            @if(empty($existencias))
                <div class="no-existencias">
                    <i class="fas fa-book"></i>
                    <h3>No hay existencias disponibles</h3>
                    <p>No se encontraron existencias para este número de control.</p>
                </div>
            @else
                <table class="existencias-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Ubicación</th>
                            <th>Estado</th>
                            <th>Biblioteca</th>
                            <th>Fecha Préstamo</th>
                            <th>Fecha Devolución</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($existencias as $existencia)
                            <tr>
                                <td>{{ $existencia->codigo ?? 'N/A' }}</td>
                                <td>{{ $existencia->ubicacion ?? 'N/A' }}</td>
                                <td>
                                    @if(isset($existencia->estado))
                                        @php
                                            $estado = strtolower($existencia->estado);
                                            $badgeClass = 'status-disponible';
                                            if(strpos($estado, 'prestado') !== false) {
                                                $badgeClass = 'status-prestado';
                                            } elseif(strpos($estado, 'reservado') !== false) {
                                                $badgeClass = 'status-reservado';
                                            }
                                        @endphp
                                        <span class="status-badge {{ $badgeClass }}">
                                            {{ $existencia->estado }}
                                        </span>
                                    @else
                                        <span class="status-badge status-disponible">Disponible</span>
                                    @endif
                                </td>
                                <td>{{ $existencia->biblioteca ?? 'N/A' }}</td>
                                <td>{{ $existencia->fecha_prestamo ?? 'N/A' }}</td>
                                <td>{{ $existencia->fecha_devolucion ?? 'N/A' }}</td>
                                <td>{{ $existencia->observaciones ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
