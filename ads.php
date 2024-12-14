<?php
include 'partials/header.php';

// Initialize variables
$errors = [];
$success = "";

// Handle Add ADS
if (isset($_POST['ruaj'])) {
  $email = trim($_POST['email']);
  $adsid = trim($_POST['adsid']);
  $shteti = trim($_POST['shteti']);

  // Validate inputs
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email-i është i pavlefshëm.";
  }
  if (empty($adsid)) {
    $errors[] = "ADS ID është i detyrueshëm.";
  }
  if (empty($shteti)) {
    $errors[] = "Shteti është i detyrueshëm.";
  }

  if (empty($errors)) {
    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO ads (email, adsid, shteti) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $adsid, $shteti);
    if ($stmt->execute()) {
      echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Regjistrimi u ruajt me sukses!',
                });
            </script>";
    } else {
      echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ruajtja e regjistrimit dështoi.',
                });
            </script>";
    }
    $stmt->close();
  } else {
    // Display errors using SweetAlert2
    $errorText = implode('<br>', $errors);
    echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gabim',
                html: '$errorText',
            });
        </script>";
  }
}

// Handle Delete ADS
if (isset($_GET['delete'])) {
  $delid = intval($_GET['delete']);
  $stmt = $conn->prepare("DELETE FROM ads WHERE id = ?");
  $stmt->bind_param("i", $delid);
  if ($stmt->execute()) {
    // Record deleted successfully
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Regjistrimi është fshirë me sukses!',
            });
        </script>";
  } else {
    // Failed to delete record
    echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Fshirja e rekordit dështoi.',
            });
        </script>";
  }
  $stmt->close();
}

// Handle Update ADS
if (isset($_POST['update'])) {
  $edit_id = intval($_POST['edit_id']);
  $email = trim($_POST['email']);
  $adsid = trim($_POST['adsid']);
  $shteti = trim($_POST['shteti']);

  // Validate inputs
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email-i është i pavlefshëm.";
  }
  if (empty($adsid)) {
    $errors[] = "ADS ID është i detyrueshëm.";
  }
  if (empty($shteti)) {
    $errors[] = "Shteti është i detyrueshëm.";
  }

  if (empty($errors)) {
    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE ads SET email = ?, adsid = ?, shteti = ? WHERE id = ?");
    $stmt->bind_param("sssi", $email, $adsid, $shteti, $edit_id);
    if ($stmt->execute()) {
      echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Regjistrimi u përditësua me sukses!',
                });
            </script>";
    } else {
      echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Dështoi në përditësimin e rekordit.',
                });
            </script>";
    }
    $stmt->close();
  } else {
    // Display errors using SweetAlert2
    $errorText = implode('<br>', $errors);
    echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gabim',
                html: '$errorText',
            });
        </script>";
  }
}

