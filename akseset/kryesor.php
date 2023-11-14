<?php include "./partials/sales.php";
?><style>
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
        <div class="row my-1">
          <div class="col-12 col-xl-5 mb-4 mb-xl-0">
            <h4 class="font-weight-bold">P&euml;rshendetje! <?php echo $user_info['givenName'] . ' ' . $user_info['familyName']; ?>
            </h4>
          </div>
        </div>
        <div class="row">
          <div class="col-md-3 grid-margin stretch-card">
            <div class="card rounded-5 shadow-sm">
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
          </div>
          <div class="col-md-3 grid-margin stretch-card">
            <div class="card rounded-5 shadow-sm">
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
          </div>
          <div class="col-md-3 grid-margin stretch-card">
            <div class="card rounded-5 shadow-sm">
              <div class="card-body">
                <p class="fw-bold text-md-left text-xl-left">Paga bruto e punonj&euml;sve</p>
                <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                  <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0">
                    <?php echo $summ7['sum']; ?>&euro;
                  </h3>
                  <i class="ti-calendar icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                </div>

              </div>
            </div>
          </div>
          <div class="col-md-3 grid-margin stretch-card">
            <div class="card rounded-5 shadow-sm">
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
        </div>

        <div class="row">
          <div class="col-md-3 grid-margin stretch-card">
            <div class="card rounded-5 shadow-sm">
              <div class="card-body">
                <p class="fw-bold text-md-left text-xl-left">Numri i takimeve</p>
                <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                  <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0">
                    <?php echo $takimet2; ?>
                  </h3>
                  <i class="ti-calendar icon-md text-muted mb-0 mb-xl-0"></i>
                </div>

              </div>
            </div>
          </div>
          <div class="col-md-3 grid-margin stretch-card">
            <div class="card rounded-5 shadow-sm">
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
          <div class="col-md-3 grid-margin stretch-card">
            <div class="card rounded-5 shadow-sm">
              <div class="card-body">
                <p class="fw-bold text-md-left text-xl-left">Takimet e realizuara</p>
                <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                  <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0">
                    <?php echo $tm2; ?>
                  </h3>
                  <i class="ti-agenda icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3 grid-margin stretch-card">
            <div class="card rounded-5 shadow-sm">
              <div class="card-body">
                <p class="fw-bold text-md-left text-xl-left">Takimet e pa realizuara</p>
                <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                  <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0">
                    <?php echo $tp2; ?>
                  </h3>
                  <i class="ti-layers-alt icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i>
                </div>
              </div>
            </div>
          </div>
        </div>






        <div class="row my-2">
          <div class="col-8">
            <div class="card rounded-5 shadow-sm ">
              <div class="card-body">
                <div id="monthlyChart"></div>
              </div>
            </div>
          </div>
          <div class="col-4">
            <div class="card rounded-5 shadow-sm">
              <div class="card-body">
                <div id="yearlyChart"></div>
              </div>
            </div>
          </div>
        </div>






        <?php
        $max = max($janarRezultatiShitjeve['sum'], $shkurtRezultatiShitjeve['sum'], $marsRezultatiShitjeve['sum'], $prillRezultatiShitjeve['sum']);
        $min = min($janarRezultatiShitjeve['sum'], $shkurtRezultatiShitjeve['sum'], $marsRezultatiShitjeve['sum'], $prillRezultatiShitjeve['sum']);
        $dd = strtotime("-6 Months");
        $ggdata = date("Y-m-d", $dd);

        $mp6 = $conn->query("SELECT SUM(klientit) AS sum FROM shitje WHERE data >= '$ggdata' AND data <= '$dataAktuale'");
        $m6 = mysqli_fetch_array($mp6);
        ?>

        <?php

        $api_key = "AIzaSyBrE0kFGTQJwn36FeR4NIyf4FEw2HqSSIQ";
        $apiu = file_get_contents('https://www.googleapis.com/youtube/v3/channels?part=snippet&id=UCV6ZBT0ZUfNbtZMbsy-L3CQ&key=' . $api_key);
        $apid = json_decode($apiu, true);

        $aa = file_get_contents('https://www.googleapis.com/youtube/v3/channels?part=statistics&id=UCV6ZBT0ZUfNbtZMbsy-L3CQ&key=' . $api_key);
        $aaa = json_decode($aa, true);
        ?>
        <div class="row">
          <div class="col-md-12 grid-margin">
            <div class="card rounded-5 shadow-sm bg-primary border-0 position-relative">
              <div class="card-body">
                <p class="fw-bold text-white">Baresha Overview</p>
                <div id="performanceOverview" class="carousel slide performance-overview-carousel position-static pt-2" data-bs-ride="carousel">
                  <div class="carousel-inner">
                    <div class="carousel-item active">
                      <div class="row">
                        <div class="col-md-4 item">
                          <div class="d-flex flex-column flex-xl-row mt-4 mt-md-0">
                            <div class="icon icon-a text-white me-3">
                              <i class="ti-cup icon-lg ms-3"></i>
                            </div>
                            <div class="content text-white">
                              <div class="d-flex flex-wrap align-items-center mb-2 mt-3 mt-xl-1">
                                <h3 class="font-weight-light me-2 mb-1">Abonues
                                  <?php echo number_format($aaa['items'][0]['statistics']['subscriberCount'], 2, '.', ','); ?>
                                </h3>
                              </div>

                              <p class="text-white font-weight-light pr-lg-2 pr-xl-5">Numri total i abonues&euml;ve
                                n&euml; kanalin
                                <?php echo $apid['items'][0]['snippet']['title']; ?> &euml;sht&euml;
                                <?php echo number_format($aaa['items'][0]['statistics']['subscriberCount'], 2, '.', ','); ?>
                              </p>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4 item">
                          <div class="d-flex flex-column flex-xl-row mt-5 mt-md-0">
                            <div class="icon icon-b text-white me-3">
                              <i class="ti-bar-chart icon-lg ms-3"></i>
                            </div>
                            <div class="content text-white">
                              <div class="d-flex flex-wrap align-items-center mb-2 mt-3 mt-xl-1">
                                <h3 class="font-weight-light me-2 mb-1">Shikime</h3>
                                <h3 class="mb-0">
                                  <?php echo number_format($aaa['items'][0]['statistics']['viewCount'], 2, '.', ','); ?>
                                </h3>
                              </div>
                              <p class="text-white font-weight-light pr-lg-2 pr-xl-5">Numri total i shikimeve n&euml;
                                kanalin
                                <?php echo $apid['items'][0]['snippet']['title']; ?> &euml;sht&euml;
                                <?php echo number_format($aaa['items'][0]['statistics']['viewCount'], 2, '.', ','); ?>
                              </p>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4 item">
                          <div class="d-flex flex-column flex-xl-row mt-5 mt-md-0">
                            <div class="icon icon-c text-white me-3">
                              <i class="ti-shopping-cart-full icon-lg ms-3"></i>
                            </div>
                            <div class="content text-white">
                              <div class="d-flex flex-wrap align-items-center mb-2 mt-3 mt-xl-1">
                                <h3 class="font-weight-light me-2 mb-1">Ngarkime</h3>
                                <h3 class="mb-0">
                                  <?php echo $aaa['items'][0]['statistics']['videoCount']; ?>
                                </h3>
                              </div>

                              <p class="text-white font-weight-light pr-lg-2 pr-xl-5">Numri total i ngarkimeve n&euml;
                                kanalin
                                <?php echo $apid['items'][0]['snippet']['title']; ?> &euml;sht&euml;
                                <?php echo $aaa['items'][0]['statistics']['videoCount']; ?>
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12 grid-margin stretch-card">
            <div class="card rounded-5 shadow-sm">
              <div class="card-body"><!-- Add a dropdown select element for selecting the year -->
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
        </div>
        <div class="row">
          <div class="col-md-12 grid-margin stretch-card">
            <div class="card rounded-5 shadow-sm">
              <div class="card-body">
                <p class="fw-bold mb-3">20 p&euml;rdoruesit m&euml; t&euml; mir&euml; me shumic&euml;n e abonent&euml;ve</p>
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
                            </b></td>
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
        <div class="row">
          <div class="col-md-4 stretch-card grid-margin grid-margin-md-0">
            <div class="card rounded-5 shadow-sm">
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
                          <a href="<?php echo $row['linku']; ?>"> <?php echo $row['emri']; ?></a>
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
                <div class="card rounded-5 shadow-sm">
                  <div class="card-body">
                    <p class="fw-bold">Pamja vizuale e klienteve te monetizuar dhe te pamonetizuar</p>
                    <br>
                    <canvas id="charts"></canvas>
                  </div>
                </div>
              </div>
              <div class="col-md-12 stretch-card grid-margin grid-margin-md-0">
                <div class="card rounded-5 shadow-sm">
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
            <div class="card rounded-5 shadow-sm">
              <div class="card-body">
                <p class="fw-bold">Regjistri i aktiviteteve</p>

                <div class="row">
                  <div class="col-md-12">
                    <?php
                    $merri = $conn->query("SELECT * FROM logs ORDER BY id DESC LIMIT 5");
                    while ($k = mysqli_fetch_array($merri)) {
                    ?>
                      <div class="card rounded-5 shadow-sm mb-3">
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
        </div>
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
  const chartsecond = new Highcharts.Chart({
    chart: {
      renderTo: 'monthlyChart', // Use the ID of the div where you want to render the chart
      type: 'column' // Use 'column' for basic column chart
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
      enabled: false
    },

    plotOptions: {
      column: {
        color: '#0070C0' // Blue color
      }
    },
    series: [{
      name: 'Sales',
      data: monthValues
    }]
  });
</script>