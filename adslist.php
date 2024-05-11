<?php
include 'partials/header.php';
$idof = $_GET['id'];
$merrl = $conn->query("SELECT * FROM klientet WHERE ads='$idof'");
?>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <nav class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Klientët</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">
            <a href="ads.php" class="text-reset" style="text-decoration: none;">
              Llogaritë e ADS
            </a>
          </li>
        </ol>
      </nav>
      <!-- Button to go back -->
      <button type="button" class="input-custom-css px-3 py-2 mb-3" onclick="history.back()"><i class="fi fi-rr-arrow-small-left"></i> Kthehu mbrapa</button>
      <div class="card rounded-5 d-none d-md-none d-lg-block">
        <div class="card-body">
          <div class="row">
            <div class="col-12">
              <div class="table-responsive">
                <table id="list_ads" class="table" width="100%" cellspacing="0">
                  <thead class="bg-light">
                    <tr>
                      <th scope="col">Emri</th>
                      <th scope="col">Emri Artistik</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    while ($loa = mysqli_fetch_array($merrl)) {
                    ?>
                      <tr>
                        <td>
                          <?php echo $loa['emri']; ?>
                        </td>
                        <td>
                          <?php echo $loa['emriart']; ?>
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
          <?php
          while ($loa = mysqli_fetch_array($merrl)) {
          ?>
            <tr>
              <td>
                <?php echo $loa['emri']; ?>
              </td>
              <td>
                <?php echo $loa['emriart']; ?>
              </td>
            </tr>
          <?php } ?>
        </ul>
      </div>
    </div>
  </div>
</div>
<script>
  $('#list_ads').DataTable({
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
    fixedHeader: true,
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
    },
    stripeClasses: ['stripe-color']
  })
</script>
<?php include 'partials/footer.php'; ?>