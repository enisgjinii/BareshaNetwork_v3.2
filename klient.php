<?php include 'partials/header.php';
if (isset($_GET['aktivizo'])) {
  $actid = mysqli_real_escape_string($conn, $_GET['aktivizo']);
  $sasa = mysqli_real_escape_string($conn, $_GET['s']);
  $conn->query("UPDATE klientet SET glist='$sasa' WHERE id='$actid'");
}
if (isset($_POST['ruaj'])) {
  $emri = $_POST['emri'];
  if (empty($_POST['min'])) {
    $mon = "Jo";
  } else {
    $mon = $_POST['min'];
  }
  $dk = mysqli_real_escape_string($conn, $_POST['dk']);
  $dks = mysqli_real_escape_string($conn, $_POST['dks']);
  $yt = mysqli_real_escape_string($conn, $_POST['yt']);
  $info = mysqli_real_escape_string($conn, $_POST['info']);
  $perq = mysqli_real_escape_string($conn, $_POST['perqindja']);
  $ads = mysqli_real_escape_string($conn, $_POST['ads']);
  $targetfolder = "dokument/";
  $targetfolder = $targetfolder . basename($_FILES['tipi']['name']);
  $ok = 1;
  $file_type = $_FILES['tipi']['type'];
  if ($file_type == "application/pdf") {
    if (move_uploaded_file($_FILES['tipi']['tmp_name'], $targetfolder)) {
    } else {
    }
  } else {
  }
  $problemet = mysqli_real_escape_string($conn, $_POST['problemet']);
  $platformat = mysqli_real_escape_string($conn, $_POST['platformat']);
  $cdata = date("Y-m-d H:i:s");
  $cname = $_SESSION['emri'];
  $cnd = $cname . " ka Regjistruar Klientin " . $emri;
  $query = "INSERT INTO logs (stafi, ndryshimi, koha) VALUES ('$cname', '$cnd', '$cdata')";
  if ($conn->query($query)) {
  } else {
    echo '<script>alert("' . $conn->error . '")</script>';
  }
  if ($conn->query("INSERT INTO klientet (emri, monetizuar, dk, dks, youtube, info, perqindja, kontrata, platformat, problemet, ads) VALUES ('$emri', '$mon', '$dk', '$dks', '$yt', '$info', '$perq', '$targetfolder', '$platformat', '$problemet', '$ads')")) {
  }
}
if (isset($_POST['idup'])) {
  $idup = mysqli_real_escape_string($conn, $_POST['idup']);
  $password = mysqli_real_escape_string($conn, $_POST['fjalkalim']);
  $password = md5($password);
  if ($conn->query("UPDATE klientet SET fjalkalimi='$password' WHERE id='$idup'")) {
    echo "<script>alert('Fjalkalimi u perditsua me sukses.')</script>";
  }
}
if (isset($_GET['blocked'])) {
  $blockid = $_GET['blocked'];
  $block = $_GET['block'];
  $conn->query("UPDATE klientet SET blocked='$block' WHERE id='$blockid'");
}
?>
<?php
if (isset($_POST['ruaj'])) {
  $emri = $_POST['emri'];
  if (empty($_POST['min'])) {
    $mon = "JO";
  } else {
    $mon = $_POST['min'];
  }
  $dk = mysqli_real_escape_string($conn, $_POST['dk']);
  $np = mysqli_real_escape_string($conn, $_POST['np']);
  $dks = mysqli_real_escape_string($conn, $_POST['dks']);
  $yt = mysqli_real_escape_string($conn, $_POST['yt']);
  $info = mysqli_real_escape_string($conn, $_POST['info']);
  $perq = mysqli_real_escape_string($conn, $_POST['perqindja']);
  $perq2 = mysqli_real_escape_string($conn, $_POST['perqindja2']);
  $ads = mysqli_real_escape_string($conn, $_POST['ads']);
  $fb = mysqli_real_escape_string($conn, $_POST['fb']);
  $ig = mysqli_real_escape_string($conn, $_POST['ig']);
  $adresa = mysqli_real_escape_string($conn, $_POST['adresa']);
  $kategoria = mysqli_real_escape_string($conn, $_POST['kategoria']);
  $nrtel = mysqli_real_escape_string($conn, $_POST['nrtel']);
  $emailadd = mysqli_real_escape_string($conn, $_POST['emailadd']);
  $emailp = mysqli_real_escape_string($conn, $_post['emailp']);
  $emriart = mysqli_real_escape_string($conn, $_POST['emriart']);
  $nrllog = mysqli_real_escape_string($conn, $_POST['nrllog']);
  $perdoruesi = mysqli_real_escape_string($conn, $_POST['perdoruesi']);
  $password = mysqli_real_escape_string($conn, $_POST["password"]);
  $emails = implode(', ', $_POST['emails']);
  $password = md5($password);
  $targetfolder = "dokument/";
  $targetfolder = $targetfolder . basename($_FILES['tipi']['name']);
  $ok = 1;
  $file_type = $_FILES['tipi']['type'];
  if ($file_type == "application/pdf") {
    if (move_uploaded_file($_FILES['tipi']['tmp_name'], $targetfolder)) {
    } else {
    }
  } else {
  }
  if ($conn->query("INSERT INTO klientet (emri, np, monetizuar, dk, dks, youtube, info, perqindja, perqindja2, kontrata, ads, fb, ig, adresa, kategoria, nrtel, emailadd, emailp, emriart, nrllog, fjalkalimi, perdoruesi, emails, blocked) VALUES ('$emri', '$np','$mon', '$dk', '$dks', '$yt', '$info', '$perq', '$perq2', '$targetfolder', '$ads', '$fb', '$ig', '$adresa', '$kategoria', '$nrtel', '$emailadd', '$emailp', '$emriart', '$nrllog', '$password', '$perdoruesi', '$emails', '0')")) {
    $cdata = date("Y-m-d H:i:s");
    $cname = $_SESSION['emri'];
    $cnd = $cname . " ka shtuar  klientin " . $emri;
    $query = "INSERT INTO logs (stafi, ndryshimi, koha) VALUES ('$cname', '$cnd', '$cdata')";
    if ($conn->query($query)) {
    } else {
      echo '<script>alert("' . $conn->error . '")</script>';
    }
    echo '<script>alert("Kengetari u shtua me sukses");</script>';
  }
}
?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<style>
  tr a .active {
    background-color: green;
  }

  tr a .inactive {
    background-color: yellow;
  }
