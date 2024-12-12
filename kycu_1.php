<?php
session_start();
header("X-Frame-Options: DENY");

// Include Composer's autoloader
require_once 'vendor/autoload.php';

use Google\Client;
use Google\Service\PeopleService;

// ==========================
// Configuration
// ==========================

// **Warning:** Storing sensitive information directly in code is not recommended.
// Consider using environment variables or secure configuration files instead.
$clientConfig = [
    "web" => [
        "client_id" => "650026602310-8g611qsm0a5ftolpd5flgq0nncm6be2p.apps.googleusercontent.com",
        "project_id" => "kinetic-horizon-357319",
        "auth_uri" => "https://accounts.google.com/o/oauth2/auth",
        "token_uri" => "https://oauth2.googleapis.com/token",
        "auth_provider_x509_cert_url" => "https://www.googleapis.com/oauth2/v1/certs",
        "client_secret" => "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",
        "redirect_uris" => [
            "http://localhost/BareshaNetwork_v3.2/kycu_1.php",
            "https://panel.bareshaoffice.com/kycu_1.php",
            "http://panel.bareshaoffice.com/kycu_1.php"
        ]
    ]
];

// Define constants for redirect URIs
define('LOCALHOST_URI', "http://localhost/BareshaNetwork_v3.2/kycu_1.php");
define('ONLINE_URI', "https://panel.bareshaoffice.com/kycu_1.php");

// Initialize Google Client
$client = new Client();
$client->setClientId($clientConfig['web']['client_id']);
$client->setClientSecret($clientConfig['web']['client_secret']);
$client->setAccessType('offline'); // Request offline access
$client->setApprovalPrompt('force'); // Force re-authorization

// Determine the environment and set redirect URI accordingly
$isLocal = ($_SERVER['SERVER_NAME'] === 'localhost');
$redirectUri = $isLocal ? LOCALHOST_URI : ONLINE_URI;
$client->setRedirectUri($redirectUri);

// Set scopes
$client->setScopes(["email", "profile"]);

// ==========================
// CSRF Protection - State Parameter
// ==========================
if (!isset($_SESSION['oauth2_state'])) {
    $_SESSION['oauth2_state'] = bin2hex(random_bytes(16));
}
$client->setState($_SESSION['oauth2_state']);

// Set prompt to ensure refresh_token is received
$client->setPrompt('select_account consent');

// Generate the OAuth 2.0 authorization URL
$login_url = $client->createAuthUrl();

// Set default timezone
date_default_timezone_set('Europe/Tirane');

