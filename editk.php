<?php
ob_start();
include 'partials/header.php';
// Get the 'id' parameter from the URL and sanitize it
$editid = mysqli_real_escape_string($conn, $_GET['id']);
// Check if the form has been submitted
if (isset($_POST['ndrysho'])) {
  // Sanitize and retrieve data from the form
  $emri = mysqli_real_escape_string($conn, $_POST['emri']);
  $mon = empty($_POST['min']) ? "JO" : mysqli_real_escape_string($conn, $_POST['min']);
  $dk = mysqli_real_escape_string($conn, $_POST['dk']);
  $dks = mysqli_real_escape_string($conn, $_POST['dks']);
  $yt = mysqli_real_escape_string($conn, $_POST['yt']);
  $info = mysqli_real_escape_string($conn, $_POST['info']);
  $np = mysqli_real_escape_string($conn, $_POST['np']);
  $perq = mysqli_real_escape_string($conn, $_POST['perqindja']);
  $perq2 = mysqli_real_escape_string($conn, $_POST['perqindja2']);
  $adsa = mysqli_real_escape_string($conn, $_POST['ads']);
  $fb = mysqli_real_escape_string($conn, $_POST['fb']);
  $emailadd = mysqli_real_escape_string($conn, $_POST['emailadd']);
  $emailp = mysqli_real_escape_string($conn, $_POST['emailp']);
  $ig = mysqli_real_escape_string($conn, $_POST['ig']);
  $adresa = mysqli_real_escape_string($conn, $_POST['adresa']);
  $kategoria = mysqli_real_escape_string($conn, $_POST['kategoria']);
  $emriart = mysqli_real_escape_string($conn, $_POST['emriart']);
  $nrllog = mysqli_real_escape_string($conn, $_POST['nrllog']);
  $bank_info = mysqli_real_escape_string($conn, $_POST['bank_info']);
  $perdoruesi = mysqli_real_escape_string($conn, $_POST['perdoruesi']);
  $fjalekalimi = md5(mysqli_real_escape_string($conn, $_POST['fjalekalimi']));
  $emails = mysqli_real_escape_string($conn, $_POST['emails']);
  $perqindja_check = isset($_POST['perqindja_check']) ? '1' : '0';
  $perqindja_e_platformave_check = isset($_POST['perqindja_platformave_check']) ? '1' : '0';
  // Define the target folder for file uploads
  $targetfolder = "dokument/";
  // Initialize a flag for file upload success
  $ok = 1;
  // Check if a file has been uploaded
  if (isset($_FILES['tipi']) && $_FILES['tipi']['error'] == UPLOAD_ERR_OK) {
    // Set the target path for the uploaded file
    $targetfolder = $targetfolder . basename($_FILES['tipi']['name']);
    // Get the file type
    $file_type = $_FILES['tipi']['type'];
    // Check if the file type is PDF
    if ($file_type == "application/pdf") {
      // Attempt to move the uploaded file to the target folder
      if (move_uploaded_file($_FILES['tipi']['tmp_name'], $targetfolder)) {
        echo "The file " . basename($_FILES['tipi']['name']) . " has been uploaded.";
      } else {
        echo "Na vjen keq, ka pasur një gabim gjatë ngarkimit të skedarit tuaj.";
      }
    } else {
      echo "Na vjen keq, lejohen vetëm skedarët PDF.";
    }
  } else {
    // File upload error handling
    // echo "Sorry, there was an error uploading your file.";
  }
  // Sanitize and retrieve the 'nrtel' field
  $nrtel = mysqli_real_escape_string($conn, $_POST['nrtel']);
  // Update the database with the new data
  if ($conn->query("UPDATE klientet SET emri='$emri', np='$np', monetizuar='$mon', emails='$emails', dk='$dk', dks='$dks', youtube='$yt', info='$info', perqindja='$perq', perqindja2='$perq2', fb='$fb', ig='$ig', adresa='$adresa', kategoria='$kategoria', nrtel='$nrtel', emailp='$emailp', emailadd='$emailadd', emriart='$emriart', nrllog='$nrllog',  bank_name='$bank_info', ads='$adsa', perdoruesi='$perdoruesi', perqindja_check='$perqindja_check', perqindja_platformave_check='$perqindja_e_platformave_check', fjalkalimi='$fjalekalimi' WHERE id='$editid'")) {
    echo '<script>
            Swal.fire({
              icon: "success",
              title: "Kengetari u perditsua me sukses",
              showConfirmButton: false,
              timer: 1500
            });
          </script>';
    // Prepare data for insertion
    $user_informations = $user_info['givenName'] . ' ' . $user_info['familyName'];
    $log_description = $user_informations . " ka ndryshuar klientin " . $emri;
    $date_information = date('Y-m-d H:i:s');
    // Prepare the INSERT statement
    $stmt = $conn->prepare("INSERT INTO logs (stafi, ndryshimi, koha) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user_informations, $log_description, $date_information);
    if ($stmt->execute()) {
    } else {
      echo '<script>
              Swal.fire({
                icon: "error",
                title: "' . $conn->error . '",
                showConfirmButton: false,
                timer: 1500
              });
            </script>';
    }
  } else {
    echo '<script>
            Swal.fire({
              icon: "error",
              title: "' . $conn->error . '",
              showConfirmButton: false,
              timer: 1500
            });
          </script>';
  }
}
// Retrieve the data for editing
$editc = $conn->query("SELECT * FROM klientet WHERE id='$editid'");
$editcl = mysqli_fetch_array($editc);
?>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="container">
        <nav class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a class="text-reset" style="text-decoration: none;">
                Klientët
              </a>
            </li>
            <li class="breadcrumb-item">
              <a class="text-reset" style="text-decoration: none;">
                Lista e klientëve
              </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
              <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">
                Edito klientit <?php echo $editid ?>
                <?php echo $filename ?>
              </a>
            </li>
        </nav>
        <div class="mb-3">
          <a href="klient.php" class="input-custom-css px-3 py-2" style="text-decoration: none;">
            Kthehu prapa
          </a>
        </div>
        <div class="card rounded-5 bordered">
          <div class="card-body">
            <!-- Page Heading -->
            <form method="POST" action="" enctype="multipart/form-data">
              <div class="form-group row">
                <div class="col">
                  <label class="form-label" for="emri">Emri dhe mbiemri</label>
                  <input type="text" name="emri" class="form-control rounded-5 border border-2" placeholder="Shëno emrin dhe mbiemrin e klientit" value="<?php echo $editcl['emri']; ?>">
                </div>
                <div class="col">
                  <label class="form-label" for="emri">Emri artistik</label>
                  <input type="text" name="emriart" id="emriart" class="form-control rounded-5 border border-2" placeholder="Shëno emrin artistik te klientit" value="<?php echo $editcl['emriart']; ?>">
                </div>
              </div>
              <div class="form-group row">
                <div class="col">
                  <label class="form-label" for="emri">ID e Dokumentit</label>
                  <input type="text" name="np" id="emriart" class="form-control rounded-5 border border-2" placeholder="Shëno ID e dokumentit" value="<?php echo $editcl['np']; ?>">
                </div>
                <div class="col">
                  <label class="form-label" for="yt">Shkruaj ID e kanalit t&euml; YouTube</label>
                  <input type="text" name="yt" class="form-control rounded-5 border border-2" placeholder="Youtube Channel ID" value="<?php echo $editcl['youtube']; ?>">
                </div>
              </div>
              <div class="form-group row">
                <div class="col">
                  <label class="form-label" for="dk">Data e fillimit të kontrates</label>
                  <input type="date" name="dk" class="form-control rounded-5 border border-2" placeholder="Shkruaj Daten e kontrates" value="<?php echo $editcl['dk']; ?>">
                </div>
                <div class="col">
                  <label class="form-label" for="dks">Data e e skadimit të kontrates</label>
                  <input type="date" name="dks" class="form-control rounded-5 border border-2" placeholder="Shkruaj Daten e skaditimit" value="<?php echo $editcl['dks']; ?>">
                </div>
              </div>
              <div class="form-group row">
                <div class="col">
                  <label class="form-label" for="yt">Kategoria</label>
                  <select class="form-select w-100" name="kategoria" id="kategoria">
                    <option value="<?php echo $editcl['kategoria']; ?>" selected><?php echo $editcl['kategoria']; ?></option>
                    <?php
                    $kg = $conn->query("SELECT * FROM kategorit");
                    while ($kgg = mysqli_fetch_array($kg)) {
                      echo '<option value="' . $kgg['kategorit'] . '">' . $kgg['kategorit'] . '</option>';
                    }
                    ?>
                  </select>
                  <script>
                    new Selectr('#kategoria', {
                      searchable: true,
                      width: 300
                    });
                  </script>
                </div>
                <div class="col">
                  <label class="form-label" for="yt">Adresa</label>
                  <input type="text" name="adresa" class="form-control rounded-5 border border-2" value="<?php echo $editcl['adresa']; ?>" placeholder="Adresa">
                </div>
              </div>
              <div class="form-group row">
                <div class="col">
                  <label class="form-label" for="yt">Numri i telefonit</label>
                  <input type="text" name="nrtel" class="form-control rounded-5 border border-2" value="<?php echo $editcl['nrtel']; ?>" placeholder="Shëno numrin e telefonit të klientit">
                </div>
              </div>
              <div class="form-group row">
                <div class="col">
                  <label class="form-label" for="yt">Numri i telefonit</label>
                  <input type="text" name="nrtel" class="form-control rounded-5 border border-2" value="<?php echo $editcl['nrtel']; ?>" placeholder="Shëno numrin e telefonit të klientit">
                </div>
                <div class="col">
                  <div class="form-group row">
                    <div class="col">
                      <label class="form-label" for="yt">Numri i xhirollogarisë</label>
                      <input type="text" name="nrllog" class="form-control rounded-5 border border-2" placeholder="Shëno numrin e xhirollogarisë të klientit" value="<?php echo $editcl['nrllog']; ?>">
                    </div>
                    <div class="col">
                      <?php
                      // Fetch default value from the database (replace with your actual SQL query)
                      $sql = "SELECT bank_name FROM klientet WHERE id = '$editid'"; // You may need to adjust this query based on your table structure
                      $result = $conn->query($sql);
                      // Check if the query was successful
                      if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $defaultBank = $row["bank_name"];
                      } else {
                        $defaultBank = "Banka Ekonomike"; // Set a default value if no result is found
                      }
                      ?>
                      <label class="form-label" for="yt">Emri i bankës</label>
                      <select id="bank_info" name="bank_info" class="form-select rounded-5 border border-2 py-2">
                        <option value="<?php echo $defaultBank; ?>" selected><?php echo $defaultBank; ?></option>
                        <option value="Banka KombetareTregtare">Banka Kombëtare Tregtare (Albania)</option>
                        <option value="Banka Per Biznes">Banka për Biznes (Kosovo)</option>
                        <option value="NLB Komercijalna Banka">NLB Komercijalna banka (Slovenia)</option>
                        <option value="NLB Banka">NLB Banka (Slovenia)</option>
                        <option value="Pro Credit Bank">ProCredit Bank (Germany)</option>
                        <option value="Raiffeisen Bank Kosovo">Raiffeisen Bank Kosovo (Austria)</option>
                        <option value="TEB SHA">TEB SH.A. (Turkey)</option>
                        <option value="Ziraat Bank">Ziraat Bank (Turkey)</option>
                        <option value="Turkiye Is Bank">Turkiye Is Bank (Turkey)</option>
                        <option value="PayPal">PayPal</option>
                        <option value="Ria">Ria</option>
                        <option value="Money Gram"> Money Gram</option>
                        <option value="Western Union">Western Union</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <div class="col">
                  <label class="form-label" for="yt">Adresa e emailit</label>
                  <input type="text" name="emailadd" class="form-control rounded-5 border border-2" value="<?php echo $editcl['emailadd']; ?>" placeholder="Shëno emailin e klientit">
                </div>
                <div class="col">
                  <label class="form-label" for="yt">Adresa e emailit per platformat</label>
                  <input type="text" name="emailp" class="form-control rounded-5 border border-2" value="<?php echo $editcl['emailp']; ?>" placeholder="Shëno adresen e emailit e klientet per platforma">
                </div>
              </div>
              <div class="form-group row">
                <div class="col">
                  <label class="form-label">Përdorues i sistemit</label>
                  <input type="text" name="perdoruesi" class="form-control rounded-5 border border-2" placeholder="Shëno perdoruesin" value="<?php echo $editcl['perdoruesi']; ?>">
                </div>
                <!-- Add a button to open the modal -->
                <div class="col">
                  <label class="form-label">Fjalëkalimi i sistemit</label>
                  <input type="password" name="fjalekalimi" class="form-control rounded-5 border border-2" placeholder="Shëno fjalëkalimin e sistemit">
                  <button type="button" class="input-custom-css px-3 py-2 rounded-5 mt-1" data-bs-toggle="modal" data-bs-target="#passwordInfoModal">Info</button>
                </div>
                <!-- Create a modal for displaying password reset information -->
                <div class="modal fade" id="passwordInfoModal" tabindex="-1" role="dialog" aria-labelledby="passwordInfoModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="passwordInfoModalLabel">Informacione për rivendosjen e fjalëkalimit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <p>
                          Ky është fjalëkalimi i enkriptuar për klientin : <?php echo $editcl['emri'] ?>
                        </p>
                        <p>
                          Fjalëkalimi : <strong><?php echo $editcl['fjalkalimi']; ?> </strong>
                        </p>
                        <p>
                          Fjalëkalimi i hash MD5 në formën e tij të deshifruar. Sidoqoftë, MD5 është një funksion hash me një drejtim, që do të thotë se nuk mund të deshifrohet drejtpërdrejt sepse është projektuar të jetë i pakthyeshëm.
                        </p>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <div class="col">
                  <label class="form-label" for="tel">Monetizuar ? </label><br>
                  <?php if ($editcl['monetizuar'] == "PO") {
                  ?>
                    <input type="radio" id="html" name="min" value="PO" checked class="form-check-input">
                    <label class="form-check-label text-success" for="flexRadioDefault1">
                      PO ( aktive )
                    </label>
                    <input type="radio" id="css" name="min" value="JO" class="form-check-input">
                    <label class="form-check-label text-muted" for="flexRadioDefault1">
                      JO
                    </label>
                  <?php
                  } else {
                  ?>
                    <input type="radio" id="html" name="min" value="PO" class="form-check-input">
                      <label class="form-check-label text-muted" for="html">PO</label>
                      <input type="radio" id="css" name="min" value="JO" checked class="form-check-input">
                      <label class="form-check-label text-success" for="css">JO ( aktive )</label><br>
                  <?php } ?>
                </div>
                <div class="form-group row">
                  <div class="col">
                    <label for="perqindja" class="text-muted">Perqindja</label>
                    <div class="input-group mb-3">
                      <div class="input-group-text bg-transparent border-0">
                        <?php
                        //  $perqindja_check contains the value from the database (either '1' or '0')
                        $perqindja_check = $editcl['perqindja_check']; // Replace this with the actual value from your database
                        ?>
                        <input type="checkbox" name="perqindja_check" id="perqindja_check" <?php echo ($perqindja_check == "1") ? 'checked' : ''; ?> />
                      </div>
                      <input type="text" name="perqindja" class="form-control rounded-5 border border-2" value="<?php echo $editcl['perqindja']; ?>" placeholder="0.00%">
                    </div>
                    <p><?php if ($perqindja_check == "1") {
                        ?>
                        <label class="form-label" for="perqindja_check" style="color:green;font-size:12px;">Statusi i kesaj perqindje ne sistemin e klientit eshte e vrojtueshme</label>
                      <?php
                        } else {
                      ?>
                        <label class="form-label" for="perqindja_check" style="color:red;font-size:12px;">JO</label>
                      <?php
                        } ?>
                    </p>
                  </div>
                  <div class="col">
                    <label for="perqindja" class="text-muted">Perqindja platformave tjera</label>
                    <div class="input-group mb-3">
                      <div class="input-group-text bg-transparent border-0">
                        <?php
                        //  $perqindja_check contains the value from the database (either '1' or '0')
                        $perqindja_e_platformave_check = $editcl['perqindja_platformave_check'];
                        ?>
                        <input type="checkbox" name="perqindja_platformave_check" id="perqindja_platformave_check" <?php echo ($perqindja_e_platformave_check == "1") ? 'checked' : ''; ?> />
                      </div>
                      <input type="text" name="perqindja2" class="form-control rounded-5 border border-2" value="<?php echo $editcl['perqindja2']; ?>" placeholder="0.00%">
                    </div>
                    <p>
                      <?php if ($perqindja_e_platformave_check == "1") { ?>
                        <label class="form-label" for="perqindja_platformave_check" style="color:green;font-size:12px;">Statusi i kesaj perqindje ne sistemin e klientit eshte e vrojtueshme</label>
                      <?php } else { ?>
                        <label class="form-label" for="perqindja_platformave_check" style="color:red;font-size:12px;">JO</label>
                      <?php } ?>
                    </p>
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col">
                    <label class="form-label" for="imei">ADS Account: </label>
                    <select class="form-select w-100" name="ads" id="exampleFormControlSelect2">
                      <?php
                      $mads = $conn->query("SELECT * FROM ads");
                      $ads_found = false;
                      while ($ads = mysqli_fetch_array($mads)) {
                        if ($ads['id'] == $editcl['ads']) {
                          $ads_found = true;
                        }
                      ?>
                        <option value="<?php echo $ads['id']; ?>" <?php if ($ads['id'] == $editcl['ads']) {
                                                                    echo "selected";
                                                                  } ?>><?php echo $ads['email']; ?> | <?php echo $ads['adsid']; ?> (<?php echo $ads['shteti']; ?>)</option>
                      <?php } ?>
                      <?php if (!$ads_found && !empty($editcl['emri'])) {
                        $guse = $conn->query("SELECT * FROM klientet WHERE emri='" . $editcl['emri'] . "'");
                        $guse2 = mysqli_fetch_array($guse);
                        $adsid = $guse2['ads'];
                        $mads = $conn->query("SELECT * FROM ads WHERE id='$adsid'");
                        $ads = mysqli_fetch_array($mads);
                      ?>
                        <option value="<?php echo $ads['id']; ?>" selected><?php echo $ads['email']; ?> | <?php echo $ads['adsid']; ?> (<?php echo $ads['shteti']; ?>)</option>
                      <?php } ?>
                    </select>
                    <script>
                      new Selectr('#exampleFormControlSelect2', {
                        searchable: true,
                        width: 300
                      });
                    </script>
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <div class="col">
                  <label class="form-label"><i class="ti-instagram"></i> Instagram URL:</label>
                  <input type="URL" name="ig" class="form-control rounded-5 border border-2" placeholder="https://instagram.com/...." value="<?php echo $editcl['ig']; ?>">
                </div>
                <div class="col">
                  <label class="form-label"><i class="ti-facebook"></i> Facebook URL:</label>
                  <input type="URL" name="fb" class="form-control rounded-5 border border-2" placeholder="https://facebook.com/...." value="<?php echo $editcl['fb']; ?>">
                </div>
                <div class="col">
                  <label class="form-label">Email qe kan akses:</label>
                  <input type="text" name="emails" class="form-control rounded-5 border border-2" placeholder="Emails" value="<?php echo $editcl['emails']; ?>">
                </div>
              </div>
              <div class="col">
                <label class="form-label" for="info"> Info Shtes&euml;</label>
                <textarea class="form-control rounded-5 border border-2" id="simpleMde" name="info" placeholder="Info Shtes&euml;"><?php echo $editcl['info']; ?></textarea>
              </div>
              <button type="submit" class="input-custom-css px-3 py-2 mt-3" name="ndrysho"><i class='fi fi-rr-paper-plane'></i> Ruaj</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include 'partials/footer.php'; ?>