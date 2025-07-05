<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuenta Personal</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Tipo-UBB', Arial, sans-serif;
            background: #f8fafc;
        }
        .perfil-container {
            background: white;
            padding: 3rem 4rem;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.08);
            text-align: center;
        }
        .perfil-title {
            font-size: 2.5rem;
            color: #003876;
            font-weight: bold;
            margin-bottom: 2rem;
        }
        .logout-button {
            background-color: #003876;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: background-color 0.2s;
            font-family: 'Tipo-UBB', Arial, sans-serif;
            font-size: 1rem;
        }
        .logout-button:hover {
            background-color: #002b5c;
        }
    </style>
</head>
<body>    <div class="perfil-container">
        <div class="perfil-title">CUENTA PERSONAL</div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-button">
                <i class="fas fa-sign-out-alt mr-2"></i>
                Cerrar sesión
            </button>
        </form>
    </div>
</body>
</html>
