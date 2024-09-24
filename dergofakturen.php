<?php
include 'partials/header.php';
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;
use Dompdf\Options;
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
    // Build HTML content for the invoice
    $row = $result->fetch_assoc();
    $titulliemailit = $row["item"];
    $numriFatura = $row["invoice_number"];
    $sql_for_getting_channel_id = "SELECT youtube,emailadd,emri FROM klientet WHERE id = " . $row["customer_id"] . "";
    $result_for_getting_channel_id = mysqli_query($conn, $sql_for_getting_channel_id);
    $row_for_getting_channel_id = mysqli_fetch_assoc($result_for_getting_channel_id);
    $channel_id = $row_for_getting_channel_id["youtube"];
    $email_of_finance = $row_for_getting_channel_id["emailadd"];
    $name = $row_for_getting_channel_id["emri"];
    $htmlContent = '<div style="text-align:center; margin-bottom: 20px;">';
    $htmlContent .= '<h1 style="font-size: 24px; font-weight: bold; margin-bottom: 10px;">Fatura: ' . $row["invoice_number"] . '</h1>';
    $htmlContent .= '<p style="font-size: 16px; color: #555;">Detajet e faturës</p>';
    $htmlContent .= '<p style="font-size: 14px; color: #FF0000;">ID-ja e kanalit: ' . $row_for_getting_channel_id["youtube"] . '</p>';
    $htmlContent .= '</div>';
    $htmlContent .= '<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">';
    $htmlContent .= '<thead>';
    $htmlContent .= '<tr>';
    $htmlContent .= '<th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">ID</th>';
    $htmlContent .= '<th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Emërtimi</th>';
    $htmlContent .= '<th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Shuma e përgjithshme</th>';
    $htmlContent .= '<th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Shuma e për. %</th>';
    $htmlContent .= '<th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Shuma e paguar</th>';
    $htmlContent .= '</tr>';
    $htmlContent .= '</thead>';
    $htmlContent .= '<tbody>';
    $htmlContent .= '<tr>';
    $htmlContent .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $row["id"] . '</td>';
    $htmlContent .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $row["item"] . '</td>';
    $htmlContent .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $row["total_amount"] . '</td>';
    $htmlContent .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $row["total_amount_after_percentage"] . '</td>';
    $htmlContent .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $row["paid_amount"] . '</td>';
    $htmlContent .= '</tr>';
    $htmlContent .= '</tbody></table>';
    $obligim = $row["total_amount_after_percentage"] - $row["paid_amount"];
    // Convert HTML content to PDF using Dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $pdf = new Dompdf($options);
    $pdf->loadHtml('
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
            margin-top: 200px;
            margin-left: auto;
            margin-right: auto;
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
        ' . $htmlContent . '
    </div>
</body>
</html>
');
    $pdf->setPaper('A4', 'portrait');
    $pdf->render();
    $canvas = $pdf->getCanvas();
    $imagePath = 'images/logo_in_invoice.png'; // Adjust the path to your logo image
    $imageX = 250; // X coordinate
    $imageY = 20; // Y coordinate
    $imageWidth = 125; // Width of the logo
    $imageHeight = 125; // Height of the logo
    $canvas->image($imagePath, $imageX, $imageY, $imageWidth, $imageHeight);
    $imagePath2 = 'images/statusi.png'; // Adjust the path to your logo image
    $imageX2 = 200; // X coordinate
    $imageY2 = 600; // Y coordinate
    $imageWidth2 = 200; // Width of the logo
    $imageHeight2 = 200; // Height of the logo
    $canvas->image($imagePath2, $imageX2, $imageY2, $imageWidth2, $imageHeight2);
    $pdfOutput = $pdf->output();
    $pdfFilePath =  $numriFatura . '.pdf';
    file_put_contents($pdfFilePath, $pdfOutput);
    // Send email with PHPMailer
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'finance@bareshamusic.com';
    $mail->Password = 'jxdzshctjuynyuwb';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('finance@bareshamusic.com', 'Baresha Finance');
    $mail->addAddress($email_of_finance, $name);
    $mail->Subject = 'Fatura - ' . $numriFatura . ' | ' . $titulliemailit;
    $mail->addAttachment($pdfFilePath);
    $mail->isHTML(true);
    $mail->Body = '
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
                    Përshëndetje, bashkangjitur e gjeni faturën e muajit ' . $titulliemailit . '.<br>
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
        </html>';
    $mail->AddEmbeddedImage('./images/logo_in_invoice.png', 'logo');
    $mail->AddEmbeddedImage('./images/facebook.jpg', 'facebook');
    $mail->AddEmbeddedImage('./images/youtube.png', 'youtube');
    $mail->AddEmbeddedImage('./images/instagram.png', 'instagram');
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
