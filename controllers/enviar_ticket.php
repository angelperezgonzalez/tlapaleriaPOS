<?php
require '../vendor/autoload.php'; // si usas PHPMailer vía composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$response = ['status' => 'error', 'message' => ''];

if (!isset($_FILES['archivo']) || !isset($_POST['correo'])) {
    $response['message'] = 'Faltan datos';
    echo json_encode($response);
    exit;
}

$correo = $_POST['correo'];
$venta_id = $_POST['venta_id'] ?? 'ticket';

// Guardar el archivo temporal
$tmp_name = $_FILES['archivo']['tmp_name'];
$filename = basename($_FILES['archivo']['name']);
$ruta = __DIR__ . "/../tmp/$filename";

if (!move_uploaded_file($tmp_name, $ruta)) {
    $response['message'] = 'No se pudo guardar el archivo';
    echo json_encode($response);
    exit;
}

// Enviar email con PHPMailer
$mail = new PHPMailer(true);
$mail->SMTPDebug = 2;
$mail->Debugoutput = 'html';
try {
    // CONFIGURA TU SMTP AQUI
    $mail->isSMTP();
    $mail->Host = 'smtp-mail.outlook.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'j.angel.perez.gonzalez@hotmail.com';
    $mail->Password = 'erfbusypnfrkbybo';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('j.angel.perez.gonzalez@hotmail.com', 'Tlapalería');
    $mail->addAddress($correo);
    $mail->Subject = "Ticket de venta #$venta_id";
    $mail->Body = "Adjunto encontrarás tu ticket de compra.";
    $mail->addAttachment($ruta, $filename);

    $mail->send();

    $response['status'] = 'ok';
} catch (Exception $e) {
    $response['message'] = 'Mailer Error: ' . $mail->ErrorInfo;
}

// Borrar archivo temporal
@unlink($ruta);

echo json_encode($response);
