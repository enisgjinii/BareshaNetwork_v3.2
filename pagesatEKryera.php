<?php include 'partials/header.php'; ?>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="container">
        <div class="p-5 shadow-sm rounded-5 mb-4 card">
          <h4 class="font-weight-bold text-gray-800 mb-4">Pagesat e perfunduara</h4> <!-- Breadcrumb -->
          <nav class="d-flex">
            <h6 class="mb-0">
              <a href="" class="text-reset">Platformat</a>
              <span>/</span>
              <a href="pagesatEKryera.php" class="text-reset" data-bs-placement="top" data-bs-toggle="tooltip" title="<?php echo __FILE__; ?>"><u>Pagesat e perfunduara</u></a>
            </h6>
          </nav>
        </div>
        <div class="card shadow-sm rounded-5 my-2 py-5 px-3 mb-4">
          <form method="GET" action="" class="form-inline">
            <div class="form-group row">
              <div class="col-6">
                Prej :
                <input type="date" class="form-control shadow-sm rounded-5 w-100 mt-3" value="<?php echo date("Y-m-d"); ?>" style="width: 230px;" name="d1" autocomplete="off">
              </div>
              <div class="col-6">
                Deri :
                <input type="date" class="form-control shadow-sm rounded-5 w-100 mt-3" value="<?php echo date("Y-m-d"); ?>" style="width: 230px;" name="d2" autocomplete="off">
              </div>
            </div>
            <div class="row">
              <div class="col-6">
                <button type="submit" class="btn btn-sm btn-primary mt-3 text-white shadow-sm rounded-5 mt-2" name="kerko" value="Kerko">
                  <i class="fi fi-rr-filter"></i>
                </button>
              </div>
            </div>
          </form>
        </div>




        <?php
        if (isset($_GET['kerko'])) {
          $d1 = $_GET['d1'];
          $d2 = $_GET['d2'];
          $merri = $conn->query("SELECT * FROM pagesatplatformat WHERE data >= '$d1' AND data <= '$d2' ORDER BY data DESC");
        ?>
          <div class="card shadow-sm rounded-5">
            <div class="card-body">
              <h4 class="card-title">Tabela e gjeneruar ne baz&euml; t&euml; datave <?php echo $d1; ?> - <?php echo $d2; ?></h4>
              <div class="row">
                <div class="col-12">
                  <br>
                  <div class="table-responsive">

                    <table id="example1" class="table w-100 table-bordered">
                      <thead class="bg-light">
                        <tr>
                          <th>Klienti</th>
                          <th>Fatura</th>
                          <th>Pershkrimi</th>
                          <th>Shuma</th>
                          <th>Menyra</th>
                          <th>Data</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (mysqli_num_rows($merri) > 0) { ?>
                          <?php while ($k = mysqli_fetch_array($merri)) {
                            $nrfatures = $k['fatura'];
                            $merremrin = $conn->query("SELECT * FROM faturaplatforma WHERE fatura='$nrfatures'");
                            $dhenat = mysqli_fetch_array($merremrin);
                            if ($dhenat != null) {
                              $cliidi = $dhenat['emri'];
                              $merremrin2 = $conn->query("SELECT * FROM klientet WHERE id='$cliidi'");
                              $dhena = mysqli_fetch_array($merremrin2);
                            }
                          ?>
                            <tr>
                              <th><?php echo $dhena['emri']; ?></th>
                              <td><?php echo $k['fatura']; ?></td>
                              <td><?php echo $k['pershkrimi']; ?></td>
                              <td><?php echo $k['shuma']; ?></td>
                              <td><?php echo $k['menyra']; ?></td>
                              <td><?php echo date("d-m-Y", strtotime($k['data'])); ?></td>
                              <td><a class="btn btn-light shadow-sm rounded-5 border" target="_blank" href="fatura.php?invoice=<?php echo $k['fatura']; ?>"><i class="fi fi-rr-print"></i></a></td>
                            </tr>
                          <?php } ?>
                        <?php } else { ?>
                          <tr>
                            <td colspan="7" align="center">No data here</td>
                          </tr>
                        <?php } ?>
                      </tbody>
                      <tfoot class="bg-light">
                        <tr>
                          <th>Klienti</th>
                          <th>Fatura</th>
                          <th>Pershkrimi</th>
                          <th>Shuma</th>
                          <th>Menyra</th>
                          <th>Data</th>
                          <th></th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>
        <br>

        <div class="card shadow-sm rounded-5">
          <div class="card-body">
            <h4 class="card-title">Tabela me te gjitha te dhenat rreth perfundimit te pagesave</h4>
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table id="example2" class="table w-100 border">
                    <thead class="bg-light">
                      <tr>
                        <th></th>
                        <th>Klienti</th>
                        <th>Fatura</th>
                        <th>Pershkrimi</th>
                        <th>Shuma</th>
                        <th>Menyra</th>
                        <th>Data</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $payments = $conn->query("SELECT * FROM pagesatplatformat ORDER BY ID DESC");

                      while ($payment = mysqli_fetch_array($payments)) {
                        $invoice_number = $payment['fatura'];
                        $invoice_info = $conn->query("SELECT * FROM faturaplatformes WHERE fatura='$invoice_number'");
                        $invoice_data = mysqli_fetch_array($invoice_info);

                        if (!empty($invoice_data)) {
                          $client_id = $invoice_data['emri'];
                          $client_info = $conn->query("SELECT * FROM klientet WHERE id='$client_id'");
                          $client_data = mysqli_fetch_array($client_info);
                      ?>
                          <tr>
                            <td><input type="checkbox"></td>
                            <td><?= $client_data['emri']; ?></td>
                            <td><?= $payment['fatura']; ?></td>
                            <td><?= $payment['pershkrimi']; ?></td>
                            <td><?= $payment['shuma']; ?></td>
                            <td><?= $payment['menyra']; ?></td>
                            <td><?= date("d-m-Y", strtotime($payment['data'])); ?></td>
                            <td>
                              <a class="btn btn-light shadow-2 border border-1" target="_blank" href="fatura.php?invoice=<?= $payment['fatura']; ?>">
                                <i class="fi fi-rr-print"></i>
                              </a>
                            </td>
                          </tr>
                      <?php
                        }
                      }
                      ?>

                    </tbody>
                    <tfoot class="bg-light">
                      <tr>
                        <th></th>
                        <th>Klienti</th>
                        <th>Fatura</th>
                        <th>Pershkrimi</th>
                        <th>Shuma</th>
                        <th>Menyra</th>
                        <th>Data</th>
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
</div>

<?php include 'partials/footer.php'; ?>
<script>
  $(document).ready(function() {

    var dataTables = $('#example1').DataTable({
      responsive: false,
      search: {
        return: true,
      },

      lengthMenu: [
        [10, 25, 50, -1],
        [10, 25, 50, "Te gjitha"]
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
      dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6"f>>' + // length menu and search input layout with margin bottom
        'Brtip',
      buttons: [{
        extend: 'pdfHtml5',
        text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
        titleAttr: 'Eksporto tabelen ne formatin PDF',
        className: 'btn btn-light border shadow-2 me-2'
      }, {
        extend: 'copyHtml5',
        text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
        titleAttr: 'Kopjo tabelen ne formatin Clipboard',
        className: 'btn btn-light border shadow-2 me-2'
      }, {
        extend: 'excelHtml5',
        text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
        titleAttr: 'Eksporto tabelen ne formatin Excel',
        className: 'btn btn-light border shadow-2 me-2',
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
      }, ],

      fixedHeader: true,
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
      },
      stripeClasses: ['stripe-color'],

    });
  });
</script>
<script>
  var datatables;

  $(document).ready(function() {
    var dataTables = $('#example2').DataTable({
      responsive: false,
      search: {
        return: true,
      },
      order: [
        [7, "desc"]
      ],
      pageLength: 10, // limit to 10 entries per page
      createdRow: function(row, data, dataIndex) {
        $(row).find('td:first-child').html('<input type="checkbox"/>');
      },
      initComplete: function() {
        var btns = $('.dt-buttons');
        btns.addClass('');
        btns.removeClass('dt-buttons btn-group');
        var lengthSelect = $('div.dataTables_length select');
        lengthSelect.addClass('form-select');
        lengthSelect.css({
          'width': 'auto',
          'margin': '0 8px',
          'padding': '0.375rem 1.75rem 0.375rem 0.75rem',
          'line-height': '1.5',
          'border': '1px solid #ced4da',
          'border-radius': '0.25rem',
        });

        // Add checkbox column header
        var th = $('<th><input type="checkbox" id="check-all"/></th>').prependTo('#example2 th tr');

        // Handle checkbox events
        $('#check-all').on('change', function() {
          $('input[type="checkbox"]').prop('checked', $(this).prop('checked'));
        });
        $('#example2 tbody').on('click', 'input[type="checkbox"]', function(e) {
          e.stopPropagation(); // Prevent row selection
          $(this).closest('tr').toggleClass('selected', $(this).prop('checked'));
        });
      },
      dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6"f>>' + 'Brtip',
      buttons: [{
          extend: 'pdfHtml5',
          text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
          titleAttr: 'Eksporto tabelen ne formatin PDF',
          className: 'btn btn-light border shadow-2 me-2'
        },
        {
          extend: 'copyHtml5',
          text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
          titleAttr: 'Kopjo tabelen ne formatin Clipboard',
          className: 'btn btn-light border shadow-2 me-2'
        },
        {
          extend: 'excelHtml5',
          text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
          titleAttr: 'Eksporto tabelen ne formatin Excel',
          className: 'btn btn-light border shadow-2 me-2',
          exportOptions: {
            rows: function(idx, data, node) {
              return $(node).find('input[type="checkbox"]').prop('checked');
            }
          }
        },
        {
          extend: 'print',
          text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
          titleAttr: 'Printo tabel&euml;n',
          className: 'btn btn-light border shadow-2 me-2'
        },
      ],
      fixedHeader: true,
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
      },
      stripeClasses: ['stripe-color'],
    });

    // Initialize Bootstrap tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Prevent row selection on click (only allow checkbox selection)
    $('#example2 tbody').on('click', 'tr td:not(:first-child)', function(e) {
      e.stopPropagation();
    });
  });
</script>