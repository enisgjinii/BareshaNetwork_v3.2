<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'partials/header.php';
$mesazhi_sukses = $mesazhi_error = "";

function getTooltip($fieldName, $label)
{
    $tooltips = [
        'emri' => "Emri dhe Mbiemri: Shkruani emrin e plotë të klientit, si Emri dhe Mbiemri.",
        'numri_tel' => "Numri i Telefonit: Shkruani një numër telefoni të vlefshëm për kontakt me klientin. P.sh., +383 44 444 444",
        'numri_personal' => "Numri Personal: Shkruani numrin personal të identifikimit të klientit (NIPT ose një numër tjetër identifikues).",
        'email' => "Adresa e Email-it: Shkruani një adresë email-i të vlefshme për komunikim me klientin. P.sh., klienti@example.com.",
        'youtube_id' => "ID-ja e Kanalit në YouTube: Shkruani identifikuesin unik të kanalit të YouTube të klientit. P.sh., UCPveY6zZb2O3YUSI20JftyQ.",
        'emriartistik' => "Emri Artistik: Shkruani emrin artistik të klientit nëse ai/ajo përdor një emër tjetër për publikun.",
        'numri_xhiroBanka' => "Numri i Xhirollogarisë Bankare: Shkruani numrin e saktë të llogarisë bankare të klientit.",
        'tvsh' => "Përqindja (Klientit): Shkruani përqindjen midis 0 dhe 100.",
        'pronari_xhiroBanka' => "Pronari i Xhirollogarisë Bankare: Emri i pronarit të llogarisë.",
        'kodi_swift' => "Kodi SWIFT: Kodi SWIFT i bankës së klientit.",
        'iban' => "IBAN: Numri i llogarisë ndërkombëtare.",
        'emri_bankes' => "Emri i Bankës: Emri i plotë i bankës.",
        'adresa_bankes' => "Adresa e Bankës: Adresa e plotë e bankës.",
        'kohezgjatja' => "Kohëzgjatja në Muaj: Shkruani numrin e muajve.",
        'lloji_dokumentit' => "Lloji i Dokumentit: Zgjidhni llojin e dokumentit.",
        'shenim' => "Shenim: Shtoni çdo shënim të nevojshëm."
    ];
    return isset($tooltips[$fieldName]) ? $tooltips[$fieldName] : "Plotësoni këtë fushë.";
}

function sendContractEmail($contractDetails, $attachments, $status)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'egjini@bareshamusic.com';
        $mail->Password = 'lcgglivhzwmpuyui';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('egjini@bareshamusic.com', 'Contract System');
        $mail->addAddress('egjini17@gmail.com', 'Egjin');
        $mail->addAddress('kastriot@bareshamusic.com', 'Kastriot');

        if (!empty($attachments['tmp_name'])) {
            foreach ($attachments['tmp_name'] as $index => $tmpName) {
                if (is_uploaded_file($tmpName)) {
                    $mail->addAttachment($tmpName, $attachments['name'][$index]);
                }
            }
        }

        $mail->isHTML(true);
        if ($status === 'error') {
            $mail->Subject = 'Gabim në Dorëzimin e Kontratës';
            $mail->Body = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Gabim në Dorëzimin e Kontratës</title>
<style>
body {
    margin:0; padding:0; background:#f4f4f4; font-family:Arial,sans-serif;
}
.container {
    max-width:600px; margin:40px auto; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.1);
}
.header {
    background:#dc3545; color:#fff; padding:20px; text-align:center;
}
.header h1 {
    margin:0; font-size:24px;
}
.body {
    padding:20px; color:#333;
}
.body h2 {
    margin-top:0; color:#dc3545;
}
.details {
    background:#fafafa; padding:15px; border-radius:5px; white-space:pre-wrap; font-size:14px; margin-bottom:20px;
    border:1px solid #eee;
}
.footer {
    background:#f4f4f4; text-align:center; padding:10px; font-size:12px; color:#777;
}
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Bareshamusic</h1>
    </div>
    <div class="body">
        <h2>Gabim në Dorëzimin e Kontratës</h2>
        <p>Kontrata juaj përfundoi me disa gabime. Detajet janë si vijon:</p>
        <div class="details">' . nl2br(htmlspecialchars($contractDetails)) . '</div>
        <p>Ju lutemi përmirësoni gabimet dhe provoni përsëri.</p>
    </div>
    <div class="footer">
        © '.date("Y").' Bareshamusic. Të drejtat e rezervuara.
    </div>
