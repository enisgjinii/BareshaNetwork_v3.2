<?php
include 'partials/header.php';
if (isset($_POST['ruaj'])) {
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  if ($conn->query("INSERT INTO emails (email) VALUES ('$email')")) {
    // Insertion successful
    echo "<script> 
          var alert = { icon: 'success', title: 'Success', text: 'Regjistrimi u shtua me sukses!' };
          </script>";
  } else {
    // Insertion failed
    echo "<script> 
          var alert = { icon: 'error', title: 'Error', text: 'Shtimi i regjistrimit dështoi.' };
          </script>";
  }
}
if (isset($_GET['delete'])) {
  $delid = $_GET['delete'];
  $stmt = $conn->prepare("DELETE FROM emails WHERE id = ?");
  $stmt->bind_param("i", $delid);
  if ($stmt->execute()) {
    // Deletion successful
    echo "<script> 
          var alert = { icon: 'success', title: 'Success', text: 'Regjistrimi është fshirë me sukses!' };
          </script>";
  } else {
    // Deletion failed
    echo "<script> 
          var alert = { icon: 'error', title: 'Error', text: 'Fshirja e regjistrimit dështoi.' };
          </script>";
  }
}
if (isset($_POST['update'])) {
  $edit_id = $_POST['edit_id'];
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  // Update the record in the database
  $result = $conn->query("UPDATE emails SET email='$email' WHERE id='$edit_id'");
  if ($result) {
    // Update successful
    echo "<script> 
          var alert = { icon: 'success', title: 'Success', text: 'Regjistrimi u përditësua me sukses!' };
          </script>";
  } else {
    // Update failed
    echo "<script> 
          var alert = { icon: 'error', title: 'Error', text: 'Përditësimi i regjistrimit dështoi.' };
          </script>";
  }
}
?>
<script>
  if (typeof alert !== 'undefined') {
    Swal.fire(alert);
  }
</script>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Shto nj&euml; email</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="" enctype="multipart/form-data">
          <div class="form-group">
            <label>Email</label>
            <input type="text" name="email" class="form-control rounded-5 border border-2" placeholder="Email" required oninvalid="this.setCustomValidity('Ju lutem plotësoni këtë fushë')" oninput="this.setCustomValidity('')">
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button>
        <input type="submit" class="input-custom-css px-3 py-2" name="ruaj" value="Ruaj">
        </form>
      </div>
    </div>
  </div>
</div>
<?php
$kueri = $conn->query("SELECT * FROM emails ORDER BY id ASC");
while ($k = mysqli_fetch_array($kueri)) {
?>
  <!-- Edit Modal -->
  <div class="modal fade" id="editModal_<?php echo $k['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel" style="font-size:14px;">Edito llogarinë e <?php echo $k['email']; ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="POST" action="">
            <input type="hidden" name="edit_id" value="<?php echo $k['id']; ?>">
            <div class="form-group">
              <label>Email</label>
              <input type="text" name="email" class="form-control rounded-5 border border-2" value="<?php echo $k['email']; ?>" required oninvalid="this.setCustomValidity('Ju lutem plotësoni këtë fushë')" oninput="this.setCustomValidity('')">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button>
          <input type="submit" class="input-custom-css px-3 py-2" name="update" value="Përditso">
          </form>
        </div>
      </div>
    </div>
  </div>
<?php } ?>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <nav class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Klientët</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">
            <a href="emails.php" class="text-reset" style="text-decoration: none;">
              Llogaritë e adresave elektronike (emails)
            </a>
          </li>
        </ol>
      </nav>
      <button type="button" class="input-custom-css px-3 py-2 mb-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
        <i class="fi fi-rr-add"></i> Shto email
      </button>
      <div class="card rounded-5 shadow-sm d-none d-md-none d-lg-block">
        <div class="card-body">
          <div class="row">
            <div class="col-12">
              <div class="table-responsive">
                <table id="example" class="table w-100 table-bordered">
                  <thead class="bg-light">
                    <tr>
                      <th>ID</th>
                      <th>Email</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $kueri = $conn->query("SELECT * FROM emails ORDER BY id DESC");
                    while ($k = mysqli_fetch_array($kueri)) {
                    ?>
                      <tr>
                        <td>
                          <?php echo $k['id']; ?>
                        </td>
                        <td>
                          <?php echo $k['email']; ?>
                        </td>
                        <td>
                          <a class="btn btn-primary text-white rounded-5 px-2 py-2" href="#" data-bs-toggle="modal" data-bs-target="#editModal_<?php echo $k['id']; ?>"><i class="fi fi-rr-edit"></i></a>
                          <button class="btn btn-danger text-white rounded-5 px-2 py-2" onclick="confirmDelete(<?php echo $k['id']; ?>)"><i class="fi fi-rr-trash"></i></button>
                        </td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="d-block d-md-block d-lg-none">
        <!-- List presentation for tablets and mobile -->
        <ul class="list-group">
          <!-- PHP loop for list content -->
          <?php
          $kueri = $conn->query("SELECT * FROM emails ORDER BY id DESC");
          while ($k = mysqli_fetch_array($kueri)) {
          ?>
            <br>
            <!-- Display list items -->
            <li class="list-group-item rounded-5">
              <div class="row">
                <div class="col-md-3">
                  <strong>ID:</strong> <?php echo $k['id']; ?>
                </div>
                <div class="col-md-3">
                  <strong>Email:</strong> <?php echo $k['email']; ?>
                </div>
                <div class="col-md-3">
                  <a class="input-custom-css px-3 py-2" style="text-decoration: none;text-transform:none;" href="#" data-bs-toggle="modal" data-bs-target="#editModal_<?php echo $k['id']; ?>"><i class="fi fi-rr-edit"></i></a>
                  <button class="input-custom-css px-3 py-1" style="text-decoration: none;text-transform:none;" onclick="confirmDelete(<?php echo $k['id']; ?>)"><i class="fi fi-rr-trash"></i></button>
                </div>
              </div>
            </li>
          <?php
          }
          ?>
        </ul>
      </div>
    </div>
  </div>
</div>
<?php include 'partials/footer.php'; ?>
<script>
  function confirmDelete(id) {
    Swal.fire({
      title: 'A je i sigurt?',
      text: 'Ky veprim nuk mund të ri-kthehet!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Po, fshijeni!',
      cancelButtonText: 'Anulo'
    }).then((result) => {
      if (result.isConfirmed) {
        // If user confirms, proceed with the deletion
        window.location.href = 'emails.php?delete=' + id;
      }
    });
  }
</script>
<script>
  $('#example').DataTable({
    responsive: true,
    search: {
      return: true,
    },
    dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
      "<'row'<'col-md-12'tr>>" +
      "<'row'<'col-md-6'><'col-md-6'p>>",
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
    order: [
      [0, 'desc']
    ],
    fixedHeader: true,
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
    },
    stripeClasses: ['stripe-color']
  })
</script>