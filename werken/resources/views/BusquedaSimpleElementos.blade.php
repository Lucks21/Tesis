<!DOCTYPE html><html lang="es"><head>    <meta charset="UTF-8">    <meta http-equiv="X-UA-Compatible" content="IE=edge">    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <title>Búsqueda Simple - Sistema de Bibliotecas UBB</title>    <link href="{{ asset('css/app.css') }}" rel="stylesheet">    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">    <style>        @font-face {            font-family: 'Tipo-UBB';            src: url('{{ asset('fonts/Tipo-UBB-Black_Condensed.otf') }}') format('opentype');            font-weight: 900;            font-style: normal;            font-display: swap;        }        @font-face {            font-family: 'Tipo-UBB';            src: url('{{ asset('fonts/Tipo-UBB-Bold_Condensed.otf') }}') format('opentype');            font-weight: bold;            font-style: normal;            font-display: swap;        }        @font-face {            font-family: 'Tipo-UBB';            src: url('{{ asset('fonts/Tipo-UBB-Regular_Condensed.otf') }}') format('opentype');            font-weight: normal;            font-style: normal;            font-display: swap;        }        @font-face {            font-family: 'Tipo-UBB';            src: url('{{ asset('fonts/Tipo-UBB-Light_Condensed.otf') }}') format('opentype');            font-weight: 300;            font-style: normal;            font-display: swap;        }        /* Base styles */        body {            margin: 0;            font-family: 'Tipo-UBB', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;            line-height: 1.5;            -webkit-font-smoothing: antialiased;            -moz-osx-font-smoothing: grayscale;        }        /* Institutional bar */        .institutional-bar {            background-color: #003876;            color: white;            font-size: 0.875rem;            padding: 0.5rem 0;        }        .institutional-bar a {            color: white;            text-decoration: none;            padding: 0.5rem 1rem;        }        .institutional-bar a:hover {            text-decoration: underline;        }        /* Main header */        .main-header {            background: white;            box-shadow: 0 2px 4px rgba(0,0,0,0.1);            padding: 1rem 0;            text-align: center;        }        .logos-container {            display: flex;            justify-content: center;            align-items: center;            max-width: 1200px;            margin: 0 auto;            padding: 0 1rem;            gap: 2rem;        }        .logo-group {            display: flex;            align-items: center;            gap: 2rem;            height: 64px;            padding: 1rem;        }        .direccion-wrapper {            display: flex;            align-items: center;            justify-content: center;            height: 100%;        }        .direccion-img {            height: 100%;            width: auto;            max-height: 64px;            object-fit: contain;        }        /* Navigation */        .nav-container {            background-color: white;            border-bottom: 1px solid #e5e7eb;        }        .nav-content {            max-width: 1200px;            margin: 0 auto;            padding: 0 1rem;            display: flex;            justify-content: space-between;            align-items: center;        }        .nav-links {            display: flex;            gap: 2rem;        }        .nav-link {            color: #4B5563;            text-decoration: none;            padding: 1rem;            transition: color 0.2s;        }        .nav-link:hover {            color: #003876;        }        /* Search buttons */        .search-actions {            display: flex;            gap: 1rem;        }        .search-button {            background-color: #003876;            color: white;            border: none;            padding: 0.75rem 1.5rem;            border-radius: 4px;            cursor: pointer;            text-decoration: none;            display: inline-flex;            align-items: center;            transition: background-color 0.2s;        }        .search-button:hover {            background-color: #002b5c;        }        /* Main content */        .container {            max-width: 1200px;            margin: 2rem auto;            padding: 0 1rem;        }        .card {            background: white;            border-radius: 8px;            box-shadow: 0 2px 10px rgba(0,0,0,0.1);            overflow: hidden;        }        .card-header {            background-color: #003876;            color: white;            padding: 1rem 1.5rem;            border-bottom: 1px solid #dee2e6;        }        .card-header h5 {            margin: 0 0 0.5rem 0;            font-size: 1.25rem;            font-weight: bold;        }        .card-header p {            margin: 0;            opacity: 0.9;            font-size: 0.9rem;        }        .card-body {            padding: 1.5rem;        }        .table {            width: 100%;            border-collapse: collapse;            margin-bottom: 1rem;        }        .table th,        .table td {            padding: 0.75rem;            vertical-align: top;            border-top: 1px solid #dee2e6;            text-align: left;        }        .table thead th {            background-color: #003876;            color: white;            border-bottom: 2px solid #dee2e6;            font-weight: bold;        }        .table-striped tbody tr:nth-of-type(odd) {            background-color: rgba(0,0,0,.05);        }        .table-hover tbody tr:hover {            background-color: rgba(0,123,255,.1);        }        .btn {            display: inline-block;            padding: 0.375rem 0.75rem;            margin-bottom: 0;            font-size: 0.875rem;            font-weight: 400;            line-height: 1.5;            text-align: center;            text-decoration: none;            vertical-align: middle;            cursor: pointer;            border: 1px solid transparent;            border-radius: 0.25rem;            transition: all 0.15s ease-in-out;        }        .btn-primary {            color: #fff;            background-color: #003876;            border-color: #003876;        }        .btn-primary:hover {            color: #fff;            background-color: #002b5c;            border-color: #002b5c;        }        .btn-secondary {            color: #fff;            background-color: #6c757d;            border-color: #6c757d;        }        .btn-secondary:hover {            color: #fff;            background-color: #5a6268;            border-color: #545b62;        }        .btn-sm {            padding: 0.25rem 0.5rem;            font-size: 0.75rem;            border-radius: 0.2rem;        }        .alert {            padding: 0.75rem 1.25rem;            margin-bottom: 1rem;            border: 1px solid transparent;            border-radius: 0.25rem;        }        .alert-info {            color: #0c5460;            background-color: #d1ecf1;            border-color: #bee5eb;        }        .table-responsive {            display: block;            width: 100%;            overflow-x: auto;            -webkit-overflow-scrolling: touch;        }        .mt-3 {            margin-top: 1rem;        }        .mb-0 {            margin-bottom: 0;        }        .h-16 {            height: 4rem;        }        .container.mx-auto {            margin-left: auto;            margin-right: auto;        }        .px-4 {            padding-left: 1rem;            padding-right: 1rem;        }        .flex {            display: flex;        }        .justify-center {            justify-content: center;        }

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
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5>Resultados de búsqueda: {{ $criterio }}</h5>
                <p class="mb-0">Término: "{{ $valorCriterio }}" | Selecciona un elemento para ver los títulos asociados</p>
            </div>

            <div class="card-body">
                @if($resultados->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>{{ $criterio }}</th>
                                    <th width="150">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($resultados as $elemento)
                                    <tr>
                                        <td>
                                            <strong>{{ $elemento->nombre }}</strong>
                                        </td>
                                        <td>
                                            <a href="{{ route('busqueda.sp', ['termino' => $elemento->nombre, 'tipo' => $tipoBusqueda, 'ver_titulos' => 1]) }}" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-book"></i> Ver títulos
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if($resultados->hasPages())
                        <div class="pagination-container">
                            <div class="pagination-info">
                                Mostrando {{ $resultados->firstItem() }} a {{ $resultados->lastItem() }} de {{ $resultados->total() }} resultados
                            </div>
                            <div>
                                {{ $resultados->links() }}
                            </div>
                        </div>
                    @endif
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        No se encontraron elementos que coincidan con tu búsqueda.
                    </div>
                @endif

                <!-- Botón para volver a la búsqueda -->
                <div class="mt-3">
                    <a href="{{ route('busqueda') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Nueva búsqueda
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>