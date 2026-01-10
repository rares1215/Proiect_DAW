<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'libs/Exception.php';
require 'libs/PHPMailer.php';
require 'libs/SMTP.php';

function sendVerificationEmail($email, $v_code) {
    $mail = new PHPMailer(true);

    try {
        // Configurări Server Mailtrap
        $mail->isSMTP();
        $mail->Host       = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth   = true;
        $mail->Username   = '355d9ce834be08'; 
        $mail->Password   = '8ab723b662d9b7';
        $mail->Port       = 2525;

        // Recipienți
        $mail->setFrom('admin@evenimente.ro', 'Sistem Evenimente');
        $mail->addAddress($email);

        // Conținut Email
        $mail->isHTML(true);
        $mail->Subject = 'Codul tau de verificare';
        $mail->Body    = "Salut! Codul tau de verificare este: <b>$v_code</b><br>Introdu-l in pagina de activare.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}