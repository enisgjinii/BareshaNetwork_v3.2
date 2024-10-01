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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/selectr/dist/selectr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="ajax.js"></script>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <!-- Breadcrumb Navigation -->
            <nav class="bg-white px-2 rounded-5 my-3" aria-label="breadcrumb">
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
            <div class="card p-5 shadow-sm rounded-5">
                <div class="row">
                    <!-- Form Column -->
                    <div class="col-12 col-md-4">
                        <form id="contractForm" method="POST" enctype="multipart/form-data" novalidate>
                            <!-- CSRF Token -->
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                            <?php
                            // Define primary form fields
                            $fields = [
                                ["label" => "Emri", "name" => "emri", "type" => "text", "placeholder" => "Sheno emrin", "required" => true],
                                ["label" => "Mbiemri", "name" => "mbiemri", "type" => "text", "placeholder" => "Sheno mbiemrin", "required" => true],
                                ["label" => "Numri i telefonit", "name" => "numri_tel", "type" => "text", "placeholder" => "Sheno numrin e telefonit", "required" => true],
                                ["label" => "Numri personal", "name" => "numri_personal", "type" => "text", "placeholder" => "Sheno numrin personal", "required" => true],
                            ];
                            // Function to render input fields
                            function renderInputFields($fields)
                            {
                                foreach ($fields as $field) {
                                    $placeholder = isset($field['placeholder']) ? "placeholder='" . htmlspecialchars($field['placeholder']) . "'" : '';
                                    $required = isset($field['required']) && $field['required'] ? 'required' : '';
                                    echo "<div class='mb-3'>
                                            <label class='form-label' for='" . htmlspecialchars($field['name']) . "'>" . htmlspecialchars($field['label']) . "</label>
                                            <input type='" . htmlspecialchars($field['type']) . "' name='" . htmlspecialchars($field['name']) . "' id='" . htmlspecialchars($field['name']) . "' class='form-control rounded-5 border-1' {$placeholder} {$required}>
                                          </div>";
                                }
                            }
                            // Render primary fields
                            renderInputFields($fields);
                            ?>
                            <!-- Klienti Select Field -->
                            <div class="mb-3">
                                <label class="form-label" for="klienti">Klienti</label>
                                <select name="klienti" id="klienti22" class="form-select rounded-5 border-1" onchange="showEmail(this)" required>
                                    <option value="" disabled selected>Zgjidhni një klient</option>
                                    <?php foreach ($clients as $client): ?>
                                        <option value="<?php echo htmlspecialchars($client['emri'] . "|" . $client['emailadd'] . "|" . $client['emriart']); ?>">
                                            <?php echo htmlspecialchars($client['emri']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php
                            // Define additional form fields
                            $additionalFields = [
                                ["label" => "Adresa e email-it", "name" => "email", "type" => "email", "placeholder" => "Sheno adresen e email-it", "required" => true],
                                ["label" => "Emri artistik", "name" => "emriartistik", "type" => "text", "placeholder" => "Sheno emrin artistik", "required" => true],
                                ["label" => "Vepra", "name" => "vepra", "type" => "text", "placeholder" => "Sheno veprën", "required" => true],
                                ["label" => "Data", "name" => "data", "type" => "date", "required" => true],
                                ["label" => "Ngarko kontratën", "name" => "pdf_file", "type" => "file", "attributes" => "accept='.docx,.pdf' onchange='validateFile(this)'", "required" => false],
                                ["label" => "Përqindja (Baresha)", "name" => "perqindja", "type" => "number", "placeholder" => "Sheno përqindjen", "attributes" => "onchange='updatePerqindjaOther()'", "required" => true],
                                ["label" => "Përqindja (Klienti)", "name" => "perqindja_other", "type" => "number", "readonly" => true, "required" => false],
                                ["label" => "Shënime", "name" => "shenime", "type" => "textarea", "rows" => 5, "placeholder" => "Shëno...", "required" => true]
                            ];
                            // Function to render additional fields
                            foreach ($additionalFields as $field) {
                                // Determine if the field is a textarea
                                if ($field['type'] == 'textarea') {
                                    $placeholder = isset($field['placeholder']) ? "placeholder='" . htmlspecialchars($field['placeholder']) . "'" : '';
                                    $rows = isset($field['rows']) ? intval($field['rows']) : 3;
                                    $required = isset($field['required']) && $field['required'] ? 'required' : '';
                                    echo "<div class='mb-3'>
                                            <label class='form-label' for='" . htmlspecialchars($field['name']) . "'>" . htmlspecialchars($field['label']) . "</label>
                                            <textarea name='" . htmlspecialchars($field['name']) . "' id='" . htmlspecialchars($field['name']) . "' class='form-control rounded-5 border-1' rows='{$rows}' {$placeholder} {$required}></textarea>
                                          </div>";
                                } else {
                                    // Handle input fields
                                    $attributes = isset($field['attributes']) ? " " . $field['attributes'] : '';
                                    $readonly = isset($field['readonly']) && $field['readonly'] ? 'readonly' : '';
                                    $placeholder = isset($field['placeholder']) ? "placeholder='" . htmlspecialchars($field['placeholder']) . "'" : '';
                                    $required = isset($field['required']) && $field['required'] ? 'required' : '';
                                    echo "<div class='mb-3'>
                                            <label class='form-label' for='" . htmlspecialchars($field['name']) . "'>" . htmlspecialchars($field['label']) . "</label>
                                            <input type='" . htmlspecialchars($field['type']) . "' name='" . htmlspecialchars($field['name']) . "' id='" . htmlspecialchars($field['name']) . "' class='form-control rounded-5 border-1' {$placeholder} {$attributes} {$readonly} {$required}>
                                          </div>";
                                }
                            }
                            ?>
                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary px-3 py-2 rounded-5">
                                <i class="fi fi-rr-paper-plane me-2"></i>Dërgo
                            </button>
                        </form>
                    </div>
                    <!-- Preview Column -->
                    <div class="col-12 col-md-8 mt-4 mt-md-0">
                        <div id="contractContent" class="container border rounded p-3 bg-light">
                            <h5>Preview i Kontratës</h5>
                            <p>Plotësoni formularin për të parë parapamjen e kontratës.</p>
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
        placeholder: 'Zgjidhni një klient'
    });
    // Initialize Flatpickr for the date input
    flatpickr("input[name='data']", {
        dateFormat: "Y-m-d",
        maxDate: "today",
        allowInput: true
    });
    // Add event listeners to form inputs for live preview
    document.querySelectorAll('#contractForm input, #contractForm select, #contractForm textarea').forEach(input => {
        input.addEventListener('input', updatePreview);
        input.addEventListener('change', updatePreview);
    });nuhi-gartenbau.de 
    // Function to display email and emriartistik based on selected klienti
    function showEmail(select) {
        const [emri, email, emriartistik] = select.value.split("|");
        document.getElementById("email").value = email || "Klienti që keni zgjedhur nuk ka adresë te emailit";
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
        const formData = new FormData(document.getElementById('contractForm'));
        fetch('preview-contract.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('contractContent').innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gabim',
                    text: 'Nuk u arrit të përditësohet preview i kontratës.',
                });
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
    // Initial preview load
    document.addEventListener('DOMContentLoaded', updatePreview);
</script>