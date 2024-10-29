<?php
// Start the session for CSRF protection and other session-based features
session_start();

// Include necessary files
include 'partials/header.php';
include 'page_access_controller.php';
include 'conn-d.php'; // Assuming you have a separate DB config

// CSRF Protection: Generate a token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch clients data securely using prepared statements
try {
    $stmt = $conn->prepare("SELECT * FROM klientet ORDER BY emri ASC");
    $stmt->execute();
    $result = $stmt->get_result();
    $clients = $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];
} catch (Exception $e) {
    // Handle errors gracefully
    $clients = [];
    error_log("Error fetching clients: " . $e->getMessage());
}

// Handle form submission
$errors = [];
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF Token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $errors[] = "Invalid CSRF token.";
    }
    // Sanitize and validate input fields
    $emri = trim($_POST['emri'] ?? '');
    $mbiemri = trim($_POST['mbiemri'] ?? '');
    $numri_tel = trim($_POST['numri_tel'] ?? '');
    $numri_personal = trim($_POST['numri_personal'] ?? '');
    $klienti = trim($_POST['klienti'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $emriartistik = trim($_POST['emriartistik'] ?? '');
    $vepra = trim($_POST['vepra'] ?? '');
    $data = trim($_POST['data'] ?? '');
    $perqindja = floatval($_POST['perqindja'] ?? 0);
    $perqindja_other = floatval($_POST['perqindja_other'] ?? 0);
    $shenime = trim($_POST['shenime'] ?? '');

    // Basic validation
    if (empty($emri)) $errors[] = "Emri is required.";
    if (empty($mbiemri)) $errors[] = "Mbiemri is required.";
    if (empty($numri_tel)) $errors[] = "Numri i telefonit is required.";
    if (empty($numri_personal)) $errors[] = "Numri personal is required.";
    if (empty($klienti)) $errors[] = "Klienti is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email address.";
    if (empty($emriartistik)) $errors[] = "Emri artistik is required.";
    if (empty($vepra)) $errors[] = "Vepra is required.";
    if (empty($data)) $errors[] = "Data is required.";
    if ($perqindja < 0 || $perqindja > 100) $errors[] = "Përqindja must be between 0 and 100.";
    if ($perqindja_other < 0 || $perqindja_other > 100) $errors[] = "Përqindja (Klienti) must be between 0 and 100.";

    // Handle file upload
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['pdf_file'];
        $allowedTypes = ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/pdf'];
        $maxSize = 10 * 1024 * 1024; // 10MB
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Error uploading file.";
        } elseif (!in_array(mime_content_type($file['tmp_name']), $allowedTypes)) {
            $errors[] = "Invalid file type. Only DOCX and PDF are allowed.";
        } elseif ($file['size'] > $maxSize) {
            $errors[] = "File size exceeds the 10MB limit.";
        } else {
            // Sanitize file name
            $fileName = basename($file['name']);
            $fileName = preg_replace("/[^A-Za-z0-9.\-_]/", '', $fileName);
            $uploadDir = 'uploads/contracts/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $filePath = $uploadDir . time() . "_" . $fileName;
            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                $errors[] = "Failed to move uploaded file.";
            }
        }
    } else {
        $filePath = null; // No file uploaded
    }

    // If no errors, proceed to insert into database
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("INSERT INTO kontratat (emri, mbiemri, numri_tel, numri_personal, klienti, email, emriartistik, vepra, data, pdf_file, perqindja, perqindja_other, shenime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "sssssssssddds",
                $emri,
                $mbiemri,
                $numri_tel,
                $numri_personal,
                $klienti,
                $email,
                $emriartistik,
                $vepra,
                $data,
                $filePath,
                $perqindja,
                $perqindja_other,
                $shenime
            );
            $stmt->execute();
            $success = true;
            // Reset the CSRF token after successful submission
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Exception $e) {
            $errors[] = "Error saving contract: " . $e->getMessage();
            error_log("Error inserting contract: " . $e->getMessage());
        }
    }
}
?>
<!-- Include necessary scripts and stylesheets -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/selectr/dist/selectr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dropzone@5/dist/min/dropzone.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/selectr/dist/selectr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/dropzone@5/dist/min/dropzone.min.js"></script>
<script src="ajax.js"></script>

