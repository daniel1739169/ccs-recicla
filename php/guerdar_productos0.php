<?php
require_once 'validate_session.php';
require_once 'main.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre_producto'] ?? '';
    $categoria_id = $_POST['categoria_producto'] ?? '';
    $puntos = $_POST['puntos_producto'] ?? 0;
    $stock = $_POST['stock_producto'] ?? 0;
    $estado = $_POST['estado_producto'] ?? 'activo';
    $descripcion = $_POST['descripcion_producto'] ?? '';
    
    try {
        // Usar MySQLi - ajustar según tu tabla real
        $sql = "INSERT INTO productos (nombre, id_categoria, puntos_requeridos, stock, activo, descripcion) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $con->prepare($sql);
        $activo = ($estado === 'activo') ? 1 : 0;
        $stmt->bind_param("sidiis", $nombre, $categoria_id, $puntos, $stock, $activo, $descripcion);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Producto guardado correctamente']);
        } else {
            throw new Exception($con->error);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error al guardar el producto: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>