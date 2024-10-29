<?php
require '../config/config.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario y eliminar espacios adicionales
    $rut_usuario = trim($_POST['rut_usuario']);
    $password = trim($_POST['password']);

    try {
        // Consulta SQL para buscar al usuario directamente con rut_usuario y rut
        $stmt = $conn->prepare("SELECT rut_usuario, rut FROM usuario WHERE rut_usuario = :rut_usuario AND rut = :rut");
        $stmt->bindParam(':rut_usuario', $rut_usuario);
        $stmt->bindParam(':rut', $password); // Compara directamente el rut con la "contraseña"
        $stmt->execute();
        
        // Obtener los datos del usuario
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "usuario: $user";
        if ($user) {
            // Si el usuario existe y la contraseña es correcta, iniciar sesión
            $_SESSION['rut_usuario'] = $user['rut_usuario'];
            header('Location: dashboard.php');
            exit;
        } else {
            // Si no coincide, mostrar mensaje de error
            echo "RUT o contraseña incorrectos.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
