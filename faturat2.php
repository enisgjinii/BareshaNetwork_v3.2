<?php require_once 'partials/header.php';
require_once('conn-d.php');
if (isset($_POST["submit_file"])) {
  $file = $_FILES["file"]["tmp_name"];
  $file_open = fopen($file, "r");

  $batchSize = 100; // Number of rows to insert in each batch
  $rowCount = 0; // Counter for tracking the number of rows

  $query = "INSERT INTO platformat (ReportingPeriod, AccountingPeriod, Artist, rel, Track, UPC, ISRC, Partner, Country, Type, Units, RevenueUSD, RevenueShare, SplitPayShare) VALUES ";

  while (($csv = fgetcsv($file_open, 0, ",")) !== false) {
    $ReportingPeriod = str_replace("'", "\'", isset($csv[0]) ? $csv[0] : "");
    $AccountingPeriod = str_replace("'", "\'", isset($csv[1]) ? $csv[1] : "");
    $Artist = str_replace("'", "\'", isset($csv[2]) ? $csv[2] : "");
    $rel = str_replace("'", "\'", isset($csv[3]) ? $csv[3] : "");
    $Track = str_replace("'", "\'", isset($csv[4]) ? $csv[4] : "");
    $UPC = str_replace("'", "\'", isset($csv[5]) ? $csv[5] : "");
    $ISRC = str_replace("'", "\'", isset($csv[6]) ? $csv[6] : "");
    $Partner = str_replace("'", "\'", isset($csv[7]) ? $csv[7] : "");
    $Country = str_replace("'", "\'", isset($csv[8]) ? $csv[8] : "");
    $Type = str_replace("'", "\'", isset($csv[9]) ? $csv[9] : "");
    $Units = str_replace("'", "\'", isset($csv[10]) ? $csv[10] : "");
    $RevenueUSD = str_replace("'", "\'", isset($csv[11]) ? $csv[11] : "");
    $RevenueShare = str_replace("'", "\'", isset($csv[12]) ? $csv[12] : "");
    $SplitPayShare = str_replace("'", "\'", isset($csv[13]) ? $csv[13] : "");

    // Add the row values to the query
    $query .= "('$ReportingPeriod', '$AccountingPeriod', '$Artist', '$rel', '$Track', '$UPC', '$ISRC', '$Partner', '$Country', '$Type', '$Units', '$RevenueUSD', '$RevenueShare', '$SplitPayShare'),";

    $rowCount++;

    // If the batch size is reached, insert the batch and reset the query
    if ($rowCount % $batchSize === 0) {
      // Remove the trailing comma from the query
      $query = rtrim($query, ',');

      // Execute the batch insertion query
      $conn->query($query);

      // Reset the query for the next batch
      $query = "INSERT INTO platformat (ReportingPeriod, AccountingPeriod, Artist, rel, Track, UPC, ISRC, Partner, Country, Type, Units, RevenueUSD, RevenueShare, SplitPayShare) VALUES ";
    }
  }

  // Insert the remaining rows if the total row count is not divisible by the batch size
  if ($rowCount % $batchSize !== 0) {
    // Remove the trailing comma from the query
    $query = rtrim($query, ',');

    // Execute the final batch insertion query
    $conn->query($query);
  }
}


