<?php
require './vendor/autoload.php'; // Sigurohuni që kjo të tregojë në skedarin autoload.php nga Composer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Krijo një instancë të re të PHPMailer
$mail = new PHPMailer(true);

try {
    // Konfigurimi i SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'egjini17@gmail.com';
    $mail->Password = 'rhydniijtqzijjdy';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Caktojnë dërguesin dhe pranuesin
    $mail->setFrom('egjini17@gmail.com', 'Enis Gjini');
    $mail->addAddress($_POST['email']); // Duke supozuar se adresa e emailit është dërguar përmes POST

    // Caktimi i përmbajtjes së emailit
    $mail->isHTML(true);
    $mail->Subject = 'Kërkesë e Re';
    // Use charset
    $mail->CharSet = 'UTF-8';

    // Dizajni i përmbajtjes së emailit HTML
    $mail->Body = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Kërkesë e Re</title>
        </head>
        <body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0;">
            <div style="max-width: 600px; margin: 20px auto; background-color: #fff; border-radius: 10px; padding: 20px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
                <h2 style="color: #333;">Kërkesë e Re</h2>
                <p style="color: #666;"><strong>Përshkrimi:</strong> ' . $_POST['requirementDescription'] . '</p>
                <p style="color: #666;"><strong>Data e Pritur:</strong> ' . $_POST['expectedDate'] . '</p>
            </div>
        </body>
        </html>
    ';

    // Dërgoni emailin
    $mail->send();
    echo 'Email u dërgua me sukses!';
} catch (Exception $e) {
    echo "Gabim gjatë dërgimit të emailit: {$mail->ErrorInfo}";
}
