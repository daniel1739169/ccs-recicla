<?php
// Peticiones AJAX deben recibir JSON — evitar redirecciones HTML al login
header('Content-Type: application/json');
require_once 'main.php';

try {
    // Usar MySQLi en lugar de PDO
    // Devolver todas las organizaciones (sin filtrar por estado) para mostrar en el selector.
    // Si necesitas filtrar por estado, se puede añadir un parámetro opcional más adelante.
    $sql = "SELECT id, nombre FROM organizacion ORDER BY nombre";
    $result = $con->query($sql);
    
    $organizaciones = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $organizaciones[] = $row;
        }
    }
    
    echo json_encode(['organizaciones' => $organizaciones]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['organizaciones' => [], 'error' => $e->getMessage()]);
}
?>