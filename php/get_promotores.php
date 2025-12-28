<?php
// get_promotores.php
require 'main.php'; // Tu archivo de conexión MySQLi
header('Content-Type: application/json');

$sql = "SELECT id, CONCAT(nombre, ' ', apellido) AS nombre FROM personal WHERE id_rol = 3 ORDER BY nombre";
$resultado = mysqli_query($con, $sql);

if ($resultado) {
    // MYSQLI_ASSOC para obtener un array asociativo
    $promotores = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
    echo json_encode($promotores);
} else {
    // Enviar un error JSON si la consulta falla
    echo json_encode(['error' => 'Error en la consulta: ' . mysqli_error($con)]);
}
?>