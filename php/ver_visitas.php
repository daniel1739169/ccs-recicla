<?php
// ver_visitas.php - VERSIÓN CORREGIDA CON JOIN

// Consulta de visitas con JOIN para obtener el nombre de la organización
$sql_v = "SELECT v.*, o.nombre as nombre_organizacion 
          FROM visitas v 
          LEFT JOIN organizacion o ON v.id_organizacion = o.id 
          ORDER BY v.fecha DESC";  
          
$result_visitas = mysqli_query($con, $sql_v);

// Verifica errores de consulta
if (!$result_visitas) {
    die("Error en consulta: " . mysqli_error($con));
}

// Opcional: para depuración
// echo "<!-- Consulta ejecutada: $sql_v -->";
// echo "<!-- Filas obtenidas: " . mysqli_num_rows($result_visitas) . " -->";
?>