<?php
header('Content-Type: application/json');
include 'main.php'; // tu archivo de conexión

// Verifica si se recibió el ID
if (!isset($_POST['id'])) {
    echo json_encode([
        'error' => 'No se recibió el ID',
        'organizacion' => null,
        'recibos' => []
    ]);
    exit;
}

$id = intval($_POST['id']);

// Consulta de la organización
$sql_org = "SELECT nombre, id FROM organizacion WHERE id = $id";
$result_org = mysqli_query($con, $sql_org);
$organizacion = mysqli_fetch_assoc($result_org);

// Consulta de los recibos
$sql_recibos = "SELECT p.correlativo, p.responsable, p.fecha, r.estado 
                FROM prerecibo AS p 
                INNER JOIN recibo_final AS r ON p.id = r.id_prerecibo 
                WHERE p.id_organizacion = $id AND r.id_organizacion = $id";
$result_recibos = mysqli_query($con, $sql_recibos);

$recibos = [];
while ($row = mysqli_fetch_assoc($result_recibos)) {
    $recibos[] = $row;
    
}


// Devolver todo como JSON
echo json_encode([
    'organizacion' => $organizacion,
    'recibos' => $recibos
    
    
]);
