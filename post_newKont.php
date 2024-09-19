<?php
session_start();
include 'partials/header.php';
include 'conn-d.php';
require 'vendor/autoload.php'; // PHPMailer
// Configuration Constants
define('UPLOAD_DIR', 'uploads/');
define('ALLOWED_FILE_TYPES', ['pdf', 'png', 'jpg', 'jpeg']);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
// Language Support
$available_languages = ['en', 'sq'];
$language = in_array($_GET['lang'] ?? '', $available_languages) ? $_GET['lang'] : 'sq';
$translations = [
    'en' => [
        'error' => 'Error',
        'success' => 'Success',
        'invoice_added' => 'Invoice added successfully!',
        'duplicate_invoice' => 'An invoice with this number already exists.',
        'upload_error' => 'There was an error uploading the file:',
        'choose_existing_company' => 'Choose an existing company',
        'add_new_company' => 'Add a new company',
        'save_data' => 'Save Data',
        'invoice_details' => 'Invoice Details',
        'submit_invoice' => 'Submit Invoice',
        'ocr_processing' => 'Processing OCR...',
        'ocr_wait' => 'Please wait while we extract text from your document.',
        'ocr_error' => 'There was an error processing the document.',
        'pdf_error' => 'There was an error reading the PDF file.',
        'invalid_file' => 'Only image and PDF files are supported for OCR.',
        'copy_success' => 'Data copied successfully!',
        'email_subject' => 'Invoice Submission Confirmation',
        'email_body' => 'Dear User,<br><br>Your invoice with number <strong>%s</strong> has been successfully submitted.<br><br>Thank you!',
    ],
    'sq' => [
        'error' => 'Gabim',
        'success' => 'Sukses',
        'invoice_added' => 'Fatura u shtua me sukses!',
        'duplicate_invoice' => 'Një faturë me këtë numër ekziston tashmë.',
        'upload_error' => 'Ka pasur një gabim gjatë ngarkimit të skedarit:',
        'choose_existing_company' => 'Zgjidh një kompani ekzistuese',
        'add_new_company' => 'Shto një kompani të re',
        'save_data' => 'Ruaj të Dhënat',
        'invoice_details' => 'Detajet e Faturës',
        'submit_invoice' => 'Dërgo Faturën',
        'ocr_processing' => 'Duke përpunuar OCR...',
        'ocr_wait' => 'Ju lutem prisni ndërsa nxjerrim tekstin nga dokumenti juaj.',
        'ocr_error' => 'Ka pasur një gabim gjatë përpunimit të dokumentit.',
        'pdf_error' => 'Ka pasur një gabim gjatë leximit të skedarit PDF.',
        'invalid_file' => 'Vetëm skedarët e imazheve dhe PDF janë të mbështetur për OCR.',
        'copy_success' => 'Të dhënat u kopjuan me sukses!',
        'email_subject' => 'Konfirmim i Dorëzimit të Faturës',
        'email_body' => 'I dashur Përdorues,<br><br>Fatura juaj me numër <strong>%s</strong> u dorëzua me sukses.<br><br>Faleminderit!',
    ]
];
$trans = $translations[$language];
// Helper Functions
function displayMessage($type, $message)
{
    echo "<script>
        Swal.fire({
            icon: '$type',
            title: '" . ($type === 'error' ? $GLOBALS['trans']['error'] : $GLOBALS['trans']['success']) . "',
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
    global $trans;
    if ($file['error'] !== UPLOAD_ERR_OK) {
        displayMessage('error', $trans['upload_error'] . ' ' . $file['name']);
        return '';
    }
    $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileType, ALLOWED_FILE_TYPES)) {
        displayMessage('error', $trans['invalid_file']);
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
        displayMessage('error', $trans['upload_error'] . ' ' . $file['name']);
        return '';
    }
}
function sendEmailNotification($to, $invoiceNumber)
{
    global $trans;
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
        $mail->Subject = $trans['email_subject'];
        $mail->Body    = sprintf($trans['email_body'], $invoiceNumber);
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
    // Handle File Upload
    $documentPath = '';
    if (!empty($_FILES['document']['name'])) {
        $documentPath = handleFileUpload($_FILES['document']);
        if (empty($documentPath)) exit();
    }
    // Insert Data into Database
    $stmt = $conn->prepare("INSERT INTO invoices_kont (invoice_creation_date, invoice_date, description, more_details, registrant, category, company_name, invoice_number, document_path, vlera_faktura) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $invoiceDate, $description, $moreDetails, $registrant, $category, $companyName, $invoiceNumber, $documentPath, $valueOfInvoice);
    if ($stmt->execute()) {
        displayMessage('success', $trans['invoice_added']);
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
            <br>
            <br>
            <div class="row">
                <!-- Data Entry Form -->
                <div class="col-md-4">
                    <div class="card rounded shadow">
                        <div class="card-body">
                            <span class='badge bg-warning text-dark rounded mb-3'>VERSION BETA</span>
                            <h4 class="card-title">Shto Faturë të Re</h4>
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
                                        'options' => ['Shpenzimet', 'Investimet', 'Obligime', 'Tjetër'],
                                        'required' => true,
                                        'help' => 'Zgjidhni kategorinë e faturës.'
                                    ],
                                    [
                                        'label' => 'Emri i Firmës',
                                        'name' => 'company_name',
                                        'type' => 'select',
                                        'options' => array_merge(
                                            ['' => $trans['choose_existing_company']],
                                            array_column($conn->query("SELECT DISTINCT company_name FROM invoices_kont")->fetch_all(MYSQLI_ASSOC), 'company_name'),
                                            ['new' => $trans['add_new_company']]
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
                                        'attributes' => 'step="0.01"',
                                        'required' => true,
                                        'help' => 'Shkruani vlerën totale të faturës.'
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
                                    // Add tooltip using the title attribute on the label
                                    echo "<label for='{$field['name']}' title='{$field['help']}'>{$field['label']} " . ($field['required'] ? '<span class="text-danger">*</span>' : '') . "</label>";
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
                                        default:
                                            echo "<input type='{$field['type']}' class='form-control rounded-5 border border-2' name='{$field['name']}' id='{$field['name']}' " . ($field['required'] ? 'required' : '') . " " . ($field['attributes'] ?? '') . ">";
                                    }
                                    // Remove the small help text
                                    /*
                                    echo "<small class='form-text text-muted'>{$field['help']}</small>";
                                    */
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
                            <span class='badge bg-info text-dark rounded mb-3'>VERSION BETA</span>
                            <h4 class="card-title">Parapamja e Faturës</h4>
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
                                <!-- OCR Text Preview -->
                                <div id="ocrTextPreview" class="mt-3">
                                    <h5>Teksti i Nxjerrë:</h5>
                                    <pre style="white-space: pre-wrap; background-color: #f4f4f4; padding: 10px; border-radius: 5px;"><?php echo htmlspecialchars($extractedText ?? ''); ?></pre>
                                    <button class="input-custom-css px-3 py-2 mt-2" onclick="copyToClipboard('ocrTextPreview')">Kopjo Tekstin</button>
                                </div>
                                <!-- Invoice Details -->
                                <div class="invoice-details mt-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Nga:</strong>
                                            <p>Baresha Network</p>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <strong>Për:</strong>
                                            <p id="preview_company_name">[Emri i Firmës]</p>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <strong>Data e Faturës:</strong>
                                            <p id="preview_invoice_date">[Data e Faturës]</p>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <strong>Vlera e Faturës:</strong>
                                            <p id="preview_valueOfInvoice">[Vlera e Faturës]</p>
                                        </div>
                                    </div>
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
        <!-- Removed Language Switcher -->
        <!--
        <div class="language-switcher">
            <a href="?lang=sv">Shqip</a> | <a href="?lang=en">English</a>
        </div>
        -->
    </div>
</div>
<!-- Include Libraries -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectr/1.3.0/selectr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@2.1.5/dist/tesseract.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
<!-- Custom Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        toggleNewCompanyInput();
        initializeSelectr();
        initializeDragAndDrop();
        bindFormEvents();
        bindPreviewEvents();
        initializeTooltips(); // Initialize tooltips
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
        performOCR(file);
    }
    function previewFile(file) {
        const preview = document.getElementById('fileContentPreview');
        const reader = new FileReader();
        reader.onload = (e) => {
            const fileType = file.type.split('/')[0];
            preview.innerHTML = fileType === 'image' ? `<img src="${e.target.result}" style="max-width:100%; border-radius: 5px;">` :
                file.type === 'application/pdf' ? `<embed src="${e.target.result}" type="application/pdf" width="100%" height="600px">` : '';
        };
        reader.readAsDataURL(file);
    }
    function performOCR(file) {
        const trans = {
            processing: '<?php echo $trans['ocr_processing']; ?>',
            wait: '<?php echo $trans['ocr_wait']; ?>',
            error: '<?php echo $trans['ocr_error']; ?>',
            pdfError: '<?php echo $trans['pdf_error']; ?>',
            invalidFile: '<?php echo $trans['invalid_file']; ?>',
            copySuccess: '<?php echo $trans['copy_success']; ?>'
        };
        Swal.fire({
            title: trans.processing,
            text: trans.wait,
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        const fileType = file.type;
        if (fileType.startsWith('image/')) {
            const img = new Image();
            img.src = URL.createObjectURL(file);
            img.onload = () => {
                Tesseract.recognize(img, 'eng+sq')
                    .then(({
                        data: {
                            text
                        }
                    }) => {
                        Swal.close();
                        displayOCRText(text);
                        populateFormFields(text);
                    })
                    .catch(() => {
                        Swal.fire({
                            icon: 'error',
                            title: trans.error,
                            text: trans.error
                        });
                    });
            }
        } else if (fileType === 'application/pdf') {
            const reader = new FileReader();
            reader.onload = (e) => {
                const typedarray = new Uint8Array(e.target.result);
                pdfjsLib.getDocument(typedarray).promise.then(pdf => {
                    let extractedText = '';
                    let processedPages = 0;
                    const totalPages = pdf.numPages;
                    for (let i = 1; i <= totalPages; i++) {
                        pdf.getPage(i).then(page => {
                            const viewport = page.getViewport({
                                scale: 1.5
                            });
                            const canvas = document.createElement('canvas');
                            const context = canvas.getContext('2d');
                            canvas.height = viewport.height;
                            canvas.width = viewport.width;
                            page.render({
                                canvasContext: context,
                                viewport
                            }).promise.then(() => {
                                Tesseract.recognize(canvas, 'eng+sq')
                                    .then(({
                                        data: {
                                            text
                                        }
                                    }) => {
                                        extractedText += text + '\n';
                                        processedPages++;
                                        if (processedPages === totalPages) {
                                            Swal.close();
                                            displayOCRText(extractedText);
                                            populateFormFields(extractedText);
                                        }
                                    })
                                    .catch(() => {
                                        Swal.fire({
                                            icon: 'error',
                                            title: trans.error,
                                            text: trans.error
                                        });
                                    });
                            });
                        });
                    }
                }).catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: trans.error,
                        text: trans.pdfError
                    });
                });
            };
            reader.readAsArrayBuffer(file);
        } else {
            Swal.fire({
                icon: 'error',
                title: trans.error,
                text: trans.invalidFile
            });
        }
        function displayOCRText(text) {
            const ocrPreview = document.getElementById('ocrTextPreview');
            ocrPreview.innerHTML = `
            <h5>Teksti i Nxjerrë:</h5>
            <pre style="white-space: pre-wrap; background-color: #f4f4f4; padding: 10px; border-radius: 5px;">${text}</pre>
            <button class="input-custom-css px-3 py-2 mt-2" onclick="copyToClipboard('ocrTextPreview')">Kopjo Tekstin</button>
        `;
        }
        function populateFormFields(text) {
            // Example parsing logic; adjust regex as per your invoice format
            const patterns = {
                invoiceDate: /(\d{4}-\d{2}-\d{2})|(\d{2}\/\d{2}\/\d{4})/,
                invoiceNumber: /(Invoice Number|Nr\. Faturë)\s*[:\-]\s*(\w+)/i,
                invoiceValue: /(Total|Vlera)\s*[:\-]\s*([€$]?\s*\d{1,3}(?:,\d{3})*(?:\.\d{2})?)/i,
                description: /(Description|Përshkrimi)\s*[:\-]\s*(.+)/i
            };
            // Invoice Date
            const dateMatch = text.match(patterns.invoiceDate);
            if (dateMatch) {
                let date = dateMatch[0].replace(/\//g, '-');
                const parts = date.split('-');
                date = parts[0].length === 4 ? date : `${parts[2]}-${parts[1]}-${parts[0]}`;
                document.getElementById('invoice_date').value = date;
                document.getElementById('preview_invoice_date').innerText = date;
            }
            // Invoice Number
            const numberMatch = text.match(patterns.invoiceNumber);
            if (numberMatch) {
                const number = numberMatch[2];
                document.getElementById('invoice_number').value = number;
                document.getElementById('preview_invoice_number').innerText = number;
            }
            // Invoice Value
            const valueMatch = text.match(patterns.invoiceValue);
            if (valueMatch) {
                let value = valueMatch[2].replace(/[€$]/g, '').trim().replace(/,/g, '');
                document.getElementById('valueOfInvoice').value = value;
                document.getElementById('preview_valueOfInvoice').innerText = value;
            }
            // Description
            const descMatch = text.match(patterns.description);
            if (descMatch) {
                const desc = descMatch[2].trim();
                document.getElementById('description').value = desc;
                document.getElementById('preview_description').innerText = desc;
            }
        }
        function bindFormEvents() {
            const form = document.getElementById('invoiceForm');
            form.addEventListener('submit', (e) => {
                const fileInput = document.getElementById('document');
                if (fileInput.files.length) {
                    e.preventDefault();
                    performOCR(fileInput.files[0]);
                }
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
        function copyToClipboard(elementId) {
            const text = document.getElementById(elementId).innerText;
            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: '<?php echo $trans['success']; ?>',
                    text: '<?php echo $trans['copy_success']; ?>'
                });
            }).catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: '<?php echo $trans['error']; ?>',
                    text: 'Kopjimi nuk u mund!',
                });
            });
        }
        // Initialize Tooltips
        function initializeTooltips() {
            const labels = document.querySelectorAll('label[title]');
            labels.forEach(label => {
                const tooltipSpan = document.createElement('span');
                tooltipSpan.className = 'tooltip-text';
                tooltipSpan.innerText = label.getAttribute('title');
                label.appendChild(tooltipSpan);
                label.classList.add('tooltip');
            });
        }
    }
</script>
<style>
    .card {
        border-radius: 15px;
        overflow: hidden;
    }
    .form-group {
        margin-bottom: 1.5rem;
        /* Increased spacing between form groups */
    }
    .form-control,
    .btn,
    .input-custom-css,
    .submit-button {
        border-radius: 10px;
        padding: 0.75rem 1rem;
        /* Increased padding for clarity */
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
        /* Added margin for spacing */
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
        /* Added margin for spacing */
    }
    /* Tooltip Styles */
    .tooltip {
        position: relative;
        cursor: pointer;
    }
    .tooltip .tooltip-text {
        visibility: hidden;
        width: 200px;
        background-color: #333;
        color: #fff;
        text-align: left;
        border-radius: 6px;
        padding: 8px;
        position: absolute;
        z-index: 1;
        bottom: 125%;
        /* Position above the label */
        left: 50%;
        margin-left: -100px;
        opacity: 0;
        transition: opacity 0.3s;
        font-size: 0.875rem;
    }
    .tooltip:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
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