<?php
# Conexion a base de datos (ccs_recicla)
$host = "localhost";
$user = "root";
$pass = "";
$db = "ccs_recicla";

$con = mysqli_connect($host, $user, $pass, $db);

// Verificar conexión
if (!$con) {
    // Si falla la conexión, mostramos un error JSON y detenemos la ejecución.
    header('Content-Type: application/json');
    die(json_encode(["error" => "Error de conexión a la base de datos: " . mysqli_connect_error()]));
}


if(!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}

# Limpieza
function limpiar_cadena($cadena){
    global $con;
    $cadena = trim($cadena);
    $cadena = stripslashes($cadena);
    $cadena = mysqli_real_escape_string($con, $cadena);
    return $cadena;
}

/*function añadir_credencial($cadena){
    global $con;
    $cadena = "tecnologia"($cadena);
    $cadena = mysqli_real_escape_string($con, $cadena);
    return $cadena;  
}*/

# Session segura y separada
/*session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);

# Establecer zona horaria
date_default_timezone_set('America/Caracas');
*/
?>