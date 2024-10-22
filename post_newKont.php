<?php
session_start();
include 'partials/header.php';
include 'conn-d.php';
require 'vendor/autoload.php'; // PHPMailer

// Configuration Constants
define('UPLOAD_DIR', 'uploads/');
define('ALLOWED_FILE_TYPES', ['pdf', 'png', 'jpg', 'jpeg']);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Helper Functions
function displayMessage($type, $message)
{
    echo "<script>
        Swal.fire({
            icon: '$type',
            title: '" . ($type === 'error' ? 'Gabim' : 'Sukses') . "',
            text: '$message'
        });
    </script>";
}

function sanitizeInput($input)
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function handleFileUpload($file)
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        displayMessage('error', 'Ka pasur një gabim gjatë ngarkimit të skedarit: ' . $file['name']);
        return '';
    }
    $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileType, ALLOWED_FILE_TYPES)) {
        displayMessage('error', 'Vetëm skedarët e imazheve dhe PDF janë të mbështetur.');
        return '';
    }
    if ($file['size'] > MAX_FILE_SIZE) {
        displayMessage('error', "Skedari {$file['name']} tejkalon kufirin maksimal të madhësisë.");
        return '';
    }
    $uniqueName = uniqid() . '_' . basename($file['name']);
    $targetFile = UPLOAD_DIR . $uniqueName;
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return $targetFile;
    } else {
        displayMessage('error', 'Ka pasur një gabim gjatë ngarkimit të skedarit: ' . $file['name']);
        return '';
    }
}

