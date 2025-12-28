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
    // Consulta para obtener reciclaje desde recibo_final
    $sql = "SELECT 
                rf.id,
                rf.correlativo,
                rf.fecha,
                rf.total_kg,
                rf.total_p,
                rf.responsable
            FROM recibo_final rf
            WHERE rf.id_organizacion = ? 
            AND rf.estado = 1
            ORDER BY rf.fecha DESC";
    
    // Preparar la consulta
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error preparando consulta: " . $con->error);
    }
    
    $stmt->bind_param("i", $org_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $reciclajes = [];
    while ($row = $result->fetch_assoc()) {
        // Obtener materiales para este recibo
        $sql_materiales = "SELECT 
                            m.descripcion,
                            mr.cantidad_kg
                          FROM material_refinal mr
                          JOIN material m ON mr.id_material = m.id
                          WHERE mr.id_refinal = ?";
        
        $stmt2 = $con->prepare($sql_materiales);
        $stmt2->bind_param("i", $row['id']);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        
        $materiales = [];
        while ($mat = $result2->fetch_assoc()) {
            $materiales[] = $mat['cantidad_kg'] . 'kg ' . $mat['descripcion'];
        }
        $stmt2->close();
        
        // Formatear datos
        $fecha = new DateTime($row['fecha']);
        $row['fecha_formateada'] = $fecha->format('d/m/Y');
        $row['puntos'] = floatval($row['total_p']);
        $row['materiales'] = implode(', ', $materiales);
        $row['responsable'] = $row['responsable'] ?: 'No especificado';
        
        $reciclajes[] = $row;
    }
    
    $stmt->close();
    
    echo json_encode(['reciclajes' => $reciclajes]);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()]);
}
?>