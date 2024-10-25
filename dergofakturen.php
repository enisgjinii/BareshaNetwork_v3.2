<?php
// Include necessary files and autoload dependencies
include 'partials/header.php';
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;
use Dompdf\Options;
// Helper function to format numbers by rounding down to the nearest integer and adding two decimal places
function formatAmount($amount)
{
    if (!is_numeric($amount)) {
        throw new InvalidArgumentException('Amount must be a numeric value.');
    }
    return number_format(floor($amount), 2, '.', '');
}
// Helper function to fetch a single row from the database
function fetchRow($conn, $sql, $types = "", ...$params)
{
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
// Retrieve the invoice ID from the GET parameter
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
    $total_amount_in_eur = formatAmount($invoice["total_amount_in_eur"]);
    $total_amount_in_eur_after_percentage = formatAmount($invoice["total_amount_in_eur_after_percentage"]);
    $paid_amount = formatAmount($invoice["paid_amount"]);
    $obligim = formatAmount($invoice["total_amount_in_eur_after_percentage"] - $invoice["paid_amount"]);
    // Determine the status image based on obligim
    if ($obligim <= 0.00) {
        // If obligim is 0.00 or less, the invoice is completed
        $statusImagePath = 'images/statusi.png';
    } else {
        // If obligim is greater than 0.00, the invoice is pending
        $statusImagePath = 'images/statusi-papaguar.png';
    }
    // Prepare HTML content for the invoice
    $completeHtml = <<<HTML
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Fatura {$invoice["invoice_number"]}</title>
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap");
            body {
                font-family: 'Roboto', sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
                color: #333;
            }
            .invoice-container {
                max-width: 600px;
                margin: 20px auto;
                background-color: #ffffff;
                padding: 15px;
                border: 1px solid #ddd;
                border-radius: 5px;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.05);
                text-align: center; /* Center all text and elements */
            }
            .header {
                margin-bottom: 15px;
            }
            /* Logo Removed from Header */
            /*
            .header img {
                max-width: 100px;
                margin-bottom: 10px;
            }
            */
            .header h1 {
                font-size: 18px;
                margin: 0;
                color: #2c3e50;
            }
            .header p {
                font-size: 12px;
                color: #7f8c8d;
                margin: 2px 0;
            }
            .details-section {
                margin-bottom: 15px;
            }
            .details-section h2 {
                font-size: 14px;
                color: #34495e;
                border-bottom: 1px solid #e74c3c;
                padding-bottom: 3px;
                margin-bottom: 8px;
            }
            .details-section p {
                font-size: 12px;
                margin: 3px 0;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
                font-size: 11px;
            }
            th, td {
                padding: 4px;
                border: 1px solid #ddd;
                text-align: center;
            }
            th {
                background-color: #e74c3c;
                color: #ffffff;
                font-weight: 500;
            }
            tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            .totals {
                margin-top: 15px;
                font-size: 12px;
            }
            .totals table {
                width: 100%;
            }
            .totals td.label {
                text-align: left;
                font-weight: 500;
                color: #34495e;
            }
            .totals td.amount {
                text-align: right;
                color: #2c3e50;
            }
            .footer {
                margin-top: 20px;
                font-size: 12px;
                color: #7f8c8d;
            }
            .footer img {
                width: 20px;
                margin: 0 5px;
                vertical-align: middle;
            }
            /* Responsive Design */
            @media (max-width: 600px) {
                .invoice-container {
                    padding: 10px;
                }
                .header h1 {
                    font-size: 16px;
                }
                .header p {
                    font-size: 11px;
                }
                .details-section h2 {
                    font-size: 13px;
                }
                .details-section p {
                    font-size: 11px;
                }
                table {
                    font-size: 10px;
                }
                th, td {
                    padding: 3px;
                }
            }
        </style>
    </head>
    <body>
        <div class="invoice-container">
            <div class="header">
                <!-- Logo Removed from PDF Header -->
                <!-- <img src="images/logo_in_invoice.png" alt="Baresha Network Logo"> -->
                <h1>Fatura: {$invoice["invoice_number"]}</h1>
                <p>Detajet e faturës</p>
                <p>ID-ja e kanalit: {$customer["youtube"]}</p>
            </div>
            <div class="details-section">
                <h2>Detajet e Klientit</h2>
                <p><strong>Emri:</strong> {$customer["emri"]}</p>
                <p><strong>Email:</strong> {$customer["emailadd"]}</p>
            </div>
            <div class="details-section">
                <h2>Detajet e Faturës</h2>
                <p><strong>Data:</strong> {$invoice["created_date"]}</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Emërtimi</th>
                        <th>Shuma e përgjithshme (EUR)</th>
                        <th>Shuma e për. % (EUR)</th>
                        <th>Shuma e paguar (EUR)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{$invoice["id"]}</td>
                        <td>{$invoice["item"]}</td>
                        <td>{$total_amount_in_eur}</td>
                        <td>{$total_amount_in_eur_after_percentage}</td>
                        <td>{$paid_amount}</td>
                    </tr>
                </tbody>
            </table>
            <div class="totals">
                <table>
                    <tr>
                        <td class="label">Shuma e përgjithshme:</td>
                        <td class="amount">{$total_amount_in_eur}</td>
                    </tr>
                    <tr>
                        <td class="label">Shuma pas %:</td>
                        <td class="amount">{$total_amount_in_eur_after_percentage}</td>
                    </tr>
                    <tr>
                        <td class="label">Paguar:</td>
                        <td class="amount">{$paid_amount}</td>
                    </tr>
                    <tr>
                        <td class="label"><strong>Obligim:</strong></td>
                        <td class="amount"><strong>{$obligim}</strong></td>
                    </tr>
                </table>
            </div>
            <!-- Embed Status Image Based on obligim -->
            <div class="status-image" style="margin-top: 15px;">
                <img src="cid:status_image" alt="Statusi" style="width: 80px;">
            </div>
            <div class="footer">
                <p>Faleminderit për bashkëpunimin!</p>
                <p>
                    <a href="https://www.facebook.com/bareshamusic/">
                        <img src="images/facebook.jpg" alt="Facebook">
                    </a>
                    <a href="https://www.youtube.com/@BareshaNetwork">
                        <img src="images/youtube.png" alt="YouTube">
                    </a>
                    <a href="https://www.instagram.com/bareshamusic/">
                        <img src="images/instagram.png" alt="Instagram">
                    </a>
                </p>
                <p>Kontaktoni në: info@bareshanetmusic.com | Tel: +383 48 151 200</p>
            </div>
        </div>
    </body>
    </html>
    HTML;
    // Initialize Dompdf with options
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $pdf = new Dompdf($options);
    // Load the compact and centered HTML content without the logo
    $pdf->loadHtml($completeHtml);
    $pdf->setPaper('A4', 'portrait');
    $pdf->render();
    // **Embed Status Image Based on obligim**
    $canvas = $pdf->getCanvas();
    $statusImageFullPath = $statusImagePath; // Determined earlier based on obligim
    if (file_exists($statusImageFullPath)) {
        // Embed the status image within the PDF using CID
        // Calculate position based on page size; adjust 'x' and 'y' as needed
        $canvas->image($statusImageFullPath, 260, 550, 80, 80); // Example positioning for A4
    }
    // Output the PDF to a file
    $pdfOutput = $pdf->output();
    $pdfFilePath = "{$invoice["invoice_number"]}.pdf";
    file_put_contents($pdfFilePath, $pdfOutput);
    // Initialize PHPMailer and configure SMTP settings
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'finance@bareshamusic.com';
    $mail->Password = 'jxdzshctjuynyuwb'; // **Security Note:** Use environment variables for sensitive data
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    // Set email parameters
    $mail->setFrom('finance@bareshamusic.com', 'Baresha Finance');
    $mail->addAddress($customer["emailadd"], $customer["emri"]);
    $mail->Subject = "Fatura - {$invoice["invoice_number"]} | {$invoice["item"]}";
    $mail->addAttachment($pdfFilePath);
    $mail->isHTML(true);
    // Prepare the email body using heredoc with compact design
    $mailBody = <<<HTML
    <!DOCTYPE html> 
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Fatura - {$invoice["item"]}</title>
    </head>
    <body style="font-family: Arial, sans-serif; line-height: 1.4; color: #333333; background-color: #f9f9f9; padding: 15px; text-align: center;">
        <div style="padding: 15px; border: 1px solid #dddddd; border-radius: 4px; max-width: 600px; margin: 0 auto; background-color: #ffffff;">
            <div style="margin-bottom: 15px;">
                <img src="cid:logo" alt="Baresha Network Logo" style="max-width: 90px;">
            </div>
            <div style="font-size: 18px; font-weight: bold; margin-bottom: 15px; background-color: #e74c3c; color: #ffffff; padding: 8px; border-radius: 4px;">
                Fatura - {$invoice["item"]}
            </div>
            <div style="font-size: 14px; margin-bottom: 15px;">
                Përshëndetje {$customer["emri"]},<br><br>
                Bashkangjitur e gjeni faturën tuaj për muajin {$invoice["item"]}.<br>
                Ju lutemi kontrolloni detajet dhe na kontaktoni nëse keni ndonjë pyetje.
            </div>
            <div style="font-size: 12px; color: #666666; margin-top: 15px;">
                Faleminderit, <br>
                Baresha Network L.L.C
            </div>
            <div style="font-size: 11px; color: #999999; margin-top: 10px;">
                <p>Për çdo ndihmë, na kontaktoni në:</p>
                <p>Email: info@bareshanetmusic.com | Tel: +383 48 151 200</p>
            </div>
            <div style="margin-top: 10px;">
                <a href="https://www.facebook.com/bareshamusic/" style="margin: 0 8px;">
                    <img src="cid:facebook" alt="Facebook" style="width: 20px;">
                </a>
                <a href="https://www.youtube.com/@BareshaNetwork" style="margin: 0 8px;">
                    <img src="cid:youtube" alt="YouTube" style="width: 20px;">
                </a>
                <a href="https://www.instagram.com/bareshamusic/" style="margin: 0 8px;">
                    <img src="cid:instagram" alt="Instagram" style="width: 20px;">
                </a>
            </div>
        </div>
    </body>
    </html>
    HTML;
    $mail->Body = $mailBody;
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
    $errorMessage = $e->getMessage();
    echo "<script>alert('Error: {$errorMessage}'); window.location.href='invoice.php?success=error&message=' + encodeURIComponent('{$errorMessage}');</script>";
    exit();
}
