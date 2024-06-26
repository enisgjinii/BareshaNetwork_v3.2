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
      <?php include 'format_page.php' ?>
      <?php
      if ($email == "endrit@bareshamusic.com" || $email == "egjini@bareshamusic.com" || $email == "lirie@bareshamusic.com" || $email == "yllzona@bareshamusic.com" || $email == "lyon@bareshamusic.com" || $email == "e.muqa@bareshamusic.com") { ?>
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
          <div class="col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 col-xxl-8">
            <div class="row gap-2">
              <div class="card rounded-5 bordered col">
                <div class="card-body">
                  <p class="fw-bold text-md-left text-xl-left ">Fitimi në platformën YouTube</span>
                  </p>
                  <div class="d-flex flex-wrap justify-content-between justify-content-sm-center justify-content-md-center justify-content-lg-between justify-content-xl-between align-items-center">
                    <?php
                    class RevenueCalculator
                    {
                      private $conn;
                      public function __construct($conn)
                      {
                        $this->conn = $conn;
                      }
                      public function getTotalRevenue()
                      {
                        $total = 0;
                        $total += $this->getTotalFromInvoices();
                        $total += $this->getTotalFromSales();
                        return $total;
                      }
                      private function getTotalFromInvoices()
                      {
                        $sql = "SELECT SUM(total_amount) AS sum FROM invoices";
                        $result = $this->conn->query($sql);
                        $row = $result->fetch_assoc();
                        return $row['sum'];
                      }
                      private function getTotalFromSales()
                      {
                        $sql = "SELECT SUM(totali) AS sum FROM shitje";
                        $result = $this->conn->query($sql);
                        $row = $result->fetch_assoc();
                        return $row['sum'];
                      }
                    }
                    // Usage:
                    $revenueCalculator = new RevenueCalculator($conn);
                    $total = $revenueCalculator->getTotalRevenue();
                    ?>
                    <h3 class="mb-0 mb-sm-2 mb-md-2 mb-lg-0 mb-xl-0 order-md-1 order-lg-0 order-xl-0 text-wrap text-sm fs-4 text-md fs-5 text-lg fs-6 text-xl fs-7">
                      <?php echo $total . ' &euro;'; ?>
                    </h3>
                    <i class="fi fi-rr-earnings icon-md text-muted mb-0 mb-md-3 mb-lg-0 mb-xl-0 d-none d-md-block"></i>
                  </div>
                </div>
              </div>
            </div>
            <br>
            <div class="row gap-2">
              <!-- Total Payments -->
              <div class="card rounded-5 bordered col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl">
                <div class="card-body">
                  <p class="fw-bold">Pagesat e bëra drejtë klientëve</p>
                  <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <h3 class="mb-0 mb-sm-2 mb-md-2 mb-lg-0 mb-xl-0 order-md-1 order-lg-0 order-xl-0 text-wrap text-sm fs-4 text-md fs-5 text-lg fs-6 text-xl fs-7">
                      <?php
                      class PaymentCalculator
                      {
                        private $conn;
                        public function __construct($conn)
                        {
                          $this->conn = $conn;
                        }
                        public function getTotalPayments()
                        {
                          $total = 0;
                          $total += $this->getTotalFromPayments();
                          $total += $this->getTotalFromPagesat();
                          return $total;
                        }
                        private function getTotalFromPayments()
                        {
                          $sql = "SELECT SUM(payment_amount) AS payment_amount FROM payments";
                          $result = $this->conn->query($sql);
                          $row = $result->fetch_assoc();
                          return $row['payment_amount'];
                        }
                        private function getTotalFromPagesat()
                        {
                          $sql = "SELECT SUM(shuma) AS shuma FROM pagesat";
                          $result = $this->conn->query($sql);
                          $row = $result->fetch_assoc();
                          return $row['shuma'];
                        }
                      }
                      // Usage:
                      $paymentCalculator = new PaymentCalculator($conn);
                      $total = $paymentCalculator->getTotalPayments();
                      echo $total;
                      ?>
                      &euro;
                    </h3>
                    <i class="fi fi-rr-team-check icon-md text-muted d-none d-md-block"></i>
                  </div>
                </div>
              </div>
              <!-- Number of Charges -->
              <div class="card rounded-5 bordered col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl">
                <div class="card-body">
                  <?php
                  $gc = $conn->query("SELECT * FROM ngarkimi");
                  $ngc = mysqli_num_rows($gc);
                  ?>
                  <p class="fw-bold">Numri i ngarkimëve të këngëve ne sistem</p>
                  <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <h3 class="mb-0 mb-sm-2 mb-md-2 mb-lg-0 mb-xl-0 order-md-1 order-lg-0 order-xl-0 text-wrap text-sm fs-4 text-md fs-5 text-lg fs-6 text-xl fs-7">
                      <?php echo $ngc; ?>
                    </h3>
                    <i class="fi fi-rr-data-transfer icon-md text-muted d-none d-md-block"></i>
                  </div>
                </div>
              </div>
            </div>
            <br>
            <div class="row gap-2 d-none d-md-block">
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
          <div class="col-4 d-none d-md-block">
            <div id="carouselExampleAutoplaying" class="carousel  slide d-none d-md-block" data-bs-ride="carousel">
              <div class="carousel-inner rounded-5 shadow-sm">
                <div class="carousel-item  active">
                  <img src="./images/youtube-music.webp" class="d-block w-100 opacity-75" alt="..." style="height: 250px;object-fit: cover">
                  <div class="carousel-caption d-none d-md-block">
                    <h5 class="bg-white text-dark border-1 rounded-5 px-1 py-2">
                      Youtube Music Revenue :
                      <?php
                      $sql = "SELECT SUM(RevenueUSD) as TotalRevenueUSD FROM `platformat_2` WHERE Partner = 'YouTube Music'";
                      $result = $conn->query($sql);
                      $row = $result->fetch_assoc();
                      $totalRevenue = number_format($row['TotalRevenueUSD'], 2); // Format the revenue amount to display with two decimal places
                      echo $totalRevenue;
                      ?> &euro;
                    </h5>
                  </div>
                </div>
                <div class="carousel-item">
                  <img src="./images/spotify.png" class="d-block w-100 opacity-75" alt="..." style="height: 250px;object-fit: cover">
                  <div class="carousel-caption d-none d-md-block">
                    <h5 class="bg-white text-dark border-1 rounded-5 p-1 ">Spotify Revenue :
                      <?php
                      $sql = "SELECT SUM(RevenueUSD) as TotalRevenueUSD FROM `platformat_2` WHERE Partner = 'Spotify'";
                      $result = $conn->query($sql);
                      $row = $result->fetch_assoc();
                      $totalRevenue = number_format($row['TotalRevenueUSD'], 2); // Format the revenue amount to display with two decimal places
                      echo $totalRevenue;
                      ?> &euro;
                    </h5>
                  </div>
                </div>
                <div class="carousel-item">
                  <img src="./images/amazon-music.webp" class="d-block w-100 opacity-75" alt="..." style="height: 250px;object-fit: cover">
                  <div class="carousel-caption d-none d-md-block">
                    <h5 class="bg-white text-dark border-1 rounded-5 p-1 ">Amazon Music Revenue :
                      <?php
                      $sql = "SELECT SUM(RevenueUSD) as TotalRevenueUSD FROM `platformat_2` WHERE Partner = 'Amazon Music'";
                      $result = $conn->query($sql);
                      $row = $result->fetch_assoc();
                      $totalRevenue = number_format($row['TotalRevenueUSD'], 2); // Format the revenue amount to display with two decimal places
                      echo $totalRevenue;
                      ?> &euro;
                    </h5>
                  </div>
                </div>
                <div class="carousel-item">
                  <img src="./images/apple-music.jpg" class="d-block w-100 opacity-75" alt="..." style="height: 250px;object-fit: cover">
                  <div class="carousel-caption d-none d-md-block">
                    <h5 class="bg-white text-dark border-1 rounded-5 p-1 ">Apple Music Revenue :
                      <?php
                      $sql = "SELECT SUM(RevenueUSD) as TotalRevenueUSD FROM `platformat_2` WHERE Partner = 'Apple Music'";
                      $result = $conn->query($sql);
                      $row = $result->fetch_assoc();
                      $totalRevenue = number_format($row['TotalRevenueUSD'], 2); // Format the revenue amount to display with two decimal places
                      echo $totalRevenue;
                      ?> &euro;
                    </h5>
                  </div>
                </div>
                <div class="carousel-item">
                  <img src="./images/deezer.webp" class="d-block w-100 opacity-75" alt="..." style="height: 250px;object-fit: cover">
                  <div class="carousel-caption d-none d-md-block">
                    <h5 class="bg-white text-dark border-1 rounded-5 p-1 ">Deezer Revenue :
                      <?php
                      $sql = "SELECT SUM(RevenueUSD) as TotalRevenueUSD FROM `platformat_2` WHERE Partner = 'Deezer'";
                      $result = $conn->query($sql);
                      $row = $result->fetch_assoc();
                      $totalRevenue = number_format($row['TotalRevenueUSD'], 2); // Format the revenue amount to display with two decimal places
                      echo $totalRevenue;
                      ?> &euro;
                    </h5>
                  </div>
                </div>
                <div class="carousel-item">
                  <img src="./images/tiktok.jpg" class="d-block w-100 opacity-75" alt="..." style="height: 250px;object-fit: cover">
                  <div class="carousel-caption d-none d-md-block">
                    <h5 class="bg-white text-dark border-1 rounded-5 p-1 ">TikTok Revenue :
                      <?php
                      $sql = "SELECT SUM(RevenueUSD) as TotalRevenueUSD FROM `platformat_2` WHERE Partner = 'TikTok'";
                      $result = $conn->query($sql);
                      $row = $result->fetch_assoc();
                      $totalRevenue = number_format($row['TotalRevenueUSD'], 2); // Format the revenue amount to display with two decimal places
                      echo $totalRevenue;
                      ?> &euro;
                    </h5>
                  </div>
                </div>
                <div class="carousel-item">
                  <img src="./images/facebook.png" class="d-block w-100 opacity-75" alt="..." style="height: 250px;object-fit: cover">
                  <div class="carousel-caption d-none d-md-block">
                    <h5 class="bg-white text-dark border-1 rounded-5 p-1 ">Facebook Revenue :
                      <?php
                      $sql = "SELECT SUM(RevenueUSD) as TotalRevenueUSD FROM `platformat_2` WHERE Partner = 'Facebook'";
                      $result = $conn->query($sql);
                      $row = $result->fetch_assoc();
                      $totalRevenue = number_format($row['TotalRevenueUSD'], 2); // Format the revenue amount to display with two decimal places
                      echo $totalRevenue;
                      ?> &euro;
                    </h5>
                  </div>
                </div>
              </div>
              <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
              </button>
            </div>
            <br>
            <div class="card rounded-5 bordered">
              <div class="card-body">
                <h5 class="card-title" style="text-transform: none;text-decoration: none;">Faturat e fundit</h5>
                <a href="invoice.php" class="input-custom-css px-3 py-2 mb-3" style="text-decoration: none;">Kalo tek faturat</a>
                <div class="table-responsive"> <!-- Ensure table responsiveness -->
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>Emri</th>
                        <th>Shuma e fatures</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $invoice_data = $conn->query("SELECT * FROM invoices ORDER BY id DESC LIMIT 8");
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
          <div class="col-sm d-md-none">
            <div class="card rounded-5 bordered">
              <div class="card-body">
                <h5 class="card-title" style="text-transform: none;text-decoration: none;">Faturat e fundit</h5>
                <a href="invoice.php" class="input-custom-css px-3 py-2 mb-3" style="text-decoration: none;">Kalo tek faturat</a>
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>Emri</th>
                        <th>Shuma e fatures</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $invoice_data = $conn->query("SELECT * FROM invoices ORDER BY id DESC LIMIT 8");
                      while ($row = $invoice_data->fetch_assoc()) {
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
              text: 'Shitje'
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