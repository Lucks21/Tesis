<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar Sesión - Biblioteca UBB</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Iniciar Sesión</h2>
        
        <form action="{{ route('login') }}" method="POST">
            @csrf

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div id="login-error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded hidden">
                Usuario o contraseña incorrectos
            </div>

            <div class="mb-4">
                <label for="rut_usuario" class="block text-sm font-medium text-gray-700">RUT Usuario:</label>
                <input type="text" 
                       id="rut_usuario"
                       name="rut_usuario" 
                       class="w-full p-2 border rounded @error('rut_usuario') border-red-500 @enderror" 
                       value="{{ old('rut_usuario') }}"
                       required>
                @error('rut_usuario')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña:</label>
                <input type="password" 
                       id="password"
                       name="password" 
                       class="w-full p-2 border rounded @error('password') border-red-500 @enderror" 
                       required>
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                Iniciar Sesión
            </button>
        </form>
    </div>
    <script src="{{ asset('js/loginDropdown.js') }}"></script>
</body>
</html>
