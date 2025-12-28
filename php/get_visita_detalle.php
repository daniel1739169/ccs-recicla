<?php
// get_visita_detalle.php - VERSIÓN SIMPLIFICADA
header('Content-Type: application/json');

// Incluir conexión
include_once 'main.php';

// Verificar que se recibió el ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de visita no válido'
    ]);
    exit;
}

$idVisita = intval($_GET['id']);

// Consulta simplificada
$sql = "SELECT 
            v.id,
            v.responsable,
            v.cargo,
            v.fecha,
            v.estado,
            o.nombre as org_nombre,
            o.ubicacion as org_ubicacion
        FROM visitas v
        LEFT JOIN organizacion o ON v.id_organizacion = o.id
        WHERE v.id = ?";
    
$stmt = mysqli_prepare($con, $sql);
if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Error preparando consulta: ' . mysqli_error($con)
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, 'i', $idVisita);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($visita = mysqli_fetch_assoc($result)) {
    // Preparar respuesta
    $response = [
        'success' => true,
        'data' => [
            'id' => $visita['id'],
            'responsable' => $visita['responsable'],
            'cargo' => $visita['cargo'],
            'fecha' => $visita['fecha'],
            'estado' => $visita['estado'],
            'organizacion' => $visita['org_nombre'],
            'ubicacion' => $visita['org_ubicacion']
        ]
    ];
    
    echo json_encode($response);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Visita no encontrada'
    ]);
}

mysqli_stmt_close($stmt);
?>