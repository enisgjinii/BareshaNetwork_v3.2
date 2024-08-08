<?php include 'partials/header.php';
if (isset($_POST['ruaj'])) {
  // $emri = $_POST['emri'];
  if (empty($_POST['min'])) {
    $mon = "JO";
  } else {
    $mon = $_POST['min'];
  }
  $pershkrimi = mysqli_real_escape_string($conn, $_POST['pershkrimi']);
  $targetfolder = "dokument/";

  $targetfolder = $targetfolder . basename($_FILES['fileToUpload']['name']);

  $ok = 1;

  $file_type = $_FILES['fileToUpload']['type'];

  if ($file_type == "application/pdf") {

    if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $targetfolder)) {
    } else {
    }
  } else {
    if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $targetfolder)) {
    }
  }


  if ($conn->query("INSERT INTO filet (pershkrimi, file) VALUES ('$pershkrimi', '$targetfolder')")) {
  }
}
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="container">
        <div class="p-5 rounded-5 shadow-sm mb-4 card">
          <h4 class="font-weight-bold text-gray-800 mb-4 text-dark">Dokumente tjera</h4>
          <nav class="d-flex">
            <h6 class="mb-0">
              <a href="filet.php" class="text-reset text-dark" data-bs-placement="top" data-bs-toggle="tooltip" title="<?php echo __FILE__; ?>"><u>Dokumente tjera</u></a>
            </h6>
          </nav>
        </div>
        <div class="card rounded-5 shadow-sm">
          <div class="card-body">
            <h4 class="card-title">Dokumente t&euml; ndryshme</h4>
            <div class="row">
              <div class="col-12">
                <div class="modal fade" id="ngarkofile" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Ngarko nj&euml; file</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <form action="filet.php" method="post" enctype="multipart/form-data">

                          <label>Ngarko nj&euml; file:</label>
                          <input type="file" name="fileToUpload" id="fileToUpload" class="form-control">
                          <label>P&euml;rshkrimi</label>
                          <input type="text" name="pershkrimi" placeholder="P&euml;rshkrimi" class="form-control" required="">


                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hiqe</button>
                        <button type="submit" class="btn btn-primary" name="ruaj">Ruaj</button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
                </p>
                <div class="table-responsive">
                  <table id="example" class="table w-100 table-bordered">
                    <thead class="bg-light">
                      <tr>
                        <th class="text-dark">Pershkrimi</th>
                        <th class="text-dark">Filet</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $kueri = $conn->query("SELECT * FROM filet ORDER BY id DESC");
                      while ($k = mysqli_fetch_array($kueri)) {
                      ?>
                        <tr>
                          <td><?php echo $k['pershkrimi']; ?></td>
                          <td>
                            <?php if (file_exists($k['file'])) { ?>
                              <a class="btn btn-light btn-lg shadow-2 border border-1" href="<?php echo $k['file']; ?>" target="_blank"><i class="fi fi-rr-file-export"></i></a>
                            <?php } else { ?>
                              <button class="btn btn-light btn-lg shadow-2 border border-1" disabled><i class="fi fi-rr-file-export"></i></button>

                              <div class="text-danger mt-4 p-3 shadow-sm rounded-5 border">Dokumenti p&euml;r p&euml;rshkrimin&nbsp;&nbsp; <i>'<?php echo $k['pershkrimi']; ?>'</i> &nbsp;&nbsp;nuk ekziston.</div>
                            <?php } ?>
                          </td>

                          <td>
                            <a class="btn btn-light btn-lg shadow-2 border border-1" href="edit_entry.php?id=<?php echo $k['id']; ?>"><i class="fi fi-rr-edit"></i></a>
                            <a href="delete_file.php?id=<?php echo $k['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this entry?')"><i class="fi fi-rr-trash"></i></a>
                          </td>
                        </tr>
                      <?php } ?>
                    </tbody>
                    <tfoot class="bg-light">
                      <tr>
                        <th>Pershkrimi</th>
                        <th>Filet</th>
                        <th></th>
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
      text: '<i class="fi fi-rr-folder-upload fa-lg"></i>&nbsp;&nbsp; Shto nje dokument',
      className: 'btn btn-light border shadow-2 me-2',
      action: function(e, node, config) {
        $('#ngarkofile').modal('show')
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