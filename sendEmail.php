<?php
// sendEmail.php

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Autoload dependencies
require_once './vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Configuration Settings
define('SMTP_HOST', 'smtp.gmail.com'); // SMTP server
define('SMTP_AUTH', true); // Enable SMTP authentication
define('SMTP_USERNAME', 'egjini@bareshamusic.com'); // SMTP username
define('SMTP_PASSWORD', 'pazvpeihqiekpkiv'); // SMTP password
define('SMTP_SECURE', 'tls'); // Encryption - 'ssl' or 'tls'
define('SMTP_PORT', 587); // TCP port to connect to

define('MAIL_FROM_ADDRESS', 'egjini@bareshamusic.com'); // Sender's email address
define('MAIL_FROM_NAME', 'Emri Juaj'); // Sender's name
define('MAIL_TO_ADDRESS', 'egjini@bareshamusic.com'); // Recipient's email address
define('MAIL_TO_NAME', 'Emri i Mbeshtetësit'); // Recipient's name

define('LOG_FILE', __DIR__ . '/email_log.txt'); // Log file path
define('RATE_LIMIT', 5); // Max emails per IP
define('RATE_LIMIT_WINDOW', 3600); // Time window in seconds (1 hour)

/**
 * Logs a message to the defined log file with a timestamp.
 *
 * @param string $message The message to log.
 */
function logMessage($message)
{
    $date = date('Y-m-d H:i:s');
    file_put_contents(LOG_FILE, "[$date] $message" . PHP_EOL, FILE_APPEND);
}

/**
 * Determines the device type based on the user agent.
 *
 * @param string $userAgent The user's user agent string.
 * @return string The device type: Tablet, Mobile, or Laptop/Desktop.
 */
function getDeviceType($userAgent)
{
    $tabletKeywords = ['tablet', 'ipad'];
    $mobileKeywords = ['mobile', 'android', 'iphone', 'ipod', 'blackberry', 'windows phone'];

    foreach ($tabletKeywords as $keyword) {
        if (stripos($userAgent, $keyword) !== false) {
            return 'Tablet';
        }
    }

    foreach ($mobileKeywords as $keyword) {
        if (stripos($userAgent, $keyword) !== false) {
            return 'Mobile';
        }
    }

    return 'Laptop/Desktop';
}

/**
 * Checks if the given IP address has exceeded the rate limit.
 *
 * @param string $ipAddress The user's IP address.
 * @return bool True if rate limited, False otherwise.
 */
function isRateLimited($ipAddress)
{
    $currentTime = time();
    $logFile = __DIR__ . '/rate_limit.log';

    // Read existing rate limit data
    if (file_exists($logFile)) {
        $data = json_decode(file_get_contents($logFile), true);
    } else {
        $data = [];
    }

    // Initialize if not set
    if (!isset($data[$ipAddress])) {
        $data[$ipAddress] = [];
    }

    // Remove timestamps outside the rate limit window
    $data[$ipAddress] = array_filter($data[$ipAddress], function ($timestamp) use ($currentTime) {
        return ($currentTime - $timestamp) < RATE_LIMIT_WINDOW;
    });


    // Add current timestamp
    $data[$ipAddress][] = $currentTime;

    // Save back to the log file
    file_put_contents($logFile, json_encode($data));

    return false;
}

// Retrieve and sanitize the 'redirect' parameter
$redirectPage = $_GET['redirect'] ?? '';
$redirectPage = filter_var($redirectPage, FILTER_SANITIZE_URL);

