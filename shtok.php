<?php
include 'partials/header.php';
require 'vendor/autoload.php';
// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;
// use Dompdf\Dompdf;
// use Dompdf\Options;
// $mail = new PHPMailer(true); // Passing `true` enables exceptions
if (isset($_POST['ruaj'])) {
  $emri = $_POST['emri'];
  if (empty($_POST['min'])) {
    $mon = "JO";
  } else {
    $mon = $_POST['min'];
  }
  $dk = mysqli_real_escape_string($conn, $_POST['dk']);
  $np = mysqli_real_escape_string($conn, $_POST['np']);
  $dks = mysqli_real_escape_string($conn, $_POST['dks']);
  $yt = mysqli_real_escape_string($conn, $_POST['yt']);
  $info = mysqli_real_escape_string($conn, $_POST['info']);
  $perq = mysqli_real_escape_string($conn, $_POST['perqindja']);
  $perq2 = mysqli_real_escape_string($conn, $_POST['perqindja2']);
  $ads = mysqli_real_escape_string($conn, $_POST['ads']);
  $fb = mysqli_real_escape_string($conn, $_POST['fb']);
  $ig = mysqli_real_escape_string($conn, $_POST['ig']);
  $adresa = mysqli_real_escape_string($conn, $_POST['adresa']);
  $kategoria = mysqli_real_escape_string($conn, $_POST['kategoria']);
  $nrtel = mysqli_real_escape_string($conn, $_POST['nrtel']);
  $emailadd = mysqli_real_escape_string($conn, $_POST['emailadd']);
  $email_kontablist = mysqli_real_escape_string($conn, $_POST['email_kontablist']);
  $emailp = mysqli_real_escape_string($conn, $_POST['emailp']);
  $emriart = mysqli_real_escape_string($conn, $_POST['emriart']);
  $nrllog = mysqli_real_escape_string($conn, $_POST['nrllog']);
  $bank_info = mysqli_real_escape_string($conn, $_POST['bank_info']);
  $perdoruesi = mysqli_real_escape_string($conn, $_POST['perdoruesi']);
  $password = mysqli_real_escape_string($conn, $_POST["password"]);
  $emails = '';
  $perqindja_check = isset($_POST['perqindja_check']) ? '1' : '0';
  $perqindja_e_platformave_check = isset($_POST['perqindja_platformave_check']) ? '1' : '0';
  if (isset($_POST['emails']) && !empty($_POST['emails'])) {
    $emails = implode(', ', $_POST['emails']);
  }
  $password = md5($password);
  $targetfolder = "dokument/";
  $targetfolder = $targetfolder . basename($_FILES['tipi']['name']);
  $ok = 1;
  $file_type = $_FILES['tipi']['type'];
  if ($file_type == "application/pdf") {
    if (move_uploaded_file($_FILES['tipi']['tmp_name'], $targetfolder)) {
    } else {
    }
  } else {
  }
  $emails = addslashes($emails);
  $type__of_client = $_POST['type_of_client'];
  if ($conn->query(
    "INSERT INTO klientet 
    (emri, np, monetizuar, dk, dks, youtube, info, perqindja, perqindja2, kontrata, ads, fb, ig, adresa, kategoria, nrtel, emailadd, emailp, emriart, nrllog, bank_name ,fjalkalimi, perdoruesi, emails, blocked, perqindja_check, perqindja_platformave_check,lloji_klientit,email_kontablist) VALUES ('$emri', '$np','$mon', '$dk', '$dks', '$yt', '$info', '$perq', '$perq2', '$targetfolder', '$ads', '$fb', '$ig', '$adresa', '$kategoria', '$nrtel', '$emailadd', '$emailp', '$emriart', '$nrllog', '$bank_info', '$password', '$perdoruesi', '$emails', '0', '$perqindja_check', '$perqindja_e_platformave_check','$type__of_client','$email_kontablist')"
  )) {
    $kueri = $conn->query("SELECT * FROM klientet ORDER BY id DESC");
    $k = mysqli_fetch_array($kueri);
    $cdata = date("Y-m-d H:i:s");
    // Kontrollo nëse email-i ekziston tashmë në bazën e të dhënave
    $check_email = $conn->prepare("SELECT `email` FROM `googleauth` WHERE `email`=?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();
    // Marrja e emrit të përdoruesit nga sesioni
    $cname = $_SESSION['username'];
    // Nëse emri dhe mbiemri nuk janë disponibël në sesion, mund të përdorni vlerat e ruajtura në cookies
    // $outha
    $f_name = $_COOKIE['user_first_name'];
    $l_name = $_COOKIE['user_last_name'];
    // Krijo string-un për të përdorur në deklaratën më poshtë
    $user_info = $f_name . ' ' . $l_name;
    // Nëse informacioni për emër dhe mbiemër nuk është në dispozicion, mund të përdorni vlerën e ruajtur në sesion
    if (empty($user_info)) {
      $user_info = $_SESSION['user_info'];
    }
    // Përgatit string-un për log-in e veprimit
    $cnd = $user_info . " ka shtuar klientin " . $emri;
    $query = "INSERT INTO logs (stafi, ndryshimi, koha) VALUES ('$cname', '$cnd', '$cdata')";
    if ($conn->query($query)) {
    } else {
      echo '<script>alert("' . $conn->error . '")</script>';
    }
    // Add the Sweet Alert with a button to go to the newly added client page
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
       window.location.href = "kanal.php?kid=' . $k['id'] . '"; // Adjust the link to the actual URL of the client page
     }
   });
   </script>';
    //     try {
    //       // Cilësimet e serverit
    //       $mail->isSMTP(); // Vendos mailer-in për të përdorur SMTP
    //       $mail->Host = 'smtp.gmail.com'; // Specifikoni serverët kryesor dhe rezervë SMTP
    //       $mail->SMTPAuth = true; // Aktivizoni autentikimin SMTP
    //       $mail->Username = 'egjini@bareshamusic.com'; // Emri i përdoruesit SMTP
    //       $mail->Password = 'nzlnbougxyeijlci'; // Fjalëkalimi SMTP
    //       $mail->SMTPSecure = 'tls'; // Aktivizoni kodimin TLS, pranohet edhe `ssl`
    //       $mail->Port = 587; // Porti TCP për tu lidhur me të
    //       $mail->setFrom('egjini@bareshamusic.com', 'Dërguesi');
    //       $mail->addAddress('egjini@bareshamusic.com', 'Dërguesi');
    //       $mail->addAddress('kastriot@bareshamusic.com', 'Kastrioti');
    //       $mail->CharSet = 'UTF-8';
    //       $mail->isHTML(true);
    //       // Bashkangjitje
    //       $mail->Subject = 'Mirë se erdhët';
    //       // Përgatitni trupin e emailit me informacionin e përdoruesit
    //       $mail->Body = "
    //     <div style='background-color: #f5f5f5; border-radius: 10px; padding: 20px; margin: 20px;'>
    //         <h2 style='color: #333;'>Ju mire se erdhet </h2>
    //         <hr>
    //     </div>
    // ";
    //       // Dërgoni emailin
    //       $mail->send();
    //     } catch (Exception $e) {
    //       // Trajtoni ndonjë gabim nëse ekziston
    //     }
  }
}
?>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- <div class="container"> -->
      <nav class="bg-white px-2 rounded-5" style="width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a class="text-reset" style="text-decoration: none;">Klientët</a></li>
          <li class="breadcrumb-item"><a href="klient.php" class="text-reset" style="text-decoration: none;">Lista e klientëve</a></li>
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
                      <label class="form-label" for="email_kontablist">Adresa elektronike e kontablistit ( Email )</label>
                      <input type="text" name="email_kontablist" id="email_kontablist" class="form-control border border-2 rounded-5" placeholder="Shëno email-in e kontablistit" autocomplete="off">
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
                      <input type="text" name="yt" id="yt" class="form-control border border-2 rounded-5" placeholder="Shëno ID e kanalit në platformën Yotube" autocomplete="off">
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
                      <select class="form-select border border-2 rounded-5 w-100" name="ads" id="llogaria">
                        <?php
                        $mads = $conn->query("SELECT * FROM ads");
                        while ($ads = mysqli_fetch_array($mads)) {
                        ?>
                          <option value="<?php echo $ads['id']; ?>"><?php echo $ads['email']; ?> | <?php echo $ads['adsid']; ?>
                            (<?php echo $ads['shteti']; ?>)</option>
                        <?php } ?>
                      </select>
                      <script>
                        new Selectr('#llogaria', {
                          searchable: true,
                          width: 300
                        })
                      </script>
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
                      <div id="perqindja_message" style="display: none; color: green;">Kjo përqindje do të jetë e dukshme për klientin.</div>
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
                      <div id="perqindja_platformave_message" style="display: none; color: green;">Kjo përqindje do të jetë e dukshme për klientin.</div>
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
                          <option value="<?php echo $maillist['email']; ?>"><?php echo $maillist['email']; ?></option>
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
  document.getElementById('perqindja_input').addEventListener('input', function() {
    var inputVal = parseFloat(this.value);
    if (!isNaN(inputVal) && inputVal >= 0 && inputVal <= 100) {
      var result = 100 - inputVal;
      document.getElementById('perqindja_output').value = result.toFixed(2) + "%";
    } else {
      // Clear the input field if the value is greater than 100 or not a number
      this.value = "";
      document.getElementById('perqindja_output').value = "";
    }
    showMessage('perqindja_check', this, 'perqindja_output', 'perqindja_message');
  });
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
</script>
<?php include 'partials/footer.php'; ?>
<script>
  $("#dk").flatpickr({
    dateFormat: "Y-m-d",
    maxDate: "today"
  })
  $("#dks").flatpickr({
    dateFormat: "Y-m-d",
    minDate: "today"
  })
</script>