<?php
header('Content-Type: application/json; charset=utf-8');

require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
require '../phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "main.php";

$nacionalidad = $_POST['nacionalidad'] ?? '';
$cedula = $_POST['cedula'] ?? '';
$fecha = $_POST['fecha_nac'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$apellido = $_POST['apellido'] ?? '';
$gerencia = $_POST['gerencia'] ?? '';
$division = $_POST['division'] ?? '';
$rol = $_POST['rol'] ?? '';

$errores = [];

// Validaciones
if (empty($nacionalidad)) {
    $errores['nacionalidad'] = "";
}
if (empty($cedula)) {
    $errores['cedula'] = "Por favor ingrese la cedula del personal";
}
if (empty($fecha)) {
    $errores['fecha_nac'] = "Por favor ingrese la fecha de nacimiento del personal";
}
if (empty($nombre)) {
    $errores['nombre'] = "Por favor ingrese el nombre del personal";
}
if (empty($apellido)) {
    $errores['apellido'] = "Por favor ingrese el apellido del personal";
}
if (empty($gerencia)) {
    $errores['gerencia'] = "";
}
if (empty($division)) {
    $errores['division'] = "";
}
if (empty($rol)) {
    $errores['rol'] = "";
}

if (!ctype_digit($cedula)) {
    $errores['cedula'] = "La cédula debe contener solo números";
}

if (strlen($cedula) < 7 || strlen($cedula) > 8) {
    $errores['cedula'] = "La cedula debe contener de 7 a 8 numeros";
}

if (filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL)) {
  $correo = $_POST['correo'] ?? '';
} else {
  $correo = $_POST['correo'] ?? '';
  $errores['correo_usu'] = "La direccion de correo electronico no es valida";
}
if (empty($correo)) {
    $errores['correo_usu'] = "Por favor ingrese el correo del personal";
}

# Consultar si el usuario ya existe (con cedula)
$stmt0 = $con->prepare("SELECT id FROM personal WHERE cedula = ?");
$stmt0->bind_param("i", $cedula);
$stmt0->execute();
$stmt0->store_result();
if($stmt0->num_rows > 0) {
    $errores['cedula'] = "El personal ya esta registrado";
    $stmt0->close();
}

// Si hay errores, devolverlos en JSON
if (!empty($errores)) {
    echo json_encode([
        "status" => "error",
        "errores" => $errores
    ]);
    exit;
}

function generarPassword($longitud = 12) {
    $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = '';
    $max = strlen($caracteres) - 1;
    for ($i = 0; $i < $longitud; $i++) {
        $password .= $caracteres[random_int(0, $max)];
    }
    return $password;
}

$clave = generarPassword(12);

// Adaptar Gerencias y Divisiones a las id de la base de datos
switch ($rol) {
    case 'Administrador': $rol = 1; break;
    case 'Gerente': $rol = 2; break;
    case 'Promotor': $rol = 3; break;
}

switch ($gerencia) {
    case 'Gestion Interna': $gerencia = 1; break;
    case 'Consultoria Juridica': $gerencia = 2; break;
    case 'Operaciones': $gerencia = 3; break;
    case 'Gestion Comercial': $gerencia = 4; break;
}

switch ($division) {
    case 'Administracion y Finanzas': $division = 1; break;
    case 'Gestion Humana': $division = 2; break;
    case 'Seguridad Integral': $division = 3; break;
    case 'Planificacion y Presupuesto': $division = 4; break;
    case 'Tecnologias de la Informacion y Comunicacion': $division = 5; break;
    case 'Gestion Comunicacional': $division = 6; break;
    case 'Servicios': $division = 7; break;
    case 'Recoleccion': $division = 8; break;
    case 'Comercializacion': $division = 9; break;
    case 'Economia Circulante': $division = 10; break;
}

$clave_hash = password_hash($clave, PASSWORD_DEFAULT);

$sql = "INSERT INTO personal(nacionalidad, cedula, fecha_nac, nombre, apellido, clave, id_rol, id_gerencia, id_division, correo, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 2)";
$stmt = $con->prepare($sql);
$stmt->bind_param("sissssiiis", $nacionalidad, $cedula, $fecha, $nombre, $apellido, $clave_hash, $rol, $gerencia, $division, $correo);

if ($stmt->execute()) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'reciclaycanjea@gmail.com';
        $mail->Password   = 'hvws pllw ggdn enzt'; // contraseña de aplicación
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('reciclaycanjea@gmail.com', 'CCS Recicla');
        $mail->addAddress($correo, $nombre.' '.$apellido);

        $mail->isHTML(true);
        $mail->Subject = 'Registro de usuario';
        $mail->Body    = 'Felicidades has sido registrado exitosamente al Sistema de reciclaje y canje de la Corporacion Caracas Recicla<br>Tu contraseña de acceso al sistema es: '.$clave;
        $mail->AltBody = 'Este es un correo de prueba enviado con PHPMailer.';

        $mail->send();

        //Devolver success en JSON
        echo json_encode([
            "status" => "success",
            "message" => "Usuario registrado y correo enviado correctamente"
        ]);
    } catch (Exception $e) {
        echo json_encode([
            "status" => "alert",
            "alerta" => "Error al enviar: {$mail->ErrorInfo}"
        ]);
    }
}

$con->close();
?>
