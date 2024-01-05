<?php

include 'partials/header.php';
if (isset($_GET['claim'])) {
  $cid = $_GET['claim'];
  $cdata = date("Y-m-d H:i:s");
  $cname = $_SESSION['emri'];
  $cnd = $cname . " ka ber Release Claim k&euml;ng&euml;n me Claim ID " . $cid;
  $query = "INSERT INTO logs (stafi, ndryshimi, koha) VALUES ('$cname', '$cnd', '$cdata')";
  if ($conn->query($query)) {
  } else {
    echo '<script>alert("' . $conn->error . '")</script>';
  }
  $cjson = file_get_contents('https://bareshamusic.sourceaudio.com/api/contentid/releaseclaim?token=6636-66f549fbe813b2087a8748f2b8243dbc&release[0][type]=claim&release[0][id]=' . $cid);

  $cdata = json_decode($cjson, true);
  if (isset($cdata['error']) && $cdata['error'] == true) {
    echo '<script>alert("' . $cdata['error'] . '");</script>';
  } else {
    echo '<script>Sukses.</script>';
  }
}
/**
 * Formats the size of a file.
 *
 * @param string $filename The name of the file.
 * @throws Exception If the file does not exist.
 * @return string The formatted size of the file.
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
    return 'Unknown';
  }
  
  return sprintf('%.2f %s', $formattedSize, UNITS[$i]);
}
$filename =   __FILE__;
$fileSize = formatFileSize($filename);
$text = 'Madhësia e dosjes: ' . $fileSize;
?>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="container">
        <nav class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Content ID</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
              <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">
                Release Claim
              </a>
            </li>
        </nav>
        <form id="claimForm" class="mb-3 p-3 rounded-5 bg-white card">
          <div class="row">
            <div class="col-md-4 mb-2">
              <label for="show" class="form-label">Shfaq (në faqe):</label>
              <p class="text-muted" style="font-size: 12px">Numri i elementeve për t'u shfaqur në tabelë.</p>
              <input type="number" id="show" name="show" value="10" class="form-control rounded-5 border border-2">
            </div>

            <div class="col-md-4 mb-2">
              <label for="pg" class="form-label">Faqja:</label>
              <p class="text-muted" style="font-size: 12px">Numri i faqes për të kërkuar të dhënat.</p>
              <input type="number" id="pg" name="pg" value="1" class="form-control rounded-5 border border-2">
            </div>
            <div class="col-md-4 mb-2">
            </div>

            <div class="mb-2">
              <button type="submit" class="input-custom-css px-3 py-2"><i class="fi fi-rr-paper-plane me-2"></i> Dërgo</button>
              <button type="button" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
                <i class="fi fi-rr-info"></i>
              </button>
            </div>



            <!-- Button trigger modal -->


            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Informacion</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <p class="text-muted" style="font-size: 12px">
                      Përdorni këto fusha për të kontrolluar numrin e elementeve që shfaqen në tabelë dhe numrin e faqeve të kërkuara për të marrë të dhënat.( Bazohu ne imazh) <br><br>
                      <img src="images/inputs.png" class="img-fluid border" alt=""><br>
                      <br>
                      Shfaqja e një numri të madh të elementeve në një herë mund të ngadalësojë shfletuesin tuaj. <br>
                      Rekomandohet të përdorni një numër të moderuar për të siguruar një përformancë optimale në shfletues.
                    </p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>
        </form>
      </div>



      <div class="card rounded-5">
        <div class="card-body">
          <div class="row">
            <div class="col-12">
              <div class="table-responsive">
                <table id="claims_table" class="table w-100 table-bordered">
                  <thead class="bg-light">
                    <tr>
                      <th>Track ID</th>
                      <th>Video</th>
                      <th>Kanali</th>
                      <th>Claim ID</th>
                      <th>Data</th>
                      <th>Action</th>
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
      <p class="text-muted mt-3"><?php echo $text; ?></p>
    </div>
  </div>
</div>
</div>
<?php include 'partials/footer.php'; ?>
<script>
  $(document).ready(function() {
    $('#claimForm').submit(function(e) {
      e.preventDefault(); // Prevent the form from submitting normally

      // Get user-defined parameters
      var show = $('#show').val();
      var pg = $('#pg').val();

      // You can capture more parameters here if needed

      // Send an AJAX request to fetch_claims.php
      $.ajax({
        type: 'POST',
        url: 'fetch_claims.php',
        data: {
          show: show,
          pg: pg
          // Add more parameters here as needed
        },
        success: function(response) {
          // Clear the existing DataTable and populate it with the new data
          var table = $('#claims_table').DataTable();
          table.clear().rows.add(response.claim).draw();
        },
        error: function(xhr, status, error) {
          console.error(error); // Handle errors here
        }
      });
    });

    // Initialize the DataTable separately (outside the submit handler)
    $('#claims_table').DataTable({
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
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
      },
      stripeClasses: ['stripe-color'],
      processing: true,
      ajax: {
        url: 'fetch_claims.php', // Replace with your API endpoint URL
        dataSrc: 'claim', // Specify the JSON data source key
      },
      order: [
        [4, "desc"] // Default ordering by invoice_id in descending order
      ],
      columnDefs: [{
        "targets": [0, 1, 2, 3, 4], // Indexes of the columns you want to apply the style to
        "render": function(data, type, row) {
          // Apply the style to the specified columns
          return type === 'display' && data !== null ? '<div style="white-space: normal;">' + data + '</div>' : data;
        }
      }],
      columns: [
        // Define your table columns here
        {
          data: 'track_id'
        },
        {
          data: 'video_title'
        },
        {
          data: 'video_author'
        },
        {
          data: 'claim_id'
        },
        {
          data: 'date',
          render: function(data) {
            // Convert the Unix timestamp to a Date object
            var date = new Date(data * 1000); // Multiply by 1000 to convert seconds to milliseconds

            // Define the date format you want (e.g., 'YYYY-MM-DD HH:mm:ss')
            var options = {
              year: 'numeric',
              month: '2-digit',
              day: '2-digit',
              hour: '2-digit',
              minute: '2-digit',
              second: '2-digit'
            };

            // Format the date using the options
            return date.toLocaleDateString('en-US', options);
          }
        },
        {
          data: 'released',
          render: function(data, type, row) {
            if (data == '1') {
              return '<p class="bg-success text-white px-3 py-2 rounded-5"><i class="fi fi-rr-check me-2"></i>' + row.released_by + '</p>';
            } else {
              return '<a class="input-custom-css px-3 py-2" style="text-decoration: none;" href="claim.php?claim=' + row.claim_id + '"><i class="fi fi-rr-cloud-upload me-2"></i> Release</a>';
            }
          },
        },
      ],
    });
  });
</script>