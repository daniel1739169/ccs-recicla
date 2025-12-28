<?php

header('Content-Type: application/json');

// Asegúrate de que esta ruta sea correcta para acceder a tu archivo de conexión
require_once 'main.php'; 

// Solo procesar peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit();
}

// 1. Obtener y limpiar el nombre de la categoría (asume que se envía con clave 'nombre')
$nombre_categoria = isset($_POST['nombre']) ? limpiar_cadena($_POST['nombre']) : null;

// 2. Validación de datos
if (empty($nombre_categoria)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El nombre de la categoría es obligatorio.']);
    exit();
}



try {
    // 3. Preparar la consulta SQL para la inserción
    // Asume la tabla 'categorias' con columna 'nombre'
    $sql_insert = "INSERT INTO categorias (nombre) VALUES (?)";
    
    // Usamos prepared statements para mayor seguridad
    $stmt = $con->prepare($sql_insert);
    
    if ($stmt) {
        $stmt->bind_param("s", $nombre_categoria);
        
    if ($stmt->execute()) {
        $response = "OK"; // corregido
    } else {
        $response = 'Error al ejecutar la consulta: ' . $stmt->error;
        http_response_code(500);
    }

    } else {
        $response[] = 'Error al preparar la consulta: ' . $con->error;
        http_response_code(500);
    }

} catch (Exception $e) {
    $response = 'Excepción: ' . $e->getMessage();
    http_response_code(500);
}

echo $response;
exit();

?>