function sendEmailNotification($to, $invoiceNumber)
{
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    try {
        // Server Settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.example.com'; // Update with your SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'user@example.com'; // SMTP username
        $mail->Password   = 'secret';           // SMTP password
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        // Recipients
        $mail->setFrom('no-reply@example.com', 'Invoice System');
        $mail->addAddress($to);
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Konfirmim i Dorëzimit të Faturës';
        $mail->Body    = "I dashur Përdorues,<br><br>Fatura juaj me numër <strong>$invoiceNumber</strong> u dorëzua me sukses.<br><br>Faleminderit!";
        $mail->send();
    } catch (Exception $e) {
        // Optionally log the error
    }
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize Inputs
    $invoiceDate = sanitizeInput($_POST['invoice_date'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    $moreDetails = sanitizeInput($_POST['more_details'] ?? '');
    $registrant = sanitizeInput($_POST['registrant'] ?? 'Unknown');
    $category = sanitizeInput($_POST['category'] ?? '');
    $invoiceNumber = sanitizeInput($_POST['invoice_number'] ?? '');
    $valueOfInvoice = sanitizeInput($_POST['valueOfInvoice'] ?? '');
    $userEmail = $_SESSION['user_email'] ?? 'user@example.com';

    // Handle Company Name
    $companyName = ($_POST['company_name'] === 'new') ? sanitizeInput($_POST['new_company_name'] ?? '') : sanitizeInput($_POST['company_name'] ?? '');

    // Backend Validation for valueOfInvoice
    if (!preg_match('/^\d+(\.\d{2})?$/', $valueOfInvoice)) {
        displayMessage('error', 'Vlera e faturës duhet të jetë një numër i vlefshëm me dy decimal (p.sh., 200.00).');
        exit();
    }

    // Optional: Format the value to ensure two decimal places
    $valueOfInvoice = number_format((float)$valueOfInvoice, 2, '.', '');

    // Handle File Upload
    $documentPath = '';
    if (!empty($_FILES['document']['name'])) {
        $documentPath = handleFileUpload($_FILES['document']);
        if (empty($documentPath)) exit();
    }

    // Proceed with Insertion without Duplicate Check
    $stmt = $conn->prepare("INSERT INTO invoices_kont (invoice_creation_date, invoice_date, description, more_details, registrant, category, company_name, invoice_number, document_path, vlera_faktura) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $invoiceDate, $description, $moreDetails, $registrant, $category, $companyName, $invoiceNumber, $documentPath, $valueOfInvoice);
    if ($stmt->execute()) {
        displayMessage('success', 'Fatura u shtua me sukses!');
        sendEmailNotification($userEmail, $invoiceNumber);
    } else {
        displayMessage('error', 'Pati një problem me ruajtjen e faturës.');
    }
}
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <a class="input-custom-css px-3 py-2 back-button" href="shpenzimet_objekt.php" style="text-decoration: none">
                <i class="fi fi-rr-angle-small-left"></i> Kthehu Mbrapa
            </a>
            <br><br>
            <div class="row">
                <!-- Data Entry Form -->
                <div class="col-md-4">
                    <div class="card rounded-5">
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data" id="invoiceForm">
                                <?php
                                $fields = [
                                    [
                                        'label' => 'Data e Faturës',
                                        'name' => 'invoice_date',
                                        'type' => 'date',
                                        'required' => true,
                                        'help' => 'Zgjidhni datën e faturës.'
                                    ],
                                    [
                                        'label' => 'Përshkrimi',
                                        'name' => 'description',
                                        'type' => 'text',
                                        'required' => true,
                                        'help' => 'Shkruani një përshkrim të shkurtër.'
                                    ],
                                    [
                                        'label' => 'Detaje Shtesë',
                                        'name' => 'more_details',
                                        'type' => 'textarea',
                                        'required' => false,
                                        'help' => 'Shkruani detaje shtesë nëse është e nevojshme.'
                                    ],
                                    [
                                        'label' => 'Kategoria',
                                        'name' => 'category',
                                        'type' => 'select',
                                        'options' => ['Shpenzimet', 'Investimet', 'Obligime', 'Pagesa AL', 'Pagesa KS', 'Tjetër'],
                                        'required' => true,
                                        'help' => 'Zgjidhni kategorinë e faturës.'
                                    ],
                                    [
                                        'label' => 'Emri i Firmës',
                                        'name' => 'company_name',
                                        'type' => 'select',
                                        'options' => array_merge(
                                            ['' => 'Zgjidh një kompani ekzistuese'],
                                            array_column($conn->query("SELECT DISTINCT company_name FROM invoices_kont")->fetch_all(MYSQLI_ASSOC), 'company_name'),
                                            ['new' => 'Shto një kompani të re']
                                        ),
                                        'required' => true,
                                        'help' => 'Zgjidhni një kompani ekzistuese ose shtoni një të re.'
                                    ],
                                    [
                                        'label' => 'Numri i Faturës',
                                        'name' => 'invoice_number',
                                        'type' => 'text',
                                        'required' => true,
                                        'help' => 'Shkruani numrin unik të faturës.'
                                    ],
                                    [
                                        'label' => 'Vlera e Faturës',
                                        'name' => 'valueOfInvoice',
                                        'type' => 'number',
                                        'required' => true,
                                        'attributes' => 'step="0.01" min="0" pattern="^\d+(\.\d{2})?$"',
                                        'help' => 'Shkruani vlerën totale të faturës (p.sh., 200.00).'
                                    ],
                                    [
                                        'label' => 'Ngarko Dokument',
                                        'name' => 'document',
                                        'type' => 'file',
                                        'accept' => '.pdf,.png,.jpg,.jpeg',
                                        'required' => false,
                                        'help' => 'Ngarkoni një skedar PDF, PNG, JPG, ose JPEG.'
                                    ],
                                ];
                                foreach ($fields as $field) {
                                    echo '<div class="form-group">';
                                    echo "<label for='{$field['name']}'>{$field['label']} " . ($field['required'] ? '<span class="text-danger">*</span>' : '') . "</label>";
                                    switch ($field['type']) {
                                        case 'textarea':
                                            echo "<textarea class='form-control rounded-5 border border-2' name='{$field['name']}' id='{$field['name']}' rows='3' " . ($field['required'] ? 'required' : '') . "></textarea>";
                                            break;
                                        case 'select':
                                            echo "<select class='form-control rounded-5 border border-2' name='{$field['name']}' id='{$field['name']}' " . ($field['required'] ? 'required' : '') . ">";
                                            foreach ($field['options'] as $key => $option) {
                                                if (is_numeric($key)) {
                                                    echo "<option value='{$option}'>{$option}</option>";
                                                } else {
                                                    echo "<option value='{$key}'>{$option}</option>";
                                                }
                                            }
                                            echo "</select>";
                                            break;
                                        case 'file':
                                            echo "<div id='dropZone' class='drop-zone'>
                                                <span class='drop-zone__prompt'>Tërhiq skedarin këtu ose klikoni për ta ngarkuar</span>
                                                <input type='file' class='form-control rounded-5 border border-2' name='{$field['name']}' id='{$field['name']}' accept='{$field['accept']}' style='display:none;'>
                                              </div>";
                                            break;
                                        case 'number':
                                            echo "<input type='{$field['type']}' class='form-control rounded-5 border border-2' name='{$field['name']}' id='{$field['name']}' " . ($field['required'] ? 'required' : '') . " " . ($field['attributes'] ?? '') . ">";
                                            break;
                                        default:
                                            echo "<input type='{$field['type']}' class='form-control rounded-5 border border-2' name='{$field['name']}' id='{$field['name']}' " . ($field['required'] ? 'required' : '') . " " . ($field['attributes'] ?? '') . ">";
                                    }
                                    if ($field['name'] === 'company_name') {
                                        echo "<input type='text' class='form-control rounded-5 border border-2 mt-2' name='new_company_name' id='new_company_name' placeholder='Shkruaj një kompani të re' style='display:none;'>";
                                    }
                                    echo '</div>';
                                }
                                ?>
                                <button type="submit" class="input-custom-css px-3 py-2 submit-button">Ruaj të Dhënat</button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Invoice Preview -->
                <div class="col-md-8">
                    <div id="invoicePreview" class="card">
                        <div class="card-body">
                            <div class="invoice-preview" id="invoiceContent">
                                <!-- Document Preview -->
                                <div id="fileContentPreview">
                                    <?php
                                    if (!empty($documentPath)) {
                                        $fileType = strtolower(pathinfo($documentPath, PATHINFO_EXTENSION));
                                        if (in_array($fileType, ['png', 'jpg', 'jpeg'])) {
                                            echo '<img src="' . $documentPath . '" alt="Uploaded Image" style="max-width:100%;">';
                                        } elseif ($fileType === 'pdf') {
                                            echo '<embed src="' . $documentPath . '" type="application/pdf" width="100%" height="600px">';
                                        }
                                    }
                                    ?>
                                </div>
                                <!-- Invoice Details -->
                                <div class="invoice-details mt-3">
                                    <p><strong>Nga:</strong> Baresha Network</p>
                                    <p><strong>Për:</strong> <span id="preview_company_name">[Emri i Firmës]</span></p>
                                    <p><strong>Data e Faturës:</strong> <span id="preview_invoice_date">[Data e Faturës]</span></p>
                                    <p><strong>Vlera e Faturës:</strong> <span id="preview_valueOfInvoice">[Vlera e Faturës]</span></p>
                                    <p><strong>Numri i Faturës:</strong> <span id="preview_invoice_number">[Numri i Faturës]</span></p>
                                </div>
                                <!-- Invoice Body -->
                                <div class="invoice-body mt-3">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Përshkrimi</th>
                                                <th>Detaje</th>
                                                <th>Kategoria</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td id="preview_description">[Përshkrimi]</td>
                                                <td id="preview_more_details">[Detaje Shtesë]</td>
                                                <td id="preview_category">[Kategoria]</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="invoice-footer mt-3">
                                    <span class="badge bg-success">Fatura e Përfunduar</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Include Libraries -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectr/1.3.0/selectr.min.js"></script>
<!-- Removed Tesseract and PDF.js Libraries -->

<!-- Custom Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        toggleNewCompanyInput();
        initializeSelectr();
        initializeDragAndDrop();
        bindFormEvents();
        bindPreviewEvents();
    });

    function toggleNewCompanyInput() {
        const companySelect = document.getElementById('company_name');
        const newCompanyInput = document.getElementById('new_company_name');
        companySelect.addEventListener('change', () => {
            newCompanyInput.style.display = companySelect.value === 'new' ? 'block' : 'none';
        });
    }

    function initializeSelectr() {
        new Selectr('#category', {
            searchable: true
        });
        new Selectr('#company_name', {
            searchable: true
        });
    }

    function initializeDragAndDrop() {
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('document');
        dropZone.addEventListener('click', () => fileInput.click());
        ['dragover', 'dragleave', 'drop'].forEach(event => {
            dropZone.addEventListener(event, preventDefaults, false);
        });
        dropZone.addEventListener('dragover', () => dropZone.classList.add('drop-zone--over'));
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drop-zone--over'));
        dropZone.addEventListener('drop', (e) => {
            dropZone.classList.remove('drop-zone--over');
            const files = e.dataTransfer.files;
            if (files.length) handleFile(files[0]);
        });
        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) handleFile(fileInput.files[0]);
        });
    }

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function handleFile(file) {
        previewFile(file);
    }

    function previewFile(file) {
        const preview = document.getElementById('fileContentPreview');
        const reader = new FileReader();
        reader.onload = (e) => {
            const fileType = file.type.split('/')[0];
            if (fileType === 'image') {
                preview.innerHTML = `<img src="${e.target.result}" style="max-width:100%; border-radius: 5px;">`;
            } else if (fileType === 'application' && file.type === 'application/pdf') {
                preview.innerHTML = `<embed src="${e.target.result}" type="application/pdf" width="100%" height="600px">`;
            } else {
                preview.innerHTML = '';
            }
        };
        reader.readAsDataURL(file);
    }

    function bindFormEvents() {
        const form = document.getElementById('invoiceForm');
        form.addEventListener('submit', (e) => {
            // No additional client-side handling required
            // Form submission is handled by PHP
        });
    }

    function bindPreviewEvents() {
        const fields = ['invoice_date', 'description', 'more_details', 'category', 'invoice_number', 'valueOfInvoice', 'company_name', 'new_company_name'];
        fields.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('input', updatePreview);
                element.addEventListener('change', updatePreview);
            }
        });
    }

    function updatePreview() {
        document.getElementById('preview_invoice_date').innerText = document.getElementById('invoice_date').value || '[Data e Faturës]';
        document.getElementById('preview_description').innerText = document.getElementById('description').value || '[Përshkrimi]';
        document.getElementById('preview_more_details').innerText = document.getElementById('more_details').value || '[Detaje Shtesë]';
        document.getElementById('preview_category').innerText = document.getElementById('category').value || '[Kategoria]';
        document.getElementById('preview_invoice_number').innerText = document.getElementById('invoice_number').value || '[Numri i Faturës]';
        document.getElementById('preview_valueOfInvoice').innerText = document.getElementById('valueOfInvoice').value || '[Vlera e Faturës]';
        const companySelect = document.getElementById('company_name').value;
        const newCompany = document.getElementById('new_company_name').value;
        document.getElementById('preview_company_name').innerText = companySelect === 'new' ? newCompany || '[Emri i Firmës]' : companySelect || '[Emri i Firmës]';
    }
