<?php
ob_start();
include 'partials/header.php';
include 'modalPayment.php';
include 'loan_modal.php';
include 'invoices_trash_modal.php';
require_once 'vendor/autoload.php';
$config = require_once 'second_config.php';
$client = initializeGoogleClient($config);
if (isset($_GET['code'])) {
  handleAuthentication($client);
}
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
    }
    $_SESSION['refresh_token'] = $refreshToken;
    // Redirect to a different page after authentication
    header('Location: invoices.php');
    exit;
  } catch (Google\Service\Exception $e) {
    echo '<pre>';
    print_r(json_decode($e->getMessage()));
    echo '</pre>';
  }
}
function getRefreshTokensFromDatabase()
{
  require_once 'conn-d.php';
  global $conn; // Use the global keyword to make $conn accessible
  $sql = "SELECT token, channel_id, channel_name, created_at FROM refresh_tokens";
  $result = $conn->query($sql);
  $refreshTokens = [];
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $refreshTokens[] = $row;
    }
  }
  return $refreshTokens;
}
// Create database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
// Check connection
if ($conn->connect_errno) {
  echo "Lidhja me MySQL d&euml;shtoi: " . $conn->connect_error;
  exit();
}
// Retrieve user-submitted date range or set a default
$selectedRange = isset($_POST['dateRange']) ? $_POST['dateRange'] : 'last7days';
// Define an array with predefined time periods
$timePeriods = array(
  "last7days" => "7 ditët e fundit",
  "last28days" => "28 ditët e fundit",
  "last90days" => "90 ditët e fundit",
  "last365days" => "365 ditët e fundit",
  "lifetime" => "Gjatë gjithë jetës"
);
// Check if the selected range is in the predefined time periods
if (array_key_exists($selectedRange, $timePeriods)) {
  // Handle predefined time periods here
  $rangeLabel = $timePeriods[$selectedRange];
  // Calculate start and end dates based on the selected range
  switch ($selectedRange) {
    case "last7days":
      $startDate = date('Y-m-d', strtotime('-6 days'));
      $endDate = date('Y-m-d');
      break;
    case "last28days":
      $startDate = date('Y-m-d', strtotime('-27 days'));
      $endDate = date('Y-m-d');
      break;
    case "last90days":
      $startDate = date('Y-m-d', strtotime('-89 days'));
      $endDate = date('Y-m-d');
      break;
    case "last365days":
      $startDate = date('Y-m-d', strtotime('-364 days'));
      $endDate = date('Y-m-d');
      break;
    case "lifetime":
      // Add a date of start date of Youtube Compnary
      $startDate = '2005-01-01';
      $endDate = date('Y-m-d');
      break;
  }
  // Set formatted date range for display
  $formattedDate = $rangeLabel;
} else {
  // Handle the case when a specific month/year is selected
  list($year, $month) = explode('-', $selectedRange);
  // Translate the month name to Albanian
  $albanianMonthNames = array(
    "Janar",
    "Shkurt",
    "Mars",
    "Prill",
    "Maj",
    "Qershor",
    "Korrik",
    "Gusht",
    "Shtator",
    "Tetor",
    "Nëntor",
    "Dhjetor"
  );
  $monthName = $albanianMonthNames[(int) $month - 1];
  // Combine the formatted date
  $formattedDate = "Muaji $monthName - $year";
  // Set start and end dates for the selected month
  $startDate = "$year-$month-01";
  $endDate = date("Y-m-t", strtotime($startDate)); // 't' gives the last day of the month
}
$_SESSION['selectedDate'] = $formattedDate;
$_SESSION['startDate'] = $startDate;
$_SESSION['endDate'] = $endDate;
$refreshTokens = getRefreshTokensFromDatabase();
?>
<?php
function getChannelDetails($channelId, $apiKey)
{
  $url = "https://www.googleapis.com/youtube/v3/channels?part=snippet&id=$channelId&key=$apiKey";
  $response = file_get_contents($url);
  $data = json_decode($response, true);
  if (isset($data['items'][0]['snippet']['thumbnails']['high']['url'])) {
    return $data['items'][0]['snippet']['thumbnails']['high']['url'];
  }
  return null;
}
?>
<?php if (!isset($_SESSION['oauth_uid'])) {
  echo "
  <script>
      document.addEventListener('DOMContentLoaded', function() {
          Swal.fire({
              icon: 'error',
              title: 'Qasja u refuzua',
              text: 'Ju nuk keni qasje në këtë faqe. Ju lutemi kontaktoni administratorin.',
              showConfirmButton: false,
              timer: 3000  // Auto close after 3 seconds
          }).then(() => {
              window.location.href = 'index.php'; // Redirect to index.php
          });
      });
  </script>
";
} else {
?>
  <style>
    .custom-tooltip {
      position: relative;
      display: inline-block;
      white-space: normal;
      cursor: pointer;
    }

    .custom-dot {
      width: 10px;
      height: 10px;
      background-color: red;
      /* Change the dot color as desired */
      border-radius: 50%;
      display: inline-block;
      white-space: normal;
      cursor: pointer;
    }

    .custom-tooltiptext {
      visibility: hidden;
      width: 80px;
      background-color: #333;
      color: #fff;
      text-align: center;
      border-radius: 6px;
      padding: 5px;
      position: absolute;
      z-index: 1;
      bottom: 100%;
      left: 50%;
      transform: translateX(-50%);
      opacity: 0.9;
      transition: opacity 0.3s;
      white-space: normal;
      cursor: pointer;
    }

    .custom-tooltip:hover .custom-tooltiptext {
      cursor: pointer;
      visibility: visible;
      white-space: normal;
      opacity: 0.9;
    }

    @media (max-width: 767px) {
      .breadcrumb-item a {
        font-size: 14px;
        /* Adjust the font size as needed */
      }

      .input-custom-css {
        font-size: 12px;
        /* Adjust the font size as needed */
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 5px;
        width: 100%;
        /* Ensure buttons take up full width */
        text-align: center;
        /* Center text within buttons */
      }

      .input-custom-css i {
        display: none;
        /* Hide icons on mobile */
      }

      .nav-pills {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
      }

      .nav-item {
        text-align: center;
        margin: 0 5px;
        /* Adjust margin as needed */
      }

      .table-sm th,
      .table-sm td {
        padding: 0.25rem;
      }

      .text-sm {
        font-size: 12px;
      }
    }
  </style>
  <div class="main-panel">
    <div class="content-wrapper">
      <div class="container-fluid">
        <nav class="bg-white px-2 rounded-5" style="width:fit-content;" aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a class="text-reset" style="text-decoration: none;">Financat</a></li>
            <li class="breadcrumb-item active" aria-current="page">
              <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">Pagesat Youtube</a>
            </li>
          </ol>
        </nav>
        <div class="row mb-2">
          <div>
            <button style="text-transform: none;" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#newInvoice">
              <i class="fi fi-rr-add-document fa-lg"></i>&nbsp; Fatur&euml; e re
            </button>
            <button style="text-transform: none;" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#listOfLoansModal">
              <i class="fi fi-rr-hand-holding-usd fa-lg"></i>&nbsp; Borgjet
            </button>
            <button style="text-transform: none;" class="input-custom-css px-3 py-2 " data-bs-toggle="modal" data-bs-target="#trashInvoices">
              <i class="fi fi-rr-delete-document fa-lg"></i>&nbsp; Faturat e fshira
            </button>
            <?php if (!($user_info['email'] == 'lirie@bareshamusic.com')) { ?>
              <a style="text-transform: none;text-decoration: none;" href="<?php echo $client->createAuthUrl(); ?>" class="input-custom-css px-3 py-2">
                <i class="fi fi-brands-youtube fa-lg"></i>&nbsp; Lidh kanal
              </a>
            <?php } ?>
            <ul class="nav nav-pills bg-white my-3 mx-0 rounded-5" style="width: fit-content; border: 1px solid lightgrey;" id="pills-tab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link rounded-5 active" style="text-transform: none" id="pills-lista_e_faturave-tab" data-bs-toggle="pill" data-bs-target="#pills-lista_e_faturave" type="button" role="tab" aria-controls="pills-lista_e_faturave" aria-selected="true">Lista e faturave ( Personale ) </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link rounded-5 active" style="text-transform: none" id="pills-lista_e_faturave_biznes-tab" data-bs-toggle="pill" data-bs-target="#pills-lista_e_faturave_biznes" type="button" role="tab" aria-controls="pills-lista_e_faturave_biznes" aria-selected="true">
                  Lista e faturave ( Biznes )
                </button>
              </li>
              <?php if (!($user_info['email'] == 'lirie@bareshamusic.com')) { ?>
                <li class="nav-item" role="presentation">
                  <button class="nav-link rounded-5" style="text-transform: none" id="pills-lista_e_kanaleve-tab" data-bs-toggle="pill" data-bs-target="#pills-lista_e_kanaleve" type="button" role="tab" aria-controls="pills-lista_e_kanaleve" aria-selected="false">Lista e kanaleve</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link rounded-5" style="text-transform: none" id="pills-lista_e_faturave_te_kryera-tab" data-bs-toggle="pill" data-bs-target="#pills-lista_e_faturave_te_kryera" type="button" role="tab" aria-controls="pills-lista_e_faturave_te_kryera" aria-selected="false">Pagesat e kryera ( Personal )</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link rounded-5" style="text-transform: none" id="pills-lista_e_faturave_te_kryera_biznes-tab" data-bs-toggle="pill" data-bs-target="#pills-lista_e_faturave_te_kryera_biznes" type="button" role="tab" aria-controls="pills-lista_e_faturave_te_kryera_biznes" aria-selected="false">Pagesa e kryera (Biznese)</button>
                </li>
              <?php } ?>
            </ul>
          </div>
        </div>
        <div class="p-3 shadow-sm rounded-5 mb-4 card">
          <div class="row">
            <div class="modal fade" id="newInvoice" tabindex="-1" aria-labelledby="newInvoiceLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="newInvoiceLabel">Krijoni një faturë të re</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <!-- Your form goes here -->
                    <form action="create_invoice.php" method="POST">
                      <div class="mb-3">
                        <label for="invoice_number" class="form-label">Numri i faturës:</label>
                        <?php
                        // Call the generateInvoiceNumber function to get the invoice number
                        $invoiceNumber = generateInvoiceNumber();
                        ?>
                        <input type="text" class="form-control rounded-5 shadow-sm py-3" id="invoice_number" name="invoice_number" value="<?php echo $invoiceNumber; ?>" required readonly>
                      </div>
                      <div class="mb-3">
                        <label for="customer_id" class="form-label">Emri i klientit:</label>
                        <select class="form-control rounded-5 shadow-sm py-3" id="customer_id" name="customer_id" required>
                          <?php
                          require_once "conn-d.php";
                          $sql = "SELECT id,emri, perqindja FROM klientet ORDER BY id DESC";
                          $result = mysqli_query($conn, $sql);
                          if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                              echo "<option value='" . $row["id"] . "' data-percentage='" . $row["perqindja"] . "'>" . $row["emri"] . "</option>";
                            }
                          }
                          ?>
                        </select>
                      </div>
                      <script>
                        new Selectr('#customer_id', {
                          searchable: true,
                          width: 300
                        });
                      </script>
                      <div class="mb-3">
                        <label for="item" class="form-label">Përshkrimi:</label>
                        <textarea type="text" class="form-control rounded-5 shadow-sm py-3" id="item" name="item" required> </textarea>
                      </div>
                      <div class="mb-3">
                        <label for="percentage" class="form-label">Përqindja:</label>
                        <input type="text" class="form-control rounded-5 shadow-sm py-3" id="percentage" name="percentage" value="" required>
                      </div>
                      <div class="mb-3 row">
                        <div class="col">
                          <label for="total_amount" class="form-label">Shuma e përgjithshme:</label>
                          <input type="text" class="form-control rounded-5 shadow-sm py-3" id="total_amount" name="total_amount" required>
                        </div>
                        <div class="col">
                          <label for="total_amount_after_percentage" class="form-label">Shuma e përgjithshme pas
                            përqindjes:</label>
                          <input type="text" class="form-control rounded-5 shadow-sm py-3" id="total_amount_after_percentage" name="total_amount_after_percentage" required>
                        </div>
                      </div>
                      <div class="mb-3">
                        <label for="created_date" class="form-label">Data e krijimit të faturës:</label>
                        <input type="date" class="form-control rounded-5 shadow-sm py-3" id="created_date" name="created_date" value="<?php echo date('Y-m-d'); ?>" required>
                      </div>
                      <div class="mb-3">
                        <label for="invoice_status" class="form-label">Gjendja e fatures:</label>
                        <select class="form-control rounded-5 shadow-sm py-3" id="invoice_status" name="invoice_status" required>
                          <option value="Rregullt" selected>Rregullt</option>
                          <option value="Parregullt">Parregullt</option>
                        </select>
                      </div>
                      <button type="submit" class="btn btn-primary btn-sm text-white rounded-5 shadow">Krijo
                        faturë</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-content" id="pills-tabContent">
              <div class="tab-pane fade show active" id="pills-lista_e_faturave" role="tabpanel" aria-labelledby="pills-lista_e_faturave-tab">
                <div class="table-responsive">
                  <table id="invoiceList" class="table table-bordered table-sm" data-source="get_invoices.php">
                    <thead class="table-light">
                      <tr>
                        <th></th>
                        <th class="text-sm">ID</th>
                        <th class="text-sm">Emri i Klientit</th>
                        <th class="text-sm">Pershkrimi</th>
                        <th class="text-sm">Detajet</th>
                        <th class="text-sm">Shuma e paguar</th>
                        <th class="text-sm">Obligim</th>
                        <th class="text-sm">Veprimi</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>
              <div class="tab-pane fade" id="pills-lista_e_faturave_biznes" role="tabpanel" aria-labelledby="pills-lista_e_faturave_biznes-tab">
                <div class="table-responsive">
                  <table id="invoiceListBiznes" class="table table-bordered w-100 " data-source="get_invoices_biznes.php">
                    <thead class="table-light">
                      <tr>
                        <th></th>
                        <th style="font-size: 12px">ID</th>
                        <!-- <th style="font-size: 12px">Numri i faturës</th> -->
                        <th style="font-size: 12px">Emri i Klientit</th>
                        <th style="font-size: 12px">Pershkrimi</th>
                        <th style="font-size: 12px">Detajet</th>
                        <th style="font-size: 12px">Shuma e paguar</th>
                        <th style="font-size: 12px">Obligim</th>
                        <th style="font-size: 12px">Veprimi</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>
              <div class="tab-pane fade" id="pills-lista_e_kanaleve" role="tabpanel" aria-labelledby="pills-lista_e_kanaleve-tab">
                <?php if (!empty($refreshTokens)) { ?>
                  <div class="row">
                    <?php
                    $albanianMonthNames = array(
                      "Janar",
                      "Shkurt",
                      "Mars",
                      "Prill",
                      "Maj",
                      "Qershor",
                      "Korrik",
                      "Gusht",
                      "Shtator",
                      "Tetor",
                      "Nëntor",
                      "Dhjetor"
                    );
                    // Predefined time periods
                    $timePeriods = array(
                      "7 ditët e fundit" => "last7days",
                      "28 ditët e fundit" => "last28days",
                      "90 ditët e fundit" => "last90days",
                      "365 ditët e fundit" => "last365days",
                      "Gjatë gjithë jetës" => "lifetime"
                    );
                    // Generate year and month options
                    $options = "";
                    $currentYear = date('Y');
                    $currentMonth = date('m');
                    // Add predefined time periods to options within an optgroup
                    $options .= "<optgroup label='Periudhat kohore'>";
                    foreach ($timePeriods as $periodName => $periodValue) {
                      $options .= "<option value='{$periodValue}'>$periodName</option>";
                    }
                    $options .= "</optgroup>";
                    // Add years and months to options within an optgroup
                    $options .= "<optgroup label='Muajt'>";
                    for ($year = 2023; $year <= $currentYear; $year++) {
                      for ($month = 1; $month <= 12; $month++) {
                        if ($year == $currentYear && $month > $currentMonth) {
                          break; // Do not list future months of the current year
                        }
                        $monthPadded = str_pad($month, 2, '0', STR_PAD_LEFT);
                        $monthName = $albanianMonthNames[$month - 1]; // Get Albanian month name
                        $options .= "<option value='{$year}-{$monthPadded}'>$monthName $year</option>";
                      }
                    }
                    $options .= "</optgroup>";
                    ?>
                    <form method="post" class="mb-2">
                      <div class="row">
                        <div class="col">
                          <label for="dateRange" class="form-label">Zgjidh Muajin:</label>
                          <select class="form-control rounded-5 shadow-sm" id="dateRange" name="dateRange" required>
                            <?php echo $options; ?>
                          </select>
                        </div>
                      </div>
                      <br>
                      <button type="submit" class="input-custom-css px-3 py-2" style="text-decoration: none;"><i class="fi fi-rr-filter"></i> Filtro</button>
                    </form>
                    <script>
                      new Selectr('#dateRange', {
                        searchable: true
                      });
                    </script>
                    <!-- SQL Commands Display Section -->
                    <div class="sql-commands-container" style="display: none;">
                      <p>Komandat SQL për shtimin e të dhënave në tabelën e faturave. Këto përfshijnë "INSERT INTO" për
                        shtimin e rreshtave të reja në një tabelë.</p>
                      <code id="sqlCommands" class="sql-commands"></code>
                    </div>
                    <br>
                    <?php
                    // Check if the user has submitted the filter form
                    if (isset($_POST['dateRange'])) {
                      // User has applied the filter, proceed with displaying the table and making the API request
                    ?>
                      <div>
                        <button id="submitSql" type="button" class="input-custom-css px-3 py-2 my-2">Dorëzoje në bazën e të
                          dhënave</button>
                      </div>
                      <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable">
                          <thead class="bg-light">
                            <tr>
                              <th style="font-size: 12px">#</th>
                              <th style="font-size: 12px">Numri i fatures</th>
                              <th style="font-size: 12px">ID e klientit</th>
                              <th style="font-size: 12px">Data</th>
                              <th style="font-size: 12px">Fitimi</th>
                              <th style="font-size: 12px">Fitimi pas perqindjes</th>
                              <th style="font-size: 12px">Data e krijimit</th>
                              <th style="font-size: 12px">Të dhenat e kanalit</th>
                              <th style="font-size: 12px">Statusi i faturës</th>
                              <th style="font-size: 12px">Veprim</th>
                              <th style="font-size: 12px">Input Check</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            // Counter variable
                            $counter = 1;
                            // Prepare a query to fetch data
                            $query = "SELECT id, emri, perqindja FROM klientet WHERE youtube = ?";
                            $stmt = mysqli_prepare($conn, $query);
                            foreach ($refreshTokens as $tokenInfo) {
                              // Bind the channel_id parameter
                              mysqli_stmt_bind_param($stmt, "s", $tokenInfo['channel_id']);
                              // Execute the query
                              if (mysqli_stmt_execute($stmt)) {
                                $result = mysqli_stmt_get_result($stmt);
                                while ($row = mysqli_fetch_assoc($result)) {
                                  $id = $row['id'];
                                  $emri = $row['emri'];
                                  $perqindja = $row['perqindja'];
                                }
                              }
                            ?>
                              <tr>
                                <td>
                                  <?php echo $counter++; ?>
                                </td>
                                <td>
                                  <?php echo $invoiceNumber . $id ?>
                                </td>
                                <td>
                                  <?php
                                  // Put in the session the  id
                                  $_SESSION['id'] = $id;
                                  echo $id ?>
                                </td>
                                <td>
                                  <?php echo $_SESSION['selectedDate'] ?>
                                </td>
                                <?php
                                $client = new Google_Client();
                                $client->setClientId('84339742200-g674o1df674m94a09tppcufciavp0bo1.apps.googleusercontent.com');
                                $client->setClientSecret('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');
                                $client->refreshToken($tokenInfo['token']);
                                $client->addScope([
                                  'https://www.googleapis.com/auth/youtube',
                                  'https://www.googleapis.com/auth/youtube.readonly',
                                  'https://www.googleapis.com/auth/youtubepartner',
                                  'https://www.googleapis.com/auth/yt-analytics-monetary.readonly',
                                  'https://www.googleapis.com/auth/yt-analytics.readonly'
                                ]);
                                $youtubeAnalytics = new Google\Service\YoutubeAnalytics($client);
                                // Get the created date for that channel in YouTube using tokenInfo channel id
                                $params = [
                                  'ids' => 'channel==' . $tokenInfo['channel_id'],
                                  'currency' => 'EUR',
                                  'startDate' => $startDate,
                                  'endDate' => $endDate,
                                  'metrics' => 'estimatedRevenue'
                                ];
                                $response = $youtubeAnalytics->reports->query($params);
                                $row = $response->getRows()[0];
                                // Initialize a variable to store the value
                                $storedValue = '';
                                // Display only the numeric values (without column headers)
                                foreach ($row as $index => $value) {
                                  // Append the value to the storedValue variable
                                  $storedValue .= $value . '<br>';
                                }
                                // Echo the storedValue outside of the loop
                                echo '<td>' . $storedValue . '</td>';
                                ?>
                                <td>
                                  <?php
                                  $difference = $value - ($value * ($perqindja / 100));
                                  echo number_format($difference, 2);
                                  ?>
                                </td>
                                <td>
                                  <?php
                                  // Get the actual date
                                  echo date('Y-m-d'); ?>
                                </td>
                                <td>
                                  <?php // Get the cover art URL
                                  $coverArtUrl = getChannelDetails($tokenInfo['channel_id'], 'AIzaSyD56A1QU67vIkP1CYSDX2sYona2nxOJ9R0');
                                  // Display cover art image
                                  if ($coverArtUrl) {
                                    echo '<img src="' . $coverArtUrl . '" class="figure-img img-fluid rounded" alt="Channel Cover">';
                                    echo '<br>';
                                    echo $tokenInfo['channel_name'];
                                  }
                                  ?>
                                </td>
                                <td>
                                  <?php
                                  $selectedDate = $_SESSION['selectedDate']; // Store the session variable in a separate variable
                                  $difference = $value - ($value * ($perqindja / 100));
                                  $sql = "SELECT * FROM invoices WHERE customer_id = '$_SESSION[id]' AND item = '$selectedDate'";
                                  $result = mysqli_query($conn, $sql);
                                  if ($row = mysqli_fetch_assoc($result)) {
                                    $item = $row['item'];
                                    // echo $item . '<br>';
                                    // Display an icon about like check
                                    echo '<p> Kjo faturë ekziston</p><br>';
                                    echo '<i class="fi fi-rr-check text-success"></i>';
                                  } else {
                                    // Display an icon about like x
                                    echo '<p> Kjo faturë nuk ekziston</p><br>';
                                    echo '<i class="fa-solid fa-x"></i>';
                                  }
                                  ?>
                                </td>
                                <td>
                                  <a class="btn btn-danger text-white btn-sm rounded-5 px-2 py-1 delete-button" data-channelid="<?php echo $tokenInfo['channel_id'] ?>">
                                    <i class="fi fi-rr-trash"></i>
                                  </a>
                                </td>
                                <td>
                                  <input type="checkbox" name="selected_channels[]" value="<?php echo $tokenInfo['channel_id'] ?>">
                                </td>
                              </tr>
                            <?php } ?>
                          </tbody>
                        </table>
                      </div>
                    <?php } ?>
                  </div>
                <?php } else { ?>
                  <p>Nuk u gjetën argumente rifreskimi në bazën e të dhënave.</p>
                <?php } ?>
                <script>
                  document.addEventListener("DOMContentLoaded", function() {
                    // Get all values from the table and store them in a JavaScript array
                    const tableRows = document.querySelectorAll("tbody tr");
                    const allValues = [];
                    tableRows.forEach((row) => {
                      const rowData = [];
                      row.querySelectorAll("td").forEach((cell) => {
                        rowData.push(cell.textContent.trim());
                      });
                      allValues.push(rowData.join(", "));
                    });
                    // Attach a click event listener to the "Insert Values" button
                    const insertButton = document.querySelector(".insert-values-btn");
                    insertButton.addEventListener("click", function() {
                      // Set the hidden field's value with all the values from the table
                      document.getElementById("allValues").value = allValues.join(";");
                      // Submit the form to insert values into the database
                      document.getElementById("insertForm").submit();
                    });
                  });
                </script>
              </div>
              <div class="tab-pane fade" id="pills-lista_e_faturave_te_kryera" role="tabpanel" aria-labelledby="pills-lista_e_faturave_te_kryera-tab">
                <div class="row">
                  <div class="col">
                    <label for="max" class="form-label" style="font-size: 14px;">Prej:</label>
                    <p class="text-muted" style="font-size: 10px;">Zgjidhni një diapazon fillues të
                      dates për të filtruar rezultatet</p>
                    <div class="input-group rounded-5">
                      <span class="input-group-text border-0" style="background-color: white;cursor: pointer;"><i class="fi fi-rr-calendar"></i></span><input type="date" id="startDate" name="startDate" class="form-control rounded-5" placeholder="Zgjidhni datën e fillimit" style="cursor: pointer;" readonly>
                    </div>
                  </div>
                  <div class="col">
                    <label for="max" class="form-label" style="font-size: 14px;">Deri:</label>
                    <p class="text-muted" style="font-size: 10px;">Zgjidhni një diapazon mbarues të
                      dates për të filtruar rezultatet.</p>
                    <div class="input-group rounded-5">
                      <span class="input-group-text border-0" style="background-color: white;cursor: pointer;"><i class="fi fi-rr-calendar"></i></span><input type="text" id="endDate" name="endDate" class="form-control rounded-5" placeholder="Zgjidhni datën e mbarimit" style="cursor: pointer;" readonly>
                    </div>
                  </div>
                </div>
                <div class="col-2 my-4">
                  <button id="clearFiltersBtn" class="input-custom-css px-3 py-2">
                    <i class="fi fi-rr-clear-alt"></i>
                    Pastro filtrat
                  </button>
                </div>
                <hr>
                <div class="table-responsive">
                  <table id="paymentsTable" class="table table-bordered table-hover w-100">
                    <thead class="table-light">
                      <tr>
                        <th style="white-space: normal;font-size: 12px;">Emri i klientit</th>
                        <th style="white-space: normal;font-size: 12px;">ID e faturës</th>
                        <th style="white-space: normal;font-size: 12px;">Vlera</th>
                        <th style="white-space: normal;font-size: 12px;">Data</th>
                        <th style="white-space: normal;font-size: 12px;">Banka</th>
                        <th style="white-space: normal;font-size: 12px;">Lloji</th>
                        <th style="white-space: normal;font-size: 12px;">Përshkrimi</th>
                        <th style="white-space: normal;font-size: 12px;">Shuma e përgjithshme pas %</th>
                        <th style="white-space: normal;font-size: 12px;">Veprim</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>
              <div class="tab-pane fade" id="pills-lista_e_faturave_te_kryera_biznes" role="tabpanel" aria-labelledby="pills-lista_e_faturave_te_kryera_biznes-tab">
                <div class="row">
                  <div class="col">
                    <label for="max" class="form-label" style="font-size: 14px;">Prej:</label>
                    <p class="text-muted" style="font-size: 10px;">Zgjidhni një diapazon fillues të
                      dates për të filtruar rezultatet</p>
                    <div class="input-group rounded-5">
                      <span class="input-group-text border-0" style="background-color: white;cursor: pointer;"><i class="fi fi-rr-calendar"></i></span><input type="date" id="startDateBiznes" name="startDateBiznes" class="form-control rounded-5" placeholder="Zgjidhni datën e fillimit" style="cursor: pointer;" readonly>
                    </div>
                  </div>
                  <div class="col">
                    <label for="max" class="form-label" style="font-size: 14px;">Deri:</label>
                    <p class="text-muted" style="font-size: 10px;">Zgjidhni një diapazon mbarues të
                      dates për të filtruar rezultatet.</p>
                    <div class="input-group rounded-5">
                      <span class="input-group-text border-0" style="background-color: white;cursor: pointer;"><i class="fi fi-rr-calendar"></i></span><input type="text" id="endDateBiznes" name="endDateBiznes" class="form-control rounded-5" placeholder="Zgjidhni datën e mbarimit" style="cursor: pointer;" readonly>
                    </div>
                  </div>
                </div>
                <div class="col-2 my-4">
                  <button id="clearFiltersBtnBiznes" class="input-custom-css px-3 py-2">
                    <i class="fi fi-rr-clear-alt"></i>
                    Pastro filtrat
                  </button>
                </div>
                <hr>
                <div class="table-responsive">
                  <table id="paymentsTableBiznes" class="table table-bordered table-hover w-100">
                    <thead class="table-light">
                      <tr>
                        <th style="white-space: normal;font-size: 12px;">Emri i klientit</th>
                        <th style="white-space: normal;font-size: 12px;">ID e faturës</th>
                        <th style="white-space: normal;font-size: 12px;">Vlera</th>
                        <th style="white-space: normal;font-size: 12px;">Data</th>
                        <th style="white-space: normal;font-size: 12px;">Banka</th>
                        <th style="white-space: normal;font-size: 12px;">Lloji</th>
                        <th style="white-space: normal;font-size: 12px;">Përshkrimi</th>
                        <th style="white-space: normal;font-size: 12px;">Shuma e përgjithshme pas %</th>
                        <th style="white-space: normal;font-size: 12px;">Veprim</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php } ?>
