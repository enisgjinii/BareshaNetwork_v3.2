<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // Server settings
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'egjini@bareshamusic.com';
    $mail->Password   = 'pazvpeihqiekpkiv';
    $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587; // TCP port to connect to

    // Recipients
    $recipientEmail = $_POST['to_email'];
    $recipientName  = ''; // You can customize this

    $mail->setFrom('baresha.invoices@gmail.com', 'Baresha Invoices');
    $mail->addAddress($recipientEmail, $recipientName);
    $mail->addReplyTo('info@example.com', 'Information');

    // Content
    $mail->isHTML(true);
    $mail->Subject = $_POST['subject'];

    // Check if the message body is empty
    if (!empty($_POST['message'])) {
        $mail->Body = $_POST['message'];
    } else {
        // If the message body is empty, send a "Thank You!" message
        $mail->Body = 'Thank You!';
    }

    // Check if a file was uploaded
    if (isset($_FILES['pdf_attachment']) && $_FILES['pdf_attachment']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES['pdf_attachment']['tmp_name'];
        $file_name = $_FILES['pdf_attachment']['name'];

        // Attach the uploaded PDF file to the email
        $mail->addAttachment($file_tmp_name, $file_name);
    }

    $mail->send();
    echo 'Message has been sent successfully.';
} catch (Exception $e) {
    echo 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
}
