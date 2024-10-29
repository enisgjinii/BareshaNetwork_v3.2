<?php
// Aktivizo mbajtjen e përmbajtjes së output-it për të parandaluar dërgimin e ndonjë output-i në shfletues
ob_start();
// Përfshij header-in e faqes
include 'partials/header.php';
class PageAccessController
{
  private $conn;
  private $user_info;
  private $user_credentials;
  private $current_url;
  private $filename;
  private $has_access;
  // Konstruktori për të filluar objektin me parametrat e nevojshëm
  public function __construct($conn, $user_info)
  {
    $this->conn = $conn;
    $this->user_info = $user_info;
    $this->current_url = $_SERVER['REQUEST_URI'];
    $this->filename = basename($this->current_url);
    $this->has_access = false;
    $this->user_credentials = $_SESSION['id'];
  }
  // Metoda për të regjistruar aktivitetin e përdoruesit
  public function logActivity()
  {
    if ($this->filename == "logs.php") {
      $user_informations = $this->user_info['givenName'] . ' ' . $this->user_info['familyName'];
      $log_description = $user_informations . " ka vrojtuar listen e aktiviteteve";
      $date_information = date('Y-m-d H:i:s');
      $stmt = $this->conn->prepare("INSERT INTO logs (stafi, ndryshimi, koha) VALUES (?, ?, ?)");
      $stmt->bind_param("sss", $user_informations, $log_description, $date_information);
      if ($stmt->execute()) {
        // Logu u fut me sukses
      } else {
        echo "Gabim: " . $stmt->error;
      }
      $stmt->close();
    }
  }
  // Metoda për të verifikuar qasjen e përdoruesit në faqen aktuale
  public function checkAccess()
  {
    $stmt = $this->conn->prepare("SELECT googleauth.firstName AS user_name, roles.name AS role_name, roles.id AS role_id, GROUP_CONCAT(DISTINCT role_pages.page) AS pages
            FROM googleauth
            LEFT JOIN user_roles ON googleauth.id = user_roles.user_id
            LEFT JOIN roles ON user_roles.role_id = roles.id
            LEFT JOIN role_pages ON roles.id = role_pages.role_id
            WHERE googleauth.id = ?
            GROUP BY googleauth.id, roles.id");
    $stmt->bind_param("i", $this->user_credentials);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
      while ($row = $result->fetch_assoc()) {
        $menu_pages = explode(',', $row['pages']);
        if (in_array($this->filename, $menu_pages)) {
          $this->has_access = true;
          break;
        }
      }
      $result->free();
    }
    return $this->has_access;
  }
}
// Krijo objektin PageAccessController me lidhjen në bazën e të dhënave dhe informacionin e përdoruesit
$pageAccessController = new PageAccessController($conn, $user_info);
// Regjistro aktivitetin e përdoruesit
$pageAccessController->logActivity();
// Kontrollo qasjen e përdoruesit në faqen aktuale
if (!$pageAccessController->checkAccess()) {
  // Nëse përdoruesi nuk ka qasje, ridrejto në faqen e gabimit dhe dal
  header('Location:error.php');
  exit;
}
// Get the filename for breadcrumb
$filename = basename($_SERVER['REQUEST_URI']);
?>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <nav class="bg-white px-2 rounded-5" style="width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a class="text-reset" style="text-decoration: none;">Vegla te shpejta</a></li>
          <li class="breadcrumb-item active" aria-current="page">
            <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">
              Lista e aktiviteteve
              <?php echo $filename ?>
            </a>
          </li>
        </ol>
      </nav>
      <!-- Filter Section -->
      <div class="card rounded-5 shadow-sm mb-4">
        <div class="card-body">
          <form id="filterForm" class="row g-3">
            <div class="col-md-4">
              <label for="startDate" class="form-label">Data Fillimit</label>
              <input type="date" class="form-control rounded-5" id="startDate" name="startDate">
            </div>
            <div class="col-md-4">
              <label for="endDate" class="form-label">Data Përfundimit</label>
              <input type="date" class="form-control rounded-5" id="endDate" name="endDate">
            </div>
            <div class="col-md-4">
              <label for="staffName" class="form-label">Emri i Stafit</label>
              <input type="text" class="form-control rounded-5" id="staffName" name="staffName" placeholder="Shkruaj emrin e stafit">
            </div>
            <script>
              const startDatePicker = flatpickr("#startDate", {
                dateFormat: "Y-m-d",
                locale: "sq",
                maxDate: new Date(), // Optional: Limit start date to today
                onChange: (selectedDates) => {
                  const startDate = selectedDates[0];
                  if (startDate) {
                    endDatePicker.set("minDate", startDate); // Set start date as min date for end date
                  }
                },
              });
              const endDatePicker = flatpickr("#endDate", {
                dateFormat: "Y-m-d",
                locale: "sq",
                maxDate: new Date(), // Optional: Limit end date to today
                onChange: (selectedDates) => {
                  const endDate = selectedDates[0];
                  if (endDate) {
                    startDatePicker.set("maxDate", endDate); // Set end date as max date for start date
                  }
                },
              });
            </script>
            <div class="col-12">
              <button type="button" id="applyFilters" class="input-custom-css px-3 py-2">Apliko Filtrat</button>
              <button type="button" id="resetFilters" class="input-custom-css px-3 py-2">Rifresko</button>
            </div>
          </form>
        </div>
      </div>
      <!-- DataTable Card -->
      <div class="card rounded-5 shadow-sm">
        <div class="card-body">
          <div class="row">
            <div class="col-12">
              <div class="table-responsive">
                <table id="example1" class="table w-100 table-bordered">
                  <thead class="bg-light">
                    <tr>
                      <th class="text-dark">Stafi</th>
                      <th class="text-dark">ndryshimi</th>
                      <th class="text-dark">Koha</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> <!-- End Container -->
  </div> <!-- End Content Wrapper -->
