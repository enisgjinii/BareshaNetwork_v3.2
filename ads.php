<?php include 'partials/header.php';
if (isset($_POST['ruaj'])) {

  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $adsid = mysqli_real_escape_string($conn, $_POST['adsid']);
  $shteti = mysqli_real_escape_string($conn, $_POST['shteti']);


  if ($conn->query("INSERT INTO ads (email, adsid, shteti) VALUES ('$email', '$adsid', '$shteti')")) {
  }
}
if (isset($_GET['delete'])) {
  $delid = $_GET['delete'];
  $conn->query("DELETE FROM ads WHERE id='$delid'");
}
?>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Shto nj&euml; llogari</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="" enctype="multipart/form-data">
          <div class="form-group">
            <label>Email</label>
            <input type="text" name="email" class="form-control" placeholder="Email">
          </div>
          <div class="form-group">
            <label>ADS ID</label>
            <input type="text" name="adsid" class="form-control" placeholder="ADS Id">
          </div>
          <div class="form-group">
            <label>Shteti</label>
            <input type="text" name="shteti" class="form-control" placeholder="Shteti">
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

        <nav class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Klientët</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
              <a href="ads.php" class="text-reset" style="text-decoration: none;">
                Llogaritë e ADS
              </a>
            </li>
        </nav>
        
        <button type="button" class="input-custom-css px-3 py-2 mb-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
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
                        <th>Email</th>
                        <th>ADS ID</th>
                        <th>Shteti</th>
                        <th>Klientet</th>
                        <th></th>

                      </tr>
                    </thead>

                    <tbody>
                      <?php
                      $kueri = $conn->query("SELECT * FROM ads ORDER BY id ASC");
                      while ($k = mysqli_fetch_array($kueri)) {

                      ?>
                        <tr>
                          <td>
                            <?php echo $k['email']; ?>
                          </td>
                          <td>
                            <?php echo $k['adsid']; ?>
                          </td>
                          <td>
                            <?php echo $k['shteti']; ?>
                          </td>
                          <td><a class="input-custom-css px-3 py-2" style="text-decoration: none;text-transform:none;" href="adslist.php?id=<?php echo $k['id']; ?>"><i class="fi fi-rr-folder"></i> Hap Listen</a>
                          </td>
                          <td>
                            <a class="btn btn-primary text-white rounded-5 px-2 py-2" href="ads.php?edit=<?php echo $k['id']; ?>"><i class="fi fi-rr-edit"></i></a>
                            <a class="btn btn-danger text-white rounded-5 px-2 py-2" href="ads.php?delete=<?php echo $k['id']; ?>"><i class="fi fi-rr-trash"></i></a>
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