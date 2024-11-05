<?php
session_start();
date_default_timezone_set('Europe/Tirane');
include 'partials/header.php';

// Function to generate CSRF token
function generateCsrfToken()
{
  if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf_token'];
}

// Function to validate CSRF token
function validateCsrfToken($token)
{
  return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ruaj'])) {
  // Validate CSRF token
  if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
    echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Invalid CSRF token. Please try again.",
                        confirmButtonText: "OK"
                    });
                  </script>';
  } else {
    // Include your database connection
    require_once "conn-d.php";

    // Ensure $user_info is defined. Replace this with your actual user info retrieval logic.
    if (!isset($user_info['givenName']) || !isset($user_info['familyName'])) {
      // Handle undefined user_info
      $user_full_name = "Unknown User";
    } else {
      $user_full_name = htmlspecialchars($user_info['givenName'] . ' ' . $user_info['familyName']);
    }

    // Prepare your insert query
    $insertQuery = "INSERT INTO ngarkimi (kengetari, emri, teksti, muzika, orkestra, co, facebook, instagram, veper, klienti, platforma, platformat, linku, data, gjuha, infosh, nga, linkuplat) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Collect and sanitize data
    $kengetari   = htmlspecialchars($_POST['kengtari'] ?? '');
    $emri        = htmlspecialchars($_POST['emri'] ?? '');
    $teksti      = htmlspecialchars($_POST['teksti'] ?? '');
    $muzika      = htmlspecialchars($_POST['muzika'] ?? '');
    $orkestra    = htmlspecialchars($_POST['orkestra'] ?? '');
    $co          = in_array($_POST['cover'] ?? '', ["Cover", "Origjinale"]) ? $_POST['cover'] : "Potpuri";
    $facebook    = isset($_POST['facebook']) && $_POST['facebook'] === "Po" ? "Po" : "Jo";
    $instagram   = isset($_POST['Instagram']) && $_POST['Instagram'] === "Po" ? "Po" : "Jo";
    $veper       = htmlspecialchars($_POST['veper'] ?? '');
    $klienti     = intval($_POST['klienti'] ?? 0); // Assuming klienti is an integer
    $platforma   = htmlspecialchars($_POST['platforma'] ?? 'YouTube');
    $platformat  = isset($_POST['platformat']) ? implode(', ', array_map('htmlspecialchars', $_POST['platformat'])) : '';
    $linku       = filter_var($_POST['linku'] ?? '', FILTER_VALIDATE_URL);
    $data_field  = htmlspecialchars($_POST['data'] ?? date("Y-m-d"));
    $gjuha       = htmlspecialchars($_POST['gjuha'] ?? 'Shqip');
    $infosh      = htmlspecialchars($_POST['infosh'] ?? '');
    $nga         = $user_full_name;
    $linkuplat   = filter_var($_POST['linkuplat'] ?? '', FILTER_VALIDATE_URL);

    // Prepare data array with types
    $types = "ssssssssssssssssss";
    $data  = [
      $types,
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
    ];

    // Prepare and execute the insertion query
    $stmt = $conn->prepare($insertQuery);
    if ($stmt) {
      // bind_param requires references, so we need to prepare an array of references
      $bind_names = [];
      foreach ($data as $key => $value) {
        $bind_names[$key] = &$data[$key];
      }
      call_user_func_array([$stmt, 'bind_param'], $bind_names);
      $insertSuccess = $stmt->execute();
      $stmt->close();
    } else {
      $insertSuccess = false;
    }

    // Handle insertion result
    if ($insertSuccess) {
      // Log successful insertion
      $log_description = $nga . " ka ngarkuar " . $kengetari . " në sistem";
      $date_information = date('Y-m-d H:i:s');
      $logQuery = "INSERT INTO logs (stafi, ndryshimi, koha) VALUES (?, ?, ?)";
      $logData = ["sss", $nga, $log_description, $date_information];

      $stmt = $conn->prepare($logQuery);
      if ($stmt) {
        // Prepare references for bind_param
        $log_bind_names = [];
        foreach ($logData as $key => $value) {
          $log_bind_names[$key] = &$logData[$key];
        }
        call_user_func_array([$stmt, 'bind_param'], $log_bind_names);
        $logSuccess = $stmt->execute();
        $stmt->close();
      } else {
        $logSuccess = false;
      }

      // Show success or log error message
      if ($logSuccess) {
        echo '<script>
                            Swal.fire({
                                icon: "success",
                                title: "Success!",
                                text: "Të dhënat janë ruajtur me sukses.",
                                confirmButtonText: "OK"
                            });
                          </script>';
      } else {
        echo '<script>
                            Swal.fire({
                                icon: "error",
                                title: "Error!",
                                text: "Pati një gabim gjatë ruajtjes së të dhënave të regjistrit.",
                                confirmButtonText: "OK"
                            });
                          </script>';
      }
    } else {
      // Show error message for insertion failure
      echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: "There was an error while saving your data.",
                            confirmButtonText: "OK"
                        });
                      </script>';
    }
  }
}

