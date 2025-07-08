<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen Completo - Sistema de Bibliotecas UBB</title>
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
        
        .texto-azul {
            color: #0066cc;
        }
        
        .campo-info {
            margin-bottom: 8px;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">
    <!-- Encabezado de la página -->
    <div class="page-header">
        <div class="container mx-auto px-6">
            <h1 class="text-3xl font-bold mb-2">Resumen Completo</h1>
            <p class="text-blue-100">Sistema de Bibliotecas UBB</p>
        </div>
    </div>

    <!-- Barra de navegación -->
    <div class="container mx-auto px-6 mb-6">
        <a href="{{ route('material.detalle', ['numero' => $detalleMaterial->nro_control]) }}" 
           class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>Volver al detalle
        </a>
    </div>

    <div class="container mx-auto px-6">
        <!-- Resumen Bibliográfico Completo -->
        <div class="mb-6">
            <div class="resumen-header">
                Resumen Bibliográfico Completo
            </div>
            
            <div class="resumen-content">
                <div class="space-y-4">
                    <div class="campo-info">
                        <span class="font-semibold texto-azul">Nro. de Control:</span>
                        <span class="ml-2">{{ $detalleMaterial->nro_control }}</span>
                    </div>
                    
                    <div class="campo-info">
                        <span class="font-semibold texto-azul">Nro. de Pedido:</span>
                        <span class="ml-2">{{ $detalleMaterial->nro_pedido ?? 'No disponible' }}</span>
                    </div>
                    
                    <div class="campo-info">
                        <span class="font-semibold texto-azul">Autor:</span>
                        <span class="ml-2">{{ $detalleMaterial->autor }}</span>
                    </div>
                    
                    <div class="campo-info">
                        <span class="font-semibold texto-azul">Título:</span>
                        <span class="ml-2">{{ $detalleMaterial->titulo }}</span>
                    </div>
                    
                    <div class="campo-info">
                        <span class="font-semibold texto-azul">Edición:</span>
                        <span class="ml-2">{{ $detalleMaterial->edicion ?? 'No disponible' }}</span>
                    </div>
                    
                    <div class="campo-info">
                        <span class="font-semibold texto-azul">Datos de Publicación:</span>
                        <span class="ml-2">{{ $detalleMaterial->datos_publicacion ?? 'No disponible' }}</span>
                    </div>
                    
                    <div class="campo-info">
                        <span class="font-semibold texto-azul">Descripción:</span>
                        <span class="ml-2">{{ $detalleMaterial->descripcion ?? 'No disponible' }}</span>
                    </div>
                    
                    <div class="campo-info">
                        <span class="font-semibold texto-azul">Material(s):</span>
                        <span class="ml-2 text-blue-600 font-semibold">{{ $detalleMaterial->materiales ?? 'No disponible' }}</span>
                    </div>
                    
                    @if($detalleMaterial->isbn_issn)
                    <div class="campo-info">
                        <span class="font-semibold texto-azul">ISBN/ISSN:</span>
                        <span class="ml-2">{{ $detalleMaterial->isbn_issn }}</span>
                    </div>
                    @endif
                    
                    @if($detalleMaterial->serie)
                    <div class="campo-info">
                        <span class="font-semibold texto-azul">Serie:</span>
                        <span class="ml-2">{{ $detalleMaterial->serie }}</span>
                    </div>
                    @endif
                    
                    @if($detalleMaterial->editorial)
                    <div class="campo-info">
                        <span class="font-semibold texto-azul">Editorial:</span>
                        <span class="ml-2">{{ $detalleMaterial->editorial }}</span>
                    </div>
                    @endif
                    
                    @if($detalleMaterial->materia)
                    <div class="campo-info">
                        <span class="font-semibold texto-azul">Materia:</span>
                        <span class="ml-2">{{ $detalleMaterial->materia }}</span>
                    </div>
                    @endif
                    
                    @if($detalleMaterial->clasificacion_dewey)
                    <div class="campo-info">
                        <span class="font-semibold texto-azul">Clasificación Dewey:</span>
                        <span class="ml-2">{{ $detalleMaterial->clasificacion_dewey }}</span>
                    </div>
                    @endif
                    
                    @if($detalleMaterial->encabezamientos_materia)
                    <div class="campo-info">
                        <span class="font-semibold texto-azul">Encabezamientos de Materia:</span>
                        <span class="ml-2">{{ $detalleMaterial->encabezamientos_materia }}</span>
                    </div>
                    @endif
                    
                    @if($detalleMaterial->notas)
                    <div class="campo-info">
                        <span class="font-semibold texto-azul">Notas:</span>
                        <span class="ml-2">{{ $detalleMaterial->notas }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Enlaces adicionales -->
        <div class="text-center mb-6">
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('detalle-material', ['numero' => $detalleMaterial->nro_control]) }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                    <i class="fas fa-arrow-left mr-2"></i>Volver al Detalle
                </a>
                <a href="{{ route('material.detalle.completo', ['numero' => $detalleMaterial->nro_control]) }}" 
                   class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm">
                    <i class="fas fa-info-circle mr-2"></i>Información Bibliográfica Completa
                </a>
            </div>
        </div>
    </div>
</body>
</html>
