<?php
include 'partials/header.php';
$kid = mysqli_real_escape_string($conn, $_GET['kid']);
$guse = $conn->query("SELECT * FROM klientet WHERE id='$kid'");
$guse2 = mysqli_fetch_array($guse);
$channel_id = $guse2['youtube'];
$api_key = "AIzaSyBrE0kFGTQJwn36FeR4NIyf4FEw2HqSSIQ";
$apiu = file_get_contents('https://www.googleapis.com/youtube/v3/channels?part=snippet&id=' . $channel_id . '&key=' . $api_key);
$apid = json_decode($apiu, true);
$aa = file_get_contents('https://www.googleapis.com/youtube/v3/channels?part=statistics&id=' . $channel_id . '&key=' . $api_key);
$aaa = json_decode($aa, true);
if (isset($_POST['infoprw'])) {
  $idup = $_POST['idup'];
  $texti = $_POST['infoprw'];
  if ($conn->query("UPDATE klientet SET infoprw='$texti' WHERE id='$idup'")) {
    echo "<script>alert('Infot private u ndryshuan me sukses, nese nuk shfaqen menjeher bejeni refresh faqen.')</script>";
  }
}
if (isset($_POST['shto'])) {
  $klienti = mysqli_real_escape_string($conn, $_POST['klienti']);
  $dataf = mysqli_real_escape_string($conn, $_POST['dataf']);
  $datas = mysqli_real_escape_string($conn, $_POST['datas']);
  $url = mysqli_real_escape_string($conn, $_POST['url']);
  $titulli = mysqli_real_escape_string($conn, $_POST['titulli']);
  $pershkrimi = mysqli_real_escape_string($conn, $_POST['pershkrimi']);
  $query = "INSERT INTO `strike`(`dataf`, `datas`, `url`, `pershkrimi`, `klienti`, `titulli`) VALUES ('$dataf', '$datas', '$url', '$pershkrimi', '$klienti', '$titulli')";
  if ($conn->query($query)) {
    echo "<script>alert('Strike u shtua me sukses');</script>";
  } else {
    echo "<script>alert('" . $conn->error;
    "');</script>";
  }
}
?>
<script src="https://cdn.tiny.cloud/1/v1lt364np68v98q2hye277yd2kz3szp65wttpsgbe8g4z6iv/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: 'textarea#editor',
    menubar: false
  });
