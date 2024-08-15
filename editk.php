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
    $email_kontablist = mysqli_real_escape_string($conn, $_POST['email_kontablist']);
    $emailp = mysqli_real_escape_string($conn, $_POST['emailp']);
    $ig = mysqli_real_escape_string($conn, $_POST['ig']);
    $adresa = mysqli_real_escape_string($conn, $_POST['adresa']);
    $kategoria = mysqli_real_escape_string($conn, $_POST['kategoria']);
    $emriart = mysqli_real_escape_string($conn, $_POST['emriart']);
    $nrllog = mysqli_real_escape_string($conn, $_POST['nrllog']);
    $bank_info = mysqli_real_escape_string($conn, $_POST['bank_info']);
    $perdoruesi = mysqli_real_escape_string($conn, $_POST['perdoruesi']);
    $fjalekalimi = md5(mysqli_real_escape_string($conn, $_POST['fjalkalimi']));
    $shtetsia = mysqli_real_escape_string($conn, $_POST['shtetsia']);
    // Convert the array to a comma-separated string
    $emails = isset($_POST['emails']) ? implode(',', $_POST['emails']) : '';
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
    $type__of_client = mysqli_real_escape_string($conn, $_POST['type_of_client']);
    // Update the database with the new data
    if ($conn->query("UPDATE klientet SET emri='$emri', np='$np', monetizuar='$mon', emails='$emails', dk='$dk', dks='$dks', youtube='$yt', info='$info', perqindja='$perq', perqindja2='$perq2', fb='$fb', ig='$ig', adresa='$adresa', kategoria='$kategoria', nrtel='$nrtel', emailp='$emailp', emailadd='$emailadd', emriart='$emriart', nrllog='$nrllog',  bank_name='$bank_info', ads='$adsa', perdoruesi='$perdoruesi', perqindja_check='$perqindja_check', perqindja_platformave_check='$perqindja_e_platformave_check', fjalkalimi='$fjalekalimi', shtetsia='$shtetsia', lloji_klientit='$type__of_client', email_kontablist = '$email_kontablist' WHERE id='$editid'")) {
        echo '<script>
            Swal.fire({
              icon: "success",
              title: "Të dhënat u perditësuan me sukses",
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
$editcl = mysqli_fetch_array($conn->query("SELECT * FROM klientet WHERE id='$editid'"));
// Retrieve the contract start date
$contractStartDate = mysqli_fetch_array($conn->query("SELECT * FROM kontrata_gjenerale WHERE youtube_id='$editcl[youtube]'"));
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" style="width:fit-content;border-style:1px solid black;"
                aria-label="breadcrumb">
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
                            Edito klientit <?php echo $editcl['emri'] ?>
                            <?php echo $filename ?>
                        </a>
                    </li>
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
                                    <button class="accordion-button border-0 rounded-5" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true"
                                        aria-controls="collapseOne">
                                        <i class="fi fi-rr-user me-5"></i> Të dhënat e klientit
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show "
                                    aria-labelledby="headingOne" data-bs-parent="#client-infos-accordion">
                                    <div class="accordion-body border-0">
                                        <div class="col">
                                            <label class="form-label" for="emri">Emri dhe mbiemri</label>
                                            <input type="text" name="emri"
                                                class="form-control rounded-5 border border-2"
                                                placeholder="Shëno emrin dhe mbiemrin e klientit"
                                                value="<?php echo $editcl['emri']; ?>">
                                        </div>
                                        <br>
                                        <div class="col">
                                            <label class="form-label" for="emri">ID e dokumentit personal</label>
                                            <input type="text" name="np" id="emriart"
                                                class="form-control rounded-5 border border-2"
                                                placeholder="Shëno ID e dokumentit"
                                                value="<?php echo $editcl['np']; ?>">
                                        </div>
                                        <br>
                                        <div class="col">
                                            <label class="form-label" for="yt">Adresa</label>
                                            <input type="text" name="adresa"
                                                class="form-control rounded-5 border border-2"
                                                value="<?php echo $editcl['adresa']; ?>" placeholder="Adresa">
                                        </div>
                                        <br>
                                        <div class="col">
                                            <label class="form-label" for="yt">Adresa elektronike ( Email )</label>
                                            <input type="text" name="emailadd"
                                                class="form-control rounded-5 border border-2"
                                                value="<?php echo $editcl['emailadd']; ?>"
                                                placeholder="Shëno emailin e klientit">
                                        </div>
                                        <br>
                                        <div class="col">
                                            <label class="form-label" for="yt">Adresa elektronike e kontablistit ( Email
                                                )</label>
                                            <input type="text" name="email_kontablist"
                                                class="form-control rounded-5 border border-2" id="email_kontablist"
                                                value="<?php echo $editcl['email_kontablist']; ?>"
                                                placeholder="Shëno email-in e platformave te klientit">
                                        </div>
                                        <br>
                                        <div class="col">
                                            <label class="form-label" for="yt">Numri i telefonit</label>
                                            <input type="text" name="nrtel"
                                                class="form-control rounded-5 border border-2"
                                                value="<?php echo $editcl['nrtel']; ?>"
                                                placeholder="Shëno numrin e telefonit të klientit">
                                        </div>
                                        <br>
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
                                            <select id="bank_info" name="bank_info"
                                                class="form-select rounded-5 border border-2 py-2">
                                                <option value="custom">Shto emrin e personalizuar të bankës</option>
                                                <!-- Add this option -->
                                                <option value="<?php echo $defaultBank; ?>" selected>
                                                    <?php echo $defaultBank; ?></option>
                                                <option value="Banka KombetareTregtare">Banka Kombëtare Tregtare
                                                    (Albania)</option>
                                                <option value="Banka Per Biznes">Banka për Biznes (Kosovo)</option>
                                                <option value="NLB Komercijalna Banka">NLB Komercijalna banka (Slovenia)
                                                </option>
                                                <option value="NLB Banka">NLB Banka (Slovenia)</option>
                                                <option value="Pro Credit Bank">ProCredit Bank (Germany)</option>
                                                <option value="Raiffeisen Bank Kosovo">Raiffeisen Bank Kosovo (Austria)
                                                </option>
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
                                                                var selectElement = document.getElementById(
                                                                    'bank_info');
                                                                var customOption = document.createElement(
                                                                    'option');
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
                                            <label class="form-label" for="yt">Numri i xhirollogarisë</label>
                                            <input type="text" name="nrllog"
                                                class="form-control rounded-5 border border-2"
                                                placeholder="Shëno numrin e xhirollogarisë të klientit"
                                                value="<?php echo $editcl['nrllog']; ?>">
                                        </div>
                                        <br>
                                        <div class="col">
                                            <label class="form-label" for="type_of_client">Lloji i klientit</label>
                                            <br>
                                            <span style="font-size: 12px;" class="text-muted mb-3">Statusi aktual i
                                                llojit te klientit : <?php echo $editcl['lloji_klientit']; ?></span>
                                            <hr>
                                            <?php // Retrieve the data for editing
                                            $type_of_client = $editcl['lloji_klientit'];
                                            ?>
                                            <select id="type_of_client" name="type_of_client"
                                                class="form-select rounded-5 border border-2">
                                                <option value="Personal"
                                                    <?php if ($type_of_client == 'Personal') echo ' selected'; ?>>
                                                    Personal</option>
                                                <option value="Biznes"
                                                    <?php if ($type_of_client == 'Biznes') echo ' selected'; ?>>Biznes
                                                </option>
                                            </select>
                                            <script>
                                                new Selectr('#type_of_client', {
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
                        <div class="accordion" id="client-infos-youtube-accordion">
                            <div class="accordion-item border-1 rounded-5">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button border-0 rounded-5" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#ciya" aria-expanded="true"
                                        aria-controls="ciya">
                                        <i class="fi fi-brands-youtube me-5"></i> Të dhënat e klientit ( Youtube )
                                    </button>
                                </h2>
                                <div id="ciya" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                                    data-bs-parent="#client-infos-youtube-accordion">
                                    <div class="accordion-body">
                                        <div class="col">
                                            <label class="form-label" for="yt">ID e kanalit në platformën
                                                Youtube</label>
                                            <input type="text" name="yt" class="form-control rounded-5 border border-2"
                                                placeholder="Shëno ID e kanalit"
                                                value="<?php echo $editcl['youtube']; ?>">
                                        </div>
                                        <br>
                                        <div class="col">
                                            <label class="form-label" for="emri">Emri artistik</label>
                                            <input type="text" name="emriart" id="emriart"
                                                class="form-control rounded-5 border border-2"
                                                placeholder="Shëno emrin artistik"
                                                value="<?php echo $editcl['emriart']; ?>">
                                        </div>
                                        <br>
                                        <div class="col">
                                            <label class="form-label" for="dk">Data e fillimit të kontratës</label>
                                            <input type="date" name="dk" id="dk"
                                                class="form-control rounded-5 border border-2"
                                                placeholder="Shkruaj Daten e kontrates"
                                                value="<?php
                                                        echo $contractStartDate['data_e_krijimit']; ?>">
                                        </div>
                                        <br>
                                        <div class="col">
                                            <label class="form-label" for="dks">Data e skadimit të kontratës</label>
                                            <?php
                                            // Perform calculations for expiration date
                                            $startDate = new DateTime($contractStartDate['data_e_krijimit']);
                                            $durationMonths = $contractStartDate['kohezgjatja'];
                                            $expirationDate = clone $startDate;
                                            $expirationDate->modify("+ $durationMonths months");
                                            ?>
                                            <input type="date" name="dks" id="dks"
                                                class="form-control rounded-5 border border-2"
                                                placeholder="Shkruaj Daten e skaditimit"
                                                value="<?php echo $expirationDate->format('Y-m-d'); ?>">
                                        </div>
                                        <br>
                                        <div class="col">
                                            <label class="form-label" for="yt">Zgjedh kategorinë</label>
                                            <select class="form-select border border-2 rounded-5 w-100" name="kategoria"
                                                id="kategoria">
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
                                            <label class="form-label" for="tel">Monetizuar ? </label><br>
                                            <?php if ($editcl['monetizuar'] == "PO") {
                                            ?>
                                                <input type="radio" id="html" name="min" value="PO" checked
                                                    class="form-check-input">
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
                                                <input type="radio" id="html" name="min" value="PO"
                                                    class="form-check-input">
                                                  <label class="form-check-label text-muted" for="html">PO</label>
                                                  <input type="radio" id="css" name="min" value="JO" checked
                                                    class="form-check-input">
                                                  <label class="form-check-label text-success" for="css">JO ( aktive
                                                    )</label><br>
                                            <?php } ?>
                                        </div>
                                        <br>
                                        <div class="col">
                                            <label class="form-label" for="imei">Edito llogarinë e ADS: </label>
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
                                                                                                } ?>><?php echo $ads['email']; ?> |
                                                        <?php echo $ads['adsid']; ?> (<?php echo $ads['shteti']; ?>)
                                                    </option>
                                                <?php } ?>
                                                <?php if (!$ads_found && !empty($editcl['emri'])) {
                                                    $guse = $conn->query("SELECT * FROM klientet WHERE emri='" . $editcl['emri'] . "'");
                                                    $guse2 = mysqli_fetch_array($guse);
                                                    $adsid = $guse2['ads'];
                                                    $mads = $conn->query("SELECT * FROM ads WHERE id='$adsid'");
                                                    $ads = mysqli_fetch_array($mads);
                                                ?>
                                                    <option value="<?php echo $ads['id']; ?>" selected>
                                                        <?php echo $ads['email']; ?> | <?php echo $ads['adsid']; ?>
                                                        (<?php echo $ads['shteti']; ?>)</option>
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
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="accordion" id="perqindja-accordion">
                            <div class="accordion-item border-1 rounded-5">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button border-0 rounded-5" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#pdp" aria-expanded="true"
                                        aria-controls="pdp">
                                        <i class="fi fi-rr-world me-5"></i> Përqindja dhe platformat
                                    </button>
                                </h2>
                                <div id="pdp" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                                    data-bs-parent="#perqindja-accordion">
                                    <div class="accordion-body">
                                        <div class="col">
                                            <label class="form-label" for="yt">Adresa elektronike ( Email ) per
                                                platforma</label>
                                            <input type="text" name="emailp"
                                                class="form-control rounded-5 border border-2"
                                                value="<?php echo $editcl['emailp']; ?>"
                                                placeholder="Shëno email-in e platformave te klientit">
                                        </div>
                                        <br>

                                        <div class="col">
                                            <label for="perqindja" class="form-label">Përqindja ( Baresha )</label>
                                            <input type="text" name="perqindja"
                                                class="form-control rounded-5 border border-2"
                                                value="<?php echo $editcl['perqindja']; ?>" placeholder="0.00%">
                                        </div>
                                        <br>
                                        <div class="col">
                                            <label for="perqindja" class="form-label">Përqindja për platformat tjera (
                                                Baresha )</label>
                                            <input type="text" name="perqindja2"
                                                class="form-control rounded-5 border border-2"
                                                value="<?php echo $editcl['perqindja2']; ?>" placeholder="0.00%">
                                        </div>
                                        <br>
                                        <div class="col">
                                            <label for="perqindja" class="form-label">Perqindja e klientit</label>
                                            <div class="input-group flex-nowrap">
                                                <div class="input-group-text bg-transparent border-0"
                                                    id="addon-wrapping">
                                                    <input type="checkbox" name="perqindja_check" id="perqindja_check" <?php if ($editcl['perqindja_check'] === "1") {
                                                                                                                            echo "checked";
                                                                                                                        }  ?>>
                                                </div>
                                                <?php
                                                if (isset($editcl['perqindja']) && is_numeric($editcl['perqindja'])) {
                                                    $perqindja_value = 100 - $editcl['perqindja'];
                                                } else {
                                                    $perqindja_value = 'Error: Invalid or missing Perqindja2 value.';
                                                }
                                                ?>
                                                <input type="text" class="form-control rounded-5 border border-2"
                                                    id="perqindja_platformave_output" name="perqindja_e_klientit"
                                                    disabled value="<?php echo $perqindja_value; ?>">
                                            </div>
                                            <br>
                                            <?php
                                            if ($editcl['perqindja_check'] === "1") {
                                                echo '<div id="perqindja_platformave_message" style="color: green;">Statusi i kesaj perqindje ne sistemin e klientit eshte e vrojtueshme.</div>';
                                            } else {
                                                echo '<div id="perqindja_platformave_message" style="color: red;">JO.</div>';
                                            }
                                            ?>
                                        </div>
                                        <br>
                                        <div class="col">
                                            <label for="perqindja" class="form-label">Perqindja e platformave për
                                                klientin</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-text bg-transparent border-0">
                                                    <input type="checkbox" name="perqindja_platformave_check"
                                                        id="perqindja_platformave_check"
                                                        <?php if ($editcl['perqindja_platformave_check'] === "1") {
                                                            echo "checked";
                                                        }  ?>>
                                                </div>
                                                <?php
                                                if (isset($editcl['perqindja2']) && is_numeric($editcl['perqindja2'])) {
                                                    $perqindja2_value = 100 - $editcl['perqindja2'];
                                                } else {
                                                    $perqindja2_value = 'Error: Invalid or missing Perqindja2 value.';
                                                }
                                                ?>
                                                <input type="text" class="form-control rounded-5 border border-2"
                                                    id="perqindja_platformave_output" name="perqindja_e_klientit"
                                                    disabled value="<?php echo $perqindja2_value; ?>">
                                            </div>
                                            <?php
                                            if ($editcl['perqindja_platformave_check'] === "1") {
                                                echo '<div id="perqindja_platformave_message" style="color: green;">Statusi i kesaj perqindje ne sistemin e klientit eshte e vrojtueshme.</div>';
                                            } else {
                                                echo '<div id="perqindja_platformave_message" style="color: red;">JO.</div>';
                                            }
                                            ?>
                                        </div>
                                        <br>
                                        <div class="col">
                                            <label class="form-label"><i class="ti-facebook"></i> Facebook URL:</label>
                                            <input type="url" name="fb" class="form-control rounded-5 border border-2"
                                                placeholder="https://facebook.com/...."
                                                value="<?php echo $editcl['fb']; ?>">
                                        </div>
                                        <br>
                                        <div class="col">
                                            <label class="form-label"><i class="ti-instagram"></i> Instagram
                                                URL:</label>
                                            <input type="url" name="ig" class="form-control rounded-5 border border-2"
                                                placeholder="https://instagram.com/...."
                                                value="<?php echo $editcl['ig']; ?>">
                                        </div>
                                        <div class="col">
                                            <a href="perqindjet_klient.php?id=<?php echo $editcl['id']; ?>"
                                                class="btn btn-primary mt-4">Ndrysho përqindjet</a>
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
                                    <button class="accordion-button border-0 rounded-5" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#iib" aria-expanded="true"
                                        aria-controls="iib">
                                        <i class="fi fi-rr-info me-5"></i> Informacion i brendshëm
                                    </button>
                                </h2>
                                <div id="iib" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                                    data-bs-parent="#information-accordion">
                                    <div class="accordion-body">
                                        <div class="col">
                                            <label class="form-label" for="imei">Ngarko kontrat&euml;n:</label>
                                            <div class="file-upload-wrapper">
                                                <input type="file" name="tipi"
                                                    class="fileuploader form-control border border-2 rounded-5" />
                                            </div>
                                        </div>
                                        <br>
                                        <div class="col">
                                            <label class="form-label" for="imei">Emails</label>
                                            <select multiple class="form-control border border-2 rounded-5"
                                                name="emails[]" id="exampleFormControlSelect3">
                                                <?php
                                                // Fetch all distinct emails from both klientet and ads tables
                                                $getemails_klientet = $conn->query("SELECT emails FROM klientet WHERE id = '$editid'");
                                                $emails_with_access = [];
                                                if ($maillist = mysqli_fetch_array($getemails_klientet)) {
                                                    // Split the emails string into an array of individual email addresses
                                                    $emails_with_access = explode(',', $maillist['emails']);
                                                    $emails_with_access = array_map('trim', $emails_with_access); // Remove leading/trailing whitespace
                                                }
                                                $getemails_ads = $conn->query("SELECT DISTINCT email FROM ads");
                                                while ($row = $getemails_ads->fetch_assoc()) {
                                                    $email_ads = $row['email']; // Retrieve the email from the fetched row
                                                    // Check if this email is not in the list of emails with access
                                                    if (!in_array($email_ads, $emails_with_access)) {
                                                        // Email without access
                                                ?>
                                                        <option value="<?php echo $email_ads; ?>"><?php echo $email_ads; ?> (nuk
                                                            ka akses)</option>
                                                    <?php } else {
                                                        // Email with access
                                                    ?>
                                                        <option value="<?php echo $email_ads; ?>" selected>
                                                            <?php echo $email_ads; ?> (ka akses)</option>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <script>
                                            new Selectr('#exampleFormControlSelect3', {
                                                multiple: true,
                                                searchable: true,
                                                width: 300
                                            })
                                        </script>
                                        <br>
                                        <div class="col">
                                            <label class="form-label">P&euml;rdoruesi <small>(Sistemit)</small>:</label>
                                            <input type="text" name="perdoruesi"
                                                class="form-control border border-2 rounded-5"
                                                placeholder="P&euml;rdoruesi i sistemit"
                                                value="<?php echo $editcl['perdoruesi']; ?>">
                                        </div>
                                        <br>
                                        <div class="col">
                                            <label class="form-label">Fjalekalimi <small>(Sistemit)</small>:</label>
                                            <input type="password" name="fjalkalimi"
                                                class="form-control border border-2 rounded-5"
                                                placeholder="Fjalkalimi i sistemit"
                                                value="<?php echo $editcl['fjalkalimi']; ?>">
                                            <button type="button" class="input-custom-css px-3 py-2 rounded-5 mt-1"
                                                data-bs-toggle="modal" data-bs-target="#passwordInfoModal">Info</button>
                                            <!-- Create a modal for displaying password reset information -->
                                            <div class="modal fade" id="passwordInfoModal" tabindex="-1" role="dialog"
                                                aria-labelledby="passwordInfoModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="passwordInfoModalLabel">
                                                                Informacione për rivendosjen e fjalëkalimit</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>
                                                                Ky është fjalëkalimi i enkriptuar për klientin :
                                                                <?php echo $editcl['emri'] ?>
                                                            </p>
                                                            <p>
                                                                Fjalëkalimi :
                                                                <strong><?php echo $editcl['fjalkalimi']; ?> </strong>
                                                            </p>
                                                            <p>
                                                                Fjalëkalimi i hash MD5 në formën e tij të deshifruar.
                                                                Sidoqoftë, MD5 është një funksion hash me një drejtim,
                                                                që do të thotë se nuk mund të deshifrohet drejtpërdrejt
                                                                sepse është projektuar të jetë i pakthyeshëm.
                                                            </p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="input-custom-css px-3 py-2"
                                                                data-bs-dismiss="modal">Mbylle</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="col">
                                            <label class="form-label">Shtetsia</label>
                                            <select class="form-select border border-2 rounded-5" name="shtetsia" id="shtetsia">
                                                <?php
                                                // Static options
                                                $staticOptions = [
                                                    "Shqipëri",
                                                    "Kosovë",
                                                    "Gjermania",
                                                    "Italia",
                                                    "Zvicër",
                                                    "Maqedonia",
                                                    "Mali i zi"
                                                ];

                                                // Fetch distinct shtetsia values from the database
                                                $query = "SELECT DISTINCT shtetsia FROM klientet WHERE id = '$editcl[id]'";
                                                $result = $conn->query($query);

                                                // Merge static and dynamic options
                                                $options = array_unique(array_merge($staticOptions, $result ? array_column($result->fetch_all(MYSQLI_ASSOC), 'shtetsia') : []));

                                                // Output options
                                                foreach ($options as $shtetsia): ?>
                                                    <option value="<?php echo htmlspecialchars($shtetsia); ?>" <?php echo (isset($editcl['shtetsia']) && $editcl['shtetsia'] == $shtetsia) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($shtetsia); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <script>
                                                new Selectr('#shtetsia', {

                                                })
                                            </script>
                                        </div>

                                        <br>
                                        <div class="col">
                                            <label class="form-label" for="info"> Info Shtes&euml;</label>
                                            <textarea class="form-control rounded-5 border border-2" id="simpleMde"
                                                name="info"
                                                placeholder="Info Shtes&euml;"><?php echo $editcl['info']; ?></textarea>
                                        </div>
                                        <hr>
                                        <div class="col d-flex justify-content-center align-items-center">
                                            <button type="submit"
                                                class="btn btn-secondary rounded-5 text-white shadow mb-3"
                                                style="text-transform:none;" name="ndrysho">
                                                <i class="ti-save"></i>
                                                <i class="fi fi-rr-edit me-2"></i>
                                                Përditso të gjitha informacionet
                                            </button>
                                            <br>
                                        </div>
                                        <div class="col d-flex justify-content-center align-items-center">
                                            <a href="kanal.php?kid=<?php echo $editcl['id']; ?>"
                                                class="btn btn-danger rounded-5 text-white shadow mb-3"
                                                style="text-transform:none;" name="ndrysho">
                                                <i class="ti-save"></i>
                                                <i class="fi fi-rr-eye me-2"></i>
                                                Shiko kanalin
                                            </a>
                                            <br>
                                        </div>
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
</div>
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