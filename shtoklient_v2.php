<?php
include 'partials/header.php';
if (isset($_POST['ruaj'])) {
  $client_name = $_POST['emri'];
  $monetization = empty($_POST['min']) ? "JO" : $_POST['min'];
  $start_date = mysqli_real_escape_string($conn, $_POST['dk']);
  $end_date = mysqli_real_escape_string($conn, $_POST['np']);
  $contract_duration = mysqli_real_escape_string($conn, $_POST['dks']);
  $youtube_channel_id = mysqli_real_escape_string($conn, $_POST['yt']);
  $info = mysqli_real_escape_string($conn, $_POST['info']);
  $percentage_1 = mysqli_real_escape_string($conn, $_POST['perqindja']);
  $percentage_2 = mysqli_real_escape_string($conn, $_POST['perqindja2']);
  $ads_platform = mysqli_real_escape_string($conn, $_POST['ads']);
  $facebook = mysqli_real_escape_string($conn, $_POST['fb']);
  $instagram = mysqli_real_escape_string($conn, $_POST['ig']);
  $address = mysqli_real_escape_string($conn, $_POST['adresa']);
  $category = mysqli_real_escape_string($conn, $_POST['kategoria']);
  $phone_number = mysqli_real_escape_string($conn, $_POST['nrtel']);
  $additional_email = mysqli_real_escape_string($conn, $_POST['emailadd']);
  $main_email = mysqli_real_escape_string($conn, $_POST['emailp']);
  $artist_name = mysqli_real_escape_string($conn, $_POST['emriart']);
  $registration_number = mysqli_real_escape_string($conn, $_POST['nrllog']);
  $bank_info = mysqli_real_escape_string($conn, $_POST['bank_info']);
  $username = mysqli_real_escape_string($conn, $_POST['perdoruesi']);
  $password = mysqli_real_escape_string($conn, $_POST["password"]);
  $emails = '';
  $percentage_check = isset($_POST['perqindja_check']) ? '1' : '0';
  $platform_percentage_check = isset($_POST['perqindja_platformave_check']) ? '1' : '0';
  if (isset($_POST['emails']) && !empty($_POST['emails'])) {
    $emails = implode(', ', $_POST['emails']);
  }
  $password = md5($password);
  $target_folder = "dokument/";
  $target_folder = $target_folder . basename($_FILES['tipi']['name']);
  $file_ok = 1;
  $file_type = $_FILES['tipi']['type'];
  if ($file_type != "application/pdf") {
    // Handle non-PDF file type
  } else {
    if (!move_uploaded_file($_FILES['tipi']['tmp_name'], $target_folder)) {
      // Handle file upload error
    }
  }
  $emails = addslashes($emails);
  $client_type = $_POST['type_of_client'];
  $insert_query = "INSERT INTO klientet 
    (emri, np, monetizuar, dk, dks, youtube, info, perqindja, perqindja2, kontrata, ads, fb, ig, adresa, kategoria, nrtel, emailadd, emailp, emriart, nrllog, bank_name ,fjalkalimi, perdoruesi, emails, blocked, perqindja_check, perqindja_platformave_check,lloji_klientit) VALUES ('$client_name', '$end_date','$monetization', '$start_date', '$contract_duration', '$youtube_channel_id', '$info', '$percentage_1', '$percentage_2', '$target_folder', '$ads_platform', '$facebook', '$instagram', '$address', '$category', '$phone_number', '$additional_email', '$main_email', '$artist_name', '$registration_number', '$bank_info', '$password', '$username', '$emails', '0', '$percentage_check', '$platform_percentage_check','$client_type')";
  if ($conn->query($insert_query)) {
    $query_latest_client = $conn->query("SELECT * FROM klientet ORDER BY id DESC");
    $latest_client = mysqli_fetch_array($query_latest_client);
    $current_date = date("Y-m-d H:i:s");
    // Check if email already exists in the database
    $check_email_query = $conn->prepare("SELECT `email` FROM `googleauth` WHERE `email`=?");
    $check_email_query->bind_param("s", $main_email);
    $check_email_query->execute();
    $check_email_query->store_result();
    // Get the username from the session
    $current_username = $_SESSION['username'];
    $user_info = $_COOKIE['user_first_name'] . ' ' . $_COOKIE['user_last_name'];
    if (empty($user_info)) {
      $user_info = $_SESSION['user_info'];
    }
    $action_log = $user_info . " ka shtuar klientin " . $client_name;
    $log_query = "INSERT INTO logs (stafi, ndryshimi, koha) VALUES ('$current_username', '$action_log', '$current_date')";
    if (!$conn->query($log_query)) {
      echo '<script>alert("' . $conn->error . '")</script>';
    }
    // Display success message with redirection
    echo '<script>
    Swal.fire({
      icon: "success",
      title: "Kengetari u shtua me sukses!",
      showConfirmButton: true,
      confirmButtonText: "Shiko k&euml;ngetarin",
      showCancelButton: true,
      cancelButtonText: "Mbylle",
      allowOutsideClick: false,
      allowEscapeKey: false,
      closeOnClickOutside: false,
      closeOnEsc: false,
    }).then((result) => {
     if (result.isConfirmed) {
       setTimeout(function() {
         window.location.href = "kanal.php?kid=' . $latest_client['id'] . '"; // Adjust the link to the actual URL of the client page
       }, 5000); // 5000 milliseconds = 5 seconds
     }
   });
   </script>';
  }
}
$query_latest_client = $conn->query("SELECT * FROM klientet ORDER BY id DESC LIMIT 1");
$latest_client = mysqli_fetch_array($query_latest_client);
?>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- <div class="container"> -->
      <nav class="bg-white px-2 rounded-5" style="width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a class="text-reset" style="text-decoration: none;">Klientët</a></li>
          <li class="breadcrumb-item"><a href="klient.php" class="text-reset" style="text-decoration: none;">Lista e
              klientëve</a></li>
          <li class="breadcrumb-item active" aria-current="page">Shto klientë</li>
        </ol>
      </nav>
      <div class="mb-3">
        <a href="klient.php" class="input-custom-css px-3 py-2" style="text-decoration: none;">
          Kthehu prapa
        </a>
      </div>
      <form method="POST" action="" enctype="multipart/form-data">
        <div class="row">
          <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="accordion " id="client-infos-accordion">
              <div class="accordion-item border-1 rounded-5">
                <h2 class="accordion-header " id="headingOne">
                  <button class="accordion-button border-0 rounded-5" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    <i class="fi fi-rr-user me-5"></i> Të dhënat e klientit
                  </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show " aria-labelledby="headingOne" data-bs-parent="#client-infos-accordion">
                  <div class="accordion-body border-0">
                    <div class="col">
                      <label class="form-label" for="emri">Emri dhe Mbiemri</label>
                      <input type="text" name="emri" id="emri" class="form-control border border-2 rounded-5" placeholder="Shëno emrin dhe mbiemrin e klientit">
                    </div>
                    <br>
                    <div class="col">
                      <label class="form-label" for="emri">ID e dokumentit personal</label>
                      <input type="text" name="np" id="emriart" class="form-control border border-2 rounded-5" placeholder="Shëno ID e dokumentit personal">
                    </div>
                    <br>
                    <div class="col">
                      <label class="form-label" for="yt">Adresa</label>
                      <input type="text" name="adresa" id="adresa" class="form-control border border-2 rounded-5" placeholder="Shëno adresën" autocomplete="off">
                    </div>
                    <br>
                    <div class="col">
                      <label class="form-label" for="yt">Adresa elektronike ( Email )</label>
                      <input type="text" name="emailadd" id="emailadd" class="form-control border border-2 rounded-5" placeholder="Shëno email-in e klientit" autocomplete="off">
                    </div>
                    <br>
                    <div class="col">
                      <label class="form-label" for="yt">Numri i telefonit</label>
                      <input type="text" name="nrtel" id="nrtel" class="form-control border border-2 rounded-5" placeholder="Shëno numrin e telefonit" autocomplete="off">
                    </div>
                    <br>
                    <div class="col">
                      <label class="form-label" for="yt">Zgjedh bankën</label>
                      <select id="bank_info" name="bank_info" class="form-select rounded-5 border border-2 py-2">
                        <option value="custom">Shto emrin e personalizuar të bankës</option> <!-- Add this option -->
                        <option value="Banka Ekonomike">Banka Ekonomike (Kosovo)</option>
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
                      <script>
                        new Selectr('#bank_info', {
                          searchable: true,
                          width: 300
                        });
                        document.getElementById('bank_info').addEventListener('change', function() {
                          var selectedOption = this.value;
                          if (selectedOption === 'custom') {
                            Swal.fire({
                              title: 'Shëno emrin e personalizuar të bankës:',
                              input: 'text',
                              showCancelButton: true,
                              confirmButtonText: 'Shto',
                              cancelButtonText: 'Anulo',
                              inputValidator: (value) => {
                                if (!value) {
                                  return 'Ju duhet të shënoni diçka!';
                                }
                              }
                            }).then((result) => {
                              if (result.isConfirmed) {
                                var customBankName = result.value;
                                // Add the custom bank name as an option
                                var selectElement = document.getElementById('bank_info');
                                var customOption = document.createElement('option');
                                customOption.value = customBankName;
                                customOption.textContent = customBankName;
                                selectElement.appendChild(customOption);
                                // Select the newly added custom bank name
                                selectElement.value = customBankName;
                              }
                            });
                          }
                        });
                      </script>
                    </div>
                    <br>
                    <div class="col">
                      <label class="form-label" for="yt">Nr. Xhirollogaris</label>
                      <input type="text" name="nrllog" id="nrllog" class="form-control border border-2 rounded-5" placeholder="Shëno numrin e xhirollogaris" autocomplete="off">
                    </div>
                    <br>
                    <div class="col">
                      <label class="form-label" for="type_of_client">Lloji i klientit</label>
                      <select name="type_of_client" id="type_of_client" class="form-select rounded-5 border border-2 py-2">
                        <option value="Personal">Personal</option>
                        <option value="Biznes">Biznes</option>
                      </select>
                      <script>
                        new Selectr('#type_of_client', {
                          searchable: true,
                          width: 300
                        })
                      </script>
                    </div>
                    <br>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="accordion" id="client-infos-youtube-accordion">
              <div class="accordion-item border-1 rounded-5">
                <h2 class="accordion-header" id="headingOne">
                  <button class="accordion-button border-0 rounded-5" type="button" data-bs-toggle="collapse" data-bs-target="#ciya" aria-expanded="true" aria-controls="ciya">
                    <i class="fi fi-brands-youtube me-5"></i> Të dhënat e klientit ( Youtube )
                  </button>
                </h2>
                <div id="ciya" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#client-infos-youtube-accordion">
                  <div class="accordion-body">
                    <div class="col">
                      <label class="form-label" for="yt">ID e kanalit në platformën Youtube</label>
                      <input type="text" name="yt" id="yt" class="form-control border border-2 rounded-5" placeholder="Shëno ID e kanalit në platformën Youtube" autocomplete="off">
                    </div>
                    <br>
                    <div class="col">
                      <label class="form-label" for="emri">Emri artistik</label>
                      <input type="text" name="emriart" id="emriart" class="form-control border border-2 rounded-5" placeholder="Shëno emrin artistik">
                    </div>
                    <br>
                    <div class="col">
                      <label class="form-label" for="dk">Data e fillimit të kontrates</label>
                      <input type="text" name="dk" id="dk" class="form-control border border-2 rounded-5" placeholder="Shëno daten e fillimit të kontrates" autocomplete="off">
                    </div>
                    <br>
                    <div class="col">
                      <label class="form-label" for="dks">Data e skadimit të kontrates</label>
                      <input type="text" name="dks" id="dks" class="form-control border border-2 rounded-5" placeholder="Shëno daten e skadimitë të kontrates" autocomplete="off">
                    </div>
                    <br>
                    <div class="col">
                      <label class="form-label" for="yt">Zgjedh kategorinë</label>
                      <select class="form-select border border-2 rounded-5 w-100" name="kategoria" id="kategoria">
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
                        })
                      </script>
                    </div>
                    <br>
                    <div class="col">
                      <label class="form-label" for="tel">A është ky kanal i monetizuar ? </label><br>
                      <input type="radio" class="form-check-input" id="html" name="min" value="PO">
                      <label class="form-label" for="html" style="color:green;">PO</label>
                    </div>
                    <br>
                    <div class="col">
                      <input type="radio" id="css" class="form-check-input" name="min" value="JO">
                      <label class="form-label" for="css" style="color:red;">JO</label><br>
                    </div>
                    <br>
                    <div class="col">
                      <label class="form-label">Zgjidhni llogarinë</label>
                      <select class="form-select border border-2 rounded-5 w-100" name="ads" id="js-example-basic-single w-100">
                        <?php
                        $mads = $conn->query("SELECT * FROM ads");
                        while ($ads = mysqli_fetch_array($mads)) {
                        ?>
                          <option value="<?php echo $ads['id']; ?>">
                            <?php echo $ads['email']; ?> |
                            <?php echo $ads['adsid']; ?>
                            (
                            <?php echo $ads['shteti']; ?>)
                          </option>
                        <?php } ?>
                      </select>
                    </div>
                    <br>
                    <div class="col">
                      <label class="form-label"><i class="ti-facebook"></i> Facebook URL:</label>
                      <input type="URL" name="fb" class="form-control border border-2 rounded-5" placeholder="https://facebook.com/....">
                    </div>
                    <br>
                    <div class="col">
                      <label class="form-label"><i class="ti-instagram"></i> Instagram URL:</label>
                      <input type="URL" name="ig" class="form-control border border-2 rounded-5" placeholder="https://instagram.com/....">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="accordion" id="perqindja-accordion">
              <div class="accordion-item border-1 rounded-5">
                <h2 class="accordion-header" id="headingOne">
                  <button class="accordion-button border-0 rounded-5" type="button" data-bs-toggle="collapse" data-bs-target="#pdp" aria-expanded="true" aria-controls="pdp">
                    <i class="fi fi-rr-world me-5"></i> Përqindja dhe platformat
                  </button>
                </h2>
                <div id="pdp" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#perqindja-accordion">
                  <div class="accordion-body">
                    <div class="col">
                      <label class="form-label" for="yt">Adresa elektronike ( Email ) per platforma</label>
                      <input type="text" name="emailp" id="emailp" class="form-control border border-2 rounded-5" placeholder="Shëno email-in e platformave te klientit" autocomplete="off">
                    </div>
                    <br>
                    <div class="col">
                      <label class="form-label" for="imei">Përqindja ( Baresha )</label>
                      <input type="text" name="perqindja" id="perqindja_input" class="form-control border border-2 rounded-5" placeholder="0.00%">
                    </div>
                    <br>
                    <div class="col">
                      <label class="form-label" for="imei">Përqindja për platformat tjera ( Baresha )</label>
                      <input type="text" name="perqindja2" id="perqindja_input2" class="form-control border border-2 rounded-5" placeholder="0.00%">
                    </div>
                    <br>
                    <div class="col">
                      <label for="perqindja" class="form-label">Perqindja e klientit</label>
                      <div class="input-group flex-nowrap">
                        <div class="input-group-text bg-transparent border-0" id="addon-wrapping">
                          <input type="checkbox" name="perqindja_check" id="perqindja_check" onchange="showMessage('perqindja_check', 'perqindja_input', 'perqindja_output', 'perqindja_message')">
                        </div>
                        <input type="text" class="form-control rounded-5 border border-2" id="perqindja_output" name="perqindja_e_klientit" disabled>
                      </div>
                      <br>
                      <div id="perqindja_message" style="display: none; color: green;">Kjo përqindje do të jetë e
                        dukshme për klientin.</div>
                    </div>
                    <br>
                    <div class="col">
                      <label for="perqindja" class="form-label">Perqindja e platformave për klientin</label>
                      <div class="input-group mb-3">
                        <div class="input-group-text bg-transparent border-0">
                          <input type="checkbox" name="perqindja_platformave_check" id="perqindja_platformave_check" onchange="showMessage('perqindja_platformave_check', 'perqindja_input2', 'perqindja_platformave_output', 'perqindja_platformave_message')">
                        </div>
                        <input type="text" class="form-control rounded-5 border border-2" id="perqindja_platformave_output" name="perqindja_e_klientit" disabled>
                      </div>
                      <div id="perqindja_platformave_message" style="display: none; color: green;">Kjo përqindje do të
                        jetë e dukshme për klientin.</div>
                    </div>
                    <br>
                    <div class="row mb-3">
                      <p class="text-muted" id="id_of_client">ID klientit : <?php echo $latest_client['id']; ?></p>
                      <br>
                      <p class="text-muted" id="youtube_info"></p>
                      <br>
                      <p class="text-muted" id="percentage_of_baresha_info"></p>
                      <br>
                      <p class="text-muted" id="percentage_info"></p>
                      <br>
                      <!-- Paragraphs to display emails and percentages -->
                      <p id="emails_paragraph"></p>
                      <p id="percentages_paragraph"></p>
                      <p id="percentage_sum_paragraph"></p>
                      <p id="precentage_of_client_after_split"></p>
                      <div class="col">
                        <label class="form-label" for="yt">Email-i</label>
                        <input type="text" name="emailsPT[]" id="emailsPT" class="form-control border border-2 rounded-5 email-input" placeholder="Shëno email-in e platformave te klientit" autocomplete="off">
                        <br>
                        <label class="form-label" for="perqindjaPT">Përqindja</label>
                        <input type="text" name="perqindjaPT[]" id="perqindjaPT" class="form-control border border-2 rounded-5 perqindja-input" placeholder="0.00%">
                      </div>
                    </div>
                    <div class="col align-self-end">
                      <button type="button" class="btn btn-primary add-row-btn">Shto Rresht</button>
                    </div>

                    <script>
                      // Add row functionality
                      $('.add-row-btn').click(function() {
                        var rowLength = $('.email-perqindja-row').length;
                        if (rowLength < 5) {
                          var newRow = `
                <div class="row email-perqindja-row mb-3">
                    <div class="col">
                        <label class="form-label" for="yt">Email-i</label>
                        <input type="text" name="emailsPT[]" class="form-control border border-2 rounded-5 email-input" placeholder="Shëno email-in e platformave te klientit" autocomplete="off">
                        <br>
                        <label class="form-label" for="perqindja">Përqindja</label>
                        <input type="text" name="perqindjaPT[]" class="form-control border border-2 rounded-5 perqindja-input" placeholder="0.00%">
                        <br>
                        <input type="checkbox" class="form-check-input remove-row-checkbox">
                        <label class="form-check-label" for="removeRow">Remove</label>
                    </div>
                </div>
            `;
                          $('.add-row-btn').before(newRow);
                          if (rowLength + 1 === 5) {
                            $('.add-row-btn').prop('disabled', true);
                          }
                        } else {
                          alert("You can add up to 5 rows.");
                        }
                      });
                    </script>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="accordion" id="information-accordion">
              <div class="accordion-item border-1 rounded-5">
                <h2 class="accordion-header" id="headingOne">
                  <button class="accordion-button border-0 rounded-5" type="button" data-bs-toggle="collapse" data-bs-target="#iib" aria-expanded="true" aria-controls="iib">
                    <i class="fi fi-rr-info me-5"></i> Informacion i brendshëm
                  </button>
                </h2>
                <div id="iib" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#information-accordion">
                  <div class="accordion-body">
                    <div class="col">
                      <label class="form-label" for="imei">Ngarko kontrat&euml;n:</label>
                      <div class="file-upload-wrapper">
                        <input type="file" name="tipi" class="fileuploader form-control border border-2 rounded-5" />
                      </div>
                    </div>
                    <br>
                    <div class="col">
                      <label class="form-label" for="imei">Email qe kan akses</label>
                      <select multiple class="form-control border border-2 rounded-5" name="emails[]" id="exampleFormControlSelect2">
                        <?php
                        $getemails = $conn->query("SELECT * FROM emails");
                        while ($maillist = mysqli_fetch_array($getemails)) {
                        ?>
                          <option value="<?php echo $maillist['email']; ?>">
                            <?php echo $maillist['email']; ?>
                          </option>
                        <?php } ?>
                      </select>
                      <script>
                        new Selectr('#exampleFormControlSelect2', {
                          multiple: true
                        })
                      </script>
                    </div>
                    <br>
                    <div class="col">
                      <label class="form-label">P&euml;rdoruesi <small>(Sistemit)</small>:</label>
                      <input type="text" name="perdoruesi" class="form-control border border-2 rounded-5" placeholder="P&euml;rdoruesi i sistemit">
                    </div>
                    <br>
                    <div class="col">
                      <label class="form-label">Fjalekalimi <small>(Sistemit)</small>:</label>
                      <input type="password" name="password" class="form-control border border-2 rounded-5" placeholder="Fjalkalimi i sistemit">
                    </div>
                    <br>
                    <div class="col">
                      <label class="form-label" for="info"> Info Shtes&euml;</label>
                      <textarea id="simpleMde" name="info" placeholder="Info Shtes&euml;" class="form-control border border-2 rounded-5"></textarea>
                    </div>
                    <br>
                    <hr>
                    <div class="col d-flex justify-content-center align-items-center">
                      <button type="submit" class="btn btn-danger rounded-5 text-white shadow mb-3" style="text-transform:none;" name="ruaj">
                        <i class="ti-save"></i>
                        <i class="fi fi-rr-bookmark me-2"></i>
                        Ruaj të gjitha informacionet
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
  // Function to show or hide message based on checkbox and input value
  function showMessage(checkboxId, inputId, outputId, messageId) {
    var checkbox = document.getElementById(checkboxId);
    var input = document.getElementById(inputId);
    var output = document.getElementById(outputId);
    var message = document.getElementById(messageId);

    // Check if the input has a valid value
    var inputValue = parseFloat(input.value);
    var isValidValue = !isNaN(inputValue) && inputValue > 0;

    // Show message only if checkbox is checked and input has a valid value
    message.style.display = checkbox.checked && input.value.trim() !== "" && isValidValue ? "block" : "none";
  }

  // Function to handle input for perqindja_input
  document.getElementById('perqindja_input').addEventListener('input', function() {
    var inputVal = parseFloat(this.value);
    if (!isNaN(inputVal) && inputVal >= 0 && inputVal <= 100) {
      var result = 100 - inputVal;
      document.getElementById('perqindja_output').value = result.toFixed(2) + "%";
      document.getElementById("percentage_info").innerHTML = "Perqindja e klientit : " + result.toFixed(2) + "%";
      document.getElementById("percentage_of_baresha_info").innerHTML = "Perqindja e Bareshës : " + inputVal + "%";
    } else {
      // Clear the input field if the value is greater than 100 or not a number
      this.value = "";
      document.getElementById('perqindja_output').value = "";
    }
    showMessage('perqindja_check', this, 'perqindja_output', 'perqindja_message');
  });

  // Function to handle input for perqindja_input2
  document.getElementById('perqindja_input2').addEventListener('input', function() {
    var inputVal = parseFloat(this.value);
    if (!isNaN(inputVal) && inputVal >= 0 && inputVal <= 100) {
      var result = 100 - inputVal;
      document.getElementById('perqindja_platformave_output').value = result.toFixed(2) + "%";
    } else {
      // Clear the input field if the value is greater than 100 or not a number
      this.value = "";
      document.getElementById('perqindja_platformave_output').value = "";
    }
    showMessage('perqindja_platformave_check', this, 'perqindja_platformave_output', 'perqindja_platformave_message');
  });
  // Remove row functionality
  $(document).on('change', '.remove-row-checkbox', function() {
    if ($(this).is(':checked')) {
      $(this).closest('.email-perqindja-row').remove();
      $('.add-row-btn').prop('disabled', false); // Enable the button when a row is removed
    }
  });
  // Update the paragraph with YouTube value
  document.getElementById("yt").oninput = function() {
    var youtubeValue = this.value;
    document.getElementById("youtube_info").innerText = "Youtube ID : " + youtubeValue;
  };

  // Function to update paragraphs with emails and percentages
  function updateParagraphs() {
    var emails = [];
    var percentages = [];

    // Iterate through each email-input and perqindja-input
    $('.email-input').each(function(index) {
      var email = $(this).val().trim();
      if (isValidEmail(email)) {
        emails.push(email);
      }
    });

    $('.perqindja-input').each(function(index) {
      var percentage = $(this).val().trim();
      if (isValidPercentage(percentage)) {
        percentages.push(parseFloat(percentage)); // Parse percentage as float and push to array
      }
    });

    // Update the paragraphs with emails and percentages
    $('#emails_paragraph').text("Email-et: " + emails.join(', '));
    $('#percentages_paragraph').text("Përqindjet: " + percentages.join(', '));

    // Calculate sum of percentages
    var sum = percentages.reduce((acc, curr) => acc + curr, 0);
    $('#percentage_sum_paragraph').text("Shuma e përqindjeve: " + sum.toFixed(2) + "%"); // Display sum below the paragraphs

    // Extract perqindja e klientit value from percentage_info paragraph
    var perqindjaKlientit = parseFloat($('#percentage_info').text().match(/\d+\.\d+/)[0]);
    var difference = perqindjaKlientit - sum;

    // Update the paragraph with the result
    $('#precentage_of_client_after_split').text("Perqindja e klientit pas ndarjes: " + difference.toFixed(2) + "%");
  }

  // Check if a string is a valid email address
  function isValidEmail(email) {
    // You can implement more comprehensive email validation if needed
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  // Check if a string is a valid percentage value
  function isValidPercentage(percentage) {
    var percentageRegex = /^\d+(\.\d{1,2})?$/; // Matches digits with optional decimal up to two places
    return percentageRegex.test(percentage) && parseFloat(percentage) >= 0 && parseFloat(percentage) <= 100;
  }

  // Update paragraphs whenever input fields change
  $(document).on('input', '.email-input, .perqindja-input', function() {
    updateParagraphs();
  });

  // Initially update the paragraphs
  updateParagraphs();
</script>
<?php include 'partials/footer.php'; ?>
<script>
  // Flatpickr configurations
  $("#dk").flatpickr({
    dateFormat: "Y-m-d",
    maxDate: "today"
  });

  $("#dks").flatpickr({
    dateFormat: "Y-m-d",
    minDate: "today"
  });
</script>