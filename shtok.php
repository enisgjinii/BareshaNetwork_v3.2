<?php
include 'partials/header.php';
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
  $emailp = mysqli_real_escape_string($conn, $_POST['emailp']);
  $emriart = mysqli_real_escape_string($conn, $_POST['emriart']);
  $nrllog = mysqli_real_escape_string($conn, $_POST['nrllog']);
  $bank_info = mysqli_real_escape_string($conn, $_POST['bank_info']);
  $perdoruesi = mysqli_real_escape_string($conn, $_POST['perdoruesi']);
  $password = mysqli_real_escape_string($conn, $_POST["password"]);
  $emails = '';
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
  if ($conn->query(
    "INSERT INTO klientet 
    (emri, np, monetizuar, dk, dks, youtube, info, perqindja, perqindja2, kontrata, ads, fb, ig, adresa, kategoria, nrtel, emailadd, emailp, emriart, nrllog, bank_name ,fjalkalimi, perdoruesi, emails, blocked) VALUES ('$emri', '$np','$mon', '$dk', '$dks', '$yt', '$info', '$perq', '$perq2', '$targetfolder', '$ads', '$fb', '$ig', '$adresa', '$kategoria', '$nrtel', '$emailadd', '$emailp', '$emriart', '$nrllog', '$bank_info', '$password', '$perdoruesi', '$emails', '0')"
  )) {
    $kueri = $conn->query("SELECT * FROM klientet ORDER BY id DESC");
    $k = mysqli_fetch_array($kueri);
    $cdata = date("Y-m-d H:i:s");
    $cname = $_SESSION['emri'];
    $cnd = $cname . " ka shtuar  klientin " . $emri;
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
  }
}
?>
<style>
  .allocation-div {
    display: none;
  }

  .allocation-div.active {
    display: block;
  }
</style>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="container">
        <div class="p-5 rounded-5 border border-2 mb-4 card">
          <div class="alert alert-successalert-dismissible" id="success" style="display:none;">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">X</a>
          </div>
          <!-- Page Heading -->
          <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group row">
              <div class="col">
                <label class="form-label" for="emri">Emri & Mbiemri</label>
                <input type="text" name="emri" id="emri" class="form-control border border-2 rounded-5" placeholder="Shkruaj Emrin Mbiemrin">
              </div>
              <div class="col">
                <label class="form-label" for="yt">Shkruaj ID e kanalit t&euml; YouTube</label>
                <input type="text" name="yt" id="yt" class="form-control border border-2 rounded-5" placeholder="Youtube Channel ID" autocomplete="off">
              </div>

            </div>
            <div class="form-group row">
              <div class="col">
                <label class="form-label" for="emri">Emri artistik</label>
                <input type="text" name="emriart" id="emriart" class="form-control border border-2 rounded-5" placeholder="Emri artistik">
              </div>
              <div class="col">
                <label class="form-label" for="emri">ID Dokumentit</label>
                <input type="text" name="np" id="emriart" class="form-control border border-2 rounded-5" placeholder="ID Dokumentit">
              </div>

            </div>
            <div class="form-group row">
              <div class="col">
                <label class="form-label" for="dk">Data e Kontrates</label>
                <input type="text" name="dk" id="dk" class="form-control border border-2 rounded-5" placeholder="Shkruaj Daten e kontrates" autocomplete="off">
              </div>
              <div class="col">
                <label class="form-label" for="dks">Data e Skadimit <small>(Kontrates)</small></label>
                <input type="text" name="dks" id="dks" class="form-control border border-2 rounded-5" placeholder="Shkruaj Daten e skaditimit" autocomplete="off">
              </div>


            </div>
            <div class="form-group row">
              <div class="col">

                <label class="form-label" for="yt">Kategoria</label>
                <select class="form-select border border-2 rounded-5 w-100" name="kategoria" id="kategoria">
                  <?php
                  $kg = $conn->query("SELECT * FROM kategorit");
                  while ($kgg = mysqli_fetch_array($kg)) {
                    echo '<option value="' . $kgg['kategorit'] . '">' . $kgg['kategorit'] . '</option>';
                  }
                  ?>
                </select>
              </div>
              <div class="col">
                <label class="form-label" for="yt">Adresa</label>
                <input type="text" name="adresa" id="adresa" class="form-control border border-2 rounded-5" placeholder="Adresa" autocomplete="off">
              </div>
            </div>
            <div class="form-group row">
              <div class="col">
                <label class="form-label" for="yt">Nr.Tel</label>
                <input type="text" name="nrtel" id="nrtel" class="form-control border border-2 rounded-5" placeholder="Nr.Tel" autocomplete="off">
              </div>
              <div class="col">
                <div class="form-group row">
                  <div class="col">
                    <label class="form-label" for="yt">Nr. Xhirollogaris</label>
                    <input type="text" name="nrllog" id="nrllog" class="form-control border border-2 rounded-5" placeholder="Nr. Xhirollogaris" autocomplete="off">
                  </div>
                  <div class="col">
                    <label class="form-label" for="yt">Zgjedh bankën</label>
                    <select id="bank_info" name="bank_info" class="form-select rounded-5 border border-2 py-2">
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
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <div class="col">
                <label class="form-label" for="yt">Email Adresa</label>
                <input type="text" name="emailadd" id="emailadd" class="form-control border border-2 rounded-5" placeholder="Email Adresa" autocomplete="off">
              </div>
              <div class="col">
                <label class="form-label" for="yt">Email Adresa per platforma</label>
                <input type="text" name="emailp" id="emailp" class="form-control border border-2 rounded-5" placeholder="Email Adresa per platforma" autocomplete="off">
              </div>
            </div>
            <div class="form-group row">
              <div class="col">
                <label class="form-label">P&euml;rdoruesi <small>(Sistemit)</small>:</label>
                <input type="text" name="perdoruesi" class="form-control border border-2 rounded-5" placeholder="P&euml;rdoruesi i sistemit">
              </div>
              <div class="col">
                <label class="form-label">Fjalekalimi <small>(Sistemit)</small>:</label>
                <input type="password" name="password" class="form-control border border-2 rounded-5" placeholder="Fjalkalimi i sistemit">
              </div>
            </div>
            <div class="form-group row">
              <div class="col">
                <label class="form-label" for="tel">Monetizuar ? </label><br>
                <input type="radio" id="html" name="min" value="PO">
                <label class="form-label" for="html" style="color:green;">PO</label>
                <input type="radio" id="css" name="min" value="JO">
                <label class="form-label" for="css" style="color:red;">JO</label><br>

              </div>

              <div class="col">
                <label class="form-label">Zgjidhni kategorin</label>
                <select class="form-select border border-2 rounded-5 w-100" name="ads" id="js-example-basic-single w-100">
                  <option value="">Zgjidhni Llogarin&euml;</option>
                  <?php
                  $mads = $conn->query("SELECT * FROM ads");
                  while ($ads = mysqli_fetch_array($mads)) {
                  ?>
                    <option value="<?php echo $ads['id']; ?>"><?php echo $ads['email']; ?> | <?php echo $ads['adsid']; ?>
                      (<?php echo $ads['shteti']; ?>)</option>
                  <?php } ?>
                </select>
              </div>

            </div><br>
            <div class="form-group row">
              <div class="col">
                <label class="form-label" for="imei">Ngarko kontrat&euml;n:</label>
                <div class="file-upload-wrapper">
                  <input type="file" name="tipi" class="fileuploader form-control border border-2 rounded-5" />
                </div>
              </div>

              <div class="col">
                <label class="form-label" for="imei">Perqindja:</label>
                <input type="text" name="perqindja" class="form-control border border-2 rounded-5" placeholder="0.00%">
              </div>
              <div class="col">
                <label class="form-label" for="imei">Perqindja platformat tjera:</label>
                <input type="text" name="perqindja2" class="form-control border border-2 rounded-5" placeholder="0.00%">
              </div>
            </div>





            <div class="form-group row">
              <div class="col">
                <label class="form-label"><i class="ti-facebook"></i> Facebook URL:</label>
                <input type="URL" name="fb" class="form-control border border-2 rounded-5" placeholder="https://facebook.com/....">
              </div>
              <div class="col">
                <label class="form-label"><i class="ti-instagram"></i> Instagram URL:</label>
                <input type="URL" name="ig" class="form-control border border-2 rounded-5" placeholder="https://instagram.com/....">
              </div>
            </div>
            <div class="form-group row">
              <div class="col">
                <label class="form-label" for="imei">Email qe kan akses <small>(Mbaj shtypur CTRL)</small> </label>
                <select multiple class="form-control border border-2 rounded-5" name="emails[]" id="exampleFormControlSelect2">
                  <?php
                  $getemails = $conn->query("SELECT * FROM emails");
                  while ($maillist = mysqli_fetch_array($getemails)) {
                  ?>
                    <option value="<?php echo $maillist['email']; ?>"><?php echo $maillist['email']; ?></option>
                  <?php } ?>
                </select>
              </div>

            </div>
            <div class="form-group row">
              <div class="col">
                <label class="form-label" for="info"> Info Shtes&euml;</label>
                <textarea id="simpleMde" name="info" placeholder="Info Shtes&euml;" class="form-control border border-2 rounded-5"></textarea>
              </div>
            </div>
        </div>
        <br>
        <center> <button type="submit" class="btn btn-primary" name="ruaj"><i class="ti-save"></i> Ruaj</button> </center>
        </form>

      </div>
      <!-- /.container-fluid -->
    </div>
  </div>