</div>
</body>
</html>';
        } else {
            $mail->Subject = 'Kontratë e Re Dorëzuar';
            $mail->Body = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Kontratë e Re Dorëzuar</title>
<style>
body {
    margin:0; padding:0; background:#f4f4f4; font-family:Arial,sans-serif;
}
.container {
    max-width:600px; margin:40px auto; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.1);
}
.header {
    background:#dc3545; /* Changed from #0d6efd to #dc3545 */
    color:#fff; 
    padding:20px; 
    text-align:center;
}
.header h1 {
    margin:0; font-size:24px;
}
.body {
    padding:20px; color:#333;
}
.body h2 {
    margin-top:0; color:#dc3545;
}
.details {
    background:#fafafa; padding:15px; border-radius:5px; white-space:pre-wrap; font-size:14px; margin-bottom:20px;
    border:1px solid #eee;
}
.footer {
    background:#f4f4f4; text-align:center; padding:10px; font-size:12px; color:#777;
}
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Bareshamusic</h1>
    </div>
    <div class="body">
        <h2>Kontratë e Dorëzuar me Sukses</h2>
        <p>Kontrata juaj u krijua me sukses! Detajet janë si vijon:</p>
        <div class="details">' . nl2br(htmlspecialchars($contractDetails)) . '</div>
        <p>Faleminderit për bashkëpunimin!</p>
    </div>
    <div class="footer">
        © '.date("Y").' Bareshamusic. Të drejtat e rezervuara.
    </div>
</div>
</body>
</html>';
        }
        $mail->CharSet = 'UTF-8';
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

$clients = [];
$mesazhi_error_db = "";