</script>

<style>
    .card {
        border-radius: 15px;
        overflow: hidden;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-control,
    .btn,
    .input-custom-css,
    .submit-button {
        border-radius: 10px;
        padding: 0.75rem 1rem;
        font-size: 1rem;
    }

    #fileContentPreview img,
    #fileContentPreview embed {
        margin: 5px;
        border: 1px solid #ddd;
        padding: 5px;
        border-radius: 5px;
        background-color: #f9f9f9;
    }

    .invoice-preview {
        background-color: #fff;
        border: 1px solid #ddd;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    .invoice-details,
    .invoice-body,
    .invoice-footer {
        margin-top: 15px;
    }

    .drop-zone {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 15px;
        border: 2px dashed #ccc;
        border-radius: 10px;
        cursor: pointer;
        transition: background-color 0.3s;
        margin-bottom: 1rem;
    }

    .drop-zone--over {
        background-color: #e9e9e9;
    }

    .drop-zone__prompt {
        font-size: 14px;
        color: #666;
    }

    @media (max-width: 768px) {

        .col-md-4,
        .col-md-8 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }

    .back-button {
        display: inline-block;
        margin-bottom: 1rem;
    }

    /* Enhanced Button Styles */
    .submit-button {
        background-color: #007bff;
        color: #fff;
        border: none;
        transition: background-color 0.3s;
    }

    .submit-button:hover {
        background-color: #0056b3;
    }

    /* Adjusted Input Margins */
    .form-control {
        margin-top: 0.5rem;
    }
</style>
<?php include('partials/footer.php'); ?>