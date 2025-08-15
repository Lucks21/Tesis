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
                            <span class="font-semibold texto-azul">Nro. de Control :</span>
                            <span class="ml-2">{{ $detalleMaterial->nro_control ?? 'No disponible' }}</span>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Título :</span>
                            <span class="ml-2">{{ $detalleMaterial->titulo ?? 'No disponible' }}</span>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Autor(es) :</span>
                            <div class="ml-2">
                                @if(!empty($detalleMaterial->autores) && count($detalleMaterial->autores) > 0)
                                    @foreach($detalleMaterial->autores as $autor)
                                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1 mb-1">{{ $autor }}</span>
                                    @endforeach
                                @else
                                    <span>No disponible</span>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Editorial(es) :</span>
                            <div class="ml-2">
                                @if(!empty($detalleMaterial->editoriales) && count($detalleMaterial->editoriales) > 0)
                                    @foreach($detalleMaterial->editoriales as $editorial)
                                        <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full mr-1 mb-1">{{ $editorial }}</span>
                                    @endforeach
                                @else
                                    <span>No disponible</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <div>
                            <span class="font-semibold texto-azul">Materia(s) :</span>
                            <div class="ml-2">
                                @if(!empty($detalleMaterial->materias) && count($detalleMaterial->materias) > 0)
                                    @foreach($detalleMaterial->materias as $materia)
                                        <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full mr-1 mb-1">{{ $materia }}</span>
                                    @endforeach
                                @else
                                    <span>No disponible</span>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Serie(s) :</span>
                            <div class="ml-2">
                                @if(!empty($detalleMaterial->series) && count($detalleMaterial->series) > 0)
                                    @foreach($detalleMaterial->series as $serie)
                                        <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full mr-1 mb-1">{{ $serie }}</span>
                                    @endforeach
                                @else
                                    <span>No disponible</span>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Clasificación Dewey :</span>
                            <div class="ml-2">
                                @if(!empty($detalleMaterial->dewey) && count($detalleMaterial->dewey) > 0)
                                    @foreach($detalleMaterial->dewey as $clasificacion)
                                        <span class="inline-block bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full mr-1 mb-1">{{ $clasificacion }}</span>
                                    @endforeach
                                @else
                                    <span>No disponible</span>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Idioma(s) :</span>
                            <div class="ml-2">
                                @if(!empty($detalleMaterial->idiomas) && count($detalleMaterial->idiomas) > 0)
                                    @foreach($detalleMaterial->idiomas as $idioma)
                                        <span class="inline-block bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded-full mr-1 mb-1">{{ $idioma }}</span>
                                    @endforeach
                                @else
                                    <span>No disponible</span>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Nota(s) :</span>
                            <div class="ml-2">
                                @if(!empty($detalleMaterial->notas) && count($detalleMaterial->notas) > 0)
                                    @foreach($detalleMaterial->notas as $nota)
                                        <div class="bg-orange-50 border-l-4 border-orange-400 p-2 mb-1 text-sm">
                                            <span class="text-orange-800">{{ $nota }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <span>No disponible</span>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Otro(s) Título(s) :</span>
                            <div class="ml-2">
                                @if(!empty($detalleMaterial->otros_titulos) && count($detalleMaterial->otros_titulos) > 0)
                                    @foreach($detalleMaterial->otros_titulos as $otroTitulo)
                                        <span class="inline-block bg-teal-100 text-teal-800 text-xs px-2 py-1 rounded-full mr-1 mb-1">{{ $otroTitulo }}</span>
                                    @endforeach
                                @else
                                    <span>No disponible</span>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Edición :</span>
                            <div class="ml-2">
                                @if(!empty($detalleMaterial->edicion))
                                    <span class="inline-block bg-cyan-100 text-cyan-800 text-xs px-2 py-1 rounded-full mr-1 mb-1">{{ $detalleMaterial->edicion }}</span>
                                @else
                                    <span>No disponible</span>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Datos de Publicación :</span>
                            <div class="ml-2">
                                @if(!empty($detalleMaterial->datos_publicacion))
                                    <div class="bg-pink-50 border-l-4 border-pink-400 p-2 mb-1 text-sm">
                                        <span class="text-pink-800">{{ $detalleMaterial->datos_publicacion }}</span>
                                    </div>
                                @else
                                    <span>No disponible</span>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Descripción :</span>
                            <div class="ml-2">
                                @if(!empty($detalleMaterial->descripcion))
                                    <div class="bg-emerald-50 border-l-4 border-emerald-400 p-2 mb-1 text-sm">
                                        <span class="text-emerald-800">{{ $detalleMaterial->descripcion }}</span>
                                    </div>
                                @else
                                    <span>No disponible</span>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">ISBN :</span>
                            <div class="ml-2">
                                @if(!empty($detalleMaterial->isbn) && count($detalleMaterial->isbn) > 0)
                                    @foreach($detalleMaterial->isbn as $isbn)
                                        <span class="inline-block bg-slate-100 text-slate-800 text-xs px-2 py-1 rounded-full mr-1 mb-1">{{ $isbn }}</span>
                                    @endforeach
                                @else
                                    <span>No disponible</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        </div>
    </div>
</body>
</html>
