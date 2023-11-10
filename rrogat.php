<?php include 'partials/header.php';
if (isset($_GET['id'])) {
  $gid = $_GET['id'];
  $conn->query("UPDATE rrogat SET lexuar='1' WHERE id='$gid'");
}
if (isset($_POST['ruaj'])) {
  $stafi = mysqli_real_escape_string($conn, $_POST['stafi']);
  $shuma = mysqli_real_escape_string($conn, $_POST['shuma']);
  $data = mysqli_real_escape_string($conn, $_POST['data']);
  $pagesa = mysqli_real_escape_string($conn, $_POST['forma']);
  $muaji = mysqli_real_escape_string($conn, $_POST['muaji']);
  $viti = mysqli_real_escape_string($conn, $_POST['viti']);

  $kontributi = $_POST['kontributi'];
  $kontributi2 = $_POST['kontributi2'];
  $kont =  ($kontributi / 100) * $shuma;
  $kont2 =  ($kontributi2 / 100) * $shuma;

  

  $pagaa = $shuma - $kont;
  if ($pagaa <= 80) {
    $p80 = $pagaa;
  } else {
    $p80 = "80";
    if ($pagaa - $p80 <= 170) {
      $p80_250 = $pagaa - $p80;
    } else {
      if ($pagaa - $p80 <= 80) {
        $p80_250 = 0;
      } else {
        $p80_250 = 170;
      }
    }
  }

  if ($pagaa - $p80 - $p80_250 <= 200) {
    $p250_450 = $pagaa - $p80 - $p80_250;
  } else {
    $p250_450 = 200;
    if ($pagaa - $p80 - $p80_250 <= 170) {
      $p250_450 = 0;
    }
  }
  if ($pagaa - $p80 - $p80_250 >= 200) {
    $p450 = $pagaa - $p80 - $p80_250 - $p250_450;
  } else {
    $p450 = 0;
  }

  $paga0 = $p80;
  $paga1 = $p80_250 * 0.04;
  $paga2 = $p250_450 * 0.08;
  $paga3 = $p450 * 0.1;
  $tatimi = $paga1 + $paga2 + $paga3;
  $neto = $pagaa - $paga1 - $paga2 - $paga3;
  if (!$conn->query("INSERT INTO rrogat (stafi, muaji, viti, shuma, kontributi, kontributi2, tatimi, neto, data, pagesa, lexuar) VALUES ('$stafi', '$muaji', '$viti', '$shuma', '$kont', '$kont2', '$tatimi', '$neto','$data', '$pagesa', '0')")) {
    echo '<script>alert("' . $conn->error . '");</script>';
  } else {
  }
}
if ($_SESSION['acc'] == '1') {
} elseif ($_SESSION['acc'] == '3') {
} else {
  die('<script>alert("Nuk keni Akses ne kete sektor")</script>');
  echo '<meta http-equiv="refresh" content="0;URL=index.php/" /> ';
}

