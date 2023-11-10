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

<link rel="stylesheet" type="text/css" href="https://paneli.bareshaoffice.com/vendors/simple-line-icons/css/simple-line-icons.css">
<!-- DataTales Example -->
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">

      <div class="p-5 rounded-5 shadow-sm card">
        <h4 class="font-weight-bold text-gray-800 mb-4">Lista e klient&euml;ve</h4> <!-- Breadcrumb -->
        <nav class="d-flex">
          <h6 class="mb-0">
            <a href="" class="text-reset">Klient&euml;t</a>
            <span>/</span>
            <a href="klient.php" class="text-reset" data-bs-placement="top" data-bs-toggle="tooltip" title="<?php echo __FILE__; ?>"><u>Lista e klient&euml;ve</u></a>
          </h6>
        </nav>
        <!-- Breadcrumb -->
      </div>

      <div class="row my-5 text-center">
        <div class="col ">
          <div class="card p-2 rounded-5 shadow-sm">
            <?php
            $kueri = $conn->query("SELECT COUNT(monetizuar) FROM klientet");
            $result = $kueri->fetch_assoc();
            ?>
            <i class="fi fi-rr-user fa-2x"></i>
            <br>
            <p>Numri total i klient&euml;ve </p>
            <h1>
              <?php echo $result["COUNT(monetizuar)"]; ?>
            </h1>
          </div>
        </div>
        <div class="col">
          <div class="card p-3 rounded-5 shadow-sm">
            <?php
            $kueri = $conn->query("SELECT COUNT(monetizuar) FROM klientet where monetizuar = 'PO'");
            $result = $kueri->fetch_assoc();
            ?>
            <i class="fi fi-rr-dollar fa-2x"></i>
            <br>
            <p>Numri i klient&euml;ve te monetizuar </p>
            <h1>
              <?php echo $result["COUNT(monetizuar)"]; ?>
            </h1>
            <div>
              <button type="button" class="btn btn-primary btn-sm text-white" style="text-transform: none" data-bs-toggle="modal" data-bs-target="#modal_of_monetized">
                Shiko list&euml;n
              </button>
            </div>
          </div>
        </div>
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




        <div class="col">
          <div class="card p-3 rounded-5 shadow-sm">
            <?php
            $kueri = $conn->query("SELECT COUNT(monetizuar) FROM klientet where monetizuar = 'JO'");
            $result = $kueri->fetch_assoc();
            ?>
            <del><i class="fi fi-rr-dollar fa-2x"></i></del>
            <br>
            <p>Numri i klient&euml;ve te pa-monetizuar </p>
            <h1>
              <?php echo $result["COUNT(monetizuar)"]; ?>
            </h1>
            <div>
              <button type="button" class="btn btn-primary btn-sm text-white" style="text-transform: none" data-bs-toggle="modal" data-bs-target="#modal_of_non_monetized">
                Shiko list&euml;n
              </button>
            </div>
          </div>
        </div>
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
              <table id="example">
                <thead class="bg-light">
                  <tr>
                    <th>Emri & Mbiemri</th>
                    <th>Emri Artistik</th>
                    <th>Adresa e email-it</th>
                    <th>Data e kontrates</th>
                    <th>Data e skadimit</th>
                    <th>Monetizuar</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $kueri = $conn->query("SELECT * FROM klientet ORDER BY id DESC");
                  while ($k = mysqli_fetch_array($kueri)) {
                    if ($k['monetizuar'] == "PO") {
                      $moni = "<td style='color:green;'>PO</td>";
                    } else {
                      $moni = "<td style='color:red;'>JO</td>";
                    }
                    if ($k['glist'] == '0') {
                      $aktive = '<a href="klient.php?aktivizo=' . $k['id'] . '&s=1"><i class="far fa-eye"></i></a>';
                    } else {
                      $aktive = '<a href="klient.php?aktivizo=' . $k['id'] . '&s=0"><i class="far fa-eye-slash"></i></a>';
                    }
                    if ($k['blocked'] == 1) {
                      $emribl = '<del style="color:red;">' . $k['emri'] . '</del>';
                      $blockii = 0;
                    } else {
                      $emribl = $k['emri'];
                      $blockii = 1;
                    }
                  ?>
                    <tr>
                      <td><a class="badge rounded-pill text-bg-success text-white w-100 shadow-sm" href="kanal.php?kid=<?php echo $k['id']; ?>"><?php echo $emribl; ?></a></td>
                      <td><a class="badge rounded-pill text-bg-success text-white w-100 shadow-sm" href="kanal.php?kid=<?php echo $k['id']; ?>"><?php echo $k['emriart']; ?></a></td>
                      <td>
                        <?php echo $k['emailadd']; ?>
                      </td>
                      <td>
                        <?php echo $k['dk']; ?>
                      </td>
                      <td>
                        <?php echo $k['dks']; ?>
                      </td>
                      <?php echo $moni; ?>
                      <td>
                        <a class="btn btn-success py-2 rounded-5 shadow-sm text-white" href="editk.php?id=<?php echo $k['id']; ?>"><i class="fi fi-rr-edit"></i></a>
                        <a class="btn btn-primary py-2 rounded-5 shadow-sm text-white" data-bs-toggle="modal" data-bs-target="#pass<?php echo $k['id']; ?>"><i class="fi fi-rr-lock"></i></a>
                        <a class="btn btn-danger py-2 rounded-5 shadow-sm text-white" href="klient.php?blocked=<?php echo $k['id']; ?>&block=<?php echo $blockii; ?>"><i class="fi fi-rr-ban"></i></a>
                      </td>
                    </tr>
                    <div class="modal fade" id="pass<?php echo $k['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Fjal&euml;kalimi i ri</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            <form method="POST" action="">
                              <label>Vendos fjalkalimin e klientit:</label>
                              <input type="hidden" name="idup" value="<?php echo $k['id']; ?>">
                              <input type="text" name="fjalkalim" class="form-control" placeholder="Fjalkalimi i ri">
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulo</button>
                            <button type="submit" name="upp" class="btn btn-primary">Ruaj Ndryshimin</button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php } ?>
                </tbody>

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
  $('#example').DataTable({
    // responsive: true,
    search: {
      return: true,
    },
    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, "T&euml; gjitha"]
    ],
    initComplete: function() {
      var btns = $('.dt-buttons');
      btns.addClass('');
      btns.removeClass('dt-buttons btn-group');
      var lengthSelect = $('div.dataTables_length select');
      lengthSelect.addClass('form-select'); // add Bootstrap form-select class
      lengthSelect.css({
        'width': 'auto', // adjust width to fit content
        'margin': '0 8px', // add some margin around the element
        'padding': '0.375rem 1.75rem 0.375rem 0.75rem', // adjust padding to match Bootstrap's styles
        'line-height': '1.5', // adjust line-height to match Bootstrap's styles
        'border': '1px solid #ced4da', // add border to match Bootstrap's styles
        'border-radius': '0.25rem', // add border radius to match Bootstrap's styles
      }); // adjust width to fit content
    },
    dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
      "<'row'<'col-md-12'tr>>" +
      "<'row'<'col-md-6'><'col-md-6'p>>",
    buttons: [{
      extend: 'pdfHtml5',
      text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
      titleAttr: 'Eksporto tabelen ne formatin PDF',
      className: 'btn btn-light btn-sm border  me-2'
    }, {
      extend: 'excelHtml5',
      text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
      titleAttr: 'Eksporto tabelen ne formatin CSV',
      className: 'btn btn-light btn-sm border  me-2'
    }, {
      extend: 'print',
      text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
      titleAttr: 'Printo tabel&euml;n',
      className: 'btn btn-light btn-sm border  me-2'
    }, {
      text: '<i class="fi fi-rr-user-add fa-lg"></i>&nbsp;&nbsp; Shto klient&euml;',
      className: 'btn btn-light btn-sm border  me-2',
      action: function(e, node, config) {
        window.location.href = 'shtok.php';
      }
    }, ],
    initComplete: function() {
      var btns = $('.dt-buttons');
      btns.addClass('');
      btns.removeClass('dt-buttons btn-group');

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
      "dom": 'frtip',
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
      "stripeClasses": ['stripe-color'],

      initComplete: function() {
        var btns = $('.dt-buttons');
        btns.addClass('');
        btns.removeClass('dt-buttons btn-group');

      },
    });
  });


  $(document).ready(function() {
    // Initialize the DataTable
    $('#monetized_clients').DataTable({
      "dom": 'frtip',
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
      "stripeClasses": ['stripe-color'],

      initComplete: function() {
        var btns = $('.dt-buttons');
        btns.addClass('');
        btns.removeClass('dt-buttons btn-group');

      },
    });
  });
