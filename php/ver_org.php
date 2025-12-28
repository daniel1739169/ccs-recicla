<?php
require "main.php";
// Activar errores para depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Consulta corregida
$sql_read_organization = "SELECT o.id, o.nombre, c.descripcion, o.ubicacion, o.nombre_responsable, o.cedula_responsable, o.telefono_responsable FROM organizacion o INNER JOIN comite c ON c.id = o.id_comite;";
$result_org = mysqli_query($con, $sql_read_organization);

// Verificación de errores en la consulta
if (!$result_org) {
    die("Error en la consulta: " . mysqli_error($con));
}

// Verificación de cantidad de registros
if (mysqli_num_rows($result_org) === 0) {
    // Puedes usar esta variable en read.php si quieres mostrar un mensaje
    $sin_registros = true;
}

?>