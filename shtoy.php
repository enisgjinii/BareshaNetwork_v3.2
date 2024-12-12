<?php
ob_start();
session_start();
date_default_timezone_set('Europe/Tirane');
include 'partials/header.php';

function logError($message)
{
  error_log("[" . date('Y-m-d H:i:s') . "] " . $message . PHP_EOL, 3, 'error_log.txt');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ruaj'])) {
  try {
    require_once "conn-d.php";

    $user_info = [
      'givenName' => 'John',
      'familyName' => 'Doe'
    ];
    $user_full_name = isset($user_info['givenName'], $user_info['familyName'])
      ? $user_info['givenName'] . ' ' . $user_info['familyName']
      : "Unknown User";

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

    $_SESSION['message'] = ['type' => 'success', 'text' => 'Të dhënat janë ruajtur me sukses.'];
  } catch (Exception $e) {
    logError($e->getMessage());
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Pati një gabim: ' . $e->getMessage()];
  }
  header('Location: ' . $_SERVER['PHP_SELF']);
  exit;
}
?>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">

      <nav class="bg-white px-2 py-2 mb-3 rounded-5" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a class="text-reset" href="#" style="text-decoration: none;">Videot & Ngarkimi</a></li>
          <li class="breadcrumb-item active" aria-current="page">
            <a href="<?php echo htmlspecialchars(__FILE__); ?>" class="text-reset" style="text-decoration: none;">
              Regjistro një këngë
            </a>
          </li>
        </ol>
      </nav>

      <div class="card rounded-5 shadow-sm p-3">
        <form method="POST" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
          <h6 class="mb-3"><span class="badge bg-info me-2"><i class="bi bi-music-note-beamed"></i></span>Informacion Kryesor</h6>
          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <div class="form-floating position-relative" data-bs-toggle="tooltip" title="Shëno emrin e plotë të këngëtarit">
                <input type="text" name="kengtari" id="kengtari" class="form-control rounded-5" placeholder="Emri i këngëtarit">
                <label for="kengtari"><i class="bi bi-person-fill position-absolute ms-2"></i> Këngëtari</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating position-relative" data-bs-toggle="tooltip" title="Shëno emrin e plotë të këngës">
                <input type="text" name="emri" id="emri" class="form-control rounded-5" placeholder="Emri i këngës" autocomplete="off">
                <label for="emri"><i class="bi bi-music-note-list position-absolute ms-2"></i> Emri i Këngës</label>
              </div>
            </div>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <div class="form-floating position-relative" data-bs-toggle="tooltip" title="Emri i tekst-shkruesit">
                <input type="text" name="teksti" id="teksti" class="form-control rounded-5" placeholder="Tekst Shkrues" autocomplete="off">
                <label for="teksti"><i class="bi bi-pencil-fill position-absolute ms-2"></i> Tekst Shkrues</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating position-relative" data-bs-toggle="tooltip" title="Emri i kompozitorit (muzika)">
                <input type="text" name="muzika" id="muzika" class="form-control rounded-5" placeholder="Muzika" autocomplete="off">
                <label for="muzika"><i class="bi bi-music-note position-absolute ms-2"></i> Muzika</label>
              </div>
            </div>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <div class="form-floating position-relative" data-bs-toggle="tooltip" title="Emri i orkestrës">
                <input type="text" name="orkestra" id="orkestra" class="form-control rounded-5" placeholder="Orkestra" autocomplete="off">
                <label for="orkestra"><i class="bi bi-broadcast-pin position-absolute ms-2"></i> Orkestra</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating position-relative" data-bs-toggle="tooltip" title="Vendosni C/O nëse ka">
                <input type="text" name="co" id="co" class="form-control rounded-5" placeholder="C/O" autocomplete="off">
                <label for="co"><i class="bi bi-arrow-right-square position-absolute ms-2"></i> C/O</label>
              </div>
            </div>
          </div>

          <hr class="my-3">

          <h6 class="mb-3"><span class="badge bg-primary me-2"><i class="bi bi-card-checklist"></i></span>Kategoria & Publikimi</h6>
          <div class="row g-2 mb-3">
            <div class="col-md-6" data-bs-toggle="tooltip" title="Zgjidhni nëse kënga është Cover, Origjinale apo Potpuri">
              <span class="d-block mb-1"><i class="bi bi-collection-play me-1"></i>Cover / Origjinale</span>
              <div class="form-check form-check-inline">
                <input type="radio" id="cover" name="cover" value="Cover" class="form-check-input">
                <label for="cover" class="form-check-label"><span class="badge bg-secondary">Cover</span></label>
              </div>
              <div class="form-check form-check-inline">
                <input type="radio" id="origjinale" name="cover" value="Origjinale" class="form-check-input">
                <label for="origjinale" class="form-check-label"><span class="badge bg-success">Origjinale</span></label>
              </div>
              <div class="form-check form-check-inline">
                <input type="radio" id="potpuri" name="cover" value="Potpuri" class="form-check-input" checked>
                <label for="potpuri" class="form-check-label"><span class="badge bg-warning text-dark">Potpuri</span></label>
              </div>
            </div>

            <div class="col-md-6" data-bs-toggle="tooltip" title="Platformat sociale ku do të ndahet kënga">
              <span class="d-block mb-1"><i class="bi bi-share me-1"></i>Platformat Sociale</span>
              <div class="form-check form-check-inline">
                <input type="checkbox" id="facebook" name="facebook" value="Po" class="form-check-input">
                <label for="facebook" class="form-check-label"><i class="bi bi-facebook me-1"></i>Facebook</label>
              </div>
              <div class="form-check form-check-inline">
                <input type="checkbox" id="instagram" name="Instagram" value="Po" class="form-check-input">
                <label for="instagram" class="form-check-label"><i class="bi bi-instagram me-1"></i>Instagram</label>
              </div>
            </div>
          </div>

          <hr class="my-3">

          <h6 class="mb-3"><span class="badge bg-danger me-2"><i class="bi bi-calendar3"></i></span>Detaje Publikimi</h6>
          <div class="row g-2 mb-3">
            <div class="col-md-3" data-bs-toggle="tooltip" title="Data e realizimit të këngës">
              <div class="form-floating position-relative">
                <input type="text" name="veper" id="datepicker" class="form-control rounded-5" placeholder="Koha e realizimit" autocomplete="off">
                <label for="datepicker"><i class="bi bi-calendar-event position-absolute ms-2"></i> Vepër Nga Koha</label>
              </div>
            </div>
            <div class="col-md-3" data-bs-toggle="tooltip" title="Zgjidhni klientin">
              <div class="form-floating">
                <select class="form-select rounded-5" id="klientiSelect" name="klienti">
                  <option value="" disabled selected>-- Klient --</option>
                  <?php
                  $clients = $conn->query("SELECT id, emri, youtube FROM klientet");
                  if ($clients) {
                    while ($client = mysqli_fetch_array($clients)) {
                      echo '<option value="' . htmlspecialchars($client['id']) . '">' . htmlspecialchars($client['emri']) . ' | ' . htmlspecialchars($client['youtube']) . '</option>';
                    }
                  } else {
                    echo '<option value="" disabled>Gabim gjatë marrjes së klientëve.</option>';
                  }
                  ?>
                </select>
                <label for="klientiSelect"><i class="bi bi-people-fill position-absolute ms-2"></i> Klienti</label>
              </div>
            </div>
            <div class="col-md-3" data-bs-toggle="tooltip" title="Platforma kryesore e publikimit">
              <div class="form-floating position-relative">
                <input type="text" class="form-control rounded-5" name="platforma" id="platforma" value="YouTube" readonly>
                <label for="platforma"><i class="bi bi-play-btn-fill position-absolute ms-2"></i> Platforma Kryesore</label>
              </div>
            </div>
            <div class="col-md-3" data-bs-toggle="tooltip" title="Channel ID i publikimit">
              <div class="form-floating position-relative">
                <input type="text" id="channelID" name="channelID" class="form-control rounded-5" readonly>
                <label for="channelID"><i class="bi bi-kanban position-absolute ms-2"></i> Channel ID</label>
              </div>
            </div>
          </div>

          <div class="mb-3" data-bs-toggle="tooltip" title="Zgjidhni platformat tjera për publikimin">
            <span class="d-block mb-2"><i class="bi bi-cloud-arrow-up-fill me-1"></i>Platformat Tjera</span>
            <select multiple class="form-select rounded-5" name="platformat[]" id="platformat">
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

          <h6 class="mb-3"><span class="badge bg-success me-2"><i class="bi bi-link-45deg"></i></span>Linkjet e Këngës</h6>
          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <div class="form-floating position-relative" data-bs-toggle="tooltip" title="Vendosni linkun e këngës">
                <input type="url" name="linku" id="linku" class="form-control rounded-5" placeholder="Linku i këngës">
                <label for="linku"><i class="bi bi-link-45deg position-absolute ms-2"></i> Linku i Këngës</label>
              </div>
              <button class="btn btn-outline-secondary mt-2 rounded-5" type="button" id="pasteButton" data-bs-toggle="tooltip" title="Paste link nga clipboard">
                <i class="bi bi-clipboard"></i> Paste
              </button>
            </div>
            <div class="col-md-6">
              <div class="form-floating position-relative" data-bs-toggle="tooltip" title="Vendosni linkun për platformat tjera">
                <input type="url" name="linkuplat" id="linkuplat" class="form-control rounded-5" placeholder="Linku për platformat">
                <label for="linkuplat"><i class="bi bi-link-45deg position-absolute ms-2"></i> Linku për Platformat</label>
              </div>
            </div>
          </div>

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

          <hr class="my-3">
          <h6 class="mb-3"><span class="badge bg-warning text-dark me-2"><i class="bi bi-card-text"></i></span>Informacion Shtesë</h6>
          <div class="row g-2 mb-3">
            <div class="col-md-6" data-bs-toggle="tooltip" title="Data e regjistrimit automatikisht">
              <div class="form-floating position-relative">
                <input type="text" name="data" id="dataChoice" class="form-control rounded-5" value="<?php echo date("Y-m-d"); ?>" readonly>
                <label for="dataChoice"><i class="bi bi-clock-fill position-absolute ms-2"></i> Data e Regjistrimit</label>
              </div>
            </div>
            <div class="col-md-6" data-bs-toggle="tooltip" title="Zgjidhni gjuhën e këngës">
              <div class="form-floating">
                <select name="gjuha" id="gjuha" class="form-select rounded-5">
                  <option value="Shqip" selected>Shqip</option>
                  <option value="English">English</option>
                  <option value="German">German</option>
                </select>
                <label for="gjuha"><i class="bi bi-globe position-absolute ms-2"></i> Gjuha</label>
              </div>
            </div>
          </div>

          <div class="form-floating mb-3" data-bs-toggle="tooltip" title="Shkruani informacione shtesë">
            <textarea id="simpleMde" name="infosh" class="form-control rounded-5" placeholder="Informacion shtesë..." style="height: 120px;"></textarea>
            <label for="simpleMde"><i class="bi bi-info-circle position-absolute ms-2"></i> Informacion Shtesë</label>
          </div>

          <button type="submit" class="btn btn-primary px-3 py-2 mt-3 rounded-5" name="ruaj">
            <i class="bi bi-send-fill me-1"></i>Ruaj
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include 'partials/footer.php'; ?>

