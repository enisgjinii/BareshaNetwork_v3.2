<?php

include('./vendor/autoload.php');

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Create an instance of PHPMailer
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';  // Specify your SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'egjini17@gmail.com'; // SMTP username
    $mail->Password = 'rhydniijtqzijjdy'; // SMTP password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    // Recipient
    $mail->setFrom('egjini17@gmail.com', 'Emri Juaj');
    $mail->addAddress('egjini17@gmail.com', 'Emri i Mbeshtetësit'); // Add recipient email address

    // Email subject with label
    $subject = 'Artikulli i Menysë Klikuar - ' . $_GET['redirect']; // Get the page name from GET parameter
    $mail->Subject = $subject;

    // Get additional information
    $userAgent = $_SERVER['HTTP_USER_AGENT']; // User's browser user agent
    $ipAddress = $_SERVER['REMOTE_ADDR']; // User's IP address
    $location = ''; // You can use external services or libraries to get the user's location based on IP address
    $deviceType = getDeviceType($userAgent); // Get device type (tablet, laptop, mobile)

    // Email content
    $mail->isHTML(true);
    // Include HTML content from a separate file
    $htmlContent = file_get_contents('email_template_al.html'); // Albanian email template
    // Replace placeholders with actual values
    $htmlContent = str_replace('{redirect}', $_GET['redirect'], $htmlContent);
    $htmlContent = str_replace('{time}', date('Y-m-d H:i:s'), $htmlContent); // Current time
    $htmlContent = str_replace('{ip_address}', $ipAddress, $htmlContent); // User's IP address
    $htmlContent = str_replace('{user_agent}', $userAgent, $htmlContent); // User's browser user agent
    $htmlContent = str_replace('{location}', $location, $htmlContent); // User's location
    $htmlContent = str_replace('{device_type}', $deviceType, $htmlContent); // Device type
    // Add more replacements for additional details if needed
    $mail->Body = $htmlContent;

    // Send email
    $mail->send();
    // Redirect to the intended page
    header('Location: ' . $_GET['redirect']);
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    // Optionally, redirect to an error page or back to the menu
}

// Function to get device type based on user agent
function getDeviceType($userAgent)
{
    // List of keywords for different devices
    $tabletKeywords = array('tablet', 'ipad');
    $mobileKeywords = array('mobile', 'android', 'iphone');

    // Check if user agent contains tablet keywords
    foreach ($tabletKeywords as $keyword) {
        if (strpos(strtolower($userAgent), $keyword) !== false) {
            return 'Tablet';
        }
    }

    // Check if user agent contains mobile keywords
    foreach ($mobileKeywords as $keyword) {
        if (strpos(strtolower($userAgent), $keyword) !== false) {
            return 'Mobile';
        }
    }

    // If not tablet or mobile, assume laptop/desktop
    return 'Laptop/Desktop';
}
