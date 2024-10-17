<?php
// Përfshij header-in e faqes
include 'partials/header.php';
// Fillon buffer-in e jashtëzakonshëm dhe sesionin
ob_start();
session_start();
// Nëse është caktuar 'id' në GET
if (isset($_GET['id'])) {
  // Përdorimi i shprehjeve të përllogaritura për të siguruar vlerën e 'id'
  $gid = mysqli_real_escape_string($conn, $_GET['id']);
  // Përdorimi i deklaratës së përgatitur për të përditësuar të dhënat në bazën e të dhënave
  $updateStatement = $conn->prepare("UPDATE rrogat SET lexuar='1' WHERE id=?");
  $updateStatement->bind_param("i", $gid);
  $result = $updateStatement->execute();
  // Nëse ndodh ndonjë gabim gjatë përpunimit të kërkesës
  if (!$result) {
    echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Gabim!",
                    text: "Gabim gjatë përditësimit të të dhënave",
                });
              </script>';
  }
  // Mbyll deklaratën e përgatitur
  $updateStatement->close();
}
// Nëse është shtypur butoni me emër 'ruaj' në POST
if (isset($_POST['ruaj'])) {
  // Përdorimi i shprehjeve të përllogaritura për të siguruar vlerat e POST
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
  // Check if the checkbox is checked
  $prepaid = isset($_POST['parapagim']) ? 1 : 0;
  // Kalkulo shumat dhe përpuno përshtatjet e tyre
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
  // Kalkulo përfundimisht shumat e ndryshme dhe përpuno tatimin dhe neton
  $paga0 = $p80;
  $paga1 = $p80_250 * 0.04;
  $paga2 = $p250_450 * 0.08;
  $paga3 = $p450 * 0.1;
  $tatimi = $paga1 + $paga2 + $paga3;
  $neto = $pagaa - $paga1 - $paga2 - $paga3;
  // Përdorimi i deklaratës së përgatitur për të futur të dhënat në bazën e të dhënave
  $insertStatement = $conn->prepare("INSERT INTO rrogat (stafi, muaji, viti, shuma, kontributi, kontributi2, tatimi, neto, data, pagesa, lexuar, parapagim) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '0', ?)");
  $insertStatement->bind_param("ssssssssssi", $stafi, $muaji, $viti, $shuma, $kont, $kont2, $tatimi, $neto, $data, $pagesa, $prepaid);
  $result = $insertStatement->execute();
  // Nëse ndodh ndonjë gabim gjatë përpunimit të kërkesës
  if (!$result) {
    echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Gabim!",
                    text: "' . $conn->error . '",
                });
              </script>';
  } else {
    // Nëse të dhënat ruhen me sukses
    echo '<script>
                Swal.fire({
                    icon: "success",
                    title: "Sukses!",
                    text: "Të dhënat janë ruajtur me sukses",
                });
              </script>';
  }
  // Mbyll deklaratën e përgatitur
  $insertStatement->close();
}
?>
<style>
  .blurred {
    filter: blur(5px);
    /* You can adjust the blur strength as needed */
    pointer-events: none;
    /* Disable pointer events on the blurred element */
  }
</style>
<!-- Modal for password -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 style="font-size: 14px;" id="passwordModalLabel">Vendosni fjalëkalimin për të vazhduar dhe shikuar faqen.</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <label class="form-label" for="password" class="form-label">Fjalëkalimi</label>
        <input type="password" class="form-control rounded-5 shadow-sm" id="password">
      </div>
      <div class="modal-footer">
        <button type="button" class="input-custom-css px-3 py-2" id="submitPassword">Kontrollo</button>
      </div>
    </div>
  </div>
