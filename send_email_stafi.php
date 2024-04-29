<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php'; // Include PHPMailer autoload.php file
// Create a new PHPMailer instance
$mail = new PHPMailer(true);
try {
    //Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
    $mail->SMTPAuth = true; // Enable SMTP authentication
    $mail->Username = 'egjini17@gmail.com'; // SMTP username
    $mail->Password = 'rhydniijtqzijjdy'; // SMTP password
    $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587; // TCP port to connect to
    // Recipients
    $mail->setFrom('egjini17@gmail.com', 'Enis Gjini');
    $mail->addAddress($_POST['email']); // Add recipient
    // Add attachments if any
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
        $mail->addAttachment($_FILES['attachment']['tmp_name'], $_FILES['attachment']['name']);
    }
    // Content
    $mail->isHTML(true);
    $mail->Subject = $_POST['subject'];
    $mail->Body = $_POST['message'];
    // Send email
    $mail->send();
    echo 'Email has been sent successfully';
    header("Location: stafi.php");
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
