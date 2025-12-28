<?php

require "main.php";
header('Content-Type: application/json');

$sql_visita = "SELECT o.nombre, v.responsable, v.cargo, v.fecha FROM visitas AS v INNER JOIN organizacion AS o ON o.id = v.id_organizacion;";
$result_visita = mysqli_query($con, $sql_visita);

$visitas = [];

while ($row = mysqli_fetch_assoc($result_visita)){
	$visitas[] = $row;
}

echo json_encode([
	'visitas' => $visitas
]);

?>