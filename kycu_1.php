<head>
    <meta name="google-site-verification" content="65Q9V_d_6p9mOYD05AFLNYLveEnM01AOs5cW2-qKrB0" />
</head>
<?php
include('./config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true); // Passing `true` enables exceptions
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
    setcookie('email', $email, time() + 86400, '/', '', true, true);
    setcookie('google_id', $google_id, time() + 86400, '/', '', true, true);
    setcookie('f_name', $f_name, time() + 86400, '/', '', true, true);
    setcookie('l_name', $l_name, time() + 86400, '/', '', true, true);
    setcookie('gender', $gender, time() + 86400, '/', '', true, true);
    setcookie('picture', $picture, time() + 86400, '/', '', true, true);
    // Retrieve IP address
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $userLog['ip_address'] = $ipAddress;
    // Retrieve user agent
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $userLog['user_agent'] = $userAgent;
    // Geolocation (using ip-api.com API)
    $ipInfo = file_get_contents("http://ip-api.com/json/{$ipAddress}");
    $ipData = json_decode($ipInfo, true);
    if ($ipData && $ipData['status'] == 'success') {
        $userLog['country'] = $ipData['country'];
        $userLog['city'] = $ipData['city'];
        $userLog['continent'] = $ipData['continent'];
        $userLog['continentCode'] = $ipData['continentCode'];
        $userLog['region'] = $ipData['region'];
        $userLog['regionName'] = $ipData['regionName'];
        $userLog['district'] = $ipData['district'];
        $userLog['zip'] = $ipData['zip'];
        $userLog['lat'] = $ipData['lat'];
        $userLog['lon'] = $ipData['lon'];
        $userLog['timezone'] = $ipData['timezone'];
        $userLog['offset'] = $ipData['offset'];
        $userLog['currency'] = $ipData['currency'];
        $userLog['isp'] = $ipData['isp'];
        $userLog['org'] = $ipData['org'];
        $userLog['as'] = $ipData['as'];
        $userLog['asname'] = $ipData['asname'];
        $userLog['mobile'] = $ipData['mobile'];
        $userLog['proxy'] = $ipData['proxy'];
        $userLog['hosting'] = $ipData['hosting'];
    }
    // Referer
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'N/A';
    $userLog['referer'] = $referer;
    // Session ID
    $sessionID = session_id();
    $userLog['session_id'] = $sessionID;
    // DNS Lookup
    $hostname = gethostbyaddr($ipAddress);
    $userLog['hostname'] = $hostname;
    // Logging logic
    $logDir = __DIR__ . '/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }
    $userLog['timestamp'] = date('Y-m-d H:i:s');
    $userLog['email'] = $email;
    $userLog['google_id'] = $google_id;
    $userLog['first_name'] = $f_name;
    $userLog['last_name'] = $l_name;
    $userLog['gender'] = $gender;
    $jsonLog = json_encode($userLog);
    $filename = $logDir . '/user_log_' . date('Y_m_d_His') . '_' . $email . '.json';
    file_put_contents($filename, $jsonLog . PHP_EOL, FILE_APPEND);
    // Sending email
    try {
        // Server settings
        $mail->isSMTP(); // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = 'egjini17@gmail.com'; // SMTP username
        $mail->Password = 'rhydniijtqzijjdy'; // SMTP password
        $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587; // TCP port to connect to
        $mail->setFrom('egjini17@gmail.com', 'Mailer');
        $mail->addAddress('egjini17@gmail.com', 'Enis Gjini');
        $mail->addReplyTo('egjini17@gmail.com', 'Information');
        // Attachments
        $mailSubject = 'Njoftimi per hyrjen ne sistem nga: ' . $f_name . ' ' . $l_name;
        $mailBodyHTML = <<<HTML
        <!DOCTYPE html>
        <html lang="sq">
        <head>
            <meta charset="UTF-8">
            <style>
                body {
                    font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                    padding: 20px;
                    background-color: #f9f9f9;
                    color: #333;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background: #ffffff;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                    padding: 20px;
                }
                h2 {
                    font-size: 22px;
                    text-align: center;
                    margin-top: 0;
                }
                p {
                    font-size: 16px;
                    line-height: 1.5;
                    color: #555;
                }
                .info-label {
                    font-weight: bold;
                }
                .user-picture {
                    width: 80px;
                    height: auto;
                    border-radius: 50%;
                    display: block;
                    margin: 10px auto;
                }
                .footer {
                    font-size: 12px;
                    text-align: center;
                    color: #aaa;
                    margin-top: 20px;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h2>Përshkrimi i detajeve të verifikimit të përdoruesit.</h2>
                <p><span class="info-label">Emri:</span> {$f_name}</p>
                <p><span class="info-label">Mbiemri:</span> {$l_name}</p>
                <p><span class="info-label">Email:</span> {$email}</p>
                <p><span class="info-label">Gjinia:</span> {$gender}</p>
                <p><span class="info-label">ID e Google:</span> {$google_id}</p>
                <p><span class="info-label">Vula kohore e kyçjes:</span> {$userLog['timestamp']}</p>
                <p><span class="info-label">IP Adresa:</span> {$userLog['ip_address']}</p>
                <p><span class="info-label">User Agent:</span> {$userLog['user_agent']}</p>
                <p><span class="info-label">Kombësia:</span> {$userLog['country']}</p>
                <p><span class="info-label">Qyteti:</span> {$userLog['city']}</p>
                <p><span class="info-label">Referer:</span> {$userLog['referer']}</p>
                <p><span class="info-label">Session ID:</span> {$userLog['session_id']}</p>
                <p><span class="info-label">Hostname:</span> {$userLog['hostname']}</p>
                <p><span class="info-label">Kontinenti:</span> {$userLog['continent']}</p>
                <p><span class="info-label">Kodi i kontinentit:</span> {$userLog['continentCode']}</p>
                <p><span class="info-label">Rajoni:</span> {$userLog['region']}</p>
                <p><span class="info-label">Emri i Rajonit:</span> {$userLog['regionName']}</p>
                <p><span class="info-label">Zona:</span> {$userLog['district']}</p>
                <p><span class="info-label">Kodi Postal:</span> {$userLog['zip']}</p>
                <p><span class="info-label">Gjerësia:</span> {$userLog['lat']}</p>
                <p><span class="info-label">Gjatësia:</span> {$userLog['lon']}</p>
                <p><span class="info-label">Zona Kohore:</span> {$userLog['timezone']}</p>
                <p><span class="info-label">Diferenca kohore:</span> {$userLog['offset']}</p>
                <p><span class="info-label">Valuta:</span> {$userLog['currency']}</p>
                <p><span class="info-label">ISP:</span> {$userLog['isp']}</p>
                <p><span class="info-label">ORG:</span> {$userLog['org']}</p>
                <p><span class="info-label">AS:</span> {$userLog['as']}</p>
                <p><span class="info-label">Emri i AS:</span> {$userLog['asname']}</p>
                <p><span class="info-label">Mobil:</span> {$userLog['mobile']}</p>
                <p><span class="info-label">Proxy:</span> {$userLog['proxy']}</p>
                <p><span class="info-label">Hosting:</span> {$userLog['hosting']}</p>
                <img src="{$picture}" alt="Fotoja e Përdoruesit" class="user-picture">
                <p>Ky email përmban detajet e kyçjes së një përdoruesi që së fundmi është kyçur në sistem.</p>
                <div class="footer">
                    &copy; {$userLog['timestamp']} Baresha Network. Të gjitha të drejtat të rezervuara.
                </div>
            </div>
        </body>
        </html>
HTML;
        $mailBodyPlainText = "Përshkrimi i detajeve të verifikimit të përdoruesit\n\nEmri: {$f_name}\nMbiemri: {$l_name}\nEmail: {$email}\nGjinia: {$gender}\nID e Google: {$google_id}\nVula Kohore e Kyçjes: {$userLog['timestamp']}\nIP Adresa: {$userLog['ip_address']}\nUser Agent: {$userLog['user_agent']}\nKombësia: {$userLog['country']}\nQyteti: {$userLog['city']}\nReferer: {$userLog['referer']}\nSession ID: {$userLog['session_id']}\nHostname: {$userLog['hostname']}\nKontinenti: {$userLog['continent']}\nKodi i kontinentit: {$userLog['continentCode']}\nRajoni: {$userLog['region']}\nEmri i Rajonit: {$userLog['regionName']}\nZona: {$userLog['district']}\nKodi Postal: {$userLog['zip']}\nGjerësia: {$userLog['lat']}\nGjatësia: {$userLog['lon']}\nZona Kohore: {$userLog['timezone']}\nDiferenca kohore: {$userLog['offset']}\nValuta: {$userLog['currency']}\nISP: {$userLog['isp']}\nORG: {$userLog['org']}\nAS: {$userLog['as']}\nEmri i AS: {$userLog['asname']}\nMobil: {$userLog['mobile']}\nProxy: {$userLog['proxy']}\nHosting: {$userLog['hosting']}\nKy email përmban detajet e kyçjes së një përdoruesi që së fundmi është kyçur në sistem.";
        // Assigning subject and body
        $mail->Subject = $mailSubject;
        $mail->Body = $mailBodyHTML;
        $mail->AltBody = $mailBodyPlainText;
        $mail->send();
    } catch (Exception $e) {
        // Exception handling
    }
    // Database operations
    include('conn-d.php');
    $check_email = $conn->prepare("SELECT `email` FROM `googleauth` WHERE `email`=?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();
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
                            <p class="text-muted">Identifikohu me llogarin&euml; t&euml;nde t&euml; Google.</p>
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