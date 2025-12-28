<?php
header('Content-Type: application/json; charset=utf-8');

require './phpmailer/src/PHPMailer.php';
require './phpmailer/src/SMTP.php';
require './phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require("./php/main.php"); // asegúrate de incluir la conexión


if(isset($_POST['userOl'])){

    
        function generarPassword($longitud = 12) {
        $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        $max = strlen($caracteres) - 1;
        for ($i = 0; $i < $longitud; $i++) {
            $password .= $caracteres[random_int(0, $max)];
        }
        return $password;
        }

        function censurarMedio($texto, $inicioVisible, $finalVisible) {
            $longitud = strlen($texto);

            $parteInicio = substr($texto, 0, $inicioVisible);
            $parteFinal = substr($texto, $longitud - $finalVisible);

            $longitudCensurada = $longitud - $inicioVisible - $finalVisible;
            $parteCensurada = str_repeat('*', $longitudCensurada);

            return $parteInicio . $parteCensurada . $parteFinal;
        }

        $errores = [];

        if (empty($_POST['userOl'])) {
            $errores['olUser'] = "Debe escribir su numero de cedula de identidad";
        }

        else if (!ctype_digit($_POST['userOl'])) {
            $errores['olUser'] = "Debe contener solo el numero de cedula";
        }

        else if (strlen($_POST['userOl']) > 8 || strlen($_POST['userOl']) < 7){
            $errores['olUser'] = "El numero de cedula debe ser de 7 o 8 digitos";
        }


        if (!empty($errores)) {
                echo json_encode([
                    "status" => "invalido",
                    "errores" => $errores
                ]);
                exit;
            }


        $olUser = $_POST['userOl'];
        $sql_ver = "SELECT nombre, apellido, correo FROM personal WHERE cedula = $olUser;";
        $datos = mysqli_query($con, $sql_ver);
        $ver_datos = mysqli_fetch_array($datos);
        $nombreOl = $ver_datos['nombre'];
        $apellidoOl = $ver_datos['apellido'];
        $correoOl = $ver_datos['correo'];
        $correoOc = censurarMedio($correoOl, 2, 14);
        $clave_new = generarPassword(12);
        $hash_new = password_hash($clave_new, PASSWORD_DEFAULT);



        $sql_ol = "UPDATE personal SET clave = ?, status = 2 WHERE cedula = ?;";
        $stmt1 = $con->prepare($sql_ol);
        $stmt1->bind_param("ss", $hash_new, $olUser);
        if ($stmt1->execute()) {
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
                $mail->addAddress($correoOl, $nombreOl.' '.$apellidoOl);

                $mail->isHTML(true);
                $mail->Subject = 'Recuperacion de acceso de usuario';
                $mail->Body    = 'Felicidades has recuperado exitosamente tu contraseña al Sistema de reciclaje y canje de la Corporacion Caracas Recicla<br>Tu contraseña de acceso al sistema es: '.$clave_new;
                $mail->AltBody = 'Este es un correo del sistema todo lo enviado ya esta automatizado';

                $mail->send();



                   echo json_encode([
                "status" => "success",
                "message" => "Se envio una contraseña aleatoria a su correo electronico ".$correoOc." ingresela para iniciar sesion y cambiar su contraseña"
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "status" => "alert",
                "alerta" => "Error al enviar: {$mail->ErrorInfo}"
            ]);
        }
    }




}
?>
