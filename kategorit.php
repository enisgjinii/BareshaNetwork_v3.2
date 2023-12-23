<?php
include 'partials/header.php';

// Function to display a SweetAlert2 confirmation dialog
function displayConfirmationDialog($delid)
{
  echo "<script>
          Swal.fire({
            title: 'Konfirmoni fshirjen',
            text: 'Jeni i sigurt që dëshironi ta fshini këtë rekord?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Po, fshijeni!',
            cancelButtonText: 'Anulo'
          }).then((result) => {
            if (result.isConfirmed) {
              // User confirmed, redirect to delete with confirmation
              window.location.href = '?delete=" . $delid . "&confirm=yes';
            }
          });
        </script>";
}

if (isset($_POST['ruaj'])) {
  $kategoria = mysqli_real_escape_string($conn, $_POST['kategoria']);
  $result = $conn->query("INSERT INTO kategorit (kategorit) VALUES ('$kategoria')");
  if ($result) {
    // Display a success message using SweetAlert2
    echo "<script>
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: 'Kategoria eshte ruajtur me sukses!',
            });
          </script>";
  } else {
    echo $conn->error;
  }
}

if (isset($_GET['delete'])) {
  $delid = $_GET['delete'];

  if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
    // User confirmed, proceed with deletion
    $result = $conn->query("DELETE FROM kategorit WHERE id='$delid'");
    if ($result) {
      // Display a success message using SweetAlert2
      echo "<script>
              Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Kategoria eshte fshire me sukses!',
              });
            </script>";
    } else {
      echo $conn->error;
    }
  } else {
    // Show the confirmation dialog
    displayConfirmationDialog($delid);
  }
}
function formatFileSize($filename)
{
  $size = filesize($filename);
  $units = array('B', 'KB', 'MB', 'GB', 'TB');
  $formattedSize = $size;
  for ($i = 0; $size >= 1024 && $i < count($units) - 1; $i++) {
    $size /= 1024;
    $formattedSize = round($size, 2);
  }
  return $formattedSize . ' ' . $units[$i];
}
$filename =   __FILE__;
$fileSize = formatFileSize($filename);
$text = 'Madhësia e dosjes: ' . $fileSize;
?>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Shto nj&euml; kategori</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body my-3">
        <form method="POST" action="">
          <input type="text" name="kategoria" class="form-control border border-2 rounded-5" placeholder="Em&euml;rtimi i kategoris">

      </div>
      <div class="modal-footer">
        <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button>
        <button type="submit" class="input-custom-css px-3 py-2" name="ruaj">Ruaje</button>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="container">

        <nav class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Klientët</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
              <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">
                Lista e kategorive
              </a>
            </li>
        </nav>
        <button type="button" class="input-custom-css px-3 py-2 mb-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
          <i class="fi fi-rr-add"></i> Shto kategori
        </button>

        <div class="card rounded-5 shadow-sm">
          <div class="card-body">
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table id="example" class="table w-100 table-bordered">
                    <thead class="bg-light">
                      <tr>
                        <th>Emertimi</th>
                        <th>Modifiko</th>
                      </tr>
                    </thead>

                    <tbody>
                      <?php
                      $kueri = $conn->query("SELECT * FROM kategorit");
                      while ($k = mysqli_fetch_array($kueri)) {

                      ?>
                        <tr>
                          <td><?php echo $k['kategorit']; ?></td>
                          <td><a class="btn btn-danger px-2 py-2 rounded-5 text-white" href="kategorit.php?delete=<?php echo $k['id']; ?>"><i class="fi fi-rr-trash"></i></a></td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

          </div>
        </div>
        <p class="text-muted mt-3"><?php echo $text; ?></p>
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
    stripeClasses: ['stripe-color']
  })
</script>