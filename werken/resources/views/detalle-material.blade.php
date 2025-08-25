<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Material - Sistema de Bibliotecas UBB</title>
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
        
        :root {
            --ubb-azul-principal: #003876;
            --ubb-azul-secundario: #0066cc;
            --ubb-azul-claro: #e6f3ff;
            --ubb-gris-claro: #f8fafc;
            --ubb-gris-medio: #e2e8f0;
            --ubb-texto-oscuro: #1a202c;
            --ubb-verde: #10b981;
            --ubb-rojo: #ef4444;
            --ubb-amarillo: #f59e0b;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Tipo-UBB', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: var(--ubb-gris-claro);
            min-height: 100vh;
            margin: 0;
            color: var(--ubb-texto-oscuro);
            line-height: 1.6;
        }
        
        .page-header {
            background: var(--ubb-azul-principal);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
            z-index: 0;
        }
        
        .page-header .container {
            position: relative;
            z-index: 1;
        }
        
        .page-header h1 {
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: 900;
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }
        
        .page-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .actions-bar {
            background: white;
            border-bottom: 1px solid var(--ubb-gris-medio);
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        
        .card-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 56, 118, 0.1);
            overflow: hidden;
            border: 1px solid rgba(0, 56, 118, 0.1);
        }
        
        .resumen-header {
            background: linear-gradient(135deg, #003876 0%, #002b5c 100%);
            color: white;
            padding: 20px 24px;
            font-weight: bold;
            font-size: 18px;
            border: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .resumen-content {
            background-color: white;
            padding: 32px 24px;
            border: none;
        }
        
        .existencias-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 56, 118, 0.1);
            overflow: hidden;
            border: 1px solid rgba(0, 56, 118, 0.1);
            margin-top: 24px;
        }
        
        .existencias-header {
            background: linear-gradient(135deg, #003876 0%, #002b5c 100%);
            color: white;
            padding: 20px 24px;
            font-weight: bold;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .existencias-table {
            width: 100%;
            border-collapse: collapse;
            width: 100%;
            border: none;
            background: white;
        }
        
        .existencias-table th {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: #003876;
            padding: 16px 20px;
            text-align: left;
            font-size: 14px;
            font-weight: 600;
            border-bottom: 2px solid #003876;
        }
        
        .existencias-table td {
            padding: 16px 20px;
            font-size: 14px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .fila-par {
            background-color: #f8fafc;
        }
        
        .fila-impar {
            background-color: white;
        }
        
        .btn-reservar {
            background: linear-gradient(135deg, #003876 0%, #002b5c 100%);
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 56, 118, 0.2);
        }
        
        .btn-reservar:hover {
            background: linear-gradient(135deg, #002b5c 0%, #001a3d 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 56, 118, 0.3);
        }
        
        .texto-azul {
            color: #003876;
            font-weight: 600;
        }
        
        .field-label {
            color: #003876;
            font-weight: 600;
            font-size: 14px;
        }
        
        .field-value {
            color: #374151;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .tag-text {
            color: #374151;
            font-size: 14px;
            font-weight: 400;
            margin-right: 8px;
            display: inline;
        }
        
        .tag-text:not(:last-child)::after {
            content: ", ";
        }
        
        .info-box {
            border-radius: 8px;
            padding: 16px;
            margin: 4px 0;
            border-left: 4px solid;
        }
        
        .info-box-publication {
            background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 100%);
            border-left-color: #ec4899;
            color: #be185d;
        }
        
        .info-box-description {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border-left-color: #10b981;
            color: #047857;
        }
        
        .info-box-note {
            background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
            border-left-color: #f97316;
            color: #c2410c;
        }
        
        .page-title {
            color: #003876;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .back-button {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border: 2px solid #3b82f6;
            color: #1d4ed8;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.1);
        }
        
        .back-button:hover {
            background: linear-gradient(135deg, #bfdbfe 0%, #93c5fd 100%);
            border-color: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(59, 130, 246, 0.2);
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
    <div class="container mx-auto px-6 py-8">
        <h1 class="page-title text-4xl mb-2">Detalle del Material</h1>
        <div class="w-24 h-1 bg-gradient-to-r from-blue-600 to-blue-800 rounded-full"></div>
    </div>

    <!-- Barra de acciones -->
    <div class="actions-bar" style="padding: 0 0 2rem 0;">
        <div class="container mx-auto px-6">
            <div class="flex justify-start items-center">
                <a href="javascript:history.back()" class="back-button">
                    <i class="fas fa-arrow-left"></i>Volver a resultados
                </a>
            </div>
        </div>
    </nav>
    <!-- Contenido principal -->
    <main class="py-8">
        <div class="container">
            <!-- Barra de acciones -->
            <div class="actions-bar">
                <div style="display: flex; justify-content: space-between; align-items: center; background: white; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                    <a href="javascript:history.back()" class="btn-action btn-back">
                        <i class="fas fa-arrow-left" style="margin-right: 0.5rem;"></i>Volver a resultados
                    </a>
                    <a href="{{ route('export.ris', ['nroControl' => $detalleMaterial->nro_control]) }}" 
                       class="btn-action btn-export">
                        <i class="fas fa-file-export" style="margin-right: 0.5rem;"></i>Exportar RIS
                    </a>
                </div>
            </div>

    <div class="container mx-auto px-6 pb-12">
        <!-- Resumen Bibliográfico -->
        <div class="card-container">
            <div class="resumen-header">
                <i class="fas fa-book"></i>
                Resumen Bibliográfico
            </div>
            
            <div class="resumen-content">
                <div class="space-y-6">
                    @if(!empty($detalleMaterial->nro_control))
                    <div class="grid grid-cols-12 gap-4 items-start">
                        <div class="col-span-3 field-label">Nro. de Control:</div>
                        <div class="col-span-9 field-value">{{ $detalleMaterial->nro_control }}</div>
                    </div>
                    @endif
                    
                    @if(!empty($detalleMaterial->titulo))
                    <div class="grid grid-cols-12 gap-4 items-start">
                        <div class="col-span-3 field-label">Título:</div>
                        <div class="col-span-9 field-value">{{ $detalleMaterial->titulo }}</div>
                    </div>
                    @endif
                    
                    @if(!empty($detalleMaterial->autores) && count($detalleMaterial->autores) > 0)
                    <div class="grid grid-cols-12 gap-4 items-start">
                        <div class="col-span-3 field-label">Autor(es):</div>
                        <div class="col-span-9">
                            @foreach($detalleMaterial->autores as $autor)
                                <span class="tag-text">{{ $autor }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    @if(!empty($detalleMaterial->editoriales) && count($detalleMaterial->editoriales) > 0)
                    <div class="grid grid-cols-12 gap-4 items-start">
                        <div class="col-span-3 field-label">Editorial(es):</div>
                        <div class="col-span-9">
                            @foreach($detalleMaterial->editoriales as $editorial)
                                <span class="tag-text">{{ $editorial }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    @if(!empty($detalleMaterial->edicion))
                    <div class="grid grid-cols-12 gap-4 items-start">
                        <div class="col-span-3 field-label">Edición:</div>
                        <div class="col-span-9">
                            <span class="tag-text">{{ $detalleMaterial->edicion }}</span>
                        </div>
                    </div>
                    @endif
                    
                    @if(!empty($detalleMaterial->datos_publicacion))
                    <div class="grid grid-cols-12 gap-4 items-start">
                        <div class="col-span-3 field-label">Datos de Publicación:</div>
                        <div class="col-span-9">
                            <span class="field-value">{{ $detalleMaterial->datos_publicacion }}</span>
                        </div>
                    </div>
                    @endif
                    
                    @if(!empty($detalleMaterial->descripcion))
                    <div class="grid grid-cols-12 gap-4 items-start">
                        <div class="col-span-3 field-label">Descripción:</div>
                        <div class="col-span-9">
                            <span class="field-value">{{ $detalleMaterial->descripcion }}</span>
                        </div>
                    </div>
                    @endif
                    
                    @if(!empty($detalleMaterial->materias) && count($detalleMaterial->materias) > 0)
                    <div class="grid grid-cols-12 gap-4 items-start">
                        <div class="col-span-3 field-label">Materia(s):</div>
                        <div class="col-span-9">
                            @foreach($detalleMaterial->materias as $materia)
                                <span class="tag-text">{{ $materia }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    @if(!empty($detalleMaterial->series) && count($detalleMaterial->series) > 0)
                    <div class="grid grid-cols-12 gap-4 items-start">
                        <div class="col-span-3 field-label">Serie(s):</div>
                        <div class="col-span-9">
                            @foreach($detalleMaterial->series as $serie)
                                <span class="tag-text">{{ $serie }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    @if(!empty($detalleMaterial->dewey) && count($detalleMaterial->dewey) > 0)
                    <div class="grid grid-cols-12 gap-4 items-start">
                        <div class="col-span-3 field-label">Clasificación Dewey:</div>
                        <div class="col-span-9">
                            @foreach($detalleMaterial->dewey as $clasificacion)
                                <span class="tag-text">{{ $clasificacion }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    @if(!empty($detalleMaterial->idiomas) && count($detalleMaterial->idiomas) > 0)
                    <div class="grid grid-cols-12 gap-4 items-start">
                        <div class="col-span-3 field-label">Idioma(s):</div>
                        <div class="col-span-9">
                            @foreach($detalleMaterial->idiomas as $idioma)
                                <span class="tag-text">{{ $idioma }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    @if(!empty($detalleMaterial->isbn) && count($detalleMaterial->isbn) > 0)
                    <div class="grid grid-cols-12 gap-4 items-start">
                        <div class="col-span-3 field-label">ISBN:</div>
                        <div class="col-span-9">
                            @foreach($detalleMaterial->isbn as $isbn)
                                <span class="tag-text">{{ $isbn }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    @if(!empty($detalleMaterial->notas) && count($detalleMaterial->notas) > 0)
                    <div class="grid grid-cols-12 gap-4 items-start">
                        <div class="col-span-3 field-label">Nota(s):</div>
                        <div class="col-span-9">
                            @foreach($detalleMaterial->notas as $nota)
                                <div class="field-value mb-2">
                                    {{ $nota }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    @if(!empty($detalleMaterial->otros_titulos) && count($detalleMaterial->otros_titulos) > 0)
                    <div class="grid grid-cols-12 gap-4 items-start">
                        <div class="col-span-3 field-label">Otro(s) Título(s):</div>
                        <div class="col-span-9">
                            @foreach($detalleMaterial->otros_titulos as $otroTitulo)
                                <span class="tag-text">{{ $otroTitulo }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        </div>
    </main>
</body>
</html>