// ==========================
// Handle OAuth Callback
// ==========================
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['code'])) {
    // Validate state parameter to prevent CSRF
    if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2_state'])) {
        error_log('Invalid OAuth state');
        header('Location: kycu_1.php?error=invalid_state');
        exit;
    }

    // Exchange authorization code for access token
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (isset($token['error'])) {
        error_log('OAuth Error: ' . $token['error_description']);
        header('Location: kycu_1.php?error=oauth_error');
        exit;
    }

    // Store tokens in secure cookies
    $accessToken = $token['access_token'];
    $refreshToken = isset($token['refresh_token']) ? $token['refresh_token'] : null;

    $cookieOptions = [
        'expires' => time() + 3600, // 1 hour
        'path' => '/',
        'domain' => '', // Set to your domain if needed
        'secure' => true, // Ensure cookies are sent over HTTPS
        'httponly' => true, // Prevent JavaScript access to cookies
        'samesite' => 'Lax' // CSRF protection
    ];
    setcookie('accessToken', $accessToken, $cookieOptions);
    if ($refreshToken) {
        setcookie('refreshToken', $refreshToken, [
            'expires' => time() + (86400 * 30), // 30 days
            'path' => '/',
            'domain' => '',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }

    // Set the access token for the client
    $client->setAccessToken($token);

    // Initialize the People Service
    $people_service = new PeopleService($client);
    $user_info = $people_service->people->get('people/me', ['personFields' => 'names,emailAddresses,genders,photos']);

    // Extract user information
    $email = $user_info->getEmailAddresses()[0]->getValue();
    $google_id = $user_info->getNames()[0]->getMetadata()->getSource()->getId();
    $f_name = $user_info->getNames()[0]->getGivenName();
    $l_name = $user_info->getNames()[0]->getFamilyName();
    $gender = !empty($user_info->getGenders()) ? $user_info->getGenders()[0]->getValue() : "";
    $picture = $user_info->getPhotos()[0]->getUrl();

    // Store user information in secure cookies
    setcookie('email', $email, $cookieOptions);
    setcookie('google_id', $google_id, $cookieOptions);
    setcookie('f_name', $f_name, $cookieOptions);
    setcookie('l_name', $l_name, $cookieOptions);
    setcookie('gender', $gender, $cookieOptions);
    setcookie('picture', $picture, $cookieOptions);

    // ==========================
    // Database Operations
    // ==========================
    // **Note:** Replace 'conn-d.php' contents with your actual database connection code.
    // Ensure that 'conn-d.php' is secure and not accessible publicly.
    include('conn-d.php'); // This should establish a $conn variable for the database connection

    // Prepare and execute a statement to check if the email exists
    $check_email = $conn->prepare("SELECT `email` FROM `googleauth` WHERE `email` = ?");
    if ($check_email === false) {
        error_log('Prepare failed: ' . $conn->error);
        header('Location: kycu_1.php?error=database_error');
        exit;
    }
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    if ($check_email->num_rows === 0) {
        // Prepare and execute an insert statement
        $query_template = "INSERT INTO `googleauth` (`oauth_uid`, `firstName`, `last_name`, `email`, `profile_pic`, `gender`, `local`) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($query_template);
        if ($insert_stmt === false) {
            error_log('Prepare failed: ' . $conn->error);
            header('Location: kycu_1.php?error=database_error');
            exit;
        }
        $local = ''; // Define $local as needed
        $insert_stmt->bind_param("sssssss", $google_id, $f_name, $l_name, $email, $picture, $gender, $local);
        $insert_stmt->execute();
        $insert_stmt->close();
    }
    $check_email->close();
    $conn->close();

    // Regenerate session ID to prevent session fixation
    session_regenerate_id(true);

    // Redirect to the main page after successful login
    header('Location: index.php');
    exit;
}

// ==========================
// Handle Login Form Submission with reCAPTCHA
// ==========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify reCAPTCHA
    $recaptchaSecret = '6LfDuM8pAAAAAFIdDn0EuoAQ_qh8FIMppgJOQts_'; // Replace with your actual secret key
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    // Make and decode POST request:
    $recaptcha = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$recaptchaResponse}");
    $recaptcha = json_decode($recaptcha, true);

    // Take action based on the score returned:
    if ($recaptcha["success"] !== true) {
        // reCAPTCHA failed
        die('reCAPTCHA verification failed. Please try again.');
    }

    // If reCAPTCHA is successful, redirect to Google's OAuth 2.0 server
    header('Location: ' . filter_var($login_url, FILTER_SANITIZE_URL));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Baresha Panel - Google Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a1927a49ea.js" crossorigin="anonymous"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        * {
            text-align: center;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: white;
            border-radius: 15px;
            /* box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); */
            border: 1px solid #ccc;
            padding: 2rem;
            width: 100%;
            max-width: 400px;
        }

        .brand-logo {
            text-align: center;
            margin-bottom: 1rem;
        }

        .brand-logo img {
            max-width: 150px;
        }

        h1 {
            color: #333;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        p {
            color: #666;
            margin-bottom: 1rem;
        }

        .features {
            margin-bottom: 1rem;
        }

        .features li {
            margin-bottom: 0.5rem;
        }

        .g-recaptcha {
            margin-bottom: 1rem;
        }

        #loginButton {
            display: none;
            width: 100%;
            padding: 0.75rem;
            background-color: #4285F4;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            text-align: center;
        }

        #loginButton:hover {
            background-color: #357AE8;
        }

        .footer {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.8rem;
            color: #888;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="brand-logo">
            <img src="images/logob.png" alt="Baresha Panel Logo">
        </div>
        <h1>Mirë se vini në Baresha Panel</h1>
        <p>Identifikohu me llogarinë tënde të Google për të aksesuar panelin e administrimit.</p>
        <form method="POST" action="kycu_1.php">
            <div class="g-recaptcha" data-sitekey="6LfDuM8pAAAAAMkJTeKSVg0BBgqBw9LH8NBmeF4-" data-callback="enableLoginButton"></div>
            <button id="loginButton" type="submit" disabled>
                <i class="fab fa-google"></i> Identifikohu me Google
            </button>
        </form>
        <div class="footer">
            <p>© 2024 Baresha Panel. Të gjitha të drejtat e rezervuara.</p>
            <p>Për ndihmë, kontaktoni info@bareshamusic.com</p>
        </div>
    </div>
    <script>
        function enableLoginButton() {
            document.getElementById('loginButton').disabled = false;
            document.getElementById('loginButton').style.display = 'block';
        }
    </script>
</body>

</html>