<div aria-live="polite" aria-atomic="true" class="position-fixed top-0 end-0 p-3" style="z-index:1055;">
  <div id="liveToast" class="toast align-items-center text-white bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
    <div class="d-flex">
      <div class="toast-body"></div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<div class="modal fade" id="platformDetailsModal" tabindex="-1" aria-labelledby="platformDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content rounded-5">
      <div class="modal-header">
        <h5 class="modal-title" id="platformDetailsModalLabel">Platform Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Mbyll"></button>
      </div>
      <div class="modal-body">
        <div id="modalContent">
          <p>Loading...</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary rounded-5" data-bs-dismiss="modal">Mbyll</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/selectr/dist/selectr.min.css">
<script src="https://cdn.jsdelivr.net/npm/selectr/dist/selectr.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(el) {
      return new bootstrap.Tooltip(el);
    });

    $('#pasteButton').on('click', async function() {
      try {
        if (!navigator.clipboard) {
          showToast('error', 'Shfletuesi juaj nuk mbështet Clipboard API.');
          return;
        }
        const text = await navigator.clipboard.readText();
        if (text) {
          try {
            const url = new URL(text);
            $('#linku').val(url.href).trigger('blur');
            showToast('success', 'Linku u vendos me sukses.');
          } catch {
            showToast('warning', 'Teksti i kopjuar nuk është URL valide.');
          }
        } else {
          showToast('info', 'Asnjë tekst në clipboard.');
        }
      } catch (err) {
        showToast('error', 'Nuk mund të ngjisni linkun. Lejimi i clipboard mungon.');
        console.error(err);
      }
    });

    var veperPicker = flatpickr("#datepicker", {
      dateFormat: 'Y-m-d',
      maxDate: "today",
      locale: "sq"
    });
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

    $('#linku').on('blur', function() {
      var link = $(this).val().trim();
      if (link) {
        $('#song-details').hide();
        $('#platform-results').html('<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div> Kontrol po bëhet...').show();
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
              if (response.data.teksti && response.data.teksti !== 'N/A') $('#teksti').val(response.data.teksti);
              if (response.data.orkestrimi && response.data.orkestrimi !== 'N/A') $('#orkestra').val(response.data.orkestrimi);

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

              if (response.data.platforms.length > 0) {
                response.data.platforms.forEach(function(platform) {
                  var badge = `
                  <span class="badge bg-secondary me-2 mb-2 d-flex align-items-center platform-badge"
                    data-platform-name="${platform.name}" 
                    data-platform-logo="${platform.logo}" 
                    data-platform-description="${platform.description}" 
                    data-platform-url="${platform.url}"
                    style="cursor:pointer;">
                    <img src="${platform.logo}" alt="${platform.name}" width="20" height="20" class="me-1">
                    ${platform.name}
                  </span>`;
                  $('#platform-badges').append(badge);
                });
                var selectedPlatforms = response.data.platforms.map(pl => pl.name);
                platformatSelect.setValue(selectedPlatforms);

                if (selectedPlatforms.includes('Spotify')) {
                  $('#platforma').val('Spotify');
                } else if (selectedPlatforms.length > 0) {
                  $('#platforma').val(selectedPlatforms[0]);
                } else {
                  $('#platforma').val('YouTube');
                }

                var spotifyPlatform = response.data.platforms.find(pl => pl.name.toLowerCase() === 'spotify');
                if (spotifyPlatform) {
                  $('#linkuplat').val(spotifyPlatform.url);
                  showToast('success', 'Linku i Spotify u vendos automatikisht.');
                } else {
                  $('#linkuplat').val('');
                }
              } else {
                $('#platform-results').text('Asnjë platformë e gjetur.').show();
                $('#platforma').val('YouTube');
                $('#linkuplat').val('');
              }

              if (response.data.client && response.data.client.id) {
                klientiSelect.setValue(response.data.client.id);
              }

              var description = response.data.description ? response.data.description.toLowerCase() : '';
              var artist = response.data.artist ? response.data.artist.toLowerCase() : '';
              var songName = response.data.title ? response.data.title.toLowerCase() : '';

              var coverPatterns = [/\bcover\b/, /\(cover\)/, /\(c\/o\)/, /\[cover\]/, /cover\s*[\(\[]?[^\)\]]*[\)\]]?/];
              var origjinalePatterns = [/\borigjinale\b/, /\(origjinale\)/, /\[origjinale\]/, /origjinale\s*[\(\[]?[^\)\]]*[\)\]]?/];

              function matchesAny(patterns, text) {
                return patterns.some(pattern => pattern.test(text));
              }

              if (matchesAny(coverPatterns, songName) || matchesAny(coverPatterns, description) || matchesAny(coverPatterns, artist)) {
                $('input[name="cover"][value="Cover"]').prop('checked', true);
              } else if (matchesAny(origjinalePatterns, songName) || matchesAny(origjinalePatterns, description) || matchesAny(origjinalePatterns, artist)) {
                $('input[name="cover"][value="Origjinale"]').prop('checked', true);
              } else {
                $('input[name="cover"][value="Potpuri"]').prop('checked', true);
              }

              if (response.data.description) {
                var desc = response.data.description.toLowerCase();
                var facebookPatterns = [/facebook/i, /facebook\.com/i, /\bfb\b/i];
                var instagramPatterns = [/instagram/i, /instagram\.com/i, /\big\b/i];

                if (matchesAny(facebookPatterns, desc)) $('#facebook').prop('checked', true);
                if (matchesAny(instagramPatterns, desc)) $('#instagram').prop('checked', true);
              }
            } else {
              $('#platform-error').text(response.message).show();
            }
          },
          error: function() {
            $('#platform-results').hide();
            $('#platform-error').text('Ndodhi një gabim gjatë kontrollit të platformave.').show();
          }
        });
      }
    });

    $(document).on('click', '.platform-badge', function() {
      var platformName = $(this).data('platform-name');
      var platformLogo = $(this).data('platform-logo');
      var platformDescription = $(this).data('platform-description');
      var platformUrl = $(this).data('platform-url');
      var modalContent = `
      <div class="d-flex align-items-center mb-3">
        <img src="${platformLogo}" alt="${platformName}" width="50" height="50" class="me-3">
        <h5 class="mb-0">${platformName}</h5>
      </div>
      <p>${platformDescription}</p>
      ${platformUrl!=='#'?`<a href="${platformUrl}" target="_blank" class="btn btn-primary rounded-5">Dëgjo në ${platformName}</a>`:'<p>S\'ka link.</p>'}
    `;
      $('#platformDetailsModalLabel').text(`${platformName} Details`);
      $('#modalContent').html(modalContent);
      new bootstrap.Modal(document.getElementById('platformDetailsModal')).show();
    });
  });

  function showToast(type, message) {
    var toastEl = $('#liveToast');
    var toastBody = toastEl.find('.toast-body');
    var bgClass = 'bg-primary';
    if (type === 'success') bgClass = 'bg-success';
    else if (type === 'error') bgClass = 'bg-danger';
    else if (type === 'warning') bgClass = 'bg-warning text-dark';
    else if (type === 'info') bgClass = 'bg-info text-dark';

    toastEl.removeClass('bg-primary bg-success bg-danger bg-warning bg-info text-dark').addClass(bgClass);
    toastBody.text(message);
    new bootstrap.Toast(toastEl[0]).show();
  }

  <?php if (isset($_SESSION['message'])): ?>
    document.addEventListener('DOMContentLoaded', function() {
      showToast('<?php echo $_SESSION['message']['type']; ?>', '<?php echo addslashes($_SESSION['message']['text']); ?>');
    });
    <?php unset($_SESSION['message']); ?>
  <?php endif; ?>
</script>

<style>
  body {
    background-color: #f8f9fa;
  }

  .card {
    border: none;
    border-radius: 10px;
  }

  .form-floating>label {
    padding-left: 2.5rem;
  }

  .form-floating .form-control {
    padding-left: 2.5rem;
  }

  .form-floating .position-relative i {
    position: absolute;
    top: 50%;
    left: 0.75rem;
    transform: translateY(-50%);
    font-size: 1rem;
    color: #6c757d;
  }

  .badge {
    font-size: 0.75rem;
    padding: 0.3em 0.5em;
    border-radius: 10px;
  }

  #song-thumbnail {
    object-fit: cover;
    height: 100%;
  }

  .platform-badge:hover {
    background-color: #5a6268;
    cursor: pointer;
  }

  #song-description {
    font-size: 0.9rem;
    color: #6c757d;
  }

  .btn {
    font-size: 0.85rem;
    padding: 0.35rem 0.6rem;
  }

  .breadcrumb-item+.breadcrumb-item::before {
    content: '>';
  }
</style>  