<?php
ob_start();
require_once 'partials/header.php';
require_once 'conn-d.php';  // Assuming you have a separate database connection file

// Update 'rrogat' table if 'id' is set in GET parameters
if (isset($_GET['id'])) {
  $gid = intval($_GET['id']);
  $stmt = $conn->prepare("UPDATE rrogat SET lexuar = 1 WHERE id = ?");
  $stmt->bind_param("i", $gid);
  $stmt->execute();
}

// Handle form submission
if (isset($_POST['ruaj'])) {
  $emri = mysqli_real_escape_string($conn, $_POST['emri']);
  $data = mysqli_real_escape_string($conn, $_POST['data']);
  $fatura = mysqli_real_escape_string($conn, $_POST['fatura']);
  $gjendjaFatures = mysqli_real_escape_string($conn, $_POST['gjendjaFatures']);

  // Fetch full name from 'facebook' table
  $stmt = $conn->prepare("SELECT emri_mbiemri FROM facebook WHERE emri_mbiemri = ?");
  $stmt->bind_param("s", $emri);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  $emrifull = $row['emri_mbiemri'] ?? '';

  // Insert into 'faturafacebook' table
  $stmt = $conn->prepare("INSERT INTO faturafacebook (emri, emrifull, data, fatura, gjendja_e_fatures) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("sssss", $emri, $emrifull, $data, $fatura, $gjendjaFatures);

  if ($stmt->execute()) {
    header("Refresh: 5; URL=shitjeFacebook.php?fatura=" . urlencode($fatura));
    exit();
  } else {
    echo "Error: " . $stmt->error;
  }
}

// Handle deletion
if (isset($_GET['fshij'])) {
  $fshijid = mysqli_real_escape_string($conn, $_GET['fshij']);

  // Start transaction
  $conn->begin_transaction();

  try {
    // Move data to 'draft' table
    $stmt = $conn->prepare("INSERT INTO draft (emri, data, fatura) SELECT emri, data, fatura FROM faturafacebook WHERE fatura = ?");
    $stmt->bind_param("s", $fshijid);
    $stmt->execute();

    // Delete from 'faturafacebook'
    $stmt = $conn->prepare("DELETE FROM faturafacebook WHERE fatura = ?");
    $stmt->bind_param("s", $fshijid);
    $stmt->execute();

    // Move data to 'shitjedraftfacebook' and delete from 'shitjefacebook'
    $stmt = $conn->prepare("INSERT INTO shitjedraftfacebook SELECT * FROM shitjefacebook WHERE fatura = ?");
    $stmt->bind_param("s", $fshijid);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM shitjefacebook WHERE fatura = ?");
    $stmt->bind_param("s", $fshijid);
    $stmt->execute();

    // Commit transaction
    $conn->commit();
  } catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo '<script>alert("Error: ' . $e->getMessage() . '");</script>';
  }
}
?>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Fatur e re</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="">
          <label for="emri">Emri & Mbiemri</label>
          <select name="emri" id="emri" class="form-select shadow-sm rounded-5 my-2">
            <?php
            $gsta = $conn->query("SELECT * FROM facebook");
            while ($gst = mysqli_fetch_array($gsta)) {
            ?>
              <option value="<?php echo $gst['id']; ?>"><?php echo $gst['emri_mbiemri']; ?></option>
            <?php } ?>
          </select>
          <label for="datas">Data:</label>
          <input type="text" name="data" class="form-control shadow-sm rounded-5 my-2" value="<?php echo date("Y-m-d"); ?>">
          <label for="imei">Fatura:</label>
          <input type="text" name="fatura" class="form-control shadow-sm rounded-5 my-2" value="<?php echo date('dmYhis'); ?>" readonly>
          <?php
          if ($_SESSION['acc'] == '1') {
          ?>
            <label for="gjendjaFatures">Zgjidhni gjendjen e fatur&euml;s:</label>
            <select name="gjendjaFatures" id="gjendjaFatures" class="form-select shadow-sm rounded-5 my-2">
              <option value="Rregullt">Rregullt</option>
              <option value="Pa rregullt">Pa rregullt</option>
            </select>
          <?php
          } else {
          }
          ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mbylle</button>
        <input type="submit" class="btn btn-primary" name="ruaj" value="Ruaj">
        </form>
      </div>
    </div>
  </div>