?>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="container">
        <!-- BreadCumber section -->
        <div class="p-5 mb-4 card rounded-5 shadow-sm">
          <h4 class="font-weight-bold text-gray-800 mb-4">Platformat tjera</h4>
          <nav class="d-flex">
            <h6 class="mb-0">
              <a href="" class="text-reset">Financat</a>
              <span>/</span>
              <a href="faturat2.php" class="text-reset" data-bs-placement="top" data-bs-toggle="tooltip" title="<?php echo __FILE__; ?>"><u>Platformat tjera</u></a>
              <br>
            </h6>
          </nav>
        </div>


        <!-- Hapsira per filtrimin e artistit dhe periodes -->
        <div class="p-5 shadow-sm rounded-5 mb-4 card">
          <div class="form-group row">
            <div class="col">
              <form method="POST">
                <label>Zgjedh artistin</label>
                <select name="artistii" id="artistii" class="form-select border shadow-2  text-dark " data-live-search="true" id="my-select">
                  <?php
                  $merrarti = $conn->query("SELECT DISTINCT Artist FROM platformat");
                  while ($merrart = mysqli_fetch_array($merrarti)) {
                  ?>
                    <option value="<?php echo $merrart['Artist']; ?>"><?php echo $merrart['Artist']; ?></option>
                  <?php
                  }
                  ?>
                </select>
            </div>
            <div class="col">
              <label>Zgjedh perioden</label>
              <!-- HTML code for the multi-select dropdown list -->
              <select name="perioda[]" id="perioda" class="form-select border shadow-2 text-dark w-100" data-live-search="true" multiple>
                <?php
                $merrarti = $conn->query("SELECT DISTINCT AccountingPeriod FROM platformat;");
                while ($merrart = mysqli_fetch_array($merrarti)) {
                ?>
                  <option value="<?php echo $merrart['AccountingPeriod']; ?>"><?php echo $merrart['AccountingPeriod']; ?></option>
                <?php
                }
                ?>
              </select>
            </div>
          </div>
          <!-- Butoni per filtrim -->
          <div class="col">
            <button type="submit" class="btn btn-sm btn-primary text-white shadow-sm rounded-5"><i class="fi fi-rr-filter"></i></button>
            </button>
          </div>
          </form>
        </div>


        <!-- Pills -->
        <div class="p-5 shadow-sm rounded-5 mb-4 card">
          <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active rounded-5 shadow-sm" id="pills-tabela-tab" data-bs-toggle="pill" data-bs-target="#pills-tabela" type="button" role="tab" aria-controls="pills-tabela" aria-selected="true" style="text-transform: none;">
                <i class="fi fi-rr-table-layout"></i> Tabela</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link rounded-5 shadow-sm" id="pills-ngarkoCSV-tab" data-bs-toggle="pill" data-bs-target="#pills-ngarkoCSV" type="button" role="tab" aria-controls="pills-ngarkoCSV" aria-selected="false" style="text-transform: none;">
                <i class="fi fi-rr-upload"></i> Ngarko CSV</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link rounded-5 shadow-sm" id="pills-raportiIBazuarNeShtete-tab" data-bs-toggle="pill" data-bs-target="#pills-raportiIBazuarNeShtete" type="button" role="tab" aria-controls="pills-raportiIBazuarNeShtete" aria-selected="false" style="text-transform: none;"><i class="fi fi-rr-flag"></i> Raporti i bazuar ne shtete ( Grafik&euml; )</button>
            </li>

            <li class="nav-item" role="presentation">
              <button class="nav-link rounded-5 shadow-sm" id="pills-raportiIBazuarNeShteteList-tab" data-bs-toggle="pill" data-bs-target="#pills-raportiIBazuarNeShteteList" type="button" role="tab" aria-controls="pills-raportiIBazuarNeShteteList" aria-selected="false" style="text-transform: none;"><i class="fi fi-rr-list-dropdown"></i> Raporti i bazuar ne shtete ( List&euml; )</button>
            </li>
          </ul>


          <div class="tab-content" id="pills-tabContent">

            <!-- Pills #1 | Start -->
            <div class="tab-pane fade show active" id="pills-tabela" role="tabpanel" aria-labelledby="pills-tabela-tab">
              <div class="card rounded-5 shadow-sm">
                <div class="card-body">
                  <h4 class="card-title">Platformat tjera</h4>
                  <div class="row">
                    <div class="col-12">
                      <div class="table-responsive">
                        <table id="example" data-ordering="false" class="table w-100 table-bordered">
                          <thead class="bg-light">
                            <tr>
                              <th>ID</th>
                              <th>Artist(s)</th>
                              <th>Reporting Period</th>
                              <th>Accounting Period</th>
                              <th>Release</th>
                              <th>Track</th>
                              <th>Country</th>
                              <th>Revenue (USD)</th>
                              <th>Revenue Share (%)</th>
                              <th>Split Pay Share (%)</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            if (isset($_POST['artistii']) && isset($_POST['perioda'])) {
                              $artistii = $_POST['artistii'];
                              $perioda = $_POST['perioda'];
                              $kueri = $conn->query("SELECT * FROM platformat WHERE Artist='$artistii' AND AccountingPeriod IN ('" . implode("', '", $perioda) . "')");
                              $q4 = $conn->query("SELECT SUM(`RevenueUSD`) as `sum` FROM `platformat` WHERE Artist='$artistii' AND AccountingPeriod IN ('" . implode("', '", $perioda) . "')");
                              $qq4 = mysqli_fetch_array($q4);
                              if (mysqli_num_rows($kueri) > 0) {
                                while ($k = mysqli_fetch_array($kueri)) {
                            ?>
                                  <tr>
                                    <td><?php echo $k['id']; ?></td>
                                    <td><?php echo $k['Artist']; ?></td>
                                    <td><?php echo $k['ReportingPeriod']; ?></td>
                                    <td><?php echo $k['AccountingPeriod']; ?></td>
                                    <td><?php echo $k['rel']; ?></td>
                                    <td><?php echo $k['Track']; ?></td>
                                    <td><?php echo $k['Country']; ?></td>
                                    <td><?php echo $k['RevenueUSD']; ?></td>
                                    <td><?php echo $k['RevenueShare']; ?></td>
                                    <td><?php echo $k['SplitPayShare']; ?></td>
                                  </tr>
                                <?php
                                }
                                ?>
                                <tr>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                                  <td><b>Totali:</b> <?php echo $qq4['sum']; ?></td>
                                  <td></td>
                                  <td></td>
                                </tr>
                            <?php
                              }
                            } else {
                              // handle empty result set
                            }
                            ?>
                          </tbody>
                          <tfoot class="bg-light">
                            <tr>
                              <th>Artist(s)</th>
                              <th>Reporting Period</th>
                              <th>Accounting Period</th>
                              <th>Release</th>
                              <th>Track</th>
                              <th>Country</th>
                              <th>Revenue (USD)</th>
                              <th>Revenue Share (%)</th>
                              <th>Split Pay Share (%)</th>
                            </tr>
                          </tfoot>
                        </table>
                        <?php
                        // Make an API request to get the exchange rate from USD to Euro
                        $apiUrl = 'https://api.exchangerate-api.com/v4/latest/USD'; // Replace with the appropriate API endpoint

                        $response = file_get_contents($apiUrl);
                        $data = json_decode($response, true);

                        // Check if the API request was successful
                        if ($data && isset($data['rates']['EUR'])) {
                          $exchangeRate = $data['rates']['EUR']; // Extract the exchange rate for USD to Euro
                          $totalUSD = $qq4['sum']; // Totali value in USD
                          $totalEuro = $totalUSD * $exchangeRate; // Convert the Totali value to Euro
                        } else {
                          // Handle the case where the API request failed or the exchange rate data is not available
                          $exchangeRate = null;
                          $totalUSD = null;
                          $totalEuro = null;
                        }
                        ?>

                        <?php if ($exchangeRate !== null && $totalUSD !== null && $totalEuro !== null) : ?>
                          <b class="shadow-sm rounded-5 p-3">Totali: <?php echo $totalUSD; ?> USD / <?php echo $totalEuro; ?> Euro</b>
                        <?php else : ?>
                          <b class="shadow-sm rounded-5 p-3">Unable to retrieve exchange rate</b>
                        <?php endif; ?>


                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Pills #1 | End -->

            <!-- Pills #2 | Start -->
            <div class="tab-pane fade" id="pills-ngarkoCSV" role="tabpanel" aria-labelledby="pills-ngarkoCSV-tab">
              <div class="row">
                <div class="col">
                  <div class="card rounded-5 shadow-sm p-3 my-3">
                    <form method="post" enctype="multipart/form-data" id="upload-form">
                      <input type="file" name="file" class="form-control shadow-sm rounded-5 w-25" id="file-input">
                      <br>
                      <input type="submit" name="submit_file" id="submit-file" class="btn btn-primary rounded-5 text-white" style="text-transform:none;" value="Dor&euml;zoje n&euml; baz&euml;n e t&euml; dh&euml;nave" />
                    </form>
                  </div>
                </div>
              </div>
            </div>
            <!-- Pills #2 | End -->
            <!-- Pills #3 | Start -->
            <div class="tab-pane fade" id="pills-raportiIBazuarNeShtete" role="tabpanel" aria-labelledby="pills-raportiIBazuarNeShtete-tab">


              <canvas id='myChart'></canvas>


              <?php


              if (isset($_POST['artistii']) && isset($_POST['perioda'])) {
                $artistii = $_POST['artistii'];
                $perioda = $_POST['perioda'];
                $kueri = $conn->query("SELECT * FROM platformat WHERE Artist='$artistii' AND AccountingPeriod IN ('" . implode("', '", $perioda) . "')");
                // $q4 = $conn->query("SELECT Country as `shtetet` FROM `platformat` WHERE Artist='$artistii' AND AccountingPeriod IN ('" . implode("', '", $perioda) . "')");
                $q4 = $conn->query("SELECT Country as `shtetet`, SUM(`RevenueUSD`) as `fitimi` FROM `platformat` WHERE Artist='$artistii' AND AccountingPeriod IN ('" . implode("', '", $perioda) . "') GROUP BY Country");


                $shuma = $conn->query("SELECT SUM(`RevenueUSD`) as `sum` FROM `platformat` WHERE Artist='$artistii' AND AccountingPeriod IN ('" . implode("', '", $perioda) . "')");

                $nxerrja_e_shumës = mysqli_fetch_array($shuma);



                $qq4 = mysqli_fetch_array($q4);


                $data = array();
                $labels = array();
                while ($row = $q4->fetch_assoc()) {
                  $data[] = $row['fitimi'];
                  $labels[] = $row['shtetet'];
                }
              } else {
                // Handle case where form data is not set
              }
              ?>
            </div>
            <!-- Pills #3 | End -->
            <!-- Pills #4 | Start -->
            <div class="tab-pane fade" id="pills-raportiIBazuarNeShteteList" role="tabpanel" aria-labelledby="pills-raportiIBazuarNeShteteList-tab">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead class="bg-light">
                    <tr>
                      <th>Country</th>
                      <th>Accounting Period</th>
                      <th>Revenue</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $query = "SELECT Country, AccountingPeriod, SUM(RevenueUSD) as total_revenue
              FROM platformat
              WHERE Artist='$artistii' AND AccountingPeriod IN ('" . implode("', '", $perioda) . "')
              GROUP BY Country, AccountingPeriod";
                    $result = mysqli_query($conn, $query);
                    $prev_country = "";
                    $total_revenue = 0;
                    while ($row = mysqli_fetch_array($result)) {
                      if ($row['Country'] != $prev_country) {

                        // Start a new row for the current country
                        echo "<tr>";
                        echo "<td>" . $row['Country'] . "</td>";
                        $prev_country = $row['Country'];
                        $total_revenue = 0;
                      }
                      // Display the revenue value for the current accounting period
                      echo "<td>" . $row['AccountingPeriod'] . "</td>";
                      echo "<td>$" . number_format($row['total_revenue'], 10) . "</td>";
                      $total_revenue += $row['total_revenue'];
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
            <!-- Pills #4 | End -->

          </div>

        </div>





      </div>
    </div>
  </div>
</div>
</div>

<?php require_once 'partials/footer.php'; ?>



<script>
  var ctx = document.getElementById('myChart');
  var myChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: <?php echo json_encode($labels); ?>,
      datasets: [{
        label: 'Fitimi i bazuar ne shtetet (<?php echo $perioda[0]; ?> - <?php echo end($perioda); ?>)',
        data: <?php echo json_encode($data); ?>,
        backgroundColor: 'rgba(255, 99, 132, 0.2)',
        borderColor: 'rgba(255, 99, 132, 1)',
        borderWidth: 1,
        fill: true,
      }]
    },
    options: {
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true
          }
        }]
      }
    }
  });
