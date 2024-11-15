<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
</head>
<body>
    <h2>Iniciar Sesión</h2>
    <form action="{{ route('login') }}" method="POST">
        @csrf
        <label for="rut_usuario">RUT Usuario:</label>
        <input type="text" name="rut_usuario" required>
        
        <label for="password">Contraseña:</label>
        <input type="password" name="password" required>

        @if($errors->has('login_error'))
            <p>{{ $errors->first('login_error') }}</p>
        @endif

        <button type="submit">Iniciar Sesión</button>
    </form>
</body>
</html>
