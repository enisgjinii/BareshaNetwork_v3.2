<?php include 'header.php';
if (isset($_GET['id'])) {
  $gid = $_GET['id'];
  $conn->query("UPDATE rrogat SET lexuar='1' WHERE id='$gid'");
}
if (isset($_POST['ruaj'])) {
  $emri = mysqli_real_escape_string($conn, $_POST['emri']);
  $data = mysqli_real_escape_string($conn, $_POST['data']);
  $fatura = mysqli_real_escape_string($conn, $_POST['fatura']);
  if ($conn->query("INSERT INTO fatura (emri, data, fatura) VALUES ('$emri', '$data','$fatura')")) {
?>
    <meta http-equiv="refresh" content="0;URL='shitje.php?fatura=<?php echo $fatura; ?>'" />
<?php
  } else {
    echo "Gabim: " . $conn->error;
  }
}
if ($_SESSION['acc'] == '1') {
} elseif ($_SESSION['acc'] == '3') {
} else {
  die('<script>alert("Nuk keni Akses ne kete sektor")</script>');
  echo '<meta http-equiv="refresh" content="0;URL=index.php/" /> ';
}
if (isset($_GET['fshij'])) {
  $fshijid = $_GET['fshij'];
  $mfsh4 = $conn->query("SELECT * FROM fatura WHERE fatura='$fshijid'");
  $mfsh2 = mysqli_fetch_array($mfsh4);
  $emr = $mfsh2['emri'];
  $fatura2 = $mfsh2['fatura'];
  $data2 = $mfsh2['data'];
  if ($conn->query("INSERT INTO draft (emri, data, fatura) VALUES ('$emr', '$data2','$fatura2')")) {
    $conn->query("DELETE FROM fatura WHERE fatura='$fshijid'");
    $shdraft = $conn->query("SELECT * FROM shitje WHERE fatura='$fshijid'");
    while ($draft = mysqli_fetch_array($shdraft)) {
      $shemertimi = $draft['emertimi'];
      $shqmimi = $draft['qmimi'];
      $shperqindja = $draft['perqindja'];
      $shklienti = $draft['klientit'];
      $shmbetja = $draft['mbetja'];
      $shtotali = $draft['totali'];
      $shfatura = $draft['fatura'];
      $shdata = $draft['data'];
      if ($conn->query("INSERT INTO shitjedraft (emertimi, qmimi, perqindja, klientit, mbetja, totali, fatura, data) VALUES ('$shemertimi', '$shqmimi', '$shperqindja', '$shklienti', '$shmbetja', '$shtotali', '$shfatura', '$shdata')")) {
        $conn->query("DELETE FROM shitje WHERE fatura='$fshijid'");
      }
    }
  } else {
    echo '<script>alert("' . $conn->error . '");</script>';
  }
}
if (isset($_POST['ruajp'])) {
  $fatura = $_POST['fatura'];
  $shuma = $_POST['shuma'];
  $data = $_POST['data'];
  $menyra = $_POST['menyra'];
  $pershkrimi = $_POST['pershkrimi'];
  $conn->query("INSERT INTO pagesat (fatura, shuma, menyra, data, pershkrimi) VALUES ('$fatura', '$shuma', '$menyra', '$data', '$pershkrimi')");
}
if (isset($_GET['kerko'])) {
  $d1 = $_GET['d1'];
  $d2 = $_GET['d2'];
  $kerka = $_GET['artistik'];
  $kerkimi = $conn->query("SELECT * FROM klientet WHERE emri LIKE '%" . $kerka . "%'");
  $kerkimi2 = mysqli_fetch_array($kerkimi);
  $idofart = $kerkimi2['id'];
  $sql = "SELECT COUNT(fatura) FROM fatura WHERE data >= '$d1' AND data <= '$d2'";
} else {
  $sql = "SELECT COUNT(fatura) FROM fatura";
}
$limit = 20;
$rs_result = $conn->query($sql);
$row = mysqli_fetch_row($rs_result);
$total_records = $row[0];
$total_pages = ceil($total_records / $limit);
?>
<link rel="stylesheet" type="text/css" href="tcal.css" />
<script type="text/javascript" src="tcal.js"></script>

  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Fatur e re</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form method="POST" action="">

            <label for="emri">Emri & Mbiemri</label>
            <select name="emri" class="form-control">
              <?php
              $gsta = $conn->query("SELECT * FROM klientet");
              while ($gst = mysqli_fetch_array($gsta)) {
              ?>
                <option value="<?php echo $gst['id']; ?>"><?php echo $gst['emri']; ?></option>
              <?php } ?>

            </select>
            <label for="datas">Data:</label>
            <input type="text" name="data" class="form-control" value="<?php echo date("Y-m-d"); ?>">
            <label for="imei">Fatura:</label>

            <input type="text" name="fatura" class="form-control" value="<?php echo date('dmYhis'); ?>" readonly>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mbylle</button>
          <input type="submit" class="btn btn-primary" name="ruaj" value="Ruaj">
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- DataTales Example -->
  <div class="main-panel">

    <div class="content-wrapper">
      <div class="container">
        <div class="card">
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
            E re
          </button>
          <div class="card-body">

            <div class="row">
              <div class="col-12">
                <form method="GET" action="" class="form-inline">

                  Prej:
                  <input type="date" class="form-control" value="<?php echo date("Y-m-d"); ?>" style="width: 230px;" name="d1" autocomplete="off">


                  Deri:
                  <input type="date" class="form-control" value="<?php echo date("Y-m-d"); ?>" style="width: 230px;" name="d2" autocomplete="off">
                  <input type="submit" class="btn btn-primary" name="kerko" value="Kerko">
                  <hr>
                </form>
                <form method="POST" action="">
                  <input type="text" name="artistik" class="form-control" onkeyup="myFunction()" id="myInput" placeholder="K&euml;rko k&euml;ng&euml;tarin">
                </form>



                <div class="table-responsive">
                  <table class="table" id="myTable" width="100%" cellspacing="0">
                    <thead>
                      <tr>
                        <th>Emri</th>
                        <th>Emri artistik</th>
                        <th>Nr. Llogaris&euml;</th>
                        <th>Data</th>
                        <th>Shuma</th>
                        <th>Sh. Paguar</th>
                        <th>Obligim</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tfoot>
                      <tr>
                        <th>Emri & Mbiemri</th>
                        <th>Emri artistik</th>
                        <th>Nr. Llogaris&euml;</th>
                        <th>Data</th>
                        <th>Shuma</th>
                        <th>Sh. Paguar</th>
                        <th>Obligim</th>
                        <th></th>
                      </tr>
                    </tfoot>
                    <tbody>
                      <?php

                      if (isset($_GET["page"])) {
                        $page  = $_GET["page"];
                      } else {
                        $page = 1;
                      };
                      $start_from = ($page - 1) * $limit;
                      if (isset($_GET['kerko'])) {
                        $d1 = $_GET['d1'];
                        $d2 = $_GET['d2'];

                        $kueri = $conn->query("SELECT * FROM fatura WHERE data >= '$d1' AND data <= '$d2' ORDER BY data LIMIT $start_from, $limit");
                      } elseif (isset($_POST['artistik'])) {
                        $kerka = $_POST['artistik'];
                        $kerkimi = $conn->query("SELECT * FROM klientet WHERE emri LIKE '%" . $kerka . "%'");
                        $kerkimi2 = mysqli_fetch_array($kerkimi);
                        $idofart = $kerkimi2['id'];
                        $d1 = "0";
                        $d2 = "0";
                        $kueri = $conn->query("SELECT * FROM fatura WHERE emri='$idofart' ORDER BY data LIMIT $start_from, $limit");
                      } else {
                        $kueri = $conn->query("SELECT * FROM fatura ORDER BY id DESC LIMIT $start_from, $limit");
                      }



                      while ($k = mysqli_fetch_array($kueri)) {

                      ?>
                        <tr>
                          <?php
                          $sid = $k['fatura'];
                          $q4 = $conn->query("SELECT SUM(`totali`) as `sum` FROM `shitje` WHERE fatura='$sid'");
                          $qq4 = mysqli_fetch_array($q4);
                          $klientiid = $k['emri'];
                          $mkl = $conn->query("SELECT * FROM klientet WHERE id='$klientiid'");
                          $k4 = mysqli_fetch_array($mkl);
                          ?>
                          <td><?php echo $k4['emri']; ?></td>
                          <td><?php echo $k4['emriart']; ?></td>
                          <td><?php echo $k4['nrllog']; ?></td>
                          <td><?php
                              $dda = $k['data'];
                              $date = date_create($dda);
                              $dats = date_format($date, 'd-m-Y');
                              echo $dats;
                              ?></td>
                          <td><?php echo $qq4['sum']; ?>&euro;</td>
                          <td>
                            <?php
                            $obliid = $k['id'];
                            $merrpagesen = $conn->query("SELECT SUM(`shuma`) as `sum` FROM `pagesat` WHERE fatura='$sid'");
                            $merrep = mysqli_fetch_array($merrpagesen);
                            echo $merrep['sum'] . "&euro; &nbsp;";

                            ?>
                            <a class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal<?php echo $k['id']; ?>"><i class="ti-money"></i></a>
                          </td>
                          <!-- Modal -->
                          <div class="modal fade" id="modal<?php echo $k['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel">Shto Pages&euml;</h5>
                                  <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>ƒ
                                </div>
                                <div class="modal-body">
                                  <?php
                                  $checkob = $conn->query("SELECT * FROM yinc WHERE kanali='$klientiid'");
                                  while ($listaob = mysqli_fetch_array($checkob)) {
                                    if ($listaob['shuma'] > $listaob['pagoi']) {
                                  ?>

                                      <div class="alert alert-danger" id="alert" role="alert">
                                        <b><?php echo $k4['emri']; ?> </b>ka nj&euml; obligim me shum&euml;n: <b><?php echo $listaob['shuma'] - $listaob['pagoi']; ?>&euro;</b>, <br><b>P&euml;rshkrimi:</b> <?php echo $listaob['pershkrimi']; ?>.
                                      </div>
                                  <?php
                                    }
                                  }
                                  ?>
                                  <form method="POST" action="">
                                    <label>Fatura:</label>
                                    <input type="text" name="fatura" class="form-control" value="<?php echo $k['fatura']; ?>">
                                    <label>P&euml;rshkrimi:</label>
                                    <textarea class="form-control" name="pershkrimi"></textarea>
                                    <label>Shuma:</label>
                                    <div class="input-group mb-3">
                                      <div class="input-group-prepend">
                                        <span class="input-group-text">&euro;</span>
                                      </div>
                                      <input type="text" name="shuma" class="form-control" placeholder="0" aria-label="Shuma">
                                      <div class="input-group-append">
                                        <span class="input-group-text">.00</span>
                                      </div>
                                    </div>
                                    <label>M&euml;nyra e pages&euml;s</label>
                                    <select name="menyra" class="form-control">
                                      <option value="BANK">BANK</option>
                                      <option value="CASH">CASH</option>
                                      <option value="PayPal">PayPal</option>
                                      <option value="Ria">Ria</option>
                                      <option value="MoneyGram">Money Gram</option>
                                      <option value="WesternUnion">Western Union</option>
                                    </select>
                                    <label>Data</label>
                                    <input type="text" name="data" value="<?php echo date("Y-m-d"); ?>" class="form-control">

                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hiqe</button>
                                  <button type="submit" name="ruajp" class="btn btn-primary">Ruaj</button>
                                  </form>
                                </div>
                              </div>
                            </div>
                          </div>
                          <td><?php $obli =  $qq4['sum'] - $merrep['sum'];
                              if ($obli > 0) {
                                echo '<span style="color:red;">' . $obli . '&euro;</span>';
                              } else {
                                echo $obli . '&euro;';
                              }

                              ?></td>


                          <td><a class="btn btn-primary btn-sm" href="shitje.php?fatura=<?php echo $sid; ?>"><i class="ti-pencil"></i></a>
                            <a class="btn btn-success btn-sm" target="_blank" href="fatura.php?invoice=<?php echo $sid; ?>"><i class="ti-printer"></i></a>
                            <a class="btn btn-danger btn-sm" href="faturat.php?fshij=<?php echo $k['fatura']; ?>" onclick="return confirm('A jeni i sigurt qe deshironi ta fshini?');"><i class="ti-trash"></i> </a>
                          </td>
                        </tr>


                      <?php } ?>
                      <tr style="font-weight: bold;">
                        <?php
                        if (isset($_GET['kerko'])) {
                          $d1 = $_GET['d1'];
                          $d2 = $_GET['d2'];
                          $sum5 = $conn->query("SELECT SUM(klientit) AS sum FROM shitje WHERE data >= '$d1' AND data <= '$d2'");
                          $summ5 = mysqli_fetch_array($sum5);
                          $sum6 = $conn->query("SELECT SUM(mbetja) AS sum FROM shitje WHERE data >= '$d1' AND data <= '$d2'");
                          $summ6 = mysqli_fetch_array($sum6);
                        } else {
                          $sum5 = $conn->query("SELECT SUM(klientit) AS sum FROM shitje");
                          $summ5 = mysqli_fetch_array($sum5);
                          $sum6 = $conn->query("SELECT SUM(mbetja) AS sum FROM shitje");
                          $summ6 = mysqli_fetch_array($sum6);
                        }

                        ?>

                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>Klient&euml;ve: <?php echo $summ5['sum']; ?></td>
                        <td>Mbetja: <?php echo $summ6['sum']; ?></td>


                      </tr>

                    </tbody>

                  </table>
                  <script>
                    function myFunction() {
                      // Declare variables
                      var input, filter, table, tr, td, i, txtValue;
                      input = document.getElementById("myInput");
                      filter = input.value.toUpperCase();
                      table = document.getElementById("myTable");
                      tr = table.getElementsByTagName("tr");

                      // Loop through all table rows, and hide those who don't match the search query
                      for (i = 0; i < tr.length; i++) {
                        td = tr[i].getElementsByTagName("td")[0];
                        if (td) {
                          txtValue = td.textContent || td.innerText;
                          if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            tr[i].style.display = "";
                          } else {
                            tr[i].style.display = "none";
                          }
                        }
                      }
                    }
                  </script>
                  <nav>
                    <ul class="pagination">

                      <li class="page-item" id="1"><a href="faturat.php?page=1" data-id="1" class="page-link">
                          << /a>
                      </li>

                      <?php
                      if (!empty($total_pages)) {
                        for ($i = 1; $i <= $total_pages; $i++) {
                          if (isset($_GET['d1'])) {
                            if ($i == $_GET['page']) {

                      ?>

                              <li class="page-item active"><a href="faturat.php?page=<?php echo $i; ?>&d1=<?php echo $_GET['d1']; ?>&d2=<?php echo $_GET['d2']; ?>&kerko=kerko" class="page-link"><?php echo $i; ?></a></li>
                            <?php
                            } else {
                            ?>
                              <li class="page-item"><a href="faturat.php?page=<?php echo $i; ?>&d1=<?php echo $_GET['d1']; ?>&d2=<?php echo $_GET['d2']; ?>&kerko=kerko" class="page-link"><?php echo $i; ?></a></li>
                            <?php
                            }
                          } else {
                            if ($i == $_GET['page']) {

                            ?>

                              <li class="page-item active"><a href="faturat.php?page=<?php echo $i; ?>" class="page-link"><?php echo $i; ?></a></li>
                            <?php
                            } else {
                            ?>
                              <li class="page-item"><a href="faturat.php?page=<?php echo $i; ?>" class="page-link"><?php echo $i; ?></a></li>
                      <?php
                            }
                          }
                        }
                      }
                      ?>
                      <li class="page-item" id="<?php echo $total_pages; ?>"><a href="faturat.php?page=<?php echo $total_pages; ?>&kat=<?php echo $sasskat; ?>" class="page-link">></a></li>

                    </ul>
                  </nav>
                </div>

              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  var $fileName = 'BareshaOffice - Faturat';

  $(document).ready(function() {
    $('#order-listing').DataTable({
      dom: 'Bfrtip',
      buttons: [{
          extend: 'csv',
          filename: $fileName
        },
        {
          extend: 'excel',
          filename: $fileName
        },
        {
          extend: 'pdf',
          filename: $fileName
        }
      ]
    });
  });
</script>
<?php include 'footer.php'; ?>