$sql = "SELECT emri, emailadd, emriart, youtube, nrllog, (100 - perqindja) AS perqindja, np, nrtel 
        FROM klientet 
        WHERE aktiv != ? OR aktiv IS NULL
        ORDER BY emri ASC";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $aktiv_value = '1';
    $stmt->bind_param("s", $aktiv_value);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $clients = $result->fetch_all(MYSQLI_ASSOC);
        if (empty($clients)) {
            $mesazhi_error_db = "Nuk u gjetën kliente të përshtatshme.";
        }
        $result->free();
    } else {
        $mesazhi_error_db = "Gabim në ekzekutimin e pyetjes: " . htmlspecialchars($stmt->error);
    }
    $stmt->close();
} else {
    $mesazhi_error_db = "Gabim në përgatitjen e pyetjes: " . htmlspecialchars($conn->error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $artisti_json = $_POST['artisti'] ?? '';
    $artisti = json_decode($artisti_json, true);

    $emri = htmlspecialchars(trim($_POST['emri'] ?? ''));
    $numri_tel = htmlspecialchars(trim($_POST['numri_tel'] ?? ''));
    $numri_personal = htmlspecialchars(trim($_POST['numri_personal'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $youtube_id = htmlspecialchars(trim($_POST['youtube_id'] ?? ''));
    $emriartistik = htmlspecialchars(trim($_POST['emriartistik'] ?? ''));
    $numri_xhiroBanka = htmlspecialchars(trim($_POST['numri_xhiroBanka'] ?? ''));
    $tvsh = htmlspecialchars(trim($_POST['tvsh'] ?? ''));
    $pronari_xhiroBanka = htmlspecialchars(trim($_POST['pronari_xhiroBanka'] ?? ''));
    $kodi_swift = htmlspecialchars(trim($_POST['kodi_swift'] ?? ''));
    $iban = htmlspecialchars(trim($_POST['iban'] ?? ''));
    $emri_bankes = htmlspecialchars(trim($_POST['emri_bankes'] ?? ''));
    $adresa_bankes = htmlspecialchars(trim($_POST['adresa_bankes'] ?? ''));
    $kohezgjatja = htmlspecialchars(trim($_POST['kohezgjatja'] ?? ''));
    $lloji_dokumentit = htmlspecialchars(trim($_POST['lloji_dokumentit'] ?? ''));
    $shenim = htmlspecialchars(trim($_POST['shenim'] ?? ''));

    $validation_errors = [];

    // Since all fields are optional, we only validate if they are filled
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $validation_errors[] = "Email-i është i pavlefshëm.";
    }
    if (!empty($tvsh)) {
        if (!is_numeric($tvsh) || $tvsh < 0 || $tvsh > 100) {
            $validation_errors[] = "Përqindja duhet të jetë numër midis 0 dhe 100.";
        }
    }
    if (!empty($kohezgjatja)) {
        if (!is_numeric($kohezgjatja) || $kohezgjatja < 1) {
            $validation_errors[] = "Kohëzgjatja duhet të jetë një numër pozitiv.";
        }
    }

    $contractDetails = "Klienti: " . ($emri ?: '---') . "\n"
        . "Email: " . ($email ?: '---') . "\n"
        . "Emri Artistik: " . ($emriartistik ?: '---') . "\n"
        . "Numri Personal: " . ($numri_personal ?: '---') . "\n"
        . "YouTube ID: " . ($youtube_id ?: '---') . "\n"
        . "Numri i Xhirollogarisë: " . ($numri_xhiroBanka ?: '---') . "\n"
        . "Kodi SWIFT: " . ($kodi_swift ?: '---') . "\n"
        . "IBAN: " . ($iban ?: '---') . "\n"
        . "Emri i Bankës: " . ($emri_bankes ?: '---') . "\n"
        . "Adresa e Bankës: " . ($adresa_bankes ?: '---') . "\n"
        . "Përqindja: " . ($tvsh ?: '---') . "%\n"
        . "Kohëzgjatja: " . ($kohezgjatja ?: '---') . " muaj\n"
        . "Lloji i Dokumentit: " . ($lloji_dokumentit ?: '---') . "\n"
        . "Shenim: " . ($shenim ?: '---');

    if (!empty($validation_errors)) {
        $errorDetails = "Ekzistojnë disa gabime në kontratë:\n";
        foreach ($validation_errors as $error) {
            $errorDetails .= "- " . $error . "\n";
        }
        $contractDetails .= "\n" . $errorDetails;

        $emailSent = sendContractEmail($contractDetails, $_FILES['documents'], 'error');

        if ($emailSent) {
            $mesazhi_error = "Kontrata nuk u krijua me sukses. Një kopje e detajeve është dërguar te administratori.";
        } else {
            $mesazhi_error = "Kontrata nuk u krijua me sukses dhe dërgimi i email-it dështoi.";
        }

        $_SESSION['mesazhi_error'] = $mesazhi_error;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $insert_sql = "INSERT INTO kontrata_gjenerale (emri, email, numri_personal, youtube_id, numri_xhirollogarise, kodi_swift, iban, emri_bankes, adresa_bankes, tvsh, kohezgjatja, lloji_dokumentit, shenim) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        if ($stmt) {
            $stmt->bind_param("sssssssssssss", $emri, $email, $numri_personal, $youtube_id, $numri_xhiroBanka, $kodi_swift, $iban, $emri_bankes, $adresa_bankes, $tvsh, $kohezgjatja, $lloji_dokumentit, $shenim);
            if ($stmt->execute()) {
                // Successfully inserted into the database
            } else {
                $validation_errors[] = "Gabim gjatë futjes së të dhënave në bazën e të dhënave.";
                $contractDetails .= "\n" . implode("\n", $validation_errors);
            }
            $stmt->close();
        } else {
            $validation_errors[] = "Gabim në përgatitjen e pyetjes: " . htmlspecialchars($conn->error);
            $contractDetails .= "\n" . implode("\n", $validation_errors);
        }

        if (empty($validation_errors)) {
            $emailSent = sendContractEmail($contractDetails, $_FILES['documents'], 'success');
            if ($emailSent) {
                $mesazhi_sukses = "Kontrata u krijua me sukses. Një kopje është dërguar te administratori.";
            } else {
                $mesazhi_error = "Kontrata u krijua me sukses, por dërgimi i email-it dështoi.";
            }
        } else {
            $emailSent = sendContractEmail($contractDetails, $_FILES['documents'], 'error');
            if ($emailSent) {
                $mesazhi_error = "Kontrata nuk u krijua me sukses. Një kopje e detajeve është dërguar te administratori.";
            } else {
                $mesazhi_error = "Kontrata nuk u krijua me sukses dhe dërgimi i email-it dështoi.";
            }
        }

        if ($emailSent) {
            $_SESSION['mesazhi_sukses'] = $mesazhi_sukses;
        } else {
            $_SESSION['mesazhi_error'] = $mesazhi_error;
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Krijimi i Kontratës së Re</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fonticons/1.0.0/fonticons.min.css" integrity="sha512-YOUR_INTEGRITY_HASH" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/selectr/dist/selectr.min.css">
    <style>
        .dropzone {
            border: 2px dashed #ced4da;
            border-radius: 0.5rem;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s;
            position: relative;
        }

        .dropzone:hover {
            background-color: #f1f1f1;
        }

        .dropzone.dragover {
            background-color: #e2e6ea;
        }

        .dropzone input[type="file"] {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            top: 0;
            left: 0;
        }

        #filePreview img {
            margin-bottom: 10px;
            max-width: 100px;
            max-height: 100px;
        }

        #filePreview i {
            color: #6c757d;
            font-size: 1.5rem;
        }

        #contractPreview {
            background-color: #f8f9fa;
            min-height: 300px;
        }

        .input-custom-css {
            background-color: #0d6efd;
            color: white;
            border: none;
            border-radius: 0.5rem;
        }

        .input-custom-css:hover {
            background-color: #0b5ed7;
        }

        .preview-section {
            position: sticky;
            top: 20px;
        }

        @media (max-width: 992px) {
            .preview-section {
                position: static;
            }
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <?php
        if (isset($_SESSION['mesazhi_sukses']) && !empty($_SESSION['mesazhi_sukses'])):
        ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['mesazhi_sukses']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php
            unset($_SESSION['mesazhi_sukses']);
        endif;

        if (isset($_SESSION['mesazhi_error']) && !empty($_SESSION['mesazhi_error'])):
        ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['mesazhi_error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php
            unset($_SESSION['mesazhi_error']);
        endif;

        if (!empty($mesazhi_error_db)):
        ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($mesazhi_error_db) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-white px-3 py-2 rounded-5 shadow-sm">
                        <li class="breadcrumb-item"><a href="#" class="text-reset text-decoration-none">Kontratat</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Kontrata e Re (Gjenerale)</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <!-- Contract Creation Form -->
            <div class="col-lg-6">
                <div class="card rounded-5 border shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title mb-4 text-center">Krijimi i Kontratës së Re</h4>
                        <form id="contractForm" method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data" onsubmit="return validateForm()">
                            <div class="mb-3">
                                <label for="artisti" class="form-label">Klienti</label>
                                <select name="artisti" id="artisti" class="form-select rounded-5" onchange="populateFields(this);" data-bs-toggle="tooltip" data-bs-placement="right" title="Zgjidhni një klient nga lista. Zgjedhja automatikisht plotëson disa fusha të tjera si Emri, Email, dhe Përqindja e Klientit.">
                                    <option value="" selected disabled>Zgjidhni një klient</option>
                                    <?php foreach ($clients as $client): ?>
                                        <option value="<?= htmlspecialchars(json_encode($client)) ?>">
                                            <?= htmlspecialchars($client['emri']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">
                                    <strong>Total Kliente:</strong> <?= count($clients) ?>
                                </div>
                                <div class="invalid-feedback">
                                    Ju lutem zgjidhni një klient.
                                </div>
                            </div>

                            <?php
                            $fields = [
                                'emri' => 'Emri dhe Mbiemri',
                                'numri_tel' => 'Numri i Telefonit',
                                'numri_personal' => 'Numri Personal',
                                'email' => 'Adresa e Email-it',
                                'youtube_id' => 'ID-ja e Kanalit në YouTube',
                                'emriartistik' => 'Emri Artistik',
                                'numri_xhiroBanka' => 'Numri i Xhirollogarisë Bankare',
                                'tvsh' => 'Përqindja (Klientit)',
                                'pronari_xhiroBanka' => 'Pronari i Xhirollogarisë Bankare',
                                'kodi_swift' => 'Kodi SWIFT',
                                'iban' => 'IBAN',
                                'emri_bankes' => 'Emri i Bankës',
                                'adresa_bankes' => 'Adresa e Bankës'
                            ];
                            foreach ($fields as $name => $label):
                            ?>
                                <div class="mb-3">
                                    <label for="<?= $name ?>" class="form-label"><?= $label ?></label>
                                    <input type="text" name="<?= $name ?>" id="<?= $name ?>" class="form-control rounded-5" placeholder="Shëno <?= strtolower($label) ?>"
                                        <?= in_array($name, ['emri', 'numri_tel', 'numri_personal', 'email', 'youtube_id', 'emriartistik']) ? 'readonly' : '' ?>
                                        title="<?= getTooltip($name, $label) ?>" data-bs-toggle="tooltip" data-bs-placement="right">
                                    <div class="invalid-feedback">
                                        Ju lutem plotësoni këtë fushë.
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <div class="mb-3">
                                <label for="kohezgjatja" class="form-label">Kohëzgjatja në Muaj</label>
                                <input type="number" name="kohezgjatja" id="kohezgjatja" class="form-control rounded-5" placeholder="Shëno kohëzgjatjen e kontratës" min="1"
                                    title="Vendosni numrin e muajve për sa kohë do të qëndrojë kontrata aktive. P.sh., 12 për një vit." data-bs-toggle="tooltip" data-bs-placement="right">
                                <div class="invalid-feedback">
                                    Ju lutem vendosni një kohëzgjatje të vlefshme.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="lloji_dokumentit" class="form-label">Lloji i Dokumentit</label>
                                <select name="lloji_dokumentit" id="lloji_dokumentit" class="form-select rounded-5" data-bs-toggle="tooltip" data-bs-placement="right" title="Zgjidhni llojin e dokumentit që po ngarkoni.">
                                    <option value="" selected disabled>Zgjidhni llojin e dokumentit</option>
                                    <option value="patente_shoferi">Patentë Shoferi</option>
                                    <option value="leternjoftim">Letërnjoftim</option>
                                    <option value="pasaporte">Pasaporte</option>
                                </select>
                                <div class="invalid-feedback">
                                    Ju lutem zgjidhni llojin e dokumentit.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="shenim" class="form-label">Shenim</label>
                                <textarea name="shenim" id="shenim" class="form-control rounded-5" placeholder="Shëno shenimin e kontratës" rows="3"
                                    title="Shtoni çdo shënim të nevojshëm për kontratën, si kushtet specifike apo detajet shtesë që mund të jenë të rëndësishme." data-bs-toggle="tooltip" data-bs-placement="right"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="fileUpload" class="form-label">Ngarkoni Dokumentet</label>
                                <div class="dropzone rounded-5 shadow-sm" id="dropzone" data-bs-toggle="tooltip" data-bs-placement="right" title="Ngarkoni dokumentet e nevojshme për kontratën. Mbështeten format .pdf, .doc, .docx, .jpg, .jpeg, dhe .png.">
                                    <input type="file" name="documents[]" id="fileUpload" class="form-control" multiple accept=".pdf, .doc, .docx, .jpg, .jpeg, .png">
                                    <p class="mt-2">Drag & Drop files këtu ose klikoni për të zgjedhur.</p>
                                </div>
                                <div id="filePreview" class="mt-3 d-flex flex-wrap gap-3"></div>
                                <div class="invalid-feedback d-block" id="fileUploadFeedback">
                                    Ju lutem ngarkoni të paktën një dokument.
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn input-custom-css py-2">
                                    <i class="fi fi-rr-memo-circle-check me-2"></i>Krijo Kontratën
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Live Preview Section -->
            <div class="col-lg-6 preview-section">
                <div class="card rounded-5 border shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3 text-center">Parashikim i Kontratës</h5>
                        <div id="contractPreview" class="p-4 border rounded-3 bg-light">
                            <h6><strong>Klienti:</strong> <span id="previewEmri">---</span></h6>
                            <p><strong>Email:</strong> <span id="previewEmail">---</span></p>
                            <p><strong>Emri Artistik:</strong> <span id="previewEmriArtistik">---</span></p>
                            <p><strong>ID YouTube:</strong> <span id="previewYouTube">---</span></p>
                            <p><strong>Numri Telefonit:</strong> <span id="previewNumriTel">---</span></p>
                            <p><strong>Numri Personal:</strong> <span id="previewNumriPersonal">---</span></p>
                            <p><strong>Numri Xhirollogarisë:</strong> <span id="previewNumriXhiroBanka">---</span></p>
                            <p><strong>Përqindja:</strong> <span id="previewPerqindja">---</span>%</p>
                            <p><strong>Kohëzgjatja:</strong> <span id="previewKohezgjatja">---</span> muaj</p>
                            <p><strong>Shenim:</strong> <span id="previewShenim">---</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- File Preview Modal -->
    <div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview e Dokumentit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Mbyll"></button>
                </div>
                <div class="modal-body">
                    <iframe id="filePreviewIframe" src="" width="100%" height="600px"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Selectr JS -->
    <script src="https://cdn.jsdelivr.net/npm/selectr/dist/selectr.min.js"></script>
    <!-- Font Icons JS (if needed) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fonticons/1.0.0/fonticons.min.js" integrity="sha512-YOUR_INTEGRITY_HASH" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll("[data-bs-toggle='tooltip']"));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            new Selectr("#artisti", {
                placeholder: "Zgjidhni një klient",
                searchPlaceholder: "Zgjidhni një klient",
                searchable: true,
                showSearch: true
            });

            const formFields = {
                emri: "previewEmri",
                email: "previewEmail",
                emriartistik: "previewEmriArtistik",
                youtube_id: "previewYouTube",
                numri_tel: "previewNumriTel",
                numri_personal: "previewNumriPersonal",
                numri_xhiroBanka: "previewNumriXhiroBanka",
                tvsh: "previewPerqindja",
                kohezgjatja: "previewKohezgjatja",
                shenim: "previewShenim"
            };

            Object.keys(formFields).forEach(field => {
                const input = document.getElementById(field);
                const preview = document.getElementById(formFields[field]);
                if (input && preview) {
                    input.addEventListener("input", () => {
                        preview.textContent = input.value.trim() || "---";
                    });
                }
            });

            window.populateFields = function (select) {
                if (select.value === "") return;
                const selectedData = JSON.parse(select.value);
                const mapping = {
                    emri: selectedData.emri || "",
                    email: selectedData.emailadd || "",
                    emriartistik: selectedData.emriart || "",
                    youtube_id: selectedData.youtube || "",
                    numri_tel: selectedData.nrtel || "",
                    numri_personal: selectedData.np || "",
                    numri_xhiroBanka: selectedData.nrllog || "",
                    tvsh: selectedData.perqindja || ""
                };
                for (const [field, value] of Object.entries(mapping)) {
                    const input = document.getElementById(field);
                    if (input) {
                        input.value = sanitize(value);
                        if (value) {
                            input.setAttribute("readonly", "readonly");
                        } else {
                            input.removeAttribute("readonly");
                        }
                    }
                    const preview = document.getElementById(formFields[field]);
                    if (preview) {
                        preview.textContent = value.trim() || "---";
                    }
                }
                const additionalFields = ["pronari_xhiroBanka", "kodi_swift", "iban", "emri_bankes", "adresa_bankes"];
                additionalFields.forEach(field => {
                    const input = document.getElementById(field);
                    if (input) {
                        input.value = "";
                        input.removeAttribute("readonly");
                    }
                });
            }

            function sanitize(value) {
                const temp = document.createElement("div");
                temp.textContent = value;
                return temp.innerHTML;
            }

            window.validateForm = function () {
                let isValid = true;
                const form = document.getElementById("contractForm");

                const inputs = form.querySelectorAll("input, select, textarea");
                inputs.forEach(input => {
                    input.classList.remove("is-invalid");
                });

                const tvshField = document.getElementById("tvsh");
                const perqindja = parseFloat(tvshField.value);
                if (tvshField.value && (isNaN(perqindja) || perqindja < 0 || perqindja > 100)) {
                    tvshField.classList.add("is-invalid");
                    isValid = false;
                }

                const kohezgjatjaField = document.getElementById("kohezgjatja");
                const kohezgjatja = parseFloat(kohezgjatjaField.value);
                if (kohezgjatjaField.value && (isNaN(kohezgjatja) || kohezgjatja < 1)) {
                    kohezgjatjaField.classList.add("is-invalid");
                    isValid = false;
                }

                const fileInput = document.getElementById("fileUpload");
                if (fileInput.files.length > 0) {
                    const allowedTypes = ["application/pdf", "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "image/jpeg", "image/png"];
                    const maxSize = 5 * 1024 * 1024;
                    for (let i = 0; i < fileInput.files.length; i++) {
                        const file = fileInput.files[i];
                        if (!allowedTypes.includes(file.type)) {
                            alert(`Lloji i skedarit ${file.name} nuk është i lejuar.`);
                            fileInput.classList.add("is-invalid");
                            isValid = false;
                            break;
                        }
                        if (file.size > maxSize) {
                            alert(`Skedari ${file.name} tejkalon madhësinë maksimale prej 5MB.`);
                            fileInput.classList.add("is-invalid");
                            isValid = false;
                            break;
                        }
                    }
                }

                return isValid;
            }

            const fileUpload = document.getElementById("fileUpload");
            const filePreview = document.getElementById("filePreview");
            fileUpload.addEventListener("change", handleFileSelect);

            function handleFileSelect(event) {
                const files = event.target.files;
                filePreview.innerHTML = "";
                if (files.length === 0) {
                    filePreview.innerHTML = "<p class=\"text-muted\">Nuk ka skedarë të ngarkuar.</p>";
                    return;
                }
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const fileURL = URL.createObjectURL(file);
                    const fileType = file.type.startsWith("image/") ? "image" : "document";
                    const fileContainer = document.createElement("div");
                    fileContainer.classList.add("d-flex", "align-items-center", "gap-3");

                    if (fileType === "image") {
                        const img = document.createElement("img");
                        img.src = fileURL;
                        img.alt = file.name;
                        img.classList.add("img-thumbnail");
                        img.style.width = "80px";
                        img.style.height = "80px";
                        img.style.objectFit = "cover";
                        fileContainer.appendChild(img);
                    } else {
                        const icon = document.createElement("i");
                        icon.classList.add("fi", "fi-rr-file-alt", "text-secondary");
                        fileContainer.appendChild(icon);
                    }

                    const fileName = document.createElement("span");
                    fileName.textContent = file.name;
                    fileName.classList.add("text-primary", "text-decoration-underline", "cursor-pointer");
                    fileName.style.cursor = "pointer";
                    fileName.addEventListener("click", () => {
                        const iframe = document.getElementById("filePreviewIframe");
                        iframe.src = fileURL;
                        const myModal = new bootstrap.Modal(document.getElementById("filePreviewModal"));
                        myModal.show();
                    });
                    fileContainer.appendChild(fileName);
                    filePreview.appendChild(fileContainer);
                }
            }

            const dropzone = document.getElementById("dropzone");
            dropzone.addEventListener("dragover", (event) => {
                event.preventDefault();
                dropzone.classList.add("dragover");
            });
            dropzone.addEventListener("dragleave", (event) => {
                event.preventDefault();
                dropzone.classList.remove("dragover");
            });
            dropzone.addEventListener("drop", (event) => {
                event.preventDefault();
                dropzone.classList.remove("dragover");
                const files = event.dataTransfer.files;
                fileUpload.files = files;
                handleFileSelect({ target: { files: files } });
            });
            ["dragenter", "dragover", "dragleave", "drop"].forEach(eventName => {
                document.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                });
            });
        });
    </script>

    <!-- File Preview Modal -->
    <div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview e Dokumentit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Mbyll"></button>
                </div>
                <div class="modal-body">
                    <iframe id="filePreviewIframe" src="" width="100%" height="600px"></iframe>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
