<?php
session_start();
include "./partials/sales.php";


// Backup logic
$backupFolder = 'backups/';
$timestamp = date('Y-m-d_H');
$backupFile = $backupFolder . 'backup_' . $timestamp . '.sql';
$zipBackupFile = $backupFolder . 'backup_' . $timestamp . '.zip';

// Fetch all tables in the database
$tables = array();
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
  $tables[] = $row[0];
}

// Ensure the backup folder exists
if (!file_exists($backupFolder)) {
  mkdir($backupFolder, 0755, true);
}

// Loop through each table and export its structure and data
$handle = fopen($backupFile, 'w');
foreach ($tables as $table) {
  // Export table structure
  $createTableSQL = "SHOW CREATE TABLE $table";
  $result = $conn->query($createTableSQL);
  $createTable = $result->fetch_row();
  fwrite($handle, $createTable[1] . ";\n");

  // Check if the table has a primary key
  $primaryKeySQL = "SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'";
  $primaryKeyResult = $conn->query($primaryKeySQL);
  $primaryKeyRow = $primaryKeyResult->fetch_assoc();

  // If the table has a primary key, export the most recent 500 rows based on it
  if ($primaryKeyRow !== null && isset($primaryKeyRow['Column_name'])) {
    $primaryKeyColumn = $primaryKeyRow['Column_name'];
    $selectDataSQL = "SELECT * FROM $table ORDER BY $primaryKeyColumn DESC LIMIT 500";
  } else {
    // If the table doesn't have a primary key, export the most recent 500 rows without ordering
    $selectDataSQL = "SELECT * FROM $table LIMIT 500";
  }

  $result = $conn->query($selectDataSQL);
  while ($row = $result->fetch_assoc()) {
    $rowValues = array_map(array($conn, 'real_escape_string'), $row);
    $rowValues = "'" . implode("', '", $rowValues) . "'";
    $insertDataSQL = "INSERT INTO $table VALUES ($rowValues);";
    fwrite($handle, $insertDataSQL . "\n");
  }
}

// Close the file handle
fclose($handle);

// Create a ZIP archive
$zip = new ZipArchive();
if ($zip->open($zipBackupFile, ZipArchive::CREATE) === TRUE) {
  $zip->addFile($backupFile, 'backup.sql');
  $zip->close();

  // Remove the uncompressed SQL file
  unlink($backupFile);
} else {
}

// Replace 'YOUR_API_KEY' with your actual YouTube API key
// $api_key = 'AIzaSyDKt-ziSnLKQfYGgAxqwjRtCc6ss-PFIaM';


// The channel ID of the YouTube channel you want to fetch videos from
$channel_id = 'UCV6ZBT0ZUfNbtZMbsy-L3CQ';

// Define the time periods for filtering
$time_periods = [
  '24 hours' => strtotime('-1 day'),
  '48 hours' => strtotime('-2 days'),
  '3 days' => strtotime('-3 days'),
  '7 days' => strtotime('-7 days'),
  '14 days ( Përdor shumë tokena , mos e perdorni shpesh ne afate te shkurta kohore)' => strtotime('-14 days'),
  '30 days ( Përdor shumë tokena , mos e perdorni shpesh ne afate te shkurta kohore)' => strtotime('-30 days'),
];

// Check if a time period is selected
$selected_period = isset($_GET['period']) ? $_GET['period'] : '24 hours';

// Calculate the start date for the selected period
$start_date = date('Y-m-d\TH:i:s\Z', $time_periods[$selected_period]);

// Initialize variables for pagination
$next_page_token = null;
$max_results = 10; // Number of videos to fetch per page

// Initialize an empty array to store videos
$videos = [];

do {
  // Construct the API request URL with the nextPageToken
  $url = "https://www.googleapis.com/youtube/v3/search?key=$api_key&channelId=$channel_id&order=date&publishedAfter=$start_date&maxResults=$max_results&pageToken=$next_page_token&type=video&part=snippet";

  // Make the API request
  $response = file_get_contents($url);

  if ($response) {
    $data = json_decode($response);

    foreach ($data->items as $item) {
      // Get video snippet data
      $snippet = $item->snippet;

      // Extract video details
      $video_title = $snippet->title;

      // $published_date = date('mm/dd/yyyy/hh:mm', strtotime($snippet->publishedAt));

      // Make this published date to look good formated
      $published_date = date('d/m/Y H:i:s', strtotime($snippet->publishedAt));
      // Add video details to the array
      $videos[] = [
        'title' => $video_title,
        'published' => $published_date,
      ];
    }

    $next_page_token = isset($data->nextPageToken) ? $data->nextPageToken : null;
  }
} while ($next_page_token);

$max = max($janarRezultatiShitjeve['sum'], $shkurtRezultatiShitjeve['sum'], $marsRezultatiShitjeve['sum'], $prillRezultatiShitjeve['sum']);
$min = min($janarRezultatiShitjeve['sum'], $shkurtRezultatiShitjeve['sum'], $marsRezultatiShitjeve['sum'], $prillRezultatiShitjeve['sum']);
$dd = strtotime("-6 Months");
$ggdata = date("Y-m-d", $dd);

$mp6 = $conn->query("SELECT SUM(klientit) AS sum FROM shitje WHERE data >= '$ggdata' AND data <= '$dataAktuale'");
$m6 = mysqli_fetch_array($mp6);

