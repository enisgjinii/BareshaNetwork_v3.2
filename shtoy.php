<?php
date_default_timezone_set('Europe/Tirane');
include 'partials/header.php';
if (isset($_POST['ruaj'])) {
  $emri = mysqli_real_escape_string($conn, $_POST['emri']);
  $teksti = mysqli_real_escape_string($conn, $_POST['teksti']);
  $muzika = mysqli_real_escape_string($conn, $_POST['muzika']);
  $orkestra = mysqli_real_escape_string($conn, $_POST['orkestra']);
  if ($_POST['cover'] == "Cover") {
    $co = "Cover";
  } elseif ($_POST['cover'] == "Origjinale") {
    $co = "Origjinale";
  } else {
    $co = "Potpuri";
  }
  if ($_POST['facebook'] == "PO") {
    $facebook = "PO";
  } else {
    $facebook = "Jo";
  }
  if ($_POST['Instagram'] == "PO") {
    $instagram = "PO";
  } else {
    $instagram = "Jo";
  }
  $veper = mysqli_real_escape_string($conn, $_POST['veper']);
  $kengetari2 = mysqli_real_escape_string($conn, $_POST['kengtari']);
  $klienti = mysqli_real_escape_string($conn, $_POST['klienti']);
  $platforma = mysqli_real_escape_string($conn, $_POST['platforma']);

  $platformat = implode(', ', $_POST['platformat']);

  $linku = mysqli_real_escape_string($conn, $_POST['linku']);
  $linkuplat = mysqli_real_escape_string($conn, $_POST['linkuplat']);
  $data = mysqli_real_escape_string($conn, $_POST['data']);
  $gjuha = mysqli_real_escape_string($conn, $_POST['gjuha']);
  $nga = $_SESSION['uid'];
  $infosh = $_POST['infosh'];



  if (!$inserto = $conn->query("INSERT INTO ngarkimi (kengetari, emri, teksti, muzika, orkestra, co, facebook, instagram, veper, klienti, platforma, platformat, linku, data, gjuha, infosh, nga, linkuplat) VALUES ('$kengetari2', '$emri', '$teksti', '$muzika', '$orkestra', '$co', '$facebook', '$instagram', '$veper', '$klienti', '$platforma', '$platformat', '$linku', '$data', '$gjuha', '$infosh', '$nga', '$linkuplat')")) {
    echo "Ka ndodhur nje gabim" . $conn->error;
  } else {
    $cdata = date("Y-m-d His");
    $cname = $_SESSION['emri'];
    $cnd = $cname . " ka ngarkuar  " . $emri . " n&euml; sistem";
    $query = "INSERT INTO logs (stafi, ndryshimi, koha) VALUES ('$cname', '$cnd', '$cdata')";
    if ($conn->query($query)) {
    } else {
      echo '<script>alert("' . $conn->error . '")</script>';
    }
  }
}




