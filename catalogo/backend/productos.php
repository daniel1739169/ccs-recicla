<?php
// catalogo/backend/productos.php
header('Content-Type: application/json');

// Ajusta la ruta a tu archivo de conexión (asumimos que está en ../../php/conn.php)
require_once '../../php/main.php'; 

$response = [
    'categorias' => [],
    'productos' => [],
    'kits_detalles' => [],
    'error' => null
];

try {
    // 1. Obtener Categorías
    // Asume la tabla 'categorias'
    $sql_categorias = "SELECT id_categoria AS id, nombre FROM categorias ORDER BY nombre";
    $result_categorias = $con->query($sql_categorias);
    while ($row = $result_categorias->fetch_assoc()) {
        $response['categorias'][] = $row;
    }

    // 2. Obtener Productos
    // Asume la tabla 'productos' con campos de puntos y stock
    $sql_productos = "SELECT id_producto AS id, nombre, descripcion, puntos_requeridos, stock, id_categoria 
                      FROM productos 
                      WHERE activo = 1 
                      ORDER BY nombre";
    $result_productos = $con->query($sql_productos);
    while ($row = $result_productos->fetch_assoc()) {
        $response['productos'][] = $row;
    }

    // 3. Obtener Detalles de Kits
    // Asume que tienes tablas 'kits' y 'kits_detalles'
    $sql_kits_detalles = "SELECT kd.id_kit, kd.id_producto, kd.cantidad, p.nombre AS producto_nombre, k.nombre AS kit_nombre
                          FROM kits_detalles kd
                          JOIN productos p ON p.id_producto = kd.id_producto
                          JOIN kits k ON k.id_kit = kd.id_kit"; 
    $result_kits_detalles = $con->query($sql_kits_detalles);
    while ($row = $result_kits_detalles->fetch_assoc()) {
        $response['kits_detalles'][] = $row;
    }

} catch (Exception $e) {
    $response['error'] = 'Error en la base de datos: ' . $e->getMessage();
    http_response_code(500);
} finally {
    if (isset($con)) $con->close();
}

echo json_encode($response);
?>