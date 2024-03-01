<?php
require './vendor/autoload.php'; // Ensure this points to the autoload.php file from Composer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (
    isset($_POST["invoice_number"]) &&
    isset($_POST["customer_id"]) &&
    isset($_POST["item"]) &&
    isset($_POST["total_amount"]) &&
    isset($_POST["total_amount_after_percentage"]) &&
    isset($_POST["created_date"]) &&
    isset($_POST["invoice_status"])
) {
    // Form values are set, proceed with processing the form data
    $invoice_number = $_POST["invoice_number"];
    $customer_id = $_POST["customer_id"];
    $item = $_POST["item"];
    $total_amount = isset($_POST["total_amount"]) ? $_POST["total_amount"] : 0;
    $total_amount_after_percentage = isset($_POST["total_amount_after_percentage"]) ? $_POST["total_amount_after_percentage"] : 0;
    $created_date = isset($_POST["created_date"]) ? $_POST["created_date"] : date('Y-m-d');
    $status = $_POST["invoice_status"];  // Retrieve the invoice status from the form
    // Connect to the database
    require_once 'conn-d.php';
    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO invoices (invoice_number, customer_id, item, total_amount, total_amount_after_percentage, created_date, state_of_invoice) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdsss", $invoice_number, $customer_id, $item, $total_amount, $total_amount_after_percentage, $created_date, $status);
    // Get name of client from table klientet based on customer_id
    $stmt2 = $conn->prepare("SELECT emri FROM klientet WHERE id = ?");
    $stmt2->bind_param("s", $customer_id);
    $stmt2->execute();
    $result = $stmt2->get_result();
    $row = $result->fetch_assoc();
    $client_name = $row['emri'];
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
        $mail->addAddress('gjinienis148@gmail.com', 'Another Recipient'); // Add another recipient
        $mail->addReplyTo('egjini17@gmail.com', 'Information');
        // Content
        $mail->isHTML(true);
        $mail->Subject = '=?utf-8?B?' . base64_encode('Krijimi i faturës me numër: ' . $invoice_number) . '?=';
        // Constructing HTML email body with CSS styles and translated content
        $mail->Body = "
        <html>
        <head>
        </head>
        <body style='font-family: Poppins; background-color: #f4f4f4;'>
        <div style='background-color: #fff; padding: 30px; margin: 50px auto; max-width: 800px; border-style: 1px solid #ddd; border-radius: 5px; box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);'>
            <h1 style='font-size: 32px; color: #333; margin-bottom: 20px;'>Fatura #" . $invoice_number . "</h1>
            <img src='cid:favicon' alt='Logo' style='display: block; margin: 0 auto; max-width: 125px;'>
            <br>
            <div style='border: 2px solid #ddd; padding: 30px; margin-bottom: 30px;'>
                <p style='margin: 10px 0;'><strong>Përshkrimi:</strong> " . $item . "</p>
                <p style='margin: 10px 0;'><strong>Numri i Faturës:</strong> #" . $invoice_number . "</p>
                <p style='margin: 10px 0;'><strong>Data e Krijimit të Faturës:</strong> " . $created_date . "</p>
            </div>
            <div style='border-bottom: 1px solid #ddd; padding: 20px 0; display: flex; justify-content: space-between;'>
                <div style='flex: 1;'>Numri i faturës : &nbsp;&nbsp;</div>
                <div style='font-weight: bold;'>" . $invoice_number . "</div>
            </div>
            <div style='border-bottom: 1px solid #ddd; padding: 20px 0; display: flex; justify-content: space-between;'>
                <div style='flex: 1;'>ID e klientit : &nbsp;&nbsp;</div>
                <div style='font-weight: bold;'>" . $customer_id . "</div>
            </div>
            <div style='border-bottom: 1px solid #ddd; padding: 20px 0; display: flex; justify-content: space-between;'>
                <div style='flex: 1;'>Emri i Klientit : &nbsp;&nbsp;</div>
                <div style='font-weight: bold;'>" . $client_name . "</div>
            </div>
            <div style='border-bottom: 1px solid #ddd; padding: 20px 0; display: flex; justify-content: space-between;'>
                <div style='flex: 1;'>Data : &nbsp;&nbsp;</div>
                <div style='font-weight: bold;'>" . $created_date . "</div>
            </div>
            <div style='border-bottom: 1px solid #ddd; padding: 20px 0; display: flex; justify-content: space-between;'>
                <div style='flex: 1;'>Fitimi : &nbsp;&nbsp;</div>
                <div style='font-weight: bold;'>" . $total_amount . " €</div>
            </div>
            <div style='border-bottom: 1px solid #ddd; padding: 20px 0; display: flex; justify-content: space-between;'>
                <div style='flex: 1;'>Fitimi pas përqindjes : &nbsp;&nbsp;</div>
                <div style='font-weight: bold;'>" . $total_amount_after_percentage . " €</div>
            </div>
            <div style='border-bottom: 1px solid #ddd; padding: 20px 0; display: flex; justify-content: space-between;'>
                <div style='flex: 1;'>Data e Krijimit : &nbsp;&nbsp;</div>
                <div style='font-weight: bold;'>" . $created_date . "</div>
            </div>
            <div style='border-top: 2px solid #ddd; padding: 20px 0; display: flex; justify-content: space-between;  margin-top: 40px;'>
                <div style='flex: 1;'>Shuma Totale :&nbsp;&nbsp;</div>
                <div style='font-weight: bold;'> " . $total_amount . " €</div>
            </div>
            <div style='border-top: 2px solid #ddd; margin-top: 40px; padding: 20px 0; text-align: center; color: #999;'>Faleminderit!</div>
        </div>
        </body>
        </html>
";
        $mail->AddEmbeddedImage('./images/favicon.png', 'favicon');
        $mail->send();
        // header('Location: logout.php');
        // exit;
    } catch (Exception $e) {
        echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
    }
    if ($stmt->execute()) {
        $stmt->close();
        mysqli_close($conn);
        header("Location: invoice.php");
    } else {
        echo "Error: " . $stmt->error;
    }
}