</script>

<script>
  var table = $('#example').DataTable({
    responsive: true,
    search: {
      return: true,
    },
    dom: 'Bfrtip',
    buttons: [
      // {
      //   text: '<i class="fi fi-rr-upload fa-lg"></i>&nbsp;&nbsp; Ngarko CSV',
      //   className: 'btn btn-light border shadow-2 me-2',
      //   action: function(e, node, config) {
      //     $('#shtochannel').modal('show')
      //   }
      // }, 

      {
        extend: 'pdfHtml5',
        text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
        titleAttr: 'Eksporto tabelen ne formatin PDF',
        className: 'btn btn-light border shadow-2 me-2'
      }, {
        extend: 'copyHtml5',
        text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
        titleAttr: 'Kopjo tabelen ne formatin Clipboard',
        className: 'btn btn-light border shadow-2 me-2'
      }, {
        extend: 'excelHtml5',
        text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
        titleAttr: 'Eksporto tabelen ne formatin CSV',
        className: 'btn btn-light border shadow-2 me-2',

      }, {
        extend: 'print',
        text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
        titleAttr: 'Printo tabel&euml;n',
        className: 'btn btn-light border shadow-2 me-2'
      },
      // {
      //   text: 'Send Data',
      //   action: function(e, dt, node, config) {
      //     var data = dt.rows().data().toArray();
      //     var jsonData = JSON.stringify(data);

      //     // Create a new form to submit the data
      //     var form = document.createElement('form');
      //     form.setAttribute('method', 'POST');
      //     form.setAttribute('action', 'fatura-specifike-per-artist.php');
      //     form.setAttribute('target', '_blank');

      //     // Add a hidden input field containing the JSON data
      //     var input = document.createElement('input');
      //     input.setAttribute('type', 'hidden');
      //     input.setAttribute('name', 'data');
      //     input.setAttribute('value', jsonData);
      //     form.appendChild(input);

      //     // Submit the form
      //     document.body.appendChild(form);
      //     form.submit();
      //   },
      //   className: 'btn btn-primary'
      // }


    ],
    initComplete: function() {
      var btns = $('.dt-buttons');
      btns.addClass('');
      btns.removeClass('dt-buttons btn-group');

    },
    fixedHeader: true,
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
    },

  });
