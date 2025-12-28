<?php
header('Content-Type: application/json; charset=utf-8');

require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
require '../phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "main.php";

$id = $_POST['id'];
$nacionalidad = $_POST['nac_actualizar'] ?? '';
$cedula = $_POST['cedula_actualizar'] ?? '';
$fecha_nac = $_POST['fecha_actualizar'] ?? '';
$nombre = $_POST['nombre_actualizar'] ?? '';
$apellido = $_POST['apellido_actualizar'] ?? '';
$gerencia = $_POST['gerencia_actualizar'] ?? '';
$division = $_POST['division_actualizar'] ?? '';
$rol = $_POST['rol_actualizar'] ?? '';
$correo = $_POST['correo_actualizar'] ?? '';

$errores = [];

// Validaciones
if (empty($nacionalidad)) {
    $errores['nacionalidad_u'] = "";
}
if (empty($cedula)) {
    $errores['cedula_u'] = "Por favor ingrese la cedula de identidad del personal";
}
if (empty($fecha_nac)) {
    $errores['fecha_nac_u'] = "Por favor ingrese la nueva fecha de nacimiento del personal";
}
if (empty($nombre)) {
    $errores['nombre_u'] = "Por favor ingrese el nuevo nombre del personal";
}
if (empty($apellido)) {
    $errores['apellido_u'] = "Por favor ingrese el nuevo apellido del personal";
}
if (empty($gerencia)) {
    $errores['gerencia_u'] = "";
}
if (empty($division)) {
    $errores['division_u'] = "";
}
if (empty($rol)) {
    $errores['rol_u'] = "";
}

if (empty($correo)) {
    $errores['correo_u'] = "Por favor ingrese el nuevo correo electronico del personal";
}

if (!ctype_digit($cedula)) {
    $errores['cedula_u'] = "La cédula debe contener solo números";
}

if (strlen($cedula) < 7 || strlen($cedula) > 8) {
    $errores['cedula_u'] = "La cedula debe contener de 7 a 8 numeros";
}

# Consultar si el usuario ya existe (con cedula)
$stmt0 = $con->prepare("SELECT id FROM personal WHERE cedula = ?");
$stmt0->bind_param("i", $cedula);
$stmt0->execute();
$stmt0->store_result();
if($stmt0->num_rows < 1) {
    $errores['cedula_u'] = "Personal no existente";
    $stmt0->close();
}

if (filter_var($_POST['correo_actualizar'], FILTER_VALIDATE_EMAIL)) {
  $correo = $_POST['correo_actualizar'] ?? '';
} else {
  $correo = $_POST['correo_actualizar'] ?? '';
  $errores['correo_u'] = "La direccion de correo electronico no es valida";
}
if (empty($correo)) {
    $errores['correo_u'] = "Por favor ingrese el correo del personal";
}


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

$clave_hash = password_hash($clave, PASSWORD_DEFAULT);

// Adaptar Gerencias y Divisiones a las id de la base de datos
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

    $sql = "UPDATE personal SET nacionalidad = ?, cedula = ?, fecha_nac = ?, nombre = ?, apellido = ?, id_rol = ?, id_gerencia = ?, id_division = ?, clave = ?, status = 2 WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param(
        "sisssiiisi", $nacionalidad, $cedula, $fecha_nac, $nombre, $apellido, $rol, $gerencia, $division, $clave_hash, $id);

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
            $mail->Subject = 'Actualizacion de usuario';
            $mail->Body    = 'Saludos se te informa que tus datos han sido actualizados exitosamente en el Sistema de reciclaje y canje de la Corporacion Caracas Recicla<br>Debido a esto se te otorgara una nueva contraseña hasta que definas otra<br>Tu contraseña de acceso al sistema es: '.$clave;
            $mail->AltBody = 'Este es un correo de prueba enviado con PHPMailer.';

            $mail->send();

            //Devolver success en JSON
            echo json_encode([
                "status" => "success",
                "message" => "Usuario actualizado y correo enviado correctamente"
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