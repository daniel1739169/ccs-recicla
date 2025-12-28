<?php
header('Content-Type: application/json');
require_once 'main.php';

try {
    // Verificar si la tabla categorías tiene datos
    $check = $con->query("SELECT COUNT(*) as total FROM categorias");
    $row = $check->fetch_assoc();
    
    if ($row['total'] == 0) {
        // Agregar categorías de prueba
        $categorias = [
            "Deportes",
            "Limpieza", 
            "Ferretería",
            "Oficina",
            "Jardinería"
        ];
        
        foreach ($categorias as $categoria) {
            $con->query("INSERT INTO categorias (nombre) VALUES ('$categoria')");
        }
        
        echo json_encode(['success' => true, 'message' => 'Categorías de prueba agregadas']);
    } else {
        echo json_encode(['success' => true, 'message' => 'Ya existen categorías: ' . $row['total']]);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>