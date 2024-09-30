<?php
include 'partials/header.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;
use Dompdf\Options;

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
    $customer = fetchRow($conn, "SELECT youtube, emailadd, emri FROM klientet WHERE id = ?", "i", $invoice["customer_id"]);
    if (!$customer) {
        throw new Exception('Customer details not found.');
    }

    // Format amounts by rounding down to the nearest whole number
    $total_amount = formatAmount($invoice["total_amount"]);
    $total_amount_after_percentage = formatAmount($invoice["total_amount_after_percentage"]);
    $paid_amount = formatAmount($invoice["paid_amount"]);
    $obligim = formatAmount($invoice["total_amount_after_percentage"] - $invoice["paid_amount"]);

    // Prepare HTML content using heredoc for better readability
    $htmlContent = <<<HTML
    <div style="text-align:center; margin-bottom: 20px;">
        <h1 style="font-size: 24px; font-weight: bold; margin-bottom: 10px;">Fatura: {$invoice["invoice_number"]}</h1>
        <p style="font-size: 16px; color: #555;">Detajet e faturës</p>
        <p style="font-size: 14px; color: #FF0000;">ID-ja e kanalit: {$customer["youtube"]}</p>
    </div>
    <table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
        <thead>
            <tr>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">ID</th>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Emërtimi</th>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Shuma e përgjithshme</th>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Shuma e për. %</th>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Shuma e paguar</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{$invoice["id"]}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{$invoice["item"]}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{$total_amount}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{$total_amount_after_percentage}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{$paid_amount}</td>
            </tr>
        </tbody>
    </table>
    HTML;

    // Convert HTML content to PDF using Dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $pdf = new Dompdf($options);
    
    // Prepare the complete HTML with styles and content
    $completeHtml = <<<HTML
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap");
            body {
                font-family: "Roboto", sans-serif;
                margin: 0;
                padding: 0;
            }
            .invoice-container {
                max-width: 800px;
                margin: 200px auto 0 auto;
                background-color: #fff;
                padding: 20px;
                border: 1px solid #ddd;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            .header {
                text-align: center;
                margin-bottom: 20px;
            }
            .header h1 {
                font-size: 24px;
                font-weight: bold;
                margin: 0;
                color: #333;
            }
            .header p {
                font-size: 16px;
                color: #555;
                margin: 0;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 10px;
                text-align: left;
            }
            th {
                background-color: #f2f2f2;
                color: #333;
            }
            tr:nth-child(even) {
                background-color: #f9f9f9;
            }
        </style>
    </head>
    <body>
        <div class="invoice-container">
            {$htmlContent}
        </div>
    </body>
    </html>
    HTML;

    $pdf->loadHtml($completeHtml);
    $pdf->setPaper('A4', 'portrait');
    $pdf->render();

    // Add images to PDF
    $canvas = $pdf->getCanvas();
    $images = [
        ['path' => 'images/logo_in_invoice.png', 'x' => 250, 'y' => 20, 'width' => 125, 'height' => 125],
        ['path' => 'images/statusi.png', 'x' => 200, 'y' => 600, 'width' => 200, 'height' => 200]
    ];
    foreach ($images as $image) {
        if (file_exists($image['path'])) {
            $canvas->image($image['path'], $image['x'], $image['y'], $image['width'], $image['height']);
        }
    }

    $pdfOutput = $pdf->output();
    $pdfFilePath = "{$invoice["invoice_number"]}.pdf";
    file_put_contents($pdfFilePath, $pdfOutput);

    // Send email with PHPMailer
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'finance@bareshamusic.com';
    $mail->Password = 'jxdzshctjuynyuwb'; // Consider using environment variables for sensitive data
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('finance@bareshamusic.com', 'Baresha Finance');
    $mail->addAddress($customer["emailadd"], $customer["emri"]);
    $mail->Subject = "Fatura - {$invoice["invoice_number"]} | {$invoice["item"]}";
    $mail->addAttachment($pdfFilePath);
    $mail->isHTML(true);

    // Prepare email body using heredoc
    $mailBody = <<<HTML
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
                Fatura - {$invoice["item"]}
            </div>
            <div style="font-size: 16px; margin-bottom: 20px;">
                Përshëndetje, bashkangjitur e gjeni faturën e muajit {$invoice["item"]}.<br>
                Për çdo paqartësi na kontaktoni në këtë email.
            </div>
            <div style="font-size: 14px; color: #666666; margin-top: 20px;">
                Faleminderit, <br>
                Baresha Network L.L.C
            </div>
            <div style="font-size: 12px; color: #999999; margin-top: 20px;">
                <p>Për çdo ndihmë, na kontaktoni në:</p>
                <p>Email: info@bareshanetmusic.com | Tel: +383 48 151 200</p>
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

    $mail->Body = $mailBody;

    // Embed images
    $mail->addEmbeddedImage('./images/logo_in_invoice.png', 'logo');
    $mail->addEmbeddedImage('./images/facebook.jpg', 'facebook');
    $mail->addEmbeddedImage('./images/youtube.png', 'youtube');
    $mail->addEmbeddedImage('./images/instagram.png', 'instagram');

    $mail->send();

    // Provide a success message response
    // Instead of redirecting immediately, display a success message
    echo "<script>alert('Email sent successfully'); window.location.href='invoice.php?success=sended';</script>";
    exit();
} catch (Exception $e) {
    // Display an error alert and redirect
    $errorMessage = $e->getMessage();
    echo "<script>alert('Error: {$errorMessage}'); window.location.href='invoice.php?success=error&message=' + encodeURIComponent('{$errorMessage}');</script>";
    exit();
}
?>
