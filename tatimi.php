<?php
ob_start();

include 'partials/header.php';

if (isset($_POST['ruaj'])) {
  $shuma = mysqli_real_escape_string($conn, $_POST['shuma']);
  $data = mysqli_real_escape_string($conn, $_POST['data']);
  $pagesa = mysqli_real_escape_string($conn, $_POST['forma']);
  $pershkrimi = mysqli_real_escape_string($conn, $_POST['pershkrimi']);

  $stmt = $conn->prepare("INSERT INTO tatimi (shuma, pershkrimi, data, pagesa) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $shuma, $pershkrimi, $data, $pagesa);

  if ($stmt->execute()) {
    // Dërgimi i suksesshëm
    echo "<script>
            Swal.fire({
              title: 'Sukses!',
              text: 'Data është futur me sukses.',
              icon: 'success',
              showConfirmButton: false,
              timer: 2000 // 2 sekonda
            });
          </script>";
  } else {
    // Trajto gabimin
    echo "<script>
            Swal.fire({
              title: 'Gabim!',
              text: 'Diçka shkoi gabim gjatë futjes së të dhënave.',
              icon: 'error',
              showConfirmButton: false,
              timer: 2000 // 2 sekonda
            });
          </script>";
  }
}



// Merr URL-në aktuale
$current_url = $_SERVER['REQUEST_URI'];

// Merr emrin e skedarit nga URL-ja aktuale
$filename = basename($current_url);

// Përgatitja për regjistrimin e aktivitetit
if ($filename == "tatimi.php") {
$user_informations = $user_info['givenName'] . ' ' . $user_info['familyName'];
$log_description = $user_informations . " ka vrojtuar listen e aktiviteteve";
$date_information = date('Y-m-d H:i:s');

// Përgatitja e deklaruar për të futur të dhënat në tabelën 'logs'
$stmt = $conn->prepare("INSERT INTO logs (stafi, ndryshimi, koha) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $user_informations, $log_description, $date_information);

if ($stmt->execute()) {
// Regjistrimi u fut me sukses
} else {
echo "Gabim: " . $stmt->error;
}

$stmt->close();
}

$has_access = false;

// Put in session that page
$_SESSION['page'] = $filename;

$user_credentials = $_SESSION['id'];

// Kërkesa SQL e përgatitur me deklaratën e përgatitur
$stmt = $conn->prepare("SELECT googleauth.firstName AS user_name, roles.name AS role_name, roles.id AS role_id, GROUP_CONCAT(DISTINCT role_pages.page) AS pages
FROM googleauth
LEFT JOIN user_roles ON googleauth.id = user_roles.user_id
LEFT JOIN roles ON user_roles.role_id = roles.id
LEFT JOIN role_pages ON roles.id = role_pages.role_id
WHERE googleauth.id = ?
GROUP BY googleauth.id, roles.id");

$stmt->bind_param("i", $user_credentials);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
while ($row = $result->fetch_assoc()) {
$menu_pages = explode(',', $row['pages']);

if (in_array(basename($filename), $menu_pages)) {
$has_access = true;
break;
}
}

$result->free();
}

// Nëse nuk ka qasje, ridrejto tek faqja e gabimit dhe mbyll programin
if (!$has_access) {
header('Location:error.php');
exit;
}

?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="container">
        <nav class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Financat</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
              <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">
                Tatimi
              </a>
            </li>
        </nav>
        <div class="row mb-2">
          <div>
            <button type="button" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
              <i class="fi fi-rr-add-document fa-lg"></i>&nbsp; Shto
            </button>
          </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tatimi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </button>
              </div>
              <div class="modal-body ">
                <form method="POST" action="" class="">
                  <div class="mb-3">
                    <label for="amount" class="form-label">Shuma:</label>
                    <input type="text" name="shuma" class="form-control border border-2 rounded-5" id="inlineFormInputGroup" value="0.00">
                  </div>
                  <div class="mb-3">
                    <label for="date_of_payment" class="form-label">Data e pages&euml;s:</label>
                    <input type="date" name="data" class="form-control border border-2 rounded-5" value="<?php echo date("d-m-Y"); ?>">
                  </div>
                  <div class="mb-3">

                    <label for="way_of_payment" class="form-label">Forma e pages&euml;s:</label>
                    <select name="forma" class="form-select mr-sm-2" id="way_of_payment">
                      <option value="Cash">Cash</option>
                      <option value="Bank">Bank</option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label for="pershkrimi" class="form-label">P&euml;rshkrimi:</label>
                    <textarea name="pershkrimi" class="form-control border border-2 rounded-5"></textarea>
                  </div>

                  <script>
                    new Selectr('#way_of_payment', {
                      searchable: true,
                      width: '100%',
                    });
                  </script>
              </div>
              <div class="modal-footer">
                <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button>
                <input type="submit" class="input-custom-css px-3 py-2" name="ruaj" value="Ruaj">
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="card shadow-sm rounded-5">
          <div class="card-body">
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table id="example" class="table w-100 table-bordered">
                    <thead class="bg-light">
                      <tr>
                        <th>Shuma</th>
                        <th>P&euml;rshkrimi</th>
                        <th>Data</th>
                        <th>Forma</th>
                      </tr>
                    </thead>

                    <tbody>
                      <?php
                      $kueri = $conn->query("SELECT * FROM tatimi ORDER BY id DESC");
                      while ($k = mysqli_fetch_array($kueri)) {

                      ?>
                        <tr>



                          <td><?php echo $k['shuma']; ?>&euro;</td>
                          <td><?php echo $k['pershkrimi']; ?></td>
                          <td><?php echo $k['data']; ?></td>
                          <td><?php echo $k['pagesa']; ?></td>
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
    dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
      "<'row'<'col-md-12'tr>>" +
      "<'row'<'col-md-6'><'col-md-6'p>>",
    responsive: true,
    searching: true,
    buttons: [{
      extend: 'pdfHtml5',
      text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
      titleAttr: 'Eksporto tabelen ne formatin PDF',
      className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
    }, {
      extend: 'copyHtml5',
      text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
      titleAttr: 'Kopjo tabelen ne formatin Clipboard',
      className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
    }, {
      extend: 'excelHtml5',
      text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
      titleAttr: 'Eksporto tabelen ne formatin CSV',
      className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
    }, {
      extend: 'print',
      text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
      titleAttr: 'Printo tabel&euml;n',
      className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
    }],
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