</script>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <nav class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Klientet</a>
          </li>
          <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Lista e klientëve</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">
            <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">
              Kanali i klientit <?php echo $guse2['emri']; ?>
            </a>
          </li>
      </nav>
      <!-- Button trigger modal -->
      <button type="button" class="input-custom-css px-3 py-2 mb-3" data-bs-toggle="modal" data-bs-target="#platformsTableModal">
        <i class="fi fi-rr-globe"></i> &nbsp; Shiko të ardhurat nga platformat
      </button>
      <div class="modal fade" id="platformsTableModal" tabindex="-1" aria-labelledby="platformsTableModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="platformsTableModalLabel">Të ardhurat nga platformat për <?php echo $guse2['emri']; ?></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="table-responsive">
                <table id="platformsTable" class="table w-100 table-bordered">
                  <thead class="bg-light">
                    <tr>
                      <th>Emri</th>
                      <th>Artist(s)</th>
                      <th>Periods</th>
                      <th>Release</th>
                      <th>Track</th>
                      <th>Country</th>
                      <th>Revenue (USD)</th>
                      <th>Revenue Share (%)</th>
                      <th>Split Pay Share (%)</th>
                      <th>Partner</th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary btn-sm rounded-5" style="text-decoration: none;text-transform: none;" data-bs-dismiss="modal">Mbylle</button>
            </div>
          </div>
        </div>
      </div>
      <div class="row gutters-sm">
        <div class="col-6 mb-3">
          <div class="card rounded-5">
            <div class="card-body">
              <div class="d-flex flex-column align-items-center text-center">
                <img src="images/youtube.png" width="100" class="mb-3">
                <img src="<?php echo $apid['items'][0]['snippet']['thumbnails']['high']['url']; ?>" alt="Vizitoni kanalin tonë në youtube" class="rounded-circle mb-4" width="150">
                <h4 class="card-title mb-3"><?php echo $apid['items'][0]['snippet']['title']; ?></h4>
                <p class="card-text text-muted"><?php echo $apid['items'][0]['snippet']['description']; ?></p>
                <div class="row justify-content-center">
                  <div class="col-auto">
                    <div class="text-secondary"><i class="ti-user"></i> Total Abonues:</div>
                    <div class="text-primary"><?php echo number_format($aaa['items'][0]['statistics']['subscriberCount'], 2, '.', ','); ?></div>
                  </div>
                  <div class="col-auto">
                    <div class="text-secondary"><i class="ti-eye"></i> Total Shikime:</div>
                    <div class="text-primary"><?php echo number_format($aaa['items'][0]['statistics']['viewCount'], 2, '.', ','); ?></div>
                  </div>
                  <div class="col-auto">
                    <div class="text-secondary"><i class="ti-video-camera"></i> Total Video:</div>
                    <div class="text-primary"><?php echo $aaa['items'][0]['statistics']['videoCount']; ?></div>
                  </div>
                </div>
                <hr class="my-4">
                <div class="row justify-content-center">
                  <div class="col-auto">
                    <a style="text-decoration: none;text-transform: none" class="input-custom-css px-3 py-2" href="listang.php?id=<?php echo $kid; ?>"><i class="ti-flag-alt"></i> Raporti</a>
                  </div>
                  <div class="col-auto">
                    <a style="text-decoration: none;text-transform: none" class="input-custom-css px-3 py-2" href="kanali.php?id=<?php echo $channel_id; ?>">Shfleto kanalin</a>
                  </div>
                  <div class="col-auto">
                    <a style="text-decoration: none;text-transform: none" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#strike"><i class="ti-info-alt"></i> Strike</a>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card mt-3">
            <ul class="list-group list-group-flush">
              <!-- Modal -->
              <div class="modal fade" id="strike" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Shto Strike</h5>
                      <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <form method="POST" action="">
                        <input type="hidden" name="klienti" value="<?php echo $kid; ?>">
                        <label>Titulli:</label>
                        <input type="text" name="titulli" placeholder="Titulli!" class="form-control">
                        <label>Data:</label>
                        <input type="text" name="dataf" placeholder="Data e Strike!" class="form-control">
                        <label>Data e skadimit:</label>
                        <input type="text" name="datas" placeholder="Data e skadimit të Strike!" class="form-control">
                        <label>URL:</label>
                        <input type="url" name="url" placeholder="Linku i kenges!" class="form-control">
                        <label>Përshkrimi:</label>
                        <textarea name="pershkrimi" placeholder="Pershkrimi" class="form-control"></textarea>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulo</button>
                      <button type="submit" name="shto" class="btn btn-danger">Shto</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
              <?php
              // Function to fetch data from the API endpoint
              function fetchDataFromAPI($url)
              {
                $ch = curl_init();
                // Set cURL options
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // Execute the request
                $response = curl_exec($ch);
                // Close cURL session
                curl_close($ch);
                // Decode JSON response
                $data = json_decode($response, true);
                return $data;
              }
              // API endpoint URLs for Instagram and Facebook
              $instagramAPIUrl = 'https://api.example.com/instagram';
              $facebookAPIUrl = 'https://api.example.com/facebook';
              try {
                // Fetch data from Instagram API
                $instagramData = fetchDataFromAPI($instagramAPIUrl);
                // Fetch data from Facebook API
                $facebookData = fetchDataFromAPI($facebookAPIUrl);
                // Render the fetched data
                // Note: Replace the placeholders with the actual data keys from the API response
                echo '<div class="card">';
                echo '<div class="card-header">';
                echo '<h5 class="card-title mb-0">Social Media</h5>';
                echo '</div>';
                echo '<ul class="list-group list-group-flush">';
                echo '<li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">';
                echo '<div>';
                echo '<h6 class="mb-1">';
                echo '<i class="fi fi-brands-instagram"></i>';
                echo '</h6>';
                echo '</div>';
                echo '<span class="text-secondary">';
                echo $guse2['ig'];
                echo '</span>';
                echo '<a href="' . $guse2['ig'] . '" style="text-decoration: none;text-transform: none" class="input-custom-css px-3 py-2" target="_blank">Visit</a>';
                echo '</li>';
                echo '<li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">';
                echo '<div>';
                echo '<h6 class="mb-1">';
                echo '<i class="fi fi-brands-facebook"></i>';
                echo '</h6>';
                echo '</div>';
                echo '<span class="text-secondary">';
                echo $guse2['fb'];
                echo '</span>';
                echo '<a href="' . $guse2['fb'] . '" style="text-decoration: none;text-transform: none" class="input-custom-css px-3 py-2" target="_blank">Visit</a>';
                echo '</li>';
                echo '</ul>';
                echo '</div>';
              } catch (Exception $e) {
                echo 'Error fetching data: ' . $e->getMessage();
              }
              ?>
            </ul>
          </div>
        </div>
        <div class="col-6">
          <?php
          $adsid = $guse2['ads'];
          $mads = $conn->query("SELECT * FROM ads WHERE id='$adsid'");
          $ads = mysqli_fetch_array($mads);
          ?>
          <div class="card mb-3">
            <div class="card-body">
              <table class="table table-bordered w-100">
                <tr>
                  <th>Emri i plotë</th>
                  <td><?php echo ucfirst($guse2['emri']); ?></td>
                </tr>
                <tr>
                  <th>Emri artistik</th>
                  <td><?php echo ucfirst($guse2['emriart']); ?></td>
                </tr>
                <tr>
                  <th>Monetizuar</th>
                  <td>
                    <?php if ($guse2['monetizuar'] == "PO") {
                      $moni = "<span style='color:green;'>PO</span>";
                    } else {
                      $moni = "<span style='color:red;'>JO</span>";
                    }
                    echo $moni;
                    ?>
                  </td>
                </tr>
                <tr>
                  <th>Email</th>
                  <td><?php echo $ads['email']; ?></td>
                </tr>
                <tr>
                  <th>ADS ID</th>
                  <td><?php echo $ads['adsid']; ?></td>
                </tr>
                <tr>
                  <th>Shteti</th>
                  <td><?php echo $ads['shteti']; ?> <br> <?php
                                                          echo " 
                              <br><a href='https://www.google.com/maps/place/" . $ads['shteti'] . "' target='_blank' class='btn btn-light shadow-sm border btn-sm'><img src='https://img.icons8.com/emoji/36/null/" . strtolower($ads['shteti']) . "-emoji.png'/></a>";
                                                          ?> </td>
                </tr>
                <?php if ($_SESSION['acc'] == 1) { ?>
                  <tr>
                    <th>Perqindja</th>
                    <td><?php echo $guse2['perqindja']; ?>%</td>
                  </tr>
                <?php } ?>
                <?php if ($_SESSION['acc'] == 1) {
                  $totali = 0.00;
                  $totaliMbetur = 0.00;
                  $totaliYoutube = 0.00;
                  $totaliObligimitYoutube = 0.00;
                  $pyetja = $conn->query("SELECT * FROM fatura WHERE emri='$kid'");
                  while ($rreshti = mysqli_fetch_array($pyetja)) {
                    $fatura = $rreshti['fatura'];
                    $shumaKlientit = $conn->query("SELECT SUM(klientit) as total FROM shitje WHERE fatura='$fatura'");
                    $shumaMbetur = $conn->query("SELECT SUM(mbetja) as total FROM shitje WHERE fatura='$fatura'");
                    $shumaYoutube = $conn->query("SELECT SUM(`totali`) as `sum` FROM `shitje` WHERE fatura='$fatura'");
                    $shumaObligimitYoutube = $conn->query("SELECT SUM(`shuma`) as `sum` FROM `pagesat` WHERE fatura='$fatura'");
                    $rreshtiShumaKlientit = mysqli_fetch_array($shumaKlientit);
                    $rreshtiShumaMbetur = mysqli_fetch_array($shumaMbetur);
                    $rreshtiShumaYoutube = mysqli_fetch_array($shumaYoutube);
                    $rreshtiShumaObligimitYoutube = mysqli_fetch_array($shumaObligimitYoutube);
                    $obligimi = $rreshtiShumaObligimitYoutube['sum'] - $rreshtiShumaYoutube['sum'];
                    $totali += $rreshtiShumaKlientit['total'];
                    $totaliMbetur += $rreshtiShumaMbetur['total'];
                    $totaliYoutube += $rreshtiShumaYoutube['sum'];
                    $totaliObligimitYoutube += $obligimi;
                  }
                  if (empty($totali)) {
                    $totali = 0.00;
                  }
                  if (empty($totaliMbetur)) {
                    $totaliMbetur = 0.00;
                  }
                  if (empty($totaliYoutube)) {
                    $totaliYoutube = 0.00;
                  }
                  if (empty($totaliObligimitYoutube)) {
                    $totaliObligimitYoutube = 0.00;
                  }
                ?>
                  <tr>
                    <th>Shuma totale e pagesave</th>
                    <td><?php echo $totali; ?>&euro;</td>
                  </tr>
                  <tr>
                    <?php
                    $emri_i_artistit = $guse2['emri'];
                    $kerkesa = $conn->query("SELECT SUM(`RevenueUSD`) as `sum` FROM `platformat` WHERE Artist='$emri_i_artistit'");
                    $nxerrja_e_kerkeses = mysqli_fetch_array($kerkesa);
                    ?>
                    <th>Shuma totale e pagesave ne platforma tjera</th>
                    <td>
                      <?php echo $nxerrja_e_kerkeses['sum']; ?> &euro;
                    </td>
                  </tr>
                  <tr>
                    <th>Shuma totale e pagesave ne platformen Youtube</th>
                    <td>
                      <?php echo $totaliYoutube; ?> &euro;
                    </td>
                  </tr>
                  <tr>
                    <th>Shuma totale e obligimit ne platformen Youtube</th>
                    <td>
                      <?php
                      if ($totaliObligimitYoutube == 0) {
                        echo "<span style='color:green;'>Ky klient nuk ka obligim</span> ";
                      } else {
                        echo "<span>" . $totaliObligimitYoutube . "</span>";
                      }
                      // echo $totaliObligimitYoutube; 
                      ?> &euro;
                    </td>
                  </tr>
                  <tr>
                    <th>Fitimi total nga klienti</th>
                    <td><?php echo $totaliMbetur; ?>&euro;</td>
                  </tr>
                <?php } ?>
                <tr>
                  <th>Data e kontratës</th>
                  <td><?php echo $guse2['dk']; ?></td>
                </tr>
                <tr>
                  <th>Data e Skadimit (Kontratës)</th>
                  <td><?php echo $guse2['dks']; ?></td>
                </tr>
                <tr>
                  <th>Kontrata</th>
                  <td><a href="kontrata.php?id=<?php echo $guse2['id']; ?>" target="_blank" class="btn btn-light"><i class="far fa-file-pdf"></i></a></td>
                </tr>
                <tr>
                  <th>Adresa</th>
                  <td><?php echo $guse2['adresa']; ?></td>
                </tr>
                <tr>
                  <th>Kategoria</th>
                  <td><?php echo $guse2['kategoria']; ?></td>
                </tr>
                <tr>
                  <th>Email</th>
                  <td><?php echo $guse2['emailadd']; ?></td>
                </tr>
                <tr>
                  <th>Email per platforma</th>
                  <td><?php echo $guse2['emailp']; ?></td>
                </tr>
                <tr>
                  <th>Nr.Tel</th>
                  <td><?php echo $guse2['nrtel']; ?></td>
                </tr>
                <tr>
                  <th>Email qe kan akses</th>
                  <td><?php echo $guse2['emails']; ?></td>
                </tr>
                <tr>
                  <th>Info Shtesë</th>
                  <td><?php echo $guse2['info']; ?></td>
                </tr>
                <tr>
                  <th>Info Private</th>
                  <td>
                    <form method="POST" action="">
                      <input type="hidden" name="idup" value="<?php echo $guse2['id']; ?>">
                      <textarea id="editor" name="infoprw" placeholder="Info Shtesë"><?php echo $guse2['infoprw']; ?></textarea>
                      <center><button type="submit" class="btn btn-info">Përditso Infot Private</button></center>
                    </form>
                  </td>
                </tr>
                <tr>
                  <td colspan="2">
                    <a class="btn btn-info " href="editk.php?id=<?php echo $_GET['kid']; ?>">Ndrysho</a>
                    <a class="btn btn-danger " href="#">Fshij</a>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- <style type="text/css">
  body {
    color: #1a202c;
    text-align: left;
    background-color: #e2e8f0;
  }

  .main-body {
    padding: 15px;
  }

  .card {
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, .1), 0 1px 2px 0 rgba(0, 0, 0, .06);
  }

  .card {
    position: relative;
    display: flex;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 0 solid rgba(0, 0, 0, .125);
    border-radius: .25rem;
  }

  .card-body {
    flex: 1 1 auto;
    min-height: 1px;
    padding: 1rem;
  }

  .gutters-sm {
    margin-right: -8px;
    margin-left: -8px;
  }

  .gutters-sm>.col,
  .gutters-sm>[class*=col-] {
    padding-right: 8px;
    padding-left: 8px;
  }

  .mb-3,
  .my-3 {
    margin-bottom: 1rem !important;
  }

  .bg-gray-300 {
    background-color: #e2e8f0;
  }

  .h-100 {
    height: 100% !important;
  }

  .shadow-none {
    box-shadow: none !important;
  }
