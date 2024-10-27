<?php
//este archivo es para procesar el inicio de sesion, recibe datos del formulario, verifica
//las credenciales y, si son correctas, inicia la sesión
require '../config/config.php'; 


session_start(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rut_usuario = $_POST['rut_usuario'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT rut_usuario, rut FROM usuario WHERE rut_usuario = :rut_usuario");
    $stmt->bindParam(':rut_usuario', $rut_usuario);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $password === $user['rut']) {
        $_SESSION['rut_usuario'] = $user['rut_usuario'];
        
        header("Location: dashboard.php");
        exit;
    } else {
        echo "RUT o contraseña incorrectos.";
    }
}
?>
