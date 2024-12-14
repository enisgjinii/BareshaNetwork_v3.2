<?php
// Parandalon mbushjen e faqes brenda frame-ve nga domain-e të ndryshme
header("X-Frame-Options: DENY");

// Përfshin konfigurimin dhe konektimin me databazë
include('./config.php');
include('conn-d.php');

// Përcakton nivelin e raportimit të gabimeve
error_reporting(1);
ini_set('display_errors', 1);

// Starton sesionin nëse nuk është startuar
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Funksion për redirektim në rast gabimi autentikimi
function handleAuthenticationError()
{
    header('Location: denied.php');
    exit;
}

// Kontrollon nëse refreshToken ekziston në cookies
if (!isset($_COOKIE['refreshToken'])) {
    header('Location: kycu_1.php');
    exit;
}

try {
    // Rifreskon token-in duke përdorur refresh token-in nga cookie
    $client->refreshToken($_COOKIE['refreshToken']);
    $accessToken = $client->getAccessToken();
    if ($accessToken == null) handleAuthenticationError();
    $_SESSION['token'] = $accessToken;
} catch (Exception $e) {
    handleAuthenticationError();
}

// Forcon skadimin e token-it aktual për të siguruar të dhëna të freskëta
$_SESSION['token']['expires_at'] = time() - 1;

// Krijon objektin OAuth2 nga Google client
$google_oauth = new Google\Service\Oauth2($client);

try {
    // Vendos token-in e rifreskuar dhe merr informacionin e përdoruesit
    $google_oauth->getClient()->setAccessToken($_SESSION['token']);
    $user_info = $google_oauth->userinfo->get();
} catch (Google_Service_Exception $e) {
    handleAuthenticationError();
}

// Lista e email-ave të lejuar (domene ose email specifik)
$allowedGmailEmails = [
    'afrimkolgeci@gmail.com',
    'besmirakolgeci1@gmail.com',
    'egjini17@gmail.com',
    'bareshafinance@gmail.com',
    'gjinienis148@gmail.com',
    'emrushavdyli9@gmail.com'
];

// Funksion për të kontrolluar nëse email-i ka domain ose vlerë të lejuar
function isValidEmailDomain($email, $allowedEmails)
{
    $domain = substr(strrchr($email, "@"), 1);
    return in_array($email, $allowedEmails) || $domain === 'bareshamusic.com';
}

// Kontrollon nëse email-i i përdoruesit është i lejuar
if (empty($user_info['email']) || !isValidEmailDomain($user_info['email'], $allowedGmailEmails)) {
    handleAuthenticationError();
}

$email = $user_info['email'];

// Merr të dhënat e përdoruesit nga databaza bazuar në email
$sql = "SELECT * FROM googleauth WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Nëse përdoruesi ekziston, ruaj të dhënat në sesion
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION['id'] = $row['id'];
    $_SESSION['email'] = $row['email'];
    $_SESSION['oauth_uid'] = $row['oauth_uid'];
} else {
    handleAuthenticationError();
}

// Mbyll deklaratën e përgatitur
$stmt->close();

// Implementimi i gjurmimit të aktivitetit të përdoruesit
$user_id = $_SESSION['id'];
$page = basename($_SERVER['PHP_SELF']);
$activity_time = date('Y-m-d H:i:s');
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$session_id = session_id();
$additional_data = json_encode(['get_params' => $_GET, 'post_params' => $_POST]);

