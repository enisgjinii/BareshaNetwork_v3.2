<?php
// Include autoload file and start session
require_once 'vendor/autoload.php';
session_start();

// Include configuration file
$config = require_once 'second_config.php';

// Initialize Google client
$client = initializeGoogleClient($config);

// Check if authentication code is present
if (isset($_GET['code'])) {
    handleAuthentication($client);
}

// If the user is not authenticated, display the authentication link
echo '<a href="' . $client->createAuthUrl() . '">Click here to authenticate</a>';

// Function to initialize Google client
function initializeGoogleClient($config)
{
    $client = new Google_Client();
    $client->setClientId($config['client_id']);
    $client->setClientSecret($config['client_secret']);
    $client->setRedirectUri($config['redirect_uri']);
    $client->setAccessType('offline');
    $client->setApprovalPrompt('force');

    $client->addScope([
        'https://www.googleapis.com/auth/youtube',
        'https://www.googleapis.com/auth/youtube.readonly',
        'https://www.googleapis.com/auth/youtubepartner',
        'https://www.googleapis.com/auth/yt-analytics-monetary.readonly',
        'https://www.googleapis.com/auth/yt-analytics.readonly'
    ]);

    return $client;
}

// Function to handle authentication
function handleAuthentication($client)
{
    try {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

        $youtube = new Google\Service\YouTube($client);
        $channels = $youtube->channels->listChannels('snippet', ['mine' => true]);
        $channel = $channels->items[0];
        $channelId = $channel->id;
        $channelName = $channel->snippet->title;

        if (isset($token['refresh_token'])) {
            $refreshToken = $token['refresh_token'];
            storeRefreshTokenInDatabase($refreshToken, $channelId, $channelName);
            // After storing refresh token in the database
            sendEmail($channelName);
        }

        $_SESSION['refresh_token'] = $refreshToken;

        echo "<script>console.log('Refresh Token: " . json_encode($refreshToken) . "');</script>";
        echo "<script>console.log('Channel ID: $channelId');</script>";
        echo "<script>console.log('Channel Name: $channelName');</script>";

        // Redirect to a different page after authentication
        header('Location: invoice.php');
        exit;
    } catch (Google\Service\Exception $e) {
        echo '<pre>';
        print_r(json_decode($e->getMessage()));
        echo '</pre>';
    }
}
// Function to send email
function sendEmail($channelName)
{
    require 'vendor/autoload.php'; // Include PHPMailer autoloader

    // Create a new PHPMailer instance
    $mail = new PHPMailer\PHPMailer\PHPMailer();

    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';  // Specify SMTP server
    $mail->SMTPAuth   = true;               // Enable SMTP authentication
    $mail->Username   = 'kastriot@bareshamusic.com';   // SMTP username
    $mail->Password   = 'xpuurhlkncbzhdyg';             // SMTP password
    $mail->SMTPSecure = 'tls';              // Enable TLS encryption, `ssl` also accepted
    $mail->Port       = 587;                // TCP port to connect to

    // Recipients
    $mail->setFrom('kastriot@bareshamusic.com', 'Baresha Network');
    $mail->addAddress('kastriot@bareshamusic.com', 'Recipient Name');
    $mail->addAddress('egjini@bareshamusic.com', 'Recipient Name'); // Add a recipient

    // Content
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = 'Kanali juaj është lidhur me Baresha Network';
    $mail->CharSet = 'UTF-8';

    $mail->Body = "
    <html>
    <head>
    </head>
    <body style='font-family: Arial, sans-serif; margin: 50px; padding: 0; background-color: #f4f4f4;'>
        <div style='max-width: 600px; margin: 0 auto; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);'>
            <div style='background-color:#fff; padding: 30px;border-style: 1px solid black; border-radius: 10px; text-align: center;'>
                <img src='cid:favicon' alt='Logo' width='125' style='display: block; margin: 0 auto 20px; border-radius: 50%;'>
                <h2 style='text-align: center; color: #333; margin-bottom: 30px;'>Emri i kanalit: $channelName</h2>
                <p style='text-align: center; color: #666; font-size: 16px; margin-bottom: 30px; line-height: 1.5;'>Faleminderit për lidhjen me Baresha Network! Kanali juaj është gati të filloni të përfitoni nga shërbimet tona të personalizuara për zhvillimin e kanalit tuaj.</p>
                <hr style='border-top: 1px solid #ddd; margin-bottom: 20px;'>
                <p style='margin: 0; color: #999; margin-bottom: 5px;'>Faleminderit!</p>
                <p style='margin: 0; color: #999; font-size: 12px;'>Baresha Network L.L.C</p>
                <p style='margin: 0; color: #999; font-size: 12px;'>+383 48 151 200</p>
            </div>
        </div>
    </body>
    </html>
";
    $mail->AltBody = 'Congratulations! Your channel ' . $channelName . ' has been connected successfully.';
    $mail->AddEmbeddedImage('./images/favicon.png', 'favicon');

    // Send the email
    if (!$mail->send()) {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Message has been sent';
    }
}




// Function to store refresh token in database
function storeRefreshTokenInDatabase($refreshToken, $channelId, $channelName)
{
    $config = require 'second_config.php';
    $conn = new mysqli($config['db_host'], $config['db_user'], $config['db_password'], $config['db_name']);

    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    $refreshToken = $conn->real_escape_string($refreshToken);
    $channelId = $conn->real_escape_string($channelId);
    $channelName = $conn->real_escape_string($channelName);

    $sql = "INSERT INTO refresh_tokens (token, channel_id, channel_name) VALUES ('$refreshToken', '$channelId', '$channelName')";
    $conn->query($sql);

    $conn->close();
}
