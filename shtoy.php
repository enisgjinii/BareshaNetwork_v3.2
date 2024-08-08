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
function validateCsrfToken()
{
  if (!isset($_SESSION['csrf_token'])) {
    return false;
  }
  return true;
}
// Check if form is submitted and CSRF token is valid
if (isset($_POST['ruaj']) && validateCsrfToken()) {
  // Assuming $conn is your database connection
  require_once "conn-d.php";
  // Your database interaction logic
  // Define your insert query and data separately
  $insertQuery = "INSERT INTO ngarkimi (kengetari, emri, teksti, muzika, orkestra, co, facebook, instagram, veper, klienti, platforma, platformat, linku, data, gjuha, infosh, nga, linkuplat) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
  $data = [
    "ssssssssssssssssss",
    $_POST['kengtari'],
    $_POST['emri'],
    $_POST['teksti'],
    $_POST['muzika'],
    $_POST['orkestra'],
    ($_POST['cover'] === "Cover" || $_POST['cover'] === "Origjinale") ? $_POST['cover'] : "Potpuri",
    ($_POST['facebook'] === "PO") ? "PO" : "Jo",
    ($_POST['Instagram'] === "PO") ? "PO" : "Jo",
    $_POST['veper'],
    $_POST['klienti'],
    $_POST['platforma'],
    implode(', ', $_POST['platformat']),
    $_POST['linku'],
    $_POST['data'],
    $_POST['gjuha'],
    $_POST['infosh'],
    $user_info['givenName'] . ' ' . $user_info['familyName'],
    $_POST['linkuplat']
  ];
  // Prepare and execute insertion query
  $stmt = $conn->prepare($insertQuery);
  if ($stmt) {
    $stmt->bind_param(...$data);
    $insertSuccess = $stmt->execute();
    $stmt->close();
  } else {
    $insertSuccess = false;
  }
  // Handle insertion result
  if ($insertSuccess) {
    // Log successful insertion
    $log_description = $data[17] . " ka ngarkuar " . $data[1] . " në sistem";
    $date_information = date('Y-m-d H:i:s');
    $logQuery = "INSERT INTO logs (stafi, ndryshimi, koha) VALUES (?, ?, ?)";
    $logData = ["sss", $data[17], $log_description, $date_information];
    $stmt = $conn->prepare($logQuery);
    if ($stmt) {
      $stmt->bind_param(...$logData);
      $logSuccess = $stmt->execute();
      $stmt->close();
    } else {
      $logSuccess = false;
    }
    // Show success message
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
    // Show error message
    echo '<script>
            Swal.fire({
              icon: "error",
              title: "Error!",
              text: "There was an error while saving your data.",
              confirmButtonText: "OK"
            });
        </script>';
  }
} elseif (isset($_POST['ruaj'])) {
  // If CSRF token is missing or invalid, show error message
  echo '<script>
        Swal.fire({
          icon: "error",
          title: "Error!",
          text: "Invalid CSRF token. Please try again.",
          confirmButtonText: "OK"
        });
    </script>';
}
// Generate CSRF token
$csrf_token = generateCsrfToken();
?>
<!-- Begin Page Content -->
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <nav class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Videot / Ngarkimi</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">
            <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">
              Regjistro një këngë
              <?php echo $filename ?>
            </a>
          </li>
      </nav>
      <div class="card rounded-5 bordered p-5">
        <form method="POST" action="" enctype="multipart/form-data">
          <div class="form-group row">
            <div class="col">
              <label for="emri" class="form-label">K&euml;ng&euml;tari</label>
              <input type="text" name="kengtari" id="term" class="form-control border border-2 rounded-5" placeholder="Shëno emrin e këngëtarit" required>
              <script type="text/javascript">
                $("#term").autocomplete({
                  source: 'ajax-db-search.php',
                });
              </script>
            </div>
            <div class="col">
              <label for="emri" class="form-label">Emri i k&euml;nges</label>
              <input type="text" name="emri" class="form-control border border-2 rounded-5" placeholder="Shëno emrin e këngës" autocomplete="off" required>
            </div>
          </div>
          <div class="form-group row">
            <div class="col">
              <label for="dk" class="form-label">Tekst Shkrues</label>
              <input type="text" name="teksti" class="form-control border border-2 rounded-5" placeholder="Shëno emrin e tekst shkruesit" autocomplete="off">
            </div>
            <div class="col">
              <label for="dks" class="form-label">Muzika</label>
              <input type="text" name="muzika" class="form-control border border-2 rounded-5" placeholder="Shëno muzikën" autocomplete="off">
            </div>
          </div>
          <div class="form-group row">
            <div class="col">
              <label for="tel" class="form-label">Orkestra</label>
              <input type="text" name="orkestra" class="form-control border border-2 rounded-5" placeholder="Shëno orkestrën" autocomplete="off">
            </div>
            <div class="col">
              <label for="tel" class="form-label">C / O</label>
              <input type="text" name="co" class="form-control border border-2 rounded-5" placeholder="Co" autocomplete="off">
            </div>
          </div>
          <hr />
          <div class="form-group row">
            <div class="col text-dark">
              <label for="tel" class="form-label">Cover / Origjinale </label><br>
              <input type="radio" id="html" name="cover" value="Cover" class="form-check-input">
              <label for="html">Cover</label>
              <input type="radio" id="css" name="cover" value="Origjinale" class="form-check-input">
              <label for="css">Origjinale</label>
              <input type="radio" id="css" name="cover" value="Potpuri" class="form-check-input">
              <label for="css">Potpuri</label><br>
            </div>
            <div class="col text-dark">
              <label for="tel" class="form-label">Platformat sociale </label><br>
              <input type="checkbox" id="facebook" name="facebook" value="Po" class="form-check-input">
              <label for="Facebook">Facebook</label><br>
              <input type="checkbox" name="Instagram" value="Po" class="form-check-input">
              <label for="Instagram"> Instagram</label><br>
            </div>
          </div>
          <hr />
          <div class="form-group row">
            <div class="col">
              <label for="yt" class="form-label">Veper Nga Koha</label>
              <input type="text" name="veper" id="datepicker" class="form-control border border-2 rounded-5 w-100" placeholder="Kliko mbi input dhe zgjedh kohen" value="" autocomplete="off">
              <script>
                flatpickr("#datepicker", {
                  dateFormat: 'Y-m-d',
                  maxDate: new Date().toISOString().split("T")[0], // Set max date to today
                  "locale": "sq" // locale for this instance only
                });
              </script>
            </div>
            <div class="col">
              <label for="imei" class="form-label">Klienti </label>
              <select class="form-select shadow-sm rounded-5" id="klientiSelect" name="klienti" required>
                <?php
                $mads = $conn->query("SELECT * FROM klientet");
                while ($ads = mysqli_fetch_array($mads)) {
                ?>
                  <option value="<?php echo $ads['id']; ?>"><?php echo $ads['emri']; ?></option>
                <?php } ?>
              </select>
              <script>
                new Selectr('#klientiSelect', {
                  searchable: true,
                  width: 300
                });
              </script>
            </div>
            <div class="form-group row">
            </div>
            <div class="col">
              <label for="platforma" class="form-label">Platforma</label>
              <input type="text" class="form-control border border-2 rounded-5" name="platforma" value="YouTube">
            </div>
            <div class="col">
              <label for="platforms" class="form-label">Platformat tjera për publikimin e këngës <br><small>(Mbaj shtypur CTRL për të zgjedhur disa opsione)</small> </label>
              <select multiple class="form-select shadow-sm rounded-5" name="platformat[]" id="exampleFormControlSelect2" style="height:fit-content">
                <option value="Spotify" selected="selected">Spotify</option>
                <option value="YouTube Music" selected="selected">YouTube Music</option>
                <option value="iTunes" selected="selected">iTunes</option>
                <option value="Apple Music" selected="selected">Apple Music</option>
                <option value="TikTok" selected="selected">TikTok</option>
                <option value="Instagram Stories" selected="selected">Instagram Stories</option>
                <option value="Tidal" selected="selected">Tidal</option>
                <option value="Amazon Music" selected="selected">Amazon Music</option>
                <option value="Pandora" selected="selected">Pandora</option>
                <option value="AudioMack" selected="selected">AudioMack</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <div class="col">
              <label for="info" class="form-label">Linku i këngës (nëse aplikohet)</label>
              <input type="url" name="linku" class="form-control border border-2 rounded-5" placeholder="Vendosni linkun e këngës" autocomplete="off">
            </div>
            <div class="col">
              <label for="info" class="form-label">Linku për platformat (nëse aplikohet)</label><br>
              <input type="url" name="linkuplat" class="form-control border border-2 rounded-5" placeholder="Vendosni linkun për platformat" autocomplete="off">
            </div>
          </div>
          <div class="form-group row">
            <div class="col">
              <label for="imei" class="form-label">Data</label>
              <input type="text" name="data" id="dataChoice" class="form-control border border-2 rounded-5 w-100" value="<?php echo date("Y-m-d"); ?>">
              <script>
                flatpickr("#dataChoice", {
                  dateFormat: 'Y-m-d',
                  maxDate: new Date().toISOString().split("T")[0], // Set max date to today
                  "locale": "sq" // locale for this instance only
                });
              </script>
            </div>
            <div class="col">
              <label for="imei" class="form-label">Gjuha</label>
              <select name="gjuha" id="gjuha" class="form-select shadow-sm rounded-5">
                <option value="Shqip" selected="">Shqip (E parazgjedhur)</option>
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
          <div class="col">
            <label for="simpleMde" class="form-label">Informacion shtesë (përdorni këtë hapësirë për të dhënë detaje shtesë)</label>
            <textarea id="simpleMde" name="infosh" placeholder="Shkruani informacionin shtesë këtu..." class="form-control border border-2 rounded-5"></textarea>
          </div>
          <button type="submit" class="input-custom-css px-3 py-2 mt-3" name="ruaj"><i class="fi fi-rr-paper-plane"></i> Ruaj</button>
      </div>
      </form>
    </div>
  </div>
  <?php include 'partials/footer.php'; ?>