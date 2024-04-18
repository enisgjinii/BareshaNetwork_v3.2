<?php include 'shtesStaf.php'; ?>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <nav class="bg-white px-2 rounded-5" class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Menaxhimi</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page"><a href="invoice.php" class="text-reset" style="text-decoration: none;">
              Stafi
            </a>
          </li>
      </nav>
      <ul class="nav nav-pills mb-3 bg-white me-auto rounded-5" id="pills-tab" role="tablist" style="width: fit-content;">
        <li class="nav-item" role="presentation">
          <button class="nav-link rounded-5 active" id="pills-tabelaStafit-tab" data-bs-toggle="pill" data-bs-target="#pills-tabelaStafit" style="text-transform: none;text-decoration: none;" type="button" role="tab" aria-controls="pills-tabelaStafit" aria-selected="true">Stafi</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link rounded-5" id="pills-listaETentativave-tab" data-bs-toggle="pill" data-bs-target="#pills-listaETentativave" type="button" role="tab" aria-controls="pills-listaETentativave" style="text-transform: none;text-decoration: none;" aria-selected="false">Lista e tentativave</button>
        </li>
      </ul>
      <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-tabelaStafit" role="tabpanel" aria-labelledby="pills-tabelaStafit-tab" tabindex="0">
          <div class="card rounded-5 shadow-sm">
            <div class="card-body">
              <div class="row">
                <div class="col-12">
                  <table id="example" class="table table-bordered w-full">
                    <thead class="bg-light">
                      <tr>
                        <th>Emri & Mbiemri</th>
                        <th>Email Adresa</th>
                        <th>Rroga</th>
                        <th>Veprime</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $kueri = $conn->query("SELECT * FROM googleauth ORDER BY id DESC");
                      while ($k = mysqli_fetch_array($kueri)) {
                        if (empty($k['ban'])) {
                          $eme = $k['firstName'] . " " . $k['last_name'];
                        } else {
                          $eme = '<del style="color:red;">' . $k['firstName'] . '</del> ';
                        }
                        if (!($k['email'] == $_SESSION['email'])) {
                      ?>
                          <tr>
                            <td><?php echo $eme; ?></td>
                            <td><?php echo $k['email']; ?></td>
                            <td><?php echo $k['salary']; ?> &euro;</td>
                            <td>
                              <button class="btn btn-sm btn-primary rounded-5 shadow-0 px-2 py-2 text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRightEdit_<?php echo $k['id']; ?>" aria-controls="offcanvasRightEdit_<?php echo $k['id']; ?>" onclick="editEmployee(<?php echo $k['id']; ?>, <?php echo $k['salary']; ?>)">
                                <i class="fi fi-rr-edit"></i>
                              </button>
                              <button class="btn btn-sm btn-danger rounded-5 shadow-0 px-2 py-2 text-white delete-btn" data-id="<?php echo $k['id']; ?>">
                                <i class="fi fi-rr-trash"></i>
                              </button>
                              <button class="btn btn-sm btn-success rounded-5 shadow-0 px-2 py-2 text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasActivities_<?php echo $k['id']; ?>" aria-controls="offcanvasActivities_<?php echo $k['id']; ?>">
                                <i class="fi fi-rr-search-alt"></i>
                              </button>
                              <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasActivities_<?php echo $k['id']; ?>" aria-labelledby="offcanvasActivities_<?php echo $k['id']; ?>">
                                <div class="offcanvas-header border-bottom">
                                  <h5 id="offcanvasRightEditLabel_<?php echo $k['id']; ?>">Aktiviteti ne sistem i puntorit <?php echo $eme; ?></h5>
                                  <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                </div>
                                <div class="offcanvas-body">
                                  <div class="timeline">
                                    <?php
                                    $query_logs = $conn->query("SELECT * FROM logs WHERE stafi = '" . $eme . "' ORDER BY id DESC");
                                    while ($logs = mysqli_fetch_array($query_logs)) {
                                    ?>
                                      <div class="timeline-item">
                                        <div class="timeline-item-content bg-light border rounded-5 p-3 my-3">
                                          <span class="text-muted d-block mb-2"><?php echo $logs['koha']; ?></span>
                                          <p class="mb-3 fs-6 text-wrap"><?php echo $logs['ndryshimi']; ?></p>
                                        </div>
                                      </div>
                                    <?php
                                    }
                                    ?>
                                  </div>
                                </div>
                              </div>
                              <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRightEdit_<?php echo $k['id']; ?>" aria-labelledby="offcanvasRightEditLabel_<?php echo $k['id']; ?>">
                                <div class="offcanvas-header">
                                  <h5 id="offcanvasRightEditLabel_<?php echo $k['id']; ?>">Përditso rrogën e <?php echo $eme; ?></h5>
                                  <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                </div>
                                <div class="offcanvas-body">
                                  <!-- Your edit form with salary input -->
                                  <form id="editForm_<?php echo $k['id']; ?>">
                                    <div class="card rounded-5 shadow-sm bg-white" style="width: 100%;">
                                      <div class="card-body">
                                        <h5 class="card-title" style="text-transform: none;text-decoration: none;">Përshkrimi i detajeve të puntorit - <?php echo $eme; ?></h5>
                                        <p class="card-text" id="name">Emri & Mbiemri : <?php echo $eme; ?></p>
                                        <p class="card-text" id="email"> Email : <?php echo $k['email']; ?></p>
                                        <?php
                                        // List of allowed emails
                                        $allowedGmailEmails = array(
                                          'afrimkolgeci@gmail.com',
                                          'besmirakolgeci1@gmail.com',
                                          'egjini17@gmail.com',
                                          'bareshafinance@gmail.com',
                                          'gjinienis148@gmail.com'
                                        );
                                        // Check if user's email exists in the allowed list
                                        if (isset($user_info['email']) && in_array($user_info['email'], $allowedGmailEmails)) {
                                          // Prepare the SQL to fetch user information based on OAuth UID linked to the email
                                          $sqlStaf = "SELECT * FROM googleauth WHERE email = ?";
                                          if ($stmtStaf = $conn->prepare($sqlStaf)) {
                                            $stmtStaf->bind_param("s", $user_info['email']);
                                            $stmtStaf->execute();
                                            $resultStaf = $stmtStaf->get_result();
                                            $rowStaf = $resultStaf->fetch_assoc();
                                            // Close the statement after use
                                            $stmtStaf->close();
                                            // Proceed if user information is found
                                            if ($rowStaf) {
                                              // Fetch the role ID of the user
                                              $sql_for_role = "SELECT role_id FROM user_roles WHERE user_id = ?";
                                              if ($stmt_for_role = $conn->prepare($sql_for_role)) {
                                                $stmt_for_role->bind_param("i", $rowStaf['id']);
                                                $stmt_for_role->execute();
                                                $result_for_role = $stmt_for_role->get_result();
                                                $row_for_role = $result_for_role->fetch_assoc();
                                                $stmt_for_role->close();
                                                $role_id = $row_for_role['role_id'];
                                                // Fetch the role name based on role ID
                                                $get_role_name = "SELECT name FROM roles WHERE id = ?";
                                                if ($stmt_role_name = $conn->prepare($get_role_name)) {
                                                  $stmt_role_name->bind_param("i", $role_id);
                                                  $stmt_role_name->execute();
                                                  $result_role_name = $stmt_role_name->get_result();
                                                  $row_role_name = $result_role_name->fetch_assoc();
                                                  $stmt_role_name->close();
                                                  $role_name = $row_role_name['name'];
                                                  // Display the OAuth UID only for administrators
                                                  if ($role_name === "Administrator") {
                                                    echo '<p class="card-text uid" style="filter: blur(5px);">OAuth UID: ' . htmlspecialchars($rowStaf['oauth_uid']) . '</p>';
                                                  }
                                                }
                                              }
                                            }
                                          }
                                        } else {
                                          // echo "Access Denied: Your email is not authorized to view this information.";
                                        }
                                        ?>
                                        <p class="card-text" id="role">Rroga aktuale : <?php echo $k['salary']; ?> &euro;</p>
                                      </div>
                                    </div>
                                    <br>
                                    <label for="salary" class="form-label">Rroga:</label>
                                    <input type="text" class="form-control rounded-5 border border-2 shadow-0" id="salary_<?php echo $k['id']; ?>" name="salary" value="<?php echo $k['salary']; ?>">
                                    <br>
                                    <button type="button" class="input-custom-css px-3 py-2" onclick="saveEmployee(<?php echo $k['id']; ?>)">Ruaj ndryshimet e bëra</button>
                                  </form>
                                </div>
                              </div>
                            </td>
                          </tr>
                      <?php
                        }
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <script>
          $(document).ready(function() {
            $(".uid").hover(function() {
              $(this).css("filter", "none");
            }, function() {
              $(this).css("filter", "blur(5px)");
            });
          });
        </script>
        <div class="tab-pane fade" id="pills-listaETentativave" role="tabpanel" aria-labelledby="pills-listaETentativave-tab" tabindex="0">
          <div class="card rounded-5 shadow-sm">
            <div class="card-body">
              <div class="row">
                <div class="col-12">
                  <table id="access_denial_logs" class="table table-bordered w-full">
                    <thead class="bg-light">
                      <tr>
                        <th>ID</th>
                        <th>Ip Address</th>
                        <th>Email attempted</th>
                        <th>User Agent</th>
                        <th>Timestamp</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $kueri = $conn->query("SELECT * FROM access_denial_logs ORDER BY id DESC");
                      while ($k = mysqli_fetch_array($kueri)) {
                      ?>
                        <tr>
                          <td><?php echo $k['id']; ?></td>
                          <td>
                            <!-- Button to trigger modal -->
                            <button type="button" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#modal<?php echo $k['id']; ?>">
                              <i class="fi fi-rr-info"></i>
                            </button>
                          </td>
                          <td><?php echo $k['email_attempted']; ?></td>
                          <td><?php echo $k['user_agent']; ?></td>
                          <td><?php echo $k['timestamp']; ?></td>
                        </tr>
                        <!-- Modal Structure -->
                        <div class="modal fade" id="modal<?php echo $k['id']; ?>" tabindex="-1" aria-labelledby="modalLabel<?php echo $k['id']; ?>" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="modalLabel<?php echo $k['id']; ?>">IP Address Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                IP Address: <?php echo $k['ip_address']; ?>
                              </div>
                            </div>
                          </div>
                        </div>
                      <?php
                      }
                      ?>
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
  // Function to handle AJAX salary update
  function saveEmployee(employeeId) {
    const newSalary = document.getElementById(`salary_${employeeId}`).value;
    // Send AJAX request to update salary
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_salary.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    // Handle the response from update_salary.php
    xhr.onload = function() {
      if (xhr.status == 200) {
        const response = JSON.parse(xhr.responseText);
        // Check the status of the response
        if (response.status === 'success') {
          // If update is successful, show success message
          Swal.fire({
            title: 'Përditësuar!',
            text: response.message,
            icon: 'success'
          }).then(() => {
            // Reload the page or perform any other action
            location.reload();
          });
        } else {
          // If an error occurs during update, show error message
          Swal.fire({
            title: 'Gabim!',
            text: response.message,
            icon: 'error'
          });
        }
      }
    };
    // Send the request with the employee ID and salary
    xhr.send('id=' + employeeId + '&salary=' + newSalary);
  }

  function editEmployee(employeeId, currentSalary) {
    // Set the dynamic ID for the offcanvas and form
    const offcanvasId = `offcanvasRightEdit_${employeeId}`;
    const formId = `editForm_${employeeId}`;
    // Set the dynamic IDs to the offcanvas and form
    document.getElementById(offcanvasId).id = offcanvasId;
    document.getElementById(formId).id = formId;
    // Populate the form fields with the current salary
    document.getElementById(`salary_${employeeId}`).value = currentSalary;
  }
</script>
<script>
  $(document).ready(function() {
    // Handle delete button click using event delegation
    $('#example').on('click', '.delete-btn', function(event) {
      const employeeId = $(this).data('id');
      // Show SweetAlert2 confirmation dialog
      Swal.fire({
        title: 'A je i sigurt?',
        text: 'Ju nuk do të jeni në gjendje ta ktheni këtë!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Po, fshijeni!'
      }).then((result) => {
        if (result.isConfirmed) {
          // If user confirms, send AJAX request to delete_employ.php
          deleteEmployee(employeeId);
        }
      });
    });
    // Function to handle AJAX deletion
    function deleteEmployee(employeeId) {
      // Send AJAX request to delete_employ.php
      $.ajax({
        type: 'POST',
        url: 'delete_employ.php',
        data: {
          id: employeeId
        },
        dataType: 'json',
        success: function(response) {
          // Check the status of the response
          if (response.status === 'success') {
            // If deletion is successful, show success message
            Swal.fire({
              title: 'U fshi!',
              text: response.message,
              icon: 'success'
            }).then(() => {
              // Reload the page or perform any other action
              location.reload();
            });
          } else {
            // If an error occurs during deletion, show error message
            Swal.fire({
              title: 'Gabim!',
              text: response.message,
              icon: 'error'
            });
          }
        },
        error: function(xhr, status, error) {
          // Handle error if AJAX request fails
          console.error(xhr.responseText);
        }
      });
    }
  });
</script>
<script>
  $('#example').DataTable({
    search: {
      return: true,
    },
    dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
      "<'row'<'col-md-12'tr>>" +
      "<'row'<'col-md-6'><'col-md-6'p>>",
    buttons: [{
      extend: 'pdfHtml5',
      text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
      titleAttr: 'Eksporto tabelen ne formatin PDF',
      className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
      filename: 'lista_e_stafit',
    }, {
      extend: 'copyHtml5',
      text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
      titleAttr: 'Kopjo tabelen ne formatin Clipboard',
      className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
      filename: 'lista_e_stafit',
    }, {
      extend: 'excelHtml5',
      text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
      titleAttr: 'Eksporto tabelen ne formatin CSV',
      className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
      filename: 'lista_e_stafit',
    }, {
      extend: 'print',
      text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
      titleAttr: 'Printo tabel&euml;n',
      className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
      filename: 'lista_e_stafit',
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
<script>
  $('#access_denial_logs').DataTable({
    searching: true,
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
      titleAttr: 'Eksporto tabelen ne formatin CSV',
      className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
    }, {
      extend: 'print',
      text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
      titleAttr: 'Printo tabel&euml;n',
      className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
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
    columnDefs: [{
      "targets": [0, 1, 2, 3, 4],
      "render": function(data, type, row) {
        return type === 'display' && data !== null ? '<div style="white-space: normal;">' + data + '</div>' : data;
      }
    }],
    fixedHeader: true,
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
    },
    stripeClasses: ['stripe-color']
  })
</script>