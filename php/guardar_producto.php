<?php
// wrapper para aceptar POST desde el frontend y devolver JSON
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit();
}

require_once 'main.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre_producto'] ?? '';
    $categoria_id = $_POST['categoria_producto'] ?? '';
    $puntos = $_POST['puntos_producto'] ?? 0;
    $stock = $_POST['stock_producto'] ?? 0;
    $estado = $_POST['estado_producto'] ?? 'activo';
    $descripcion = $_POST['descripcion_producto'] ?? '';

    try {
        $sql = "INSERT INTO productos (nombre, id_categoria, puntos_requeridos, stock, activo, descripcion) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("sidiis", $nombre, $categoria_id, $puntos, $stock, $estado, $descripcion);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Producto guardado correctamente']);
        } else {
            throw new Exception($con->error);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al guardar el producto: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>