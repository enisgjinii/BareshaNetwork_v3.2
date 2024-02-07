<?php
session_start();
// include "./partials/sales.php";
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
        <?php include 'format_page.php' ?>
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
              $result->free();
            }
            ?>
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
                    <form id="yearForm">
                      <label for="year" class="form-label">Zgjidhni vitin:</label>
                      <select id="year" name="year" class="form-select">
                        <?php
                        // Generate options for years from 2021 to 2024
                        for ($year = 2021; $year <= 2024; $year++) {
                          echo "<option value=\"$year\">$year</option>";
                        }
                        ?>
                      </select>
                      <script>
                        new Selectr('#year', {
                          searchable: true
                        });
                      </script>
                      <br>
                      <button type="submit" class="input-custom-css px-3 py-2">
                        <i class="fi fi-rr-filter"></i>
                        Filtro</button>
                    </form>
                    <div id="monthlyChart"></div>
                  </div>

                </div>
              </div>
            </div>
            <div class="col-4">
              <div class="card rounded-5 bordered">
                <div class="card-body">
                  <h5 class="card-title" style="text-transform: none;text-decoration: none;">Faturat e fundit</h5>
                  <a href="invoice.php" class="input-custom-css px-3 py-2 mb-3" style="text-decoration: none;">Kalo tek faturat</a>
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
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
  $(document).ready(function() {
    // Function to fetch data and render the chart
    function fetchDataAndRenderChart(year) {
      // Fetch data from sales.php using AJAX
      $.ajax({
        url: './partials/sales.php',
        method: 'POST',
        data: {
          year: year
        },
        dataType: 'json',
        success: function(data) {
          // Process the data and render the chart
          renderChart(data);
        },
        error: function(xhr, status, error) {
          console.error('Error fetching data:', error);
        }
      });
    }

    // Function to render the chart
    // Function to render the chart
    function renderChart(data) {
      // Extract month names and values from the received data
      var monthNames = ['Janar', 'Shkurt', 'Mars', 'Prill', 'Maj', 'Qershor', 'Korrik', 'Gusht', 'Shtator', 'Tetor', 'Nentor', 'Dhjetor'];
      var shitjeValues = Object.values(data.shitje);
      var mbetjeValues = Object.values(data.mbetje);

      // Set up the chart options
      var options = {
        chart: {
          type: 'bar',
          height: 350,
        },
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: '55%',
            endingShape: 'rounded'
          },
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          show: true,
          width: 2,
          colors: ['transparent']
        },
        series: [{
          name: 'Shitje',
          data: shitjeValues
        }, {
          name: 'Mbetje',
          data: mbetjeValues
        }],
        xaxis: {
          categories: monthNames,
        },
        yaxis: {
          title: {
            text: 'Sales'
          }
        },
        fill: {
          opacity: 1
        },
        tooltip: {
          y: {
            formatter: function(val) {
              return val + " €";
            }
          }
        }
      };

      // Remove any existing chart element
      $('#monthlyChart').empty();

      // Create the ApexCharts instance
      var chart = new ApexCharts(document.querySelector("#monthlyChart"), options);

      // Render the chart
      chart.render();
    }


    // Event listener for form submission
    $('#yearForm').submit(function(e) {
      e.preventDefault(); // Prevent default form submission

      var selectedYear = $('#year').val();
      fetchDataAndRenderChart(selectedYear);
    });

    // Fetch and render chart when page loads
    var selectedYear = $('#year').val();
    fetchDataAndRenderChart(selectedYear);
  });
</script>

<?php } ?>