// Define a whitelist of allowed redirect pages
$allowedPages = [
    // Specific pages with sendEmail redirection
    "index.php",
    "lista_kopjeve_rezerve.php",
    "strike-platform.php",
    "investime.php",
    'stafi.php',
    'roles.php',
    'rrogat.php',
    'aktiviteti.php',
    'office_investments.php',
    'office_damages.php',
    'office_requirements.php',
    'klient.php',
    'kategorit.php',
    'ads.php',
    'emails.php',
    'klient-avanc.php',
    'rating_list.php',
    'shtoy.php',
    'listang.php',
    'claim.php',
    'whitelist.php',
    'invoice.php',
    'faturat.php',
    'pagesat.php',
    'tatimi.php',
    'yinc.php',
    'shpenzimep.php',
    'pasqyrat.php',
    'pagesat_punetor.php',
    'shpenzimet_objekt.php',
    'ttatimi.php',
    'fitimi_pergjithshem.php',
    'kontabiliteti_pagesat.php',
    'filet.php',
    'notes.php',
    'takimet.php',
    'klient_CSV.php',
    'logs.php',
    'kontrata_2.php',
    'lista_kontratave.php',
    'ofertat.php',
    'kontrata_gjenelare_2.php',
    'lista_kontratave_gjenerale.php',
    'vegla_facebook.php',
    'faturaFacebook.php',
    'lista_faturave_facebook.php',
    'csvFiles.php',
    'filtroCSV.php',
    'listaEFaturaveTePlatformave.php',
    'pagesatEKryera.php',
    'platform_invoices.php',
    'currency.php',
    // Add other allowed pages here
];

// Validate redirect page
if (!in_array($redirectPage, $allowedPages)) {
    logMessage("Invalid redirect attempt to: $redirectPage from IP: " . $_SERVER['REMOTE_ADDR']);
    echo "<p class='text-danger'>Invalid redirect page.</p>";
    exit();
}

// Get user's IP address
$ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

// Enforce rate limiting
if (isRateLimited($ipAddress)) {
    logMessage("Rate limit exceeded for IP: $ipAddress");
    echo "<p class='text-danger'>You have exceeded the number of allowed requests. Please try again later.</p>";
    exit();
}

// Get user's user agent
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

// Determine device type
$deviceType = getDeviceType($userAgent);

// Prepare email subject
$subject = 'Artikulli i Menysë Klikuar - ' . htmlspecialchars($redirectPage, ENT_QUOTES, 'UTF-8');

// Initialize PHPMailer
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = SMTP_AUTH;
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;
    $mail->SMTPSecure = SMTP_SECURE;
    $mail->Port = SMTP_PORT;
    $mail->CharSet = 'UTF-8';

    // Recipients
    $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
    $mail->addAddress(MAIL_TO_ADDRESS, MAIL_TO_NAME);

    // Content
    $mail->isHTML(true);
    $mail->Subject = $subject;

    // Load email template
    $templatePath = 'email_template_al.html';
    if (!file_exists($templatePath)) {
        throw new Exception("Email template not found.");
    }
    $htmlContent = file_get_contents($templatePath);

    // Replace placeholders with actual values
    $placeholders = [
        '{redirect}' => htmlspecialchars($redirectPage, ENT_QUOTES, 'UTF-8'),
        '{time}' => date('Y-m-d H:i:s'),
        '{ip_address}' => htmlspecialchars($ipAddress, ENT_QUOTES, 'UTF-8'),
        '{user_agent}' => htmlspecialchars($userAgent, ENT_QUOTES, 'UTF-8'),
        '{device_type}' => htmlspecialchars($deviceType, ENT_QUOTES, 'UTF-8'),
    ];

    $htmlContent = str_replace(array_keys($placeholders), array_values($placeholders), $htmlContent);

    $mail->Body = $htmlContent;

    // Send the email
    $mail->send();
    logMessage("Email sent successfully for page: $redirectPage from IP: $ipAddress");

    // Redirect to the intended page
    header('Location: ' . $redirectPage);
    exit();
} catch (Exception $e) {
    // Log the error
    logMessage("Failed to send email for page: $redirectPage from IP: $ipAddress. Error: {$mail->ErrorInfo}");

    // Show a user-friendly error message
    echo "<p class='text-danger'>Sorry, we encountered an issue while processing your request. Please try again later.</p>";

    // Optionally, redirect to an error page
    // header('Location: error_page.php');
    exit();
}
