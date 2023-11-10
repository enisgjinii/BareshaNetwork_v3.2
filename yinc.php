<?php include 'partials/header.php';

if (isset($_SESSION['oauth_uid'])) {
  $user_id = $_SESSION['oauth_uid'];
} else {
  // Use SweetAlert2 for the alert
  echo '<script>
    Swal.fire({
      icon: "error",
      title: "Akses i Kufizuar",
      text: "Nuk keni akses në këtë sektor.",
      showConfirmButton: false,
      timer: 2000  // Close the alert after 2 seconds
    }).then(function() {
      window.location.href = "index.php";
    });
  </script>';
  exit;
}
if (isset($_POST['paguaj'])) {
  $shpages = $_POST['pagoi'];
  $lloji = $_POST['lloji'];
  $idof = $_POST['idp'];
  if ($conn->query("UPDATE yinc SET pagoi=pagoi + '$shpages', lloji='$lloji' WHERE id='$idof'")) {
    echo '<script>alert("Pagesa u be me sukses")</script>';
  } else {
    echo '<script>alert(' . $conn->error . ')</script>';
  }
}
?>

<!-- Begin Page Content -->
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="container">
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Financat</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page"><a href="pagesat.php" class="text-reset" style="text-decoration: none;">
                <!-- Get the path of file  -->
                Shpenzimet
              </a></li>
        </nav>
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Shpenzimet e klient&euml;ve</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form method="POST" action="process_form.php">
                  <!-- Row 1 -->
                  <div class="row">
                    <div class="col-md-6">
                      <label for="emri" class="form-label">Zgjidh nj&euml;rin nga klient&euml;t</label>
                      <select name="stafi" class="form-select shadow-sm rounded-5" style="border: 1px solid #ced4da">
                        <?php
                        $gsta = $conn->query("SELECT * FROM klientet");
                        while ($gst = mysqli_fetch_array($gsta)) {
                        ?>
                          <option value="<?php echo $gst['id']; ?>"><?php echo $gst['emri']; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label for="datab" class="form-label">Shuma</label>
                      <div class="input-group mb-2 rounded-5 me-2">
                        <div class="input-group-prepend rounded-5 me-2" style="border: 1px solid #ced4da">
                          <div class="input-group-text rounded-5">&euro;</div>
                        </div>
                        <input type="text" name="shuma" class="form-control shadow-sm rounded-5" style="border: 1px solid #ced4da" id="inlineFormInputGroup" value="0.00">
                      </div>
                    </div>
                  </div>
                  <!-- Row 2 -->
                  <div class="row">
                    <div class="col-md-6">
                      <label for="datas" class="form-label">Data e pages&euml;s</label>
                      <input type="text" name="data" class="form-control shadow-sm rounded-5" style="border: 1px solid #ced4da" value="<?php echo date("d-m-Y"); ?>">
                    </div>
                    <div class="col-md-6">
                      <label for="pershkrimi" class="form-label">P&euml;rshkrimi</label>
                      <textarea name="pershkrimi" class="form-control shadow-sm rounded-5" style="border: 1px solid #ced4da"></textarea>
                    </div>
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



        <div class="card shadow-sm rounded-5">
          <div class="card-body">
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table id="example" class="table">
                    <thead class="bg-light">
                      <tr>
                        <th></th>

                        <th>Klienti</th>
                        <th>Shuma</th>
                        <th>Pagoi</th>
                        <th>Obligim</th>
                        <th>Forma</th>
                        <th>P&euml;rshkrimi</th>
                        <th>Data</th>

                      </tr>
                    </thead>

                    <tbody>
                      <?php
                      $kueri = $conn->query("SELECT * FROM yinc ORDER BY id DESC");
                      while ($k = mysqli_fetch_array($kueri)) {

                      ?>
                        <tr>
                          <?php
                          $sid = $k['kanali'];
                          $gstaf = $conn->query("SELECT * FROM klientet WHERE id='$sid'");
                          $gstafi = mysqli_fetch_array($gstaf);
                          //My number is 928.
                          $myNumber = $k['shuma'];

                          //I want to get 25% of 928.
                          $percentToGet = (float)$gstafi['perqindja'];

                          //Convert our percentage value into a decimal.
                          $percentInDecimal = $percentToGet / 100;

                          //Get the result.
                          $percent = $percentInDecimal * $myNumber;

                          //Print it out - Result is 232.

                          ?>
                          <td>
                            <a href="delete_record.php?id=<?php echo $k['id']; ?>" class="btn btn-danger px-2 m-2 btn-sm text-white rounded-5 shadow-sm" style="text-transform: none;"><i class="fas fa-trash py-2"></i></a><a data-bs-toggle="modal" data-bs-target="#pages<?php echo $k['id']; ?>" class="btn btn-primary btn-sm px-2 m-2 text-white rounded-5 shadow-sm" style="text-transform: none;"><i class="fas fa-money-bill py-2"></i></a>
                          </td>
                          <td><?php echo $gstafi['emri']; ?></td>

                          <td><?php echo $k['shuma']; ?>&euro;</td>
                          <td><?php echo $k['pagoi']; ?>&euro;</td>
                          <td style="color:red;"><?php echo $k['shuma'] - $k['pagoi']; ?>&euro; </td>

                          <td><?php echo $k['lloji']; ?></td>
                          <td><?php echo $k['pershkrimi']; ?></td>

                          <td><?php echo $k['data']; ?></td>
                          <!-- Implement a button who fetch id and then go to another form submit and delete based from that id -->

                        </tr>
                        <div class="modal fade" id="pages<?php echo $k['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Pages&euml;</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <form method="POST" action="">
                                  <input type="hidden" name="idp" value="<?php echo $k['id']; ?>">

                                  <div class="mb-3">
                                    <label for="pagoi" class="form-label">Shuma:</label>
                                    <div class="input-group">
                                      <span class="input-group-text">&euro;</span>
                                      <input type="text" name="pagoi" class="form-control" id="pagoi" value="0.00">
                                    </div>
                                  </div>

                                  <div class="mb-3">
                                    <label for="lloji" class="form-label">Forma e pages&euml;s:</label>
                                    <select name="lloji" class="form-select" id="lloji">
                                      <option value="Bank">Bank</option>
                                      <option value="Cash">Cash</option>
                                    </select>
                                  </div>


                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hiqe</button>
                                <button type="submit" name="paguaj" class="btn btn-primary">Paguaj</button>
                              </div>
                              </form>
                            </div>
                          </div>
                        </div>


                      <?php } ?>

                    </tbody>
                    <tfoot class="bg-light">
                      <tr>
                        <th></th>
                        <th>Klienti</th>
                        <th>Shuma</th>
                        <th>Pagoi</th>
                        <th>Obligim</th>
                        <th>Forma</th>
                        <th>P&euml;rshkrimi</th>
                        <th>Data</th>

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
    order: [

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
    stripeClasses: ['stripe-color']
  })
</script>