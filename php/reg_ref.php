<?php 
header('Content-Type: application/json');
include 'main.php'; // tu archivo de conexión

// Verifica si se recibió el ID
if (!isset($_POST['correlativo'])) {
    echo json_encode([
        'error' => 'No se recibió el ID para el prerecibo',
        'organizacion_ref' => null,

    ]);
    exit;
}

$correlativo = intval($_POST['correlativo']);


$sql_org_ref = "SELECT o.nombre, r.correlativo, r.responsable, r.cargo, o.ubicacion FROM recibo_final AS r INNER JOIN organizacion AS o ON o.id = r.id_organizacion WHERE r.correlativo = $correlativo;
;";
$result_org = mysqli_query($con, $sql_org_ref);
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

echo json_encode([
	'organizacion_ref' => $organizacion,
    'materiales_ref' => $materiales,
    'division' => $division
]);


 ?>