<?php function generateInvoiceNumber()
{
  $currentDateTime = date("dmYHis");
  $invoiceNumber = $currentDateTime;
  return $invoiceNumber;
}
?>
</div>
<script>
  document.getElementById('customer_id').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    var percentage = selectedOption.getAttribute('data-percentage');
    document.getElementById('percentage').value = percentage;
    var totalAmount = parseFloat(document.getElementById('total_amount').value);
    var totalAmountAfterPercentage = totalAmount - (totalAmount * (percentage / 100));
    document.getElementById('total_amount_after_percentage').value = totalAmountAfterPercentage.toFixed(2);
  });
  document.getElementById('total_amount').addEventListener('input', function() {
    var totalAmount = parseFloat(this.value);
    var percentage = parseFloat(document.getElementById('percentage').value);
    var totalAmountAfterPercentage = totalAmount - (totalAmount * (percentage / 100));
    document.getElementById('total_amount_after_percentage').value = totalAmountAfterPercentage.toFixed(2);
  });

  function getCustomerName(customerId) {
    var customerName = '';
    $.ajax({
      url: 'get_customer_name.php',
      type: 'POST',
      data: {
        'customer_id': customerId
      },
      async: false,
      success: function(response) {
        customerName = response;
      }
    });
    return customerName;
  }
  $(document).ready(function() {
    var table = $('#invoiceList').DataTable({
      processing: true,
      responsive: {
        details: {
          display: $.fn.dataTable.Responsive.display.childRowImmediate,
          type: ''
        }
      },
      serverSide: true,
      "searching": {
        "regex": true
      },
      "paging": true,
      "pageLength": 10,
      dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
        "<'row'<'col-md-12'tr>>" +
        "<'row'<'col-md-6'><'col-md-6'p>>",
      ajax: {
        url: 'get_invoices.php',
        type: 'POST',
        "dataFilter": function(data) {
          console.log('DataTables Data:', data);
          return data;
        }
      },
      initComplete: function() {
        var btns = $(".dt-buttons");
        btns.addClass("").removeClass("dt-buttons btn-group");
        var lengthSelect = $("div.dataTables_length select");
        lengthSelect.addClass("form-select");
        lengthSelect.css({
          width: "auto",
          margin: "0 8px",
          padding: "0.375rem 1.75rem 0.375rem 0.75rem",
          lineHeight: "1.5",
          border: "1px solid #ced4da",
          borderRadius: "0.25rem",
        });
      },
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
      },
      buttons: [{
          extend: "pdf",
          text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
          titleAttr: "Eksporto tabelen ne formatin PDF",
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
        },
        {
          extend: "excelHtml5",
          text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
          titleAttr: "Eksporto tabelen ne formatin Excel",
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
          exportOptions: {
            modifier: {
              search: "applied",
              order: "applied",
              page: "all",
            },
          },
        },
        {
          extend: "print",
          text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
          titleAttr: "Printo tabel&euml;n",
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
        }, {
          text: '<i class="fi fi-rr-trash fa-lg"></i>&nbsp;&nbsp; Fshij',
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
          action: function() {
            const selectedIds = [];
            $('.row-checkbox:checked').each(function() {
              selectedIds.push($(this).data('id'));
            });
            if (selectedIds.length > 0) {
              Swal.fire({
                icon: 'warning',
                title: 'Konfirmo Fshirjen',
                text: 'A jeni i sigurt që dëshironi të fshini elementet e zgjedhura?',
                showCancelButton: true,
                confirmButtonText: 'Po, Fshij',
                cancelButtonText: 'Anulo',
              }).then((result) => {
                if (result.isConfirmed) {
                  $.ajax({
                    url: 'delete_invoice.php',
                    type: 'POST',
                    data: {
                      ids: selectedIds
                    },
                    success: function(response) {
                      Swal.fire({
                        icon: 'success',
                        title: 'Fshirja u krye me sukses!',
                        text: response,
                      });
                      const currentPage = table.page.info().page;
                      // Reload table data
                      table.ajax.reload(function() {
                        // After reload, set the table to the saved current page
                        table.page(currentPage).draw('page');
                      });
                    },
                    error: function(error) {
                      console.error('Error deleting items:', error);
                      Swal.fire({
                        icon: 'error',
                        title: 'Gabim gjatë fshirjes',
                        text: 'Dicka shkoi keq gjatë fshirjes. Ju lutem provoni përsëri.',
                      });
                    }
                  });
                }
              });
            } else {
              Swal.fire({
                icon: 'info',
                title: 'Nuk ke zgjedhur elemente',
                text: 'Ju lutem zgjedhni elemente për t\'i fshirë.',
              });
            }
          },
        },
      ],
      stripeClasses: ["stripe-color"],
      columnDefs: [{
        "targets": [0, 1, 2, 3, 4, 5, 6, 7],
        "render": function(data, type, row) {
          return type === 'display' && data !== null ? '<div style="white-space: normal;">' + data + '</div>' : data;
        }
      }],
      columns: [{
          data: 'id',
          render: function(data, type, row) {
            return '<input type="checkbox" class="row-checkbox" data-id="' + data + '">';
          }
        },
        {
          data: 'id',
        },
        {
          data: 'customer_name',
          render: function(data, type, row) {
            const loanAmount = row.customer_loan_amount;
            const loanPaid = row.customer_loan_paid;
            const difference = loanAmount - loanPaid;
            if (difference > 0) {
              const dotHTML = '<div class="custom-tooltip" >' +
                '<div class="custom-dot"></div>' +
                '<span class="custom-tooltiptext">' + difference + ' €</span>' +
                '</div>';
              return '<p style="white-space: normal;">' + data + '</p>' + dotHTML;
            } else {
              return '<p style="white-space: normal;">' + data + '</p>';
            }
          },
        },
        {
          data: 'item',
          render: function(data, type, row, meta) {
            var stateOfInvoice = row.state_of_invoice;
            var badgeClass = '';
            if (stateOfInvoice === 'Parregullt') {
              badgeClass = 'bg-danger';
            } else if (stateOfInvoice === 'Rregullt') {
              badgeClass = 'bg-success';
            }
            var combinedData = '<div class="item-column">';
            combinedData += data;
            combinedData += '</div><br>';
            combinedData += '<div class="badge-column">';
            combinedData += '<span class="badge ' + badgeClass + ' mx-1 rounded-5">' + stateOfInvoice + '</span>';
            combinedData += '</div>';
            return combinedData;
          }
        },
        {
          "data": null,
          "render": function(data, type, row) {
            return '<table style="width:100%; font-size:12px;">' +
              '<tr>' +
              '<td style="text-align:left;">Shuma e përgjitshme:</td>' +
              '<td style="text-align:right;">' + row.total_amount + '</td>' +
              '</tr>' +
              '<tr>' +
              '<td style="text-align:left;">Shuma e për. % :</td>' +
              '<td style="text-align:right;">' + row.total_amount_after_percentage + '</td>' +
              '</tr>' +
              '</table>';
          }
        },
        {
          data: 'paid_amount'
        },
        {
          data: 'remaining_amount',
          render: function(data, type, row) {
            const remainingAmount = row.total_amount_after_percentage - row.paid_amount;
            return remainingAmount.toFixed(2);
          }
        },
        {
          data: 'actions',
          render: function(data, type, row) {
            return '<div>' +
              '<a href="#" style="text-decoration:none;" class="bg-white border border-1 px-3 py-2 rounded-5 mx-1 text-dark open-payment-modal" ' +
              'data-id="' + row.id + '" ' +
              'data-invoice-number="' + row.invoice_number + '" ' +
              'data-customer-id="' + row.customer_id + '" ' +
              'data-item="' + row.item + '" ' +
              'data-total-amount="' + row.total_amount_after_percentage + '" ' +
              'data-paid-amount="' + row.paid_amount + '" ' +
              'data-remaining-amount="' + (row.total_amount_after_percentage - row.paid_amount) + '">' +
              '<i class="fi fi-rr-euro"></i> Paguaj</a>  ' +
              '<a target="_blank" style="text-decoration:none;" href="complete_invoice.php?id=' + row.id + '" class="bg-white border border-1 px-3 py-2 rounded-5 mx-1 text-dark">' +
              '<i class="fi fi-rr-edit"></i> Edito</a>' +
              '<a target="_blank" style="text-decoration:none;" href="print_invoice.php?id=' + row.invoice_number + '" class="bg-white border border-1 px-3 py-2 rounded-5 mx-1 text-dark">' +
              '<i class="fi fi-rr-print"></i> Printo v1.0</a></div>';
          }
        }
      ],
    });
    var tableSecond = $('#invoiceListBiznes').DataTable({
      processing: true,
      serverSide: true,
      "searching": {
        "regex": true
      },
      "paging": true,
      "pageLength": 10,
      dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
        "<'row'<'col-md-12'tr>>" +
        "<'row'<'col-md-6'><'col-md-6'p>>",
      ajax: {
        url: 'get_invoices_biznes.php',
        type: 'POST',
        "dataFilter": function(data) {
          return data;
        }
      },
      initComplete: function() {
        var btns = $(".dt-buttons");
        btns.addClass("").removeClass("dt-buttons btn-group");
        var lengthSelect = $("div.dataTables_length select");
        lengthSelect.addClass("form-select");
        lengthSelect.css({
          width: "auto",
          margin: "0 8px",
          padding: "0.375rem 1.75rem 0.375rem 0.75rem",
          lineHeight: "1.5",
          border: "1px solid #ced4da",
          borderRadius: "0.25rem",
        });
      },
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
      },
      buttons: [{
          extend: "pdf",
          text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
          titleAttr: "Eksporto tabelen ne formatin PDF",
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
        },
        {
          extend: "excelHtml5",
          text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
          titleAttr: "Eksporto tabelen ne formatin Excel",
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
          exportOptions: {
            modifier: {
              search: "applied",
              order: "applied",
              page: "all",
            },
          },
        },
        {
          extend: "print",
          text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
          titleAttr: "Printo tabel&euml;n",
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
        }, {
          text: '<i class="fi fi-rr-trash fa-lg"></i>&nbsp;&nbsp; Fshij',
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
          action: function() {
            const selectedIds = [];
            $('.row-checkbox:checked').each(function() {
              selectedIds.push($(this).data('id'));
            });
            if (selectedIds.length > 0) {
              Swal.fire({
                icon: 'warning',
                title: 'Konfirmo Fshirjen',
                text: 'A jeni i sigurt që dëshironi të fshini elementet e zgjedhura?',
                showCancelButton: true,
                confirmButtonText: 'Po, Fshij',
                cancelButtonText: 'Anulo',
              }).then((result) => {
                if (result.isConfirmed) {
                  $.ajax({
                    url: 'delete_invoice.php',
                    type: 'POST',
                    data: {
                      ids: selectedIds
                    },
                    success: function(response) {
                      Swal.fire({
                        icon: 'success',
                        title: 'Fshirja u krye me sukses!',
                        text: response,
                      });
                      const currentPage = tableSecond.page.info().page;
                      // Reload table data
                      tableSecond.ajax.reload(function() {
                        // After reload, set the table to the saved current page
                        tableSecond.page(currentPage).draw('page');
                      });
                    },
                    error: function(error) {
                      console.error('Error deleting items:', error);
                      Swal.fire({
                        icon: 'error',
                        title: 'Gabim gjatë fshirjes',
                        text: 'Dicka shkoi keq gjatë fshirjes. Ju lutem provoni përsëri.',
                      });
                    }
                  });
                }
              });
            } else {
              Swal.fire({
                icon: 'info',
                title: 'Nuk ke zgjedhur elemente',
                text: 'Ju lutem zgjedhni elemente për t\'i fshirë.',
              });
            }
          },
        },
      ],
      stripeClasses: ["stripe-color"],
      columnDefs: [{
        "targets": [0, 1, 2, 3, 4, 5, 6, 7],
        "render": function(data, type, row) {
          return type === 'display' && data !== null ? '<div style="white-space: normal;">' + data + '</div>' : data;
        }
      }],
      columns: [{
          data: 'id',
          render: function(data, type, row) {
            return '<input type="checkbox" class="row-checkbox" data-id="' + data + '">';
          }
        },
        {
          data: 'id',
        },
        {
          data: 'customer_name',
          render: function(data, type, row) {
            const loanAmount = row.customer_loan_amount;
            const loanPaid = row.customer_loan_paid;
            const difference = loanAmount - loanPaid;
            if (difference > 0) {
              const dotHTML = '<div class="custom-tooltip" >' +
                '<div class="custom-dot"></div>' +
                '<span class="custom-tooltiptext">' + difference + ' €</span>' +
                '</div>';
              return '<p style="white-space: normal;">' + data + '</p>' + dotHTML;
            } else {
              return '<p style="white-space: normal;">' + data + '</p>';
            }
          },
        },
        {
          data: 'item',
          render: function(data, type, row, meta) {
            // Combine item and state_of_invoice information
            var stateOfInvoice = row.state_of_invoice;
            var badgeClass = '';
            // Check the value and apply Bootstrap badge accordingly
            if (stateOfInvoice === 'Parregullt') {
              badgeClass = 'bg-danger';
            } else if (stateOfInvoice === 'Rregullt') {
              badgeClass = 'bg-success';
            }
            // Combine item with state_of_invoice information
            var combinedData = '<div class="item-column">';
            combinedData += data; // Append the original item data
            combinedData += '</div><br>';
            combinedData += '<div class="badge-column">';
            combinedData += '<span class="badge ' + badgeClass + ' mx-1 rounded-5">' + stateOfInvoice + '</span>';
            combinedData += '</div>';
            return combinedData;
          }
        },
        {
          "data": null,
          "render": function(data, type, row) {
            // Concatenate 'total_amount' and 'total_amount_after_percentage' with HTML table formatting
            return '<table style="width:100%; font-size:12px;">' +
              '<tr>' +
              '<td style="text-align:left;">Shuma e përgjitshme:</td>' +
              '<td style="text-align:right;">' + row.total_amount + '</td>' +
              '</tr>' +
              '<tr>' +
              '<td style="text-align:left;">Shuma e për. % :</td>' +
              '<td style="text-align:right;">' + row.total_amount_after_percentage + '</td>' +
              '</tr>' +
              '</table>';
          }
        },
        {
          data: 'paid_amount'
        },
        {
          data: 'remaining_amount',
          render: function(data, type, row) {
            const remainingAmount = row.total_amount_after_percentage - row.paid_amount;
            return remainingAmount.toFixed(2);
          }
        },
        {
          data: 'actions',
          render: function(data, type, row) {
            return '<div>' +
              '<a href="#" style="text-decoration:none;" class="bg-white border border-1 px-3 py-2 rounded-5 mx-1 text-dark open-payment-modal" ' +
              'data-id="' + row.id + '" ' +
              'data-invoice-number="' + row.invoice_number + '" ' +
              'data-customer-id="' + row.customer_id + '" ' +
              'data-item="' + row.item + '" ' +
              'data-total-amount="' + row.total_amount_after_percentage + '" ' +
              'data-paid-amount="' + row.paid_amount + '" ' +
              'data-remaining-amount="' + (row.total_amount_after_percentage - row.paid_amount) + '">' +
              '<i class="fi fi-rr-euro"></i> Paguaj</a>  ' +
              '<a target="_blank" style="text-decoration:none;" href="complete_invoice.php?id=' + row.id + '" class="bg-white border border-1 px-3 py-2 rounded-5 mx-1 text-dark">' +
              '<i class="fi fi-rr-edit"></i> Edito</a>' +
              '<a target="_blank" style="text-decoration:none;" href="print_invoice.php?id=' + row.invoice_number + '" class="bg-white border border-1 px-3 py-2 rounded-5 mx-1 text-dark">' +
              '<i class="fi fi-rr-print"></i> Printo v1.0</a></div>';
          }
        }
      ],
    });
    var currentPage = 0;
    $(document).on('click', '.open-payment-modal', function(e) {
      e.preventDefault();
      var id = $(this).data('id');
      var invoiceNumber = $(this).data('invoice-number');
      var customerId = $(this).data('customer-id');
      var item = $(this).data('item');
      var totalAmount = $(this).data('total-amount');
      var paidAmount = $(this).data('paid-amount');
      var remainingAmount = $(this).data('remaining-amount');
      var customerName = getCustomerName(customerId);
      $('#invoiceId').val(id);
      $('#invoiceNumber').text(invoiceNumber);
      $('#customerName').text(customerName);
      $('#item').text(item);
      $('#totalAmount').text(totalAmount);
      $('#paidAmount').text(paidAmount);
      $('#remainingAmount').text(remainingAmount.toFixed(2));
      $('#paymentAmount').val(remainingAmount.toFixed(2));
      $('#paymentModal').modal('show');
    });
    $('#paymentAmount').on('input', function() {
      var paymentAmount = parseFloat($(this).val());
      var remainingAmount = parseFloat($('#remainingAmount').text());
      if (paymentAmount > remainingAmount) {
        $('#paymentAmountError').text('Shuma e pagesës nuk mund të kalojë shumën e obligimit.');
        $('#submitPayment').prop('disabled', true);
      } else {
        $('#paymentAmountError').text('');
        $('#submitPayment').prop('disabled', false);
      }
    });
    $('#submitPayment').click(function(e) {
      e.preventDefault();
      var invoiceId = $('#invoiceId').val();
      var paymentAmount = $('#paymentAmount').val();
      var bankInfo = $('#bankInfo').val();
      var type_of_pay = $('#type_of_pay').val();
      var description = $('#description').val();
      $.ajax({
        url: 'make_payment.php',
        method: 'POST',
        data: {
          invoiceId: invoiceId,
          paymentAmount: paymentAmount,
          bankInfo: bankInfo,
          type_of_pay: type_of_pay,
          description: description
        },
        success: function(response) {
          if (response === 'success') {
            $('#paymentModal').modal('hide');
            var row = table.row('#' + invoiceId).node();
            $(row).addClass('success-row');
            setTimeout(function() {
              $(row).removeClass('success-row');
            }, 3000);
            Swal.fire({
              title: 'Pagesa është kryer me sukses',
              icon: 'success',
              showConfirmButton: false,
              position: 'top-end',
              toast: true,
              timer: 5000,
              html: '<div style="text-align: left;">' +
                '<p>Numri i Faturës: ' + invoiceId + '</p>' +
                '<p>Emri i Klientit: ' + customerName.textContent + '</p>' +
                '<p>Shuma e Paguar: ' + paymentAmount + ' €</p>' +
                '</div>',
              width: '500px'
            });
            var currentPage = table.page.info().page;
            var currentPageOfSecondTable = tableSecond.page.info().page;
            table.ajax.reload(function() {
              table.page(currentPage).draw(false);
            });
            tableSecond.ajax.reload(function() {
              tableSecond.page(currentPageOfSecondTable).draw(false);
            });
            completePayments.ajax.reload();
          } else {
            Swal.fire({
              title: 'Pagesa dështoi',
              text: 'Pagesa për ID-në e faturës: ' + invoiceId + ' ka dështuar. Ju lutemi provoni përsëri.',
              icon: 'error',
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 5000
            });
            console.log('Pagesa dështoi: ' + response);
          }
        },
        error: function(error) {
          console.log('Gabim në kryerjen e pagesës: ' + error);
        }
      });
    });
    var invoice_trash = $('#invoices_trash').DataTable({
      responsive: true,
      processing: true,
      serverSide: true,
      dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
        "<'row'<'col-md-12'tr>>" +
        "<'row'<'col-md-6'><'col-md-6'p>>",
      buttons: [{
          extend: "pdfHtml5",
          text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
          titleAttr: "Eksporto tabelen ne formatin PDF",
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
          filename: "faturat_e_fshira_" + getCurrentDate() + ""
        },
        {
          extend: "copyHtml5",
          text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
          titleAttr: "Kopjo tabelen ne formatin Clipboard",
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
          filename: "faturat_e_fshira_" + getCurrentDate() + ""
        },
        {
          extend: "excelHtml5",
          text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
          titleAttr: "Eksporto tabelen ne formatin Excel",
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
          exportOptions: {
            modifier: {
              search: "applied",
              order: "applied",
              page: "all",
            },
          },
          filename: "faturat_e_fshira_" + getCurrentDate() + ""
        },
        {
          extend: "print",
          text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
          titleAttr: "Printo tabel&euml;n",
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
          filename: "faturat_e_fshira_" + getCurrentDate() + ""
        },
      ],
      ajax: {
        url: 'invoices_trash_server.php',
        type: 'POST',
      },
      columns: [{
          data: 'invoice_number'
        },
        {
          data: 'client_name',
        },
        {
          data: 'item'
        },
        {
          data: 'total_amount'
        },
        {
          data: 'total_amount_after_percentage'
        },
        {
          data: 'paid_amount'
        },
        {
          data: 'created_date'
        },
      ],
      order: [],
      initComplete: function() {
        var btns = $(".dt-buttons");
        btns.addClass("").removeClass("dt-buttons btn-group");
        var lengthSelect = $("div.dataTables_length select");
        lengthSelect.addClass("form-select");
        lengthSelect.css({
          width: "auto",
          margin: "0 8px",
          padding: "0.375rem 1.75rem 0.375rem 0.75rem",
          lineHeight: "1.5",
          border: "1px solid #ced4da",
          borderRadius: "0.25rem",
        });
      },
      fixedHeader: true,
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
      },
      stripeClasses: ['stripe-color']
    });

    function getCurrentDate() {
      var today = new Date();
      var dd = String(today.getDate()).padStart(2, '0');
      var mm = String(today.getMonth() + 1).padStart(2, '0');
      var yyyy = today.getFullYear();
      return yyyy + mm + dd;
    }
    $('#invoices_trash tbody').on('click', '.restore-btn', function() {
      var invoiceId = $(this).data('id');
      $.ajax({
        url: 'restore_invoice.php',
        type: 'POST',
        data: {
          id: invoiceId
        },
        success: function(response) {
          var result = JSON.parse(response);
          if (result.success) {
            Swal.fire({
              icon: 'success',
              title: 'Sukses!',
              text: result.message,
              timer: 3000,
              showConfirmButton: false
            }).then(function() {
              currentPage = invoice_trash.page.info().page;
              currentTablePage = table.page.info().page;
              invoice_trash.ajax.reload(function() {
                invoice_trash.page(currentPage).draw(false);
                table.page(currentTablePage).draw(false);
              });
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Gabim!',
              text: result.message
            });
          }
        },
        error: function(error) {
          console.error('Error restoring invoice:', error);
        }
      });
    });
  });
</script>
<script src="percentage_calculations.js"></script>
<script src="create_manual_invoice.js"></script>
<script src="completed_invoice_personal.js"> </script>
<script src="completed_invoice_biznes.js"> </script>
<script src="channels.js"></script>
<script src="invoice_trash.js"></script>
<script src="delete_buton_invoice.js"></script>
<script src="states.js"></script>
<?php include 'partials/footer.php' ?>
</body>

</html>