?>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container">
      <div class="p-5 shadow-sm rounded-5 mb-4 card">
        <h4 class="font-weight-bold text-gray-800 mb-4">Pagat</h4>
        <nav class="d-flex">
          <h6 class="mb-0">
            <a href="" class="text-reset">Financat</a>
            <span>/</span>
            <a href="rrogat.php" class="text-reset" data-bs-placement="top" data-bs-toggle="tooltip" title="<?php echo __FILE__; ?>"><u>Pagat</u></a>
          </h6>
        </nav>
      </div>


      <!-- Modal -->
      <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Pagat</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>
            <div class="modal-body">
              <form method="POST" action="">
                <div class="form-group">
                  <label for="emrib">Muaji</label>
                  <select name="muaji" class="form-select">
                    <option value="Janar">Janar</option>
                    <option value="Shkurt">Shkurt</option>
                    <option value="Mars">Mars</option>
                    <option value="Prill">Prill</option>
                    <option value="Maj">Maj</option>
                    <option value="Qershor">Qershror</option>
                    <option value="Korrik">Korrik</option>
                    <option value="Gusht">Gusht</option>
                    <option value="Shtator">Shtator</option>
                    <option value="Tetor">Tetor</option>
                    <option value="Nentor">Nentor</option>
                    <option value="Dhjetor">Dhjetor</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="emrit">Viti</label>
                  <input type="text" class="form-control" name="viti" value="<?php echo date("Y"); ?>">
                </div>
                <div class="form-group">
                  <label for="emri">Zgjidh nj&euml;rin nga stafi</label>
                  <select name="stafi" class="form-select">
                    <?php
                    $gsta = $conn->query("SELECT * FROM users");
                    while ($gst = mysqli_fetch_array($gsta)) {
                    ?>
                      <option value="<?php echo $gst['id']; ?>"><?php echo $gst['name']; ?></option>
                    <?php } ?>

                  </select>
                </div>
                <div class="form-group">
                  <label for="datab">Shuma:</label>

                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text">&euro;</div>
                    </div>
                    <input type="text" name="shuma" class="form-control" id="inlineFormInputGroup" value="0.00">
                  </div>
                </div>
                <div class="form-group">
                  <label for="emrib">Kontributi i pun&euml;dhn&euml;n&euml;sit %</label>
                  <input type="text" class="form-control" name="kontributi" value="5">
                </div>
                <div class="form-group">
                  <label for="emrib">Kontributi i pun&euml;torit %</label>
                  <input type="text" class="form-control" name="kontributi2" value="5">
                </div>
                <div class="form-group">
                  <label for="datas">Data e pages&euml;s:</label>
                  <input type="text" name="data" class="form-control" value="<?php echo date("Y-m-d"); ?>">
                </div>
                <div class="form-group">
                  <label for="imei">Forma e pages&euml;s:</label>
                  <select name="forma" class="form-select">

                    <option value="Cash">Cash</option>
                    <option value="Bank">Bank</option>
                  </select>
                </div>


            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mbylle</button>
              <input type="submit" class="btn btn-primary" name="ruaj" value="Ruaj">
              </form>
            </div>
          </div>
        </div>
      </div>

      <div class="card rounded-5 shadow-sm">
        <div class="card-body">
          <div class="row">
            <div class="col-12">
              <div class="table-responsive">
                <table id="example" class="table w-100 table-bordered">
                  <thead class="bg-light">
                    <tr>
                      <th>Stafi</th>
                      <th>Muaji</th>
                      <th>Viti</th>
                      <th>Bruto</th>
                      <th>Kontributi i pun&euml;dh&euml;n&euml;sit</th>
                      <th>Kontributi i pun&euml;torit</th>
                      <th>Tatimi</th>
                      <th>Neto</th>
                      <th>Data</th>
                      <th>Forma</th>
                    </tr>
                  </thead>

                  <tbody>
                    <?php
                    $kueri = $conn->query("SELECT * FROM rrogat ORDER BY id DESC");
                    while ($k = mysqli_fetch_array($kueri)) {
                      $sid = $k['stafi'];
                      $gstaf = $conn->query("SELECT * FROM users WHERE id='$sid'");
                      $gstafi = mysqli_fetch_array($gstaf);
                      if (!is_null($gstafi)) {
                        $name = $gstafi['name'];
                      } else {
                        $name = "Unknow";
                      }
                    ?>
                      <tr>
                        <td><?php echo $name; ?></td>
                        <td><?php echo $k['muaji']; ?></td>
                        <td><?php echo $k['viti']; ?></td>
                        <td><?php echo $k['shuma']; ?>&euro;</td>
                        <td><?php echo $k['kontributi']; ?>&euro;</td>
                        <td><?php echo $k['kontributi2']; ?>&euro;</td>
                        <td><?php echo $k['tatimi']; ?>&euro;</td>
                        <td><?php echo $k['neto']; ?>&euro;</td>
                        <td><?php echo $k['data']; ?></td>
                        <td><?php echo $k['pagesa']; ?></td>
                      </tr>
                    <?php } ?>


                  </tbody>
                  <tfoot class="bg-light">
                    <tr>
                      <th>Stafi</th>
                      <th>Muaji</th>
                      <th>Viti</th>
                      <th>Bruto</th>
                      <th>Kontributi i pun&euml;dh&euml;n&euml;sit</th>
                      <th>Kontributi i pun&euml;torit</th>
                      <th>Tatimi</th>
                      <th>Neto</th>
                      <th>Data</th>
                      <th>Forma</th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

<?php include 'partials/footer.php'; ?>

<script>
  $('#example').DataTable({
    responsive: true,
    search: {
      return: true,
    },
    dom: 'Bfrtip',
    buttons: [{
      text: '<i class="fi fi-rr-user-add fa-lg"></i>&nbsp;&nbsp; E re',
      className: 'btn btn-light border shadow-2 me-2',
      action: function(e, node, config) {
        $('#exampleModal').modal('show')
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
    stripeClasses: ['stripe-color']
  })
</script>