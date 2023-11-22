<?php
session_start();

if (!isset($_SESSION['token'])) {
  header('Location: kycu_1.php');
  exit;
}

include('./config.php');
include('conn-d.php');

$client->setAccessToken($_SESSION['token']);

if ($client->isAccessTokenExpired()) {
  header('Location: logout.php');
  exit;
}

$google_oauth = new Google_Service_Oauth2($client);
try {
  $user_info = $google_oauth->userinfo->get();
  // Proceed with the rest of your code
} catch (Google_Service_Exception $e) {
  // Handle the authentication error, e.g., by redirecting to the login page
  header('Location: kycu_1.php');
  exit;
}

$allowedGmailEmails = array('afrimkolgeci@gmail.com', 'besmirakolgeci1@gmail.com','egjini17@gmail.com','bareshafinance@gmail.com');

if (empty($user_info['email'])) {
  // If the user doesn't have a valid email, deny access.
  header('Location: denied.php');
  exit;
}

$domain = substr(strrchr($user_info['email'], "@"), 1);

if ($domain === 'gmail.com' && !in_array($user_info['email'], $allowedGmailEmails)) {
  // If the email is a Gmail address but not in the allowed list, deny access.
  header('Location: denied.php');
  exit;
} elseif ($domain !== 'bareshamusic.com' && !in_array($user_info['email'], $allowedGmailEmails)) {
  // If the email domain is not 'bareshamusic.com' and not in the allowed Gmail list, deny access.
  header('Location: denied.php');
  exit;
}

$gender = $user_info['gender']; // Retrieve the user's gender

$email = $user_info['email'];

