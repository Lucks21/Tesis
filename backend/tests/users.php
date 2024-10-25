<?php
require '../config/config.php';

try {
    $stmt = $conn->query("SELECT rut_usuario, nombre_usuario FROM USUARIO");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($users);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
