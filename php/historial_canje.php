<?php
session_start();
require_once 'main.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID de organización no especificado']);
    exit;
}

$org_id = intval($_GET['id']);

try {
    // Usar tu conexión mysqli
    $sql = "SELECT 
                cr.id_canje,
                cr.fecha,
                cr.total_puntos,
                p.nombre AS producto_nombre,
                cd.cantidad
            FROM canje_realizado cr
            JOIN canje_detalle cd ON cr.id_canje = cd.id_canje
            JOIN productos p ON cd.id_producto = p.id_producto
            WHERE cr.id_organizacion = ?
            ORDER BY cr.fecha DESC";
    
    // Preparar la consulta
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error preparando consulta: " . $con->error);
    }
    
    $stmt->bind_param("i", $org_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $canjes = [];
    while ($row = $result->fetch_assoc()) {
        // Formatear la fecha
        $fecha = new DateTime($row['fecha']);
        $row['fecha_formateada'] = $fecha->format('d/m/Y H:i');
        $row['producto'] = $row['producto_nombre'] . ($row['cantidad'] > 1 ? ' x' . $row['cantidad'] : '');
        $canjes[] = $row;
    }
    
    $stmt->close();
    
    echo json_encode(['canjes' => $canjes]);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()]);
}
?>