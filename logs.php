<?php
ob_start();
include 'partials/header.php';
$current_url = $_SERVER['REQUEST_URI'];
$has_access = false;

$userName = $_SESSION['emri'];
$sql = "SELECT users.name AS user_name, roles.name AS role_name, GROUP_CONCAT(DISTINCT role_pages.page) AS pages 
        FROM users 
        LEFT JOIN user_roles ON users.id = user_roles.user_id 
        LEFT JOIN roles ON user_roles.role_id = roles.id 
        LEFT JOIN role_pages ON roles.id = role_pages.role_id 
        WHERE users.name = '$userName'
        GROUP BY users.id, roles.id";

if ($result = $conn->query($sql)) {
  while ($row = $result->fetch_assoc()) {
    $menu_pages = explode(',', $row['pages']);

    // Check if the user has access to the current page
    if (in_array(basename($current_url), $menu_pages)) {
      $has_access = true;
      break;
    }
  }

  $result->free();
}

if (!$has_access) {
  // Redirect the user to an error page or show an error message
  header('Location:error.php');
  exit;
}

ob_flush();


?>



























<?php

$current_url = $_SERVER['REQUEST_URI'];
$filename = basename($current_url);

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
    <div class="container">
      <div class="p-5 rounded-5 shadow-sm mb-4 card">
        <h4 class="font-weight-bold text-gray-800 mb-4">Lista e aktiviteteve te perdoruesve ne sistem</h4> <!-- Breadcrumb -->
        <nav class="d-flex">
          <h6 class="mb-0">
            <a href="logs.php" class="text-reset" data-bs-placement="top" data-bs-toggle="tooltip" title="<?php echo __FILE__; ?>"><u>Logs</u></a>
          </h6>
        </nav>
      </div>
      <div class="card rounded-5 shadow-sm">
        <div class="card-body">
          <h4 class="card-title">Logs</h4>
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
<?php include 'partials/footer.php'; ?>
<script>
  $('#example1').DataTable({
    responsive: true,
    search: {
      return: true,
    },
    dom: 'Bfrtip',
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
      titleAttr: 'Eksporto tabelen ne formatin CSV',
      className: 'btn btn-light border shadow-2 me-2'
    }, {
      extend: 'print',
      text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
      titleAttr: 'Printo tabel&euml;n',
      className: 'btn btn-light border shadow-2 me-2'
    }],
    initComplete: function() {
      var btns = $('.dt-buttons');
      btns.addClass('');
      btns.removeClass('dt-buttons btn-group');
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