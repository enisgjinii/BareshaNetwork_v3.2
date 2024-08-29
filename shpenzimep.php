<?php include 'partials/header.php';

if (isset($_POST['ruaj'])) {
  $shuma = mysqli_real_escape_string($conn, $_POST['shuma']);
  $data = mysqli_real_escape_string($conn, $_POST['data']);
  $pagesa = mysqli_real_escape_string($conn, $_POST['forma']);
  $pershkrimi = mysqli_real_escape_string($conn, $_POST['pershkrimi']);

  // Attempt to insert data into the database
  $query = "INSERT INTO shpenzimep (shuma, pershkrimi, data, pagesa) VALUES ('$shuma', '$pershkrimi','$data', '$pagesa')";

  if ($conn->query($query)) {
    // Data insertion was successful
    echo '<script>
          Swal.fire({
              icon: "success",
              title: "Sukses!",
              text: "Te dhenat u ruajten me sukses.",
              showConfirmButton: false,
              timer: 1500
          });
      </script>';
  } else {
    // Data insertion failed
    echo '<script>
          Swal.fire({
              icon: "error",
              title: "Gabim!",
              text: "Nje gabim ndodhi gjate ruajtjes se te dhenave.",
              showConfirmButton: false,
              timer: 1500
          });
      </script>';
  }
}


/**
 * Formatizon madhësinë e një dosjeje.
 *
 * @param string $filename Emri i dosjes.
 * @throws Exception Nëse dosja nuk ekziston.
 * @return string Madhësia e formatizuar e dosjes.
 */
const UNITS = array('B', 'KB', 'MB', 'GB', 'TB');

function formatFileSize($filename)
{
  if (!file_exists($filename)) {
    $size = 0;
  } else {
    $size = filesize($filename);
  }

  $formattedSize = $size;
  $i = 0;
  while ($size >= 1024 && $i < count(UNITS) - 1) {
    $size /= 1024;
    $formattedSize = round($size, 2);
    $i++;
  }

  if ($size === false) {
    return 'E panjohur';
  }

  return sprintf('%.2f %s', $formattedSize, UNITS[$i]);
}

$filename = __FILE__;
$fileSize = formatFileSize($filename);
$text = 'Madhësia e dosjes: ' . $fileSize;
?>
<form method="POST" action="">
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Shto borxh personal</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

          <div class="mb-3">
            <label for="shuma" class="form-label">Shuma</label>
            <input type="text" name="shuma" class="form-control rounded-5 border border-2" id="shuma" value="0.00">
          </div>

          <div class="mb-3">
            <label for="data" class="form-label">Data e pagesës</label>
            <input type="text" name="data" class="form-control rounded-5 border border-2" id="data" value="<?php echo date("Y-m-d"); ?>">
            <!-- Use flatpickr.js -->
            <script>
              flatpickr("#data", {
                appearance: "dark",
                allowInput: true,
                enableTime: true,
                dateFormat: "Y-m-d",
                maxDate: new Date().toISOString().split("T")[0],
              })
            </script>
          </div>

          <div class="mb-3">
            <label for="forma" class="form-label">Forma e pagesës</label>
            <select name="forma" class="form-select" id="forma">
              <option value="Cash">Cash</option>
              <option value="Bank">Bank</option>
            </select>
            <script>
              new Selectr('#forma', {
                searchable: true,
              });
            </script>
          </div>

          <div class="mb-3">
            <label for="pershkrimi" class="form-label">Përshkrimi</label>
            <textarea name="pershkrimi" class="form-control rounded-5 border border-2" id="pershkrimi"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button>
          <input type="submit" class="input-custom-css px-3 py-2" name="ruaj" value="Ruaj">

        </div>
      </div>
    </div>
  </div>
