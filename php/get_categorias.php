<?php
// Peticiones AJAX deben recibir JSON — no redirecciones HTML al login
header('Content-Type: application/json');

// Comprobar sesión y responder con JSON si no está autenticado (evitar redirect HTML)
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    echo json_encode(['categorias' => [], 'error' => 'No autenticado']);
    exit();
}

require_once 'main.php';

try {
    // Usar MySQLi - ajustar según tu tabla real
    // Devolvemos 'id' y 'id_categoria' para compatibilidad con el frontend
    // No filtramos por 'estado' porque la tabla no tiene ese campo
    $sql = "SELECT id_categoria AS id, id_categoria, nombre FROM categorias ORDER BY nombre";
    $result = $con->query($sql);
    
    $categorias = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categorias[] = $row;
        }
    }
    
    echo json_encode(['categorias' => $categorias]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['categorias' => [], 'error' => $e->getMessage()]);
}

?>