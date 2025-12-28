<?php
header('Content-Type: application/json');
require_once 'main.php';

$response = [];

try {
    // Verificar si las tablas existen
    $tables = ['categorias', 'productos', 'kits_detalles'];
    
    foreach ($tables as $table) {
        $result = $con->query("SHOW TABLES LIKE '$table'");
        $exists = $result->num_rows > 0;
        $response['tablas'][$table] = $exists ? 'EXISTE' : 'NO EXISTE';
        
        if ($exists) {
            $count_result = $con->query("SELECT COUNT(*) as total FROM $table");
            $count_row = $count_result->fetch_assoc();
            $response['conteo'][$table] = $count_row['total'];
        }
    }
    
    // Mostrar estructura de categorías si existe
    if ($response['tablas']['categorias'] === 'EXISTE') {
        $structure = $con->query("DESCRIBE categorias");
        $response['estructura_categorias'] = [];
        while ($row = $structure->fetch_assoc()) {
            $response['estructura_categorias'][] = $row;
        }
    }
    
    // Mostrar algunos productos si existen
    if ($response['tablas']['productos'] === 'EXISTE') {
        $products = $con->query("SELECT id_producto, nombre, puntos_requeridos, stock FROM productos LIMIT 5");
        $response['productos_ejemplo'] = [];
        while ($row = $products->fetch_assoc()) {
            $response['productos_ejemplo'][] = $row;
        }
    }

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>