<?php
include 'partials/header.php';
?>
<!-- Add Email Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <!-- Modal content for adding email -->
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="addEmailForm">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Shto nj&euml; email</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control rounded-5 border border-2" placeholder="Email" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button>
          <button type="submit" class="input-custom-css px-3 py-2">Ruaj</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Edit Email Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <!-- Modal content for editing email -->
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="editEmailForm">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edito email</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="edit_id" id="edit_id">
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" id="edit_email" class="form-control rounded-5 border border-2" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button>
          <button type="submit" class="input-custom-css px-3 py-2">Përditso</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Main Panel -->
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Breadcrumb and Add Button -->
      <nav class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a class="text-reset" style="text-decoration: none;">Klientët</a></li>
          <li class="breadcrumb-item active" aria-current="page">
            <a href="emails.php" class="text-reset" style="text-decoration: none;">
              Llogaritë e adresave elektronike (emails)
            </a>
          </li>
        </ol>
      </nav>
      <button type="button" class="input-custom-css px-3 py-2 mb-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
        <i class="fi fi-rr-add"></i> Shto email
      </button>
      <!-- DataTable -->
      <div class="card rounded-5 shadow-sm">
        <div class="card-body">
          <table id="emailsTable" class="table w-100 table-bordered">
            <thead class="bg-light">
              <tr>
                <th class="text-dark">ID</th>
                <th class="text-dark">Email</th>
                <th class="text-dark">Veprime</th>
              </tr>
            </thead>
            <tbody>
              <!-- Data will be populated by DataTables via AJAX -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include 'partials/footer.php'; ?>
<script>
  $(document).ready(function() {
    // Initialize DataTable with AJAX source
    var table = $('#emailsTable').DataTable({
      "ajax": "api/get_methods/get_emails_data.php",
      "columns": [{
          "data": "id"
        },
        {
          "data": "email"
        },
        {
          "data": "actions",
          "orderable": false,
          "searchable": false
        }
      ],
      initComplete: function() {
        $(".dt-buttons").removeClass("dt-buttons btn-group");
        $("div.dataTables_length select").addClass("form-select").css({
          width: 'auto',
          margin: '0 8px',
          padding: '0.375rem 1.75rem 0.375rem 0.75rem',
          lineHeight: '1.5',
          border: '1px solid #ced4da',
          borderRadius: '0.25rem',
        });
      },
      "responsive": true,
      "dom": "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
        "<'row'<'col-md-12'tr>>" +
        "<'row'<'col-md-6'><'col-md-6'p>>",
      "buttons": [{
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
      "order": [
        [0, 'desc']
      ],
      "fixedHeader": true,
      "language": {
        "url": "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
      },
      "stripeClasses": ['stripe-color']
    });
    // Function to show SweetAlert with auto-close
    function showSweetAlert(icon, title, text) {
      Swal.fire({
        icon: icon,
        title: title,
        text: text,
        timer: 2000, // 2 seconds
        timerProgressBar: true,
        showConfirmButton: false,
        willClose: () => {
          // Actions after alert closes can be added here if needed
        }
      });
    }
    // Handle Add Email Form Submission
    $('#addEmailForm').on('submit', function(e) {
      e.preventDefault();
      var email = $('input[name="email"]').val();
      // Disable the submit button to prevent multiple submissions
      var submitButton = $(this).find('button[type="submit"]');
      submitButton.prop('disabled', true);
      $.ajax({
        url: 'api/post_methods/post_emails_action.php',
        type: 'POST',
        data: {
          action: 'insert',
          email: email
        },
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            showSweetAlert('success', 'Success', response.message);
            $('#exampleModal').modal('hide');
            $('#addEmailForm')[0].reset();
            table.ajax.reload(null, false); // Reload without resetting pagination
          } else {
            showSweetAlert('error', 'Error', response.message);
          }
        },
        error: function() {
          showSweetAlert('error', 'Error', 'Ndodhi një gabim gjatë shtimit të email-it.');
        },
        complete: function() {
          // Re-enable the submit button after request completes
          submitButton.prop('disabled', false);
        }
      });
    });
    // Open Edit Modal and Populate Data
    $('#emailsTable').on('click', '.btn-edit', function() {
      var id = $(this).data('id');
      var email = $(this).data('email');
      $('#edit_id').val(id);
      $('#edit_email').val(email);
      $('#editModal').modal('show');
    });
    // Handle Edit Email Form Submission
    $('#editEmailForm').on('submit', function(e) {
      e.preventDefault();
      var edit_id = $('#edit_id').val();
      var email = $('#edit_email').val();
      // Disable the submit button to prevent multiple submissions
      var submitButton = $(this).find('button[type="submit"]');
      submitButton.prop('disabled', true);
      $.ajax({
        url: 'emails_action.php',
        type: 'POST',
        data: {
          action: 'update',
          edit_id: edit_id,
          email: email
        },
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            showSweetAlert('success', 'Success', response.message);
            $('#editModal').modal('hide');
            table.ajax.reload(null, false); // Reload without resetting pagination
          } else {
            showSweetAlert('error', 'Error', response.message);
          }
        },
        error: function() {
          showSweetAlert('error', 'Error', 'Ndodhi një gabim gjatë përditësimit të email-it.');
        },
        complete: function() {
          // Re-enable the submit button after request completes
          submitButton.prop('disabled', false);
        }
      });
    });
    // Handle Delete Action
    $('#emailsTable').on('click', '.btn-delete', function() {
      var id = $(this).data('id');
      Swal.fire({
        title: 'A je i sigurt?',
        text: 'Ky veprim nuk mund të ri-kthehet!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Po, fshijeni!',
        cancelButtonText: 'Anulo',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          // Show a loading indicator
          Swal.fire({
            title: 'Fshirja po përpunohet...',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading()
            }
          });
          $.ajax({
            url: 'emails_action.php',
            type: 'POST',
            data: {
              action: 'delete',
              id: id
            },
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                Swal.fire({
                  icon: 'success',
                  title: 'Success',
                  text: response.message,
                  timer: 2000,
                  timerProgressBar: true,
                  showConfirmButton: false
                });
                table.ajax.reload(null, false); // Reload without resetting pagination
              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: response.message,
                  timer: 2000,
                  timerProgressBar: true,
                  showConfirmButton: false
                });
              }
            },
            error: function() {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ndodhi një gabim gjatë fshirjes së email-it.',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
              });
            }
          });
        }
      });
    });
  });
</script>