</form>
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
                Borxhet personale
              </a>
            </li>
        </nav>

        <div class="row mb-2">
          <div>
            <!-- Button trigger modal -->
            <button type="button" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
              <i class="fi fi-rr-add"></i> &nbsp; Shto borxh
            </button>
            <button id="deleteRowsBtn" class="input-custom-css px-3 py-2">
              <i class="fi fi-rr-trash"></i> &nbsp; Fshij
            </button>
          </div>
        </div>


        <div class="card shadow-sm rounded-5">
          <div class="card-body">
            <div class="row">
              <div class="col-12">
                <div class="text-end mt-3">

                </div>
                <div class="table-responsive">
                  <table id="example" class="table w-100 table-bordered">
                    <thead class="bg-light">
                      <tr>
                        <th class="text-dark">#</th> <!-- Add this column -->
                        <th class="text-dark">ID</th>
                        <th class="text-dark">Shuma</th>
                        <th class="text-dark">P&euml;rshkrimi</th>
                        <th class="text-dark">Data</th>
                        <th class="text-dark">Forma</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
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
  $(document).ready(function() {
    var dataTable = $('#example').DataTable({
      search: {
        return: true,
      },
      order: [
        [0, "desc"]
      ],
      ajax: {
        url: 'api/get_methods/get_expenses.php', // Replace with your server-side script to fetch data
        type: 'POST',
        dataSrc: ''
      },
      columns: [{
          data: null,
          render: function(data, type, row) {
            return '<input type="checkbox" class="deleteCheckbox">';
          }
        },

        {
          data: 'id',
        },
        {
          data: 'shuma',
          title: 'Shuma'
        },
        {
          data: 'pershkrimi',
          title: 'Pershkrimi'
        },
        {
          data: 'data',
          title: 'Data'
        },
        {
          data: 'pagesa',
          title: 'Forma e pagesës'
        }


      ],
      columnDefs: [{
        "targets": [0, 1, 2, 3], // Indexes of the columns you want to apply the style to
        "render": function(data, type, row) {
          // Apply the style to the specified columns
          return type === 'display' && data !== null ? '<div style="white-space: normal;">' + data + '</div>' : data;
        }
      }],
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
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
      },
      stripeClasses: ['stripe-color'],
      columnDefs: [{
        "targets": [0, 1, 2, 3, 4], // Indexes of the columns you want to apply the style to
        "render": function(data, type, row) {
          // Apply the style to the specified columns
          return type === 'display' && data !== null ? '<div style="white-space: normal;">' + data + '</div>' : data;
        }
      }],
    })


    $('#deleteRowsBtn').on('click', function() {
      // Get all checked checkboxes
      var checkboxes = $('.deleteCheckbox:checked');

      // Get the IDs of the selected rows
      var ids = checkboxes.map(function() {
        return dataTable.row($(this).closest('tr')).data().id;
      }).get();

      // Show a confirmation dialog with SweetAlert2
      Swal.fire({
        title: 'A jeni të sigurt që dëshironi të fshini këto rreshta?',
        text: 'Ky veprim nuk mund të kthehet mbrapa!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Po, fshij!',
        cancelButtonText: 'Anulo'
      }).then((result) => {
        if (result.isConfirmed) {
          // If the user confirms, perform the deletion using AJAX
          $.ajax({
            url: 'delete_expense.php',
            method: 'POST',
            data: {
              ids: ids
            },
            dataType: 'json',
            success: function(response) {
              // Check if the deletion was successful
              if (response.success) {
                // Update DataTable
                dataTable.ajax.reload();

                // Show success message with SweetAlert2
                Swal.fire({
                  icon: 'success',
                  title: 'Rreshtat janë fshirë',
                  text: 'Rreshtat e përzgjedhura janë fshirë me sukses.',
                });
              } else {
                // Show error message with SweetAlert2
                Swal.fire({
                  icon: 'error',
                  title: 'Gabim',
                  text: 'Gabim gjatë fshirjes së rreshtave. Ju lutemi provoni përsëri.',
                });
              }
            },
            error: function() {
              // Show error message with SweetAlert2
              Swal.fire({
                icon: 'error',
                title: 'Gabim',
                text: 'Gabim gjatë fshirjes së rreshtave. Ju lutemi provoni përsëri.',
              });
            }
          });
        }
      });
    });
  });
</script>