?>
<link rel="stylesheet" type="text/css" href="tcal.css" />
<script type="text/javascript" src="tcal.js"></script>
<!-- Begin Page Content -->
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="container">
        <div class="p-5 rounded-5 shadow-sm mb-2 card">
          <h4 class="font-weight-bold text-gray-800 mb-4">Regjistro nj&euml; k&euml;ng&euml;</h4>
          <nav class="d-flex">
            <h6 class="mb-0">
              <a href="" class="text-reset">Videot / Ngarkimi</a>
              <span>/</span>
              <a href="shtoy.php" class="text-reset" data-bs-placement="top" data-bs-toggle="tooltip"
                title="<?php echo __FILE__; ?>"><u>Regjistro nj&euml; k&euml;ng&euml;</u></a>
            </h6>
          </nav>
        </div>
        <div class="alert alert-successalert-dismissible" id="success" style="display:none;">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">X</a>
        </div>
        <div class="card rounded-5 shadow-sm p-5">
          <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group row">
              <div class="col">
                <label for="emri" class="form-label">K&euml;ng&euml;tari</label>
                <input type="text" name="kengtari" id="term" class="form-control shadow-sm rounded-5"
                  placeholder="Emri i k&euml;ng&euml;tarit" required>
                <script type="text/javascript">
                  $("#term").autocomplete({
                    source: 'ajax-db-search.php',
                  });
                </script>
              </div>
              <div class="col">
                <label for="emri" class="form-label">Emri i k&euml;nges</label>
                <input type="text" name="emri" class="form-control shadow-sm rounded-5" placeholder="Emri i k&euml;nges"
                  autocomplete="off" required>
              </div>
            </div>
            <div class="form-group row">
              <div class="col">
                <label for="dk" class="form-label">Tekst Shkrues</label>
                <input type="text" name="teksti" class="form-control shadow-sm rounded-5" placeholder="Tekst Shkrues"
                  autocomplete="off">
              </div>
              <div class="col">
                <label for="dks" class="form-label">Muzika</label>
                <input type="text" name="muzika" class="form-control shadow-sm rounded-5" placeholder="Muzika"
                  autocomplete="off">
              </div>
            </div>
            <div class="form-group row">
              <div class="col">
                <label for="tel" class="form-label">Orkestra</label>
                <input type="text" name="orkestra" class="form-control shadow-sm rounded-5" placeholder="Orkestra"
                  autocomplete="off">
              </div>
              <div class="col">
                <label for="tel" class="form-label">Co</label>
                <input type="text" name="co" class="form-control shadow-sm rounded-5" placeholder="Co"
                  autocomplete="off">
              </div>
            </div>
            <hr />
            <div class="form-group row">
              <div class="col">
                <label for="tel" class="form-label">Cover / Origjinale </label><br>
                <input type="radio" id="html" name="cover" value="Cover" class="form-check-input">
                <label for="html">Cover</label>
                <input type="radio" id="css" name="cover" value="Origjinale" class="form-check-input">
                <label for="css">Origjinale</label>
                <input type="radio" id="css" name="cover" value="Potpuri" class="form-check-input">
                <label for="css">Potpuri</label><br>
              </div>
              <div class="col">
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
                <input type="text" name="veper" class="tcal form-control shadow-sm rounded-5 w-100"
                  placeholder="Veper nga Koha" value="" autocomplete="off">
              </div>
              <div class="col">
                <label for="imei" class="form-label">Klienti </label>
                <select class="form-select shadow-sm rounded-5" data-live-search="true" name="klienti" required>
                  <?php
                  $mads = $conn->query("SELECT * FROM klientet");
                  while ($ads = mysqli_fetch_array($mads)) {
                    ?>
                    <option value="<?php echo $ads['id']; ?>"><?php echo $ads['emri']; ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group row">
              </div>
              <div class="col">
                <label for="platforma" class="form-label">Platforma</label>
                <input type="text" class="form-control shadow-sm rounded-5" name="platforma" value="YouTube">
              </div>
              <div class="col">
                <label for="imei" class="form-label">Platformat tjera <small>(Mbaj shtypur CTRL)</small> </label>
                <select multiple class="form-select shadow-sm rounded-5" name="platformat[]"
                  id="exampleFormControlSelect2" style="height:fit-content">
                  <option value="Spotify" selected="selected"> Spotify</option>
                  <option value="Youtube Music" selected="selected">YouTube Music</option>
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
                <label for="info" class="form-label">Linku i k&euml;ng&euml;s</label>
                <input type="url" name="linku" class="form-control shadow-sm rounded-5" placeholder="Linku"
                  autocomplete="off">
              </div>
              <div class="col">
                <label for="info" class="form-label">Linku Platformave</label><br>
                <input type="url" name="linkuplat" class="form-control shadow-sm rounded-5"
                  placeholder="Linku platformave" autocomplete="off">
              </div>
            </div>
            <div class="form-group row">
              <div class="col">
                <label for="imei" class="form-label">Data</label>
                <input type="text" name="data" class="tcal form-control w-100" value="<?php echo date("Y-m-d"); ?>">
              </div>
              <div class="col">
                <label for="imei" class="form-label">Gjuha</label>
                <select name="gjuha" class="form-select   shadow-sm rounded-5">
                  <option value="Shqip" selected="">Shqip (E parazgjedhur)</option>
                  <option value="English">English</option>
                  <option value="German">German</option>
                </select>
              </div>
            </div>
            <div class="col">
              <label for="simpleMde" class="form-label">Info shtes&euml; </label>
              <textarea id="simpleMde" name="infosh" placeholder="Info shtes&euml;"
                class="form-control shadow-sm rounded-5"></textarea>
            </div>
            <button type="submit" class="btn btn-primary shadow-sm rounded-5 mt-3 text-white" name="ruaj"><i
                class="ti-save"></i> Ruaj</button>
        </div>
        </form>
      </div>
    </div>
  </div>
  <?php include 'partials/footer.php'; ?>