<head>
    <meta name="google-site-verification" content="65Q9V_d_6p9mOYD05AFLNYLveEnM01AOs5cW2-qKrB0" />
</head>
<?php
include('./config.php');
$login_url = $client->createAuthUrl();
date_default_timezone_set('Europe/Tirane');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (isset($token['error'])) {
        header('Location: kycu_1.php');
        exit;
    }

    $accessToken = $token['access_token'];
    $refreshToken = isset($token['refresh_token']) ? $token['refresh_token'] : null;
    setcookie('accessToken', $accessToken, time() + 3600, '/', '', true, true);
    setcookie('refreshToken', $refreshToken, time() + 86400 * 30, '/', '', true, true);

    $client->setAccessToken($token);
    $people_service = new Google\Service\PeopleService($client);
    $user_info = $people_service->people->get('people/me', ['personFields' => 'names,emailAddresses,genders,photos']);

    $email = $user_info->getEmailAddresses()[0]->getValue();
    $google_id = $user_info->getNames()[0]->getMetadata()->getSource()->getId();
    $f_name = $user_info->getNames()[0]->getGivenName();
    $l_name = $user_info->getNames()[0]->getFamilyName();
    $gender = !empty($user_info->getGenders()) ? $user_info->getGenders()[0]->getValue() : "";
    $picture = $user_info->getPhotos()[0]->getUrl();

    include('conn-d.php');
    $check_email = $conn->prepare("SELECT `email` FROM `googleauth` WHERE `email`=?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    // Logging logic starts here
    $logDir = __DIR__ . '/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }

    $userLog = [
        'timestamp' => date('Y-m-d H:i:s'),
        'email' => $email,
        'google_id' => $google_id,
        'first_name' => $f_name,
        'last_name' => $l_name,
        'gender' => $gender,
        // Additional fields can be added here
    ];
    $jsonLog = json_encode($userLog);

    $filename = $logDir . '/user_log_' . date('Y_m_d_His') . '_' . $email . '.json';
    file_put_contents($filename, $jsonLog . PHP_EOL, FILE_APPEND);
    // Logging logic ends here

    if ($check_email->num_rows === 0) {
        $query_template = "INSERT INTO `googleauth` (`oauth_uid`, `firstName`, `last_name`,`email`,`profile_pic`,`gender`,`local`) VALUES (?,?,?,?,?,?,?)";
        $insert_stmt = $conn->prepare($query_template);
        $insert_stmt->bind_param("sssssss", $google_id, $f_name, $l_name, $email, $picture, $gender, $local);
        $insert_stmt->execute();
    }

    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="vendors/base/vendor.bundle.base.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" href="images/favicon.png" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.0.1/mdb.min.css" rel="stylesheet" />
    <title>Baresha Panel - Google Login</title>
    <script src="https://kit.fontawesome.com/a1927a49ea.js" crossorigin="anonymous"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <!-- <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' http://paneli.bareshaoffice.com;"> -->
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5 rounded-6 shadow-4">
                            <div class="brand-logo">
                                <img src="images/logob.png" alt="logo">
                            </div>
                            <p class="font-weight-light">P&euml;rsh&euml;ndetje!</p>
                            <p class="text-muted">Identifikohu me llogarinë tënde të Google.</p>
                            <!-- Display the reCAPTCHA widget -->
                            <div class="g-recaptcha" data-sitekey="6LdT2w0pAAAAAJu92-zDVcDBinqaqT08sZhDbMfx" data-callback="enableLoginButton"></div>
                            <!-- Replace the button with an anchor tag -->
                            <a id="loginButton" href="<?= $login_url ?>" style="text-transform: none; display: none;" class="btn btn-light border shadow btn-sm">
                                <img src="https://tinyurl.com/46bvrw4s" alt="Google Logo" width="20" class="me-2">
                                Identifikohu me Google
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Your existing script tags go here -->
    <script>
        // Enable the login button after reCAPTCHA is successfully completed
        function enableLoginButton() {
            // Display the login button
            document.getElementById('loginButton').style.display = 'inline-block';
        }
    </script>
    <script src="vendors/base/vendor.bundle.base.js" defer></script>
    <script src="js/off-canvas.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/template.js"></script>
</body>

</html>