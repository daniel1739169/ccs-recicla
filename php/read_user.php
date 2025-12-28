<?php
include 'main.php';

// Activar errores para depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Consulta corregida
$sql_read = "SELECT * FROM personal ORDER BY id DESC";
$result = mysqli_query($con, $sql_read);

// Verificación de errores en la consulta
if (!$result) {
    die("Error en la consulta: " . mysqli_error($con));
}

// Verificación de cantidad de registros
if (mysqli_num_rows($result) === 0) {
    // Puedes usar esta variable en read.php si quieres mostrar un mensaje
    $sin_registros = true;
}
?>