</style>
<!-- DataTales Example -->
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <nav class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Klientët</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">
            <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">
              Lista e klientëve
            </a>
          </li>
      </nav>
      <div class="row mb-3">
        <div>
          <a style="text-transform: none;text-decoration:none;" class="input-custom-css px-3 py-2" href="shtok.php"><i class="fi fi-rr-add"></i>
            &nbsp;
            Shto klientë
          </a>
        </div>
      </div>
      <div class="row text-center mb-3">
        <div class="modal fade" id="modal_of_monetized" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Lista e klient&euml;ve te monetizuar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body text-start">
                <div class="table-responsive">
                  <table class="table table-bordered" width="100%" id="monetized_clients">
                    <thead class="bg-light">
                      <tr>
                        <th>Emri dhe mbiemri</th>
                        <th>Statusi</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php
        $total_client_query = $conn->query("SELECT COUNT(id) FROM klientet");
        $total_result =  $total_client_query->fetch_assoc();
        $total_result["COUNT(id)"];
        $monetized_query = $conn->query("SELECT COUNT(monetizuar) FROM klientet WHERE monetizuar = 'PO'");
        $monetized_result = $monetized_query->fetch_assoc();
        $monetized_result["COUNT(monetizuar)"];
        $non_monetized_query = $conn->query("SELECT COUNT(monetizuar) FROM klientet WHERE monetizuar = 'JO'");
        $non_monetized_result = $non_monetized_query->fetch_assoc();
        $non_monetized_result["COUNT(monetizuar)"];
        ?>
        <div class="col-8">
          <div class="card p-2 rounded-5 shadow-sm">
            <!-- Add a container for the ApexCharts chart -->
            <div id="chart-container"></div>
          </div>
        </div>
        <div class="col-4">
          <div class="accordion" id="accordionExample">
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                  Të monetizuar - <?php echo $monetized_result["COUNT(monetizuar)"]; ?> klientë
                </button>
              </h2>
              <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                  <button type="button" class="input-custom-css px-3 py-2" style="text-transform: none" data-bs-toggle="modal" data-bs-target="#modal_of_monetized">
                    Shiko list&euml;n
                  </button>
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                  Të pa-monetizuar - <?php echo $non_monetized_result["COUNT(monetizuar)"]; ?> klientë
                </button>
              </h2>
              <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                  <button type="button" class="input-custom-css px-3 py-2" style="text-transform: none" data-bs-toggle="modal" data-bs-target="#modal_of_non_monetized">
                    Shiko list&euml;n
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <script>
          // JavaScript function to render the ApexCharts chart
          function renderChart(totalClients, monetizedCount, nonMonetizedCount) {
            var options = {
              chart: {
                type: 'bar',
                height: 350,
              },
              series: [{
                  name: "Numri total i klientëve",
                  data: [totalClients]
                },
                {
                  name: 'Numri total i klientëve të monetizuar',
                  data: [monetizedCount],
                }, {
                  name: 'Numri total i klientëve të pa-monetizuar',
                  data: [nonMonetizedCount],
                }
              ],
              xaxis: {
                categories: ['Të dhënat e përmbledhura për klientët'],
              },
            };
            var chart = new ApexCharts(document.querySelector("#chart-container"), options);
            chart.render();
            // Event listener for clicking on the Monetized series
            chart.addEventListener("dataPointClick", function(event, chartContext, config) {
              if (config.seriesIndex === 0) { // Monetized series
                // Open the modal for monetized clients
                $('#modal_of_monetized').modal('show');
              }
            });
            // Event listener for clicking on the Non-Monetized series
            chart.addEventListener("dataPointClick", function(event, chartContext, config) {
              if (config.seriesIndex === 1) { // Non-Monetized series
                // Open the modal for non-monetized clients
                $('#modal_of_non_monetized').modal('show');
              }
            });
          }
          // Call the renderChart function with the counts
          renderChart(<?php echo $total_result["COUNT(id)"] ?>, <?php echo $monetized_result["COUNT(monetizuar)"]; ?>, <?php echo $non_monetized_result["COUNT(monetizuar)"]; ?>);
        </script>
        <div class="modal fade" id="modal_of_non_monetized" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Lista e klient&euml;ve te pa-monetizuar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body text-start">
                <div class="table-responsive">
                  <table class="table table-bordered" width="100%" id="non_monetized_clients">
                    <thead class="bg-light">
                      <tr>
                        <th>Emri dhe mbiemri</th>
                        <th>Statusi</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card rounded-5 shadow-sm">
        <div class="card-body">
          <div class="row">
            <div class="table-responsive">
              <table id="example" class="table">
                <thead class="bg-light">
                  <tr>
                    <th>Emri & Mbiemri</th>
                    <th>Emri artistik</th>
                    <th>Adresa e email-it</th>
                    <th>Datat e kontrates ( Fillim - Skadim )</th>
                    <th>Data e kontrates ( Versioni i ri )</th>
                    <th>Data e skadimit ( Versioni i ri )</th>
                    <th>Veprim</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <!-- <div class="card p-5 my-3">
        <canvas id="client-chart"></canvas>
      </div> -->
    </div>
  </div>