</div>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <nav class="bg-white px-2 rounded-5" style="width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a class="text-reset" style="text-decoration: none;">
              Facebook
            </a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">
            <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">
              Krijo faturë ( Facebook )
            </a>
          </li>
      </nav>
      <div class="modal fade" id="pagesmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Shto Pages&euml;</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form id="user_form">
                <label>Fatura:</label>
                <input type="text" name="fatura" id="fatura" class="form-control shadow-sm rounded-5 my-2" value="" placeholder="Sh&euml;no numrin e fatur&euml;s">
                <label>P&euml;rshkrimi:</label>
                <textarea class="form-control shadow-sm rounded-5 my-2" name="pershkrimi" id="pershkrimi"></textarea>
                <label>Shuma:</label>
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text">&euro;</span>
                  </div>
                  <input type="text" name="shuma" id="shuma" class="form-control shadow-sm rounded-5 my-2" placeholder="0" aria-label="Shuma">
                  <div class="input-group-append">
                    <span class="input-group-text">.00</span>
                  </div>
                </div>
                <label>M&euml;nyra e pages&euml;s</label>
                <select name="menyra" id="menyra" class="form-select shadow-sm rounded-5 my-2">
                  <option value="BANK">BANK</option>
                  <option value="CASH">CASH</option>
                  <option value="PayPal">PayPal</option>
                  <option value="Ria">Ria</option>
                  <option value="MoneyGram">Money Gram</option>
                  <option value="WesternUnion">Western Union</option>
                </select>
                <label>Data</label>
                <input type="text" name="data" id="data" value="<?php echo date("Y-m-d"); ?>" class="form-control shadow-sm rounded-5 my-2">
                <input type="checkbox" name="kategorizimi[]" value="null" style="display:none;">
                <table class="table table-bordered mt-3">
                  <thead class="bg-light">
                    <tr>
                      <th>Emri i kategoris&euml;</th>
                      <th>Zgjedhe</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>
                        Biznes
                      </td>
                      <td>
                        <input type="checkbox" name="kategorizimi[]" value="Biznes">
                      </td>
                    </tr>
                    <tr>
                      <td>
                        Personal
                      </td>
                      <td>
                        <input type="checkbox" name="kategorizimi[]" value="Personal">
                      </td>
                    </tr>
                  </tbody>
                </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hiqe</button>
              <input type="button" name="ruajp" id="btnruaj" class="btn btn-primary" value="Ruaj">
              </form>
            </div>
          </div>
        </div>
      </div>
      <div class="mb-3">
        <button style="text-transform: none;" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fi fi-rr-add-document fa-lg"></i>&nbsp; Fatur&euml; e re</button>
        <button style="text-transform: none;" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#pagesmodal"><i class="fi fi-rr-badge-dollar fa-lg"></i>&nbsp; Pages&euml; e re</button>
      </div>
      <div class="card shadow-sm rounded-5">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <label for="max" class="form-label" style="font-size: 14px;">Prej:</label>
              <p class="text-muted" style="font-size: 10px;">Zgjidhni një diapazon fillues të
                dates për të filtruar rezultatet</p>
              <div class="input-group rounded-5">
                <span class="input-group-text border-0" style="background-color: white;cursor: pointer;"><i class="fi fi-rr-calendar"></i></span><input type="date" id="start_date1" name="start_date1" class="form-control rounded-5" placeholder="Zgjidhni datën e fillimit" style="cursor: pointer;" readonly>
              </div>
            </div>
            <div class="col">
              <label for="max" class="form-label" style="font-size: 14px;">Deri:</label>
              <p class="text-muted" style="font-size: 10px;">Zgjidhni një diapazon mbarues të
                dates për të filtruar rezultatet.</p>
              <div class="input-group rounded-5">
                <span class="input-group-text border-0" style="background-color: white;cursor: pointer;"><i class="fi fi-rr-calendar"></i></span><input type="text" id="end_date1" name="end_date1" class="form-control rounded-5" placeholder="Zgjidhni datën e mbarimit" style="cursor: pointer;" readonly>
              </div>
            </div>
          </div>
          <br>
          <div class="table-responsive">
            <?php
            include('conn-d.php');
            $query = "SELECT * FROM faturafacebook";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $result = $stmt->get_result();
            ?>
            <table id="employeeList" class="table w-100 table-bordered">
              <thead class="bg-light">
                <tr>
                  <th class="text-dark">Emri</th>
                  <th class="text-dark">Fatura</th>
                  <th class="text-dark">Data</th>
                  <th class="text-dark">Shuma</th>
                  <th class="text-dark">Sh.Paguar</th>
                  <th class="text-dark">Obligim</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
            <script type="text/javascript">
              $(document).ready(function() {
                $('#btnruaj').click(function() {
                  var data = $('#user_form').serialize() + '&btn_save=btn_save';
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
                        timer: 1500 // auto close timer in milliseconds
                      }).then(() => {
                        // Do any other required actions here after clicking 'OK'
                      });
                      setTimeout(function() {
                        // Close the Swal modal after a certain time (e.g., 5 seconds)
                        $('#pagesmodal').modal('hide'); // Close the Bootstrap modal after a certain time (e.g., 5 seconds)
                        // Do any other required actions here after the modals are closed
                      }, 1800);
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
              });
            </script>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include 'partials/footer.php'; ?>
