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
    // For example, if using OAuth or another authentication system.
    // Here, I'm assuming $user_info is available. If not, define it accordingly.
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
      <nav class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a class="text-reset" style="text-decoration: none;">Videot / Ngarkimi</a></li>
          <li class="breadcrumb-item active" aria-current="page">
            <a href="<?php echo htmlspecialchars(__FILE__); ?>" class="text-reset" style="text-decoration: none;">
              Regjistro një këngë
            </a>
          </li>
        </ol>
      </nav>
      <div class="card rounded-5 bordered p-5">
        <form method="POST" action="" enctype="multipart/form-data">
          <!-- CSRF Token -->
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

          <!-- Këngëtari -->
          <div class="form-group row">
            <div class="col">
              <label for="kengtari" class="form-label">K&euml;ng&euml;tari</label>
              <input type="text" name="kengtari" id="term" class="form-control border border-2 rounded-5" placeholder="Shëno emrin e këngëtarit" required>
              <script type="text/javascript">
                $("#term").autocomplete({
                  source: 'api/get_methods/get_kengtari.php',
                });
              </script>
            </div>
            <div class="col">
              <label for="emri" class="form-label">Emri i k&euml;nges</label>
              <input type="text" name="emri" class="form-control border border-2 rounded-5" placeholder="Shëno emrin e këngës" autocomplete="off" required>
            </div>
          </div>

          <!-- Tekst Shkrues dhe Muzika -->
          <div class="form-group row">
            <div class="col">
              <label for="teksti" class="form-label">Tekst Shkrues</label>
              <input type="text" name="teksti" class="form-control border border-2 rounded-5" placeholder="Shëno emrin e tekst shkruesit" autocomplete="off">
            </div>
            <div class="col">
              <label for="muzika" class="form-label">Muzika</label>
              <input type="text" name="muzika" class="form-control border border-2 rounded-5" placeholder="Shëno muzikën" autocomplete="off">
            </div>
          </div>

          <!-- Orkestra dhe C/O -->
          <div class="form-group row">
            <div class="col">
              <label for="orkestra" class="form-label">Orkestra</label>
              <input type="text" name="orkestra" class="form-control border border-2 rounded-5" placeholder="Shëno orkestrën" autocomplete="off">
            </div>
            <div class="col">
              <label for="co" class="form-label">C / O</label>
              <input type="text" name="co" class="form-control border border-2 rounded-5" placeholder="Co" autocomplete="off">
            </div>
          </div>

          <hr />

          <!-- Cover / Origjinale dhe Platformat Sociale -->
          <div class="form-group row">
            <div class="col text-dark">
              <label class="form-label">Cover / Origjinale </label><br>
              <input type="radio" id="cover" name="cover" value="Cover" class="form-check-input" required>
              <label for="cover">Cover</label>
              <input type="radio" id="origjinale" name="cover" value="Origjinale" class="form-check-input">
              <label for="origjinale">Origjinale</label>
              <input type="radio" id="potpuri" name="cover" value="Potpuri" class="form-check-input">
              <label for="potpuri">Potpuri</label><br>
            </div>
            <div class="col text-dark">
              <label class="form-label">Platformat sociale </label><br>
              <input type="checkbox" id="facebook" name="facebook" value="Po" class="form-check-input">
              <label for="facebook">Facebook</label><br>
              <input type="checkbox" id="instagram" name="Instagram" value="Po" class="form-check-input">
              <label for="instagram">Instagram</label><br>
            </div>
          </div>

          <hr />

          <!-- Veper, Klienti, Platforma dhe Platformat -->
          <div class="form-group row">
            <div class="col">
              <label for="veper" class="form-label">Veper Nga Koha</label>
              <input type="text" name="veper" id="datepicker" class="form-control border border-2 rounded-5 w-100" placeholder="Kliko mbi input dhe zgjedh kohën" value="" autocomplete="off">
              <script>
                flatpickr("#datepicker", {
                  dateFormat: 'Y-m-d',
                  maxDate: new Date().toISOString().split("T")[0],
                  locale: "sq"
                });
              </script>
            </div>
            <div class="col">
              <label for="klientiSelect" class="form-label">Klienti </label>
              <select class="form-select shadow-sm rounded-5" id="klientiSelect" name="klienti" required>
                <?php
                $mads = $conn->query("SELECT * FROM klientet");
                while ($ads = mysqli_fetch_array($mads)) {
                  echo '<option value="' . htmlspecialchars($ads['id']) . '">' . htmlspecialchars($ads['emri']) . '</option>';
                }
                ?>
              </select>
              <script>
                new Selectr('#klientiSelect', {
                  searchable: true,
                  width: 300
                });
              </script>
            </div>
            <div class="col">
              <label for="platforma" class="form-label">Platforma</label>
              <input type="text" class="form-control border border-2 rounded-5" name="platforma" value="YouTube" readonly>
            </div>
            <div class="col">
              <label for="platformat" class="form-label">Platformat tjera për publikimin e këngës <br><small>(Mbaj shtypur CTRL për të zgjedhur disa opsione)</small></label>
              <select multiple class="form-select shadow-sm rounded-5" name="platformat[]" id="platformat" style="height:fit-content">
                <option value="Spotify" selected>Spotify</option>
                <option value="YouTube Music" selected>YouTube Music</option>
                <option value="iTunes" selected>iTunes</option>
                <option value="Apple Music" selected>Apple Music</option>
                <option value="TikTok" selected>TikTok</option>
                <option value="Instagram Stories" selected>Instagram Stories</option>
                <option value="Tidal" selected>Tidal</option>
                <option value="Amazon Music" selected>Amazon Music</option>
                <option value="Pandora" selected>Pandora</option>
                <option value="AudioMack" selected>AudioMack</option>
              </select>
            </div>
          </div>

          <!-- Linku i këngës dhe Linku për platformat -->
          <div class="form-group row">
            <div class="col">
              <label for="linku" class="form-label">Linku i këngës (nëse aplikohet)</label>
              <input type="url" name="linku" class="form-control border border-2 rounded-5" placeholder="Vendosni linkun e këngës" autocomplete="off">
            </div>
            <div class="col">
              <label for="linkuplat" class="form-label">Linku për platformat (nëse aplikohet)</label><br>
              <input type="url" name="linkuplat" class="form-control border border-2 rounded-5" placeholder="Vendosni linkun për platformat" autocomplete="off">
            </div>
          </div>

          <!-- Data dhe Gjuha -->
          <div class="form-group row">
            <div class="col">
              <label for="dataChoice" class="form-label">Data</label>
              <input type="text" name="data" id="dataChoice" class="form-control border border-2 rounded-5 w-100" value="<?php echo date("Y-m-d"); ?>" readonly>
              <script>
                flatpickr("#dataChoice", {
                  dateFormat: 'Y-m-d',
                  maxDate: new Date().toISOString().split("T")[0],
                  locale: "sq"
                });
              </script>
            </div>
            <div class="col">
              <label for="gjuha" class="form-label">Gjuha</label>
              <select name="gjuha" id="gjuha" class="form-select shadow-sm rounded-5">
                <option value="Shqip" selected>Shqip (E parazgjedhur)</option>
                <option value="English">English</option>
                <option value="German">German</option>
              </select>
              <script>
                new Selectr('#gjuha', {
                  searchable: true,
                  width: 300
                });
              </script>
            </div>
          </div>

          <!-- Informacion Shtesë -->
          <div class="form-group row">
            <div class="col">
              <label for="simpleMde" class="form-label">Informacion shtesë (përdorni këtë hapësirë për të dhënë detaje shtesë)</label>
              <textarea id="simpleMde" name="infosh" placeholder="Shkruani informacionin shtesë këtu..." class="form-control border border-2 rounded-5"></textarea>
            </div>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="input-custom-css px-3 py-2 mt-3" name="ruaj">
            <i class="fi fi-rr-paper-plane"></i> Ruaj
          </button>
        </form>
      </div>
    </div>
  </div>
  <?php include 'partials/footer.php'; ?>