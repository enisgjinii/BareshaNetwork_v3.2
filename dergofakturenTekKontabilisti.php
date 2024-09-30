<?php
include 'partials/header.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// **Establish database connection here (assuming $conn is your connection object)**
require 'conn-d.php'; // Replace with your actual database connection script

// Helper function to format numbers by rounding down to the nearest integer
function formatAmount($amount) {
    return number_format(floor($amount), 2, '.', '');
}

// Helper function to fetch a single row from the database
function fetchRow($conn, $sql, $types = "", ...$params) {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Failed to prepare SQL statement.');
    }
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    if ($id <= 0) {
        throw new Exception('Invalid invoice ID.');
    }

    // Fetch invoice details
    $invoice = fetchRow($conn, "SELECT * FROM invoices WHERE id = ?", "i", $id);
    if (!$invoice) {
        throw new Exception('No invoice found with the given ID.');
    }

    // Fetch customer details
    $customer = fetchRow($conn, "SELECT * FROM klientet WHERE id = ?", "i", $invoice["customer_id"]);
    if (!$customer) {
        throw new Exception('Customer details not found.');
    }

    // Determine total amount and format it by rounding down
    $raw_totali = isset($invoice["total_amount_in_eur_after_percentage"]) ? $invoice["total_amount_in_eur_after_percentage"] : $invoice["total_amount_after_percentage"];
    $totali = formatAmount($raw_totali);

    $titulliemailit = $invoice["item"];
    $numriFatura = $invoice["invoice_number"];
    $is_new_customer = isset($customer['is_new_customer']) ? $customer['is_new_customer'] : false; // Ensure a default value if the field is not set
    $name = $customer['emri'];
    $email_of_finance = $customer['email_kontablist'];

    // Prepare email content
    $greeting = "Përshëndetje, urojmë të jeni mirë!";
    if ($is_new_customer) {
        $greeting .= " Jemi nga stafi Baresha.";
    }

    // Prepare email body using heredoc syntax
    $email_body = <<<HTML
    <!DOCTYPE html>
    <html>
    <head>
    </head>
    <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333333; background-color: #f9f9f9; padding: 20px; text-align: center;">
        <div style="padding: 20px; border: 1px solid #dddddd; border-radius: 5px; max-width: 600px; margin: 0 auto; background-color: #ffffff;">
            <div style="margin-bottom: 20px;">
                <img src="cid:logo" alt="Baresha Network Logo" style="max-width: 100px;">
            </div>
            <div style="font-size: 24px; font-weight: bold; margin-bottom: 20px; background-color: #FF0000; color: #ffffff; padding: 10px; border-radius: 5px;">
                Fatura - {$titulliemailit}
            </div>
            <div style="font-size: 16px; margin-bottom: 20px;">
                {$greeting} <br>
                Për këtë muaj ju lutem të na dërgoni faturën e vulosur me këtë vlerë për: <br>
                {$name} - {$totali}
            </div>
            <div style="font-size: 14px; color: #666666; margin-top: 20px;">
                Faleminderit, <br>
                Baresha Network L.L.C
            </div>
            <div style="font-size: 12px; color: #999999; margin-top: 20px;">
                <p>Email: finance@bareshamusic.com | Tel: +383 48 153 200</p>
            </div>
            <div style="margin-top: 20px;">
                <a href="https://www.facebook.com/bareshamusic/" style="margin: 0 10px;">
                    <img src="cid:facebook" alt="Facebook" style="width: 24px;">
                </a>
                <a href="https://www.youtube.com/@BareshaNetwork" style="margin: 0 10px;">
                    <img src="cid:youtube" alt="YouTube" style="width: 24px;">
                </a>
                <a href="https://www.instagram.com/bareshamusic/" style="margin: 0 10px;">
                    <img src="cid:instagram" alt="Instagram" style="width: 24px;">
                </a>
            </div>
        </div>
    </body>
    </html>
    HTML;

    // Send email with PHPMailer
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'finance@bareshamusic.com';
    $mail->Password = 'jxdzshctjuynyuwb'; // Hardcoded password as per your request
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    $mail->setFrom('finance@bareshamusic.com', 'Baresha Finance');

    // Detect environment based on server name or any other criteria
    $server_name = $_SERVER['SERVER_NAME']; // Get server name
    $env = ($server_name === 'localhost' || $server_name === '127.0.0.1') ? 'local' : 'online';

    // Decide which email address to add
    if ($env === 'local') {
        $mail->addAddress('egjini17@gmail.com', 'Recipient Name');
    } else {
        $mail->addAddress($email_of_finance, 'Për ' . $name);
    }

    $mail->Subject = 'Faturë nga Baresha Network';
    $mail->isHTML(true);
    $mail->Body = $email_body;
    $mail->addEmbeddedImage('./images/logo_in_invoice.png', 'logo');
    $mail->addEmbeddedImage('./images/facebook.jpg', 'facebook');
    $mail->addEmbeddedImage('./images/youtube.png', 'youtube');
    $mail->addEmbeddedImage('./images/instagram.png', 'instagram');

    $mail->send();

    // Provide a success message response
    echo "<script>alert('Email sent successfully'); window.location.href='invoice.php?success=sended';</script>";
    exit();
} catch (Exception $e) {
    // Display an error alert and redirect
    $errorMessage = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    echo "<script>
        alert('Error: {$errorMessage}');
        window.location.href='invoice.php?success=error&message=' + encodeURIComponent('{$errorMessage}');
    </script>";
    exit();
}
?>
