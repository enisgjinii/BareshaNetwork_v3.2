<?php
ob_start();
include 'partials/header.php';
// $current_url = $_SERVER['REQUEST_URI'];
// $has_access = false;

// $userName = $_SESSION['emri'];
// $sql = "SELECT users.name AS user_name, roles.name AS role_name, GROUP_CONCAT(DISTINCT role_pages.page) AS pages 
//         FROM users 
//         LEFT JOIN user_roles ON users.id = user_roles.user_id 
//         LEFT JOIN roles ON user_roles.role_id = roles.id 
//         LEFT JOIN role_pages ON roles.id = role_pages.role_id 
//         WHERE users.name = '$userName'
//         GROUP BY users.id, roles.id";

// if ($result = $conn->query($sql)) {
//   while ($row = $result->fetch_assoc()) {
//     $menu_pages = explode(',', $row['pages']);

//     // Check if the user has access to the current page
//     if (in_array(basename($current_url), $menu_pages)) {
//       $has_access = true;
//       break;
//     }
//   }

//   $result->free();
// }

// if (!$has_access) {
//   // Redirect the user to an error page or show an error message
//   header('Location:error.php');
//   exit;
// }

// ob_flush();
$current_url = $_SERVER['REQUEST_URI'];
$filename = basename($current_url);

// Check if the current page is 'logs.php'
if ($filename == "logs.php") {
  // Prepare data for insertion
  $user_informations = $user_info['givenName'] . ' ' . $user_info['familyName'];
  $log_description = $user_informations . " ka vrojtuar listen e aktiviteteve";
  $date_information = date('Y-m-d H:i:s');

  // Prepare the INSERT statement
  $stmt = $conn->prepare("INSERT INTO logs (stafi, ndryshimi, koha) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $user_informations, $log_description, $date_information);

  // Execute the statement and check for errors
  if ($stmt->execute()) {
    // echo "Data inserted successfully!";
  } else {
    echo "Error: " . $stmt->error;
  }

  // Close the prepared statement
  $stmt->close();
}

?>



























<?php



if (in_array($current_url, $menu_pages)) {
  // User has access to this page, show menu item
  echo '<li class="nav-item">
          <a class="nav-link" href="' . $current_url . '">
            <i class="fi fi-rr-users-alt menu-icon pe-3"></i>
            <span class="menu-title">' . $page . ' </span>
          </a>
        </li>';
} else {
  // User doesn't have access to this page, don't show menu item
}
?>





<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="container">
        <nav class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Vegla te shpejta</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
              <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">
                Lista e aktiviteteve
                <?php echo $filename ?>
              </a>
            </li>
        </nav>
        <div class="card rounded-5 shadow-sm">
          <div class="card-body">
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table id="example1" class="table w-100 table-bordered">
                    <thead class="bg-light">
                      <tr>
                        <th>Stafi</th>
                        <th>Sherbimi</th>
                        <th>Koha</th>
                      </tr>
                    </thead>
                    <tbody>

                    </tbody>
                    <tfoot class="bg-light">
                      <tr>
                        <th>Stafi</th>
                        <th>Sherbimi</th>
                        <th>Koha</th>
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
<?php include 'partials/footer.php'; ?>
<script>
  $('#example1').DataTable({
    searching: true,
    dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
      "<'row'<'col-md-12'tr>>" +
      "<'row'<'col-md-6'><'col-md-6'p>>",
    buttons: [{
      extend: 'pdfHtml5',
      text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
      titleAttr: 'Eksporto tabelen ne formatin PDF',
      className: 'btn btn-sm btn-light border rounded-5 me-2'
    }, {
      extend: 'copyHtml5',
      text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
      titleAttr: 'Kopjo tabelen ne formatin Clipboard',
      className: 'btn btn-sm btn-light border rounded-5 me-2'
    }, {
      extend: 'excelHtml5',
      text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
      titleAttr: 'Eksporto tabelen ne formatin CSV',
      className: 'btn btn-sm btn-light border rounded-5 me-2'
    }, {
      extend: 'print',
      text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
      titleAttr: 'Printo tabel&euml;n',
      className: 'btn btn-sm btn-light border rounded-5 me-2'
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
    "ajax": "api/fetchLogs.php",
    "columns": [{
        "data": 0
      },
      {
        "data": 1
      },
      {
        "data": 2
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
  })
</script>