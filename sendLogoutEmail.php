<?php
session_start();
require './vendor/autoload.php'; // Ensure this points to the autoload.php file from Composer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// Fetch user details from cookies
$userFirstName = isset($_COOKIE['user_first_name']) ? $_COOKIE['user_first_name'] : 'User';
$userLastName = isset($_COOKIE['user_last_name']) ? $_COOKIE['user_last_name'] : '';
$userEmail = isset($_COOKIE['user_email']) ? urldecode($_COOKIE['user_email']) : 'default@example.com';
$userFullName = $userFirstName . ' ' . $userLastName;
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
    $mail->SMTPAuth = true; // Enable SMTP authentication
    $mail->Username = 'egjini17@gmail.com'; // SMTP username
    $mail->Password = 'rhydniijtqzijjdy'; // SMTP password
    $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587; // TCP port to connect to
    // Recipients
    $mail->setFrom('egjini17@gmail.com', 'Mailer');
    $mail->addAddress('egjini17@gmail.com', 'Enis Gjini');
    $mail->addReplyTo('egjini17@gmail.com', 'Information');
    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Njoftimi i daljes';
    // Correct the dynamic content integration for logout time
    $logoutTime = date('Y-m-d H:i:s'); // Get the current time
    $mail->Body = <<<EOT
<!DOCTYPE html>
<html>
<head>
    <title>Njoftimi i daljes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            background-color: #fff;
            border: 1px solid #dedede;
            border-radius: 5px;
            padding: 20px;
            margin: 0 auto;
            max-width: 600px;
        }
        h1 {
            color: #444;
            font-size: 24px;
            text-align: center;
        }
        p {
            font-size: 16px;
            line-height: 1.5;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #aaa;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Njoftimi i daljes</h1>
        <p>Kjo është për të njoftuar se <strong>{$userFullName}</strong> ka dalë me sukses në ora <em>{$logoutTime}</em> nga paneli administrativ.</p>
        <p>Email-i juaj: <strong>{$userEmail}</strong></p>
        <p>Nëse nuk e keni nisur këtë veprim, ju lutemi kontaktoni menjëherë ekipin tonë të mbështetjes.</p>
        <div class="footer">
            &copy; {$logoutTime} Baresha Network. Të gjitha të drejtat të rezervuara.
        </div>
    </div>
</body>
</html>
EOT;
    $mail->AltBody = 'Kjo është për të njoftuar se ' . $userFullName . ' ka dalë me sukses.';
    $mail->send();
    header('Location: logout.php');
    exit;
} catch (Exception $e) {
    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
}
