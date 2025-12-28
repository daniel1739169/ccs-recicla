<?php


// Activar errores para depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Consulta corregida
$sql_read_ma = "SELECT * prerecibo";
$li_pre = mysqli_query($con, $sql_read_relation);




// Verificación de errores en la consulta
if (!$result_r) {
    die("Error en la consulta: " . mysqli_error($con));
}

// Verificación de cantidad de registros
if (mysqli_num_rows($result_r) === 0) {
    // Puedes usar esta variable en read.php si quieres mostrar un mensaje
    $sin_registros = true;
}


?>