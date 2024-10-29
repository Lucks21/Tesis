<?php
require '../config/config.php';

try {
    $stmt = $conn->query("SELECT * FROM USUARIO_PERFIL");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($users);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