</div>
</div>
<div class="modal fade" id="shtoKlient" tabindex="-1" aria-labelledby="shtoKlient" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="shtoKlient">Formulari p&euml;r t&euml; shtuar nj&euml; klient t&euml; ri</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="" enctype="multipart/form-data">
          <div class="form-group row">
            <div class="col">
              <label for="emri">Emri & Mbiemri</label>
              <input type="text" name="emri" id="emri" class="form-control" placeholder="Shkruaj Emrin Mbiemrin">
            </div>
            <div class="col">
              <label for="emri">Emri artistik</label>
              <input type="text" name="emriart" id="emriart" class="form-control" placeholder="Emri artistik">
            </div>
          </div>
          <div class="form-group row">
            <div class="col">
              <label for="emri">ID Dokumentit</label>
              <input type="text" name="np" id="emriart" class="form-control" placeholder="ID Dokumentit">
            </div>
            <div class="col">
              <label for="dk">Data e Kontrates</label>
              <input type="text" name="dk" id="dk" class="form-control" placeholder="Shkruaj Daten e kontrates" autocomplete="off">
            </div>
          </div>
          <div class="form-group row">
            <div class="col">
              <label for="dks">Data e Skadimit <small>(Kontrates)</small></label>
              <input type="text" name="dks" id="dks" class="form-control" placeholder="Shkruaj Daten e skaditimit" autocomplete="off">
            </div>
            <div class="col">
              <label for="yt">Shkruaj ID e kanalit t&euml; YouTube</label>
              <input type="text" name="yt" id="yt" class="form-control" placeholder="Youtube Channel ID" autocomplete="off">
            </div>
          </div>
          <div class="form-group row">
            <div class="col">
              <label for="yt">Kategoria</label>
              <select class="form-select" name="kategoria" id="kategoria">
                <?php
                $kg = $conn->query("SELECT * FROM kategorit");
                while ($kgg = mysqli_fetch_array($kg)) {
                  echo '<option value="' . $kgg['kategorit'] . '">' . $kgg['kategorit'] . '</option>';
                }
                ?>
              </select>
            </div>
            <div class="col">
              <label for="yt">Adresa</label>
              <input type="text" name="adresa" id="adresa" class="form-control" placeholder="Adresa" autocomplete="off">
            </div>
          </div>
          <div class="form-group row">
            <div class="col">
              <label for="yt">Nr.Tel</label>
              <input type="text" name="nrtel" id="nrtel" class="form-control" placeholder="Nr.Tel" autocomplete="off">
            </div>
            <div class="col">
              <label for="yt">Nr. Xhirollogaris</label>
              <input type="text" name="nrllog" id="nrllog" class="form-control" placeholder="Nr. Xhirollogaris" autocomplete="off">
            </div>
          </div>
          <div class="form-group row">
            <div class="col">
              <label for="yt">Email Adresa</label>
              <input type="text" name="emailadd" id="emailadd" class="form-control" placeholder="Email Adresa" autocomplete="off">
            </div>
            <div class="col">
              <label for="yt">Email Adresa per platforma</label>
              <input type="text" name="emailp" id="emailp" class="form-control" placeholder="Email Adresa per platforma" autocomplete="off">
            </div>
          </div>
          <div class="form-group row">
            <div class="col">
              <label>P&euml;rdoruesi <small>(Sistemit)</small>:</label>
              <input type="text" name="perdoruesi" class="form-control" placeholder="P&euml;rdoruesi i sistemit">
            </div>
            <div class="col">
              <label>Fjalkalimi <small>(Sistemit)</small>:</label>
              <input type="text" name="password" class="form-control" placeholder="Fjalkalimi i sistemit">
            </div>
          </div>
          <div class="form-group row">
            <div class="col">
              <label for="tel">Monetizuar ? </label><br>
              <input type="radio" id="html" name="min" value="PO" class="form-check-input">
              <label for="html" style="color:green;">PO</label>
              <input type="radio" id="css" name="min" value="JO" class="form-check-input">
              <label for="css" style="color:red;">JO</label><br>
            </div>
            <div class="col">
              <label>Zgjidhni kategorin</label>
              <select class="form-select" name="ads" id="js-example-basic-single w-100">
                <option value="">Zgjidhni Llogarin&euml;</option>
                <?php
                $mads = $conn->query("SELECT * FROM ads");
                while ($ads = mysqli_fetch_array($mads)) {
                ?>
                  <option value="<?php echo $ads['id']; ?>"><?php echo $ads['email']; ?> | <?php echo $ads['adsid']; ?>
                    (<?php echo $ads['shteti']; ?>)</option>
                <?php } ?>
              </select>
            </div>
          </div><br>
          <div class="form-group row">
            <div class="col">
              <label for="imei">Ngarko kontrat&euml;n:</label>
              <div class="file-upload-wrapper">
                <input type="file" name="tipi" class="fileuploader" />
              </div>
            </div>
            <div class="col">
              <label for="imei">Perqindja:</label>
              <input type="text" name="perqindja" class="form-control" placeholder="0.00%">
            </div>
            <div class="col">
              <label for="imei">Perqindja platformat tjera:</label>
              <input type="text" name="perqindja2" class="form-control" placeholder="0.00%">
            </div>
          </div>
          <div class="form-group row">
            <div class="col">
              <label><i class="ti-facebook"></i> Facebook URL:</label>
              <input type="URL" name="fb" class="form-control" placeholder="https://facebook.com/....">
            </div>
            <div class="col">
              <label><i class="ti-instagram"></i> Instagram URL:</label>
              <input type="URL" name="ig" class="form-control" placeholder="https://instagram.com/....">
            </div>
          </div>
          <div class="form-group row">
            <div class="col">
              <label for="imei">Email qe kan akses <small>(Mbaj shtypur CTRL)</small> </label>
              <select multiple class="form-control" name="emails[]" id="exampleFormControlSelect2">
                <?php
                $getemails = $conn->query("SELECT * FROM emails");
                while ($maillist = mysqli_fetch_array($getemails)) {
                ?>
                  <option value="<?php echo $maillist['email']; ?>"><?php echo $maillist['email']; ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="col">
              <label for="info"> Info Shtes&euml;</label>
              <textarea id="simpleMde" name="info" placeholder="Info Shtes&euml;"></textarea>
            </div>
          </div>
      </div>
      <br>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary" name="ruaj">Ruaj</button>
      </div>
      </form>
    </div>
  </div>
</div>
<?php include 'partials/footer.php'; ?>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    // Fetch the client count data from your PHP script using AJAX
    fetch("get-client-count.php")
      .then((response) => response.json())
      .then((data) => {
        // Extract the client count value from the response
        const clientCount = data.count;
        // Create a chart using ApexCharts
        const options = {
          chart: {
            type: "bar", // Change the chart type to "bar" for a bar chart
          },
          xaxis: {
            categories: ["Totali i klientëve"],
          },
          plotOptions: {
            bar: {
              horizontal: false, // Set to true for horizontal bars, false for vertical bars
            },
          },
          series: [{
            name: "Client Count",
            data: [clientCount],
          }, ],
        };
        const chart = new ApexCharts(
          document.querySelector("#clientCountChart"),
          options
        );
        chart.render();
      })
      .catch((error) => {
        console.error("Error fetching data: ", error);
      });
  });
