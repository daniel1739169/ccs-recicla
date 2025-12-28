<?php
// Importar las clases de PHPMailer
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
require '../phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Configuración del servidor SMTP de Gmail
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'granadoscarrilloangeldavid@gmail.com';      // tu correo Gmail
    $mail->Password   = 'ohfj eaqr kqco qfbw
';          // tu contraseña de aplicación
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Remitente y destinatario
    $mail->setFrom('granadoscarrilloangeldavid@gmail.com', 'Angel');
    $mail->addAddress('michitecnico08@gmail.com', 'David');

    // Contenido del correo
    $mail->isHTML(true);
    $mail->Subject = 'Prueba desde localhost';
    $mail->Body    = 'Este es un correo de prueba enviado con PHPMailer.';
    $mail->AltBody = 'Este es un correo de prueba enviado con PHPMailer.';

    $mail->send();
    echo 'Correo enviado correctamente';
} catch (Exception $e) {
    echo "Error al enviar: {$mail->ErrorInfo}";
}
