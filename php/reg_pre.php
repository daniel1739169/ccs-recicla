<?php 
header('Content-Type: application/json');
include 'main.php'; // tu archivo de conexión

// Verifica si se recibió el ID
if (!isset($_POST['id'])) {
    echo json_encode([
        'error' => 'No se recibió el ID para el prerecibo',
        'organizacion_pre' => null,

    ]);
    exit;
}

$id_org = intval($_POST['id']);


$sql_org_pre = "SELECT nombre, ubicacion FROM organizacion WHERE id = $id_org;";
$result_org = mysqli_query($con, $sql_org_pre);
$organizacion = mysqli_fetch_assoc($result_org);



// Consulta para traer campos de materiales
$sql_material = "SELECT descripcion, id FROM material;";
$result_material = mysqli_query($con, $sql_material);
$materiales = [];
while ($row = mysqli_fetch_assoc($result_material)){
    $materiales[] = $row;
}

// Leer parametro de division entre kilo y puntaje
$sql_division = "SELECT division_kilo FROM parametro_calculo;";
$result_division = mysqli_query($con, $sql_division);
$division = mysqli_fetch_assoc($result_division);

$sql_pre_visita = "SELECT id, fecha, responsable, cargo FROM visitas WHERE id_organizacion = $id_org AND estado = 0;";
$result_visita = mysqli_query($con, $sql_pre_visita);
$visitas = [];
while ($visita = mysqli_fetch_assoc($result_visita)) {
    $visitas[] = $visita;
}


echo json_encode([
	'organizacion_pre' => $organizacion,
    'materiales_pre' => $materiales,
    'division' => $division,
    'visitas_pre' => $visitas

]);


 ?>