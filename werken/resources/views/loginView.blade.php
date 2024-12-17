<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">

    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Iniciar Sesión</h2>
        
        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="rut_usuario" class="block text-sm font-medium text-gray-700">RUT Usuario:</label>
                <input type="text" name="rut_usuario" class="w-full p-2 border rounded" required>
            </div>
            
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña:</label>
                <input type="password" name="password" class="w-full p-2 border rounded" required>
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                Iniciar Sesión
            </button>
        </form>
    </div>

</body>
</html>
