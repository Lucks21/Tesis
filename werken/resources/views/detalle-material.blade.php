<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <title>Detalle del Material - Sistema de Bibliotecas UBB</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @font-face {
            font-family: 'Tipo-UBB';
            src: url('{{ asset('fonts/Tipo-UBB-Black_Condensed.otf') }}') format('opentype');
            font-weight: 900;
            font-style: normal;
        }
        @font-face {
            font-family: 'Tipo-UBB';
            src: url('{{ asset('fonts/Tipo-UBB-Bold_Condensed.otf') }}') format('opentype');
            font-weight: bold;
            font-style: normal;
        }
        @font-face {
            font-family: 'Tipo-UBB';
            src: url('{{ asset('fonts/Tipo-UBB-Regular_Condensed.otf') }}') format('opentype');
            font-weight: normal;
            font-style: normal;
        }
        
        body {
            font-family: 'Tipo-UBB', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f3f4f6;
        }
        
        .page-header {
            background: linear-gradient(135deg, #003876 0%, #002b5c 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        
        .resumen-header {
            background-color: #003876;
            color: white;
            padding: 8px 16px;
            font-weight: bold;
            border-radius: 4px 4px 0 0;
        }
        
        .resumen-content {
            background-color: #e6e6fa;
            padding: 16px;
            border: 1px solid #003876;
            border-radius: 0 0 4px 4px;
        }
        
        .existencias-header {
            background-color: #003876;
            color: white;
            padding: 8px 16px;
            font-weight: bold;
            border-radius: 4px 4px 0 0;
        }
        
        .existencias-table {
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #003876;
        }
        
        .existencias-table th {
            background-color: #003876;
            color: white;
            padding: 8px 12px;
            text-align: left;
            font-size: 12px;
            font-weight: bold;
        }
        
        .existencias-table td {
            padding: 8px 12px;
            font-size: 12px;
            border-bottom: 1px solid #ddd;
        }
        
        .fila-par {
            background-color: #9999ff;
        }
        
        .fila-impar {
            background-color: #6666cc;
        }
        
        .btn-reservar {
            background-color: #6666cc;
            color: white;
            padding: 2px 8px;
            border: none;
            border-radius: 3px;
            font-size: 11px;
            cursor: pointer;
        }
        
        .btn-reservar:hover {
            background-color: #5555bb;
        }
        
        .texto-azul {
            color: #0066cc;
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

        .logos-container {
            display: flex;
            justify-content: center;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            gap: 2rem;
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
            align-items: center;
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
            justify-content: center;
            transition: background-color 0.2s;
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: bold;
            min-width: 160px;
            text-align: center;
        }

        .search-button i {
            margin-right: 0.5rem;
        }

        .search-button:hover {
            background-color: #002b5c;
        }

        /* Additional styles for container compatibility */
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .institutional-links a {
            margin-left: 2rem;
        }

        .institutional-links a:first-child {
            margin-left: 0;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Barra institucional -->
    <div class="institutional-bar">
        <div class="container mx-auto px-4">
            <div class="flex justify-center institutional-links">
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

    <!-- Título de la página -->
    <div class="container mx-auto px-6 py-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Detalle del Material</h1>
    </div>

    <!-- Barra de acciones -->
    <div class="actions-bar">
        <div class="container mx-auto px-6">
            <div class="flex justify-between items-center">
                <a href="{{ route('busqueda-avanzada-resultados', ['titulo' => '']) }}" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>Volver a resultados
                </a>
                <a href="{{ route('export.ris', ['nroControl' => $detalleMaterial->nro_control]) }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                    <i class="fas fa-file-export mr-2"></i>Exportar RIS
                </a>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6">
        <!-- Resumen Bibliográfico -->
        <div class="mb-6">
            <div class="resumen-header">
                Resumen Bibliográfico
            </div>
            
            <div class="resumen-content">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <div>
                            <span class="font-semibold texto-azul">Nro. de Pedido :</span>
                            <span class="ml-2">{{ $detalleMaterial->nro_pedido ?? 'No disponible' }}</span>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Autor :</span>
                            <span class="ml-2">{{ $detalleMaterial->autor ?? 'No disponible' }}</span>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Título :</span>
                            <span class="ml-2">{{ $detalleMaterial->titulo ?? 'No disponible' }}</span>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Edición :</span>
                            <span class="ml-2">{{ $detalleMaterial->edicion ?? 'No disponible' }}</span>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <div>
                            <span class="font-semibold texto-azul">Datos de Publicación :</span>
                            <span class="ml-2">{{ $detalleMaterial->datos_publicacion ?? 'No disponible' }}</span>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Descripción :</span>
                            <span class="ml-2">{{ $detalleMaterial->descripcion ?? 'No disponible' }}</span>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Material(s) :</span>
                            <span class="ml-2 text-blue-600 font-semibold">{{ $detalleMaterial->materiales ?? 'No disponible' }}</span>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Editorial :</span>
                            <span class="ml-2">{{ $detalleMaterial->editorial ?? 'No disponible' }}</span>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Materia :</span>
                            <span class="ml-2">{{ $detalleMaterial->materia ?? 'No disponible' }}</span>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Serie :</span>
                            <span class="ml-2">{{ $detalleMaterial->serie ?? 'No disponible' }}</span>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Clasificación Dewey :</span>
                            <span class="ml-2">{{ $detalleMaterial->dewey ?? 'No disponible' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Existencias -->
        <div class="mb-6">
            <div class="existencias-header">
                Existencias
            </div>
            
            <div class="overflow-x-auto">
                <table class="existencias-table">
                    <thead>
                        <tr>
                            <th>Reserva</th>
                            <th>Ubicación</th>
                            <th>Volumen</th>
                            <th>Parte</th>
                            <th>Suplemento</th>
                            <th>Días préstamo</th>
                            <th>Formato</th>
                            <th>Estado</th>
                            <th>Copias</th>
                            <th>Próxima devolución</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($detalleMaterial->existencias) && count($detalleMaterial->existencias) > 0)
                            @foreach($detalleMaterial->existencias as $index => $existencia)
                                <tr class="{{ $index % 2 == 0 ? 'fila-par' : 'fila-impar' }}">
                                    <td>
                                        @if($existencia->nombre_tb_estado == 'DISPONIBLE')
                                            <button class="btn-reservar">
                                                Reservar
                                            </button>
                                        @else
                                            <span class="text-gray-500 text-xs">No disponible</span>
                                        @endif
                                    </td>
                                    <td>{{ $existencia->nombre_tb_campus ?? 'No disponible' }}</td>
                                    <td>{{ $existencia->nro_volumen_existe ?? '-' }}</td>
                                    <td>{{ $existencia->nro_parte_existe ?? '-' }}</td>
                                    <td>{{ $existencia->nro_suplemento_existe ?? '-' }}</td>
                                    <td>{{ $existencia->dias ?? '-' }}</td>
                                    <td>{{ $existencia->nombre_tb_format ?? '-' }}</td>
                                    <td>{{ $existencia->nombre_tb_estado ?? '-' }}</td>
                                    <td>{{ $existencia->Total ?? '1' }}</td>
                                    <td>{{ $existencia->fecha_dev ?? '-' }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="fila-par">
                                <td>
                                    <span class="text-gray-500 text-xs">No disponible</span>
                                </td>
                                <td>No disponible</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>Libro</td>
                                <td>Sin información</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        </div>
    </div>
</body>
</html>
