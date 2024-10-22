<?php
require __DIR__ . '/../config/config.php'; // Asegúrate de que esta ruta apunte al archivo de configuración

try {
    // Si la conexión es exitosa, devuelve un mensaje de éxito en formato JSON
    echo json_encode([
        'status' => 'success',
        'message' => 'Conexión exitosa a la base de datos.'
    ]);
} catch (PDOException $e) {
    // Si hay un error en la conexión, devuelve un mensaje de error en formato JSON
    echo json_encode([
        'status' => 'error',
        'message' => 'Error al conectar a la base de datos: ' . $e->getMessage()
    ]);
}
