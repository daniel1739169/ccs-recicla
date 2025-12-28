<?php
// backend/db_config.php - Usando tu formato de conexión

// Datos de conexión a base de datos (ccs_recicla)
$host = "localhost";
$user = "root";
$pass = "";
$db = "ccs_recicla";
$port = "3306"; // Aunque 3306 es el puerto por defecto y a menudo no se necesita

// La variable de conexión global será $conn
$conn = mysqli_connect($host, $user, $pass, $db, $port);


?>