$sql = "SELECT * FROM googleauth WHERE email = '$email'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $_SESSION['id'] = $row['id'];
  $_SESSION['email'] = $row['email'];
  $_SESSION['oauth_uid'] = $row['oauth_uid'];
} else {
  // Handle the situation where the user data is not found in the database
  header('Location: denied.php');
  exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Meta Tags's -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="description" content="Panel administrativ">
  <meta name="keywords" content="Panel administrativ">
  <meta name="author" content="Enis Gjini">
  <meta name="google-site-verification" content="65Q9V_d_6p9mOYD05AFLNYLveEnM01AOs5cW2-qKrB0" />
  <!-- Title -->
  <title>BareshaNetwork -
    <?php echo date("Y"); ?>
  </title>

  <!-- UIcons -->
  <link rel="stylesheet" href="assets/uicons-regular-rounded/css/uicons-regular-rounded.css">

  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-brands/css/uicons-brands.css'>


  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/2.3.1/list.min.js" integrity="sha512-93wYgwrIFL+b+P3RvYxi/WUFRXXUDSLCT2JQk9zhVGXuS2mHl2axj6d+R6pP+gcU5isMHRj1u0oYE/mWyt/RjA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.6.0/tinymce.min.js" integrity="sha512-hMjDyb/4G3SapFEM71rK+Gea0+ZEr9vDlhBTyjSmRjuEgza0Ytsb67GE0aSpRMYW++z6kZPPcnddwlUG6VKm9w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/darkreader/4.9.58/darkreader.js" integrity="sha512-SVegqt9Q4E2cRDZ5alp9NLqLLJEAh6Ske9I/iU37Jiq0fHSFbkIsIbaIGYPcadf1JBLzdxPrkqfH1cpTuBQJvw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


  <!-- Material Design Icons -->
  <!-- <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css"> -->

  <!-- Font Awesome 6.0.0 | Local -->
  <link rel="stylesheet" href="assets/fontawesome-free-6.4.0-web/css/all.min.css">

  <!-- Datatables | Local files -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/autofill/2.3.7/css/autoFill.bootstrap5.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.0.0/css/buttons.bootstrap5.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/colreorder/1.5.4/css/colReorder.bootstrap5.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/datetime/1.1.1/css/dataTables.dateTime.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.2.0/css/fixedHeader.bootstrap5.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/keytable/2.6.4/css/keyTable.bootstrap5.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowreorder/1.2.8/css/rowReorder.bootstrap5.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/scroller/2.0.5/css/scroller.bootstrap5.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/searchbuilder/1.3.1/css/searchBuilder.bootstrap5.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.3.3/css/select.bootstrap5.min.css">



  <!-- Fav Icon ne formatin .png -->
  <link rel="shortcut icon" href="images/favicon.png" />

  <!-- Datatable Min JS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.5/af-2.6.0/b-2.4.1/b-colvis-2.4.1/b-html5-2.4.1/b-print-2.4.1/cr-1.7.0/date-1.5.1/fc-4.3.0/fh-3.4.0/kt-2.10.0/r-2.5.0/rg-1.4.0/rr-1.4.1/sc-2.2.0/sb-1.5.0/sp-2.2.0/sl-1.7.0/sr-1.3.0/datatables.min.css" rel="stylesheet">

  <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.5/af-2.6.0/b-2.4.1/b-colvis-2.4.1/b-html5-2.4.1/b-print-2.4.1/cr-1.7.0/date-1.5.1/fc-4.3.0/fh-3.4.0/kt-2.10.0/r-2.5.0/rg-1.4.0/rr-1.4.1/sc-2.2.0/sb-1.5.0/sp-2.2.0/sl-1.7.0/sr-1.3.0/datatables.min.js"></script>


  <!-- Material Design Bootstrap 5 | Local -->
  <link href="mdb5/css/mdb.min.css" rel="stylesheet" />

  <!-- Highcharts JS | CDN -->
  <!-- Include Highcharts library -->
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <script src="https://code.highcharts.com/highcharts-3d.js"></script>
  <script src="https://code.highcharts.com/modules/exporting.js"></script>

  <!-- Import Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4"></script>

  <!-- Import SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Import Signature Pad -->
  <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

  <!-- Import Material Design Icons CSS -->
  <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">


  <!-- Import vendor bundle CSS -->
  <link rel="stylesheet" href="vendors/base/vendor.bundle.base.css">

  <!-- External CSS -->
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="partials/style.css">

  <!-- Import CSS for the DataTables DateTime extension -->
  <link rel="stylesheet" href="https://cdn.datatables.net/datetime/1.4.0/css/dataTables.dateTime.min.css" />

  <!-- Import CSS for the jQuery UI library -->
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.0/themes/smoothness/jquery-ui.min.css">

  <!-- Import jQuery UI library -->
  <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>


  <script src="plugins/dark-reader/darkreader.js"></script>
  <link href="https://unpkg.com/mobius1-selectr@latest/dist/selectr.min.css" rel="stylesheet" type="text/css">
  <script src="https://unpkg.com/mobius1-selectr@latest/dist/selectr.min.js" type="text/javascript"></script>


  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://npmcdn.com/flatpickr/dist/l10n/sq.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/@weavy/dropin-js/dist/weavy-dropin.js" crossorigin="anonymous"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css">
  <script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />

  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">

  <!-- Moment.js for date formatting -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>

  <!-- DataTables DateTime Plugin -->
  <script src="https://cdn.datatables.net/datetime/1.5.1/js/dataTables.dateTime.min.js"></script>

  <style>
    * {
      font-family: 'Inter', sans-serif;
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

    /* Styling for the toggle button */
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
      /* Set default icon color to black */
      color: #000;
    }

    .toggle-button.dark-mode .toggle-icon {
      /* Icon color for dark mode (white) */
      color: #fff;
    }

    /* Styling for the toggle switch itself */
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

    /* Style for the export button container */
    .highcharts-contextbutton {
      background-color: #fff;
      border: 1px solid #ccc;
      border-radius: 4px;
      padding: 6px;
      position: absolute;
      top: 10px;
      right: 10px;
      z-index: 10;
    }

    /* Style for the export button symbol */
    .highcharts-button-symbol {
      font-size: 20px;
      color: #333;
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

    #faturat_Youtube>table {
      table-layout: fixed;
    }

    #faturat_Youtube>table>tbody>td {
      word-wrap: normal;
      max-width: 500px;
    }

    #faturat_Youtube td {
      white-space: inherit;
    }

    /* Define the fadeIn animation */
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

    /* Define the slideIn animation */
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

    /* Apply animations to elements */
    .fade-in {
      animation: fadeIn 0.5s ease-in-out;
    }

    .slide-in {
      animation: slideIn 0.5s ease-in-out;
    }

    /* CSS */
    .button-custom-light {
      background-color: #fff;
      position: relative;
      border: 1px solid #d5d9d9;
      border-radius: 8px;
      box-shadow: rgba(213, 217, 217, .5) 0 2px 5px 0;
      box-sizing: border-box;
      color: #0f1111;
      cursor: pointer;
      display: inline-block;
      font-size: 15px;
      line-height: 29px;
      padding: 2px 12px 0px 12px;
      text-align: center;
      text-decoration: none;
      user-select: none;
      -webkit-user-select: none;
      touch-action: manipulation;
      vertical-align: middle;
      text-transform: none;
      flex-grow: 1;
      margin-right: 5px;
    }

    .button-custom-light:hover {
      background-color: #f7fafa;
    }

    .button-custom-light:focus {
      border-color: #008296;
      box-shadow: rgba(213, 217, 217, .5) 0 2px 5px 0;
      outline: 0;
    }



    /* Add the tooltip text */
    .button-custom-light::before {
      content: attr(data-tooltip);
      /* Get the tooltip text from the 'data-tooltip' attribute */
      position: absolute;
      bottom: 100%;
      /* Position the tooltip above the button */
      left: 50%;
      margin-bottom: 5px;
      /* Position the tooltip centered horizontally */
      transform: translateX(-50%);
      background-color: #333;
      color: #fff;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 12px;
      opacity: 0;
      /* Start with zero opacity */
      visibility: hidden;
      /* Start hidden */
      transition: opacity 0.2s, visibility 0.2s;
    }

    /* Show the tooltip on hover */
    .button-custom-light:hover::before {
      opacity: 1;
      visibility: visible;
    }

    /* Add this style to your CSS */
    .dot {
      display: inline-block;
      width: 12px;
      height: 12px;
      border-radius: 75%;
      cursor: pointer;
      /* Add a pointer cursor to indicate interactivity */
    }

    /* Tooltip style */
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
      /* Ensure the tooltip is above other elements */
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




    /* CSS */
    .save-button-custom-css {
      background-color: #fff;
      border: 1px solid #d5d9d9;
      border-radius: 8px;
      box-shadow: rgba(213, 217, 217, .5) 0 2px 5px 0;
      box-sizing: border-box;
      color: #0f1111;
      cursor: pointer;
      display: inline-block;
      font-family: "Amazon Ember", sans-serif;
      font-size: 13px;
      line-height: 29px;
      padding: 0 10px 0 11px;
      position: relative;
      text-align: center;
      text-decoration: none;
      user-select: none;
      -webkit-user-select: none;
      touch-action: manipulation;
      vertical-align: middle;
      width: 100px;
    }

    .save-button-custom-css:hover {
      background-color: #f7fafa;
    }

    .save-button-custom-css:focus {
      border-color: #008296;
      box-shadow: rgba(213, 217, 217, .5) 0 2px 5px 0;
      outline: 0;
    }

    #time_of_token_expiry {
      display: none;
      /* Hide the second span initially */
    }

    #token-countdown:hover+#time_of_token_expiry {
      display: inline-block;
      /* Show the second span when hovering over the first span */
      animation: fadeIn 0.3s;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    .button-4 {
      appearance: none;
      background-color: #FAFBFC;
      border: 1px solid rgba(27, 31, 35, 0.15);
      border-radius: 6px;
      box-shadow: rgba(27, 31, 35, 0.04) 0 1px 0, rgba(255, 255, 255, 0.25) 0 1px 0 inset;
      box-sizing: border-box;
      color: #24292E;
      cursor: pointer;
      display: inline-block;

      font-size: 14px;
      line-height: 20px;
      list-style: none;
      padding: 6px 16px;
      position: relative;
      transition: background-color 0.2s cubic-bezier(0.3, 0, 0.5, 1);
      user-select: none;
      -webkit-user-select: none;
      touch-action: manipulation;
      vertical-align: middle;
      white-space: nowrap;
      word-wrap: break-word;
    }

    .button-4:hover {
      background-color: #F3F4F6;
      text-decoration: none;
      transition-duration: 0.1s;
    }

    .button-4:disabled {
      background-color: #FAFBFC;
      border-color: rgba(27, 31, 35, 0.15);
      color: #959DA5;
      cursor: default;
    }

    .button-4:active {
      background-color: #EDEFF2;
      box-shadow: rgba(225, 228, 232, 0.2) 0 1px 0 inset;
      transition: none 0s;
    }

    .button-4:focus {
      outline: 1px transparent;
    }

    .button-4:before {
      display: none;
    }

    .button-4:-webkit-details-marker {
      display: none;
    }
  </style>
</head>

<!-- K&euml;tu importohet navbari dhe sidebar-i n&euml; faqen kryesore -->

<body>
  <?php include "partials/navbar.php" ?>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper">
      <?php include "sidebar.php" ?>
      <!-- K&euml;tu vazhdon kodi i faqes kryesore -->