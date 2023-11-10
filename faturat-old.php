<?php
ob_start();
include 'partials/header.php';
if (isset($_GET['id'])) {
  $gid = $_GET['id'];
  $conn->query("UPDATE rrogat SET lexuar='1' WHERE id='$gid'");
}
if (isset($_POST['ruaj'])) {
  $emri = mysqli_real_escape_string($conn, $_POST['emri']);
  $merreperemer = $conn->query("SELECT * FROM klientet WHERE id='$emri'");
  $merreperemer2 = mysqli_fetch_array($merreperemer);
  $emrifull = $merreperemer2['emri'];
  $data = mysqli_real_escape_string($conn, $_POST['data']);
  $fatura = mysqli_real_escape_string($conn, $_POST['fatura']);
  $gjendjaFatures = mysqli_real_escape_string($conn, $_POST['gjendjaFatures']);
  $linkuIKengesArray = array();
  for ($i = 1; $i <= 5; $i++) {
    $fieldName = "linkuIKenges_" . $i;
    if (isset($_POST[$fieldName])) {
      $linkuIKengesArray[] = mysqli_real_escape_string($conn, $_POST[$fieldName]);
    }
  }
  $kenga = implode(",", $linkuIKengesArray);
  $emri_i_kengetarit = mysqli_real_escape_string($conn, $_POST['emri_i_kengetarit']);
  if ($conn->query("INSERT INTO fatura (emri, emrifull, data, fatura, gjendja_e_fatures) VALUES ('$emri', '$emrifull', '$data','$fatura','$gjendjaFatures')")) {
    header("Location: shitje.php?fatura=$fatura");
    exit;
  } else {
    echo "Gabim: " . $conn->error;
  }
}

if (isset($_GET['fshij'])) {
  $fshijid = $_GET['fshij'];
  $mfsh4 = $conn->query("SELECT * FROM fatura WHERE fatura='$fshijid'");
  $mfsh2 = mysqli_fetch_array($mfsh4);
  $emr = $mfsh2['emri'];
  $fatura2 = $mfsh2['fatura'];
  $data2 = $mfsh2['data'];
  if ($conn->query("INSERT INTO draft (emri, data, fatura) VALUES ('$emr', '$data2','$fatura2')")) {
    $conn->query("DELETE FROM fatura WHERE fatura='$fshijid'");
    $shdraft = $conn->query("SELECT * FROM shitje WHERE fatura='$fshijid'");
    while ($draft = mysqli_fetch_array($shdraft)) {
      $shemertimi = $draft['emertimi'];
      $shqmimi = $draft['qmimi'];
      $shperqindja = $draft['perqindja'];
      $shklienti = $draft['klientit'];
      $shmbetja = $draft['mbetja'];
      $shtotali = $draft['totali'];
      $shfatura = $draft['fatura'];
      $shdata = $draft['data'];
      if ($conn->query("INSERT INTO shitjedraft (emertimi, qmimi, perqindja, klientit, mbetja, totali, fatura, data) VALUES ('$shemertimi', '$shqmimi', '$shperqindja', '$shklienti', '$shmbetja', '$shtotali', '$shfatura', '$shdata')")) {
        $conn->query("DELETE FROM shitje WHERE fatura='$fshijid'");
      }
    }
  } else {
    echo '<script>alert("' . $conn->error . '");</script>';
  }
}
?>