</style> -->
<?php include 'partials/footer.php'; ?>
<script>
  var table = $('#example').DataTable({
    responsive: true,
    search: {
      return: true,
    },
    dom: 'Bfrtip',
    buttons: [{
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
      titleAttr: 'Printo tabelën',
      className: 'btn btn-light border shadow-2 me-2'
    }],
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
  $(document).ready(function() {
    $('#platformsTable').DataTable({
      "processing": true,
      "serverSide": true,
      "ajax": {
        "url": "fetch_dataPlatforms.php", // PHP script to handle AJAX request
        "type": "POST",
        "data": function(d) {
          // Add artistii parameter to the data being sent to the server
          d.artistii = "<?php echo $guse2['emri']; ?>"; // Enclose the value in quotes
        }
      },
      dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
        "<'row'<'col-md-12'tr>>" +
        "<'row'<'col-md-6'i><'col-md-6'p>>",
      buttons: [{
          extend: "pdfHtml5",
          text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
          titleAttr: "Eksporto tabelen ne formatin PDF",
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
        },
        {
          extend: "copyHtml5",
          text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
          titleAttr: "Kopjo tabelen ne formatin Clipboard",
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
        },
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
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
      },
      stripeClasses: ['stripe-color'],
      "paging": true,
      "lengthChange": true,
      lengthMenu: [
        [5, 14, 25, 50, -1],
        [5, 14, 25, 50, "Te gjitha"]
      ],
      columnDefs: [{
        "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9], // Indexes of the columns you want to apply the style to
        "render": function(data, type, row) {
          // Apply the style to the specified columns
          return type === 'display' && data !== null ? '<div style="white-space: normal;">' + data + '</div>' : data;
        }
      }],
      "searching": true,
      "ordering": false,
      "autoWidth": false
    });
  });
</script>