<style>
    /* Custom styles for enhanced design */
    .main-panel {
        /* background-color: #f8f9fa; */
        min-height: 100vh;
    }

    .card {
        border: none;
        border-radius: 15px;
    }

    .form-label {
        font-weight: 600;
    }

    .dropzone {
        border: 2px dashed #0d6efd;
        border-radius: 10px;
        background: #f1f1f1;
    }

    .dropzone .dz-message {
        font-size: 1.2rem;
        color: #6c757d;
    }

    .preview-section {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* Loader Styles */
    .loader {
        border: 6px solid #f3f3f3;
        border-top: 6px solid #0d6efd;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: auto;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid py-4">
            <!-- Breadcrumb Navigation -->
            <nav class="bg-white px-3 py-2 rounded-3 mb-4" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#" class="text-reset text-decoration-none">Kontratat</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="<?php echo htmlspecialchars(__FILE__); ?>" class="text-reset text-decoration-none">Kontrata e re (Këngë)</a>
                    </li>
                </ol>
            </nav>

            <!-- Display Success or Error Messages -->
            <?php if ($success): ?>
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses!',
                        text: 'Kontrata është ruajtur me sukses.',
                        confirmButtonText: 'Ok'
                    });
                </script>
            <?php elseif (!empty($errors)): ?>
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Gabim!',
                        html: '<?php echo implode("<br>", array_map("htmlspecialchars", $errors)); ?>',
                        confirmButtonText: 'Ok'
                    });
                </script>
            <?php endif; ?>

            <!-- Contract Form Card -->
            <div class="card p-5 border border-2 rounded-5">
                <div class="row">
                    <!-- Form Column -->
                    <div class="col-12 col-lg-5 mb-4 mb-lg-0">
                        <form id="contractForm" method="POST" enctype="multipart/form-data" novalidate>
                            <!-- CSRF Token -->
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                            <!-- Personal Information -->
                            <h4 class="mb-3">Informacioni Personal</h4>
                            <div class="mb-3">
                                <label class="form-label" for="emri">Emri <span class="text-danger">*</span></label>
                                <input type="text" name="emri" id="emri" class="form-control rounded-3" placeholder="Sheno emrin" required>
                                <div class="invalid-feedback">
                                    Ju lutem shkruani emrin tuaj.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="mbiemri">Mbiemri <span class="text-danger">*</span></label>
                                <input type="text" name="mbiemri" id="mbiemri" class="form-control rounded-3" placeholder="Sheno mbiemrin" required>
                                <div class="invalid-feedback">
                                    Ju lutem shkruani mbiemrin tuaj.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="numri_tel">Numri i Telefonit <span class="text-danger">*</span></label>
                                <input type="tel" name="numri_tel" id="numri_tel" class="form-control rounded-3" placeholder="Sheno numrin e telefonit" required pattern="[0-9]{10}">
                                <div class="invalid-feedback">
                                    Ju lutem shkruani një numër telefoni valid (10 shifra).
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="numri_personal">Numri Personal <span class="text-danger">*</span></label>
                                <input type="text" name="numri_personal" id="numri_personal" class="form-control rounded-3" placeholder="Sheno numrin personal" required pattern="\d{10}">
                                <div class="invalid-feedback">
                                    Ju lutem shkruani një numër personal valid (10 shifra).
                                </div>
                            </div>

                            <!-- Client Selection -->
                            <h4 class="mt-4 mb-3">Zgjidhni Klientin</h4>
                            <div class="mb-3">
                                <label class="form-label" for="klienti">Klienti <span class="text-danger">*</span></label>
                                <select name="klienti" id="klienti22" class="form-select rounded-3" required>
                                    <option value="" disabled selected>Zgjidhni një klient</option>
                                    <?php foreach ($clients as $client): ?>
                                        <option value="<?php echo htmlspecialchars($client['emri'] . "|" . $client['emailadd'] . "|" . $client['emriart']); ?>">
                                            <?php echo htmlspecialchars($client['emri']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Ju lutem zgjidhni një klient.
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <h4 class="mt-4 mb-3">Informacioni Shtesë</h4>
                            <div class="mb-3">
                                <label class="form-label" for="email">Adresa e Email-it <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control rounded-3" placeholder="Sheno adresen e email-it" required>
                                <div class="invalid-feedback">
                                    Ju lutem shkruani një adresë email-i valid.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="emriartistik">Emri Artistik <span class="text-danger">*</span></label>
                                <input type="text" name="emriartistik" id="emriartistik" class="form-control rounded-3" placeholder="Sheno emrin artistik" required>
                                <div class="invalid-feedback">
                                    Ju lutem shkruani emrin tuaj artistik.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="vepra">Vepra <span class="text-danger">*</span></label>
                                <input type="text" name="vepra" id="vepra" class="form-control rounded-3" placeholder="Sheno veprën" required>
                                <div class="invalid-feedback">
                                    Ju lutem shkruani veprën tuaj.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="data">Data <span class="text-danger">*</span></label>
                                <input type="text" name="data" id="data" class="form-control rounded-3" placeholder="Zgjidhni datën" required>
                                <div class="invalid-feedback">
                                    Ju lutem zgjidhni një datë.
                                </div>
                            </div>

                            <!-- File Upload -->
                            <div class="mb-3">
                                <label class="form-label">Ngarko Kontratën <span class="text-danger">*</span></label>
                                <div class="dropzone" id="file-upload-dropzone">
                                    <div class="dz-message">
                                        <i class="fas fa-upload fa-2x"></i>
                                        <p>Drag & Drop skedarin tuaj këtu ose klikoni për të zgjedhur.</p>
                                    </div>
                                </div>
                                <input type="hidden" name="pdf_file" id="pdf_file">
                                <div class="invalid-feedback d-block" id="file-error">
                                    Ju lutem ngarkoni një skedar (DOCX ose PDF) që nuk tejkalon 10MB.
                                </div>
                            </div>

                            <!-- Percentage Fields -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="perqindja">Përqindja (Baresha) <span class="text-danger">*</span></label>
                                    <input type="number" name="perqindja" id="perqindja" class="form-control rounded-3" placeholder="Sheno përqindjen" min="0" max="100" step="0.01" required onchange="updatePerqindjaOther()">
                                    <div class="invalid-feedback">
                                        Përqindja duhet të jetë midis 0 dhe 100.
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="perqindja_other">Përqindja (Klienti)</label>
                                    <input type="number" name="perqindja_other" id="perqindja_other" class="form-control rounded-3" readonly>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="mb-3">
                                <label class="form-label" for="shenime">Shënime <span class="text-danger">*</span></label>
                                <textarea name="shenime" id="shenime" class="form-control rounded-3" rows="4" placeholder="Shëno..." required></textarea>
                                <div class="invalid-feedback">
                                    Ju lutem shtoni disa shënime.
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary w-100 rounded-3">
                                <i class="fas fa-paper-plane me-2"></i>Dërgo
                            </button>
                        </form>
                    </div>

                    <!-- Preview Column -->
                    <div class="col-12 col-lg-7">
                        <div class="preview-section">
                            <h4 class="mb-3">Preview i Kontratës</h4>
                            <div id="contractContent">
                                <p>Plotësoni formularin për të parë parapamjen e kontratës.</p>
                            </div>
                            <!-- Loader -->
                            <div id="loader" class="d-none">
                                <div class="loader"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('partials/footer.php'); ?>

    <!-- JavaScript Enhancements -->
    <script>
        // Initialize Selectr for the 'klienti22' select element
        new Selectr('#klienti22', {
            searchable: true,
            clearable: true,
            placeholder: 'Zgjidhni një klient',
            searchPlaceholder: 'Kërko...'
        });

        // Initialize Flatpickr for the date input
        flatpickr("input[name='data']", {
            dateFormat: "Y-m-d",
            maxDate: "today",
            allowInput: true,
            locale: {
                firstDayOfWeek: 1,
                weekdays: {
                    shorthand: ["E D", "E H", "T M", "E M", "E K", "T H", "E S"],
                    longhand: ["E Diel", "E Hënë", "T Martë", "E Mërkurë", "E Enjte", "T Premte", "E Shtunë"],
                },
                months: {
                    shorthand: ["Jan", "Shk", "Mar", "Pri", "Maj", "Qer", "Kor", "Gus", "Sht", "Tet", "Nën", "Dhj"],
                    longhand: ["Janar", "Shkurt", "Mars", "Prill", "Maj", "Qershor", "Korrik", "Gusht", "Shtator", "Tetor", "Nëntor", "Dhjetor"],
                },
            },
        });

        // Initialize Dropzone for file upload
        Dropzone.autoDiscover = false;
        const dropzone = new Dropzone("#file-upload-dropzone", {
            url: "#", // Prevent actual upload
            autoProcessQueue: false,
            maxFiles: 1,
            acceptedFiles: '.docx,.pdf',
            addRemoveLinks: true,
            dictDefaultMessage: "Drag & Drop skedarin tuaj këtu ose klikoni për të zgjedhur.",
            init: function() {
                this.on("addedfile", function(file) {
                    // Validate file size
                    if (file.size > 10 * 1024 * 1024) {
                        this.removeFile(file);
                        Swal.fire({
                            icon: 'error',
                            title: 'Skedar i Madh',
                            text: 'Madhësia e skedarit tejkalon limitin prej 10MB.',
                        });
                        return;
                    }
                    // Validate file type
                    const allowedTypes = ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/pdf'];
                    if (!allowedTypes.includes(file.type)) {
                        this.removeFile(file);
                        Swal.fire({
                            icon: 'error',
                            title: 'Lloj Skedari i Panjohur',
                            text: 'Ju lutem zgjidhni një skedar të vlefshëm (DOCX ose PDF).',
                        });
                        return;
                    }
                    // Update hidden input with file info if needed
                });

                this.on("removedfile", function(file) {
                    // Handle file removal if necessary
                });
            }
        });

        // Handle Dropzone file upload manually
        document.getElementById('contractForm').addEventListener('submit', function(e) {
            if (dropzone.getAcceptedFiles().length > 0) {
                // Proceed with form submission
                // You can handle the file upload via AJAX if needed
            } else {
                // Prevent form submission if no file is uploaded
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Nuk ka Skedar',
                    text: 'Ju lutem ngarkoni një skedar për të vazhduar.',
                });
            }
        });

        // Add event listeners to form inputs for live preview
        document.querySelectorAll('#contractForm input, #contractForm select, #contractForm textarea').forEach(input => {
            input.addEventListener('input', updatePreview);
            input.addEventListener('change', updatePreview);
        });

        // Function to display email and emriartistik based on selected klienti
        function showEmail(select) {
            const [emri, email, emriartistik] = select.value.split("|");
            document.getElementById("email").value = email || "Klienti që keni zgjedhur nuk ka adresë të email-it";
            document.getElementById("emriartistik").value = emriartistik || "";
            updatePreview();
        }

        // Function to update perqindja_other based on perqindja
        function updatePerqindjaOther() {
            const perqindja = parseFloat(document.getElementById('perqindja').value);
            document.getElementById('perqindja_other').value = isNaN(perqindja) ? "" : (100 - perqindja).toFixed(2);
            updatePreview();
        }

        // Function to update the contract preview via AJAX
        function updatePreview() {
            const form = document.getElementById('contractForm');
            const formData = new FormData(form);
            const loader = document.getElementById('loader');
            const contractContent = document.getElementById('contractContent');

            loader.classList.remove('d-none');
            contractContent.classList.add('d-none');

            fetch('preview-contract.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    contractContent.innerHTML = data;
                    loader.classList.add('d-none');
                    contractContent.classList.remove('d-none');
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gabim',
                        text: 'Nuk u arrit të përditësohet preview i kontratës.',
                    });
                    loader.classList.add('d-none');
                    contractContent.classList.remove('d-none');
                });
        }

        // Function to validate file inputs
        function validateFile(input) {
            const file = input.files[0];
            const maxSize = 10 * 1024 * 1024; // 10MB
            const allowedTypes = ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/pdf'];
            if (!file) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nuk ka skedar',
                    text: 'Ju lutem zgjidhni një skedar për të ngarkuar.',
                });
                input.value = '';
                return;
            }
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lloj Skedari i Panjohur',
                    text: 'Ju lutem zgjidhni një skedar të vlefshëm (DOCX ose PDF).',
                });
                input.value = '';
                return;
            }
            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'Skedar i Madh',
                    text: 'Madhësia e skedarit tejkalon limitin prej 10MB.',
                });
                input.value = '';
                return;
            }
            // If valid, update the preview
            updatePreview();
        }

        // Form validation
        (function() {
            'use strict'
            const forms = document.querySelectorAll('#contractForm')

            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                        Swal.fire({
                            icon: 'error',
                            title: 'Gabim',
                            text: 'Ju lutem plotësoni të gjitha fushat e nevojshme.',
                        });
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()

        // Initial preview load
        document.addEventListener('DOMContentLoaded', updatePreview)
    </script>