<style>
  .dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-right: 5px;
  }

  .dot-red {
    background-color: red;
  }

  .dot-green {
    background-color: green;
  }

  [data-toggle="tooltip"] {
    position: relative;
    cursor: pointer;
  }

  [data-toggle="tooltip"]::after {
    content: attr(title);
    position: absolute;
    left: 90%;
    bottom: 90%;
    transform: translateX(-25%);
    padding: 7px;
    white-space: nowrap;
    background-color: white;
    border: 1px solid lightgray;
    color: black;
    margin-bottom: 3px;
    border-radius: 7px;
    font-size: 12px;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.5s ease-in-out;
    z-index: 5;
  }

  [data-toggle="tooltip"]:hover::after {
    opacity: 1;
  }

  #faturat_Youtube>table {
    table-layout: fixed;
  }

  #faturat_Youtube>table>tbody>td {
    word-wrap: normal;
    max-width: 500px;
  }

  #faturat_Youtube td {
    white-space: inherit;
  }
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="tcal.css" />
<script type="text/javascript" src="tcal.js"></script>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Fatur&euml; e re</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="">
          <div class="row">
            <div class="col">
              <label for="emri" class="form-label">Emri & Mbiemri</label>
              <input type="text" id="searchInput" onkeyup="filterOptions()" class="form-control shadow-sm rounded-5 py-3" style="border:1" placeholder="Search for names..">
              <br>
              <select id="emriSelect" name="emri" class="form-select shadow-sm rounded-5 py-3">
                <?php
                // PHP: Fetching data from the "klientet" table where blocked='0'
                $gsta = $conn->query("SELECT * FROM klientet WHERE blocked='0'");
                while ($gst = mysqli_fetch_array($gsta)) {
                  // PHP: Generating option elements with values from the database
                  echo '<option value="' . $gst['id'] . '">' . $gst['emri'] . '</option>';
                }
                ?>
              </select>
            </div>

            <script>
              function filterOptions() {
                var input, filter;
                input = document.getElementById('searchInput');
                filter = input.value.toLowerCase();

                // Create an AJAX request
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                  if (this.readyState == 4 && this.status == 200) {
                    // Update the select dropdown with the response
                    document.getElementById('emriSelect').innerHTML = this.responseText;
                  }
                };

                // Send the request to the server-side script
                xmlhttp.open('GET', 'filter_names.php?filter=' + filter, true);
                xmlhttp.send();
              }
            </script>


            <div class="col">
              <!-- Input field for entering Data -->
              <label for="datas" class="form-label">Data:</label>
              <input type="text" name="data" class="form-control shadow-sm rounded-5 py-3" value="<?php echo date("Y-m-d"); ?>">

            </div>
          </div>

          <div class="row my-3">
            <div class="col">
              <!-- Input field for displaying Fatura -->
              <label for="imei" class="form-label">Fatura:</label>
              <input type="text" name="fatura" class="form-control shadow-sm rounded-5 py-3" value="<?php echo date('dmYhis'); ?>" readonly>
            </div>
            <div class="col">
              <?php
              // Checking if the user's session is set to '1'
              if ($_SESSION['acc'] == '1') {
              ?>
                <!-- Select field for choosing gjendjaFatures -->
                <label for="gjendjaFatures" class="form-label">Zgjidhni gjendjen e fatur&euml;s:</label>
                <select name="gjendjaFatures" id="gjendjaFatures" class="form-select shadow-sm rounded-5 py-3">
                  <option value="Rregullt">Rregullt</option>
                  <option value="Pa rregullt">Pa rregullt</option>
                </select>
              <?php
              } else {
              }
              ?>
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