</script>
<script>
  $('#example').DataTable({
    ordering: false,
    searching: true,
    processing: true,
    serverSide: true,
    lengthMenu: [
      [10, 25, 50, 100, 500, 1000],
      [10, 25, 50, 100, 500, 1000],
    ],
    ajax: {
      url: 'get-clients.php', // Replace with your server-side script URL
      type: 'GET',
    },
    columns: [{
        data: 'emri',
        render: function(data, type, row) {
          if (row.monetizuar == 'PO') {
            return '<p>' + data + '</p>' +
              '<span class="text-success">Klient i monetizuar </span>';
          } else {
            return '<p>' + data + '</p>' + '<span class="text-danger rounded-5">Klient i pa-monetizuar </span>';
          }
        }
      }, {
        data: 'emriart'
      },
      {
        data: 'emailadd'
      },
      {
        data: null,
        render: function(data, type, row) {
          return row.dk + ' - ' + row.dks;
        }
      },
      {
        data: 'data_e_krijimit',
        render: function(data, type, row) {
          // Set Albanian locale for moment.js
          moment.locale('sq');
          if (!data) {
            return '<span class="text-danger">Nuk u gjet asnjë datë.</span>';
          } else {
            var contractStartDate = moment(data); // Assuming data_e_krijimit is the contract start date
            if (!contractStartDate.isValid()) {
              return '<span class="text-danger">Data e fillimit të kontratës e pavlefshme</span>';
            }
            // Format the contract creation date in Albanian
            var creationDateFormatted = contractStartDate.format('dddd, D MMMM YYYY');
            return '<span>' + creationDateFormatted + '</span>';
          }
        }
      },
      {
        data: 'kohezgjatja',
        render: function(data, type, row) {
          // Check if the contract duration is null or empty
          if (data == null || data === '') {
            return '<span class="text-danger">Nuk u gjet asnjë datë.</span>'; // Handle null or empty values
          } else {
            var months = parseInt(data);
            if (isNaN(months) || months <= 0) {
              return '<span class="text-danger">Data e skadimit jo-valide</span>'; // Handle invalid values
            }
            var years = Math.floor(months / 12);
            var remainingMonths = months % 12;
            var durationHTML = '';
            if (years === 0) {
              // If less than a year, display only months
              durationHTML = '<p>' + data + ' Muaj</p>';
            } else if (remainingMonths === 0) {
              // If exact years, display only years
              durationHTML = '<p>' + years + ' Vjet</p>';
            } else {
              // Display both years and remaining months
              durationHTML = '<p>' + years + ' Vjet ' + remainingMonths + ' Muaj</p>';
            }
            // Set contract start date and calculate expiration date
            var contractDate = moment(row.data_e_krijimit); // Assuming data_e_krijimit is the contract start date
            if (!contractDate.isValid()) {
              return '<span class="text-danger">Data e fillimit të kontratës e pavlefshme</span>'; // Handle invalid date
            }
            var expirationDate = contractDate.clone().add(months, 'months');
            expirationDate.locale('sq');
            var expirationDateFormatted = expirationDate.format('dddd, LL');
            // Set current date and calculate days until expiration
            var today = moment();
            var daysUntilExpiration = expirationDate.diff(today, 'days');
            // Define thresholds for near and far expiration
            var nearExpirationThreshold = 30; // 30 days
            var farExpirationThreshold = 90; // 90 days
            // Determine contract status based on expiration date
            var contractStatus, statusClass, statusMessage;
            if (daysUntilExpiration < 0) {
              contractStatus = 'Skaduar';
              statusClass = 'text-warning';
              statusMessage = 'Kontrata është skaduar';
            } else if (daysUntilExpiration <= nearExpirationThreshold) {
              contractStatus = 'Afër skadimit';
              statusClass = 'text-danger';
              statusMessage = 'Skadon shumë shpejt';
            } else if (daysUntilExpiration <= farExpirationThreshold) {
              contractStatus = 'Pranë skadimit';
              statusClass = 'text-warning';
              statusMessage = 'Skadon në një të ardhme të afërt';
            } else {
              contractStatus = 'Aktive';
              statusClass = 'text-success';
              statusMessage = 'Aktive';
            }
            // Return formatted output with contract status
            if (contractStatus === 'Skaduar') {
              return durationHTML + '<span class="' + statusClass + '">' + statusMessage + '</span>';
            } else {
              return durationHTML + '<span class="' + statusClass + '">' + expirationDateFormatted + ' (' + statusMessage + ')</span>';
            }
          }
        }
      },
      { // Custom column for buttons
        data: 'id', // Assuming 'id' is the property containing the ID
        render: function(data, type, row) {
          var buttonsHtml = `
            <a class="btn btn-sm btn-success py-2 px-2 rounded-5 shadow-sm text-white" href="editk.php?id=${data}"><i class="fi fi-rr-edit"></i></a>
        `;
          return buttonsHtml;
        }
      }
      //  <a class="btn btn-sm btn-primary py-2 px-2 rounded-5 shadow-sm text-white" data-bs-toggle="modal" data-bs-target="#pass${data}"><i class="fi fi-rr-lock"></i></a>
      // <a class="btn btn-sm btn-danger py-2 px-2 rounded-5 shadow-sm text-white" href="klient.php?blocked=${data}&block=${row.blockii}"><i class="fi fi-rr-ban"></i></a>
    ],
    columnDefs: [{
      "targets": [0, 1, 2, 3, 4, 5, 6], // Indexes of the columns you want to apply the style to
      "render": function(data, type, row) {
        // Apply the style to the specified columns
        return type === 'display' && data !== null ? '<div style="white-space: normal;">' + data + '</div>' : data;
      }
    }],
    dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
      "<'row'<'col-md-12'tr>>" +
      "<'row'<'col-md-6'i><'col-md-6'p>>",
    buttons: [{
      extend: 'pdfHtml5',
      text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
      titleAttr: 'Eksporto tabelen ne formatin PDF',
      className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
    }, {
      extend: 'excelHtml5',
      text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
      titleAttr: 'Eksporto tabelen ne formatin CSV',
      className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
    }, {
      extend: "copyHtml5",
      text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
      titleAttr: "Kopjo tabelen ne formatin Clipboard",
      className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
    }, {
      extend: 'print',
      text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
      titleAttr: 'Printo tabel&euml;n',
      className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
    }, ],
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
    stripeClasses: ['stripe-color'],
    "ordering": false
  })
  $(document).ready(function() {
    // Initialize the DataTable
    $('#non_monetized_clients').DataTable({
      dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
        "<'row'<'col-md-12'tr>>" +
        "<'row'<'col-md-6'i><'col-md-6'p>>",
      lengthMenu: [
        [10, 25, 50, 100, 500, 1000],
        [10, 25, 50, 100, 500, 1000]
      ],
      buttons: [{
        extend: 'pdfHtml5',
        text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
        titleAttr: 'Eksporto tabelen ne formatin PDF',
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
      }, {
        extend: 'excelHtml5',
        text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
        titleAttr: 'Eksporto tabelen ne formatin CSV',
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
      }, {
        extend: "copyHtml5",
        text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
        titleAttr: "Kopjo tabelen ne formatin Clipboard",
        className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
      }, {
        extend: 'print',
        text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
        titleAttr: 'Printo tabel&euml;n',
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
      }, ],
      "processing": true,
      "serverSide": true,
      "ajax": {
        "url": "ajax_get_non_monetized_clients.php",
        "type": "POST",
      },
      "columns": [{
          "data": "emri"
        },
        {
          "data": "monetizuar"
        }
      ],
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
      stripeClasses: ['stripe-color'],
    });
  });
  $(document).ready(function() {
    // Initialize the DataTable
    $('#monetized_clients').DataTable({
      dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
        "<'row'<'col-md-12'tr>>" +
        "<'row'<'col-md-6'i><'col-md-6'p>>",
      lengthMenu: [
        [10, 25, 50, 100, 500, 1000],
        [10, 25, 50, 100, 500, 1000]
      ],
      buttons: [{
        extend: 'pdfHtml5',
        text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
        titleAttr: 'Eksporto tabelen ne formatin PDF',
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
      }, {
        extend: 'excelHtml5',
        text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
        titleAttr: 'Eksporto tabelen ne formatin CSV',
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
      }, {
        extend: "copyHtml5",
        text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
        titleAttr: "Kopjo tabelen ne formatin Clipboard",
        className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
      }, {
        extend: 'print',
        text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
        titleAttr: 'Printo tabel&euml;n',
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
      }, ],
      "processing": true,
      "serverSide": true,
      "ajax": {
        "url": "ajax_get_monetized_clients.php",
        "type": "POST",
      },
      "columns": [{
          "data": "emri"
        },
        {
          "data": "monetizuar"
        }
      ],
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
      stripeClasses: ['stripe-color'],
    });
  });
</script>