// Fut të dhënat e aktivitetit në databazë
$track_stmt = $conn->prepare("
    INSERT INTO user_activity (
        user_id, page, activity_time, ip_address, user_agent, session_id, additional_data
    ) VALUES (?, ?, ?, ?, ?, ?, ?)
");
$track_stmt->bind_param("issssss", $user_id, $page, $activity_time, $ip_address, $user_agent, $session_id, $additional_data);

if (!$track_stmt->execute()) {
    error_log("Gabim gjatë futjes së aktivitetit: " . $track_stmt->error);
}

$track_stmt->close();

// Lista e CSS që do të përfshihen
$cssFiles = [
    'https://cdn-uicons.flaticon.com/2.1.0/uicons-regular-rounded/css/uicons-regular-rounded.css',
    'https://cdn-uicons.flaticon.com/uicons-brands/css/uicons-brands.css',
    'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',
    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
    'https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.5/af-2.6.0/b-2.4.1/b-colvis-2.4.1/b-html5-2.4.1/b-print-2.4.1/cr-1.7.0/date-1.5.1/fc-4.3.0/fh-3.4.0/kt-2.10.0/r-2.5.0/rg-1.4.0/rr-1.4.1/sc-2.2.0/sb-1.5.0/sp-2.2.0/sl-1.7.0/sr-1.3.0/datatables.min.css',
    'mdb5/css/mdb.min.css',
    'vendors/mdi/css/materialdesignicons.min.css',
    'vendors/base/vendor.bundle.base.css',
    'css/style.css',
    'partials/style.css',
    'https://cdn.datatables.net/datetime/1.4.0/css/dataTables.dateTime.min.css',
    'https://code.jquery.com/ui/1.13.0/themes/smoothness/jquery-ui.min.css',
    'https://unpkg.com/mobius1-selectr@latest/dist/selectr.min.css',
    'https://fonts.googleapis.com/css2?family=Inter+Tight:ital,wght@0,500;1,500&display=swap',
    'https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/plugins/monthSelect/style.min.css',
    'https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css',
];

// Lista e JS që do të përfshihen
$jsFiles = [
    'https://cdn.jsdelivr.net/npm/flatpickr',
    'https://cdnjs.cloudflare.com/ajax/libs/darkreader/4.9.58/darkreader.js',
    'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js',
    'https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.5/af-2.6.0/b-2.4.1/b-colvis-2.4.1/b-html5-2.4.1/b-print-2.4.1/cr-1.7.0/date-1.5.1/fc-4.3.0/fh-3.4.0/kt-2.10.0/r-2.5.0/rg-1.4.0/rr-1.4.1/sc-2.2.0/sb-1.5.0/sp-2.2.0/sl-1.7.0/sr-1.3.0/datatables.min.js',
    'https://cdn.jsdelivr.net/npm/sweetalert2@11',
    'https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js',
    'plugins/dark-reader/darkreader.js',
    'https://unpkg.com/mobius1-selectr@latest/dist/selectr.min.js',
    'https://unpkg.com/xlsx/dist/xlsx.full.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js',
    'https://cdn.jsdelivr.net/npm/moment/moment.min.js',
    'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js',
    'https://cdn.jsdelivr.net/npm/apexcharts'
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="shortcut icon" href="images/favicon.png" />
    <?php
    // Përfshin fajlat CSS
    foreach ($cssFiles as $css) {
        echo "<link rel='stylesheet' href='$css'>\n";
    }
    // Përfshin fajlat JS
    foreach ($jsFiles as $js) {
        echo "<script src='$js'></script>\n";
    }

    // Merr emrin e faqes aktuale dhe vendos titullin
    $current_page = basename($_SERVER['PHP_SELF'], '.php');
    $page_title = ucwords(str_replace('_', ' ', $current_page));
    ?>
    <title><?= $page_title ?> | Baresha Network</title>
    <!-- Stile të personalizuara -->
    <style>
        * {
            font-family: "Inter Tight", serif;
            font-optical-sizing: auto;
            font-weight: 500;
            font-style: normal;
        }

        body {
            transition: background-color .3s, color .3s
        }

        .btn-icon {
            font-size: 1.5rem
        }

        .nav-item {
            color: #fff
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        .fade-up {
            animation: fadeUp .5s ease-in-out
        }

        .toggle-button {
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            width: 120px;
            height: 50px;
            border-radius: 10px;
            border: 1px solid lightgrey;
            padding: 5px;
            background-color: #dddddd
        }

        .toggle-button .toggle-icon {
            font-size: 16px;
            color: #000
        }

        .toggle-button.dark-mode .toggle-icon {
            color: #fff
        }

        .toggle-switch {
            position: relative;
            width: 60px;
            height: 30px;
            background-color: #555;
            border-radius: 15px
        }

        .toggle-switch input[type="checkbox"] {
            display: none
        }

        .toggle-switch input[type="checkbox"]+label {
            position: absolute;
            top: 0;
            left: 0;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .2);
            cursor: pointer;
            transition: transform .3s ease-in-out
        }

        .toggle-switch input[type="checkbox"]:checked+label {
            transform: translateX(30px)
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px
        }

        .pagination a {
            margin: 0 5px;
            padding: 5px 10px;
            border: 1px solid #ccc;
            text-decoration: none;
            color: #333;
            border-radius: 5px
        }

        .pagination a.active {
            background-color: #007bff;
            color: #fff
        }

        .pagination a.disabled {
            color: #ccc;
            pointer-events: none
        }

        [data-toggle="tooltip"] {
            position: relative;
            cursor: pointer
        }

        [data-toggle="tooltip"]::after {
            content: attr(title);
            position: absolute;
            left: 90%;
            bottom: 90%;
            transform: translateX(-25%);
            padding: 7px;
            white-space: nowrap;
            background-color: white;
            border: 1px solid lightgray;
            color: black;
            margin-bottom: 3px;
            border-radius: 7px;
            font-size: 12px;
            opacity: 0;
            pointer-events: none;
            transition: opacity .5s ease-in-out;
            z-index: 5
        }

        [data-toggle="tooltip"]:hover::after {
            opacity: 1
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(20px)
            }

            100% {
                opacity: 1;
                transform: translateY(0)
            }
        }

        @keyframes slideIn {
            0% {
                opacity: 0;
                transform: translateX(-20px)
            }

            100% {
                opacity: 1;
                transform: translateX(0)
            }
        }

        .dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 75%;
            cursor: pointer
        }

        .dot:hover::before {
            content: attr(title);
            position: absolute;
            display: block;
            padding: 5px;
            border: 1px solid #ccc;
            background-color: white;
            color: black;
            border-radius: 5px;
            z-index: 1;
            margin-left: -10px;
            margin-top: -30px
        }

        .input-custom-css {
            background-color: #fff;
            border: 1px solid #d5d9d9;
            border-radius: 8px;
            box-shadow: rgba(213, 217, 217, .5)0 2px 5px 0;
            box-sizing: border-box;
            color: #0f1111;
            cursor: pointer;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
            vertical-align: middle
        }

        .input-custom-css-disabled {
            background-color: #fff;
            opacity: .6;
            border: 1px solid #d5d9d9;
            border-radius: 8px;
            box-shadow: rgba(213, 217, 217, .5)0 2px 5px 0;
            box-sizing: border-box;
            color: #0f1111;
            cursor: not-allowed;
            font-family: "Amazon Ember", sans-serif;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
            vertical-align: middle
        }

        .input-custom-css:hover {
            background-color: #f7fafa
        }

        @keyframes fadeIn {
            from {
                opacity: 0
            }

            to {
                opacity: 1
            }
        }
    </style>
</head>

<body>
    <?php include "partials/navbar.php"; ?>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper">
            <?php include "partials/sidebar.php"; ?>