<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="container">
        <div class="p-5 shadow-sm rounded-5 mb-4 card">
          <h4 class="font-weight-bold text-gray-800 mb-4">Pagesat Youtube</h4>
          <nav class="d-flex">
            <h6 class="mb-0">
              <a href="" class="text-reset">Financat</a>
              <span>/</span>
              <a href="faturat.php" class="text-reset" data-bs-placement="top" data-bs-toggle="tooltip" title="<?php echo __FILE__; ?>"><u>Pagesat Youtube</u></a>
              <div class="modal fade" id="videoUdhezuese" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h1 class="modal-title fs-5" id="exampleModalLabel">Video udh&euml;zuese</h1>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                  </div>
                </div>
              </div>
            </h6>
          </nav>
        </div>
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



                  <!-- <input type="checkbox" name="kategorizimi[]" value="Biznes" id="biznes">
                  <label for="biznes">Biznes</label>

                  <input type="checkbox" name="kategorizimi[]" value="Personal" id="personal">
                  <label for="personal">Personal</label> -->


                  <div id="mesg" style="color:red;"></div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hiqe</button>
                <input type="button" name="ruajp" id="btnruaj" class="btn btn-primary" value="Ruaj">
                </form>
              </div>
            </div>
          </div>
        </div>


        <div class="card p-5 my-2 rounded-5 shadow-sm">
          <div class="row">
            <div class="col">
              <h5 style="text-transform: none;" class="card-title"> Veglat p&euml;r aksesim t&euml; shpejt&euml; </h5>
              <div class="">
                <button style="text-transform: none;" class="btn btn-sm btn-primary text-white shadow-sm rounded-5" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fi fi-rr-add-document fa-lg"></i>&nbsp; Fatur&euml; e re</button>
                <button style="text-transform: none;" class="btn btn-sm btn-primary text-white shadow-sm rounded-5" data-bs-toggle="modal" data-bs-target="#pagesmodal"><i class="fi fi-rr-badge-dollar fa-lg"></i>&nbsp; Pages&euml; e re</button>
              </div>
            </div>
            <!-- <div class="col">
              <h5 style="text-transform: none;" class="card-title"> Filtro t&euml; dh&euml;nat </h5>

              <form method="POST">
                <div class="row">
                  <div class="col">
                    <label for="start_date" class="form-label">Prej :</label>
                    <input type="date" class="form-control shadow-sm rounded-5 my-2 shadow-sm rounded-5 mt-2" id="start_date" name="start_date">
                  </div>
                  <div class="col">
                    <label for="end_date" class="form-label">Deri :</label>
                    <input type="date" class="form-control shadow-sm rounded-5 my-2 shadow-sm rounded-5 mt-2" id="end_date" name="end_date">
                  </div>

                </div>
                <div>
                  <button style="text-transform: capitalize;" type="submit" name="submit" class="btn btn-sm btn-primary text-white shadow-sm rounded-5"><i class="fi fi-rr-filter"></i></button>
                </div>
              </form>
            </div> -->
          </div>
        </div>






        <?php
        include 'conn-d.php';



        if (isset($_POST['submit'])) {
          $start_date = $_POST['start_date'];
          $end_date = $_POST['end_date'];

          // Add the filter to the query using prepared statements
          $query = "SELECT * FROM fatura WHERE data >= ? AND data <= ?";
          $stmt = $conn->prepare($query);
          $stmt->bind_param("ss", $start_date, $end_date);
          $stmt->execute();
          $result = $stmt->get_result();
        ?>
          <!-- <div class="p-5 shadow-sm rounded-5 mb-4 card w-50">
            <p>Ju keni zgjedhur filtrimin mes datave t&euml; m&euml;poshtme</p>
            <div class="row">
              <div class="col">

                <span class="badge rounded-pill text-bg-primary text-white w-100 shadow-sm">
                  <i class="fi fi-rr-calendar-lines"></i>
                  <br><br>
                  <?php echo $start_date ?>
                </span>
              </div>
              <div class="col">
                <span class="badge rounded-pill text-bg-primary text-white w-100 shadow-sm"><i class="fi fi-rr-calendar-lines"></i>
                  <br><br>
                  <?php echo $end_date ?>
                </span>
              </div>
            </div>

          </div> -->
          <div id="alert_message"></div>
        <?php } else {
          // SELECT * FROM fatura ORDER BY data,id DESCDESC ORDER BY Date DESC, ID DESC
          $query = " SELECT * FROM fatura ORDER BY data DESC";
          $stmt = $conn->prepare($query);
          $stmt->execute();
          $result = $stmt->get_result();
        }
        ?>
        <div class="p-3 rounded-5 shadow-sm card">
          <div class="table-responsive">
            <table id="faturat_Youtube" class="display table table-bordered" style="width:100%">
              <thead class="bg-light">
                <tr>
                  <th>
                    Emri
                  </th>
                  <th>
                    Emri artistik
                  </th>
                  <th>
                    Fatura
                  </th>
                  <th>
                    Data
                  </th>
                  <th>
                    Shuma
                  </th>
                  <th>
                    Sh.Paguar
                  </th>
                  <th>
                    Obligim
                  </th>
                  <th>
                    Aksionet
                  </th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

</div>
<?php include 'partials/footer.php'; ?>

<script type="text/javascript">
  $(document).ready(function() {
    $('#btnruaj').click(function() {
      var data = $('#user_form').serialize() + '&btn_save=btn_save';
      $.ajax({
        url: 'api/insertpages.php',
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

            // Reload the DataTable
            $('#faturat_Youtube').DataTable().ajax.reload(null, false); // Reload without resetting current page

            // Do any other required actions here after the modals are closed
          }, 1800);
        },
        error: function(xhr, status, error) {
          Swal.fire({
            title: 'Error',
            text: error,
            icon: 'error',
            confirmButtonText: 'OK',
            timer: 1500 // auto close timer in milliseconds
          });
        }
      });
    });
  });
</script>


