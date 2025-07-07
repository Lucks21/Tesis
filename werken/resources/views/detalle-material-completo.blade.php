<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información Bibliográfica Completa - Sistema de Bibliotecas UBB</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
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
        
        .info-section {
            background-color: white;
            border: 1px solid #003876;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .info-header {
            background-color: #003876;
            color: white;
            padding: 8px 16px;
            font-weight: bold;
            border-radius: 4px 4px 0 0;
        }
        
        .info-content {
            padding: 16px;
            background-color: #ffffff;
        }
        
        .campo-info {
            margin-bottom: 12px;
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .campo-info:last-child {
            border-bottom: none;
        }
        
        .label-campo {
            font-weight: bold;
            color: #003876;
            display: inline-block;
            min-width: 150px;
        }
        
        .valor-campo {
            color: #333;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">
    <!-- Encabezado de la página -->
    <div class="page-header">
        <div class="container mx-auto px-6">
            <h1 class="text-3xl font-bold mb-2">Información Bibliográfica Completa</h1>
            <p class="text-blue-100">Sistema de Bibliotecas UBB</p>
        </div>
    </div>

    <!-- Barra de navegación -->
    <div class="container mx-auto px-6 mb-6">
        <a href="{{ route('detalle-material', ['numero' => $detalleMaterial->nro_control]) }}" 
           class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>Volver al detalle
        </a>
    </div>

    <div class="container mx-auto px-6">
        <!-- Información Bibliográfica Completa -->
        <div class="info-section">
            <div class="info-header">
                Información Bibliográfica Completa
            </div>
            
            <div class="info-content">
                <div class="campo-info">
                    <span class="label-campo">Nro. de Control:</span>
                    <span class="valor-campo">{{ $detalleMaterial->nro_control }}</span>
                </div>
                
                <div class="campo-info">
                    <span class="label-campo">Nro. de Pedido:</span>
                    <span class="valor-campo">{{ $detalleMaterial->nro_pedido ?? 'No disponible' }}</span>
                </div>
                
                <div class="campo-info">
                    <span class="label-campo">Autor Principal:</span>
                    <span class="valor-campo">{{ $detalleMaterial->autor }}</span>
                </div>
                
                <div class="campo-info">
                    <span class="label-campo">Título Completo:</span>
                    <span class="valor-campo">{{ $detalleMaterial->titulo }}</span>
                </div>
                
                @if($detalleMaterial->edicion)
                <div class="campo-info">
                    <span class="label-campo">Edición:</span>
                    <span class="valor-campo">{{ $detalleMaterial->edicion }}</span>
                </div>
                @endif
                
                @if($detalleMaterial->datos_publicacion)
                <div class="campo-info">
                    <span class="label-campo">Datos de Publicación:</span>
                    <span class="valor-campo">{{ $detalleMaterial->datos_publicacion }}</span>
                </div>
                @endif
                
                @if($detalleMaterial->descripcion)
                <div class="campo-info">
                    <span class="label-campo">Descripción Física:</span>
                    <span class="valor-campo">{{ $detalleMaterial->descripcion }}</span>
                </div>
                @endif
                
                @if($detalleMaterial->materiales)
                <div class="campo-info">
                    <span class="label-campo">Material(s):</span>
                    <span class="valor-campo text-blue-600">{{ $detalleMaterial->materiales }}</span>
                </div>
                @endif
                
                @if($detalleMaterial->isbn_issn)
                <div class="campo-info">
                    <span class="label-campo">ISBN/ISSN:</span>
                    <span class="valor-campo">{{ $detalleMaterial->isbn_issn }}</span>
                </div>
                @endif
                
                @if($detalleMaterial->serie)
                <div class="campo-info">
                    <span class="label-campo">Serie:</span>
                    <span class="valor-campo">{{ $detalleMaterial->serie }}</span>
                </div>
                @endif
                
                @if($detalleMaterial->editorial)
                <div class="campo-info">
                    <span class="label-campo">Editorial:</span>
                    <span class="valor-campo">{{ $detalleMaterial->editorial }}</span>
                </div>
                @endif
                
                @if($detalleMaterial->materia)
                <div class="campo-info">
                    <span class="label-campo">Materia:</span>
                    <span class="valor-campo">{{ $detalleMaterial->materia }}</span>
                </div>
                @endif
                
                @if($detalleMaterial->clasificacion_dewey)
                <div class="campo-info">
                    <span class="label-campo">Clasificación Dewey:</span>
                    <span class="valor-campo">{{ $detalleMaterial->clasificacion_dewey }}</span>
                </div>
                @endif
                
                @if($detalleMaterial->encabezamientos_materia)
                <div class="campo-info">
                    <span class="label-campo">Encabezamientos de Materia:</span>
                    <span class="valor-campo">{{ $detalleMaterial->encabezamientos_materia }}</span>
                </div>
                @endif
                
                @if($detalleMaterial->notas)
                <div class="campo-info">
                    <span class="label-campo">Notas:</span>
                    <span class="valor-campo">{{ $detalleMaterial->notas }}</span>
                </div>
                @endif
                
                <div class="campo-info">
                    <span class="label-campo">Tipo de Material:</span>
                    <span class="valor-campo">{{ $detalleMaterial->tipo_material ?? 'No especificado' }}</span>
                </div>
                
                <div class="campo-info">
                    <span class="label-campo">Biblioteca:</span>
                    <span class="valor-campo">{{ $detalleMaterial->biblioteca ?? 'No especificada' }}</span>
                </div>
                
                <div class="campo-info">
                    <span class="label-campo">Estado:</span>
                    <span class="valor-campo">
                        <span class="px-2 py-1 rounded text-xs {{ $detalleMaterial->estado == 'Disponible' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $detalleMaterial->estado ?? 'No especificado' }}
                        </span>
                    </span>
                </div>
            </div>
        </div>

        @if(isset($informacionCompleta) && $informacionCompleta)
        <!-- Información Adicional -->
        <div class="info-section">
            <div class="info-header">
                Información Adicional
            </div>
            
            <div class="info-content">
                @if($informacionCompleta->campos_adicionales)
                <div class="campo-info">
                    <span class="label-campo">Campos Adicionales:</span>
                    <span class="valor-campo">{{ $informacionCompleta->campos_adicionales }}</span>
                </div>
                @endif
                
                @if($informacionCompleta->enlaces_relacionados)
                <div class="campo-info">
                    <span class="label-campo">Enlaces Relacionados:</span>
                    <span class="valor-campo">{{ $informacionCompleta->enlaces_relacionados }}</span>
                </div>
                @endif
                
                @if($informacionCompleta->historia_catalogacion)
                <div class="campo-info">
                    <span class="label-campo">Historia de Catalogación:</span>
                    <span class="valor-campo">{{ $informacionCompleta->historia_catalogacion }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Enlaces adicionales -->
        <div class="text-center mb-6">
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('detalle-material', ['numero' => $detalleMaterial->nro_control]) }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                    <i class="fas fa-arrow-left mr-2"></i>Volver al Detalle
                </a>
                <a href="{{ route('material.resumen', ['numero' => $detalleMaterial->nro_control]) }}" 
                   class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm">
                    <i class="fas fa-file-alt mr-2"></i>Ver Resumen
                </a>
                <a href="{{ route('export.ris', ['nroControl' => $detalleMaterial->nro_control]) }}" 
                   class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors text-sm">
                    <i class="fas fa-file-export mr-2"></i>Exportar RIS
                </a>
            </div>
        </div>
    </div>
</body>
</html>
