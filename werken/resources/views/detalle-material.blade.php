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
        
        .aviso-informativo {
            background-color: #e8f4fd;
            border: 1px solid #bde3ff;
            color: #1e40af;
            padding: 12px 16px;
            border-radius: 6px;
            margin: 16px 0;
            font-size: 14px;
        }
        
        .aviso-informativo .titulo-aviso {
            font-weight: bold;
            margin-bottom: 4px;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">
    <!-- Encabezado de la página -->
    <div class="page-header">
        <div class="container mx-auto px-6">
            <h1 class="text-3xl font-bold mb-2">Detalle del Material</h1>
            <p class="text-blue-100">Sistema de Bibliotecas UBB</p>
        </div>
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
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Columna 1: Información básica -->
                    <div class="space-y-2">
                        <div>
                            <span class="font-semibold texto-azul">Nro. de Control :</span>
                            <span class="ml-2">{{ $detalleMaterial->nro_control ?? 'No disponible' }}</span>
                        </div>
                        
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
                            <span class="font-semibold texto-azul">Título Normalizado :</span>
                            <span class="ml-2 text-sm text-gray-600">{{ $detalleMaterial->titulo_normalizado ?? 'No disponible' }}</span>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Editorial :</span>
                            <span class="ml-2">{{ $detalleMaterial->editorial ?? 'No disponible' }}</span>
                        </div>
                    </div>
                    
                    <!-- Columna 2: Detalles de publicación -->
                    <div class="space-y-2">
                        <div>
                            <span class="font-semibold texto-azul">ISBN/ISSN :</span>
                            <span class="ml-2">{{ $detalleMaterial->isbn_issn ?? 'No disponible' }}</span>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Datos de Publicación :</span>
                            <span class="ml-2">{{ $detalleMaterial->datos_publicacion ?? 'No disponible' }}</span>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Tipo de Material :</span>
                            <span class="ml-2">{{ $detalleMaterial->tipo ?? 'No disponible' }}</span>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Clasificación Dewey :</span>
                            <span class="ml-2">{{ $detalleMaterial->dewey ?? 'No disponible' }}</span>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Material(s) :</span>
                            <span class="ml-2 text-blue-600 font-semibold">{{ $detalleMaterial->materiales ?? 'No disponible' }}</span>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Suscripción :</span>
                            <span class="ml-2">{{ $detalleMaterial->suscripcion ?? 'No' }}</span>
                        </div>
                    </div>
                    
                    <!-- Columna 3: Información técnica -->
                    <div class="space-y-2">
                        <div>
                            <span class="font-semibold texto-azul">Copias Registradas :</span>
                            <span class="ml-2">{{ $detalleMaterial->copias_registradas ?? 'No disponible' }}</span>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Catalogador :</span>
                            <span class="ml-2">{{ $detalleMaterial->catalogador ?? 'No disponible' }}</span>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Fecha de Ingreso :</span>
                            <span class="ml-2">
                                @if(isset($detalleMaterial->fecha_ingreso) && $detalleMaterial->fecha_ingreso != 'No disponible')
                                    {{ \Carbon\Carbon::parse($detalleMaterial->fecha_ingreso)->format('d/m/Y') }}
                                @else
                                    No disponible
                                @endif
                            </span>
                        </div>
                        
                        <div>
                            <span class="font-semibold texto-azul">Descripción :</span>
                            <span class="ml-2 text-sm">{{ $detalleMaterial->descripcion ?? 'No disponible' }}</span>
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
                                        @if(isset($existencia->nombre_tb_estado) && $existencia->nombre_tb_estado == 'DISPONIBLE')
                                            <button class="btn-reservar">
                                                Reservar
                                            </button>
                                        @else
                                            <span class="text-gray-500 text-xs">No disponible</span>
                                        @endif
                                    </td>
                                    <td>{{ $existencia->nombre_tb_campus ?? $existencia->ubicacion ?? 'No disponible' }}</td>
                                    <td>{{ $existencia->nro_volumen_existe ?? $existencia->volumen ?? '-' }}</td>
                                    <td>{{ $existencia->nro_parte_existe ?? $existencia->parte ?? '-' }}</td>
                                    <td>{{ $existencia->nro_suplemento_existe ?? $existencia->suplemento ?? '-' }}</td>
                                    <td>{{ $existencia->dias ?? $existencia->dias_prestamo ?? '-' }}</td>
                                    <td>{{ $existencia->nombre_tb_format ?? $existencia->formato ?? 'Libro' }}</td>
                                    <td>
                                        <span class="px-2 py-1 rounded text-xs
                                            @if(isset($existencia->nombre_tb_estado))
                                                @if($existencia->nombre_tb_estado == 'DISPONIBLE')
                                                    bg-green-100 text-green-800
                                                @elseif($existencia->nombre_tb_estado == 'PRESTADO')
                                                    bg-red-100 text-red-800
                                                @elseif($existencia->nombre_tb_estado == 'RESERVADO')
                                                    bg-yellow-100 text-yellow-800
                                                @else
                                                    bg-gray-100 text-gray-800
                                                @endif
                                            @else
                                                bg-gray-100 text-gray-800
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
                                    <span class="text-gray-500 text-xs">No disponible</span>
                                </td>
                                <td>No disponible</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>Libro</td>
                                <td>
                                    <span class="px-2 py-1 rounded text-xs bg-gray-100 text-gray-800">
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
</body>
</html>