</script>

<script>
  const apiKey = "WVrG8fyiCIO6ZoNxxVG9zdkJlEVVlAVj";

  const form = document.querySelector('#currencyForm');
  const result = document.getElementById('result');

  form.addEventListener('submit', (event) => {
    event.preventDefault();

    const fromCurrency = document.getElementById('fromCurrency').value;
    const toCurrency = document.getElementById('toCurrency').value;
    const amount = document.getElementById('amount').value;

    fetch(`https://api.apilayer.com/fixer/convert?to=${toCurrency}&from=${fromCurrency}&amount=${amount}&apikey=${apiKey}`)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const convertedAmount = Math.round(data.result);
          result.textContent = `${amount} ${fromCurrency} is equal to ${convertedAmount} ${toCurrency}`;
        } else {
          result.textContent = "Ju nuk keni specifikuar nj&euml; shum&euml; p&euml;r t'u konvertuar. [Shembull: Shuma=5]";
        }
      })
      .catch(error => console.log('error', error));
  });
</script>

<script>
  // Get references to the two selects
  const artistiiSelect = document.getElementById('artistii');
  const periodaSelect = document.getElementById('perioda');

  // Define a function to fetch the options for the second select
  function updatePeriodaOptions() {
    // Get the value of the first select
    const artistii = artistiiSelect.value;

    // Fetch the options for the second select based on the value of the first select
    // You can use AJAX to make a request to the server and fetch the options dynamically
    // For simplicity, this example uses PHP to fetch the options from the server and embed them in the page
    const url = 'fetch-perioda-options-2.php?artistii=' + encodeURIComponent(artistii);
    fetch(url)
      .then(response => response.text())
      .then(optionsHtml => {
        periodaSelect.innerHTML = optionsHtml;
      })
      .catch(error => console.error(error));
  }

  // Attach the function to the change event of the first select
  artistiiSelect.addEventListener('change', updatePeriodaOptions);

  // Initialize the options for the second select based on the initial value of the first select
  updatePeriodaOptions();
</script>