</div>
<div class="main-panel blurred">
  <div class="content-wrapper">
    <div class="container">
      <nav class="bg-white px-2 rounded-5" class="bg-white px-2 rounded-5" style="width:fit-content" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Financat</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page"><a href="invoice.php" class="text-reset" style="text-decoration: none;">
              Pagat
            </a></li>
      </nav>
      <p id="remainingTime"></p>
      <p id="informationAboutRemainingTime"></p>
      <button type="button" class="input-custom-css px-3 py-2 mb-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
        <i class="fi fi-rr-add" style="font-size: 13px"></i> E re
      </button>
      <?php include 'salaries_modal.php'; ?>
      <div class="card rounded-5" style="border-style:1px solid red;">
        <div class="card-body">
          <div class="row">
            <div class="col-12">
              <div class="table-responsive text-dark">
                <table id="table_of_salaries_of_staff" class="table w-100 table-bordered text-dark">
                  <thead class="bg-light">
                    <tr>
                      <th class="text-dark" style="white-space: normal;">Stafi</th>
                      <th class="text-dark" style="white-space: normal;">Muaji & Viti</th>
                      <th class="text-dark" style="white-space: normal;">Bruto</th>
                      <th class="text-dark" style="white-space: normal;">Kontributi i punëdhënësit</th>
                      <th class="text-dark" style="white-space: normal;">Kontributi i punëtorit</th>
                      <th class="text-dark" style="white-space: normal;">Tatimi</th>
                      <th class="text-dark" style="white-space: normal;">Neto</th>
                      <th class="text-dark" style="white-space: normal;">Data</th>
                      <th class="text-dark" style="white-space: normal;">Forma</th>
                      <th class="text-dark" style="white-space: normal;">Vepro</th>
                    </tr>
                  </thead>
                  <tbody class="text-dark">
                    <?php
                    $kueri = $conn->query("SELECT * FROM rrogat ORDER BY id DESC");
                    while ($k = mysqli_fetch_array($kueri)) {
                      $sid = $k['stafi'];
                      $gstaf = $conn->query("SELECT * FROM googleauth WHERE id='$sid'");
                      $gstafi = mysqli_fetch_array($gstaf);
                      if (!is_null($gstafi)) {
                        $name = $gstafi['firstName'];
                        $last_name = $gstafi['last_name'];
                        $name = $name . " " . $last_name;
                      } else {
                        $name = "Unknow";
                      }
                    ?>
                      <tr>
                        <td><?php echo $name; ?> <br>
                          <?php
                          if ($k['parapagim'] == 1) {
                            echo "<p class='rounded-5 bg-warning text-dark my-2 px-1' style='width:max-content'>Pages e para-kohshme</p> ";
                          } else if ($k['parapagim'] == 0) {
                            echo "<p class='rounded-5 bg-success text-white my-2 px-1' style='width:max-content'>Pages me rregull</p> ";
                          } else {
                            echo "Mungon informacion";
                          }
                          ?></td>
                        <td><?php echo $k['muaji']; ?> & <?php echo $k['viti']; ?></td>
                        <td><?php echo $k['shuma']; ?>&euro;</td>
                        <td><?php echo $k['kontributi']; ?>&euro;</td>
                        <td><?php echo $k['kontributi2']; ?>&euro;</td>
                        <td><?php echo $k['tatimi']; ?>&euro;</td>
                        <td><?php echo $k['neto']; ?>&euro;</td>
                        <td><?php echo $k['data']; ?></td>
                        <td><?php echo $k['pagesa']; ?></td>
                        </td>
                        <td>
                          <button class="btn btn-primary text-white rounded-5 px-2 py-2 edit-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" data-row-id="<?php echo $k['id']; ?>">
                            <i class="fi fi-rr-edit"></i>
                          </button>
                          <button class="btn btn-danger text-white rounded-5 px-2 py-2 delete-btn" type="button" data-bs-toggle="modal" data-bs-target="#deleteModal" data-row-id="<?php echo $k['id']; ?>">
                            <i class="fi fi-rr-trash"></i>
                          </button>
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
</div>
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasRightLabel">Përditso të dhënat e rroges se puntorit</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <form id="editForm">
      <!-- <label class="form-label" for="editedName">Stafi</label>
      <input type="text" class="form-control rounded-5 border border-2 mb-2" id="editedName" name="editedName" required> -->
      <label class="form-label" for="editedMuaji">Muaji</label>
      <input type="text" class="form-control rounded-5 border border-2 mb-2" id="editedMuaji" name="editedMuaji" required>
      <!-- Add more input fields based on your data -->
      <label class="form-label" for="editedBruto">Bruto</label>
      <input type="text" class="form-control rounded-5 border border-2 mb-2" id="editedBruto" name="editedBruto" required>
      <label class="form-label" for="editedKontributi">Kontributi</label>
      <input type="text" class="form-control rounded-5 border border-2 mb-2" id="editedKontributi" name="editedKontributi" required>
      <label class="form-label" for="editedKontributi2">Kontributi 2</label>
      <input type="text" class="form-control rounded-5 border border-2 mb-2" id="editedKontributi2" name="editedKontributi2" required>
      <label class="form-label" for="editedTatimi">Tatimi</label>
      <input type="text" class="form-control rounded-5 border border-2 mb-2" id="editedTatimi" name="editedTatimi" required>
      <label class="form-label" for="editedNeto">Neto</label>
      <input type="text" class="form-control rounded-5 border border-2 mb-2" id="editedNeto" name="editedNeto" required>
      <br>
      <button type="button" class="input-custom-css px-3 py-2" id="saveChanges">Ruaj ndryshimet</button>
    </form>
  </div>
</div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.edit-btn').forEach(function(button) {
      button.addEventListener('click', function() {
        var rowId = this.getAttribute('data-row-id');
        // Fetch corresponding data using AJAX
        $.ajax({
          type: 'GET', // Assuming you have a server endpoint to fetch data
          url: 'fetch_specificUpdateSalary.php?id=' + rowId,
          success: function(data) {
            // Pre-populate form fields with the fetched data
            $('#editedName').val(data.stafi);
            $('#editedMuaji').val(data.muaji);
            $('#editedBruto').val(data.shuma);
            $('#editedKontributi').val(data.kontributi);
            $('#editedKontributi2').val(data.kontributi2);
            $('#editedTatimi').val(data.tatimi);
            $('#editedNeto').val(data.neto);
            $('#editedData').val(data.data);
            $('#editedPagesa').val(data.pagesa);
            // ... add more fields as needed
            // Show the off-canvas
            $('#offcanvasRight').offcanvas('show');
          },
          error: function(xhr, status, error) {
            console.error('Error:', xhr.responseText);
            Swal.fire({
              title: 'Error!',
              text: 'An error occurred while fetching data.',
              icon: 'error'
            });
          }
        });
      });
    });
  });
