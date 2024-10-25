<?php
// Include necessary files and autoload dependencies
include 'partials/header.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// **Establish database connection here (assuming $conn is your connection object)**
require 'conn-d.php'; // Replace with your actual database connection script

// Helper function to format numbers by rounding down to the nearest integer and adding two decimal places
function formatAmount($amount) {
    if (!is_numeric($amount)) {
        throw new InvalidArgumentException('Amount must be a numeric value.');
    }
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

    // Calculate obligim (remaining amount)
    $obligim = formatAmount($raw_totali - $invoice["paid_amount"]);

    $titulliemailit = htmlspecialchars($invoice["item"], ENT_QUOTES, 'UTF-8');
    $numriFatura = htmlspecialchars($invoice["invoice_number"], ENT_QUOTES, 'UTF-8');
    $is_new_customer = isset($customer['is_new_customer']) ? $customer['is_new_customer'] : false; // Ensure a default value if the field is not set
    $name = htmlspecialchars($customer['emri'], ENT_QUOTES, 'UTF-8');
    $email_of_finance = htmlspecialchars($customer['email_kontablist'], ENT_QUOTES, 'UTF-8');

    // Prepare email greeting
    $greeting = "Përshëndetje, urojmë të jeni mirë!";
    if ($is_new_customer) {
        $greeting .= " Jemi nga stafi Baresha.";
    }

    // Prepare email body using heredoc syntax with compact and centered design
    $email_body = <<<HTML
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Fatura - {$titulliemailit}</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.4;
                color: #333333;
                background-color: #f9f9f9;
                padding: 20px;
                text-align: center;
            }
            .email-container {
                border: 1px solid #dddddd;
                border-radius: 5px;
                padding: 20px;
                max-width: 600px;
                margin: 0 auto;
                background-color: #ffffff;
            }
            .logo {
                margin-bottom: 15px;
            }
            .logo img {
                max-width: 100px;
            }
            .title {
                font-size: 20px;
                font-weight: bold;
                background-color: #FF0000;
                color: #ffffff;
                padding: 10px;
                border-radius: 5px;
                margin-bottom: 20px;
            }
            .content {
                font-size: 16px;
                margin-bottom: 20px;
            }
            .content strong {
                display: inline-block;
                width: 100px;
                text-align: right;
            }
            .obligim {
                margin-top: 20px;
                font-size: 14px;
                color: #2c3e50;
            }
            .footer {
                font-size: 12px;
                color: #7f8c8d;
                margin-top: 20px;
            }
            .footer a {
                margin: 0 5px;
                display: inline-block;
            }
            .footer img {
                width: 20px;
                vertical-align: middle;
            }
            @media (max-width: 600px) {
                .email-container {
                    padding: 15px;
                }
                .title {
                    font-size: 18px;
                }
                .content {
                    font-size: 14px;
                }
                .obligim {
                    font-size: 12px;
                }
                .footer img {
                    width: 18px;
                }
            }
        </style>
    </head>
    <body>
        <div class="email-container">
            <div class="logo">
                <img src="cid:logo" alt="Baresha Network Logo">
            </div>
            <div class="title">Fatura - {$titulliemailit}</div>
            <div class="content">
                {$greeting} <br><br>
                Për këtë muaj ju lutem të na dërgoni faturën e vulosur me këtë vlerë për: <br><br>
                <strong>Emri:</strong> {$name} <br>
                <strong>Totali:</strong> {$totali} EUR
            </div>
            <div class="obligim">
                <strong>Obligim:</strong> {$obligim} EUR
            </div>
            <div class="footer">
                Faleminderit, <br>
                Baresha Network L.L.C <br><br>
                Email: finance@bareshamusic.com | Tel: +383 48 153 200 <br><br>
                <a href="https://www.facebook.com/bareshamusic/">
                    <img src="cid:facebook" alt="Facebook">
                </a>
                <a href="https://www.youtube.com/@BareshaNetwork">
                    <img src="cid:youtube" alt="YouTube">
                </a>
                <a href="https://www.instagram.com/bareshamusic/">
                    <img src="cid:instagram" alt="Instagram">
                </a>
            </div>
        </div>
    </body>
    </html>
    HTML;

    // Initialize PHPMailer and configure SMTP settings
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'finance@bareshamusic.com';
    $mail->Password = 'jxdzshctjuynyuwb'; // **Security Note:** Use environment variables for sensitive data
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    $mail->setFrom('finance@bareshamusic.com', 'Baresha Finance');

    // Detect environment based on server name or any other criteria
    $server_name = $_SERVER['SERVER_NAME']; // Get server name
    $env = ($server_name === 'localhost' || $server_name === '127.0.0.1') ? 'local' : 'online';

    // Decide which email address to add
    if ($env === 'local') {
        $mail->addAddress('egjini17@gmail.com', 'Recipient Name'); // Replace with appropriate test email
    } else {
        $mail->addAddress($email_of_finance, 'Për ' . $name);
    }

    $mail->Subject = 'Faturë nga Baresha Network';
    $mail->isHTML(true);
    $mail->Body = $email_body;

    // Embed images in the email
    $mail->addEmbeddedImage('./images/logo_in_invoice.png', 'logo');
    $mail->addEmbeddedImage('./images/facebook.jpg', 'facebook');
    $mail->addEmbeddedImage('./images/youtube.png', 'youtube');
    $mail->addEmbeddedImage('./images/instagram.png', 'instagram');

    // Send the email
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
