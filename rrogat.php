<?php
include 'partials/header.php';
ob_start();

if (isset($_GET['id'])) {
  $gid = mysqli_real_escape_string($conn, $_GET['id']);
  $updateStatement = $conn->prepare("UPDATE rrogat SET lexuar='1' WHERE id=?");
  $updateStatement->bind_param("i", $gid);
  $result = $updateStatement->execute();
  if (!$result) {
    echo '<script>
            Swal.fire({
                icon: "error",
                title: "Gabim!",
                text: "Gabim gjatë përditësimit të të dhënave",
            });
          </script>';
  }
  $updateStatement->close();
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
  $kont = ($kontributi / 100) * $shuma;
  $kont2 = ($kontributi2 / 100) * $shuma;
  $prepaid = isset($_POST['parapagim']) ? 1 : 0;

  $pagaa = $shuma - $kont;
  if ($pagaa <= 80) {
    $p80 = $pagaa;
    $p80_250 = 0;
  } else {
    $p80 = 80;
    if ($pagaa - $p80 <= 170) {
      $p80_250 = $pagaa - $p80;
    } else {
      $p80_250 = 170;
    }
  }

  if ($pagaa - $p80 - $p80_250 <= 200) {
    $p250_450 = $pagaa - $p80 - $p80_250;
  } else {
    $p250_450 = 200;
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

  $insertStatement = $conn->prepare("INSERT INTO rrogat (stafi, muaji, viti, shuma, kontributi, kontributi2, tatimi, neto, data, pagesa, lexuar, parapagim) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '0', ?)");
  $insertStatement->bind_param("ssssssssssi", $stafi, $muaji, $viti, $shuma, $kont, $kont2, $tatimi, $neto, $data, $pagesa, $prepaid);
  $result = $insertStatement->execute();
  if (!$result) {
    echo '<script>
            Swal.fire({
                icon: "error",
                title: "Gabim!",
                text: "' . $conn->error . '",
            });
          </script>';
  } else {
    echo '<script>
            Swal.fire({
                icon: "success",
                title: "Sukses!",
                text: "Të dhënat janë ruajtur me sukses",
            });
          </script>';
  }
  $insertStatement->close();
}
?>
<div class="main-panel blurred">
  <div class="content-wrapper">
    <div class="container">
      <nav class="bg-white px-2 rounded-5" style="width:fit-content" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Financat</a></li>
          <li class="breadcrumb-item active" aria-current="page"><a href="invoice.php" class="text-reset" style="text-decoration: none;">Pagat</a></li>
        </ol>
      </nav>
      <p id="remainingTime"></p>
      <p id="informationAboutRemainingTime"></p>
      <button type="button" class="input-custom-css px-3 py-2 mb-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
        <i class="fi fi-rr-add" style="font-size: 13px"></i> E re
      </button>
      <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Pagat</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form method="POST" action="">
                <div class="row">
                  <div class="col">
                    <div class="form-group">
                      <label for="emrib" class="form-label">Muaji</label>
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
                  </div>
                  <div class="col">
                    <div class="form-group">
                      <label for="emrit" class="form-label">Viti</label>
                      <select class="form-select" name="viti">
                        <option value="2021">2021</option>
                        <option value="2022">2022</option>
                        <option value="2023">2023</option>
                      </select>
                      <script>
                        new Selectr('select[name="viti"]', {
                          searchable: true,
                        })
                        new Selectr('select[name="muaji"]', {
                          searchable: true,
                        })
                        var currentYear = new Date().getFullYear();
                        var dropdown = document.querySelector('select[name="viti"]');
                        for (var year = currentYear + 1; year <= currentYear + 5; year++) {
                          var option = document.createElement('option');
                          option.value = year;
                          option.textContent = year;
                          if (year === 2024 || year === 2025 || year === 2026 || year === 2027 || year === 2028) {
                            option.disabled = true;
                          }
                          dropdown.appendChild(option);
                        }
                      </script>
                    </div>
                  </div>
                  <div class="col">
                    <div class="form-group">
                      <label for="emri" class="form-label">Zgjidh njërin nga stafi</label>
                      <select name="stafi" id="stafi" class="form-select">
                        <?php
                        $get_employees = $conn->query("SELECT * FROM googleauth");
                        if ($get_employees->num_rows > 0) {
                          while ($row = $get_employees->fetch_assoc()) {
                            echo '<option value="' . $row['id'] . '">' . $row['firstName'] . ' ' . $row['last_name'] . '</option>';
                          }
                        }
                        ?>
                      </select>
                      <script>
                        new Selectr('select[name="stafi"]', {
                          searchable: true,
                        })
                      </script>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col">
                      <label for="salary-display" class="form-label"> Rroga statike</label>
                      <input type="text" class="form-control rounded-5 shadow-sm border border-2" id="salary-display">
                      <script>
                        document.getElementById('stafi').addEventListener('change', function() {
                          var selectedId = this.value;
                          var xhr = new XMLHttpRequest();
                          xhr.open('GET', 'get_salary.php?id=' + selectedId, true);
                          xhr.onload = function() {
                            if (xhr.status == 200) {
                              document.getElementById('salary-display').value = xhr.responseText;
                            }
                          };
                          xhr.send();
                        });
                      </script>
                    </div>
                    <div class="col">
                      <label for="datab" class="form-label">Shuma</label>
                      <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1">€</span>
                        </div>
                        <input type="text" class="form-control" name="shuma" value="0.00">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <div class="form-group">
                      <label for="emrib" class="form-label">Kontributi i punëdhënësit (%)</label>
                      <input type="text" class="form-control rounded-5 shadow-sm" name="kontributi" value="5">
                    </div>
                  </div>
                  <div class="col">
                    <div class="form-group">
                      <label for="emrib" class="form-label">Kontributi i punëtorit (%)</label>
                      <input type="text" class="form-control rounded-5 shadow-sm" name="kontributi2" value="5">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <div class="form-group">
                      <label for="datas" class="form-label">Data e pages&euml;s</label>
                      <input type="date" name="data" class="form-control rounded-5 shadow-sm" value="<?php echo date("Y-m-d"); ?>">
                    </div>
                  </div>
                  <div class="col">
                    <div class="form-group">
                      <label for="imei" class="form-label">Forma e pages&euml;s</label>
                      <select name="forma" class="form-select rounded-5 shadow-sm">
                        <option value="Cash">Cash</option>
                        <option value="Bank">Bank</option>
                      </select>
                      <script>
                        new Selectr('select[name="forma"]', {
                          searchable: true,
                        })
                      </script>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    <div class="form-group">
                      <label for="parapagim" class="form-label">Pagesa e para-kohshme</label><br>
                      <input type="checkbox" name="parapagim" style="width: 20px; height: 20px;">
                    </div>
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
      <div class="card rounded-5">
        <div class="card-body">
          <div class="row">
            <div class="col-12">
              <div class="table-responsive text-dark">
                <table id="table_of_salaries_of_staff" class="table w-100 table-bordered text-dark">
                  <thead class="bg-light">
                    <tr>
                      <th class="text-dark">Stafi</th>
                      <th class="text-dark">Muaji & Viti</th>
                      <th class="text-dark">Bruto</th>
                      <th class="text-dark">Kontributi i punëdhënësit</th>
                      <th class="text-dark">Kontributi i punëtorit</th>
                      <th class="text-dark">Tatimi</th>
                      <th class="text-dark">Neto</th>
                      <th class="text-dark">Data</th>
                      <th class="text-dark">Forma</th>
                      <th class="text-dark">Vepro</th>
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
                        $name = $gstafi['firstName'] . " " . $gstafi['last_name'];
                      } else {
                        $name = "Unknow";
                      }
                    ?>
                      <tr>
                        <td><?php echo $name; ?><br>
                          <?php
                          if ($k['parapagim'] == 1) {
                            echo "<p class='rounded-5 bg-warning text-dark my-2 px-1' style='width:max-content'>Pages e para-kohshme</p>";
                          } else if ($k['parapagim'] == 0) {
                            echo "<p class='rounded-5 bg-success text-white my-2 px-1' style='width:max-content'>Pages me rregull</p>";
                          } else {
                            echo "Mungon informacion";
                          }
                          ?>
                        </td>
                        <td><?php echo $k['muaji']; ?> & <?php echo $k['viti']; ?></td>
                        <td><?php echo $k['shuma']; ?>€</td>
                        <td><?php echo $k['kontributi']; ?>€</td>
                        <td><?php echo $k['kontributi2']; ?>€</td>
                        <td><?php echo $k['tatimi']; ?>€</td>
                        <td><?php echo $k['neto']; ?>€</td>
                        <td><?php echo $k['data']; ?></td>
                        <td><?php echo $k['pagesa']; ?></td>
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
    <h5 class="offcanvas-title" id="offcanvasRightLabel">Përditso të dhënat e rroges</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <form id="editForm">
      <label class="form-label" for="editedMuaji">Muaji</label>
      <input type="text" class="form-control rounded-5 border border-2 mb-2" id="editedMuaji" name="editedMuaji" required>
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
      <button type="button" class="input-custom-css px-3 py-2" id="saveChanges">Ruaj ndryshimet</button>
    </form>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.edit-btn').forEach(function(button) {
      button.addEventListener('click', function() {
        var rowId = this.getAttribute('data-row-id');
        $.ajax({
          type: 'GET',
          url: 'fetch_specificUpdateSalary.php?id=' + rowId,
          success: function(data) {
            $('#editedMuaji').val(data.muaji);
            $('#editedBruto').val(data.shuma);
            $('#editedKontributi').val(data.kontributi);
            $('#editedKontributi2').val(data.kontributi2);
            $('#editedTatimi').val(data.tatimi);
            $('#editedNeto').val(data.neto);
            $('#offcanvasRight').offcanvas('show');
          },
          error: function(xhr, status, error) {
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
  $(document).ready(function() {
    $('#table_of_salaries_of_staff').DataTable({
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
          titleAttr: "Eksporto tabelen ne PDF",
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
        },
        {
          extend: "excelHtml5",
          text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
          titleAttr: "Eksporto tabelen ne Excel",
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
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json"
      },
      stripeClasses: ['stripe-color']
    });
  })
</script>
<script>
  $(document).ready(function() {
    function checkSessionStatus() {
      $.ajax({
        url: 'check-session-status.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
          if (response.authenticated) {
            $('.main-panel').removeClass('blurred');
          } else {
            $('#passwordModal').modal('show');
            $('.main-panel').addClass('blurred');
          }
        }
      });
    }

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
        }
      });
    }
    checkSessionStatus();
    updateRemainingTime();
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
            text: 'Ndodhi një gabim gjatë verifikimit të fjalëkalimit.',
          });
        }
      });
    });
  });
</script>