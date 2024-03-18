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
      <div class="card rounded-5 shadow-sm">
        <div class="card-body">
          <div class="row">
            <div class="col-12">
              <table id="example" class="table table-bordered" style="width: 100%;">
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

                          <button class="btn btn-sm btn-success rounded-5 shadow-0 px-2 py-2 text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRightAddSalary" aria-controls="offcanvasRightAddSalary">
                            <i class="fi fi-rr-search-alt"></i>
                          </button>

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
                                    <p class="card-text" id="uid">Oauth UID : <?php echo $k['oauth_uid']; ?></p>
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
  document.addEventListener('DOMContentLoaded', function() {
    // Handle delete button click
    document.querySelectorAll('.delete-btn').forEach(function(button) {
      button.addEventListener('click', function() {
        const employeeId = this.getAttribute('data-id');

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
    });

    // Function to handle AJAX deletion
    function deleteEmployee(employeeId) {
      // Send AJAX request to delete_employ.php
      const xhr = new XMLHttpRequest();
      xhr.open('POST', 'delete_employ.php', true);
      xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

      // Handle the response from delete_employ.php
      xhr.onload = function() {
        if (xhr.status == 200) {
          const response = JSON.parse(xhr.responseText);

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
        }
      };

      // Send the request with the employee ID
      xhr.send('id=' + employeeId);
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
    fixedHeader: true,
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
    },
    stripeClasses: ['stripe-color']
  })
</script>