<script>
  $(document).ready(function() {
    // Flatpickr date picker initialization options
    const flatpickrOptions = {
      dateFormat: "Y-m-d",
      allowInput: true,
      locale: "sq"
    };
    // Initialize start and end date fields
    const dateFields = [{
        start: '#start_date1',
        end: '#end_date1'
      },
      {
        start: '#startDateBiznes',
        end: '#endDateBiznes'
      }
    ];
    dateFields.forEach(pair => {
      if ($(pair.start).length && $(pair.end).length) {
        flatpickr(pair.start, {
          ...flatpickrOptions,
          onChange: function(selectedDates, dateStr, instance) {
            $(pair.end)[0]._flatpickr.set('minDate', dateStr);
          }
        });
        flatpickr(pair.end, {
          ...flatpickrOptions,
          maxDate: 'today',
          onChange: function(selectedDates, dateStr, instance) {
            $(pair.start)[0]._flatpickr.set('maxDate', dateStr);
          }
        });
      } else {
        console.error(`One or both elements not found: ${pair.start}, ${pair.end}`);
      }
    });
    var dataTables = $('#employeeList').DataTable({
      responsive: false,
      "order": [
        [3, "desc"]
      ],
      serverSide: false,
      processing: true,
      "ajax": {
        url: "api/get_methods/get_faturaFacebook.php",
        type: "POST",
        data: function(d) {
          d.start_date = $('#start_date1').val();
          d.end_date = $('#end_date1').val();
        }
      },
      "columns": [{
          "data": "emrifull",
        },
        {
          "data": "fatura"
        },
        {
          "data": "data"
        },
        {
          "data": "shuma"
        },
        {
          "data": "shuma_e_paguar"
        },
        {
          "data": "obli"
        },
        {
          "data": "aksion"
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
        text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
        titleAttr: 'Eksporto tabelen ne formatin PDF',
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
      }, {
        extend: 'copyHtml5',
        text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
        titleAttr: 'Kopjo tabelen ne formatin Clipboard',
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
      }, {
        extend: 'excelHtml5',
        text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
        titleAttr: 'Eksporto tabelen ne formatin Excel',
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
        exportOptions: {
          modifier: {
            search: 'applied',
            order: 'applied',
            page: 'all'
          }
        }
      }, {
        extend: 'print',
        text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
        titleAttr: 'Printo tabel&euml;n',
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
      }, ],
      initComplete: function() {
        var btns = $('.dt-buttons');
        btns.addClass('');
        btns.removeClass('dt-buttons btn-group');
        var lengthSelect = $('div.dataTables_length select');
        lengthSelect.addClass('form-select'); // add Bootstrap form-select class
        lengthSelect.css({
          'width': 'auto', // adjust width to fit content
          'margin': '0 8px', // add some margin around the element
          'padding': '0.375rem 1.75rem 0.375rem 0.75rem', // adjust padding to match Bootstrap's styles
          'line-height': '1.5', // adjust line-height to match Bootstrap's styles
          'border': '1px solid #ced4da', // add border to match Bootstrap's styles
          'border-radius': '0.25rem', // add border radius to match Bootstrap's styles
        }); // adjust width to fit content
      },
      fixedHeader: true,
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
      },
      stripeClasses: ['stripe-color'],
    });
    // Add event listener for date filter
    $('#start_date1, #end_date1').on('change', function() {
      dataTables.ajax.reload();
    });
    $(document).on('click', '.delete', function() {
      var id = $(this).attr("id");
      Swal.fire({
        title: 'A jeni i sigurt q&euml; doni ta fshini k&euml;t&euml;?',
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
              id: id
            },
            success: function(data) {
              Swal.fire({
                title: 'Fatura &euml;sht&euml; fshir&euml; me sukses',
                icon: 'success',
                showConfirmButton: false,
                timer: 2000
              });
              $('#employeeList').DataTable().ajax.reload(null, false); // Reload the DataTable
            }
          });
        }
      });
    });
    $('#btnruaj').click(function() {
      $('#employeeList').DataTable().ajax.reload();
    });
    $(document).on('click', '.open-modal', function() {
      // Get the fatura value from the parent table cell's text
      var fatura = $(this).parent().text().trim();
      var shuma = $(this).parent().next().next().text().trim();
      var shuma_e_paguar = $(this).parent().next().next().next().text().trim();
      var oblgimi = $(this).parent().next().next().next().next().text().trim();
      $('#fatura').val(fatura);
      $('#shuma').val(shuma);
      if (shuma_e_paguar == 0) {
        $('#shuma').val(shuma);
      } else {
        $('#shuma').val(oblgimi);
      }
      var myModal = new bootstrap.Modal(document.getElementById('pagesmodal'));
      myModal.show();
    });
  });
</script>