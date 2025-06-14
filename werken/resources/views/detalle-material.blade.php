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
        
        .actions-bar {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem 0;
            margin-bottom: 2rem;
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
                <a href="{{ url()->previous() }}" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
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
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Información del Material</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">            <div class="grid md:grid-cols-2 gap-8">
                <!-- Información básica -->
                <div class="bg-white p-6 rounded-lg shadow-md space-y-4">
                    <h3 class="text-lg font-bold text-blue-800 border-b border-blue-200 pb-2 mb-4">
                        <i class="fas fa-info-circle mr-2"></i>Información Básica
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <span class="font-semibold text-gray-700">Título:</span>
                            <p class="mt-1">{{ $detalleMaterial->titulo }}</p>
                        </div>

                        <div>
                            <span class="font-semibold text-gray-700">Autor/Editor:</span>
                            <p class="mt-1">{{ $detalleMaterial->autor }}</p>
                        </div>

                        <div>
                            <span class="font-semibold text-gray-700">Editorial:</span>
                            <p class="mt-1">{{ $detalleMaterial->editorial }}</p>
                        </div>

                        <div>
                            <span class="font-semibold text-gray-700">ISBN/ISSN:</span>
                            <p class="mt-1">{{ $detalleMaterial->isbn_issn ?? 'No disponible' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="bg-white p-6 rounded-lg shadow-md space-y-4">
                    <h3 class="text-lg font-bold text-blue-800 border-b border-blue-200 pb-2 mb-4">
                        <i class="fas fa-book mr-2"></i>Detalles Adicionales
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <span class="font-semibold text-gray-700">Tipo de Material:</span>
                            <p class="mt-1">{{ $detalleMaterial->tipo_material }}</p>
                        </div>
                        
                        <div>
                            <span class="font-semibold text-gray-700">Biblioteca:</span>
                            <p class="mt-1">{{ $detalleMaterial->biblioteca }}</p>
                        </div>

                        @if($detalleMaterial->estado)
                        <div>
                            <span class="font-semibold text-gray-700">Estado:</span>
                            <p class="mt-1">
                                <span class="px-2 py-1 rounded text-sm {{ $detalleMaterial->estado == 'Disponible' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $detalleMaterial->estado }}
                                </span>
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                    <div>
                        <span class="font-semibold">Año de Publicación:</span>
                        <p>{{ $detalleMaterial->DSM_PUBLICACION }}</p>
                    </div>

                    <div>
                        <span class="font-semibold">Cantidad Disponible:</span>
                        <p>{{ $detalleMaterial->DSM_CANTIDAD_ORIGINAL }}</p>
                    </div>

                    @if($detalleMaterial->DSM_IND_SUSCRIPCION)
                        <div>
                            <span class="font-semibold">Suscripción:</span>
                            <p>Material por suscripción</p>
                        </div>
                    @endif

                    @if($detalleMaterial->DSM_OBSERVACION)
                        <div>
                            <span class="font-semibold">Observaciones:</span>
                            <p>{{ $detalleMaterial->DSM_OBSERVACION }}</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</body>
</html>
