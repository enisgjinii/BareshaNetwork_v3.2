<?php
ob_start();
include 'partials/header.php';

// Ensure the connection variable $conn is available
// You might need to include your database connection script here if not already included in 'partials/header.php'

// Get the 'id' parameter from the URL and sanitize it
$editid = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Retrieve the data for editing
$editQuery = "SELECT * FROM klientet WHERE id='$editid'";
$editResult = $conn->query($editQuery);

if ($editResult && $editResult->num_rows > 0) {
    $editcl = mysqli_fetch_assoc($editResult);
} else {
    // Handle case where client is not found
    echo '<script>
            Swal.fire({
              icon: "error",
              title: "Gabim",
              text: "Klienti nuk u gjet.",
              showConfirmButton: true
            }).then(() => {
              window.location.href = "klient.php";
            });
          </script>';
    exit();
}

// Initialize $contractStartDate as an empty array
$contractStartDate = array();

// Retrieve the contract start date
$contractStartDateResult = $conn->query("SELECT * FROM kontrata_gjenerale WHERE youtube_id='{$editcl['youtube']}'");
if ($contractStartDateResult && $contractStartDateResult->num_rows > 0) {
    $contractStartDate = mysqli_fetch_assoc($contractStartDateResult);
    // Now proceed with calculations
    if (isset($contractStartDate['data_e_krijimit']) && !empty($contractStartDate['data_e_krijimit'])) {
        $startDate = new DateTime($contractStartDate['data_e_krijimit']);
        $durationMonths = isset($contractStartDate['kohezgjatja']) ? $contractStartDate['kohezgjatja'] : 0;
        if (!empty($durationMonths)) {
            $expirationDate = clone $startDate;
            $expirationDate->modify("+$durationMonths months");
            $expirationDateFormatted = $expirationDate->format('Y-m-d');
        } else {
            // Handle case where 'kohezgjatja' is empty
            $expirationDateFormatted = '';
        }
    } else {
        // Handle case where 'data_e_krijimit' is empty
        $expirationDateFormatted = '';
    }
} else {
    // No contract found; handle accordingly
    $expirationDateFormatted = '';
}

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
    $fjalekalimi = md5(mysqli_real_escape_string($conn, $_POST['fjalkalimi'])); // Consider using password_hash instead
    $shtetsia = mysqli_real_escape_string($conn, $_POST['shtetsia']);
    $shtetsiaKontabiliteti = mysqli_real_escape_string($conn, $_POST['shtetsiaKontabiliteti']);

    // Convert the array to a comma-separated string
    $emails = isset($_POST['emails']) ? implode(',', array_map('trim', $_POST['emails'])) : '';

    // Checkbox values
    $perqindja_check = isset($_POST['perqindja_check']) ? '1' : '0';
    $perqindja_e_platformave_check = isset($_POST['perqindja_platformave_check']) ? '1' : '0';

    // File upload handling
    $uploadedFileName = $editcl['kontrata']; // Existing file name from database
    $targetfolder = "dokument/"; // Ensure this folder exists and is writable

    if (isset($_FILES['tipi']) && $_FILES['tipi']['error'] == UPLOAD_ERR_OK) {
        // Get the file type
        $file_type = $_FILES['tipi']['type'];
        // Check if the file type is PDF
        if ($file_type == "application/pdf") {
            // Generate a unique filename to prevent overwriting
            $uniqueFileName = uniqid('contract_', true) . '.pdf';
            $targetPath = $targetfolder . basename($uniqueFileName);
            // Attempt to move the uploaded file to the target folder
            if (move_uploaded_file($_FILES['tipi']['tmp_name'], $targetPath)) {
                // Update the filename to store in the database
                $uploadedFileName = $uniqueFileName;
                echo '<script>
                    Swal.fire({
                      icon: "success",
                      title: "Skedari u ngarkua me sukses.",
                      showConfirmButton: false,
                      timer: 1500
                    });
                  </script>';
            } else {
                echo '<script>
                    Swal.fire({
                      icon: "error",
                      title: "Na vjen keq, ka pasur një gabim gjatë ngarkimit të skedarit tuaj.",
                      showConfirmButton: false,
                      timer: 1500
                    });
                  </script>';
            }
        } else {
            echo '<script>
                Swal.fire({
                  icon: "error",
                  title: "Na vjen keq, lejohen vetëm skedarët PDF.",
                  showConfirmButton: false,
                  timer: 1500
                });
              </script>';
        }
    }

    // Sanitize and retrieve the 'nrtel' field
    $nrtel = mysqli_real_escape_string($conn, $_POST['nrtel']);
    $type__of_client = mysqli_real_escape_string($conn, $_POST['type_of_client']);
    $statusi_i_kontrates = mysqli_real_escape_string($conn, $_POST['statusi_i_kontrates'] ?? '');

    // Update the database with the new data, including the uploaded file name
    $updateQuery = "UPDATE klientet SET 
        emri='$emri', 
        np='$np', 
        monetizuar='$mon', 
        emails='$emails', 
        dk='$dk', 
        dks='$dks', 
        youtube='$yt', 
        info='$info', 
        perqindja='$perq', 
        perqindja2='$perq2', 
        fb='$fb', 
        ig='$ig', 
        adresa='$adresa', 
        kategoria='$kategoria', 
        nrtel='$nrtel', 
        emailp='$emailp', 
        emailadd='$emailadd', 
        emriart='$emriart', 
        nrllog='$nrllog',  
        bank_name='$bank_info', 
        ads='$adsa', 
        perdoruesi='$perdoruesi', 
        perqindja_check='$perqindja_check', 
        perqindja_platformave_check='$perqindja_e_platformave_check', 
        fjalkalimi='$fjalekalimi', 
        shtetsia='$shtetsia', 
        lloji_klientit='$type__of_client', 
        statusi_i_kontrates='$statusi_i_kontrates', 
        email_kontablist='$email_kontablist', 
        shtetsiaKontabiliteti='$shtetsiaKontabiliteti'
        WHERE id='$editid'";

    if ($conn->query($updateQuery)) {
        echo '<script>
            Swal.fire({
              icon: "success",
              title: "Të dhënat u përditësuan me sukses",
              showConfirmButton: false,
              timer: 1500
            });
          </script>';
        // Prepare data for insertion into logs
        // Ensure $user_info is defined and contains 'givenName' and 'familyName'
        // If not, adjust accordingly
        $user_informations = isset($user_info) ? $user_info['givenName'] . ' ' . $user_info['familyName'] : 'Stafi i Panjohur';
        $log_description = $user_informations . " ka ndryshuar klientin " . $emri;
        $date_information = date('Y-m-d H:i:s');
        // Prepare the INSERT statement
        $stmt = $conn->prepare("INSERT INTO logs (stafi, ndryshimi, koha) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $user_informations, $log_description, $date_information);
        if ($stmt->execute()) {
            // Log inserted successfully
        } else {
            echo '<script>
              Swal.fire({
                icon: "error",
                title: "Gabim gjatë regjistrimit të log-ut: ' . $conn->error . '",
                showConfirmButton: false,
                timer: 1500
              });
            </script>';
        }
    } else {
        echo '<script>
            Swal.fire({
              icon: "error",
              title: "Gabim gjatë përditësimit të të dhënave: ' . $conn->error . '",
              showConfirmButton: false,
              timer: 1500
            });
          </script>';
    }
}
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
                            Edito klientin <?php echo htmlspecialchars($editcl['emri']); ?>
                        </a>
                    </li>
                </ol>
            </nav>
            <div class="mb-3">
                <a href="klient.php" class="input-custom-css px-3 py-2" style="text-decoration: none;">
                    Kthehu prapa
                </a>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="row">
                    <!-- Client Information Accordion -->
                    <div class="col-3">
                        <div class="accordion" id="client-infos-accordion">
                            <div class="accordion-item border-1 rounded-5">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button border-0 rounded-5" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true"
                                        aria-controls="collapseOne">
                                        <i class="fi fi-rr-user me-3"></i> Të dhënat e klientit
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show"
                                    aria-labelledby="headingOne" data-bs-parent="#client-infos-accordion">
                                    <div class="accordion-body border-0">
                                        <div class="col mb-3">
                                            <label class="form-label" for="emri">Emri dhe Mbiemri</label>
                                            <input type="text" name="emri"
                                                class="form-control rounded-5 border border-2"
                                                placeholder="Shëno emrin dhe mbiemrin e klientit"
                                                value="<?php echo htmlspecialchars($editcl['emri']); ?>" required>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="np">ID e Dokumentit Personal</label>
                                            <input type="text" name="np"
                                                class="form-control rounded-5 border border-2"
                                                placeholder="Shëno ID e dokumentit"
                                                value="<?php echo htmlspecialchars($editcl['np']); ?>" required>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="adresa">Adresa</label>
                                            <input type="text" name="adresa"
                                                class="form-control rounded-5 border border-2"
                                                value="<?php echo htmlspecialchars($editcl['adresa']); ?>"
                                                placeholder="Adresa" required>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="emailadd">Adresa Elektronike (Email)</label>
                                            <input type="email" name="emailadd"
                                                class="form-control rounded-5 border border-2"
                                                value="<?php echo htmlspecialchars($editcl['emailadd']); ?>"
                                                placeholder="Shëno emailin e klientit" required>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="email_kontablist">Adresa Elektronike e Kontablistit (Email)</label>
                                            <input type="email" name="email_kontablist"
                                                class="form-control rounded-5 border border-2"
                                                value="<?php echo htmlspecialchars($editcl['email_kontablist']); ?>"
                                                placeholder="Shëno email-in e platformave të klientit">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="nrtel">Numri i Telefonit</label>
                                            <input type="tel" name="nrtel"
                                                class="form-control rounded-5 border border-2"
                                                value="<?php echo htmlspecialchars($editcl['nrtel']); ?>"
                                                placeholder="Shëno numrin e telefonit të klientit" required>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="bank_info">Emri i Bankës</label>
                                            <select id="bank_info" name="bank_info"
                                                class="form-select rounded-5 border border-2 py-2" required>
                                                <option value="custom">Shto emrin e personalizuar të bankës</option>
                                                <option value="<?php echo htmlspecialchars($editcl['bank_name']); ?>" selected>
                                                    <?php echo htmlspecialchars($editcl['bank_name']); ?>
                                                </option>
                                                <option value="Banka Kombëtare Tregtare">Banka Kombëtare Tregtare (Albania)</option>
                                                <option value="Banka për Biznes">Banka për Biznes (Kosovo)</option>
                                                <option value="NLB Komercijalna Banka">NLB Komercijalna Banka (Slovenia)</option>
                                                <option value="NLB Banka">NLB Banka (Slovenia)</option>
                                                <option value="ProCredit Bank">ProCredit Bank (Germany)</option>
                                                <option value="Raiffeisen Bank Kosovo">Raiffeisen Bank Kosovo (Austria)</option>
                                                <option value="TEB SH.A.">TEB SH.A. (Turkey)</option>
                                                <option value="Ziraat Bank">Ziraat Bank (Turkey)</option>
                                                <option value="Turkiye Is Bank">Turkiye Is Bank (Turkey)</option>
                                                <option value="PayPal">PayPal</option>
                                                <option value="Ria">Ria</option>
                                                <option value="Money Gram">Money Gram</option>
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
                                        <div class="col mb-3">
                                            <label class="form-label" for="nrllog">Numri i Xhirollogarisë</label>
                                            <input type="text" name="nrllog"
                                                class="form-control rounded-5 border border-2"
                                                placeholder="Shëno numrin e xhirollogarisë së klientit"
                                                value="<?php echo htmlspecialchars($editcl['nrllog']); ?>" required>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="type_of_client">Lloji i Klientit</label>
                                            <br>
                                            <span style="font-size: 12px;" class="text-muted mb-3">
                                                Statusi aktual i llojit të klientit: <?php echo htmlspecialchars($editcl['lloji_klientit']); ?>
                                            </span>
                                            <hr>
                                            <select id="type_of_client" name="type_of_client"
                                                class="form-select rounded-5 border border-2" required>
                                                <option value="Personal" <?php if ($editcl['lloji_klientit'] == 'Personal') echo 'selected'; ?>>
                                                    Personal
                                                </option>
                                                <option value="Biznes" <?php if ($editcl['lloji_klientit'] == 'Biznes') echo 'selected'; ?>>
                                                    Biznes
                                                </option>
                                            </select>
                                            <script>
                                                new Selectr('#type_of_client', {
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
                    <!-- YouTube Information Accordion -->
                    <div class="col-3">
                        <div class="accordion" id="client-infos-youtube-accordion">
                            <div class="accordion-item border-1 rounded-5">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button border-0 rounded-5" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true"
                                        aria-controls="collapseTwo">
                                        <i class="fi fi-brands-youtube me-3"></i> Të dhënat e Klientit (YouTube)
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo"
                                    data-bs-parent="#client-infos-youtube-accordion">
                                    <div class="accordion-body">
                                        <div class="col mb-3">
                                            <label class="form-label" for="yt">ID e Kanalit në Platformën YouTube</label>
                                            <input type="text" name="yt"
                                                class="form-control rounded-5 border border-2"
                                                placeholder="Shëno ID e kanalit"
                                                value="<?php echo htmlspecialchars($editcl['youtube']); ?>" required>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="emriart">Emri Artistik</label>
                                            <input type="text" name="emriart"
                                                class="form-control rounded-5 border border-2"
                                                placeholder="Shëno emrin artistik"
                                                value="<?php echo htmlspecialchars($editcl['emriart']); ?>" required>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="dk">Data e Fillimit të Kontratës</label>
                                            <input type="date" name="dk" id="dk"
                                                class="form-control rounded-5 border border-2"
                                                placeholder="Shkruaj Daten e kontratës"
                                                value="<?php echo htmlspecialchars($contractStartDate['data_e_krijimit'] ?? ''); ?>" required>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="dks">Data e Skadimit të Kontratës</label>
                                            <input type="date" name="dks" id="dks"
                                                class="form-control rounded-5 border border-2"
                                                placeholder="Shkruaj Daten e skadimit"
                                                value="<?php echo htmlspecialchars($expirationDateFormatted); ?>" required>
                                        </div>
                                        <div class="col mb-3">
                                            <?php
                                            $sql = "SELECT kg.youtube_id FROM kontrata_gjenerale kg 
                                                        JOIN klientet k ON kg.youtube_id = k.youtube 
                                                        WHERE k.youtube = '" . mysqli_real_escape_string($conn, $editcl['youtube']) . "'";
                                            $result = $conn->query($sql);
                                            if (!$result) {
                                                echo "Query failed: " . htmlspecialchars($conn->error);
                                            } else if ($result->num_rows == 0) {
                                                // Handle no results found, display dropdown
                                                echo '<div class="mb-3">
                                                            <label class="form-label" for="statusi_i_kontrates">Statusi i Kontratës</label>
                                                            <select class="form-select border border-2 rounded-5 w-100" name="statusi_i_kontrates" id="statusi_i_kontrates" required>
                                                                <option value="Kontratë fizike">Kontratë fizike</option>
                                                                <option value="S\'ka kontratë">S\'ka kontratë</option>
                                                            </select>
                                                        </div>';
                                                // JS for initializing Selectr
                                                echo "<script>
                                                        new Selectr('#statusi_i_kontrates', { searchable: true, width: 300 });
                                                      </script>";
                                            }
                                            ?>
                                        </div>
                                        <div class="col mb-3">
                                            <?php
                                            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                                            $userCategory = '';
                                            if ($id > 0) {
                                                $stmt = $conn->prepare("SELECT kategoria FROM klientet WHERE id = ?");
                                                $stmt->bind_param("i", $id);
                                                $stmt->execute();
                                                $stmt->bind_result($userCategory);
                                                $stmt->fetch();
                                                $stmt->close();
                                            }
                                            $categories = [];
                                            if ($userCategory) {
                                                $categories[] = $userCategory;
                                            }
                                            $catQuery = $conn->query("SELECT kategorit FROM kategorit");
                                            if ($catQuery && $catQuery->num_rows > 0) {
                                                while ($row = $catQuery->fetch_assoc()) {
                                                    $categories[] = $row['kategorit'];
                                                }
                                            }
                                            $categories = array_unique($categories);
                                            ?>
                                            <label class="form-label" for="kategoria">Zgjidh Kategorinë</label>
                                            <select class="form-select border border-2 rounded-5 w-100" name="kategoria" id="kategoria" required>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?= htmlspecialchars($category); ?>" <?= ($userCategory == $category) ? 'selected' : ''; ?>>
                                                        <?= htmlspecialchars($category); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                                <?php if (!$userCategory && empty($categories)): ?>
                                                    <option value="Këngëtar">Këngëtar</option>
                                                <?php endif; ?>
                                            </select>
                                            <script>
                                                new Selectr('#kategoria', {
                                                    searchable: true,
                                                    width: 300
                                                });
                                            </script>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="min">Monetizuar?</label><br>
                                            <?php if ($editcl['monetizuar'] == "PO"): ?>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="min" id="po" value="PO" checked>
                                                    <label class="form-check-label text-success" for="po">PO (aktive)</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="min" id="jo" value="JO">
                                                    <label class="form-check-label text-muted" for="jo">JO</label>
                                                </div>
                                            <?php else: ?>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="min" id="po" value="PO">
                                                    <label class="form-check-label text-muted" for="po">PO</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="min" id="jo" value="JO" checked>
                                                    <label class="form-check-label text-success" for="jo">JO (aktive)</label>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="ads">Edito Llogarinë e ADS:</label>
                                            <select class="form-select w-100" name="ads" id="exampleFormControlSelect2" required>
                                                <?php
                                                $mads = $conn->query("SELECT * FROM ads");
                                                $ads_found = false;
                                                while ($ads = mysqli_fetch_assoc($mads)) {
                                                    if ($ads['id'] == $editcl['ads']) {
                                                        $ads_found = true;
                                                    }
                                                ?>
                                                    <option value="<?php echo htmlspecialchars($ads['id']); ?>" <?php if ($ads['id'] == $editcl['ads']) echo "selected"; ?>>
                                                        <?php echo htmlspecialchars($ads['email']); ?> |
                                                        <?php echo htmlspecialchars($ads['adsid']); ?> (<?php echo htmlspecialchars($ads['shteti']); ?>)
                                                    </option>
                                                <?php } ?>
                                                <?php if (!$ads_found && !empty($editcl['emri'])):
                                                    $guse = $conn->query("SELECT * FROM klientet WHERE emri='" . mysqli_real_escape_string($conn, $editcl['emri']) . "'");
                                                    $guse2 = mysqli_fetch_assoc($guse);
                                                    $adsid = $guse2['ads'];
                                                    $mads = $conn->query("SELECT * FROM ads WHERE id='" . mysqli_real_escape_string($conn, $adsid) . "'");
                                                    $ads = mysqli_fetch_assoc($mads);
                                                    if ($ads):
                                                ?>
                                                        <option value="<?php echo htmlspecialchars($ads['id']); ?>" selected>
                                                            <?php echo htmlspecialchars($ads['email']); ?> | <?php echo htmlspecialchars($ads['adsid']); ?> (<?php echo htmlspecialchars($ads['shteti']); ?>)
                                                        </option>
                                                <?php endif;
                                                endif; ?>
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

                    <!-- Percentage and Platforms Accordion -->
                    <div class="col-3">
                        <div class="accordion" id="perqindja-accordion">
                            <div class="accordion-item border-1 rounded-5">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button border-0 rounded-5" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="true"
                                        aria-controls="collapseThree">
                                        <i class="fi fi-rr-world me-3"></i> Përqindja dhe Platformat
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse show" aria-labelledby="headingThree"
                                    data-bs-parent="#perqindja-accordion">
                                    <div class="accordion-body">
                                        <div class="col mb-3">
                                            <label class="form-label" for="emailp">Adresa Elektronike (Email) për Platforma</label>
                                            <input type="email" name="emailp"
                                                class="form-control rounded-5 border border-2"
                                                value="<?php echo htmlspecialchars($editcl['emailp']); ?>"
                                                placeholder="Shëno email-in e platformave të klientit" required>
                                        </div>
                                        <div class="col mb-3">
                                            <label for="perqindja" class="form-label">Përqindja (Baresha)</label>
                                            <input type="number" step="0.01" name="perqindja"
                                                class="form-control rounded-5 border border-2"
                                                value="<?php echo htmlspecialchars($editcl['perqindja']); ?>"
                                                placeholder="0.00%" required>
                                        </div>
                                        <div class="col mb-3">
                                            <label for="perqindja2" class="form-label">Përqindja për Platforma të Tjera (Baresha)</label>
                                            <input type="number" step="0.01" name="perqindja2"
                                                class="form-control rounded-5 border border-2"
                                                value="<?php echo htmlspecialchars($editcl['perqindja2']); ?>"
                                                placeholder="0.00%" required>
                                        </div>
                                        <div class="col mb-3">
                                            <label for="perqindja_check" class="form-label">Përqindja e Klientit</label>
                                            <div class="input-group flex-nowrap">
                                                <div class="input-group-text bg-transparent border-0">
                                                    <input type="checkbox" name="perqindja_check" id="perqindja_check" <?php if ($editcl['perqindja_check'] === "1") echo "checked"; ?>>
                                                </div>
                                                <?php
                                                if (isset($editcl['perqindja']) && is_numeric($editcl['perqindja'])) {
                                                    $perqindja_value = 100 - $editcl['perqindja'];
                                                } else {
                                                    $perqindja_value = 'Error: Invalid or missing Perqindja value.';
                                                }
                                                ?>
                                                <input type="text" class="form-control rounded-5 border border-2"
                                                    id="perqindja_platformave_output" name="perqindja_e_klientit"
                                                    disabled value="<?php echo htmlspecialchars($perqindja_value); ?>">
                                            </div>
                                            <br>
                                            <?php
                                            if ($editcl['perqindja_check'] === "1") {
                                                echo '<div id="perqindja_platformave_message" style="color: green;">Statusi i kësaj përqindjeje në sistemin e klientit është e vrajtueshme.</div>';
                                            } else {
                                                echo '<div id="perqindja_platformave_message" style="color: red;">JO.</div>';
                                            }
                                            ?>
                                        </div>
                                        <div class="col mb-3">
                                            <label for="perqindja_platformave_check" class="form-label">Përqindja e Platformave për Klientin</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-text bg-transparent border-0">
                                                    <input type="checkbox" name="perqindja_platformave_check"
                                                        id="perqindja_platformave_check" <?php if ($editcl['perqindja_platformave_check'] === "1") echo "checked"; ?>>
                                                </div>
                                                <?php
                                                if (isset($editcl['perqindja2']) && is_numeric($editcl['perqindja2'])) {
                                                    $perqindja2_value = 100 - $editcl['perqindja2'];
                                                } else {
                                                    $perqindja2_value = 'Error: Invalid or missing Perqindja2 value.';
                                                }
                                                ?>
                                                <input type="text" class="form-control rounded-5 border border-2"
                                                    id="perqindja_platformave_output" name="perqindja_e_platformave_klientit"
                                                    disabled value="<?php echo htmlspecialchars($perqindja2_value); ?>">
                                            </div>
                                            <?php
                                            if ($editcl['perqindja_platformave_check'] === "1") {
                                                echo '<div id="perqindja_platformave_message" style="color: green;">Statusi i kësaj përqindjeje në sistemin e klientit është e vrajtueshme.</div>';
                                            } else {
                                                echo '<div id="perqindja_platformave_message" style="color: red;">JO.</div>';
                                            }
                                            ?>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="fb">Facebook URL:</label>
                                            <input type="url" name="fb"
                                                class="form-control rounded-5 border border-2"
                                                placeholder="https://facebook.com/...."
                                                value="<?php echo htmlspecialchars($editcl['fb']); ?>">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="ig">Instagram URL:</label>
                                            <input type="url" name="ig"
                                                class="form-control rounded-5 border border-2"
                                                placeholder="https://instagram.com/...."
                                                value="<?php echo htmlspecialchars($editcl['ig']); ?>">
                                        </div>
                                        <div class="col mb-3">
                                            <a href="perqindjet_klient.php?id=<?php echo htmlspecialchars($editcl['id']); ?>"
                                                class="input-custom-css px-3 py-2 " style="text-decoration: none;">Ndrysho Përqindjet</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Internal Information Accordion with Offcanvas PDF Preview -->
                    <div class="col-3">
                        <div class="accordion" id="information-accordion">
                            <div class="accordion-item border-1 rounded-5">
                                <h2 class="accordion-header" id="headingFour">
                                    <button class="accordion-button border-0 rounded-5" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="true"
                                        aria-controls="collapseFour">
                                        <i class="fi fi-rr-info me-3"></i> Informacion i Brendshëm
                                    </button>
                                </h2>
                                <div id="collapseFour" class="accordion-collapse collapse show" aria-labelledby="headingFour"
                                    data-bs-parent="#information-accordion">
                                    <div class="accordion-body">
                                        <!-- PDF Upload Section -->
                                        <div class="col mb-3">
                                            <label class="form-label" for="tipi">Ngarko Kontratën:</label>
                                            <input type="file" name="tipi" id="tipi" accept="application/pdf"
                                                class="form-control rounded-5 border border-2">
                                        </div>
                                        <!-- Button to Open Offcanvas for PDF Preview -->
                                        <div class="col mb-3">
                                            <button type="button" class="input-custom-css px-3 py-2" data-bs-toggle="offcanvas"
                                                data-bs-target="#pdfPreviewOffcanvas" aria-controls="pdfPreviewOffcanvas">
                                                Preview PDF
                                            </button>
                                        </div>
                                        <!-- Bootstrap Offcanvas for PDF Preview -->
                                        <div class="offcanvas offcanvas-end" style="width: 50%;" tabindex="-1" id="pdfPreviewOffcanvas"
                                            aria-labelledby="pdfPreviewOffcanvasLabel">
                                            <div class="offcanvas-header">
                                                <h5 class="offcanvas-title" id="pdfPreviewOffcanvasLabel">Preview Kontratë</h5>
                                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="offcanvas-body">
                                                <iframe id="contractPreview" style="width: 100%; height: 500px; border: none;"></iframe>
                                            </div>
                                        </div>
                                        <!-- Existing PDF Preview Button (Optional) -->
                                        <?php if (!empty($editcl['tipi']) && file_exists("dokument/" . $editcl['tipi'])): ?>
                                            <div class="col mb-3">
                                                <button type="button" class="btn btn-secondary" data-bs-toggle="offcanvas"
                                                    data-bs-target="#pdfPreviewOffcanvas" aria-controls="pdfPreviewOffcanvas">
                                                    Shiko Kontraten Ekzistuese
                                                </button>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Emails Selection -->
                                        <div class="col mb-3">
                                            <label class="form-label" for="emails">Emails</label>
                                            <select multiple class="form-control border border-2 rounded-5"
                                                name="emails[]" id="exampleFormControlSelect3" required>
                                                <?php
                                                // Fetch all distinct emails from both klientet and ads tables
                                                $getemails_klientet = $conn->query("SELECT emails FROM klientet WHERE id = '$editid'");
                                                $emails_with_access = [];
                                                if ($maillist = mysqli_fetch_assoc($getemails_klientet)) {
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
                                                        <option value="<?php echo htmlspecialchars($email_ads); ?>">
                                                            <?php echo htmlspecialchars($email_ads); ?> (nuk ka akses)
                                                        </option>
                                                    <?php } else {
                                                        // Email with access
                                                    ?>
                                                        <option value="<?php echo htmlspecialchars($email_ads); ?>" selected>
                                                            <?php echo htmlspecialchars($email_ads); ?> (ka akses)
                                                        </option>
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
                                            });
                                        </script>

                                        <!-- System User Information -->
                                        <div class="col mb-3">
                                            <label class="form-label" for="perdoruesi">Përdoruesi <small>(Sistemit)</small>:</label>
                                            <input type="text" name="perdoruesi"
                                                class="form-control border border-2 rounded-5"
                                                placeholder="Përdoruesi i sistemit"
                                                value="<?php echo htmlspecialchars($editcl['perdoruesi']); ?>" required>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="fjalkalimi">Fjalëkalimi <small>(Sistemit)</small>:</label>
                                            <input type="password" name="fjalkalimi"
                                                class="form-control border border-2 rounded-5"
                                                placeholder="Fjalëkalimi i sistemit"
                                                value="<?php echo htmlspecialchars($editcl['fjalkalimi']); ?>" required>
                                            <button type="button" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#passwordInfoModal">Info</button>
                                            <!-- Modal for Password Information -->
                                            <div class="modal fade" id="passwordInfoModal" tabindex="-1" role="dialog"
                                                aria-labelledby="passwordInfoModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="passwordInfoModalLabel">
                                                                Informacione për Rivendosjen e Fjalëkalimit
                                                            </h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>
                                                                Ky është fjalëkalimi i enkriptuar për klientin:
                                                                <?php echo htmlspecialchars($editcl['emri']); ?>
                                                            </p>
                                                            <p>
                                                                Fjalëkalimi:
                                                                <strong><?php echo htmlspecialchars($editcl['fjalkalimi']); ?> </strong>
                                                            </p>
                                                            <p>
                                                                Fjalëkalimi është enkriptuar me MD5, një funksion hash me një drejtim.
                                                                Kjo do të thotë se nuk mund të deshifrohet drejtpërdrejt sepse është projektuar të jetë i pakthyeshëm.
                                                            </p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Mbylle</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="shtetsia">Shtetësia</label>
                                            <select class="form-select border border-2 rounded-5" name="shtetsia" id="shtetsia" required>
                                                <?php
                                                // Static options
                                                $staticOptions = [
                                                    "Shqipëri",
                                                    "Kosovë",
                                                    "Gjermania",
                                                    "Italia",
                                                    "Zvicër",
                                                    "Maqedonia",
                                                    "Mali i Zi"
                                                ];
                                                // Fetch distinct shtetsia values from the database
                                                $query = "SELECT DISTINCT shtetsia FROM klientet WHERE id = '$editid'";
                                                $result = $conn->query($query);
                                                // Merge static and dynamic options
                                                $options = array_unique(array_merge($staticOptions, $result ? array_column($result->fetch_all(MYSQLI_ASSOC), 'shtetsia') : []));
                                                // Output options
                                                foreach ($options as $shtetsiaOption): ?>
                                                    <option value="<?php echo htmlspecialchars($shtetsiaOption); ?>" <?php echo (isset($editcl['shtetsia']) && $editcl['shtetsia'] == $shtetsiaOption) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($shtetsiaOption); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <script>
                                                new Selectr('#shtetsia', {})
                                            </script>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="shtetsiaKontabiliteti">Kontabiliteti</label>
                                            <select class="form-select border border-2 rounded-5" name="shtetsiaKontabiliteti" id="shtetsiaKontabiliteti" required>
                                                <?php
                                                // Static options
                                                $staticOptions = [
                                                    "Kosova",
                                                    "Shqipëri",
                                                    "Gjermania",
                                                    "Francë",
                                                    "Slloveni"
                                                ];
                                                // Fetch distinct shtetsiaKontabiliteti values from the database
                                                $query = "SELECT DISTINCT shtetsiaKontabiliteti FROM klientet WHERE id = '$editid'";
                                                $result = $conn->query($query);
                                                // Merge static and dynamic options
                                                $dbOptions = $result ? array_column($result->fetch_all(MYSQLI_ASSOC), 'shtetsiaKontabiliteti') : [];
                                                $options = array_unique(array_merge($staticOptions, $dbOptions));
                                                // Output options
                                                foreach ($options as $shtetsiaKontabilitetiOption): ?>
                                                    <option value="<?php echo htmlspecialchars($shtetsiaKontabilitetiOption); ?>" <?php echo (isset($editcl['shtetsiaKontabiliteti']) && $editcl['shtetsiaKontabiliteti'] == $shtetsiaKontabilitetiOption) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($shtetsiaKontabilitetiOption); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <script>
                                                new Selectr('#shtetsiaKontabiliteti', {})
                                            </script>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="info">Info Shtesë</label>
                                            <textarea class="form-control rounded-5 border border-2" id="simpleMde"
                                                name="info" placeholder="Info Shtesë" required><?php echo htmlspecialchars($editcl['info']); ?></textarea>
                                        </div>
                                        <hr>
                                        <div class="col d-flex justify-content-center align-items-center">
                                            <button type="submit"
                                                class="btn btn-secondary rounded-5 text-white shadow mb-3"
                                                style="text-transform:none;" name="ndrysho">
                                                <i class="ti-save me-2"></i>
                                                Përditso të gjitha Informacionet
                                            </button>
                                        </div>
                                        <div class="col d-flex justify-content-center align-items-center">
                                            <a href="kanal.php?kid=<?php echo htmlspecialchars($editcl['id']); ?>"
                                                class="btn btn-danger rounded-5 text-white shadow mb-3"
                                                style="text-transform:none;">
                                                <i class="fi fi-rr-eye me-2"></i>
                                                Shiko Kanalin
                                            </a>
                                        </div>
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
<?php include 'partials/footer.php'; ?>

<!-- JavaScript for Offcanvas PDF Preview -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipiInput = document.getElementById('tipi');
        const contractPreview = document.getElementById('contractPreview');

        tipiInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.type === 'application/pdf') {
                    const fileUrl = URL.createObjectURL(file);
                    contractPreview.src = fileUrl;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gabim',
                        text: 'Ju lutem ngarkoni vetëm skedarë PDF.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    tipiInput.value = '';
                    contractPreview.src = '';
                }
                // do not let file more than 10 mb to upload
                if (file.size > 10000000) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gabim',
                        text: 'Ju lutem ngarkoni skedarë PDF me mbi 10MB.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    tipiInput.value = '';
                    contractPreview.src = '';
                }
            } else {
                contractPreview.src = '';
            }
        });

        // If there's an existing PDF, set it in the Offcanvas preview
        <?php if (!empty($editcl['tipi']) && file_exists("dokument/" . $editcl['tipi'])): ?>
            contractPreview.src = 'dokument/<?php echo htmlspecialchars($editcl['tipi']); ?>';
        <?php endif; ?>
    });
</script>

<!-- Initialize Flatpickr for Date Inputs -->
<script>
    const flatpickrOptions = {
        dateFormat: "Y-m-d"
    };
    flatpickr("#dk", {
        ...flatpickrOptions,
        maxDate: "today"
    });
    flatpickr("#dks", {
        ...flatpickrOptions,
        minDate: "today"
    });
</script>