// Determine if viewing klientet for a specific ADS
$viewKlientet = false;
if (isset($_GET['id'])) {
  $viewKlientet = true;
  $idof = intval($_GET['id']);
  // Fetch ADS details
  $stmt_ads = $conn->prepare("SELECT * FROM ads WHERE id = ?");
  $stmt_ads->bind_param("i", $idof);
  $stmt_ads->execute();
  $result_ads = $stmt_ads->get_result();
  if ($result_ads->num_rows == 0) {
    echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'ADS nuk u gjet.',
            }).then(() => {
                window.location.href = 'ads.php';
            });
        </script>";
    exit();
  }
  $adsDetails = $result_ads->fetch_assoc();
  $stmt_ads->close();

  // Fetch related klientet
  $stmt_klientet = $conn->prepare("SELECT * FROM klientet WHERE ads = ?");
  $stmt_klientet->bind_param("i", $idof);
  $stmt_klientet->execute();
  $merrl = $stmt_klientet->get_result();
  $stmt_klientet->close();
}
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <?php if (!$viewKlientet): ?>
        <!-- ADS Management Section -->
        <nav class="bg-white px-2 rounded-5 mb-3" style="width: fit-content;" aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a class="text-reset" href="#">Klientët</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="ads.php" class="text-reset">Llogaritë e ADS</a></li>
          </ol>
        </nav>

        <!-- Button to Open Add Modal -->
        <button type="button" class="input-custom-css px-3 py-2 mb-3" data-bs-toggle="modal" data-bs-target="#addModal">
          <i class="fi fi-rr-add"></i> Shto ADS
        </button>

        <!-- Add ADS Modal -->
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-header">
                  <h5 class="modal-title" id="addModalLabel">Shto Një Llogari ADS</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="form-group mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control rounded-5 border border-2" placeholder="Email" required oninvalid="this.setCustomValidity('Ju lutem plotësoni këtë fushë')" oninput="this.setCustomValidity('')">
                  </div>
                  <div class="form-group mb-3">
                    <label>ADS ID</label>
                    <input type="text" name="adsid" class="form-control rounded-5 border border-2" placeholder="ADS ID" required oninvalid="this.setCustomValidity('Ju lutem plotësoni këtë fushë')" oninput="this.setCustomValidity('')">
                  </div>
                  <div class="form-group mb-3">
                    <label>Shteti</label>
                    <input type="text" name="shteti" class="form-control rounded-5 border border-2" placeholder="Shteti" required oninvalid="this.setCustomValidity('Ju lutem plotësoni këtë fushë')" oninput="this.setCustomValidity('')">
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button>
                  <input type="submit" class="input-custom-css px-3 py-2" name="ruaj" value="Ruaj">
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- ADS List Table -->
        <div class="card rounded-5 shadow-sm d-none d-md-none d-lg-block">
          <div class="card-body">
            <div class="table-responsive">
              <table id="adsTable" class="table w-100 table-bordered">
                <thead class="bg-light">
                  <tr>
                    <th class="text-dark">Email</th>
                    <th class="text-dark">ADS ID</th>
                    <th class="text-dark">Shteti</th>
                    <th class="text-dark">Klientët</th>
                    <th class="text-dark">Veprime</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $kueri = $conn->query("SELECT * FROM ads ORDER BY id ASC");
                  while ($k = mysqli_fetch_assoc($kueri)) {
                  ?>
                    <tr>
                      <td><?php echo htmlspecialchars($k['email']); ?></td>
                      <td><?php echo htmlspecialchars($k['adsid']); ?></td>
                      <td><?php echo htmlspecialchars($k['shteti']); ?></td>
                      <td>
                        <a class="input-custom-css px-3 py-2" href="ads.php?id=<?php echo $k['id']; ?>" style="text-decoration: none; text-transform: none;">
                          <i class="fi fi-rr-folder"></i> Hap Listen
                        </a>
                      </td>
                      <td>
                        <button class="btn btn-primary text-white rounded-5 px-2 py-2" data-bs-toggle="modal" data-bs-target="#editModal_<?php echo $k['id']; ?>"><i class="fi fi-rr-edit"></i></button>
                        <button class="btn btn-danger text-white rounded-5 px-2 py-2" onclick="confirmDelete(<?php echo $k['id']; ?>)"><i class="fi fi-rr-trash"></i></button>
                      </td>
                    </tr>

                    <!-- Edit ADS Modal -->
                    <div class="modal fade" id="editModal_<?php echo $k['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel_<?php echo $k['id']; ?>" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <form method="POST" action="">
                            <div class="modal-header">
                              <h5 class="modal-title" id="editModalLabel_<?php echo $k['id']; ?>">Edito Llogarinë e <?php echo htmlspecialchars($k['email']); ?></h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <input type="hidden" name="edit_id" value="<?php echo $k['id']; ?>">
                              <div class="form-group mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control rounded-5 border border-2" value="<?php echo htmlspecialchars($k['email']); ?>" required oninvalid="this.setCustomValidity('Ju lutem plotësoni këtë fushë')" oninput="this.setCustomValidity('')">
                              </div>
                              <div class="form-group mb-3">
                                <label>ADS ID</label>
                                <input type="text" name="adsid" class="form-control rounded-5 border border-2" value="<?php echo htmlspecialchars($k['adsid']); ?>" required oninvalid="this.setCustomValidity('Ju lutem plotësoni këtë fushë')" oninput="this.setCustomValidity('')">
                              </div>
                              <div class="form-group mb-3">
                                <label>Shteti</label>
                                <input type="text" name="shteti" class="form-control rounded-5 border border-2" value="<?php echo htmlspecialchars($k['shteti']); ?>" required oninvalid="this.setCustomValidity('Ju lutem plotësoni këtë fushë')" oninput="this.setCustomValidity('')">
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button>
                              <input type="submit" class="input-custom-css px-3 py-2" name="update" value="Përditso">
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- ADS List for Mobile and Tablets -->
        <div class="d-block d-md-block d-lg-none">
          <ul class="list-group">
            <?php
            $kueri = $conn->query("SELECT * FROM ads ORDER BY id ASC");
            while ($k = mysqli_fetch_assoc($kueri)) {
            ?>
              <li class="list-group-item rounded-5 mb-2">
                <div class="row text-dark">
                  <div class="col-12 mb-2">
                    <strong>Email:</strong> <?php echo htmlspecialchars($k['email']); ?>
                  </div>
                  <div class="col-12 mb-2">
                    <strong>ADS ID:</strong> <?php echo htmlspecialchars($k['adsid']); ?>
                  </div>
                  <div class="col-12 mb-2">
                    <strong>Shteti:</strong> <?php echo htmlspecialchars($k['shteti']); ?>
                  </div>
                  <div class="col-12 mb-2">
                    <a class="input-custom-css px-3 py-2 me-2" href="ads.php?id=<?php echo $k['id']; ?>" style="text-decoration: none; text-transform: none;">
                      <i class="fi fi-rr-folder"></i> Hap Listen
                    </a>
                    <button class="btn btn-primary text-white rounded-5 px-3 py-2 me-2" data-bs-toggle="modal" data-bs-target="#editModal_<?php echo $k['id']; ?>"><i class="fi fi-rr-edit"></i></button>
                    <button class="btn btn-danger text-white rounded-5 px-3 py-2" onclick="confirmDelete(<?php echo $k['id']; ?>)"><i class="fi fi-rr-trash"></i></button>
                  </div>
                </div>
              </li>

              <!-- Edit ADS Modal for Mobile -->
              <div class="modal fade" id="editModal_<?php echo $k['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel_<?php echo $k['id']; ?>" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <form method="POST" action="">
                      <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel_<?php echo $k['id']; ?>">Edito Llogarinë e <?php echo htmlspecialchars($k['email']); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <input type="hidden" name="edit_id" value="<?php echo $k['id']; ?>">
                        <div class="form-group mb-3">
                          <label>Email</label>
                          <input type="email" name="email" class="form-control rounded-5 border border-2" value="<?php echo htmlspecialchars($k['email']); ?>" required oninvalid="this.setCustomValidity('Ju lutem plotësoni këtë fushë')" oninput="this.setCustomValidity('')">
                        </div>
                        <div class="form-group mb-3">
                          <label>ADS ID</label>
                          <input type="text" name="adsid" class="form-control rounded-5 border border-2" value="<?php echo htmlspecialchars($k['adsid']); ?>" required oninvalid="this.setCustomValidity('Ju lutem plotësoni këtë fushë')" oninput="this.setCustomValidity('')">
                        </div>
                        <div class="form-group mb-3">
                          <label>Shteti</label>
                          <input type="text" name="shteti" class="form-control rounded-5 border border-2" value="<?php echo htmlspecialchars($k['shteti']); ?>" required oninvalid="this.setCustomValidity('Ju lutem plotësoni këtë fushë')" oninput="this.setCustomValidity('')">
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button>
                        <input type="submit" class="input-custom-css px-3 py-2" name="update" value="Përditso">
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            <?php } ?>
          </ul>
        </div>
      <?php else: ?>
        <!-- Klientet for Specific ADS Section -->
        <nav class="bg-white px-2 rounded-5 mb-3" style="width: fit-content;" aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a class="text-reset" href="ads.php">Klientët</a></li>
            <li class="breadcrumb-item"><a class="text-reset" href="ads.php">Llogaritë e ADS</a></li>
            <li class="breadcrumb-item active" aria-current="page">Klientet e <?php echo htmlspecialchars($adsDetails['email']); ?></li>
          </ol>
        </nav>

        <!-- Button to Go Back -->
        <button type="button" class="input-custom-css px-3 py-2 mb-3" onclick="history.back()">
          <i class="fi fi-rr-arrow-small-left"></i> Kthehu Mbrapa
        </button>

        <!-- Klientet List Table -->
        <div class="card rounded-5 shadow-sm d-none d-md-none d-lg-block">
          <div class="card-body">
            <div class="table-responsive">
              <table id="klientetTable" class="table w-100 table-bordered">
                <thead class="bg-light">
                  <tr>
                    <th class="text-dark">Emri</th>
                    <th class="text-dark">Emri Artistik</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($loa = mysqli_fetch_assoc($merrl)) { ?>
                    <tr>
                      <td><?php echo htmlspecialchars($loa['emri']); ?></td>
                      <td><?php echo htmlspecialchars($loa['emriart']); ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Klientet List for Mobile and Tablets -->
        <div class="d-block d-md-block d-lg-none">
          <ul class="list-group">
            <?php
            // Reset the result pointer and fetch again for mobile view
            $stmt_klientet_mobile = $conn->prepare("SELECT * FROM klientet WHERE ads = ?");
            $stmt_klientet_mobile->bind_param("i", $idof);
            $stmt_klientet_mobile->execute();
            $merrl_mobile = $stmt_klientet_mobile->get_result();
            while ($loa = mysqli_fetch_assoc($merrl_mobile)) {
            ?>
              <li class="list-group-item rounded-5 mb-2">
                <div class="row text-dark">
                  <div class="col-12 mb-2">
                    <strong>Emri:</strong> <?php echo htmlspecialchars($loa['emri']); ?>
                  </div>
                  <div class="col-12 mb-2">
                    <strong>Emri Artistik:</strong> <?php echo htmlspecialchars($loa['emriart']); ?>
                  </div>
                </div>
              </li>
            <?php } ?>
            <?php $stmt_klientet_mobile->close(); ?>
          </ul>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include 'partials/footer.php'; ?>

<!-- JavaScript Section -->
<script>
  // Delete Confirmation
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
        window.location.href = 'ads.php?delete=' + id;
      }
    });
  }

  // Initialize DataTables for ADS Table
  <?php if (!$viewKlientet): ?>
    $('#adsTable').DataTable({
      responsive: true,
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
          titleAttr: "Printo tabelën",
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
      fixedHeader: true,
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
      },
      stripeClasses: ['stripe-color']
    });

    // Initialize DataTables for Klientet Table
  <?php elseif ($viewKlientet): ?>
    $('#klientetTable').DataTable({
      responsive: true,
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
          titleAttr: "Printo tabelën",
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
      fixedHeader: true,
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
      },
      stripeClasses: ['stripe-color']
    });
  <?php endif; ?>
</script>