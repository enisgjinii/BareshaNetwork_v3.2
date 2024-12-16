<?php
include 'partials/header.php';
require 'vendor/autoload.php';
// Ensure the connection variable $conn is available
// You might need to include your database connection script here if not already included in 'partials/header.php'
// Check if the form has been submitted
if (isset($_POST['ruaj'])) {
    $fields = [
        'emri',
        'dk',
        'np',
        'dks',
        'yt',
        'info',
        'perqindja',
        'perqindja2',
        'ads',
        'fb',
        'ig',
        'adresa',
        'kategoria',
        'nrtel',
        'emailadd',
        'email_kontablist',
        'emailp',
        'emriart',
        'nrllog',
        'bank_info',
        'perdoruesi',
        'password',
        'shtetsia',
        'shtetsiaKontabiliteti',
        'agent'
    ];
    $data = [];
    foreach ($fields as $field) {
        $data[$field] = mysqli_real_escape_string($conn, $_POST[$field]);
    }
    // Special cases
    $data['mon'] = empty($_POST['min']) ? "JO" : mysqli_real_escape_string($conn, $_POST['min']);
    $data['password'] = md5($data['password']); // Consider using password_hash instead
    $data['emails'] = isset($_POST['emails']) && !empty($_POST['emails']) ? addslashes(implode(', ', $_POST['emails'])) : '';
    $perqindja_check = isset($_POST['perqindja_check']) ? '1' : '0';
    $perqindja_e_platformave_check = isset($_POST['perqindja_platformave_check']) ? '1' : '0';
    $type_of_client = mysqli_real_escape_string($conn, $_POST['type_of_client']);
    $shtetsia = mysqli_real_escape_string($conn, $_POST['shtetsia']);
    $shtetsiaKontabiliteti = mysqli_real_escape_string($conn, $_POST['shtetsiaKontabiliteti']);
    $agent = mysqli_real_escape_string($conn, $_POST['agent']);
    // File upload handling
    $uploadedFileName = ''; // Initialize as empty
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
    // Proceed to insert into the database only if the file upload was successful or no file was uploaded
    if ($uploadedFileName !== '' || !isset($_FILES['tipi'])) {
        if ($conn->query(
            "INSERT INTO klientet 
                (emri, np, monetizuar, dk, dks, youtube, info, perqindja, perqindja2, kontrata, ads, fb, ig, adresa, kategoria, nrtel, emailadd, emailp, emriart, nrllog, bank_name, fjalkalimi, perdoruesi, emails, blocked, perqindja_check, perqindja_platformave_check, lloji_klientit, email_kontablist, shtetsia, shtetsiaKontabiliteti,agent) 
                VALUES (
                    '{$data['emri']}', '{$data['np']}', '{$data['mon']}', '{$data['dk']}', '{$data['dks']}', '{$data['yt']}', 
                    '{$data['info']}', '{$data['perqindja']}', '{$data['perqindja2']}', '$uploadedFileName', '{$data['ads']}', 
                    '{$data['fb']}', '{$data['ig']}', '{$data['adresa']}', '{$data['kategoria']}', '{$data['nrtel']}', 
                    '{$data['emailadd']}', '{$data['emailp']}', '{$data['emriart']}', '{$data['nrllog']}', '{$data['bank_info']}', 
                    '{$data['password']}', '{$data['perdoruesi']}', '{$data['emails']}', '0', '$perqindja_check', 
                    '$perqindja_e_platformave_check', '$type_of_client', '{$data['email_kontablist']}', '$shtetsia', '{$data['shtetsiaKontabiliteti']}','{$data['agent']}'
                )"
        )) {
            $kueri = $conn->query("SELECT * FROM klientet ORDER BY id DESC");
            $k = mysqli_fetch_array($kueri);
            $cdata = date("Y-m-d H:i:s");
            // Check if email exists
            $email = $data['emailadd']; // Assuming 'emailadd' is the email to check
            $check_email = $conn->prepare("SELECT `email` FROM `googleauth` WHERE `email`=?");
            $check_email->bind_param("s", $email);
            $check_email->execute();
            $check_email->store_result();
            // Add the Sweet Alert with a button to go to the newly added client page
            echo '<script>
                Swal.fire({
                    icon: "success",
                    title: "Këndgetari u shtua me sukses!",
                    showConfirmButton: true,
                    confirmButtonText: "Shiko këndgetarin",
                    showCancelButton: true,
                    cancelButtonText: "Mbylle",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    closeOnClickOutside: false,
                    closeOnEsc: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "kanal.php?kid=' . $k['id'] . '"; 
                    }
                });
                </script>';
        }
    }
}
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" style="width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a class="text-reset" href="#" style="text-decoration: none;">Klientët</a></li>
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
                    <!-- Client Information Accordion -->
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="accordion" id="client-infos-accordion">
                            <div class="accordion-item border-1 rounded-5">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button border-0 rounded-5" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true"
                                        aria-controls="collapseOne">
                                        <i class="fi fi-rr-user me-3"></i> Të dhënat e Klientit
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show"
                                    aria-labelledby="headingOne" data-bs-parent="#client-infos-accordion">
                                    <div class="accordion-body border-0">
                                        <?php
                                        $inputs = [
                                            ["label" => "Emri dhe Mbiemri", "name" => "emri", "id" => "emri", "placeholder" => "Shëno emrin dhe mbiemrin e klientit"],
                                            ["label" => "ID e dokumentit personal", "name" => "np", "id" => "np", "placeholder" => "Shëno ID e dokumentit personal"],
                                            ["label" => "Adresa", "name" => "adresa", "id" => "adresa", "placeholder" => "Shëno adresën"],
                                            ["label" => "Adresa elektronike (Email)", "name" => "emailadd", "id" => "emailadd", "placeholder" => "Shëno email-in e klientit"],
                                            ["label" => "Adresa elektronike e kontablistit (Email)", "name" => "email_kontablist", "id" => "email_kontablist", "placeholder" => "Shëno email-in e kontablistit"],
                                            ["label" => "Numri i telefonit", "name" => "nrtel", "id" => "nrtel", "placeholder" => "Shëno numrin e telefonit"],
                                            ["label" => "Nr. Xhirollogaris", "name" => "nrllog", "id" => "nrllog", "placeholder" => "Shëno numrin e xhirollogaris"]
                                        ];
                                        foreach ($inputs as $input) {
                                            echo '<div class="col mb-3">';
                                            echo '<label class="form-label" for="' . $input['id'] . '">' . $input['label'] . '</label>';
                                            echo '<input type="text" name="' . $input['name'] . '" id="' . $input['id'] . '" class="form-control border border-2 rounded-5" placeholder="' . $input['placeholder'] . '" autocomplete="off" required>';
                                            echo '</div>';
                                        }
                                        ?>
                                        <div class="col mb-3">
                                            <label class="form-label" for="bank_info">Zgjidh bankën</label>
                                            <select id="bank_info" name="bank_info" class="form-select rounded-5 border border-2 py-2" required>
                                                <option value="custom">Shto emrin e personalizuar të bankës</option>
                                                <?php
                                                $banks = [
                                                    "Banka Ekonomike (Kosovo)",
                                                    "Banka Kombëtare Tregtare (Albania)",
                                                    "Banka për Biznes (Kosovo)",
                                                    "NLB Komercijalna banka (Slovenia)",
                                                    "NLB Banka (Slovenia)",
                                                    "ProCredit Bank (Germany)",
                                                    "Raiffeisen Bank Kosovo (Austria)",
                                                    "TEB SH.A. (Turkey)",
                                                    "Ziraat Bank (Turkey)",
                                                    "Turkiye Is Bank (Turkey)",
                                                    "PayPal",
                                                    "Ria",
                                                    "Money Gram",
                                                    "Western Union"
                                                ];
                                                foreach ($banks as $bank) {
                                                    echo '<option value="' . htmlspecialchars($bank) . '">' . htmlspecialchars($bank) . '</option>';
                                                }
                                                ?>
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
                                            <label class="form-label" for="type_of_client">Lloji i Klientit</label>
                                            <select name="type_of_client" id="type_of_client" class="form-select rounded-5 border border-2" required>
                                                <option value="Personal">Personal</option>
                                                <option value="Biznes">Biznes</option>
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
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
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
                                            <input type="text" name="yt" id="yt" class="form-control border border-2 rounded-5"
                                                placeholder="Shëno ID e kanalit në platformën YouTube" autocomplete="off" required>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="emriart">Emri Artistik</label>
                                            <input type="text" name="emriart" id="emriart" class="form-control border border-2 rounded-5"
                                                placeholder="Shëno emrin artistik" required>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="dk">Data e Fillimit të Kontratës</label>
                                            <input type="text" name="dk" id="dk" class="form-control border border-2 rounded-5"
                                                placeholder="Shëno datën e fillimit të kontratës" autocomplete="off" required>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="dks">Data e Skadimit të Kontratës</label>
                                            <input type="text" name="dks" id="dks" class="form-control border border-2 rounded-5"
                                                placeholder="Shëno datën e skadimit të kontratës" autocomplete="off" required>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="kategoria">Zgjidh Kategorinë</label>
                                            <select class="form-select border border-2 rounded-5 w-100" name="kategoria" id="kategoria" required>
                                                <?php
                                                $kg = $conn->query("SELECT * FROM kategorit");
                                                while ($kgg = mysqli_fetch_array($kg)) {
                                                    echo '<option value="' . htmlspecialchars($kgg['kategorit']) . '">' . htmlspecialchars($kgg['kategorit']) . '</option>';
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
                                        <div class="col mb-3">
                                            <label class="form-label">A është ky kanal i monetizuar?</label><br>
                                            <div class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input" id="po" name="min" value="PO" required>
                                                <label class="form-check-label text-success" for="po">PO</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input" id="jo" name="min" value="JO" required>
                                                <label class="form-check-label text-danger" for="jo">JO</label>
                                            </div>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="ads">Zgjidh Llogarinë e ADS</label>
                                            <select class="form-select border border-2 rounded-5 w-100" name="ads" id="llogaria" required>
                                                <?php
                                                $mads = $conn->query("SELECT * FROM ads");
                                                while ($ads = mysqli_fetch_assoc($mads)) {
                                                    echo '<option value="' . htmlspecialchars($ads['id']) . '">' . htmlspecialchars($ads['email']) . ' | ' . htmlspecialchars($ads['adsid']) . ' (' . htmlspecialchars($ads['shteti']) . ')</option>';
                                                }
                                                ?>
                                            </select>
                                            <script>
                                                new Selectr('#llogaria', {
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
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
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
                                                value="<?php echo htmlspecialchars($data['emailp'] ?? ''); ?>"
                                                placeholder="Shëno email-in e platformave të klientit" required>
                                        </div>
                                        <div class="col mb-3">
                                            <label for="perqindja" class="form-label">Përqindja (Baresha)</label>
                                            <input type="number" step="0.01" name="perqindja"
                                                class="form-control rounded-5 border border-2"
                                                value="<?php echo htmlspecialchars($data['perqindja'] ?? ''); ?>"
                                                placeholder="0.00%" required>
                                        </div>
                                        <div class="col mb-3">
                                            <label for="perqindja2" class="form-label">Përqindja për Platforma të Tjera (Baresha)</label>
                                            <input type="number" step="0.01" name="perqindja2"
                                                class="form-control rounded-5 border border-2"
                                                value="<?php echo htmlspecialchars($data['perqindja2'] ?? ''); ?>"
                                                placeholder="0.00%" required>
                                        </div>
                                        <div class="col mb-3">
                                            <label for="perqindja_check" class="form-label">Përqindja e Klientit</label>
                                            <div class="input-group flex-nowrap">
                                                <div class="input-group-text bg-transparent border-0">
                                                    <input type="checkbox" name="perqindja_check" id="perqindja_check" onchange="showMessage('perqindja_check', 'perqindja_input', 'perqindja_output', 'perqindja_message')" <?php echo (isset($data['perqindja_check']) && $data['perqindja_check'] === "1") ? "checked" : ""; ?>>
                                                </div>
                                                <?php
                                                if (isset($data['perqindja']) && is_numeric($data['perqindja'])) {
                                                    $perqindja_value = 100 - $data['perqindja'];
                                                } else {
                                                    $perqindja_value = 'Error: Invalid or missing Perqindja value.';
                                                }
                                                ?>
                                                <input type="text" class="form-control rounded-5 border border-2"
                                                    id="perqindja_output" name="perqindja_e_klientit"
                                                    disabled value="<?php echo htmlspecialchars($perqindja_value); ?>">
                                            </div>
                                            <br>
                                            <?php
                                            if (isset($data['perqindja_check']) && $data['perqindja_check'] === "1") {
                                                echo '<div id="perqindja_message" style="color: green;">Kjo përqindje do të jetë e dukshme për klientin.</div>';
                                            } else {
                                                echo '<div id="perqindja_message" style="color: red;">JO.</div>';
                                            }
                                            ?>
                                        </div>
                                        <div class="col mb-3">
                                            <label for="perqindja_platformave_check" class="form-label">Përqindja e Platformave për Klientin</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-text bg-transparent border-0">
                                                    <input type="checkbox" name="perqindja_platformave_check" id="perqindja_platformave_check" onchange="showMessage('perqindja_platformave_check', 'perqindja_input2', 'perqindja_platformave_output', 'perqindja_platformave_message')" <?php echo (isset($data['perqindja_platformave_check']) && $data['perqindja_platformave_check'] === "1") ? "checked" : ""; ?>>
                                                </div>
                                                <?php
                                                if (isset($data['perqindja2']) && is_numeric($data['perqindja2'])) {
                                                    $perqindja2_value = 100 - $data['perqindja2'];
                                                } else {
                                                    $perqindja2_value = 'Error: Invalid or missing Perqindja2 value.';
                                                }
                                                ?>
                                                <input type="text" class="form-control rounded-5 border border-2"
                                                    id="perqindja_platformave_output" name="perqindja_e_platformave_klientit"
                                                    disabled value="<?php echo htmlspecialchars($perqindja2_value); ?>">
                                            </div>
                                            <?php
                                            if (isset($data['perqindja_platformave_check']) && $data['perqindja_platformave_check'] === "1") {
                                                echo '<div id="perqindja_platformave_message" style="color: green;">Kjo përqindje do të jetë e dukshme për klientin.</div>';
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
                                                value="<?php echo htmlspecialchars($data['fb'] ?? ''); ?>">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="ig">Instagram URL:</label>
                                            <input type="url" name="ig"
                                                class="form-control rounded-5 border border-2"
                                                placeholder="https://instagram.com/...."
                                                value="<?php echo htmlspecialchars($data['ig'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Internal Information Accordion with Offcanvas PDF Preview -->
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
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
                                                class="form-control rounded-5 border border-2" required>
                                        </div>
                                        <!-- Button to Open Offcanvas for PDF Preview -->
                                        <div class="col mb-3">
                                            <button type="button" class="input-custom-css px-3 py-2" style="text-decoration: none;"
                                                data-bs-toggle="offcanvas" data-bs-target="#pdfPreviewOffcanvas" aria-controls="pdfPreviewOffcanvas">
                                                Preview PDF
                                            </button>
                                        </div>
                                        <!-- Bootstrap Offcanvas for PDF Preview -->
                                        <div class="offcanvas offcanvas-end" tabindex="-1" id="pdfPreviewOffcanvas"
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
                                        <?php if (!empty($data['tipi']) && file_exists("dokument/" . $data['tipi'])): ?>
                                            <div class="col mb-3">
                                                <button type="button" class="btn btn-secondary px-3 py-2" style="text-decoration: none;"
                                                    data-bs-toggle="offcanvas" data-bs-target="#pdfPreviewOffcanvas" aria-controls="pdfPreviewOffcanvas">
                                                    Shiko Kontraten Ekzistuese
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                        <!-- Emails Selection -->
                                        <div class="col mb-3">
                                            <label class="form-label" for="emails">Emails</label>
                                            <select multiple class="form-control border border-2 rounded-5"
                                                name="emails[]" id="exampleFormControlSelect2" required>
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
                                            new Selectr('#exampleFormControlSelect2', {
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
                                                value="<?php echo htmlspecialchars($data['perdoruesi'] ?? ''); ?>" required>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="password">Fjalëkalimi <small>(Sistemit)</small>:</label>
                                            <input type="password" name="password"
                                                class="form-control border border-2 rounded-5"
                                                placeholder="Fjalëkalimi i sistemit"
                                                value="<?php echo htmlspecialchars($data['password'] ?? ''); ?>" required>
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
                                                                <?php echo htmlspecialchars($data['emri'] ?? ''); ?>
                                                            </p>
                                                            <p>
                                                                Fjalëkalimi:
                                                                <strong><?php echo htmlspecialchars($data['password'] ?? ''); ?> </strong>
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
                                                    <option value="<?php echo htmlspecialchars($shtetsiaOption); ?>" <?php echo (isset($data['shtetsia']) && $data['shtetsia'] == $shtetsiaOption) ? 'selected' : ''; ?>>
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
                                                    <option value="<?php echo htmlspecialchars($shtetsiaKontabilitetiOption); ?>" <?php echo (isset($data['shtetsiaKontabiliteti']) && $data['shtetsiaKontabiliteti'] == $shtetsiaKontabilitetiOption) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($shtetsiaKontabilitetiOption); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <script>
                                                new Selectr('#shtetsiaKontabiliteti', {})
                                            </script>
                                        </div>
                                        <?php
                                        // Define the options for the agents
                                        $agents = ["Gjermani", "Itali", "Francë", "Zvicer"];
                                        ?>
                                        <div class="col mb-3">
                                            <label class="form-label" for="agents">Agjenti</label>
                                            <select name="agent" id="agent" class="form-select rounded-5 border border-2" required>
                                                <option value="" disabled selected>Zgjidh Agjentin</option>
                                                <?php foreach ($agents as $agent): ?>
                                                    <option value="<?= htmlspecialchars($agent) ?>"><?= htmlspecialchars($agent) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <script>
                                                new Selectr('#agent', {
                                                    searchable: true,
                                                    width: 300
                                                });
                                            </script>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="info">Info Shtesë</label>
                                            <textarea class="form-control rounded-5 border border-2" id="simpleMde"
                                                name="info" placeholder="Info Shtesë" required><?php echo htmlspecialchars($data['info'] ?? ''); ?></textarea>
                                        </div>
                                        <hr>
                                        <div class="col d-flex justify-content-center align-items-center">
                                            <button type="submit"
                                                class="btn btn-danger rounded-5 text-white shadow mb-3"
                                                style="text-transform:none;" name="ruaj">
                                                <i class="ti-save"></i>
                                                <i class="fi fi-rr-bookmark me-2"></i>
                                                Ruaj të gjitha informacionet
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
            </form>
        </div>
    </div>
</div>
<?php include 'partials/footer.php'; ?>
<!-- JavaScript for Offcanvas PDF Preview and SweetAlert2 Notifications -->
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
                        showConfirmButton: false,
                        timer: 2000
                    });
                    tipiInput.value = '';
                    contractPreview.src = '';
                }
            } else {
                contractPreview.src = '';
            }
            // do not let file more than 10 mb to upload
            if (file.size > 10000000) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gabim',
                    text: 'Ju lutem ngarkoni skedarë PDF me mbi 10MB.',
                    showConfirmButton: false,
                    timer: 2000
                });
                tipiInput.value = '';
                contractPreview.src = '';
            }
        });
        // If there's an existing PDF, set it in the Offcanvas preview
        <?php if (!empty($data['tipi']) && file_exists("dokument/" . $data['tipi'])): ?>
            contractPreview.src = 'dokument/<?php echo htmlspecialchars($data['tipi']); ?>';
        <?php endif; ?>
    });
    // Function to show/hide messages based on checkbox and input validity
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
    // Event listeners for 'perqindja' inputs
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