<?php
ob_start();
session_start();
date_default_timezone_set('Europe/Tirane');
include 'partials/header.php';
// Function to log errors
function logError($message)
{
  // You can customize the log file path
  error_log("[" . date('Y-m-d H:i:s') . "] " . $message . PHP_EOL, 3, 'error_log.txt');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ruaj'])) {
  try {
    require_once "conn-d.php";
    // Example user information; replace with actual user data retrieval
    // Assuming you have a user authentication system
    $user_info = [
      'givenName' => 'John',
      'familyName' => 'Doe'
    ];
    $user_full_name = isset($user_info['givenName'], $user_info['familyName'])
      ? $user_info['givenName'] . ' ' . $user_info['familyName']
      : "Unknown User";
    // Assign form inputs without sanitization
    $kengetari = $_POST['kengtari'] ?? '';
    $emri = $_POST['emri'] ?? '';
    $teksti = $_POST['teksti'] ?? '';
    $muzika = $_POST['muzika'] ?? '';
    $orkestra = $_POST['orkestra'] ?? '';
    $cover = $_POST['cover'] ?? 'Potpuri';
    $co = $_POST['co'] ?? '';
    $facebook = $_POST['facebook'] ?? 'Jo';
    $instagram = $_POST['Instagram'] ?? 'Jo';
    $veper = $_POST['veper'] ?? '';
    $klienti = $_POST['klienti'] ?? '';
    $platforma = $_POST['platforma'] ?? 'YouTube';
    $platformat = isset($_POST['platformat']) ? implode(', ', $_POST['platformat']) : '';
    $linku = $_POST['linku'] ?? '';
    $linkuplat = $_POST['linkuplat'] ?? '';
    $data_field = $_POST['data'] ?? date("Y-m-d");
    $gjuha = $_POST['gjuha'] ?? 'Shqip';
    $infosh = $_POST['infosh'] ?? '';
    $nga = $user_full_name;
    $channelID = $_POST['channelID'] ?? '';
    // Prepare the INSERT statement
    $insertQuery = "INSERT INTO ngarkimi 
                      (kengetari, emri, teksti, muzika, orkestra, co, facebook, instagram, veper, klienti, platforma, platformat, linku, data, gjuha, infosh, nga, linkuplat) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    if ($stmt === false) {
      throw new Exception("Database prepare failed: " . $conn->error);
    }
    $stmt->bind_param(
      'ssssssssssssssssss',
      $kengetari,
      $emri,
      $teksti,
      $muzika,
      $orkestra,
      $co,
      $facebook,
      $instagram,
      $veper,
      $klienti,
      $platforma,
      $platformat,
      $linku,
      $data_field,
      $gjuha,
      $infosh,
      $nga,
      $linkuplat
    );
    $insertSuccess = $stmt->execute();
    if ($insertSuccess === false) {
      throw new Exception("Database execute failed: " . $stmt->error);
    }
    $stmt->close();
    // Log the insertion action
    $log_description = "{$nga} ka ngarkuar '{$kengetari}' në sistem";
    $date_information = date('Y-m-d H:i:s');
    $logQuery = "INSERT INTO logs (stafi, ndryshimi, koha) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($logQuery);
    if ($stmt === false) {
      throw new Exception("Database prepare for logs failed: " . $conn->error);
    }
    $stmt->bind_param("sss", $nga, $log_description, $date_information);
    $logSuccess = $stmt->execute();
    if ($logSuccess === false) {
      throw new Exception("Database execute for logs failed: " . $stmt->error);
    }
    $stmt->close();
    $_SESSION['message'] = [
      'type' => 'success',
      'text' => 'Të dhënat janë ruajtur me sukses.'
    ];
  } catch (Exception $e) {
    // Log the error
    logError($e->getMessage());
    $_SESSION['message'] = [
      'type' => 'error',
      'text' => 'Pati një gabim gjatë përpunimit të kërkesës suaj: ' . $e->getMessage()
    ];
  }
  // Redirect to avoid form resubmission
  header('Location: ' . $_SERVER['PHP_SELF']);
  exit;
}
?>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Breadcrumb Navigation -->
      <nav class="bg-white px-2 rounded-5 mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a class="text-reset" href="#" style="text-decoration: none;">Videot & Ngarkimi</a></li>
          <li class="breadcrumb-item active" aria-current="page">
            <a href="<?php echo htmlspecialchars(__FILE__); ?>" class="text-reset" style="text-decoration: none;">
              Regjistro një këngë
            </a>
          </li>
        </ol>
      </nav>
      <!-- Form Card -->
      <div class="card rounded-5 shadow-sm p-4">
        <form method="POST" action="" enctype="multipart/form-data">
          <!-- Këngëtari and Emri i këngës -->
          <div class="row mb-2">
            <div class="col-md-6 mb-2">
              <label for="kengtari" class="form-label">K&euml;ng&euml;tari
                <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Shëno emrin e plotë të këngëtarit"></i>
              </label>
              <input type="text" name="kengtari" id="kengtari" class="form-control border border-2 rounded-5" placeholder="Shëno emrin e këngëtarit" data-bs-toggle="tooltip" data-bs-placement="right" title="Vendosni emrin e këngëtarit">
            </div>
            <div class="col-md-6 mb-2">
              <label for="emri" class="form-label">Emri i këngës
                <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Shëno emrin e plotë të këngës"></i>
              </label>
              <input type="text" name="emri" id="emri" class="form-control border border-2 rounded-5" placeholder="Shëno emrin e këngës" autocomplete="off" data-bs-toggle="tooltip" data-bs-placement="right" title="Vendosni emrin e këngës">
            </div>
          </div>
          <!-- Teksti Shkrues and Muzika -->
          <div class="row mb-2">
            <div class="col-md-6 mb-2">
              <label for="teksti" class="form-label">Tekst Shkrues
                <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Shëno emrin e plotë të tekstit shkrues"></i>
              </label>
              <input type="text" name="teksti" id="teksti" class="form-control border border-2 rounded-5" placeholder="Shëno tekstin shkruesit" autocomplete="off" data-bs-toggle="tooltip" data-bs-placement="right" title="Vendosni emrin e tekst shkruesit">
            </div>
            <div class="col-md-6 mb-2">
              <label for="muzika" class="form-label">Muzika
                <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Shëno emrin e plotë të muzikës"></i>
              </label>
              <input type="text" name="muzika" id="muzika" class="form-control border border-2 rounded-5" placeholder="Shëno muzikën" autocomplete="off" data-bs-toggle="tooltip" data-bs-placement="right" title="Vendosni emrin e muzikës">
            </div>
          </div>
          <!-- Orkestra and C/O -->
          <div class="row mb-2">
            <div class="col-md-6 mb-2">
              <label for="orkestra" class="form-label">Orkestra
                <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Shëno emrin e orkestrës"></i>
              </label>
              <input type="text" name="orkestra" id="orkestra" class="form-control border border-2 rounded-5" placeholder="Shëno orkestrën" autocomplete="off" data-bs-toggle="tooltip" data-bs-placement="right" title="Vendosni emrin e orkestrës">
            </div>
            <div class="col-md-6 mb-2">
              <label for="co" class="form-label">C / O
                <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Shëno 'C/O' nëse ka"></i>
              </label>
              <input type="text" name="co" id="co" class="form-control border border-2 rounded-5" placeholder="C/O" autocomplete="off" data-bs-toggle="tooltip" data-bs-placement="right" title="Vendosni C/O nëse ka">
            </div>
          </div>
          <hr class="my-3">
          <!-- Cover / Origjinale Radio Buttons and Social Platforms -->
          <div class="row mb-2">
            <div class="col-md-6 mb-2">
              <label class="form-label">Cover / Origjinale
                <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni nëse kënga është Cover, Origjinale ose Potpuri"></i>
              </label>
              <div class="form-check form-check-inline">
                <input type="radio" id="cover" name="cover" value="Cover" class="form-check-input" data-bs-toggle="tooltip" data-bs-placement="right" title="Zgjidhni 'Cover' nëse është një interpretim i një kënge ekzistuese">
                <label for="cover" class="form-check-label">Cover</label>
              </div>
              <div class="form-check form-check-inline">
                <input type="radio" id="origjinale" name="cover" value="Origjinale" class="form-check-input" data-bs-toggle="tooltip" data-bs-placement="right" title="Zgjidhni 'Origjinale' nëse kënga është e re dhe origjinale">
                <label for="origjinale" class="form-check-label">Origjinale</label>
              </div>
              <div class="form-check form-check-inline">
                <input type="radio" id="potpuri" name="cover" value="Potpuri" class="form-check-input" checked data-bs-toggle="tooltip" data-bs-placement="right" title="Zgjidhni 'Potpuri' nëse është një version i përzgjedhur ose i ndryshuar">
                <label for="potpuri" class="form-check-label">Potpuri</label>
              </div>
            </div>
            <div class="col-md-6 mb-2">
              <label class="form-label">Platformat sociale
                <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni platformat sociale ku dëshironi të publikoni këngën"></i>
              </label>
              <div class="form-check form-check-inline">
                <input type="checkbox" id="facebook" name="facebook" value="Po" class="form-check-input" data-bs-toggle="tooltip" data-bs-placement="right" title="Zgjidhni 'Facebook' nëse dëshironi të ndani në Facebook">
                <label for="facebook" class="form-check-label">Facebook</label>
              </div>
              <div class="form-check form-check-inline">
                <input type="checkbox" id="instagram" name="Instagram" value="Po" class="form-check-input" data-bs-toggle="tooltip" data-bs-placement="right" title="Zgjidhni 'Instagram' nëse dëshironi të ndani në Instagram">
                <label for="instagram" class="form-check-label">Instagram</label>
              </div>
            </div>
          </div>
          <hr class="my-3">
          <!-- Veper, Klienti, Platforma, Platformat -->
          <div class="row mb-2">
            <div class="col-md-3 mb-2">
              <label for="veper" class="form-label">Veper Nga Koha
                <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni datën kur kënga është realizuar"></i>
              </label>
              <input type="text" name="veper" id="datepicker" class="form-control border border-2 rounded-5" placeholder="Kliko mbi input dhe zgjedh kohën" autocomplete="off" data-bs-toggle="tooltip" data-bs-placement="right" title="Vendosni datën kur kënga është realizuar">
            </div>
            <div class="col-md-3 mb-2">
              <label for="klientiSelect" class="form-label">Klienti
                <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni klientin për këtë këngë"></i>
              </label>
              <select class="form-select shadow-sm rounded-5" id="klientiSelect" name="klienti" data-bs-toggle="tooltip" data-bs-placement="right" title="Zgjidhni klientin për këtë këngë">
                <option value="" disabled selected>-- Zgjidh Klientin --</option>
                <?php
                $clients = $conn->query("SELECT id, emri, youtube FROM klientet");
                if ($clients) {
                  while ($client = mysqli_fetch_array($clients)) {
                    echo '<option value="' . htmlspecialchars($client['id']) . '">' . htmlspecialchars($client['emri']) . ' | ' . htmlspecialchars($client['youtube']) . '</option>';
                  }
                } else {
                  echo '<option value="" disabled>Ndodhi një gabim gjatë marrjes së klientëve.</option>';
                }
                ?>
              </select>
            </div>
            <div class="col-md-3 mb-2">
              <label for="platforma" class="form-label">Platforma
                <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Platforma e parë për publikimin"></i>
              </label>
              <input type="text" class="form-control border border-2 rounded-5" name="platforma" id="platforma" value="YouTube" readonly data-bs-toggle="tooltip" data-bs-placement="right" title="Platforma e parë për publikimin">
            </div>
            <div class="col-md-3 mb-2">
              <label for="channelId" class="form-label">ChannelId
                <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="ChannelId e parë në publikimin"></i>
              </label>
              <input type="text" id="channelID" name="channelID" class="form-control border border-2 rounded-5" readonly>
            </div>
          </div>
          <!-- Platformat tjera për publikimin -->
          <div class="row mb-2">
            <div class="col-md-3 mb-2">
              <label for="platformat" class="form-label">Platformat tjera për publikimin e këngës
                <br><small>(Mbaj shtypur CTRL për të zgjedhur disa opsione)</small>
                <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni platformat tjera për publikimin"></i>
              </label>
              <select multiple class="form-select shadow-sm rounded-5" name="platformat[]" id="platformat" data-bs-toggle="tooltip" data-bs-placement="right" title="Zgjidhni platformat tjera për publikimin">
                <option value="Spotify">Spotify</option>
                <option value="YouTube Music">YouTube Music</option>
                <option value="iTunes">iTunes</option>
                <option value="Apple Music">Apple Music</option>
                <option value="TikTok">TikTok</option>
                <option value="Instagram Stories">Instagram Stories</option>
                <option value="Tidal">Tidal</option>
                <option value="Amazon Music">Amazon Music</option>
                <option value="Pandora">Pandora</option>
                <option value="AudioMack">AudioMack</option>
              </select>
            </div>
          </div>
          <!-- Linku i këngës and Linku për platformat -->
          <div class="row mb-2">
            <div class="col-md-6 mb-2">
              <label for="linku" class="form-label">Linku i këngës
                <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Vendosni linkun e këngës nëse aplikohet"></i>
              </label>
              <div class="input-group">
                <input type="url" name="linku" id="linku" class="form-control border border-2 rounded-5" placeholder="Vendosni linkun e këngës" autocomplete="off" data-bs-toggle="tooltip" data-bs-placement="right" title="Vendosni linkun e këngës nëse aplikohet">
                <button class="btn btn-outline-secondary px-3 py-2 ms-2 rounded-5" type="button" id="pasteButton" data-bs-toggle="tooltip" data-bs-placement="top" title="Paste link from clipboard">
                  <i class="fi fi-rr-clipboard"></i>
                </button>
              </div>
            </div>
            <div class="col-md-6 mb-2">
              <label for="linkuplat" class="form-label">Linku për platformat
                <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Vendosni linkun për platformat e tjera nëse aplikohet"></i>
              </label>
              <input type="url" name="linkuplat" id="linkuplat" class="form-control border border-2 rounded-5" placeholder="Vendosni linkun për platformat" autocomplete="off" data-bs-toggle="tooltip" data-bs-placement="right" title="Vendosni linkun për platformat e tjera nëse aplikohet">
            </div>
          </div>
          <!-- Song Details and Platform Results -->
          <div class="row mb-3">
            <div class="col-md-12">
              <div id="song-details" class="card mb-3" style="display: none;">
                <div class="row g-0">
                  <div class="col-md-4">
                    <img src="" class="img-fluid rounded-start" alt="Song Thumbnail" id="song-thumbnail">
                  </div>
                  <div class="col-md-8">
                    <div class="card-body">
                      <h5 class="card-title" id="song-title"></h5>
                      <p class="card-text"><strong>Artist:</strong> <span id="song-artist"></span></p>
                      <p class="card-text"><strong>Description:</strong> <span id="song-description"></span></p>
                      <p class="card-text"><strong>Available on:</strong></p>
                      <div id="platform-badges" class="d-flex flex-wrap"></div>
                    </div>
                  </div>
                </div>
              </div>
              <div id="platform-results" class="alert alert-info" role="alert" style="display: none;">
                Checking platforms...
              </div>
              <div id="platform-error" class="alert alert-danger" role="alert" style="display: none;">
                An error occurred while checking platforms.
              </div>
            </div>
          </div>
          <!-- Data and Gjuha -->
          <div class="row mb-2">
            <div class="col-md-6 mb-2">
              <label for="dataChoice" class="form-label">Data
                <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Data e regjistrimit të këngës"></i>
              </label>
              <input type="text" name="data" id="dataChoice" class="form-control border border-2 rounded-5" value="<?php echo date("Y-m-d"); ?>" readonly data-bs-toggle="tooltip" data-bs-placement="right" title="Data e regjistrimit të këngës">
            </div>
            <div class="col-md-6 mb-2">
              <label for="gjuha" class="form-label">Gjuha
                <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni gjuhën e këngës"></i>
              </label>
              <select name="gjuha" id="gjuha" class="form-select shadow-sm rounded-5" data-bs-toggle="tooltip" data-bs-placement="right" title="Zgjidhni gjuhën e këngës">
                <option value="Shqip" selected>Shqip (E parazgjedhur)</option>
                <option value="English">English</option>
                <option value="German">German</option>
              </select>
            </div>
          </div>
          <!-- Informacion Shtesë -->
          <div class="row mb-3">
            <div class="col">
              <label for="simpleMde" class="form-label">Informacion shtesë
                <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Përdorni këtë hapësirë për të dhënë detaje shtesë"></i>
              </label>
              <textarea id="simpleMde" name="infosh" placeholder="Shkruani informacionin shtesë këtu..." class="form-control border border-2 rounded-5" rows="4" data-bs-toggle="tooltip" data-bs-placement="right" title="Shkruani informacionin shtesë këtu..."></textarea>
            </div>
          </div>
          <!-- Submit Button -->
          <button type="submit" class="input-custom-css px-3 py-2 px-3 py-2 mt-3" name="ruaj">
            <i class="fi fi-rr-paper-plane"></i> Ruaj
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include 'partials/footer.php'; ?>
<!-- Toast Container -->
<div aria-live="polite" aria-atomic="true" class="position-fixed top-0 end-0 p-3" style="z-index: 1055;">
  <div id="liveToast" class="toast align-items-center text-white bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
    <div class="d-flex">
      <div class="toast-body">
        <!-- Message will be injected here -->
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>
<!-- Platform Details Modal -->
<div class="modal fade" id="platformDetailsModal" tabindex="-1" aria-labelledby="platformDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="platformDetailsModalLabel">Platform Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="modalContent">
          <p>Loading...</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- JavaScript and CSS Includes -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/selectr/dist/selectr.min.css">
