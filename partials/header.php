<?php
header("X-Frame-Options: DENY");
include('./config.php');
include('conn-d.php');
error_reporting(1);
ini_set('display_errors', 1);
function handleAuthenticationError()
{
  header('Location: denied.php');
  exit;
}
if (!isset($_COOKIE['refreshToken'])) {
  header('Location: kycu_1.php');
  exit;
}
try {
  $client->refreshToken($_COOKIE['refreshToken']);
  $accessToken = $client->getAccessToken();
  if ($accessToken == null) handleAuthenticationError();
  $_SESSION['token'] = $accessToken;
} catch (Exception $e) {
  handleAuthenticationError();
}
$_SESSION['token']['expires_at'] = time() - 1;
$google_oauth = new Google\Service\Oauth2($client);
try {
  $google_oauth->getClient()->setAccessToken($_SESSION['token']);
  $user_info = $google_oauth->userinfo->get();
} catch (Google_Service_Exception $e) {
  handleAuthenticationError();
}
$allowedGmailEmails = ['afrimkolgeci@gmail.com', 'besmirakolgeci1@gmail.com', 'egjini17@gmail.com', 'bareshafinance@gmail.com', 'gjinienis148@gmail.com', 'emrushavdyli9@gmail.com'];
if (empty($user_info['email']) || !isValidEmailDomain($user_info['email'], $allowedGmailEmails)) {
  handleAuthenticationError();
}
$email = $user_info['email'];
$sql = "SELECT * FROM googleauth WHERE email = '$email'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $_SESSION['id'] = $row['id'];
  $_SESSION['email'] = $row['email'];
  $_SESSION['oauth_uid'] = $row['oauth_uid'];
} else {
  handleAuthenticationError();
}
function isValidEmailDomain($email, $allowedDomains)
{
  $domain = substr(strrchr($email, "@"), 1);
  return in_array($email, $allowedDomains) || $domain === 'bareshamusic.com';
}
$cssFiles = [
  'https://cdn-uicons.flaticon.com/2.1.0/uicons-regular-rounded/css/uicons-regular-rounded.css',
  'https://cdn-uicons.flaticon.com/uicons-brands/css/uicons-brands.css',
  'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',
  'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
  'assets/fontawesome-free-6.4.0-web/css/all.min.css',
  'https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.5/af-2.6.0/b-2.4.1/b-colvis-2.4.1/b-html5-2.4.1/b-print-2.4.1/cr-1.7.0/date-1.5.1/fc-4.3.0/fh-3.4.0/kt-2.10.0/r-2.5.0/rg-1.4.0/rr-1.4.1/sc-2.2.0/sb-1.5.0/sp-2.2.0/sl-1.7.0/sr-1.3.0/datatables.min.css',
  'mdb5/css/mdb.min.css',
  'vendors/mdi/css/materialdesignicons.min.css',
  'vendors/base/vendor.bundle.base.css',
  'css/style.css',
  'partials/style.css',
  'https://cdn.datatables.net/datetime/1.4.0/css/dataTables.dateTime.min.css',
  'https://code.jquery.com/ui/1.13.0/themes/smoothness/jquery-ui.min.css',
  'https://unpkg.com/mobius1-selectr@latest/dist/selectr.min.css',
  'https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css',
  'https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css',
  'https://fonts.googleapis.com/css2?family=Inter&display=swap'
];
$jsFiles = [
  'https://cdn.jsdelivr.net/npm/pdfmake@0.1.36/build/pdfmake.min.js',
  'https://cdn.jsdelivr.net/npm/pdfmake@0.1.36/build/vfs_fonts.js',
  'https://cdn.jsdelivr.net/npm/flatpickr',
  'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
  'https://cdnjs.cloudflare.com/ajax/libs/list.js/2.3.1/list.min.js',
  'https://cdn.jsdelivr.net/npm/tinymce/tinymce.min.js',
  'https://cdnjs.cloudflare.com/ajax/libs/darkreader/4.9.58/darkreader.js',
  'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js',
  'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js',
  'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js',
  'https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.5/af-2.6.0/b-2.4.1/b-colvis-2.4.1/b-html5-2.4.1/b-print-2.4.1/cr-1.7.0/date-1.5.1/fc-4.3.0/fh-3.4.0/kt-2.10.0/r-2.5.0/rg-1.4.0/rr-1.4.1/sc-2.2.0/sb-1.5.0/sp-2.2.0/sl-1.7.0/sr-1.3.0/datatables.min.js',
  'https://code.highcharts.com/highcharts.js',
  'https://code.highcharts.com/highcharts-3d.js',
  'https://code.highcharts.com/modules/exporting.js',
  'https://cdn.jsdelivr.net/npm/chart.js@2.9.4',
  'https://cdn.jsdelivr.net/npm/sweetalert2@11',
  'https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js',
  'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js',
  'plugins/dark-reader/darkreader.js',
  'https://unpkg.com/mobius1-selectr@latest/dist/selectr.min.js',
  'https://npmcdn.com/flatpickr/dist/l10n/sq.js',
  'https://cdn.jsdelivr.net/npm/@weavy/dropin-js/dist/weavy-dropin.js',
  'https://unpkg.com/xlsx/dist/xlsx.full.min.js',
  'https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js',
  'https://cdn.jsdelivr.net/npm/moment/moment.min.js',
  'https://cdn.datatables.net/datetime/1.5.1/js/dataTables.dateTime.min.js',
  'https://cdn.jsdelivr.net/npm/darkreader@4.9.87/darkreader.min.js',
  'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js',
  'https://cdn.jsdelivr.net/npm/sweetalert2@11'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="shortcut icon" href="images/favicon.png" />
  <?php
  foreach ($cssFiles as $css) echo "<link rel='stylesheet' href='$css'>";
  foreach ($jsFiles as $js) echo "<script src='$js'></script>";
  ?>
  <style>
    * {
      font-family: 'Inter', sans-serif;
    }
    body {
      transition: background-color 0.3s, color 0.3s;
    }
    .btn-icon {
      font-size: 1.5rem;
    }
    .nav-item {
      color: #fff;
    }
    @keyframes fadeUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    .fade-up {
      animation: fadeUp 0.5s ease-in-out;
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
      background-color: #dddddd;
    }
    .toggle-button .toggle-icon {
      font-size: 16px;
      color: #000;
    }
    .toggle-button.dark-mode .toggle-icon {
      color: #fff;
    }
    .toggle-switch {
      position: relative;
      width: 60px;
      height: 30px;
      background-color: #555;
      border-radius: 15px;
    }
    .toggle-switch input[type="checkbox"] {
      display: none;
    }
    .toggle-switch input[type="checkbox"]+label {
      position: absolute;
      top: 0;
      left: 0;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background-color: #fff;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
      cursor: pointer;
      transition: transform 0.3s ease-in-out;
    }
    .toggle-switch input[type="checkbox"]:checked+label {
      transform: translateX(30px);
    }
    .pagination {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-top: 20px;
    }
    .pagination a {
      margin: 0 5px;
      padding: 5px 10px;
      border: 1px solid #ccc;
      text-decoration: none;
      color: #333;
      border-radius: 5px;
    }
    .pagination a.active {
      background-color: #007bff;
      color: #fff;
    }
    .pagination a.disabled {
      color: #ccc;
      pointer-events: none;
    }
    [data-toggle="tooltip"] {
      position: relative;
      cursor: pointer;
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
      transition: opacity 0.5s ease-in-out;
      z-index: 5;
    }
    [data-toggle="tooltip"]:hover::after {
      opacity: 1;
    }
    @keyframes fadeIn {
      0% {
        opacity: 0;
        transform: translateY(20px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }
    @keyframes slideIn {
      0% {
        opacity: 0;
        transform: translateX(-20px);
      }
      100% {
        opacity: 1;
        transform: translateX(0);
      }
    }
    .dot {
      display: inline-block;
      width: 12px;
      height: 12px;
      border-radius: 75%;
      cursor: pointer;
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
      margin-top: -30px;
    }
    .input-custom-css {
      background-color: #fff;
      border: 1px solid #d5d9d9;
      border-radius: 8px;
      box-shadow: rgba(213, 217, 217, .5) 0 2px 5px 0;
      box-sizing: border-box;
      color: #0f1111;
      cursor: pointer;
      user-select: none;
      -webkit-user-select: none;
      touch-action: manipulation;
      vertical-align: middle;
    }
    .input-custom-css-disabled {
      background-color: #fff;
      opacity: 0.6;
      border: 1px solid #d5d9d9;
      border-radius: 8px;
      box-shadow: rgba(213, 217, 217, .5) 0 2px 5px 0;
      box-sizing: border-box;
      color: #0f1111;
      cursor: not-allowed;
      font-family: "Amazon Ember", sans-serif;
      user-select: none;
      -webkit-user-select: none;
      touch-action: manipulation;
      vertical-align: middle;
    }
    .input-custom-css:hover {
      background-color: #f7fafa;
    }
    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }
  </style>
</head>
<body>
  <?php include "partials/navbar.php" ?>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper">
      <?php include "partials/sidebar.php" ?>