</script>


<script>
  var ctx = document.getElementById('client-chart').getContext('2d');
  var myChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Numri total i klient&euml;ve', 'Numri i klient&euml;ve te monetizuar', 'Numri i klient&euml;ve te pa-monetizuar'],
      datasets: [{
        label: '# of Clients',
        data: [
          <?php
          $kueri = $conn->query("SELECT COUNT(*) FROM klientet");
          $result = $kueri->fetch_assoc();
          echo $result["COUNT(*)"] . ",";
          $kueri = $conn->query("SELECT COUNT(*) FROM klientet WHERE monetizuar = 'PO'");
          $result = $kueri->fetch_assoc();
          echo $result["COUNT(*)"] . ",";
          $kueri = $conn->query("SELECT COUNT(*) FROM klientet WHERE monetizuar = 'JO'");
          $result = $kueri->fetch_assoc();
          echo $result["COUNT(*)"];
          ?>
        ],
        backgroundColor: [
          'rgba(255, 99, 132)',
          'rgba(54, 162, 235)',
          'rgba(255, 206, 86)'
        ],
        borderColor: [
          'rgba(255, 99, 132)',
          'rgba(54, 162, 235)',
          'rgba(255, 206, 86)'
        ],
        borderWidth: 1
      }]
    },


    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      },
      legend: {
        display: true,
        position: 'right',
        labels: {
          fontColor: 'rgb(255, 99, 132)'
        }
      }
    }
  });
</script>