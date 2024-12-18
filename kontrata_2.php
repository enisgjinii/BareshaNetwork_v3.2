<?php
session_start();
include 'partials/header.php';
include 'conn-d.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];

try {
    $stmt = $conn->prepare("SELECT * FROM klientet ORDER BY emri ASC");
    $stmt->execute();
    $result = $stmt->get_result();
    $clients = $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];
} catch (Exception $e) {
    $clients = [];
    error_log("Error fetching clients: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = "Invalid CSRF token.";
    } else {
        $emri = trim($_POST['emri'] ?? '');
        $mbiemri = trim($_POST['mbiemri'] ?? '');
        $nrtel = trim($_POST['nrtel'] ?? '');
        $numri_personal = trim($_POST['numri_personal'] ?? '');
        $klienti = trim($_POST['klienti'] ?? '');
        $emailadd = trim($_POST['emailadd'] ?? '');
        $emriartistik = trim($_POST['emriartistik'] ?? '');
        $vepra = trim($_POST['vepra'] ?? '');
        $data = trim($_POST['data'] ?? '');
        $perqindja = trim($_POST['perqindja'] ?? '');
        $perqindja_other = trim($_POST['perqindja_other'] ?? '');
        $shenime = trim($_POST['shenime'] ?? '');

        if ($emri === '') {
            $errors[] = "Emri është i kërkuar.";
        }
        if ($mbiemri === '') {
            $errors[] = "Mbiemri është i kërkuar.";
        }
        if ($nrtel === '') {
            $errors[] = "Numri i telefonit është i kërkuar.";
        }
        if ($numri_personal === '') {
            $errors[] = "Numri personal është i kërkuar.";
        }
        if ($klienti === '') {
            $errors[] = "Klienti është i kërkuar.";
        }
        if ($emailadd === '') {
            $errors[] = "Adresa e Email-it është e kërkuar.";
        } elseif (!filter_var($emailadd, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Adresa e Email-it është e pavlefshme.";
        }
        if ($emriartistik === '') {
            $errors[] = "Emri Artistik është i kërkuar.";
        }
        if ($vepra === '') {
            $errors[] = "Vepra është e kërkuar.";
        }
        if ($data === '') {
            $errors[] = "Data është e kërkuar.";
        } elseif (!DateTime::createFromFormat('Y-m-d', $data)) {
            $errors[] = "Data duhet të jetë në formatin YYYY-MM-DD.";
        }
        if ($perqindja === '') {
            $errors[] = "Përqindja është e kërkuar.";
        } elseif (!is_numeric($perqindja) || $perqindja < 0 || $perqindja > 100) {
            $errors[] = "Përqindja duhet të jetë një numër midis 0 dhe 100.";
        }
        

        if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['pdf_file'];
            $fileName = preg_replace("/[^A-Za-z0-9.\-_]/", '', basename($file['name']));
            $uploadDir = 'uploads/contracts/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $filePath = $uploadDir . time() . "_" . $fileName;
            $allowedMimeTypes = ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/pdf'];
            if (!in_array($file['type'], $allowedMimeTypes)) {
                $errors[] = "Vetëm DOCX dhe PDF janë të lejuara për ngarkim.";
            }
            if ($file['size'] > 10 * 1024 * 1024) {
                $errors[] = "Madhësia e skedarit nuk duhet të tejkalojë 10MB.";
            }
            if (empty($errors)) {
                if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                    $errors[] = "Dështoi ngarkimi i skedarit të kontratës.";
                }
            }
        } else {
            $filePath = null;
        }

        if (empty($errors)) {
            try {
                $stmt = $conn->prepare("INSERT INTO kontrata (emri, mbiemri, numri_i_telefonit, numri_personal, klienti, klient_email, emriartistik, vepra, data, pdf_file, perqindja, shenim) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param(
                    "sssssssssdss",
                    $emri,
                    $mbiemri,
                    $nrtel,
                    $numri_personal,
                    $klienti,
                    $emailadd,
                    $emriartistik,
                    $vepra,
                    $data,
                    $filePath,
                    $perqindja,
                    $shenime
                );
                $stmt->execute();
                $success = true;
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } catch (Exception $e) {
                $errors[] = "Error saving contract: " . $e->getMessage();
                error_log("Error inserting contract: " . $e->getMessage());
            }
        }
    }
}
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/selectr/dist/selectr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/selectr/dist/selectr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-3 py-2 rounded my-3" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Kontratat</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Kontrata e Re (Këngë)</li>
                </ol>
            </nav>
            <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
                <?php if (isset($success) && $success): ?>
                    <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                Kontrata është ruajtur me sukses.
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($errors)): ?>
                    <div id="errorToast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <?php echo implode("<br>", array_map("htmlspecialchars", $errors)); ?>
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card p-4 shadow-sm rounded">
                <form id="contractForm" class="needs-validation" method="POST" enctype="multipart/form-data" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="emri" class="form-label" data-bs-toggle="tooltip" data-bs-placement="top" title="Shkruani emrin tuaj të plotë">Emri</label>
                            <input type="text" name="emri" id="emri" class="form-control rounded" placeholder="Sheno emrin tuaj" value="<?php echo htmlspecialchars($_POST['emri'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="mbiemri" class="form-label" data-bs-toggle="tooltip" data-bs-placement="top" title="Shkruani mbiemrin tuaj të plotë">Mbiemri</label>
                            <input type="text" name="mbiemri" id="mbiemri" class="form-control rounded" placeholder="Sheno mbiemrin tuaj" value="<?php echo htmlspecialchars($_POST['mbiemri'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="nrtel" class="form-label" data-bs-toggle="tooltip" data-bs-placement="top" title="Shkruani numrin tuaj të telefonit personal ose profesional">Numri i Telefonit</label>
                            <input type="tel" name="nrtel" id="nrtel" class="form-control rounded" placeholder="Sheno numrin e telefonit" value="<?php echo htmlspecialchars($_POST['nrtel'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="numri_personal" class="form-label" data-bs-toggle="tooltip" data-bs-placement="top" title="Numri personal do të plotësohet automatikisht nga të dhënat e klientit">Numri Personal</label>
                            <input type="text" name="numri_personal" id="numri_personal" class="form-control rounded" placeholder="Sheno numrin personal" value="<?php echo htmlspecialchars($_POST['numri_personal'] ?? ''); ?>" required>
                        </div>
                        <div class="col-12">
                            <label for="klienti22" class="form-label" data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni një klient nga lista e disponueshme">Klienti</label>
                            <select name="klienti" id="klienti22" class="form-select rounded" onchange="showClientDetails(this)" required>
                                <option value="" disabled <?php echo !isset($_POST['klienti']) ? 'selected' : ''; ?>>Zgjidhni një klient</option>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?php echo htmlspecialchars($client['emri'] . "|" . $client['emailadd'] . "|" . $client['emriart'] . "|" . $client['nrtel'] . "|" . $client['np']); ?>" <?php echo (isset($_POST['klienti']) && $_POST['klienti'] === ($client['emri'] . "|" . $client['emailadd'] . "|" . $client['emriart'] . "|" . $client['nrtel'] . "|" . $client['np'])) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($client['emri']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="emailadd" class="form-label" data-bs-toggle="tooltip" data-bs-placement="top" title="Adresa e Email-it do të plotësohet automatikisht nga të dhënat e klientit">Adresa e Email-it</label>
                            <input type="email" name="emailadd" id="emailadd" class="form-control rounded" placeholder="Sheno adresen e emailit" value="<?php echo htmlspecialchars($_POST['emailadd'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="emriartistik" class="form-label" data-bs-toggle="tooltip" data-bs-placement="top" title="Emri Artistik do të plotësohet automatikisht nga të dhënat e klientit">Emri Artistik</label>
                            <input type="text" name="emriartistik" id="emriartistik" class="form-control rounded" placeholder="Sheno emrin artistik (nëse aplikohet)" value="<?php echo htmlspecialchars($_POST['emriartistik'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="vepra" class="form-label" data-bs-toggle="tooltip" data-bs-placement="top" title="Shkruani emrin e veprës për të cilën po krijoni kontratën">Vepra</label>
                            <input type="text" name="vepra" id="vepra" class="form-control rounded" placeholder="Sheno emrin e veprës" value="<?php echo htmlspecialchars($_POST['vepra'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="data" class="form-label" data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni datën e nënshkrimit të kontratës">Data</label>
                            <input type="text" name="data" id="data" class="form-control rounded" placeholder="Zgjidhni datën e kontratës" value="<?php echo htmlspecialchars($_POST['data'] ?? ''); ?>" required>
                        </div>
                        <div class="col-12">
                            <label for="pdf_file" class="form-label" data-bs-toggle="tooltip" data-bs-placement="top" title="Ngarkoni një kopje të kontratës në format DOCX ose PDF">Ngarko Kontratën</label>
                            <input type="file" name="pdf_file" id="pdf_file" class="form-control rounded" accept=".docx,.pdf" onchange="validateFile(this)">
                            <small class="form-text text-muted">Formatet e lejuara: DOCX, PDF. Madhësia maksimale: 10MB.</small>
                        </div>
                        <div class="col-md-6">
                            <label for="perqindja" class="form-label" data-bs-toggle="tooltip" data-bs-placement="top" title="Shkruani përqindjen tuaj të përfitimit nga kontrata">Përqindja (Baresha)</label>
                            <div class="input-group">
                                <input type="number" name="perqindja" id="perqindja" class="form-control rounded" placeholder="Përqindja tuaj (%)" min="0" max="100" step="0.01" onchange="updatePerqindjaOther()" value="<?php echo htmlspecialchars($_POST['perqindja'] ?? ''); ?>" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="perqindja_other" class="form-label" data-bs-toggle="tooltip" data-bs-placement="top" title="Përqindja që do të përfitojë klienti bazuar në përqindjen tuaj">Përqindja (Klienti)</label>
                            <div class="input-group">
                                <input type="number" name="perqindja_other" id="perqindja_other" class="form-control rounded" placeholder="Përqindja e klientit (%)" readonly value="<?php echo htmlspecialchars($_POST['perqindja_other'] ?? ''); ?>" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="shenime" class="form-label" data-bs-toggle="tooltip" data-bs-placement="top" title="Shkruani çdo informacion shtesë ose shënim të rëndësishëm për këtë kontratë">Shënime</label>
                            <textarea name="shenime" id="shenime" class="form-control rounded" rows="3" placeholder="Shënime shtesë..."><?php echo htmlspecialchars($_POST['shenime'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary rounded px-4" data-bs-toggle="tooltip" data-bs-placement="top" title="Klikoni për të dërguar kontratën">Dërgo</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include('partials/footer.php'); ?>
<script>
    new Selectr('#klienti22', {
        searchable: true,
        clearable: true,
        placeholder: 'Zgjidhni një klient'
    });

    flatpickr("#data", {
        dateFormat: "Y-m-d",
        maxDate: "today",
        allowInput: true
    });

    function showClientDetails(select) {
        if (select.value) {
            const [emri, emailadd, emriartistik, nrtel, np] = select.value.split("|");
            document.getElementById("emailadd").value = emailadd || "";
            document.getElementById("emriartistik").value = emriartistik || "";
            document.getElementById("nrtel").value = nrtel || "";
            document.getElementById("numri_personal").value = np || "";
        } else {
            document.getElementById("emailadd").value = "";
            document.getElementById("emriartistik").value = "";
            document.getElementById("nrtel").value = "";
            document.getElementById("numri_personal").value = "";
        }
    }

    function updatePerqindjaOther() {
        const perqindja = parseFloat(document.getElementById('perqindja').value);
        document.getElementById('perqindja_other').value = isNaN(perqindja) ? "" : (100 - perqindja).toFixed(2);
    }

    function validateFile(input) {
        const file = input.files[0];
        const maxSize = 10 * 1024 * 1024;
        const allowedTypes = ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/pdf'];
        if (file) {
            if (!allowedTypes.includes(file.type)) {
                showToast('errorToast', 'Lloj Skedari i Pavlefshëm', 'Vetëm DOCX dhe PDF lejohet.');
                input.value = '';
                return;
            }
            if (file.size > maxSize) {
                showToast('errorToast', 'Skedar i Madh', 'Madhësia e skedarit tejkalon 10MB.');
                input.value = '';
                return;
            }
        }
    }

    function showToast(toastId, title, message) {
        const toastElement = document.getElementById(toastId);
        if (toastElement) {
            const toast = new bootstrap.Toast(toastElement);
            const toastBody = toastElement.querySelector('.toast-body');
            toastBody.innerHTML = `<strong>${title}:</strong> ${message}`;
            toast.show();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        var form = document.getElementById('contractForm');
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);

        <?php if (isset($success) && $success): ?>
            var successToastEl = document.getElementById('successToast');
            var successToast = new bootstrap.Toast(successToastEl);
            successToast.show();
            successToastEl.addEventListener('hidden.bs.toast', function() {
                document.getElementById('contractForm').reset();
                const selectrInstance = Selectr.instances.get(document.querySelector('#klienti22'));
                if (selectrInstance) {
                    selectrInstance.clear();
                }
                const fp = flatpickr("#data");
                if (fp.length > 0) {
                    fp[0].clear();
                }
                document.getElementById("emailadd").value = "";
                document.getElementById("emriartistik").value = "";
                document.getElementById("nrtel").value = "";
                document.getElementById("numri_personal").value = "";
                document.getElementById("perqindja_other").value = "";
            });
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            var errorToastEl = document.getElementById('errorToast');
            var errorToast = new bootstrap.Toast(errorToastEl);
            errorToast.show();
        <?php endif; ?>
    });
</script>