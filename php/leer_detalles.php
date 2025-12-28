<?php
header('Content-Type: application/json');
include 'main.php'; // tu archivo de conexión

// Verifica si se recibió el ID
if (!isset($_POST['num_co'])) {
    echo json_encode([
        'error' => 'No se recibió el id del recibo',
        'detalles_m' => []
    ]);
    exit;
}

$correlativo = intval($_POST['num_co']);

// Consulta de detalles de material
$sql_m = "SELECT m.descripcion, mp.cantidad_kg, mp.cantidad_p, mp.total_kg, mp.total_p FROM material_pre AS mp INNER JOIN material AS m ON m.id = mp.id_material WHERE mp.correlativo = $correlativo;";
$result_m = mysqli_query($con, $sql_m);
$detalles_m = [];
while ($row0 = mysqli_fetch_assoc($result_m)) {
	$detalles_m[] = $row0;
}

$sql_mr = "SELECT m.descripcion, mr.cantidad_kg, mr.cantidad_p, mr.total_kg, mr.total_p FROM material_refinal AS mr INNER JOIN material AS m ON m.id = mr.id_material WHERE mr.correlativo = $correlativo;";
$result_mr = mysqli_query($con, $sql_mr);
$detalles_mr = [];
while ($row1 = mysqli_fetch_assoc($result_mr)) {
    $detalles_mr[] = $row1;
}


echo json_encode([
	'detalles_m' => $detalles_m,
    'detalles_mr' => $detalles_mr]);
?>

