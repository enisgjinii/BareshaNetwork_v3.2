<?php
// Include the header partial and handle inclusion errors
if (!@include 'partials/header.php') {
  // Terminate the script if the header cannot be included
  die("Error: Unable to include header.php");
}
/**
 * Fetch data using cURL.
 *
 * @param string $url The API endpoint URL.
 * @param string|null $post_fields The data to POST (if any).
 * @param bool $is_json Whether the POST data is JSON.
 * @return array An associative array with 'success' and either 'data' or 'error'.
 */
function fetchData($url, $post_fields = null, $is_json = false)
{
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  if ($post_fields !== null) {
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    if ($is_json) {
      curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($post_fields)
      ]);
    }
  }
  $response = curl_exec($ch);
  if ($response === FALSE) {
    $error_msg = curl_error($ch);
    curl_close($ch);
    return ['success' => false, 'error' => "cURL Error: {$error_msg}"];
  }
  $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if ($http_status !== 200) {
    curl_close($ch);
    return ['success' => false, 'error' => "HTTP Error: {$http_status}"];
  }
  curl_close($ch);
  return ['success' => true, 'data' => $response];
}
/**
 * Handle adding or removing a channel from the whitelist.
 *
 * @param mysqli $conn The database connection.
 * @param array $user_info The user information array.
 * @param string $action The action to perform ('shtuar në' or 'fshirë nga').
 * @param string $channel_id The ID of the channel.
 * @param string $note Optional note for adding a channel.
 * @return void
 */
function handleAction($conn, $user_info, $action, $channel_id, $note = '')
{
  // Trim and validate the channel ID
  $channel_id = trim($channel_id);
  if (empty($channel_id)) {
    echo "<script>Swal.fire({icon:'error', title:'Gabim', text:'Channel ID nuk mund të jetë bosh.'});</script>";
    return;
  }
  // Sanitize and concatenate the user's full name
  $user_name = htmlspecialchars($user_info['givenName'] . ' ' . $user_info['familyName'], ENT_QUOTES, 'UTF-8');
  // Get the current date and time
  $date = date('Y-m-d H:i:s');
  // Create a log description
  $log_description = "$user_name ka $action Whitelist kanalin me id $channel_id me datë: $date";
  // Prepare the SQL statement to insert the log
  $stmt = $conn->prepare("INSERT INTO logs (stafi, ndryshimi, koha) VALUES (?, ?, ?)");
  if (!$stmt) {
    echo "<script>Swal.fire({icon:'error', title:'Gabim', text:'Gabim në përgatitjen e pyetjes së bazës së të dhënave: " . $conn->error . "'});</script>";
    return;
  }
  // Bind parameters and execute the statement
  if (!$stmt->bind_param("sss", $user_name, $log_description, $date)) {
    echo "<script>Swal.fire({icon:'error', title:'Gabim', text:'Gabim në lidhjen e parametrave: " . $stmt->error . "'});</script>";
    $stmt->close();
    return;
  }
  if (!$stmt->execute()) {
    echo "<script>Swal.fire({icon:'error', title:'Gabim', text:'Gabim gjatë ekzekutimit të pyetjes së bazës së të dhënave: " . $stmt->error . "'});</script>";
    $stmt->close();
    return;
  }
  $stmt->close();
  // Define API token and base URL
  $api_token = '6636-66f549fbe813b2087a8748f2b8243dbc';
  $base_api_url = 'https://bareshamusic.sourceaudio.com/api/contentid/';
  // Construct the API endpoint and payload based on action
  if ($action === 'shtuar në') {
    $api_endpoint = 'whitelistChannel';
    $payload = [
      'token' => $api_token,
      'channel' => [
        [
          'channel_id' => $channel_id,
          'note' => $note
        ]
      ]
    ];
  } else { // Removing from whitelist
    $api_endpoint = 'whitelistRemove';
    $payload = [
      'token' => $api_token,
      'channel' => [
        'channel_id' => $channel_id
      ]
    ];
  }
  // Encode payload as JSON
  $post_fields = json_encode($payload);
  if ($post_fields === false) {
    echo "<script>Swal.fire({icon:'error', title:'Gabim', text:'Gabim në kodimin e të dhënave të API-së.'});</script>";
    return;
  }
  // Fetch API response using cURL
  $api_response = fetchData("{$base_api_url}{$api_endpoint}", $post_fields, true);
  if (!$api_response['success']) {
    echo "<script>Swal.fire({icon:'error', title:'Gabim', text:'{$api_response['error']}'});</script>";
    return;
  }
  // Decode the API response
  $api_data = json_decode($api_response['data'], true);
  if (json_last_error() !== JSON_ERROR_NONE) {
    echo "<script>Swal.fire({icon:'error', title:'Gabim', text:'Gabim në dekodimin e përgjigjes së API-së: " . json_last_error_msg() . "'});</script>";
    return;
  }
  // Determine the result based on API response
  if (isset($api_data['error']) && $api_data['error']) {
    $api_result = ['error', 'Gabim', 'Gabimi: ' . htmlspecialchars($api_data['error'], ENT_QUOTES, 'UTF-8')];
  } else {
    $api_result = ['success', 'Sukses', 'Operacioni është kryer me sukses.'];
  }
  // Display success or error messages using SweetAlert
  echo "<script>Swal.fire({icon:'success', title:'Sukses', text:'Veprimi u krye me sukses.'});</script>";
  echo "<script>Swal.fire({icon:'{$api_result[0]}', title:'{$api_result[1]}', text:'{$api_result[2]}' });</script>";
}
/**
 * Fetch whitelist data from the API.
 *
 * @param string $url The API endpoint URL.
 * @return array The decoded whitelist data or an empty array on failure.
 */
