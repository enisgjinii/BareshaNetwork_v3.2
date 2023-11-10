<?php ob_start();
include 'partials/header.php'; ?>
<?php
// Include your database connection file
include 'conn-d.php';

// Start output buffering


// Get the ID from the URL
$id = $_GET['id'];

// Fetch the record from the database
$query = "SELECT * FROM kontrata_gjenerale WHERE id = $id";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Get the updated values from the form
    $emri = $_POST['emri'];
    $mbiemri = $_POST['mbiemri'];
    $tvsh = $_POST['tvsh'];
    $numri_personal = $_POST['numri_personal'];
    $pronari_xhirollogarise = $_POST['pronari_xhirollogarise'];
    $numri_xhirollogarise = $_POST['numri_xhirollogarise'];
    $kodi_swift = $_POST['kodi_swift'];
    $iban = $_POST['iban'];
    $emri_bankes = $_POST['emri_bankes'];
    $adresa_bankes = $_POST['adresa_bankes'];
    $kohezgjatja = $_POST['kohezgjatja'];

    // Update the record in the database
    $query = "UPDATE kontrata_gjenerale SET emri = '$emri', mbiemri = '$mbiemri', tvsh = '$tvsh', numri_personal = '$numri_personal', pronari_xhirollogarise = '$pronari_xhirollogarise', numri_xhirollogarise = '$numri_xhirollogarise', kodi_swift = '$kodi_swift', iban = '$iban', emri_bankes = '$emri_bankes', adresa_bankes = '$adresa_bankes' , kohezgjatja = '$kohezgjatja' WHERE id = $id";

    mysqli_query($conn, $query);

    // Disable caching
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Redirect to the same page to reflect the updated values
    header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id);
    exit;
}

// Flush the output buffer and turn off output buffering
ob_flush();
ob_end_clean();
?>


<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="p-5 mb-4 card shadow-sm rounded-5">
                <h1 class="card-title" style="text-transform:none;">Modifiko t&euml; dh&euml;nat te cilat do te shfaqen ne kontrat&euml;</h1>
                <form action="" method="post">
                    <div class="row">
                        <div class="col">
                            <label for="emri" class="form-label">Emri</label>
                            <input type="text" class="form-control shadow-sm rounded-5" name="emri" id="emri" value="<?php echo $row['emri']; ?>">
                        </div>
                        <div class="col">
                            <label for="mbiemri" class="form-label">Mbiemri</label>
                            <input type="text" class="form-control shadow-sm rounded-5" name="mbiemri" id="mbiemri" value="<?php echo $row['mbiemri']; ?>">
                        </div>

                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <label for="tvsh" class="form-label">P&euml;rqindja</label>
                            <input type="text" class="form-control shadow-sm rounded-5" name="tvsh" id="tvsh" value="<?php echo $row['tvsh']; ?>">
                        </div>
                        <div class="col">
                            <label for="numri_personal" class="form-label">Numri personal</label>
                            <input type="text" class="form-control shadow-sm rounded-5" name="numri_personal" id="numri_personal" value="<?php echo $row['numri_personal']; ?>">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="pronari_xhirollogarise" class="form-label">Pronari i xhirollogaris&euml; bankare</label>
                            <input type="text" class="form-control shadow-sm rounded-5" name="pronari_xhirollogarise" id="pronari_xhirollogarise" value="<?php echo $row['pronari_xhirollogarise']; ?>">
                        </div>
                        <div class="col">
                            <label for="numri_xhirollogarise" class="form-label">Numri i xhirollogaris&euml; bankare</label>
                            <input type="text" class="form-control shadow-sm rounded-5" name="numri_xhirollogarise" id="numri_xhirollogarise" value="<?php echo $row['numri_xhirollogarise']; ?>">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="kodi_swift" class="form-label">Kodi SWIFT</label>
                            <input type="text" class="form-control shadow-sm rounded-5" name="kodi_swift" id="kodi_swift" value="<?php echo $row['kodi_swift']; ?>">
                        </div>
                        <div class="col">
                            <label for="iban" class="form-label">IBAN</label>
                            <input type="text" class="form-control shadow-sm rounded-5" name="iban" id="iban" value="<?php echo $row['iban']; ?>">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="emri_bankes" class="form-label">Emri i bank&euml;s</label>
                            <input type="text" class="form-control shadow-sm rounded-5" name="emri_bankes" id="emri_bankes" value="<?php echo $row['emri_bankes']; ?>">
                        </div>
                        <div class="col">
                            <label for="adresa_bankes" class="form-label">Adresa e bank&euml;s</label>
                            <input type="text" class="form-control shadow-sm rounded-5" name="adresa_bankes" id="adresa_bankes" value="<?php echo $row['adresa_bankes']; ?>">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-6">
                            <label for="kohezgjatja" class="form-label">Koh&euml;zgjatja n&euml; muaj</label>
                            <input type="text" class="form-control shadow-sm rounded-5" name="kohezgjatja" id="kohezgjatja" value="<?php echo $row['kohezgjatja']; ?>">
                        </div>
                    </div>


                    <input type="submit" class="btn btn-primary text-white shadow-sm rounded-5 mt-4" name="submit" style="text-transform:none;" value="P&euml;rditso">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('partials/footer.php') ?>