$api_key = "AIzaSyBrE0kFGTQJwn36FeR4NIyf4FEw2HqSSIQ";
$apiu = file_get_contents('https://www.googleapis.com/youtube/v3/channels?part=snippet&id=UCV6ZBT0ZUfNbtZMbsy-L3CQ&key=' . $api_key);
$apid = json_decode($apiu, true);

$aa = file_get_contents('https://www.googleapis.com/youtube/v3/channels?part=statistics&id=UCV6ZBT0ZUfNbtZMbsy-L3CQ&key=' . $api_key);
$aaa = json_decode($aa, true);
?>
<style>
  #container {
    height: 400px;
  }

  .highcharts-figure,
  .highcharts-data-table table {
    min-width: 310px;
    max-width: 800px;
    margin: 1em auto;
  }

  #sliders td input[type="range"] {
    display: inline;
  }

  #sliders td {
    padding-right: 1em;
    white-space: nowrap;
  }
</style>


<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="container">
        <!-- <div class="row my-1">
          <div class="col-12 col-xl-5 mb-4 mb-xl-0">
            <h4 class="font-weight-bold"></h4>
          </div>
        </div> -->
        <div class="row mb-3">
          <div class="col-12 bordered card rounded-5">
            <h5 class="text-center pt-3">Përmbledhje e përgjithshme</h5>
            <div class="row">
              <div class="col-4">
                <div class="card bg-primary rounded-5 text-white mx-auto text-center py-3 my-2">

                  <h3 class="font-weight-light me-2 mb-1">Abonues
                    <?php echo number_format($aaa['items'][0]['statistics']['subscriberCount'], 2, '.', ','); ?>
                  </h3>


                  <p>Numri total i abonues&euml;ve :
                    <?php echo number_format($aaa['items'][0]['statistics']['subscriberCount'], 2, '.', ','); ?>
                  </p>
                </div>
              </div>
              <div class="col-4">
                <div class="card bg-primary rounded-5 text-white mx-auto text-center py-3 my-2">

                  <h3 class="font-weight-light me-2 mb-1">Shikime
                    <?php echo number_format($aaa['items'][0]['statistics']['viewCount'], 2, '.', ','); ?>
                  </h3>


                  <p>Numri total i shikimeve :
                    <?php echo number_format($aaa['items'][0]['statistics']['viewCount'], 2, '.', ','); ?>
                  </p>
                </div>
              </div>
              <div class="col-4">
                <div class="card bg-primary rounded-5 text-white mx-auto text-center py-3 my-2">

                  <h3 class="font-weight-light me-2 mb-1">Ngarkime
                    <?php echo $aaa['items'][0]['statistics']['videoCount']; ?>
                  </h3>


                  <p>
                    Numri total i ngarkimeve :
                    <?php echo $aaa['items'][0]['statistics']['videoCount']; ?>
                  </p>
                </div>
              </div>
            </div>
          </div>

        </div>
        <?php
        function format_page_name($page)
        {
          if ($page == 'index.php') {
            return 'Shtepia';
          }

          if ($page == 'roles.php') {
            return 'Rolet';
          }

          if ($page == 'stafi.php') {
            return 'Klientet';
          }

          if ($page == 'ads.php') {
            return 'Llogarit&euml; e ADS';
          }

          if ($page == 'emails.php') {
            return 'Lista e email-eve';
          }

          if ($page == 'klient.php') {
            return 'Lista e klient&euml;ve';
          }

          if ($page == 'klient2.php') {
            return 'Lista e klient&euml;ve tjer&euml;';
          }


          if ($page == 'kategorit.php') {
            return 'Lista e kategorive';
          }

          if ($page == 'claim.php') {
            return 'Recent Claim';
          }

          if ($page == 'tiketa.php') {
            return 'Lista e tiketave';
          }

          if ($page == 'listang.php') {
            return 'Lista e k&euml;ng&euml;ve';
          }

          if ($page == 'shtoy.php') {
            return 'Regjistro k&euml;ng&euml;';
          }

          if ($page == 'listat.php') {
            return 'Lista e tiketave';
          }

          if ($page == 'tiketa.php') {
            return 'Tiket e re';
          }


          if ($page == 'whitelist.php') {
            return 'Whitelist';
          }

          if ($page == 'faturat.php') {
            return 'Pagesat Youtube';
          }

          if ($page == 'invoice.php') {
            return 'Pagesat Youtube_channel ( New )';
          }
          if ($page == 'pagesat_youtube.php') {
            return 'Pagesat YouTube ( Faza Test )';
          }


          if ($page == 'faturat2.php') {
            return 'Platformat Tjera';
          }

          if ($page == 'pagesat.php') {
            return 'Pagesat e kryera';
          }

          if ($page == 'rrogat.php') {
            return 'Pagat';
          }

          if ($page == 'shpenzimep.php') {
            return 'Shpenzimet personale';
          }

          if ($page == 'tatimi.php') {
            return 'Tatimi';
          }
          if ($page == 'yinc.php') {
            return 'Shpenzimet';
          }

          if ($page == 'filet.php') {
            return 'Dokumente tjera';
          }
          if ($page == 'github_logs.php') {
            return 'Aktiviteti ne Github';
          }

          if ($page == 'klient_CSV.php') {
            return 'Klient CSV';
          }

          if ($page == 'logs.php') {
            return 'Logs';
          }

          if ($page == 'notes.php') {
            return 'Shenime';
          }

          if ($page == 'takimet.php') {
            return 'Takimet';
          }


          if ($page == 'todo_list.php') {
            return 'To Do';
          }

          if ($page == 'kontrata_2.php') {
            return 'Kontrata e re';
          }

          if ($page == 'lista_kontratave.php') {
            return 'Lista e kontratave';
          }

          if ($page == 'csvFiles.php') {
            return 'Inserto CSV';
          }

          if ($page == 'filtroCSV.php') {
            return 'Filtro CSV';
          }

          if ($page == 'listaEFaturaveTePlatformave.php') {
            return 'Lista e faturave';
          }


          if ($page == 'pagesatEKryera.php') {
            return 'Pagesat e perfunduara';
          }

          if ($page == 'check_musics.php') {
            return 'Konfirmimi i kengeve';
          }

          if ($page == 'dataYT.php') {
            return 'Statistikat nga Youtube';
          }
          if ($page == 'channel_selection.php') {
            return 'Kanalet';
          }

          if ($page == 'ofertat.php') {
            return 'Ofertat';
          }

          if ($page == 'youtube_studio.php') {
            return 'Baresha analytics';
          }

          if ($page == 'kontrata_gjenelare_2.php') {
            return 'Kontrate e re ( Gjenerale )';
          }

          if ($page == 'lista_kontratave_gjenerale.php') {
            return 'Lista e kontratave ( Gjenerale )';
          }

          if ($page == 'facebook.php') {
            return 'Vegla Facebook';
          }

          if ($page == 'lista_faturave_facebook.php') {
            return 'Lista e faturave (Facebook)';
          }

          if ($page == 'autor.php') {
            return 'Autor';
          }

          if ($page == 'lista_kopjeve_rezerve.php') {
            return 'Lista e kopjeve rezerve';
          }

          if ($page == 'faturaFacebook.php') {
            return 'Krijo fatur&euml; (Facebook)';
          }
          if ($page == 'ascap.php') {
            return 'Ascap';
          }
          if ($page == 'klient-avanc.php') {
            return 'Lista e avanceve te klienteve';
          }
          if ($page == 'office_investments.php') {
            return 'Investimet e objektit';
          }
          if ($page == 'office_damages.php') {
            return 'Prishjet';
          }
          if ($page == 'office_requirements.php') {
            return 'Kerkesat';
          }
        }
        $pages = array(
          'stafi.php',
          'roles.php',
          'klient.php',
          'klient2.php',
          'kategorit.php',
          'ads.php',
          'emails.php',
          'shtoy.php',
          'listang.php',
          'tiketa.php',
          'listat.php',
          'claim.php',
          'whitelist.php',
          'rrogat.php',
          'tatimi.php',
          'yinc.php',
          'shpenzimep.php',
          'faturat.php',
          'pagesat.php',
          'faturat2.php',
          'filet.php',
          'notes.php',
          'github_logs.php',
          'todo_list.php',
          'takimet.php',
          'klient_CSV.php',
          'logs.php',
          'kontrata_2.php',
          'lista_kontratave.php',
          'csvFiles.php',
          'filtroCSV.php',
          'listaEFaturaveTePlatformave.php',
          'pagesatEKryera.php',
          'check_musics.php',
          'dataYT.php',
          'ofertat.php',
          'youtube_studio.php',
          'kontrata_gjenelare_2.php',
          'lista_kontratave_gjenerale.php',
          'facebook.php',
          'lista_faturave_facebook.php',
          'autor.php',
          'faturaFacebook.php',
          'ascap.php',
          'klient-avanc.php',
          'office_investments.php',
          'office_damages.php',
          'office_requirements.php'
        );

        ?>
        <?php
        if ($email == "endrit@bareshamusic.com" || $email == "egjini@bareshamusic.com" || $email == "lirie@bareshamusic.com" || $email == "yllzona@bareshamusic.com" || $email == "lyon@bareshamusic.com") { ?>
          <div class="card p-5 rounded-5 my-5">
            <h6 class="text-muted mb-3">Lista e faqeve në të cilat ju, si <p class="badge bg-primary rounded-5">
                <?php echo $email ?>
              </p> , keni akses : </h6>
            <?php
            $sql = 'SELECT googleauth.firstName AS user_name, roles.name AS role_name, roles.id AS role_id, GROUP_CONCAT(DISTINCT role_pages.page) AS pages
        FROM googleauth
        LEFT JOIN user_roles ON googleauth.id = user_roles.user_id
        LEFT JOIN roles ON user_roles.role_id = roles.id
        LEFT JOIN role_pages ON roles.id = role_pages.role_id
        WHERE googleauth.email = "' . $email . '"
        GROUP BY googleauth.id, roles.id';

            if ($result = $conn->query($sql)) {
              $accessiblePages = [];

              while ($row = $result->fetch_assoc()) {
                $pages = explode(',', $row['pages']);
                $accessiblePages = array_merge($accessiblePages, $pages);
              }

              // Filter unique accessible pages
              $accessiblePages = array_unique($accessiblePages);

              echo '<div class="row">';

              foreach ($accessiblePages as $page) {
                echo '<div class="col-md-3 mb-4">';
                echo '<div class="card  rounded-5 bg-light">';
                echo '<div class="card-body">';
                echo "<h6>" . format_page_name(trim($page)) . '</h3>';
                echo '<br><a href="' . $page . '" class="input-custom-css px-3 py-2 btn-sm" style="text-decoration: none;text-transform: none;">Hape faqen</a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
              }

              echo '</div>';

              // Free result set
              $result->free();
            }
            ?>

            <!-- <i class="fi fi-rr-sad text-danger"></i> -->
          </div>
        <?php } else { 
          
          ?>

          <div class="row">
            <div class="col-8">
              <div class="row gap-2">
                <div class="card rounded-5 bordered col">
                  <div class="card-body">
                    <p class="fw-bold text-md-left text-xl-left ">Fitimi n&euml; platform&euml;n YouTube</span>
                    </p>
                    <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                      <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0">
                        <?php echo $summ6['sum']; ?>&euro;
                      </h3>
                      <i class="ti-calendar icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                    </div>
                  </div>
                </div>
                <div class="card rounded-5 bordered col">
                  <div class="card-body">
                    <p class="fw-bold text-md-left text-xl-left">Fitimi n&euml; platformat tjera

                    </p>
                    <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                      <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0">
                        <?php echo $summ8['sum']; ?>&euro;
                      </h3>
                      <i class="ti-calendar icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                    </div>

                  </div>
                </div>
              </div>
              <br>
              <div class="row gap-2">
                <div class="card rounded-5 bordered col">
                  <div class="card-body">
                    <p class="fw-bold text-md-left text-xl-left">Pagesa klient&euml;ve</p>
                    <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                      <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0">
                        <?php echo $summ5['sum']; ?>&euro;
                      </h3>
                      <i class="ti-calendar icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                    </div>
                  </div>
                </div>
                <div class="card rounded-5 bordered col">
                  <div class="card-body">
                    <?php
                    $gc = $conn->query("SELECT * FROM ngarkimi");
                    $ngc = mysqli_num_rows($gc);
                    ?>
                    <p class="fw-bold text-md-left text-xl-left">Numri i ngarkim&euml;ve</p>
                    <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                      <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0">
                        <?php echo $ngc; ?>
                      </h3>
                      <i class="ti-youtube icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                    </div>
                  </div>
                </div>
              </div>
              <br>
              <div class="row gap-2">
                <div class="card rounded-5 bordered col">
                  <div class="card-body">
                    <div id="monthlyChart"></div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-4">
              <div class="card rounded-5 bordered">
                <!-- <img src="images/brand-icon.png" class="mx-auto px-3 py-3" width="25%" alt="..."> -->
                <!-- <hr /> -->
                <div class="card-body">
                  <h5 class="card-title" style="text-transform: none;text-decoration: none;">Faturat e fundit</h5>
                  <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>-->
                  <a href="invoices.php" class="input-custom-css px-3 py-2 mb-3" style="text-decoration: none;">Kalo tek faturat</a>
                  <br><br>
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>Emri</th>
                        <th>Shuma e fatures</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $invoice_data = $conn->query("SELECT * FROM invoices  ORDER BY id DESC LIMIT 8");
                      while ($row = $invoice_data->fetch_assoc()) {
                        // Go in table klientet and based on customer_id fetch emri
                        $customer_data = $conn->query("SELECT * FROM klientet WHERE id = '{$row['customer_id']}'");
                        $customer = $customer_data->fetch_assoc();
                        $row['customer_name'] = $customer['emri'];
                      ?>
                        <tr>
                          <td><?php echo $row['customer_name'] ?></td>
                          <td><?php echo $row['total_amount']; ?></td>
                        </tr>
                      <?php
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>












          <?php

          ?>

          <?php


          ?>


          <!-- <div class="row">
            <div class="col-12 grid-margin stretch-card">
              <div class="card rounded-5 bordered">
                <div class="card-body">
                  <select id="year-select">
                    <option value="2021">2021</option>
                    <option value="2022">2022</option>
                    <option value="2023">2023</option>
                  </select>
                  <p class="fw-bold">Pagesat e platformave</p>
                  <p class="text-muted font-weight-light">Grafiku i pagesave dhe fitimeve nga platformat</p>
                  <div id="myChart"></div>
                </div>
              </div>
            </div>
          </div> -->
          <!-- <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card rounded-5 bordered">
                <div class="card-body">
                  <p class="fw-bold mb-3">20 p&euml;rdoruesit m&euml; t&euml; mir&euml; me shumic&euml;n e abonent&euml;ve
                  </p>
                  <div class="table-responsive">
                    <table class="table border ">
                      <thead class="table-light">
                        <tr>
                          <th>Artisti</th>

                          <th>Subscribers</th>
                          <th>Last Pay</th>
                          <th>Total Pay</th>

                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $lastpay = null; // Initialize $lastpay variable
                        $totalii = null; // Initialize $totalii variable
                        $most = $conn->query("SELECT * FROM klientet ORDER BY subscribers DESC LIMIT 20");

                        while ($res = mysqli_fetch_assoc($most)) {
                          $kengtaid = $res['id'];

                          $merrpagesenefundit = $conn->prepare("SELECT * FROM fatura WHERE emri=? ORDER BY id DESC");
                          $merrpagesenefundit->bind_param("s", $kengtaid);
                          $merrpagesenefundit->execute();
                          $mpf = $merrpagesenefundit->get_result()->fetch_assoc();

                          if ($mpf !== null) {
                            $mft = $mpf['fatura'];

                            $lastpay1 = $conn->prepare("SELECT SUM(shuma) AS sumc FROM pagesat WHERE fatura=?");
                            $lastpay1->bind_param("s", $mft);
                            $lastpay1->execute();
                            $lastpay = $lastpay1->get_result()->fetch_assoc();

                            $sqlja = $conn->query("SELECT * FROM fatura WHERE emri='$kengtaid'");
                            $totalii = 0;

                            while ($sqlja2 = $sqlja->fetch_assoc()) {
                              $fatli = $sqlja2['fatura'];
                              $getsum = $conn->query("SELECT SUM(klientit) as total FROM shitje WHERE fatura='$fatli'");
                              $rowit = $getsum->fetch_assoc();
                              $totalii += $rowit['total'];
                            }

                            if (empty($totalii)) {
                              $totalii = "0.00";
                            }
                          }

                        ?>


                          <tr>
                            <td><b>
                                <?php echo $res['emri']; ?>
                              </b></td>
                            <td><b>
                                <?php echo $res['subscribers']; ?>
                              </b></td>
                            <td>
                              <b>
                                <?php echo isset($lastpay['sumc']) ? $lastpay['sumc'] : ''; ?>&euro;
                              </b>
                            </td>
                            <td>
                              <?php echo $totalii; ?>&euro;
                              </b>
                            </td>
                          </tr>
                        <?php
                        }
                        ?>




                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

          </div> -->
          <!-- <div class="row">
            <div class="col-md-4 stretch-card grid-margin grid-margin-md-0">
              <div class="card rounded-5 bordered">
                <div class="card-body">
                  <p class="fw-bold mb-0">Ngarkimet n&euml; Baresha</p>
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>K&euml;nga</th>
                        <th>Platforma</th>
                        <th>Data</th>
                      </tr>

                    </thead>
                    <tbody>
                      <?php
                      $kueri = $conn->query("SELECT * FROM ngarkimi WHERE klienti='197' ORDER BY id DESC LIMIT 10");
                      while ($row = mysqli_fetch_array($kueri)) {
                      ?>
                        <tr>
                          <td class="text-muted">
                            <a href="<?php echo $row['linku']; ?>">
                              <?php echo $row['emri']; ?>
                            </a>
                          </td>
                          <td class="text-muted">
                            <?php echo $row['platforma']; ?>
                          </td>
                          <td class="text-muted">
                            <?php echo $row['data']; ?>
                          </td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="col-md-4 stretch-card">
              <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                  <div class="card rounded-5 bordered">
                    <div class="card-body">
                      <p class="fw-bold">Pamja vizuale e klienteve te monetizuar dhe te pamonetizuar</p>
                      <br>
                      <canvas id="charts"></canvas>
                    </div>
                  </div>
                </div>
                <div class="col-md-12 stretch-card grid-margin grid-margin-md-0">
                  <div class="card rounded-5 bordered">
                    <div class="card-body">
                      <p class="fw-bold">Numri i takim&euml;ve</p>
                      <br>
                      <div class="row">
                        <div class="col-8">
                          <h3>
                            <?php echo $takimet2; ?>
                          </h3>
                          <p class=" font-weight-light mb-0">Numri total i takimeve t&euml; mbajtura dhe takimet
                            n&euml; proces</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4 stretch-card">
              <div class="card rounded-5 bordered">
                <div class="card-body">
                  <p class="fw-bold">Regjistri i aktiviteteve</p>

                  <div class="row">
                    <div class="col-md-12">
                      <?php
                      $merri = $conn->query("SELECT * FROM logs ORDER BY id DESC LIMIT 5");
                      while ($k = mysqli_fetch_array($merri)) {
                      ?>
                        <div class="card rounded-5 bordered mb-3">
                          <div class="card-body">
                            <h5 class="card-title">
                              <?php echo $k['stafi']; ?>
                            </h5>
                            <h6 class="card-subtitle mb-2 text-muted">
                              <?php echo $k['koha']; ?>
                            </h6>
                            <p class="card-text">
                              <?php echo $k['ndryshimi']; ?>
                            </p>
                          </div>
                        </div>
                      <?php } ?>
                    </div>
                  </div>



                </div>
              </div>
            </div>
          </div> -->
      </div>
    </div>
  </div>
</div>

<?php

          $v2021 = $conn->query("SELECT SUM(mbetja) AS sum FROM shitje WHERE data >= '2021-01-01' AND data <= '2021-12-31'");
          $v21 = mysqli_fetch_array($v2021);
          $v2022 = $conn->query("SELECT SUM(mbetja) AS sum FROM shitje WHERE data >= '2022-01-01' AND data <= '2022-12-31'");
          $v22 = mysqli_fetch_array($v2022);
          $v2023 = $conn->query("SELECT SUM(mbetja) AS sum FROM shitje WHERE data >= '2023-01-01' AND data <= '2023-12-31'");
          $v23 = mysqli_fetch_array($v2023);
?>
<script>
  const yearSelect = document.getElementById("year-select");
  const chartContainer = document.getElementById("myChart");
  // Define data objects
  var shitje2021 = {
    labels: ["Janar", "Shkurt", "Mars", "Prill", "Maj", "Qershor", "Korrik", "Gusht", "Shtator", "Tetor", "Nentor", "Dhjetor"],
    datasets: [{
        label: "Shitje",
        data: [
          <?php echo $janarRezultatiShitjeve2021['sum']; ?>,
          <?php echo $shkurtRezultatiShitjeve2021['sum']; ?>,
          <?php echo $marsRezultatiShitjeve2021['sum']; ?>,
          <?php echo $prillRezultatiShitjeve2021['sum']; ?>,
          <?php echo $majRezultatiShitjeve2021['sum']; ?>,
          <?php echo $qershorRezultatiShitjeve2021['sum']; ?>,
          <?php echo $korrikRezultatiShitjeve2021['sum']; ?>,
          <?php echo $gushtRezultatiShitjeve2021['sum']; ?>,
          <?php echo $shtatorRezultatiShitjeve2021['sum']; ?>,
          <?php echo $tetorRezultatiShitjeve2021['sum']; ?>,
          <?php echo $nentorRezultatiShitjeve2021['sum']; ?>,
          <?php echo $dhjetorRezultatiShitjeve2021['sum']; ?>
        ],

      },
      {
        label: "Mbetje",
        data: [
          <?php echo $janarRezultatiMbetjes2021['sum']; ?>,
          <?php echo $shkurtRezultatiMbetjes2021['sum']; ?>,
          <?php echo $marsRezultatiMbetjes2021['sum']; ?>,
          <?php echo $prillRezultatiMbetjes2021['sum']; ?>,
          <?php echo $majRezultatiMbetjes2021['sum']; ?>,
          <?php echo $qershorRezultatiMbetjes2021['sum']; ?>,
          <?php echo $korrikRezultatiMbetjes2021['sum']; ?>,
          <?php echo $gushtRezultatiMbetjes2021['sum']; ?>,
          <?php echo $shtatorRezultatiMbetjes2021['sum']; ?>,
          <?php echo $tetorRezultatiMbetjes2021['sum']; ?>,
          <?php echo $nentorRezultatiMbetjes2021['sum']; ?>,
          <?php echo $dhjetorRezultatiMbetjes2021['sum']; ?>
        ],

      },
    ],
  };

  var shitje2022 = {
    labels: ["Janar", "Shkurt", "Mars", "Prill", "Maj", "Qershor", "Korrik", "Gusht", "Shtator", "Tetor", "Nentor", "Dhjetor"],
    datasets: [{
        label: "Shitjet",
        data: [
          <?php echo $janarRezultatiShitjeve['sum']; ?>,
          <?php echo $shkurtRezultatiShitjeve['sum']; ?>,
          <?php echo $marsRezultatiShitjeve['sum']; ?>,
          <?php echo $prillRezultatiShitjeve['sum']; ?>,
          <?php echo $majRezultatiShitjeve['sum']; ?>,
          <?php echo $qershorRezultatiShitjeve['sum']; ?>,
          <?php echo $korrikRezultatiShitjeve['sum']; ?>,
          <?php echo $gushtRezultatiShitjeve['sum']; ?>,
          <?php echo $shtatorRezultatiShitjeve['sum']; ?>,
          <?php echo $tetorRezultatiShitjeve['sum']; ?>,
          <?php echo $nentorRezultatiShitjeve['sum']; ?>,
          <?php echo $dhjetorRezultatiShitjeve['sum']; ?>
        ],
      },
      {
        label: "Mbetje",
        data: [
          <?php echo $janarRezultatiMbetjes['sum']; ?>,
          <?php echo $shkurtRezultatiMbetjes['sum']; ?>,
          <?php echo $marsRezultatiMbetjes['sum']; ?>,
          <?php echo $prillRezultatiMbetjes['sum']; ?>,
          <?php echo $majRezultatiMbetjes['sum']; ?>,
          <?php echo $qershorRezultatiMbetjes['sum']; ?>,
          <?php echo $korrikRezultatiMbetjes['sum']; ?>,
          <?php echo $gushtRezultatiMbetjes['sum']; ?>,
          <?php echo $shtatorRezultatiMbetjes['sum']; ?>,
          <?php echo $tetorRezultatiMbetjes['sum']; ?>,
          <?php echo $nentorRezultatiMbetjes['sum']; ?>,
          <?php echo $dhjetorRezultatiMbetjes['sum']; ?>
        ],

      },
    ],
  };

  var shitje2023 = {
    labels: ["Janar", "Shkurt", "Mars", "Prill", "Maj", "Qershor", "Korrik", "Gusht", "Shtator", "Tetor", "Nentor", "Dhjetor"],
    datasets: [{
        label: "Shitjet",
        data: [<?php echo $janarRezultatiShitjeve2023['sum']; ?>,
          <?php echo $shkurtRezultatiShitjeve2023['sum']; ?>,
          <?php echo $marsRezultatiShitjeve2023['sum']; ?>,
          <?php echo $prillRezultatiShitjeve2023['sum']; ?>,
          <?php echo $majRezultatiShitjeve2023['sum']; ?>,
          <?php echo $qershorRezultatiShitjeve2023['sum']; ?>,
          <?php echo $korrikRezultatiShitjeve2023['sum']; ?>,
          <?php echo $gushtRezultatiShitjeve2023['sum']; ?>,
          <?php echo $shtatorRezultatiShitjeve2023['sum']; ?>,
          <?php echo $tetorRezultatiShitjeve2023['sum']; ?>,
          <?php echo $nentorRezultatiShitjeve2023['sum']; ?>,
          <?php echo $dhjetorRezultatiShitjeve2023['sum']; ?>
        ],
      },
      {
        label: "Mbetjet",
        data: [<?php echo $janarRezultatiMbetjes2023['sum']; ?>,
          <?php echo $shkurtRezultatiMbetjes2023['sum']; ?>,
          <?php echo $marsRezultatiMbetjes2023['sum']; ?>,
          <?php echo $prillRezultatiMbetjes2023['sum']; ?>,
          <?php echo $majRezultatiMbetjes2023['sum']; ?>,
          <?php echo $qershorRezultatiMbetjes2023['sum']; ?>,
          <?php echo $korrikRezultatiMbetjes2023['sum']; ?>,
          <?php echo $gushtRezultatiMbetjes2023['sum']; ?>,
          <?php echo $shtatorRezultatiMbetjes2023['sum']; ?>,
          <?php echo $tetorRezultatiMbetjes2023['sum']; ?>,
          <?php echo $nentorRezultatiMbetjes2023['sum']; ?>,
          <?php echo $dhjetorRezultatiMbetjes2023['sum']; ?>
        ],
      },

    ],
  };

  const defaultYear = "2023";
  const defaultData = {
    labels: ["Janar", "Shkurt", "Mars", "Prill", "Maj", "Qershor", "Korrik", "Gusht", "Shtator", "Tetor", "Nentor", "Dhjetor"],
    datasets: [{
        label: "Shitjet",
        data: [<?php echo $janarRezultatiShitjeve2023['sum']; ?>,
          <?php echo $shkurtRezultatiShitjeve2023['sum']; ?>,
          <?php echo $marsRezultatiShitjeve2023['sum']; ?>,
          <?php echo $prillRezultatiShitjeve2023['sum']; ?>,
          <?php echo $majRezultatiShitjeve2023['sum']; ?>,
          <?php echo $qershorRezultatiShitjeve2023['sum']; ?>,
          <?php echo $korrikRezultatiShitjeve2023['sum']; ?>,
          <?php echo $gushtRezultatiShitjeve2023['sum']; ?>,
          <?php echo $shtatorRezultatiShitjeve2023['sum']; ?>,
          <?php echo $tetorRezultatiShitjeve2023['sum']; ?>,
          <?php echo $nentorRezultatiShitjeve2023['sum']; ?>,
          <?php echo $dhjetorRezultatiShitjeve2023['sum']; ?>
        ],
      },
      {
        label: "Mbetjet",
        data: [<?php echo $janarRezultatiMbetjes2023['sum']; ?>,
          <?php echo $shkurtRezultatiMbetjes2023['sum']; ?>,
          <?php echo $marsRezultatiMbetjes2023['sum']; ?>,
          <?php echo $prillRezultatiMbetjes2023['sum']; ?>,
          <?php echo $majRezultatiMbetjes2023['sum']; ?>,
          <?php echo $qershorRezultatiMbetjes2023['sum']; ?>,
          <?php echo $korrikRezultatiMbetjes2023['sum']; ?>,
          <?php echo $gushtRezultatiMbetjes2023['sum']; ?>,
          <?php echo $shtatorRezultatiMbetjes2023['sum']; ?>,
          <?php echo $tetorRezultatiMbetjes2023['sum']; ?>,
          <?php echo $nentorRezultatiMbetjes2023['sum']; ?>,
          <?php echo $dhjetorRezultatiMbetjes2023['sum']; ?>
        ],
      },

    ],
  }; // Create the initial chart
  const pagesat_chart = Highcharts.chart('myChart', {
    chart: {
      type: 'line'
    },
    title: {
      text: 'Pagesat e platformave'
    },
    xAxis: {
      categories: defaultData.labels
    },
    yAxis: {
      title: {
        text: 'Shuma'
      }
    },
    series: defaultData.datasets
  });

  // Event listener for year selection
  yearSelect.addEventListener("change", function() {
    const selectedYear = yearSelect.value;

    // Update chart data based on user selection
    if (selectedYear === "2021") {
      pagesat_chart.update({
        xAxis: {
          categories: shitje2021.labels
        },
        series: shitje2021.datasets
      });
    } else if (selectedYear === "2022") {
      pagesat_chart.update({
        xAxis: {
          categories: shitje2022.labels
        },
        series: shitje2022.datasets
      });
    } else if (selectedYear === "2023") {
      pagesat_chart.update({
        xAxis: {
          categories: shitje2023.labels
        },
        series: shitje2023.datasets
      });
    }
  });
</script>

<script>
  const coins = ["2021", "2022", "2023"];
  const marketCap = [<?php echo $v21['sum']; ?>, <?php echo $v22['sum']; ?>, <?php echo $v23['sum']; ?>];

  // Create the pie chart
  Highcharts.chart('yearlyChart', {
    chart: {
      type: 'pie'
    },
    title: {
      text: 'Raporti p&euml;rgjat&euml; viteve'
    },
    tooltip: {
      pointFormat: '{series.name}: <b>{point.y}</b>'
    },
    plotOptions: {
      pie: {
        allowPointSelect: true,
        cursor: 'pointer',
        dataLabels: {
          enabled: true,
          format: '<b>{point.name}</b>: {point.y}'
        },
        colors: ['rgba(62, 149, 205, 0.8)', 'rgba(237, 85, 101, 0.8)', 'rgba(102, 204, 102, 0.8)']
      }
    },
    series: [{
      name: 'Pagesa',
      data: coins.map((year, index) => ({
        name: year,
        y: marketCap[index]
      }))
    }]
  });

  // Create the line chart
</script>



<?php
          $kueri = $conn->query("SELECT COUNT(*) as count FROM klientet where monetizuar = 'PO'");
          $klientetEMonetizuar = mysqli_fetch_array($kueri);

          $kueri2 = $conn->query("SELECT COUNT(*) as count FROM klientet where monetizuar = 'JO'");
          $klientetEPamonetizuar = mysqli_fetch_array($kueri2);

          $labels = array('Monetizuar', 'Pamonetizuar');
          $data = array($klientetEMonetizuar['count'], $klientetEPamonetizuar['count']);

?>

<script>
  document.addEventListener('keydown', function(event) {
    if (event.shiftKey && event.key === 'A') {
      window.location.href = 'takimet.php';
    }
  });



  const charts = document.getElementById("charts");

  var ctx2 = charts.getContext("2d");

  var data = {
    labels: <?php echo json_encode($labels); ?>,
    datasets: [{
      label: 'Klientet',
      data: <?php echo json_encode($data); ?>,
      backgroundColor: ['#36A2EB', '#FF6384']
    }]
  };
  var myChart = new Chart(ctx2, {
    type: 'pie',
    data: data,
    options: {
      responsive: true,
      legend: {
        display: true,
        position: "top",
        labels: {
          fontColor: "#333",
          fontSize: 16
        }
      },
      animation: {
        duration: 1000,
        easing: "easeOutQuart"
      },
      elements: {
        arc: {
          borderWidth: 2
        }
      },
      plugins: {
        legend: {
          title: {
            display: true,
            text: "Klientet e monetizuar dhe te pamonetizuar"
          }
        }
      }
    }
  });
</script>

<script>
  // Data from PHP variables
  var monthsData = [{
      name: "Janar",
      value: <?php echo $janarRezultatiShitjeve['sum']; ?>
    },
    {
      name: "Shkurt",
      value: <?php echo $shkurtRezultatiShitjeve['sum']; ?>
    },
    {
      name: "Mars",
      value: <?php echo $marsRezultatiShitjeve['sum']; ?>
    },
    {
      name: "Prill",
      value: <?php echo $prillRezultatiShitjeve['sum']; ?>
    },
    {
      name: "Maj",
      value: <?php echo $majRezultatiShitjeve['sum']; ?>

    },
    {

      name: "Qershor",
      value: <?php echo $qershorRezultatiShitjeve['sum']; ?>

    },
    {

      name: "Korrik",
      value: <?php echo $korrikRezultatiShitjeve['sum']; ?>

    },
    {

      name: "Gusht",
      value: <?php echo $gushtRezultatiShitjeve['sum']; ?>

    },
    {
      name: "Shtator",
      value: <?php echo $shtatorRezultatiShitjeve['sum']; ?>
    },
    {

      name: "Tetor",
      value: <?php echo $tetorRezultatiShitjeve['sum']; ?>
    },
    {
      name: "Nentor",
      value: <?php echo $nentorRezultatiShitjeve['sum']; ?>
    },
    {
      name: "Dhjetor",
      value: <?php echo $dhjetorRezultatiShitjeve['sum']; ?>
    },

    // Add data for other months
  ];

  // Extract month names and values for chart
  var monthNames = monthsData.map(function(month) {
    return month.name;
  });
  var monthValues = monthsData.map(function(month) {
    return month.value;
  });

  // Set up the chart
  const chartOptions = {
    chart: {
      renderTo: 'monthlyChart', // Use the ID of the div where you want to render the chart
      type: 'column' // Use 'column' for a basic column chart
    },
    xAxis: {
      categories: monthNames,
      title: {
        text: 'Muajt'
      }
    },
    yAxis: {
      title: {
        text: 'Pagesa'
      }
    },
    tooltip: {
      headerFormat: '<b>{point.key}</b><br>',
      pointFormat: 'Pagesa: {point.y}&euro;'
    },
    title: {
      text: ''
    },
    legend: {
      enabled: true, // Enable legend
      align: 'left',
      verticalAlign: 'top',
      layout: 'horizontal',
    },

    plotOptions: {
      column: {
        color: '#0070C0',
        dataLabels: {
          enabled: true,
          format: '{point.y:.2f} €' // Show data labels with two decimal places
        }
      }
    },

    series: [{
      name: 'Shitjet',
      data: monthValues
    }]
  };

  // Create the Highcharts chart
  const chartsecond = new Highcharts.Chart(chartOptions);
</script>

<?php } ?>