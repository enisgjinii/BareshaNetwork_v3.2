<?php
ob_start();
require_once 'partials/header.php';
require_once 'conn-d.php';

function executePreparedStatement($conn, $query, $types = '', $params = [])
{
  $stmt = $conn->prepare($query);
  if (!$stmt) throw new Exception($conn->error);
  if ($types && $params) $stmt->bind_param($types, ...$params);
  if (!$stmt->execute()) throw new Exception($stmt->error);
  return $stmt;
}

if (isset($_GET['id'])) {
  $gid = intval($_GET['id']);
  try {
    executePreparedStatement($conn, "UPDATE rrogat SET lexuar = 1 WHERE id = ?", "i", [$gid]);
  } catch (Exception $e) {
    echo '<script>alert("Error updating record: ' . htmlspecialchars($e->getMessage()) . '");</script>';
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ruaj'])) {
  $emri = $_POST['emri'] ?? '';
  $data = $_POST['data'] ?? '';
  $fatura = $_POST['fatura'] ?? '';
  $gjendjaFatures = $_POST['gjendjaFatures'] ?? '';
  try {
    $stmt = executePreparedStatement($conn, "SELECT emri_mbiemri FROM facebook WHERE id = ?", "i", [intval($emri)]);
    $emrifull = $stmt->get_result()->fetch_assoc()['emri_mbiemri'] ?? '';
    executePreparedStatement($conn, "INSERT INTO faturafacebook (emri, emrifull, data, fatura, gjendja_e_fatures) VALUES (?, ?, ?, ?, ?)", "sssss", [$emri, $emrifull, $data, $fatura, $gjendjaFatures]);
    header("Refresh: 5; URL=shitjeFacebook.php?fatura=" . urlencode($fatura));
    exit();
  } catch (Exception $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
  }
}

if (isset($_GET['fshij'])) {
  $fshijid = $_GET['fshij'];
  $conn->begin_transaction();
  try {
    $queries = [
      ["INSERT INTO draft (emri, data, fatura) SELECT emri, data, fatura FROM faturafacebook WHERE fatura = ?", "s"],
      ["DELETE FROM faturafacebook WHERE fatura = ?", "s"],
      ["INSERT INTO shitjedraftfacebook SELECT * FROM shitjefacebook WHERE fatura = ?", "s"],
      ["DELETE FROM shitjefacebook WHERE fatura = ?", "s"]
    ];
    foreach ($queries as [$sql, $type]) executePreparedStatement($conn, $sql, $type, [$fshijid]);
    $conn->commit();
  } catch (Exception $e) {
    $conn->rollback();
    echo '<script>alert("Error during deletion: ' . htmlspecialchars($e->getMessage()) . '");</script>';
  }
}
?>
<div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Faturë e Re</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <label for="emri">Emri & Mbiemri</label>
          <select name="emri" id="emri" class="form-select my-2">
            <?php
            $users = $conn->query("SELECT id, emri_mbiemri FROM facebook");
            while ($user = $users->fetch_assoc()) echo "<option value=\"{$user['id']}\">" . htmlspecialchars($user['emri_mbiemri']) . "</option>";
            ?>
          </select>
          <label for="data">Data:</label>
          <input type="date" name="data" id="data" class="form-control my-2" value="<?= date("Y-m-d"); ?>">
          <label for="fatura">Fatura:</label>
          <input type="text" name="fatura" id="fatura" class="form-control my-2" value="<?= date('dmYhis'); ?>" readonly>
          <label for="gjendjaFatures">Gjendja e faturës:</label>
          <select name="gjendjaFatures" id="gjendjaFatures" class="form-select my-2">
            <?php
            foreach (["Rregullt", "Pa rregullt"] as $option) echo "<option value=\"" . htmlspecialchars($option) . "\">" . htmlspecialchars($option) . "</option>";
            ?>
          </select>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mbylle</button>
          <button type="submit" class="input-custom-css px-3 py-2 " name="ruaj">Ruaj</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="pagesmodal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="user_form">
        <div class="modal-header">
          <h5 class="modal-title">Shto Pagesë</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <label for="fatura">Fatura:</label>
          <input type="text" name="fatura" id="fatura" class="form-control my-2" placeholder="Shëno numrin e faturës">
          <label for="pershkrimi">Përshtkrimi:</label>
          <textarea name="pershkrimi" id="pershkrimi" class="form-control my-2"></textarea>
          <label for="shuma">Shuma:</label>
          <div class="input-group mb-3">
            <span class="input-group-text">&euro;</span>
            <input type="number" name="shuma" id="shuma" class="form-control" placeholder="0" step="0.01">
            <span class="input-group-text">.00</span>
          </div>
          <label for="menyra">Mënyra e pagesës</label>
          <select name="menyra" id="menyra" class="form-select my-2">
            <?php
            foreach (["BANK", "CASH", "PayPal", "Ria", "MoneyGram", "WesternUnion"] as $option) echo "<option value=\"" . htmlspecialchars($option) . "\">" . htmlspecialchars($option) . "</option>";
            ?>
          </select>
          <label for="data">Data</label>
          <input type="date" name="data" id="data" class="form-control my-2" value="<?= date("Y-m-d"); ?>">
          <input type="hidden" name="kategorizimi[]" value="null">
          <table class="table table-bordered mt-3">
            <thead>
              <tr>
                <th>Emri i kategorisë</th>
                <th>Zgjidhe</th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach (["Biznes", "Personal"] as $kategoria) {
                echo "<tr>
                          <td>" . htmlspecialchars($kategoria) . "</td>
                          <td><input type=\"checkbox\" name=\"kategorizimi[]\" value=\"" . htmlspecialchars($kategoria) . "\"></td>
                        </tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hiqe</button>
          <button type="button" class="input-custom-css px-3 py-2 " id="btnruaj">Ruaj</button>
        </div>
      </form>
    </div>
  </div>
</div>



<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <nav class="breadcrumb bg-white px-2 rounded-5 border" >
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a class="text-reset text-decoration-none">Facebook</a></li>
          <li class="breadcrumb-item active"><a href="<?= htmlspecialchars(__FILE__); ?>" class="text-reset text-decoration-none">Krijo faturë (Facebook)</a></li>
        </ol>
      </nav>
      <div class="mb-3">
        <button class="input-custom-css px-3 py-2 me-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
          <i class="fi fi-rr-add-document fa-lg"></i> Faturë e re
        </button>
        <button class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#pagesmodal">
          <i class="fi fi-rr-badge-dollar fa-lg"></i> Pagesë e re
        </button>
      </div>
      <div class="card shadow-sm rounded-5 mt-3">
        <div class="card-body">
          <div class="row">
            <?php
            $dateFilters = [
              ['label' => 'Prej:', 'id' => 'start_date1', 'placeholder' => 'Zgjidhni datën e fillimit'],
              ['label' => 'Deri:', 'id' => 'end_date1', 'placeholder' => 'Zgjidhni datën e mbarimit']
            ];
            foreach ($dateFilters as $filter) {
              echo "<div class=\"col\">
                        <label for=\"{$filter['id']}\" class=\"form-label\">{$filter['label']}</label>
                        <p class=\"text-muted\" style=\"font-size: 10px;\">Zgjidhni një diapazon {$filter['label']} dates për të filtruar rezultatet</p>
                        <div class=\"input-group rounded-5\">
                          <span class=\"input-group-text bg-white\"><i class=\"fi fi-rr-calendar\"></i></span>
                          <input type=\"date\" id=\"{$filter['id']}\" name=\"{$filter['id']}\" class=\"form-control\" placeholder=\"{$filter['placeholder']}\" readonly>
                        </div>
                      </div>";
            }
            ?>
          </div>
          <div class="table-responsive mt-3">
            <table id="employeeList" class="table table-bordered w-100">
              <thead class="bg-light">
                <tr>
                  <th>Emri</th>
                  <th>Fatura</th>
                  <th>Data</th>
                  <th>Shuma</th>
                  <th>Sh.Paguar</th>
                  <th>Obligim</th>
                  <th>Aksion</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include 'partials/footer.php'; ?>
<script>
  $(document).ready(function() {
    const flatpickrOptions = {
      dateFormat: "Y-m-d",
      allowInput: true,
      locale: "sq"
    };

    [{
      start: '#start_date1',
      end: '#end_date1'
    }, {
      start: '#startDateBiznes',
      end: '#endDateBiznes'
    }].forEach(pair => {
      if ($(pair.start).length && $(pair.end).length) {
        flatpickr(pair.start, {
          ...flatpickrOptions,
          onChange: (selectedDates, dateStr) => {
            $(pair.end)[0]._flatpickr.set('minDate', dateStr);
          }
        });
        flatpickr(pair.end, {
          ...flatpickrOptions,
          maxDate: 'today',
          onChange: (selectedDates, dateStr) => {
            $(pair.start)[0]._flatpickr.set('maxDate', dateStr);
          }
        });
      }
    });

    const dataTables = $('#employeeList').DataTable({
      responsive: false,
      order: [
        [3, "desc"]
      ],
      serverSide: false,
      processing: true,
      ajax: {
        url: "api/get_methods/get_faturaFacebook.php",
        type: "POST",
        data: function(d) {
          d.start_date = $('#start_date1').val();
          d.end_date = $('#end_date1').val();
        }
      },
      columns: [{
          data: "emrifull"
        },
        {
          data: "fatura"
        },
        {
          data: "data"
        },
        {
          data: "shuma"
        },
        {
          data: "shuma_e_paguar"
        },
        {
          data: "obli"
        },
        {
          data: "aksion"
        }
      ],
      lengthMenu: [
        [10, 25, 50, -1],
        [10, 25, 50, "Te gjitha"]
      ],
      dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
        "<'row'<'col-md-12'tr>>" +
        "<'row'<'col-md-6'><'col-md-6'p>>",
      buttons: [{
          extend: 'pdfHtml5',
          text: '<i class="fi fi-rr-file-pdf fa-lg"></i> PDF',
          titleAttr: 'Eksporto tabelen ne formatin PDF',
          className: 'btn btn-light btn-sm border me-2 rounded-5'
        },
        {
          extend: 'copyHtml5',
          text: '<i class="fi fi-rr-copy fa-lg"></i> Kopjo',
          titleAttr: 'Kopjo tabelen ne formatin Clipboard',
          className: 'btn btn-light btn-sm border me-2 rounded-5'
        },
        {
          extend: 'excelHtml5',
          text: '<i class="fi fi-rr-file-excel fa-lg"></i> Excel',
          titleAttr: 'Eksporto tabelen ne formatin Excel',
          className: 'btn btn-light btn-sm border me-2 rounded-5',
          exportOptions: {
            modifier: {
              search: 'applied',
              order: 'applied',
              page: 'all'
            }
          }
        },
        {
          extend: 'print',
          text: '<i class="fi fi-rr-print fa-lg"></i> Printo',
          titleAttr: 'Printo tabelën',
          className: 'btn btn-light btn-sm border me-2 rounded-5'
        }
      ],
      initComplete: function() {
        $('.dt-buttons').removeClass('dt-buttons btn-group').addClass('');
        $('div.dataTables_length select').addClass('form-select').css({
          'width': 'auto',
          'margin': '0 8px',
          'padding': '0.375rem 1.75rem 0.375rem 0.75rem',
          'line-height': '1.5',
          'border': '1px solid #ced4da',
          'border-radius': '0.25rem'
        });
      },
      fixedHeader: true,
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json"
      },
      stripeClasses: ['stripe-color']
    });

    $('#start_date1, #end_date1').on('change', function() {
      dataTables.ajax.reload();
    });

    $(document).on('click', '.delete', function() {
      const id = $(this).attr("id");
      Swal.fire({
        title: 'A jeni i sigurt që doni ta fshini këtë?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Po, fshije'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: "api/deletefat.php",
            method: "POST",
            data: {
              id
            },
            success: function() {
              Swal.fire({
                title: 'Fatura është fshirë me sukses',
                icon: 'success',
                showConfirmButton: false,
                timer: 2000
              });
              dataTables.ajax.reload(null, false);
            }
          });
        }
      });
    });

    $('#btnruaj').click(function() {
      const data = $('#user_form').serialize() + '&btn_save=btn_save';
      $.ajax({
        url: 'api/post_methods/post_pagesave_facebook.php',
        type: 'POST',
        data: data,
        success: function(response) {
          Swal.fire({
              title: 'Success',
              text: response,
              icon: 'success',
              confirmButtonText: 'OK',
              timer: 1500
            })
            .then(() => {
              $('#pagesmodal').modal('hide');
              dataTables.ajax.reload();
            });
        },
        error: function(xhr, status, error) {
          Swal.fire({
            title: 'Error',
            text: error,
            icon: 'error',
            confirmButtonText: 'OK',
            timer: 1500
          });
        }
      });
    });

    $(document).on('click', '.open-modal', function() {
      const row = $(this).closest('tr');
      const fatura = row.find('td').eq(1).text().trim();
      const shuma = row.find('td').eq(3).text().trim();
      const shuma_e_paguar = row.find('td').eq(4).text().trim();
      const obligim = row.find('td').eq(5).text().trim();
      $('#fatura').val(fatura);
      $('#shuma').val(shuma_e_paguar == 0 ? shuma : obligim);
      $('#pagesmodal').modal('show');
    });
  });
</script>