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
      <div class="container">

        <div class="p-5 rounded-5 shadow-sm mb-4 card">
          <h4 class="font-weight-bold text-gray-800 mb-4">Lista e keng&euml;ve</h4> <!-- Breadcrumb -->
          <nav class="d-flex">
            <h6 class="mb-0">
              <a href="javascript:void(0)" class="text-reset">K&euml;ng&euml;tar&euml;t</a>
              <span>/</span>
              <a href="klient.php" class="text-reset" data-bs-placement="top" data-bs-toggle="tooltip" title="<?php echo __FILE__; ?>" aria-current="page"><u>Profili i k&euml;ng&euml;tarit</u></a>
            </h6>
          </nav>
        </div>


        <div class="p-5 rounded-5 shadow-sm mb-4 card">
          <table id="example" data-ordering="false" class="table w-100 table-bordered">
            <span>
              M&euml; posht&euml; &euml;sht&euml; tabela e gjeneruar ( CSV ) p&euml;r klientin : <?php echo $apid['items'][0]['snippet']['title']; ?>
            </span>
            <br>
            <thead class="bg-light">
              <tr>
                <th>Emri</th>
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

              $artistii = $apid['items'][0]['snippet']['title'];





              $kueri = $conn->query("SELECT * FROM platformat_2 WHERE Emri='$artistii'");
              if (mysqli_num_rows($kueri) > 0) {
                while ($k = mysqli_fetch_array($kueri)) {

              ?>
                  <tr>
                    <td>
                      <?php echo $k['Emri']; ?>
                    </td>
                    <td>
                      <?php echo $k['Artist']; ?>
                    </td>
                    <td>
                      <?php echo $k['ReportingPeriod']; ?>
                    </td>
                    <td>
                      <?php echo $k['AccountingPeriod']; ?>
                    </td>
                    <td>
                      <?php echo $k['Release']; ?>
                    </td>
                    <td>
                      <?php echo $k['Track']; ?>
                    </td>
                    <td>
                      <?php echo $k['Country']; ?>
                    </td>
                    <td>
                      <?php echo $k['RevenueUSD']; ?>
                    </td>
                    <td>
                      <?php echo $k['RevenueShare']; ?>
                    </td>
                    <td>
                      <?php echo $k['SplitPayShare']; ?>
                    </td>

                  </tr>
                <?php
                }
                ?>
                <!-- <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>Totali:</b>
                              <?php echo $qq4['sum']; ?>
                            </td>
                            <td></td>
                            <td></td>
                          </tr> -->
              <?php
              } else {
                // handle empty result set
              }
              ?>
            </tbody>
            <tfoot class="bg-light">
              <tr>
                <th>Emri</th>
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

        </div>



        <div class="row gutters-sm">
          <div class="col-6 mb-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex flex-column align-items-center text-center">
                  <img src="images/youtube.png" width="100"><br>
                  <img src="<?php echo $apid['items'][0]['snippet']['thumbnails']['high']['url']; ?>" alt="Vizitoni kanalin ton&euml; n&euml; youtube" class="rounded-circle" width="150">
                  <div class="mt-3">
                    <h4>
                      <?php echo $apid['items'][0]['snippet']['title']; ?>
                    </h4>
                    <?php echo "<b>Total Abonues:</b> " . number_format($aaa['items'][0]['statistics']['subscriberCount'], 2, '.', ',') . " | &nbsp; ";
                    echo "<b>Total Shikime:</b> " . number_format($aaa['items'][0]['statistics']['viewCount'], 2, '.', ',') . " | &nbsp;";
                    echo "<b>Total Video:</b> " . $aaa['items'][0]['statistics']['videoCount']; ?>
                    <hr>
                    <p class="text-muted font-size-sm">
                      <?php echo $apid['items'][0]['snippet']['description']; ?>
                    </p>

                    <a class="btn btn-secondary" href="listang.php?id=<?php echo $kid; ?>"><i class="ti-flag-alt"></i>
                      Raporti</a>
                    <a class="btn btn-primary" href="kanali.php?id=<?php echo $channel_id; ?>">Shfleto kanalin</a>
                    <a class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#strike"><i class="ti-info-alt"></i>
                      Strike</a>

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
                          <input type="text" name="datas" placeholder="Data e skadimit t&euml; Strike!" class="form-control">
                          <label>URL:</label>
                          <input type="url" name="url" placeholder="Linku i kenges!" class="form-control">
                          <label>P&euml;rshkrimi:</label>
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

                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                  <h6 class="mb-0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-instagram mr-2 icon-inline text-danger">
                      <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                      <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                      <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                    </svg>Instagram</h6>
                  <span class="text-secondary">
                    <?php echo $guse2['ig']; ?>
                  </span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                  <h6 class="mb-0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-facebook mr-2 icon-inline text-primary">
                      <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                    </svg>Facebook</h6>
                  <span class="text-secondary">
                    <?php echo $guse2['fb']; ?>
                  </span>
                </li>
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
                    <th>Emri i plot&euml;</th>
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
                    <th>Data e kontrat&euml;s</th>
                    <td><?php echo $guse2['dk']; ?></td>
                  </tr>
                  <tr>
                    <th>Data e Skadimit (Kontrat&euml;s)</th>
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
                    <th>Info Shtes&euml;</th>
                    <td><?php echo $guse2['info']; ?></td>
                  </tr>
                  <?php if ($_SESSION['acc'] == '1') { ?>
                    <tr>
                      <th>Info Private</th>
                      <td>
                        <form method="POST" action="">
                          <input type="hidden" name="idup" value="<?php echo $guse2['id']; ?>">
                          <textarea id="editor" name="infoprw" placeholder="Info Shtes&euml;"><?php echo $guse2['infoprw']; ?></textarea>
                          <center><button type="submit" class="btn btn-info">P&euml;rditso Infot Private</button></center>
                        </form>
                      </td>
                    </tr>
                  <?php } ?>
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
</div>


<style type="text/css">
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
</style>

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
      titleAttr: 'Printo tabel&euml;n',
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