</div>

<script>
  const allocationDivs = document.querySelectorAll('.allocation-div');
  const createAllocationBtn = document.querySelector('#create-allocation-btn');
  let currentDivIndex = 0;

  createAllocationBtn.addEventListener('click', () => {
    if (currentDivIndex < allocationDivs.length) {
      allocationDivs[currentDivIndex].classList.add('active');
      currentDivIndex++;
    }

    if (currentDivIndex >= allocationDivs.length) {
      createAllocationBtn.disabled = true;
    }
  });

  // Your YouTube Data API key
</script>
<?php include 'partials/footer.php'; ?>

<script>
  $(document).ready(function() {
    $('#yt').on('input', function() {
      var channelId = $(this).val();

      // Check if the input is empty
      if (!channelId) {
        $('#channelInfo').html('');
        $('#emriart').val(''); // Clear the artistik name input
        return;
      }

      // Your YouTube Data API key
      var apiKey = 'AIzaSyCjlRRPMTbGcM_QE081YCy4zHKI9sUaZTg';

      // Make a request to the YouTube Data API to get channel information
      var url = 'https://www.googleapis.com/youtube/v3/channels?part=snippet,statistics&id=' + channelId + '&key=' + apiKey;

      $.ajax({
        url: url,
        type: 'GET',
        success: function(data) {
          if (data.items && data.items.length > 0) {
            var channelInfo = data.items[0].snippet;
            var channelStatistics = data.items[0].statistics;


            // Set the channel name in the "emriart" input field
            $('#emriart').val(channelInfo.title);
          } else {
            showErrorAlert('Channel not found.');
          }
        },
        error: function() {
          showErrorAlert('Unable to fetch channel information.');
        }
      });
    });

    // Function to show error alerts
    function showErrorAlert(message) {
      $('#channelInfo').html('<div class="alert alert-danger">' + message + '</div>');
      $('#emriart').val(''); // Clear the artistik name input
    }
  });
</script>