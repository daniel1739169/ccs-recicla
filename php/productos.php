<?php
// php/productos.php - VERSIÓN CORREGIDA
header('Content-Type: application/json');
require_once 'main.php';

$response = [
    'categorias' => [],
    'productos' => [],
    'kits_detalles' => [],
    'error' => null
];

try {
    // 1. Obtener Categorías
    $sql_categorias = "SELECT id_categoria AS id, nombre FROM categorias ORDER BY nombre";
    $result_categorias = $con->query($sql_categorias);
    
    if ($result_categorias && $result_categorias->num_rows > 0) {
        while ($row = $result_categorias->fetch_assoc()) {
            $response['categorias'][] = $row;
        }
    } else {
        $response['categorias'] = [
            ['id' => 1, 'nombre' => 'Productos Generales']
        ];
    }

    // 2. Obtener Productos - CORREGIDO según tu BD
    $sql_productos = "SELECT 
            id_producto AS id, 
            nombre, 
            descripcion, 
            puntos_requeridos AS puntos,
            COALESCE(id_categoria, 1) AS cat,
            es_kit,
            COALESCE(stock, 0) as stock
        FROM productos 
        WHERE activo = 1 
        ORDER BY nombre";
        
    $result_productos = $con->query($sql_productos);
    
    if ($result_productos) {
        while ($row = $result_productos->fetch_assoc()) {
            $response['productos'][] = $row;
        }
    }

    // 3. Obtener Detalles de Kits - CORREGIDO según tu BD
    $sql_kits_detalles = "SELECT 
            id_kit AS kitId, 
            id_producto AS prodId, 
            cantidad 
        FROM kits_detalles";
        
    $result_kits_detalles = $con->query($sql_kits_detalles);
    
    if ($result_kits_detalles) {
        while ($row = $result_kits_detalles->fetch_assoc()) {
            $response['kits_detalles'][] = $row;
        }
    }

} catch (Exception $e) {
    $response['error'] = 'Error al consultar la base de datos: ' . $e->getMessage();
}

echo json_encode($response);
$con->close();
?>