<script src="https://cdn.jsdelivr.net/npm/selectr/dist/selectr.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    // Paste Button Functionality
    $('#pasteButton').on('click', async function() {
      try {
        if (!navigator.clipboard) {
          showToast('error', 'Your browser does not support the Clipboard API required for this feature.');
          return;
        }
        const text = await navigator.clipboard.readText();
        if (text) {
          try {
            const url = new URL(text);
            $('#linku').val(url.href);
            $('#linku').trigger('blur');
            showToast('success', 'The link has been pasted into the input field.');
          } catch (e) {
            showToast('warning', 'The pasted text is not a valid URL. Please try again.');
          }
        } else {
          showToast('info', 'There is no text in your clipboard to paste.');
        }
      } catch (err) {
        showToast('error', 'Could not paste the link. Please ensure you have granted clipboard access and try again.');
        console.error('Error accessing clipboard:', err);
      }
    });
    // Initialize Flatpickr
    var veperPicker = flatpickr("#datepicker", {
      dateFormat: 'Y-m-d',
      maxDate: "today",
      locale: "sq"
    });
    // Initialize Selectr
    var platformatSelect = new Selectr('#platformat', {
      multiple: true,
      searchable: true,
      width: '100%'
    });
    var gjuhaSelect = new Selectr('#gjuha', {
      searchable: true,
      width: '100%'
    });
    var klientiSelect = new Selectr('#klientiSelect', {
      searchable: true,
      width: '100%'
    });
    // Handle Link Input Blur Event
    $('#linku').on('blur', function() {
      var link = $(this).val().trim();
      if (link) {
        $('#song-details').hide();
        $('#platform-results').html('<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div> Checking platforms...').show();
        $('#platform-error').hide();
        $('#platform-badges').empty();
        $.ajax({
          url: 'check_platforms.php',
          type: 'POST',
          data: {
            linku: link
          },
          dataType: 'json',
          success: function(response) {
            $('#platform-results').hide();
            $('#platform-error').hide();
            if (response.success) {
              // Populate Teksti and Orkestrimi fields
              if (response.data.teksti && response.data.teksti !== 'N/A') {
                $('#teksti').val(response.data.teksti);
              }
              if (response.data.orkestrimi && response.data.orkestrimi !== 'N/A') {
                $('#orkestra').val(response.data.orkestrimi);
              }
              // Populate Song Details
              if (response.data.title && response.data.channel) {
                $('#kengtari').val(response.data.artist);
                $('#emri').val(response.data.title);
                $('#muzika').val(response.data.title);
                $('#song-title').text(response.data.title);
                $('#song-artist').text(response.data.channel);
                $('#song-description').text(response.data.description);
                $('#channelID').val(response.data.channelID || 'N/A');
                $('#song-thumbnail').attr('src', response.data.thumbnail || 'assets/logos/default_song.png');
                $('#song-details').show();
                if (response.data.publishedDate) {
                  veperPicker.setDate(response.data.publishedDate, true, "Y-m-d");
                } else {
                  veperPicker.clear();
                }
              }
              // Populate Platforms
              if (response.data.platforms.length > 0) {
                response.data.platforms.forEach(function(platform) {
                  var badge = `
                                        <span class="badge bg-secondary me-2 mb-2 d-flex align-items-center platform-badge" 
                                              data-platform-name="${platform.name}" 
                                              data-platform-logo="${platform.logo}" 
                                              data-platform-description="${platform.description}" 
                                              data-platform-url="${platform.url}"
                                              style="cursor: pointer;">
                                              <img src="${platform.logo}" alt="${platform.name}" width="20" height="20" class="me-1">
                                              ${platform.name}
                                        </span>
                                    `;
                  $('#platform-badges').append(badge);
                });
                var selectedPlatforms = response.data.platforms.map(function(platform) {
                  return platform.name;
                });
                platformatSelect.setValue(selectedPlatforms);
                // === Set Primary Platform (platforma field) ===
                // Prioritize Spotify if available
                if (selectedPlatforms.includes('Spotify')) {
                  $('#platforma').val('Spotify');
                } else if (selectedPlatforms.length > 0) {
                  $('#platforma').val(selectedPlatforms[0]);
                } else {
                  $('#platforma').val('YouTube');
                }
                // === Automatically Populate Spotify Link ===
                var spotifyPlatform = response.data.platforms.find(function(platform) {
                  return platform.name.toLowerCase() === 'spotify';
                });
                if (spotifyPlatform) {
                  $('#linkuplat').val(spotifyPlatform.url);
                  showToast('success', 'The Spotify link has been automatically populated.');
                } else {
                  $('#linkuplat').val('');
                }
              } else {
                $('#platform-results').html('No platforms found for this song.').show();
                $('#platforma').val('YouTube');
                $('#linkuplat').val('');
              }
              // Populate Klienti if available
              if (response.data.client && response.data.client.id) {
                klientiSelect.setValue(response.data.client.id);
              }
              // === Automatic Radio Button Selection ===
              var description = response.data.description ? response.data.description.toLowerCase() : '';
              var artist = response.data.artist ? response.data.artist.toLowerCase() : '';
              var songName = response.data.title ? response.data.title.toLowerCase() : '';
              var coverPatterns = [
                /\bcover\b/,
                /\(cover\)/,
                /\(c\/o\)/,
                /\[cover\]/,
                /cover\s*[\(\[]?[^\)\]]*[\)\]]?/
              ];
              var origjinalePatterns = [
                /\borigjinale\b/,
                /\(origjinale\)/,
                /\[origjinale\]/,
                /origjinale\s*[\(\[]?[^\)\]]*[\)\]]?/
              ];
              function matchesAny(patterns, text) {
                return patterns.some(function(pattern) {
                  return pattern.test(text);
                });
              }
              if (matchesAny(coverPatterns, songName) || matchesAny(coverPatterns, description) || matchesAny(coverPatterns, artist)) {
                $('input[name="cover"][value="Cover"]').prop('checked', true);
              } else if (matchesAny(origjinalePatterns, songName) || matchesAny(origjinalePatterns, description) || matchesAny(origjinalePatterns, artist)) {
                $('input[name="cover"][value="Origjinale"]').prop('checked', true);
              } else {
                $('input[name="cover"][value="Potpuri"]').prop('checked', true);
              }
              // === Automatically Check Social Platforms ===
              if (response.data.description) {
                var description = response.data.description.toLowerCase();
                var facebookPatterns = [
                  /facebook/i,
                  /facebook\.com/i,
                  /\bfb\b/i,
                  /facebook\s*►\s*https?:\/\/(?:www\.)?facebook\.com\/\w+/i,
                  /facebook\s*►\s*https?:\/\/(?:www\.)?smarturl\.it\/\w*fb\w*/i,
                  /fb\s*►\s*https?:\/\/(?:www\.)?smarturl\.it\/\w*fb\w*/i
                ];
                var instagramPatterns = [
                  /instagram/i,
                  /instagram\.com/i,
                  /\big\b/i,
                  /instagram\s*►\s*https?:\/\/(?:www\.)?instagram\.com\/\w+/i,
                  /instagram\s*►\s*https?:\/\/(?:www\.)?smarturl\.it\/\w*ig\w*/i,
                  /ig\s*►\s*https?:\/\/(?:www\.)?smarturl\.it\/\w*ig\w*/i
                ];
                if (matchesAny(facebookPatterns, description)) {
                  $('#facebook').prop('checked', true);
                } else {
                  $('#facebook').prop('checked', false);
                }
                if (matchesAny(instagramPatterns, description)) {
                  $('#instagram').prop('checked', true);
                } else {
                  $('#instagram').prop('checked', false);
                }
              }
            } else {
              $('#platform-error').text(response.message).show();
            }
          },
          error: function() {
            $('#platform-results').hide();
            $('#platform-error').text('An error occurred while checking platforms.').show();
          }
        });
      }
    });
    // Handle Platform Badge Clicks
    $(document).on('click', '.platform-badge', function() {
      var platformName = $(this).data('platform-name');
      var platformLogo = $(this).data('platform-logo');
      var platformDescription = $(this).data('platform-description');
      var platformUrl = $(this).data('platform-url');
      var modalContent = `
                <div class="d-flex align-items-center mb-3">
                    <img src="${platformLogo}" alt="${platformName}" width="50" height="50" class="me-3">
                    <h5>${platformName}</h5>
                </div>
                <p>${platformDescription}</p>
                ${platformUrl !== '#' ? `<a href="${platformUrl}" target="_blank" class="btn btn-primary">Listen on ${platformName}</a>` : '<p>No direct link available.</p>'}
            `;
      $('#platformDetailsModalLabel').text(`${platformName} Details`);
      $('#modalContent').html(modalContent);
      var platformModal = new bootstrap.Modal(document.getElementById('platformDetailsModal'));
      platformModal.show();
    });
  });
  // Function to show Bootstrap Toasts
  function showToast(type, message) {
    var toastEl = $('#liveToast');
    var toastBody = toastEl.find('.toast-body');
    // Set toast background based on type
    var bgClass = 'bg-primary';
    if (type === 'success') {
      bgClass = 'bg-success';
    } else if (type === 'error') {
      bgClass = 'bg-danger';
    } else if (type === 'warning') {
      bgClass = 'bg-warning text-dark';
    } else if (type === 'info') {
      bgClass = 'bg-info';
    }
    toastEl.removeClass('bg-primary bg-success bg-danger bg-warning bg-info');
    toastEl.addClass(bgClass);
    // Set message
    toastBody.text(message);
    // Show toast
    var toast = new bootstrap.Toast(toastEl[0]);
    toast.show();
  }
  // Check for session messages and display toast
  <?php if (isset($_SESSION['message'])): ?>
    document.addEventListener('DOMContentLoaded', function() {
      showToast('<?php echo $_SESSION['message']['type']; ?>', '<?php echo addslashes($_SESSION['message']['text']); ?>');
    });
    <?php unset($_SESSION['message']); ?>
  <?php endif; ?>
</script>
<!-- Custom Styles -->
<style>
  .form-label {
    font-weight: 600;
    font-size: 0.9rem;
  }
  .badge {
    font-size: 0.8rem;
    padding: 0.3em 0.5em;
    border-radius: 10px;
  }
  #song-thumbnail {
    object-fit: cover;
    height: 100%;
  }
  @media (max-width: 767.98px) {
    #song-details .row.g-0 {
      flex-direction: column;
    }
    #song-details .col-md-4,
    #song-details .col-md-8 {
      width: 100%;
    }
  }
  .platform-badge {
    cursor: pointer;
    transition: background-color 0.3s;
  }
  .platform-badge:hover {
    background-color: #5a6268;
  }
  #song-description {
    font-size: 0.9rem;
    color: #6c757d;
  }
  /* Reduce padding and margin for compact UI */
  .form-control,
  .form-select {
    padding: 0.4rem 0.6rem;
  }
  .btn {
    padding: 0.4rem 0.8rem;
    font-size: 0.9rem;
  }
  .input-group .btn {
    padding: 0.4rem 0.8rem;
  }
  .breadcrumb-item+.breadcrumb-item::before {
    content: '>';
  }
</style>