</div> <!-- End Main Panel -->
</div>
<?php include 'partials/footer.php'; ?>
<script>
  $(document).ready(function() {
    // Initialize DataTable
    var table = $('#example1').DataTable({
      searching: true,
      dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
        "<'row'<'col-md-12'tr>>" +
        "<'row'<'col-md-6'><'col-md-6'p>>",
      buttons: [{
          extend: 'pdfHtml5',
          text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
          titleAttr: 'Eksporto tabelen ne formatin PDF',
          className: 'btn btn-sm btn-light border rounded-5 me-2'
        },
        {
          extend: 'copyHtml5',
          text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
          titleAttr: 'Kopjo tabelen ne formatin Clipboard',
          className: 'btn btn-sm btn-light border rounded-5 me-2'
        },
        {
          extend: 'excelHtml5',
          text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
          titleAttr: 'Eksporto tabelen ne formatin CSV',
          className: 'btn btn-sm btn-light border rounded-5 me-2'
        },
        {
          extend: 'print',
          text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
          titleAttr: 'Printo tabel&euml;n',
          className: 'btn btn-sm btn-light border rounded-5 me-2'
        }
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
      "ajax": {
        "url": "api/fetchLogs.php",
        "type": "POST",
        "data": function(d) {
          // Add filter parameters to the AJAX request
          d.startDate = $('#startDate').val();
          d.endDate = $('#endDate').val();
          d.staffName = $('#staffName').val();
        }
      },
      "columns": [{
          "data": "stafi"
        },
        {
          "data": "ndryshimi"
        },
        {
          "data": "koha"
        }
      ],
      "paging": true,
      "searching": true,
      "processing": true,
      "info": true,
      "fixedHeader": true,
      "language": {
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
      },
      stripeClasses: ['stripe-color'],
    });
    // Apply Filters
    $('#applyFilters').on('click', function() {
      table.ajax.reload();
    });
    // Reset Filters
    $('#resetFilters').on('click', function() {
      $('#filterForm')[0].reset();
      table.ajax.reload();
    });
  });
</script>