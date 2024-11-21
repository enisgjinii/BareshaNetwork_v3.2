<?php
ob_start();
include 'partials/header.php';
$editid = isset($_GET['id']) ? intval($_GET['id']) : 0;
$editQuery = "SELECT * FROM klientet WHERE id='$editid'";
$editResult = $conn->query($editQuery);
if ($editResult && $editResult->num_rows > 0) {
    $editcl = mysqli_fetch_assoc($editResult);
} else {
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
$contractStartDate = [];
$contractQuery = "SELECT * FROM kontrata_gjenerale WHERE youtube_id='{$editcl['youtube']}'";
$contractResult = $conn->query($contractQuery);
if ($contractResult && $contractResult->num_rows > 0) {
    $contractStartDate = mysqli_fetch_assoc($contractResult);
    if (!empty($contractStartDate['data_e_krijimit'])) {
        $startDate = new DateTime($contractStartDate['data_e_krijimit']);
        $durationMonths = $contractStartDate['kohezgjatja'] ?? 0;
        $expirationDateFormatted = $durationMonths ? $startDate->modify("+$durationMonths months")->format('Y-m-d') : '';
    } else {
        $expirationDateFormatted = '';
    }
} else {
    $expirationDateFormatted = '';
}
$contractUploaded = !empty($editcl['kontrata']) && file_exists($editcl['kontrata']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['fshi_kontraten'])) {
        if ($contractUploaded) {
            unlink($editcl['kontrata']);
            $editcl['kontrata'] = '';
            $contractUploaded = false;
            $updateContractQuery = "UPDATE klientet SET kontrata='' WHERE id='$editid'";
            if ($conn->query($updateContractQuery)) {
                echo '<script>
                    Swal.fire({
                      icon: "success",
                      title: "Kontrata u fshi me sukses.",
                      showConfirmButton: false,
                      timer: 1500
                    }).then(() => {
                      window.location.reload();
                    });
                  </script>';
            } else {
                echo '<script>
                    Swal.fire({
                      icon: "error",
                      title: "Gabim gjatë fshirjes së kontratës: ' . $conn->error . '",
                      showConfirmButton: false,
                      timer: 2000
                    });
                  </script>';
            }
        }
    }
    if (isset($_POST['ndrysho'])) {
        $fields = [
            'emri',
            'min',
            'dk',
            'dks',
            'yt',
            'info',
            'np',
            'perqindja',
            'perqindja2',
            'ads',
            'fb',
            'nrtel',
            'emailadd',
            'email_kontablist',
            'emailp',
            'ig',
            'adresa',
            'kategoria',
            'emriart',
            'nrllog',
            'bank_info',
            'perdoruesi',
            'fjalkalimi',
            'shtetsia',
            'shtetsiaKontabiliteti',
            'type_of_client',
            'statusi_i_kontrates'
        ];
        foreach ($fields as $field) {
            $$field = mysqli_real_escape_string($conn, $_POST[$field] ?? '');
        }
        $mon = empty($_POST['min']) ? "JO" : mysqli_real_escape_string($conn, $_POST['min']);
        $fjalekalimi = !empty($fjalkalimi) ? password_hash($fjalkalimi, PASSWORD_DEFAULT) : $editcl['fjalkalimi'];
        $emails = isset($_POST['emails']) ? implode(',', array_map('trim', $_POST['emails'])) : '';
        $perqindja_check = isset($_POST['perqindja_check']) ? '1' : '0';
        $perqindja_e_platformave_check = isset($_POST['perqindja_platformave_check']) ? '1' : '0';
        if (!$contractUploaded && isset($_FILES['tipi']) && $_FILES['tipi']['error'] == UPLOAD_ERR_OK) {
            $file_type = $_FILES['tipi']['type'];
            if ($file_type == "application/pdf" && $_FILES['tipi']['size'] <= 10000000) {
                $uniqueFileName = uniqid('contract_', true) . '.pdf';
                $targetPath = "dokument/" . basename($uniqueFileName);
                if (move_uploaded_file($_FILES['tipi']['tmp_name'], $targetPath)) {
                    $editcl['kontrata'] = $targetPath;
                    $contractUploaded = true;
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
                          title: "Gabim gjatë ngarkimit të skedarit.",
                          showConfirmButton: false,
                          timer: 1500
                        });
                      </script>';
                }
            } else {
                $errorMsg = $file_type != "application/pdf" ? "Lejohen vetëm skedarët PDF." : "Lejohen vetëm skedarët PDF me madhësi deri në 10MB.";
                echo '<script>
                    Swal.fire({
                      icon: "error",
                      title: "Na vjen keq, ' . $errorMsg . '",
                      showConfirmButton: false,
                      timer: 2000
                    });
                  </script>';
            }
        }
        $updateQuery = "UPDATE klientet SET 
            emri='$emri', 
            np='$np', 
            monetizuar='$mon', 
            emails='$emails', 
            dk='$dk', 
            dks='$dks', 
            youtube='$yt', 
            info='$info', 
            perqindja='$perqindja', 
            perqindja2='$perqindja2', 
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
            ads='$ads', 
            perdoruesi='$perdoruesi', 
            perqindja_check='$perqindja_check', 
            perqindja_platformave_check='$perqindja_e_platformave_check', 
            fjalkalimi='$fjalekalimi', 
            shtetsia='$shtetsia', 
            lloji_klientit='$type_of_client', 
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
            $user_informations = isset($user_info) ? $user_info['givenName'] . ' ' . $user_info['familyName'] : 'Stafi i Panjohur';
            $log_description = "$user_informations ka ndryshuar klientin $emri";
            $date_information = date('Y-m-d H:i:s');
            $stmt = $conn->prepare("INSERT INTO logs (stafi, ndryshimi, koha) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $user_informations, $log_description, $date_information);
            $stmt->execute();
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
}
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="breadcrumb bg-white px-3 py-2 rounded-5 border" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="klient.php" class="text-reset text-decoration-none">Klientët</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edito klientin <?php echo htmlspecialchars($editcl['emri']); ?></li>
                </ol>
            </nav>
            <div class="mb-4">
                <a href="klient.php" class="input-custom-css px-3 py-2" style="text-decoration: none;">Kthehu prapa</a>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="row g-4">
                    <!-- Të dhënat e klientit -->
                    <div class="col-lg-3 col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-secondary text-white">Të dhënat e klientit</div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="emri" class="form-label" data-bs-toggle="tooltip" title="Emri dhe Mbiemri i klientit">Emri dhe Mbiemri</label>
                                    <input type="text" name="emri" class="form-control" value="<?php echo htmlspecialchars($editcl['emri']); ?>" >
                                </div>
                                <div class="mb-3">
                                    <label for="np" class="form-label" data-bs-toggle="tooltip" title="ID e Dokumentit Personal">ID e Dokumentit Personal</label>
                                    <input type="text" name="np" class="form-control" value="<?php echo htmlspecialchars($editcl['np']); ?>" >
                                </div>
                                <div class="mb-3">
                                    <label for="adresa" class="form-label" data-bs-toggle="tooltip" title="Adresa e klientit">Adresa</label>
                                    <input type="text" name="adresa" class="form-control" value="<?php echo htmlspecialchars($editcl['adresa']); ?>" >
                                </div>
                                <div class="mb-3">
                                    <label for="emailadd" class="form-label" data-bs-toggle="tooltip" title="Adresa Elektronike e klientit">Adresa Elektronike (Email)</label>
                                    <input type="email" name="emailadd" class="form-control" value="<?php echo htmlspecialchars($editcl['emailadd']); ?>" >
                                </div>
                                <div class="mb-3">
                                    <label for="email_kontablist" class="form-label" data-bs-toggle="tooltip" title="Adresa Elektronike e Kontablistit">Adresa Elektronike e Kontablistit</label>
                                    <input type="email" name="email_kontablist" class="form-control" value="<?php echo htmlspecialchars($editcl['email_kontablist']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="nrtel" class="form-label" data-bs-toggle="tooltip" title="Numri i Telefonit të klientit">Numri i Telefonit</label>
                                    <input type="tel" name="nrtel" class="form-control" value="<?php echo htmlspecialchars($editcl['nrtel']); ?>" >
                                </div>
                                <div class="mb-3">
                                    <label for="bank_info" class="form-label" data-bs-toggle="tooltip" title="Emri i Bankës së klientit">Emri i Bankës</label>
                                    <select id="bank_info" name="bank_info" class="form-select" >
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
                                </div>
                                <div class="mb-3">
                                    <label for="nrllog" class="form-label" data-bs-toggle="tooltip" title="Numri i Xhirollogarisë së klientit">Numri i Xhirollogarisë</label>
                                    <input type="text" name="nrllog" class="form-control" value="<?php echo htmlspecialchars($editcl['nrllog']); ?>" >
                                </div>
                                <div class="mb-3">
                                    <label for="type_of_client" class="form-label" data-bs-toggle="tooltip" title="Lloji i Klientit">Lloji i Klientit</label>
                                    <select id="type_of_client" name="type_of_client" class="form-select" >
                                        <option value="Personal" <?php echo ($editcl['lloji_klientit'] == 'Personal') ? 'selected' : ''; ?>>Personal</option>
                                        <option value="Biznes" <?php echo ($editcl['lloji_klientit'] == 'Biznes') ? 'selected' : ''; ?>>Biznes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Të dhënat e Klientit (YouTube) -->
                    <div class="col-lg-3 col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-danger text-white">Të dhënat e Klientit (YouTube)</div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="yt" class="form-label" data-bs-toggle="tooltip" title="ID e Kanalit në YouTube">ID e Kanalit në YouTube</label>
                                    <input type="text" name="yt" class="form-control" value="<?php echo htmlspecialchars($editcl['youtube']); ?>" >
                                </div>
                                <div class="mb-3">
                                    <label for="emriart" class="form-label" data-bs-toggle="tooltip" title="Emri Artistik i klientit">Emri Artistik</label>
                                    <input type="text" name="emriart" class="form-control" value="<?php echo htmlspecialchars($editcl['emriart']); ?>" >
                                </div>
                                <div class="mb-3">
                                    <label for="dk" class="form-label" data-bs-toggle="tooltip" title="Data e Fillimit të Kontratës">Data e Fillimit të Kontratës</label>
                                    <input type="date" name="dk" id="dk" class="form-control" value="<?php echo htmlspecialchars($contractStartDate['data_e_krijimit'] ?? ''); ?>" >
                                </div>
                                <div class="mb-3">
                                    <label for="dks" class="form-label" data-bs-toggle="tooltip" title="Data e Skadimit të Kontratës">Data e Skadimit të Kontratës</label>
                                    <input type="date" name="dks" id="dks" class="form-control" value="<?php echo htmlspecialchars($expirationDateFormatted); ?>" >
                                </div>
                                <?php
                                $statusiQuery = "SELECT kg.youtube_id FROM kontrata_gjenerale kg 
                                                JOIN klientet k ON kg.youtube_id = k.youtube 
                                                WHERE k.youtube = '" . mysqli_real_escape_string($conn, $editcl['youtube']) . "'";
                                $statusiResult = $conn->query($statusiQuery);
                                if ($statusiResult && $statusiResult->num_rows == 0) {
                                    echo '<div class="mb-3">
                                            <label for="statusi_i_kontrates" class="form-label" data-bs-toggle="tooltip" title="Statusi i Kontratës">Statusi i Kontratës</label>
                                            <select class="form-select" name="statusi_i_kontrates" id="statusi_i_kontrates" >
                                                <option value="Kontratë fizike">Kontratë fizike</option>
                                                <option value="S\'ka kontratë">S\'ka kontratë</option>
                                            </select>
                                          </div>';
                                }
                                ?>
                                <div class="mb-3">
                                    <label class="form-label" for="kategoria" data-bs-toggle="tooltip" title="Zgjidhni Kategorinë e klientit">Zgjidh Kategorinë</label>
                                    <select class="form-select" name="kategoria" id="kategoria" >
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= htmlspecialchars($category); ?>" <?= ($userCategory == $category) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($category); ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <?php if (!$userCategory && empty($categories)): ?>
                                            <option value="Këngëtar">Këngëtar</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" data-bs-toggle="tooltip" title="A është monetizuar kanali?">Monetizuar?</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="min" id="po" value="PO" <?php echo ($editcl['monetizuar'] == "PO") ? 'checked' : ''; ?>>
                                        <label class="form-check-label text-success" for="po">PO</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="min" id="jo" value="JO" <?php echo ($editcl['monetizuar'] == "JO") ? 'checked' : ''; ?>>
                                        <label class="form-check-label text-muted" for="jo">JO</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="ads" class="form-label" data-bs-toggle="tooltip" title="Zgjidhni llogarinë e ADS">Edito Llogarinë e ADS:</label>
                                    <select class="form-select" name="ads" id="ads" >
                                        <?php
                                        $adsResult = $conn->query("SELECT * FROM ads");
                                        $ads_found = false;
                                        while ($ads = mysqli_fetch_assoc($adsResult)) {
                                            $selected = ($ads['id'] == $editcl['ads']) ? 'selected' : '';
                                            if ($selected) $ads_found = true;
                                            echo "<option value=\"" . htmlspecialchars($ads['id']) . "\" $selected>" . htmlspecialchars($ads['email']) . " | " . htmlspecialchars($ads['adsid']) . " (" . htmlspecialchars($ads['shteti']) . ")</option>";
                                        }
                                        if (!$ads_found && !empty($editcl['emri'])) {
                                            $adsid = $editcl['ads'];
                                            $adsData = $conn->query("SELECT * FROM ads WHERE id='" . mysqli_real_escape_string($conn, $adsid) . "'");
                                            if ($ads = mysqli_fetch_assoc($adsData)) {
                                                echo "<option value=\"" . htmlspecialchars($ads['id']) . "\" selected>" . htmlspecialchars($ads['email']) . " | " . htmlspecialchars($ads['adsid']) . " (" . htmlspecialchars($ads['shteti']) . ")</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Përqindja dhe Platformat -->
                    <div class="col-lg-3 col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-success text-white">Përqindja dhe Platformat</div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="emailp" class="form-label" data-bs-toggle="tooltip" title="Adresa Elektronike për Platforma">Adresa Elektronike për Platforma</label>
                                    <input type="email" name="emailp" class="form-control" value="<?php echo htmlspecialchars($editcl['emailp']); ?>" >
                                </div>
                                <div class="mb-3">
                                    <label for="perqindja" class="form-label" data-bs-toggle="tooltip" title="Përqindja e Bareshës">Përqindja (Baresha)</label>
                                    <input type="number" step="0.01" name="perqindja" class="form-control" value="<?php echo htmlspecialchars($editcl['perqindja']); ?>" >
                                </div>
                                <div class="mb-3">
                                    <label for="perqindja2" class="form-label" data-bs-toggle="tooltip" title="Përqindja për Platforma të Tjera">Përqindja për Platforma të Tjera (Baresha)</label>
                                    <input type="number" step="0.01" name="perqindja2" class="form-control" value="<?php echo htmlspecialchars($editcl['perqindja2']); ?>" >
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" data-bs-toggle="tooltip" title="Aktivizoni përqindjen e klientit">Përqindja e Klientit</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="perqindja_check" id="perqindja_check" <?php echo ($editcl['perqindja_check'] === "1") ? "checked" : ""; ?>>
                                        <label class="form-check-label" for="perqindja_check">Aktive</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" data-bs-toggle="tooltip" title="Aktivizoni përqindjen e platformave për klientin">Përqindja e Platformave për Klientin</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="perqindja_platformave_check" id="perqindja_platformave_check" <?php echo ($editcl['perqindja_platformave_check'] === "1") ? "checked" : ""; ?>>
                                        <label class="form-check-label" for="perqindja_platformave_check">Aktive</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="fb" class="form-label" data-bs-toggle="tooltip" title="URL e Facebook të klientit">Facebook URL:</label>
                                    <input type="url" name="fb" class="form-control" value="<?php echo htmlspecialchars($editcl['fb']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="ig" class="form-label" data-bs-toggle="tooltip" title="URL e Instagram të klientit">Instagram URL:</label>
                                    <input type="url" name="ig" class="form-control" value="<?php echo htmlspecialchars($editcl['ig']); ?>">
                                </div>
                                <div class="mb-3">
                                    <a href="perqindjet_klient.php?id=<?php echo htmlspecialchars($editcl['id']); ?>" class="input-custom-css px-3 py-2 text-decoration-none">Ndrysho Përqindjet</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Informacion i Brendshëm -->
                    <div class="col-lg-3 col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-info text-white">Informacion i Brendshëm</div>
                            <div class="card-body">
                                <?php if (!$contractUploaded): ?>
                                    <div class="mb-3">
                                        <label for="tipi" class="form-label" data-bs-toggle="tooltip" title="Ngarko kontratën në format PDF">Ngarko Kontratën:</label>
                                        <input type="file" name="tipi" id="tipi" accept="application/pdf" class="form-control" >
                                    </div>
                                    <div class="mb-3">
                                        <button type="button" class="input-custom-css px-3 py-2" data-bs-toggle="offcanvas" data-bs-target="#pdfPreviewOffcanvas">
                                            Preview PDF
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <div class="mb-3">
                                        <button type="button" class="input-custom-css px-3 py-2" data-bs-toggle="offcanvas" data-bs-target="#pdfPreviewOffcanvas">
                                            Shiko Kontratën
                                        </button>
                                    </div>
                                    <div class="mb-3">
                                        <button type="button" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#deleteContractModal">
                                            Fshi Kontratën
                                        </button>
                                    </div>
                                <?php endif; ?>
                                <!-- PDF Preview Offcanvas -->
                                <div class="offcanvas offcanvas-end" style="width: 60%;" tabindex="-1" id="pdfPreviewOffcanvas" aria-labelledby="pdfPreviewOffcanvasLabel">
                                    <div class="offcanvas-header">
                                        <h5 class="offcanvas-title" id="pdfPreviewOffcanvasLabel">Preview Kontratë</h5>
                                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                    </div>
                                    <div class="offcanvas-body">
                                        <?php if ($contractUploaded): ?>
                                            <iframe id="contractPreview" style="width: 100%; height: 500px; border: none;" src="<?php echo htmlspecialchars($editcl['kontrata']); ?>"></iframe>
                                        <?php else: ?>
                                            <iframe id="contractPreview" style="width: 100%; height: 500px; border: none;"></iframe>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if ($contractUploaded): ?>
                                    <!-- Delete Contract Modal -->
                                    <div class="modal fade" id="deleteContractModal" tabindex="-1" aria-labelledby="deleteContractModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteContractModalLabel">Fshi Kontratën Ekzistuese</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    A jeni i sigurt që dëshironi të fshini kontratën ekzistuese?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="input-custom-css px-3 py-2 bg-secondary text-white" data-bs-dismiss="modal">Anulo</button>
                                                    <button type="submit" class="input-custom-css px-3 py-2 bg-danger text-white" name="fshi_kontraten">Fshi</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="mb-3">
                                    <label for="emails" class="form-label" data-bs-toggle="tooltip" title="Zgjidhni emails për akses">Emails</label>
                                    <select multiple class="form-control" name="emails[]" id="emails" >
                                        <?php
                                        $emails_with_access = [];
                                        $emailsResult = $conn->query("SELECT emails FROM klientet WHERE id = '$editid'");
                                        if ($maillist = mysqli_fetch_assoc($emailsResult)) {
                                            $emails_with_access = array_map('trim', explode(',', $maillist['emails']));
                                        }
                                        $adsEmails = $conn->query("SELECT DISTINCT email FROM ads");
                                        while ($row = $adsEmails->fetch_assoc()) {
                                            $email = htmlspecialchars($row['email']);
                                            $selected = in_array($email, $emails_with_access) ? 'selected' : '';
                                            $status = $selected ? '(ka akses)' : '(nuk ka akses)';
                                            echo "<option value=\"$email\" $selected>$email $status</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="perdoruesi" class="form-label" data-bs-toggle="tooltip" title="Përdoruesi i sistemit">Përdoruesi <small>(Sistemit)</small>:</label>
                                    <input type="text" name="perdoruesi" class="form-control" value="<?php echo htmlspecialchars($editcl['perdoruesi']); ?>" >
                                </div>
                                <div class="mb-3">
                                    <label for="fjalkalimi" class="form-label" data-bs-toggle="tooltip" title="Vendosni fjalëkalimin e ri të sistemit">Fjalëkalimi <small>(Sistemit)</small>:</label>
                                    <input type="password" name="fjalkalimi" class="form-control" placeholder="Fjalëkalimi i sistemit">
                                    <button type="button" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#passwordInfoModal">Info</button>
                                    <!-- Password Info Modal -->
                                    <div class="modal fade" id="passwordInfoModal" tabindex="-1" aria-labelledby="passwordInfoModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="passwordInfoModalLabel">Informacione për Rivendosjen e Fjalëkalimit</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Nëse dëshironi të ndryshoni fjalëkalimin e sistemit, ju lutem shkruani një fjalëkalim të ri në fushën e lartme dhe klikoni butonin "Përditëso".
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="input-custom-css px-3 py-2 bg-secondary text-white" data-bs-dismiss="modal">Mbylle</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="shtetsia" class="form-label" data-bs-toggle="tooltip" title="Zgjidhni shtetësinë e klientit">Shtetësia</label>
                                    <select class="form-select" name="shtetsia" id="shtetsia" >
                                        <?php
                                        $staticOptions = ["Shqipëri", "Kosovë", "Gjermania", "Italia", "Zvicër", "Maqedonia", "Mali i Zi"];
                                        $dynamicOptions = [];
                                        $result = $conn->query("SELECT DISTINCT shtetsia FROM klientet WHERE id = '$editid'");
                                        if ($result) {
                                            while ($row = $result->fetch_assoc()) {
                                                $dynamicOptions[] = $row['shtetsia'];
                                            }
                                        }
                                        $options = array_unique(array_merge($staticOptions, $dynamicOptions));
                                        foreach ($options as $option) {
                                            $selected = ($editcl['shtetsia'] == $option) ? 'selected' : '';
                                            echo "<option value=\"" . htmlspecialchars($option) . "\" $selected>" . htmlspecialchars($option) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="shtetsiaKontabiliteti" class="form-label" data-bs-toggle="tooltip" title="Zgjidhni kontabilitetin">Kontabiliteti</label>
                                    <select class="form-select" name="shtetsiaKontabiliteti" id="shtetsiaKontabiliteti" >
                                        <?php
                                        $staticOptions = ["Kosova", "Shqipëri", "Gjermania", "Francë", "Slloveni"];
                                        $dynamicOptions = [];
                                        $result = $conn->query("SELECT DISTINCT shtetsiaKontabiliteti FROM klientet WHERE id = '$editid'");
                                        if ($result) {
                                            while ($row = $result->fetch_assoc()) {
                                                $dynamicOptions[] = $row['shtetsiaKontabiliteti'];
                                            }
                                        }
                                        $options = array_unique(array_merge($staticOptions, $dynamicOptions));
                                        foreach ($options as $option) {
                                            $selected = ($editcl['shtetsiaKontabiliteti'] == $option) ? 'selected' : '';
                                            echo "<option value=\"" . htmlspecialchars($option) . "\" $selected>" . htmlspecialchars($option) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="info" class="form-label" data-bs-toggle="tooltip" title="Informacione shtesë rreth klientit">Info Shtesë</label>
                                    <textarea class="form-control" name="info" id="info" rows="3" ><?php echo htmlspecialchars($editcl['info']); ?></textarea>
                                </div>
                                <div class="d-grid gap-2 text-center">
                                    <button type="submit" class="input-custom-css px-3 py-2" name="ndrysho"><i class="fi fi-rr-edit"></i> Ndrysho</button>
                                    <a href="kanal.php?kid=<?php echo htmlspecialchars($editcl['id']); ?>" class="input-custom-css px-3 py-2 text-decoration-none">
                                        <i class="fi fi-rr-eye"></i> Shiko Kanalin</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <!-- PDF Preview Offcanvas (Duplicate Removed) -->
        </div>
    </div>
</div>
<?php include 'partials/footer.php'; ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipiInput = document.getElementById('tipi');
        const contractPreview = document.getElementById('contractPreview');
        const pdfPreviewOffcanvas = new bootstrap.Offcanvas(document.getElementById('pdfPreviewOffcanvas'));
        tipiInput?.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.type === 'application/pdf') {
                    if (file.size <= 10000000) {
                        const fileUrl = URL.createObjectURL(file);
                        contractPreview.src = fileUrl;
                        pdfPreviewOffcanvas.show();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gabim',
                            text: 'Ju lutem ngarkoni skedarë PDF me madhësi deri në 10MB.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        tipiInput.value = '';
                        contractPreview.src = '';
                    }
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
            } else {
                contractPreview.src = '';
            }
        });
        <?php if ($contractUploaded): ?>
            contractPreview.src = '<?php echo htmlspecialchars($editcl['kontrata']); ?>';
        <?php endif; ?>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
<script>
    flatpickr("#dk", {
        dateFormat: "Y-m-d",
        maxDate: "today"
    });
    flatpickr("#dks", {
        dateFormat: "Y-m-d",
        minDate: "today"
    });
</script>