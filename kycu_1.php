<!DOCTYPE html>

<head>
    <meta name="google-site-verification" content="65Q9V_d_6p9mOYD05AFLNYLveEnM01AOs5cW2-qKrB0" />
</head>
<?php
session_start();
header("X-Frame-Options: DENY");
include('./config.php');

// Ensure the Google Client is properly initialized in config.php
use Google\Client;
use Google\Service\PeopleService;

$login_url = $client->createAuthUrl();
date_default_timezone_set('Europe/Tirane');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (isset($token['error'])) {
        header('Location: kycu_1.php');
        exit;
    }

    // Store access and refresh tokens in secure cookies
    $accessToken = $token['access_token'];
    $refreshToken = isset($token['refresh_token']) ? $token['refresh_token'] : null;
    setcookie('accessToken', $accessToken, time() + 3600, '/', '', true, true);
    setcookie('refreshToken', $refreshToken, time() + 86400 * 30, '/', '', true, true);

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
    setcookie('email', $email, time() + 86400, '/', '', true, true);
    setcookie('google_id', $google_id, time() + 86400, '/', '', true, true);
    setcookie('f_name', $f_name, time() + 86400, '/', '', true, true);
    setcookie('l_name', $l_name, time() + 86400, '/', '', true, true);
    setcookie('gender', $gender, time() + 86400, '/', '', true, true);
    setcookie('picture', $picture, time() + 86400, '/', '', true, true);

    // Optional: Database operations to store user info
    include('conn-d.php');
    $check_email = $conn->prepare("SELECT `email` FROM `googleauth` WHERE `email`=?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    if ($check_email->num_rows === 0) {
        $query_template = "INSERT INTO `googleauth` (`oauth_uid`, `firstName`, `last_name`, `email`, `profile_pic`, `gender`, `local`) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($query_template);
        $local = ''; // Define $local as needed
        $insert_stmt->bind_param("sssssss", $google_id, $f_name, $l_name, $email, $picture, $gender, $local);
        $insert_stmt->execute();
    }

    // Redirect to the main page after successful login
    header('Location: index.php');
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
        <div class="g-recaptcha" data-sitekey="6LfDuM8pAAAAAMkJTeKSVg0BBgqBw9LH8NBmeF4-" data-callback="enableLoginButton"></div>
        <a id="loginButton" href="<?= htmlspecialchars($login_url . '&session_id=' . session_id()) ?>">
            <i class="fab fa-google"></i> Identifikohu me Google
        </a>
        <div class="footer">
            <p>© 2024 Baresha Panel. Të gjitha të drejtat e rezervuara.</p>
            <p>Për ndihmë, kontaktoni info@bareshamusic.com</p>
        </div>
    </div>
    <script>
        function enableLoginButton() {
            document.getElementById('loginButton').style.display = 'block';
        }
    </script>
</body>

</html>