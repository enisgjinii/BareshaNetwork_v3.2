<?php include 'partials/header.php';

if (isset($_POST['ruaj'])) {
    $pershkrimi = mysqli_real_escape_string($conn, $_POST['pershkrimi']);
    // File upload handling
    $targetfolder = "dokument/";
    $filename = basename($_FILES['fileToUpload']['name']);
    $targetfolder = $targetfolder . $filename;
    $file_type = $_FILES['fileToUpload']['type'];
    $file_size = $_FILES['fileToUpload']['size'];
    $uploadOk = false;

    // File size validation (e.g., max 5MB)
    if ($file_size > 5 * 1024 * 1024) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Skedari është shumë i madh',
                text: 'Ju lutemi ngarkoni një skedar më të vogël se 5MB.',
            });
        </script>";
    } else {
        // Check file type and move the file
        if ($file_type == "application/pdf" || strpos($file_type, 'image/') === 0 || $file_type == "text/plain") {
            if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $targetfolder)) {
                $uploadOk = true;
            }
        }

        // Database insertion if the file was uploaded successfully
        if ($uploadOk) {
            if ($conn->query("INSERT INTO filet (pershkrimi, file) VALUES ('$pershkrimi', '$targetfolder')")) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Dokumenti u ruajt me sukses',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location = 'filet.php';
                    });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Gabim gjatë ruajtjes',
                        text: 'Ju lutemi provoni përsëri.',
                    });
                </script>";
            }
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gabim gjatë ngarkimit',
                    text: 'Ju lutemi ngarkoni një skedar të vlefshëm (PDF, imazh ose tekst).',
                });
            </script>";
        }
    }
}
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <nav class="bg-white px-2 rounded-5" style="width:fit-content;" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a class="text-reset" style="text-decoration: none;">Dokumente</a></li>
          <li class="breadcrumb-item active" aria-current="page"><a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">Lista e dokumenteve</a></li>
        </ol>
      </nav>
      <!-- Button trigger modal -->
      <button type="button" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#ngarkofile">
        <i class="fi fi-rr-add"></i>&nbsp; Shto
      </button>
      <br> <br>
      <form action="filet.php" method="post" enctype="multipart/form-data">
        <div class="modal fade" id="ngarkofile" name="ngarkofile" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-upload"></i> Ngarko një file</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label for="fileToUpload" class="form-label"><i class="fas fa-file"></i> Zgjidhni një dokument nga PC-ja juaj:</label>
                  <input type="file" name="fileToUpload" id="fileToUpload" class="form-control rounded-5" required>
                  <small id="fileHelp" class="form-text text-muted">Lejohen vetëm dokumente PDF, imazhe dhe skedarë teksti. Madhësia maksimale 5MB.</small>
                </div>
                <div class="mb-3">
                  <label for="pershkrimi" class="form-label"> Përshkrimi:</label>
                  <input type="text" name="pershkrimi" id="pershkrimi" placeholder="Shkruani përshkrimin këtu" class="form-control rounded-5" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Hiqe</button>
                <button type="submit" class="input-custom-css px-3 py-2" name="ruaj">Ruaj</button>
              </div>
            </div>
          </div>
        </div>
      </form>

      <div class="card rounded-5 shadow-none d-none d-md-none d-lg-block">
        <div class="card-body">
          <div class="row">
            <div class="table-responsive">
              <table id="example" class="table w-100 table-bordered">
                <thead class="bg-light">
                  <tr>
                    <th class="text-dark">Përshkrimi</th>
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
                          <a style="text-decoration: none;" class="input-custom-css px-3 py-2" href="<?php echo $k['file']; ?>" target="_blank"><i class="fi fi-rr-file-export"></i></a>
                        <?php } else { ?>
                          <button class="input-custom-css px-3 py-2" disabled><i class="fi fi-rr-file-export"></i></button>
                          <div class="text-danger mt-4 p-3 shadow-sm rounded-5 border">Dokumenti për përshkrimin&nbsp;&nbsp;<i>'<?php echo $k['pershkrimi']; ?>'</i>&nbsp;&nbsp;nuk ekziston.</div>
                        <?php } ?>
                      </td>
                      <td>
                        <a style="text-decoration: none;" class="input-custom-css px-3 py-2" href="edit_entry.php?id=<?php echo $k['id']; ?>"><i class="fi fi-rr-edit"></i></a>
                        <a style="text-decoration: none;" href="javascript:void(0);" class="input-custom-css px-3 py-2" onclick="deleteFile(<?php echo $k['id']; ?>)"><i class="fi fi-rr-trash"></i></a>
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
  </div>
</div>

<?php include 'partials/footer.php'; ?>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  function deleteFile(id) {
    Swal.fire({
      title: 'A jeni të sigurt?',
      text: "Ky veprim nuk mund të zhbëhet!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Po, fshije!',
      cancelButtonText: 'Anulo'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = 'api/delete_methods/delete_file.php?id=' + id;
      }
    })
  }

  $('#example').DataTable({
    responsive: true,
    search: {
      return: true,
    },
    initComplete: function() {
      $(".dt-buttons").removeClass("dt-buttons btn-group");
      $("div.dataTables_length select").addClass("form-select").css({
        width: "auto",
        margin: "0 8px",
        padding: "0.375rem 1.75rem 0.375rem 0.75rem",
        lineHeight: "1.5",
        border: "1px solid #ced4da",
        borderRadius: "0.25rem"
      });
    },
    fixedHeader: true,
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
    },
    stripeClasses: ['stripe-color']
  });
</script>

