<?php
require '../config/config.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // se obtienen datos y se eliminan espacios
    $rut_usuario = trim($_POST['rut_usuario']);
    $password = trim($_POST['password']);

    try {
        $stmt = $conn->prepare("SELECT rut_usuario, rut FROM usuario WHERE rut_usuario = :rut_usuario AND rut = :rut");
        $stmt->bindParam(':rut_usuario', $rut_usuario);
        $stmt->bindParam(':rut', $password);
        $stmt->execute();
        
        // se obtienen los datos del usuario
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "usuario: $user";
        if ($user) {
            // si es correcto, inicia sesion
            $_SESSION['rut_usuario'] = $user['rut_usuario'];
            header('Location: dashboard.php');
            exit;
        } else {
            echo "RUT o contraseña incorrectos.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