</script>
<?php include 'partials/footer.php'; ?>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-btn').forEach(function(button) {
      button.addEventListener('click', function() {
        var rowId = this.getAttribute('data-row-id');
        // Show SweetAlert2 confirmation dialog
        Swal.fire({
          title: 'A je i sigurt?',
          text: 'Ju nuk do të jeni në gjendje ta ktheni këtë!',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Po, fshije atë!'
        }).then((result) => {
          if (result.isConfirmed) {
            // If confirmed, perform the deletion using AJAX
            $.ajax({
              type: 'POST', // You can also use 'GET' based on your server-side implementation
              url: 'delete_salary.php', // Replace with the actual URL or script
              data: {
                id: rowId
              },
              success: function(response) {
                // Handle the response from the server, e.g., show a success message
                if (response.success) {
                  Swal.fire({
                    title: 'U fshi!',
                    text: 'Rreshti është fshirë.',
                    icon: 'success'
                  });
                  // Remove the row from the table (optional)
                  $(button).closest('tr').remove();
                } else {
                  Swal.fire({
                    title: 'Gabim!',
                    text: 'Ndodhi një gabim gjatë fshirjes së rreshtit.',
                    icon: 'error'
                  });
                }
              },
              error: function(xhr, status, error) {
                // Handle the error, e.g., show an error message
                console.error('Error:', xhr.responseText);
                Swal.fire({
                  title: 'Gabim!',
                  text: 'Ndodhi një gabim gjatë përpunimit të kërkesës suaj.',
                  icon: 'error'
                });
              }
            });
          }
        });
      });
    });
  });
</script>
<script>
  $(document).ready(function() {
    $('#table_of_salaries_of_staff').DataTable({
      // responsive: true,
      "searching": {
        "regex": true
      },
      "paging": true,
      "pageLength": 10,
      dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
        "<'row'<'col-md-12'tr>>" +
        "<'row'<'col-md-6'><'col-md-6'p>>",
      "columnDefs": [{
        "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8],
        "render": function(data, type, row) {
          return type === 'display' && data !== null ? '<div style="white-space: normal;">' + data + '</div>' : data;
        }
      }],
      buttons: [{
          extend: "pdf",
          text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
          titleAttr: "Eksporto tabelen ne formatin PDF",
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
      fixedHeader: true,
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
      },
      stripeClasses: ['stripe-color']
    });
  })
</script>
<script>
  $(document).ready(function() {
    // Function to check session status and update UI
    function checkSessionStatus() {
      $.ajax({
        url: 'check-session-status.php', // Replace with the actual path to your PHP script
        method: 'GET',
        dataType: 'json',
        success: function(response) {
          if (response.authenticated) {
            $('.main-panel').removeClass('blurred');
          } else {
            $('#passwordModal').modal('show');
            $('.main-panel').addClass('blurred');
          }
        },
        error: function() {
          console.log('Gabim në kontrollimin e statusit të sesionit');
        }
      });
    }
    // Function to update remaining time in UI
    function updateRemainingTime() {
      $.ajax({
        url: 'get-remaining-time.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
          var remainingTime = response.remainingTime;
          var minutes = Math.floor(remainingTime / 60);
          var seconds = remainingTime % 60;
          $('#remainingTime').text("Koha e mbetur: " + minutes + " minuta e " + seconds + " sekonda");
        },
        error: function() {
          console.log('Gabim me marrjen e kohës së mbetur');
        }
      });
    }
    // Call the functions initially
    checkSessionStatus();
    updateRemainingTime();
    // Set an interval to update every second
    setInterval(function() {
      checkSessionStatus();
      updateRemainingTime();
    }, 500);
    $('#submitPassword').click(function() {
      var enteredPassword = $('#password').val();
      $.ajax({
        url: 'password-validation.php',
        method: 'POST',
        data: {
          password: enteredPassword
        },
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            $('#passwordModal').modal('hide');
            $('.main-panel').removeClass('blurred');
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Fjalëkalim i pasaktë',
              text: response.message,
            });
          }
        },
        error: function() {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ndodhi një gabim gjatë verifikimit të fjalëkalimit. Ju lutemi provoni përsëri.',
          });
        }
      });
    });
  });
</script>