function fetchWhitelistData($url)
{
  $api_response = fetchData($url);
  if (!$api_response['success']) {
    echo "<script>Swal.fire({icon:'error', title:'Gabim', text:'{$api_response['error']}'});</script>";
    return ['whitelist' => []];
  }
  // Decode the API response
  $data = json_decode($api_response['data'], true);
  if (json_last_error() !== JSON_ERROR_NONE) {
    echo "<script>Swal.fire({icon:'error', title:'Gabim', text:'Gabim në dekodimin e whitelist-it: " . json_last_error_msg() . "'});</script>";
    return ['whitelist' => []];
  }
  // Validate the structure of the whitelist data
  if (!isset($data['whitelist']) || !is_array($data['whitelist'])) {
    echo "<script>Swal.fire({icon:'error', title:'Gabim', text:'Të dhënat e whitelist-it nuk janë në formatin e pritur.'});</script>";
    return ['whitelist' => []];
  }
  return $data;
}
// Check if the request method is POST and handle adding a channel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['channel_id'])) {
  // Sanitize POST inputs
  $channel_id = filter_input(INPUT_POST, 'channel_id', FILTER_SANITIZE_STRING);
  $note = filter_input(INPUT_POST, 'note', FILTER_SANITIZE_STRING);
  handleAction($conn, $user_info, 'shtuar në', $channel_id, $note);
}
// Check if the request method is GET and handle removing a channel
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['remove'])) {
  // Sanitize GET input
  $remove_channel_id = filter_input(INPUT_GET, 'remove', FILTER_SANITIZE_STRING);
  handleAction($conn, $user_info, 'fshirë nga', $remove_channel_id);
}
// Fetch whitelist data from the API
$whitelist_api_url = 'https://bareshamusic.sourceaudio.com/api/contentid/whitelist?token=6636-66f549fbe813b2087a8748f2b8243dbc';
$whitelist_data = fetchWhitelistData($whitelist_api_url);
?>
<!-- Main Panel -->
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Breadcrumb Navigation -->
      <nav class="bg-white px-2 rounded-5" style="width:fit-content;" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a class="text-reset" style="text-decoration: none;">Content ID</a></li>
          <li class="breadcrumb-item active" aria-current="page"><a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">Whitelist</a></li>
        </ol>
      </nav>
      <!-- Add Channel Button -->
      <div class="row mb-2">
        <div>
          <button type="button" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#shtochannel">
            <i class="fi fi-rr-add"></i> &nbsp; Shto kanal
          </button>
        </div>
      </div>
      <!-- Whitelist Table -->
      <div class="card shadow-sm rounded-5">
        <div class="card-body">
          <div class="table-responsive">
            <table id="example" class="table w-100 table-bordered">
              <thead class="bg-light">
                <tr>
                  <th class="text-dark">Kanali</th>
                  <th class="text-dark">Shënime</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($whitelist_data['whitelist'])): ?>
                  <?php foreach ($whitelist_data['whitelist'] as $channel): ?>
                    <tr>
                      <td>
                        <a class="input-custom-css px-3 py-2" href="https://www.youtube.com/channel/<?php echo htmlspecialchars($channel['channel_id'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" style="text-decoration: none;">
                          <?php echo htmlspecialchars($channel['channel_name'], ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                      </td>
                      <td><?php echo htmlspecialchars($channel['data']['note'], ENT_QUOTES, 'UTF-8'); ?></td>
                      <td>
                        <a class="input-custom-css px-3 py-2" href="whitelist.php?remove=<?php echo urlencode($channel['channel_id']); ?>" style="text-decoration: none;" onclick="return confirm('A jeni i sigurt që dëshironi të fshini këtë kanal nga whitelist?');">
                          <i class="fi fi-rr-trash"></i>
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="3" class="text-center">Nuk ka kanale të whitelistuara.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Add Channel Modal -->
<div class="modal fade" id="shtochannel" tabindex="-1" role="dialog" aria-labelledby="shtochannelLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" action="whitelist.php">
        <div class="modal-header">
          <h5 class="modal-title" id="shtochannelLabel">Shto një kanal</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Channel ID Input -->
          <div class="mb-3">
            <label for="channel_id" class="form-label">Channel ID:</label>
            <input type="text" name="channel_id" id="channel_id" placeholder="Channel ID" class="form-control rounded-5 border border-2" required>
          </div>
          <!-- Note Input -->
          <div class="mb-3">
            <label for="note" class="form-label">Përshkrim:</label>
            <input type="text" name="note" id="note" placeholder="Përshkrimi" class="form-control rounded-5 border border-2">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button>
          <button type="submit" class="input-custom-css px-3 py-2">Shto</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include 'partials/footer.php'; ?>
<script>
  // Initialize DataTable with custom configurations
  $('#example').DataTable({
    searching: true,
    dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>><'row'<'col-md-12'tr>><'row'<'col-md-6'><'col-md-6'p>>",
    buttons: [{
        extend: "pdfHtml5",
        text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
        titleAttr: "Eksporto tabelen ne formatin PDF",
        className: "btn btn-light btn-sm bg-light border me-2 rounded-5"
      },
      {
        extend: "copyHtml5",
        text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
        titleAttr: "Kopjo tabelen ne formatin Clipboard",
        className: "btn btn-light btn-sm bg-light border me-2 rounded-5"
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
            page: "all"
          }
        }
      },
      {
        extend: "print",
        text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
        titleAttr: "Printo tabelën",
        className: "btn btn-light btn-sm bg-light border me-2 rounded-5"
      }
    ],
    initComplete: function() {
      var btns = $(".dt-buttons");
      btns.removeClass("dt-buttons btn-group").addClass("");
      var lengthSelect = $("div.dataTables_length select");
      lengthSelect.addClass("form-select").css({
        width: "auto",
        margin: "0 8px",
        padding: "0.375rem 1.75rem 0.375rem 0.75rem",
        lineHeight: "1.5",
        border: "1px solid #ced4da",
        borderRadius: "0.25rem"
      });
    },
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json"
    },
    stripeClasses: ['stripe-color']
  });
</script>