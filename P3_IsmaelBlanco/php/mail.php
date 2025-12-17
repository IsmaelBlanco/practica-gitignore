<?php
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer();
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->CharSet = 'UTF-8';
$mail->SMTPAuth = true;

//Cambiar en caso de creación de cuenta en dominio propio, cambio de mail...
$mail->Username = 'blancocalleismael@gmail.com';
$mail->Password = 'iywx mvnh tmck jmyi'; //wzvl quly wboh iykm
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;

$nombre = isset($_POST['nombre']) ? strip_tags($_POST['nombre']) : 'Sin nombre';
$email = (isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) ? $_POST['email'] : null;
$mensaje = isset($_POST['mensaje']) ? strip_tags($_POST['mensaje']) : '';

$mail->SMTPDebug = 3;
$mail->Debugoutput = 'html';

$mail->setFrom('blancocalleismael@gmail.com', 'Formulario Web');
$mail->addAddress('carsolucion74@yahoo.com');

if ($email) {
    $mail->addReplyTo($email, $nombre);
}

$mail->Subject = 'Mensaje desde tu formulario web';
$mail->addCustomHeader('X-Mailer', "Mensaje desde carsolucion.com");
$mail->isHTML(true);
$mail->Body = "<p><strong>Nombre:</strong> {$nombre}<br><strong>Email:</strong> " . ($email ?? 'No proporcionado') . "<br><strong>Mensaje:</strong> {$mensaje}</p>";
$mail->AltBody = "Nombre: {$nombre}\nEmail: " . ($email ?? 'No proporcionado') . "\nMensaje: {$mensaje}";

try {
    if ($mail->send()) {
        header('Location: /pages/informacion.php?status=success');
    } else {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    }
} catch (Exception $e) {
    echo 'Error de excepción: ' . $e->getMessage();
}