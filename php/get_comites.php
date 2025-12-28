<?php
// get_comites.php
require 'main.php'; // Tu archivo de conexión MySQLi
header('Content-Type: application/json');

$sql = "SELECT id, descripcion FROM comite ORDER BY descripcion";
$resultado = mysqli_query($con, $sql);

if ($resultado) {
    // MYSQLI_ASSOC para obtener un array asociativo
    $comites = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
    echo json_encode($comites);
} else {
    // Enviar un error JSON si la consulta falla
    echo json_encode(['error' => 'Error en la consulta: ' . mysqli_error($con)]);
}
?>
