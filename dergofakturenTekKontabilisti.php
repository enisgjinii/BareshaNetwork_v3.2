<?php
include 'partials/header.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    // Establish database connection here (assuming $conn is your connection object)
    if ($id <= 0) {
        throw new Exception('Invalid invoice ID.');
    }

    // Fetch invoice details from the database
    $sql = "SELECT * FROM invoices WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Failed to prepare SQL statement.');
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception('No invoice found with the given ID.');
    }

    $row = $result->fetch_assoc();
    $titulliemailit = $row["item"];
    $numriFatura = $row["invoice_number"];
    $totali = isset($row["total_amount_in_eur_after_percentage"]) ? $row["total_amount_in_eur_after_percentage"] : $row["total_amount_after_percentage"];

    // Fetch customer details to determine if the customer is new
    $sql_for_customer = "SELECT * FROM klientet WHERE id = " . $row["customer_id"];
    $result_for_customer = mysqli_query($conn, $sql_for_customer);
    $row_for_customer = mysqli_fetch_assoc($result_for_customer);
    $is_new_customer = $row_for_customer['is_new_customer']; // Assuming there's a field indicating if the customer is new
    $name = $row_for_customer['emri'];
    $email_of_finance = $row_for_customer['email_kontablist'];
    // Prepare email content
    $greeting = "Pershendetje, urojme te jeni mire!";
    if ($is_new_customer) {
        $greeting .= " Jemi nga stafi Baresha.";
    }

    $email_body = '
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
                    Fatura - ' . $titulliemailit . '
                </div>
                <div style="font-size: 16px; margin-bottom: 20px;">
                    ' . $greeting . ' <br>
                    Per kete muaj ju lutem te na dergoni faturen e vulosur me kete vlere per: <br>
                    ' . $name . ' - ' . $totali . '
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
        </html>';

    // Send email with PHPMailer
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'finance@bareshamusic.com';
    $mail->Password = 'hocrbvnxzoteynup';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('finance@bareshamusic.com', 'Baresha Finance');
    // Detect environment based on server name or any other criteria
    $server_name = $_SERVER['SERVER_NAME']; // Get server name

    if ($server_name === 'localhost' || $server_name === '127.0.0.1') {
        // Local environment
        $env = 'local';
    } else {
        // Assuming it's online if not localhost
        $env = 'online';
    }

    // Now use $env to decide which email address to add
    if ($env === 'local') {
        // Local
        $mail->addAddress('kastriot@bareshamusic.com', 'Recipient Name');
    } else {
        // Online
        $mail->addAddress($email_of_finance, 'Për ' . $name);
    }

    $mail->Subject = 'Faturë nga Baresha Network';
    $mail->isHTML(true);
    $mail->Body = $email_body;
    $mail->AddEmbeddedImage('./images/logo_in_invoice.png', 'logo');
    $mail->AddEmbeddedImage('./images/facebook.jpg', 'facebook');
    $mail->AddEmbeddedImage('./images/youtube.png', 'youtube');
    $mail->AddEmbeddedImage('./images/instagram.png', 'instagram');
    $mail->CharSet = 'UTF-8';
    $mail->send();

    // Provide a success message response
    echo "Email sent successfully";

    // Redirect to invoice.php with success parameter
    header('Location: invoice.php?success=sended');
    exit();
} catch (Exception $e) {
    // Redirect to invoice.php with error message
    header('Location: invoice.php?success=error&message=' . urlencode($e->getMessage()));
    exit();
}