// Generate CSRF token for the form
$csrf_token = generateCsrfToken();
?>
<!-- Begin Page Content -->
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <nav class="bg-white px-2 rounded-5 mb-4"  aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a class="text-reset" href="#" style="text-decoration: none;">Videot & Ngarkimi</a></li>
          <li class="breadcrumb-item active" aria-current="page">
            <a href="<?php echo htmlspecialchars(__FILE__); ?>" class="text-reset" style="text-decoration: none;">
              Regjistro një këngë
            </a>
          </li>
        </ol>
      </nav>
      <div class="card rounded-5 shadow-sm p-5">
        <form method="POST" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
          <!-- CSRF Token -->
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

          <!-- Këngëtari -->
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="kengtari" class="form-label">K&euml;ng&euml;tari <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Shëno emrin e plotë të këngëtarit"></i></label>
              <input type="text" name="kengtari" id="kengtari" class="form-control border border-2 rounded-5" placeholder="Shëno emrin e këngëtarit" required data-bs-toggle="tooltip" data-bs-placement="right" title="Vendosni emrin e këngëtarit">
              <div class="invalid-feedback">
                Ju lutem, shënoni emrin e këngëtarit.
              </div>
              <script type="text/javascript">
                $("#kengtari").autocomplete({
                  source: 'api/get_methods/get_kengtari.php',
                });
              </script>
            </div>
            <div class="col-md-6">
              <label for="emri" class="form-label">Emri i këngës <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Shëno emrin e plotë të këngës"></i></label>
              <input type="text" name="emri" id="emri" class="form-control border border-2 rounded-5" placeholder="Shëno emrin e këngës" autocomplete="off" required data-bs-toggle="tooltip" data-bs-placement="right" title="Vendosni emrin e këngës">
              <div class="invalid-feedback">
                Ju lutem, shënoni emrin e këngës.
              </div>
            </div>
          </div>

          <!-- Tekst Shkrues dhe Muzika -->
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="teksti" class="form-label">Tekst Shkrues <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Shëno emrin e plotë të tekstit shkrues"></i></label>
              <input type="text" name="teksti" id="teksti" class="form-control border border-2 rounded-5" placeholder="Shëno emrin e tekst shkruesit" autocomplete="off" data-bs-toggle="tooltip" data-bs-placement="right" title="Vendosni emrin e tekst shkruesit">
            </div>
            <div class="col-md-6">
              <label for="muzika" class="form-label">Muzika <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Shëno emrin e plotë të muzikës"></i></label>
              <input type="text" name="muzika" id="muzika" class="form-control border border-2 rounded-5" placeholder="Shëno muzikën" autocomplete="off" data-bs-toggle="tooltip" data-bs-placement="right" title="Vendosni emrin e muzikës">
            </div>
          </div>

          <!-- Orkestra dhe C/O -->
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="orkestra" class="form-label">Orkestra <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Shëno emrin e orkestrës"></i></label>
              <input type="text" name="orkestra" id="orkestra" class="form-control border border-2 rounded-5" placeholder="Shëno orkestrën" autocomplete="off" data-bs-toggle="tooltip" data-bs-placement="right" title="Vendosni emrin e orkestrës">
            </div>
            <div class="col-md-6">
              <label for="co" class="form-label">C / O <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Shëno 'C/O' nëse ka"></i></label>
              <input type="text" name="co" id="co" class="form-control border border-2 rounded-5" placeholder="Co" autocomplete="off" data-bs-toggle="tooltip" data-bs-placement="right" title="Vendosni C/O nëse ka">
            </div>
          </div>

          <hr />

          <!-- Cover / Origjinale dhe Platformat Sociale -->
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Cover / Origjinale <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni nëse kënga është Cover, Origjinale ose Potpuri"></i></label>
              <div class="form-check">
                <input type="radio" id="cover" name="cover" value="Cover" class="form-check-input" required data-bs-toggle="tooltip" data-bs-placement="right" title="Zgjidhni 'Cover' nëse është një interpretim i një kënge ekzistuese">
                <label for="cover" class="form-check-label">Cover</label>
              </div>
              <div class="form-check">
                <input type="radio" id="origjinale" name="cover" value="Origjinale" class="form-check-input" data-bs-toggle="tooltip" data-bs-placement="right" title="Zgjidhni 'Origjinale' nëse kënga është e re dhe origjinale">
                <label for="origjinale" class="form-check-label">Origjinale</label>
              </div>
              <div class="form-check">
                <input type="radio" id="potpuri" name="cover" value="Potpuri" class="form-check-input" data-bs-toggle="tooltip" data-bs-placement="right" title="Zgjidhni 'Potpuri' nëse është një version i përzgjedhur ose i ndryshuar">
                <label for="potpuri" class="form-check-label">Potpuri</label>
              </div>
              <div class="invalid-feedback">
                Ju lutem, zgjidhni një opsion.
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Platformat sociale <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni platformat sociale ku dëshironi të publikoni këngën"></i></label>
              <div class="form-check">
                <input type="checkbox" id="facebook" name="facebook" value="Po" class="form-check-input" data-bs-toggle="tooltip" data-bs-placement="right" title="Zgjidhni 'Facebook' nëse dëshironi të ndani në Facebook">
                <label for="facebook" class="form-check-label">Facebook</label>
              </div>
              <div class="form-check">
                <input type="checkbox" id="instagram" name="Instagram" value="Po" class="form-check-input" data-bs-toggle="tooltip" data-bs-placement="right" title="Zgjidhni 'Instagram' nëse dëshironi të ndani në Instagram">
                <label for="instagram" class="form-check-label">Instagram</label>
              </div>
            </div>
          </div>

          <hr />

          <!-- Veper, Klienti, Platforma dhe Platformat -->
          <div class="row mb-3">
            <div class="col-md-3">
              <label for="veper" class="form-label">Veper Nga Koha <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni datën kur kënga është realizuar"></i></label>
              <input type="text" name="veper" id="datepicker" class="form-control border border-2 rounded-5" placeholder="Kliko mbi input dhe zgjedh kohën" autocomplete="off" required data-bs-toggle="tooltip" data-bs-placement="right" title="Vendosni datën kur kënga është realizuar">
              <div class="invalid-feedback">
                Ju lutem, zgjidhni një datë valide.
              </div>
              <script>
                flatpickr("#datepicker", {
                  dateFormat: 'Y-m-d',
                  maxDate: "today",
                  locale: "sq"
                });
              </script>
            </div>
            <div class="col-md-3">
              <label for="klientiSelect" class="form-label">Klienti <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni klientin për këtë këngë"></i></label>
              <select class="form-select shadow-sm rounded-5" id="klientiSelect" name="klienti" required data-bs-toggle="tooltip" data-bs-placement="right" title="Zgjidhni klientin për këtë këngë">
                <option value="" disabled selected>-- Zgjidh Klientin --</option>
                <?php
                $mads = $conn->query("SELECT * FROM klientet");
                while ($ads = mysqli_fetch_array($mads)) {
                  echo '<option value="' . htmlspecialchars($ads['id']) . '">' . htmlspecialchars($ads['emri']) . '</option>';
                }
                ?>
              </select>
              <div class="invalid-feedback">
                Ju lutem, zgjidhni një klient.
              </div>
              <script>
                new Selectr('#klientiSelect', {
                  searchable: true,
                  width: '100%'
                });
              </script>
            </div>
            <div class="col-md-3">
              <label for="platforma" class="form-label">Platforma <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Platforma e parë për publikimin"></i></label>
              <input type="text" class="form-control border border-2 rounded-5" name="platforma" value="YouTube" readonly data-bs-toggle="tooltip" data-bs-placement="right" title="Platforma e parë për publikimin">
            </div>
            <div class="col-md-3">
              <label for="platformat" class="form-label">Platformat tjera për publikimin e këngës <br><small>(Mbaj shtypur CTRL për të zgjedhur disa opsione)</small> <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni platformat tjera për publikimin"></i></label>
              <select multiple class="form-select shadow-sm rounded-5" name="platformat[]" id="platformat" required data-bs-toggle="tooltip" data-bs-placement="right" title="Zgjidhni platformat tjera për publikimin">
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
              <div class="invalid-feedback">
                Ju lutem, zgjidhni të paktën një platformë tjetër.
              </div>
            </div>
          </div>

          <!-- Linku i këngës dhe Linku për platformat -->
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="linku" class="form-label">Linku i këngës <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Vendosni linkun e këngës nëse aplikohet"></i></label>
              <input type="url" name="linku" id="linku" class="form-control border border-2 rounded-5" placeholder="Vendosni linkun e këngës" autocomplete="off" data-bs-toggle="tooltip" data-bs-placement="right" title="Vendosni linkun e këngës nëse aplikohet">
              <div class="invalid-feedback">
                Ju lutem, vendosni një link të vlefshëm.
              </div>
            </div>
            <div class="col-md-6">
              <label for="linkuplat" class="form-label">Linku për platformat <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Vendosni linkun për platformat e tjera nëse aplikohet"></i></label>
              <input type="url" name="linkuplat" id="linkuplat" class="form-control border border-2 rounded-5" placeholder="Vendosni linkun për platformat" autocomplete="off" data-bs-toggle="tooltip" data-bs-placement="right" title="Vendosni linkun për platformat e tjera nëse aplikohet">
              <div class="invalid-feedback">
                Ju lutem, vendosni një link të vlefshëm.
              </div>
            </div>
          </div>

          <!-- Data dhe Gjuha -->
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="dataChoice" class="form-label">Data <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Data e regjistrimit të këngës"></i></label>
              <input type="text" name="data" id="dataChoice" class="form-control border border-2 rounded-5" value="<?php echo date("Y-m-d"); ?>" readonly data-bs-toggle="tooltip" data-bs-placement="right" title="Data e regjistrimit të këngës">
              <script>
                flatpickr("#dataChoice", {
                  dateFormat: 'Y-m-d',
                  maxDate: "today",
                  locale: "sq",
                  enable: false // Make it read-only
                });
              </script>
            </div>
            <div class="col-md-6">
              <label for="gjuha" class="form-label">Gjuha <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni gjuhën e këngës"></i></label>
              <select name="gjuha" id="gjuha" class="form-select shadow-sm rounded-5" required data-bs-toggle="tooltip" data-bs-placement="right" title="Zgjidhni gjuhën e këngës">
                <option value="Shqip" selected>Shqip (E parazgjedhur)</option>
                <option value="English">English</option>
                <option value="German">German</option>
              </select>
              <div class="invalid-feedback">
                Ju lutem, zgjidhni një gjuhë.
              </div>
              <script>
                new Selectr('#gjuha', {
                  searchable: true,
                  width: '100%'
                });
              </script>
            </div>
          </div>

          <!-- Informacion Shtesë -->
          <div class="row mb-4">
            <div class="col">
              <label for="simpleMde" class="form-label">Informacion shtesë <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Përdorni këtë hapësirë për të dhënë detaje shtesë"></i></label>
              <textarea id="simpleMde" name="infosh" placeholder="Shkruani informacionin shtesë këtu..." class="form-control border border-2 rounded-5" rows="4" data-bs-toggle="tooltip" data-bs-placement="right" title="Shkruani informacionin shtesë këtu..."></textarea>
            </div>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="btn btn-primary px-4 py-2 mt-3" name="ruaj">
            <i class="fi fi-rr-paper-plane"></i> Ruaj
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include 'partials/footer.php'; ?>

<!-- Initialize Bootstrap Tooltips -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Bootstrap form validation
    var forms = document.querySelectorAll('.needs-validation');

    Array.prototype.slice.call(forms)
      .forEach(function(form) {
        form.addEventListener('submit', function(event) {
          if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            Swal.fire({
              icon: 'error',
              title: 'Form Invalid',
              text: 'Ju lutem plotësoni të gjitha fushat e kërkuara si duhet.',
              confirmButtonText: 'OK'
            });
          } else {
            // Optionally, show a loading spinner
            // You can add a spinner or disable the submit button here
          }

          form.classList.add('was-validated');
        }, false);
      });
  });
</script>