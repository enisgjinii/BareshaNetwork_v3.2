<?php
include 'conn-d.php';
require 'vendor/autoload.php'; // Make sure you have PHPMailer installed via Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Get google_id from cookies
if (!isset($_COOKIE['google_id'])) {
    echo json_encode(['success' => false, 'message' => 'Google ID not set in cookies']);
    exit;
}

$google_id = $_COOKIE['google_id'];
$title = $_POST['title'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

// Fetch the user ID and email corresponding to the google_id
$sql = "SELECT id, email FROM googleauth WHERE oauth_uid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $google_id);
$stmt->execute();
$stmt->bind_result($user_id, $user_email);
$stmt->fetch();
$stmt->close();

if (empty($user_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid Google ID']);
    exit;
}

// Insert the leave request with the retrieved user ID
$sql = "INSERT INTO leaves (title, start_date, end_date, user_id) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $title, $start_date, $end_date, $user_id);

if ($stmt->execute()) {
    // Send email to supervisor
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Replace with your SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username = 'egjini@bareshamusic.com';
        $mail->Password = 'pazvpeihqiekpkiv';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom($user_email, 'Employee Name'); // Replace 'Employee Name' with actual name
        $mail->addAddress('supervisor@example.com', 'Supervisor Name'); // Replace with supervisor's email and name

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Leave Request: ' . $title;
        $mail->Body    = "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <h2 style='color: #2c3e50;'>Leave Request</h2>
            <p>Dear Supervisor,</p>
            <p>I hope this email finds you well. I am writing to formally request leave for the following period:</p>
            <table style='border-collapse: collapse; width: 100%; max-width: 500px;'>
                <tr style='background-color: #f2f2f2;'>
                    <th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Leave Type</th>
                    <td style='border: 1px solid #ddd; padding: 8px;'>{$title}</td>
                </tr>
                <tr>
                    <th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Start Date</th>
                    <td style='border: 1px solid #ddd; padding: 8px;'>{$start_date}</td>
                </tr>
                <tr style='background-color: #f2f2f2;'>
                    <th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>End Date</th>
                    <td style='border: 1px solid #ddd; padding: 8px;'>{$end_date}</td>
                </tr>
            </table>
            <p>I have ensured that my work responsibilities will be covered during my absence. If you need any additional information or have any concerns, please don't hesitate to contact me.</p>
            <p>Thank you for your consideration.</p>
            <p>Best regards,<br>Employee Name</p>
        </body>
        </html>
        ";

        $mail->send();
        header("Location: aktiviteti.php");
        echo json_encode(['success' => true, 'message' => 'New leave request added successfully and email sent to supervisor']);
    } catch (Exception $e) {
        echo json_encode(['success' => true, 'message' => 'New leave request added successfully, but failed to send email: ' . $mail->ErrorInfo]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