<script>
  $(document).ready(function() {




    var dataTables = $('#employeeList').DataTable({
      responsive: false,
      order: true,
      serverSide: false,
      "paging": true,
      "ordering": true,
      "info": true,
      "filter": true,
      "length": true,
      "processing": true,
      "ajax": {
        url: "ajax_fatura.php",
        type: "POST",
      },
      "deferRender": true,
      "columns": [{
          "data": "emrifull",
        },
        {
          "data": "emriartikullit",
          "visible": false // Hide the column by default

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
        [10, 25, 50, "T&euml; gjitha"]
      ],
      dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6"f>>' + // length menu and search input layout with margin bottom
        'Brtip',
      buttons: [{
          extend: 'pdfHtml5',
          text: '<i class="fi fi-rr-file-pdf"></i>&nbsp;&nbsp; PDF',
          titleAttr: 'Eksporto tabelen ne formatin PDF',
          className: 'btn '
        }, {
          extend: 'copyHtml5',
          text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
          titleAttr: 'Kopjo tabelen ne formatin Clipboard',
          className: 'btn btn-light border shadow-2 me-2'
        }, {
          extend: 'excelHtml5',
          text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
          titleAttr: 'Eksporto tabelen ne formatin Excel',
          className: 'btn btn-light btn-sm m-2 border shadow-2 me-2',
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
          className: 'btn btn-light border shadow-2 me-2'

        },
        {
          text: '<i class="fi fi-rr-circle-user fa-lg"></i>&nbsp;&nbsp; Shfaqe kolonen emri artistik',
          titleAttr: 'Shfaqe kolonen emri artistik',
          className: 'btn btn-light border shadow-2 me-2',
          id: 'toggle-artikullit-btn',
          action: function(e, node, config) {
            // toggle the visibility of the column
            dataTables.column(1).visible(!dataTables.column(1).visible());
          }

        },

      ],
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
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
      },
      stripeClasses: ['stripe-color'],

    });
    // $(document).on('click', '.delete', function() {
    //   var id = $(this).attr("id");
    //   Swal.fire({
    //     title: 'A jeni i sigurt q&euml; doni ta fshini k&euml;t&euml;?',
    //     icon: 'warning',
    //     showCancelButton: true,
    //     confirmButtonColor: '#3085d6',
    //     cancelButtonColor: '#d33',
    //     confirmButtonText: 'Po, fshije'
    //   }).then((result) => {
    //     if (result.isConfirmed) {
    //       $.ajax({
    //         url: "api/deletefat.php",
    //         method: "POST",
    //         data: {
    //           id: id
    //         },
    //         success: function(data) {
    //           Swal.fire({
    //             title: 'Fatura &euml;sht&euml; fshir&euml; me sukses',
    //             icon: 'success',
    //             showConfirmButton: false,
    //             timer: 2000
    //           });
    //           $('#employeeList').DataTable().ajax.reload(null, false); // Reload the DataTable
    //         }
    //       });
    //     }
    //   });
    // });



    // Make this table employeeList to be reloaded when i press button Ruaj
    $('#btnruaj').click(function() {
      $('#employeeList').DataTable().ajax.reload();

    });
  });


  $(document).on('click', '.open-modal', function() {
    // Get the fatura value from the parent table cell's text
    var fatura = $(this).parent().text().trim();
    var shuma = $(this).parent().next().next().text().trim();
    var shuma_e_paguar = $(this).parent().next().next().next().text().trim();
    var oblgimi = $(this).parent().next().next().next().next().text().trim();

    // Fill the fatura input field with the retrieved data
    $('#fatura').val(fatura);
    $('#shuma').val(shuma);

    if (shuma_e_paguar == 0) {
      $('#shuma').val(shuma);
    } else {
      $('#shuma').val(oblgimi);
    }

    // Show the modal - this depends on the version of Bootstrap you are using
    // Bootstrap 4:
    // $('#myModal').modal('show');
    // Bootstrap 5:
    var myModal = new bootstrap.Modal(document.getElementById('pagesmodal'));
    myModal.show();
  });
</script>


<script>
  var dataTabless;

  $(function() {
    $('[data-toggle="tooltip"]').tooltip();
  });

  $(document).ready(function() {
    var dataTabless;

    function initDataTable() {
      dataTabless = $("#faturat_Youtube").DataTable({
        dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
          "<'row'<'col-md-12'tr>>" +
          "<'row'<'col-md-6'><'col-md-6'p>>",
        responsive: true,
        serverSide: true,
        paging: true,
        ordering: true,
        info: true,
        filter: true,
        length: true,
        processing: true,
        order: [],
        ajax: {
          url: "ajax_faturaYoutube.php",
          type: "POST",
        },
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
        language: {
          url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
        },
        stripeClasses: ["stripe-color"],
      });
    }

    function handleDelete(id) {
      if (confirm("Are you sure you want to remove this?")) {
        $.ajax({
          url: "api/deletefat.php",
          method: "POST",
          data: {
            id: id
          },
          success: function(data) {
            $("#alert_message").html(
              '<div class="alert alert-success">' + data + "</div>"
            );
            if (data === "success") {
              dataTabless.ajax.reload(null, false);
            } else {
              // Handle error if needed
            }
          },
          error: function(xhr, status, error) {
            // Handle error if needed
            $("#alert_message").html(
              '<div class="alert alert-danger">Error: ' + error + "</div>"
            );
          },
        });
      }
    }

    initDataTable();

    $(document).on("click", ".delete", function() {
      var id = $(this).attr("id");
      handleDelete(id);
    });
  });
</script>