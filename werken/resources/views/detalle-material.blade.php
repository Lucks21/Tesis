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
        
        .btn-action {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            font-family: 'Tipo-UBB', sans-serif;
        }
        
        .btn-back {
            color: var(--ubb-azul-secundario);
            background: var(--ubb-azul-claro);
            border: 1px solid var(--ubb-azul-secundario);
        }
        
        .btn-export {
            background: var(--ubb-azul-principal);
            color: white;
            border: none;
        }
        
        .card {
            background: white;
            border-radius: 16px;
            margin-bottom: 2rem;
            overflow: hidden;
            border: 1px solid var(--ubb-gris-medio);
        }
        
        .card-header {
            background: var(--ubb-azul-principal);
            color: white;
            padding: 1.5rem 2rem;
            font-weight: bold;
            font-size: 1.25rem;
            font-family: 'Tipo-UBB', sans-serif;
            position: relative;
        }
        
        .card-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--ubb-azul-secundario);
        }
        
        .card-content {
            padding: 2rem;
            background: white;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .info-column {
            space-y: 1.5rem;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
            padding: 1rem;
            border-radius: 8px;
            background: var(--ubb-gris-claro);
            border-left: 4px solid var(--ubb-azul-secundario);
        }
        
        .info-label {
            font-weight: bold;
            color: var(--ubb-azul-principal);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            font-family: 'Tipo-UBB', sans-serif;
        }
        
        .info-value {
            color: var(--ubb-texto-oscuro);
            font-size: 1rem;
            line-height: 1.5;
        }
        
        .info-value.destacado {
            color: var(--ubb-azul-secundario);
            font-weight: 600;
        }
        
        .existencias-table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .existencias-table th {
            background: var(--ubb-azul-principal);
            color: white;
            padding: 1rem 0.75rem;
            text-align: left;
            font-size: 0.9rem;
            font-weight: bold;
            font-family: 'Tipo-UBB', sans-serif;
            border: none;
        }
        
        .existencias-table td {
            padding: 1rem 0.75rem;
            font-size: 0.9rem;
            border-bottom: 1px solid var(--ubb-gris-medio);
        }
        
        .fila-par {
            background-color: #ffffff;
        }
        
        .fila-impar {
            background-color: var(--ubb-gris-claro);
        }
        
        .btn-reservar {
            background: var(--ubb-verde);
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            font-size: 0.8rem;
            cursor: pointer;
            font-weight: 600;
            font-family: 'Tipo-UBB', sans-serif;
        }
        
        .estado-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-family: 'Tipo-UBB', sans-serif;
        }
        
        .estado-disponible {
            background: var(--ubb-verde);
            color: white;
        }
        
        .estado-prestado {
            background: var(--ubb-rojo);
            color: white;
        }
        
        .estado-reservado {
            background: var(--ubb-amarillo);
            color: white;
        }
        
        .estado-sin-info {
            background: var(--ubb-gris-medio);
            color: var(--ubb-texto-oscuro);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
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
        
        /* Main header */
        .main-header {
            background: white;
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
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: normal;
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
            font-family: 'Tipo-UBB', sans-serif;
            font-weight: bold;
            min-width: 160px;
            text-align: center;
        }

        .search-button i {
            margin-right: 0.5rem;
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
        
        @media (max-width: 768px) {
            .logo-group {
                flex-direction: column;
                gap: 1rem;
            }
            
            .nav-content {
                flex-direction: column;
                gap: 1rem;
            }
            
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .search-actions {
                flex-direction: column;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .card-content {
                padding: 1rem;
            }
            
            .existencias-table {
                font-size: 0.8rem;
            }
            
            .existencias-table th,
            .existencias-table td {
                padding: 0.5rem 0.4rem;
            }
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
                    <i class="fas fa-search"></i>Búsqueda Simple
                </a>
                <a href="{{ route('busqueda-avanzada') }}" class="search-button">
                    <i class="fas fa-filter"></i>Búsqueda Avanzada
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

            <div>
        <!-- Resumen Bibliográfico -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-book" style="margin-right: 0.5rem;"></i>
                Resumen Bibliográfico
            </div>
            
            <div class="card-content">
                <div style="display: flex; flex-direction: column; gap: 1rem; max-width: 800px;">
                    <div style="display: flex; align-items: flex-start;">
                        <span style="font-weight: bold; color: var(--ubb-azul-principal); min-width: 180px; margin-right: 1rem;">Nro. de Pedido :</span>
                        <span style="color: var(--ubb-texto-oscuro);">{{ $detalleMaterial->nro_pedido ?? 'No disponible' }}</span>
                    </div>
                    
                    <div style="display: flex; align-items: flex-start;">
                        <span style="font-weight: bold; color: var(--ubb-azul-principal); min-width: 180px; margin-right: 1rem;">Autor :</span>
                        <span style="color: var(--ubb-texto-oscuro);">{{ $detalleMaterial->autor ?? 'No disponible' }}</span>
                    </div>
                    
                    <div style="display: flex; align-items: flex-start;">
                        <span style="font-weight: bold; color: var(--ubb-azul-principal); min-width: 180px; margin-right: 1rem;">Título :</span>
                        <span style="color: var(--ubb-azul-secundario); font-weight: 600;">{{ $detalleMaterial->titulo ?? 'No disponible' }}</span>
                    </div>
                    
                    <div style="display: flex; align-items: flex-start;">
                        <span style="font-weight: bold; color: var(--ubb-azul-principal); min-width: 180px; margin-right: 1rem;">Edición :</span>
                        <span style="color: var(--ubb-texto-oscuro);">{{ $detalleMaterial->edicion ?? 'No disponible' }}</span>
                    </div>
                    
                    <div style="display: flex; align-items: flex-start;">
                        <span style="font-weight: bold; color: var(--ubb-azul-principal); min-width: 180px; margin-right: 1rem;">Datos de Publicación :</span>
                        <span style="color: var(--ubb-texto-oscuro);">{{ $detalleMaterial->datos_publicacion ?? 'No disponible' }}</span>
                    </div>
                    
                    <div style="display: flex; align-items: flex-start;">
                        <span style="font-weight: bold; color: var(--ubb-azul-principal); min-width: 180px; margin-right: 1rem;">Descripción :</span>
                        <span style="color: var(--ubb-texto-oscuro);">{{ $detalleMaterial->descripcion ?? 'No disponible' }}</span>
                    </div>
                    
                    <div style="display: flex; align-items: flex-start;">
                        <span style="font-weight: bold; color: var(--ubb-azul-principal); min-width: 180px; margin-right: 1rem;">Materia(s) :</span>
                        <span style="color: var(--ubb-azul-secundario); font-weight: 600; text-transform: uppercase;">{{ $detalleMaterial->materiales ?? 'No disponible' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Existencias -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-warehouse" style="margin-right: 0.5rem;"></i>
                Existencias
            </div>
            
            <div class="card-content" style="padding: 0;">
                <div style="overflow-x: auto;">
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
                                            @if(isset($existencia->nombre_tb_estado) && $existencia->nombre_tb_estado == 'DISPONIBLE')
                                                <button class="btn-reservar">
                                                    <i class="fas fa-bookmark" style="margin-right: 0.3rem;"></i>
                                                    Reservar
                                                </button>
                                            @else
                                                <span style="color: #64748b; font-size: 0.8rem;">No disponible</span>
                                            @endif
                                        </td>
                                        <td>{{ $existencia->nombre_tb_campus ?? $existencia->ubicacion ?? 'No disponible' }}</td>
                                        <td>{{ $existencia->nro_volumen_existe ?? $existencia->volumen ?? '-' }}</td>
                                        <td>{{ $existencia->nro_parte_existe ?? $existencia->parte ?? '-' }}</td>
                                        <td>{{ $existencia->nro_suplemento_existe ?? $existencia->suplemento ?? '-' }}</td>
                                        <td>{{ $existencia->dias ?? $existencia->dias_prestamo ?? '-' }}</td>
                                        <td>{{ $existencia->nombre_tb_format ?? $existencia->formato ?? 'Libro' }}</td>
                                        <td>
                                            <span class="estado-badge
                                                @if(isset($existencia->nombre_tb_estado))
                                                    @if($existencia->nombre_tb_estado == 'DISPONIBLE')
                                                        estado-disponible
                                                    @elseif($existencia->nombre_tb_estado == 'PRESTADO')
                                                        estado-prestado
                                                    @elseif($existencia->nombre_tb_estado == 'RESERVADO')
                                                        estado-reservado
                                                    @else
                                                        estado-sin-info
                                                    @endif
                                                @else
                                                    estado-sin-info
                                                @endif
                                            ">
                                                {{ $existencia->nombre_tb_estado ?? $existencia->estado ?? 'Sin información' }}
                                            </span>
                                        </td>
                                        <td>{{ $existencia->Total ?? $existencia->copias ?? '1' }}</td>
                                        <td>
                                            @if(isset($existencia->fecha_dev) && $existencia->fecha_dev != '-')
                                                {{ \Carbon\Carbon::parse($existencia->fecha_dev)->format('d/m/Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="fila-par">
                                    <td>
                                        <span style="color: #64748b; font-size: 0.8rem;">No disponible</span>
                                    </td>
                                    <td>No disponible</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>Libro</td>
                                    <td>
                                        <span class="estado-badge estado-sin-info">
                                            Sin información
                                        </span>
                                    </td>
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
    </main>
</body>
</html>
