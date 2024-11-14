<?php
session_start();
//Esto es para verificar si el usuario ha iniciado sesión

if (!isset($_SESSION['rut_usuario'])) {
    header('Location: login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['rut_usuario']); ?>!</h1>
    
    <p>Este es tu panel principal. Aquí puedes acceder a diferentes opciones de tu cuenta.</p>
    
    <!-- Opciones de navegación -->
    <nav>
        <ul>
            <li><a href="reserva.php">Reservas</a></li>
        </ul>
    </nav>

    <!-- Botón de cerrar sesión -->
    <form action="logout.php" method="post">
        <button type="submit">Cerrar Sesión</button>
    </form>
</body>
</html>
