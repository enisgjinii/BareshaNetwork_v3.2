<?php
include 'partials/header.php';
include 'conn-d.php';
// Helper functions
function displayError($message)
{
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Gabim',
            text: '$message'
        });
    </script>";
}
function displaySuccess($message)
{
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Sukses',
            text: '$message'
        });
    </script>";
}
function sanitizeInput($input)
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
function validateCompany($conn, $companyName)
{
    $sql = "SELECT company_name FROM invoices_kont WHERE company_name = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $companyName);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}
function isDuplicateInvoiceNumber($conn, $invoiceNumber)
{
    $sql = "SELECT COUNT(*) as count FROM invoices_kont WHERE invoice_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $invoiceNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'] > 0;
}
function handleMultipleFileUpload($files)
{
    $target_dir = "uploads/";
    $uploadedFiles = [];
    foreach ($files["name"] as $key => $name) {
        $target_file = $target_dir . basename($name);
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (!in_array($fileType, ['pdf', 'png', 'jpg', 'jpeg'])) {
            displayError('Lejohen vetëm skedarët PDF, PNG, JPG ose JPEG.');
            continue; // Skip invalid file
        }
        if (move_uploaded_file($files["tmp_name"][$key], $target_file)) {
            $uploadedFiles[] = $target_file;
        } else {
            displayError('Ka pasur një gabim gjatë ngarkimit të skedarit: ' . $name);
        }
    }
    return $uploadedFiles;
}
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $invoice_creation_date = date('Y-m-d H:i:s');
    $invoice_date = sanitizeInput($_POST['invoice_date']);
    $description = sanitizeInput($_POST['description']);
    $more_details = sanitizeInput($_POST['more_details']);
    $registrant = sanitizeInput($_POST['registrant']);
    $category = sanitizeInput($_POST['category']);
    $invoice_number = sanitizeInput($_POST['invoice_number']);
    $valueOfInvoice = sanitizeInput($_POST['valueOfInvoice']);
    // Check for duplicate invoice number
    if (isDuplicateInvoiceNumber($conn, $invoice_number)) {
        displayError('Një faturë me këtë numër ekziston tashmë.');
        exit;
    }
    // Handle company selection or addition
    if ($_POST['company_name'] == 'new' && !empty($_POST['new_company_name'])) {
        $companyName = sanitizeInput($_POST['new_company_name']);
    } else {
        $companyName = sanitizeInput($_POST['company_name']);
    }
    // Handle multiple file uploads
    $document_paths = [];
    if (!empty($_FILES["documents"]["name"][0])) { // Check if at least one file is selected
        $document_paths = handleMultipleFileUpload($_FILES["documents"]);
        if (empty($document_paths)) {
            exit; // Stop execution if no valid files were uploaded
        }
    }
    // Convert the array of file paths to a comma-separated string for storage
    $document_paths_str = implode(',', $document_paths);
    // Insert data into MySQL database
    $sql = "INSERT INTO invoices_kont (invoice_creation_date, invoice_date, description, more_details, registrant, category, company_name, invoice_number, document_path, vlera_faktura)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssss", $invoice_creation_date, $invoice_date, $description, $more_details, $registrant, $category, $companyName, $invoice_number, $document_paths_str, $valueOfInvoice);
    if ($stmt->execute()) {
        displaySuccess('Fatura u shtua me sukses!');
    } else {
        displayError('Pati një problem me ruajtjen e faturës.');
    }
}
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="mb-4">
                    <a type="button" class="input-custom-css px-3 py-2 position-relative" style="text-decoration: none" href="shpenzimet_objekt.php">
                        <i class="fi fi-rr-angle-small-left"></i> Shko prapa
                    </a>
                </div>
                <!-- Column 1: Data Form -->
                <div class="col-md-4">
                    <div class="card rounded shadow">
                        <div class="card-body">
                            <span class='badge bg-danger rounded mb-3'>BETA VERSION</span>
                            <h4 class="card-title">Shto Faturë të Re</h4>
                            <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm();">
                                <div class="form-group">
                                    <label>Data e faturës</label>
                                    <input type="date" class="form-control rounded-5 border border-2" name="invoice_date" id="invoice_date" required>
                                </div>
                                <div class="form-group">
                                    <label>Përshkrimi</label>
                                    <input type="text" class="form-control rounded-5 border border-2" name="description" id="description" required>
                                </div>
                                <div class="form-group">
                                    <label>Detaje shtesë</label>
                                    <textarea class="form-control rounded-5 border border-2" name="more_details" id="more_details" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Kategoria</label>
                                    <select class="form-control rounded-5 border border-2" name="category" id="category">
                                        <option value="Shpenzimet">Shpenzimet</option>
                                        <option value="Investimet">Investimet</option>
                                        <option value="Obligime">Obligime</option>
                                        <option value="Tjetër">Tjetër</option>
                                    </select>
                                    <script>
                                        new Selectr('select[name="category"]', {
                                            searchable: true
                                        })
                                    </script>
                                </div>
                                <div class="form-group">
                                    <label>Emri i firmës</label>
                                    <select class="form-control rounded-5 border border-2" name="company_name" id="company_name_select" required onchange="checkNewCompany()">
                                        <option value="">Zgjidh një kompani ekzistuese</option>
                                        <?php
                                        $sql = "SELECT DISTINCT company_name FROM invoices_kont";
                                        $stmt = $conn->query($sql);
                                        while ($row = $stmt->fetch_assoc()) {
                                            echo "<option value='" . htmlspecialchars($row['company_name']) . "'>" . htmlspecialchars($row['company_name']) . "</option>";
                                        }
                                        ?>
                                        <option value="new">Shto një kompani të re</option>
                                        <script>
                                            new Selectr('select[name="company_name"]', {
                                                searchable: true
                                            })
                                        </script>
                                    </select>
                                    <input type="text" class="form-control rounded-5 border border-2 mt-2" name="new_company_name" id="new_company_name" placeholder="Shkruaj një kompani të re" style="display:none;">
                                </div>
                                <div class="form-group">
                                    <label>Numri i faturës</label>
                                    <input type="text" class="form-control rounded-5 border border-2" name="invoice_number" id="invoice_number" required>
                                </div>
                                <div class="form-group">
                                    <label>Vlera e fatures</label>
                                    <input type="text" class="form-control rounded-5 border border-2" name="valueOfInvoice" id="valueOfInvoice" required>
                                </div>
                                <div class="form-group">
                                    <label>Ngarko dokumente</label>
                                    <input type="file" class="form-control rounded-5 border border-2-5" name="documents[]" accept=".pdf,.png,.jpg,.jpeg" multiple onchange="previewFiles()">
                                </div>
                                <div id="filePreview"></div>
                                <button type="submit" class="input-custom-css px-3 py-2">Ruaj të dhënat</button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Column 2: Invoice Preview -->
                <div class="col-md-8">
                    <div id="invoicePreview" class="card">
                        <div class="card-body">
                            <span class='badge bg-danger rounded mb-3'>BETA VERSION</span>
                            <h4 class="card-title">Parapamja e Faturës</h4>
                            <div class="invoice-preview">
                                <div class="invoice-header">
                                    <h2>FATURË</h2>
                                    <div class="invoice-number">Nr. Faturës: <span id="preview_invoice_number">[Numri i Faturës]</span></div>
                                </div>
                                <div class="invoice-details">
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
                                    <div class="row">
                                        <div class="col-md-6 text-right">
                                            <strong>Data e Faturës:</strong>
                                            <p id="preview_invoice_date">[Data e Faturës]</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="invoice-body">
                                    <table class="table">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
    function checkNewCompany() {
        var selectBox = document.getElementById("company_name_select");
        var newCompanyInput = document.getElementById("new_company_name");
        newCompanyInput.style.display = (selectBox.value === "new") ? "block" : "none";
    }

    function previewFiles() {
        var preview = document.getElementById('filePreview');
        preview.innerHTML = ""; // Clear existing preview content
        var files = document.querySelector('input[type=file]').files;

        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var reader = new FileReader();
            reader.onload = (function(theFile) {
                return function(e) {
                    var fileType = theFile.type.split('/')[0];
                    var span = document.createElement('span');
                    span.style.marginRight = "15px";

                    if (fileType === "image") {
                        span.innerHTML = '<img src="' + e.target.result + '" width="450px" height="100px" />';
                    } else if (theFile.type === "application/pdf") {
                        span.innerHTML = '<embed src="' + e.target.result + '" type="application/pdf" width="100px" height="100px">';
                    }
                    preview.appendChild(span);
                };
            })(file);
            reader.readAsDataURL(file);
        }
    }

    function updatePreview() {
        const albanianMonths = [
            'Janar', 'Shkurt', 'Mars', 'Prill', 'Maj', 'Qershor',
            'Korrik', 'Gusht', 'Shtator', 'Tetor', 'Nëntor', 'Dhjetor'
        ];

        function formatDate(dateString) {
            if (dateString) {
                var parts = dateString.split('-');
                var day = parts[2];
                var month = albanianMonths[parseInt(parts[1], 10) - 1];
                var year = parts[0];
                return `${day} ${month} ${year}`;
            }
            return '';
        }
        var invoiceDateInput = document.getElementById('invoice_date').value;
        var formattedInvoiceDate = formatDate(invoiceDateInput);
        document.getElementById('preview_invoice_date').innerText = formattedInvoiceDate || '[Data e Faturës]';
        document.getElementById('preview_description').innerText = document.getElementById('description').value || '[Përshkrimi]';
        document.getElementById('preview_more_details').innerText = document.getElementById('more_details').value || '[Detaje Shtesë]';
        document.getElementById('preview_category').innerText = document.getElementById('category').value || '[Kategoria]';
        document.getElementById('preview_invoice_number').innerText = document.getElementById('invoice_number').value || '[Numri i Faturës]';

        var companyNameSelect = document.getElementById('company_name_select').value;
        if (companyNameSelect === 'new') {
            var newCompanyNameInput = document.getElementById('new_company_name').value;
            document.getElementById('preview_company_name').innerText = newCompanyNameInput || '[Emri i Firmës]';
        } else {
            var selectedCompany = document.getElementById('company_name_select').options[document.getElementById('company_name_select').selectedIndex].text;
            document.getElementById('preview_company_name').innerText = selectedCompany || '[Emri i Firmës]';
        }
    }
</script>
<style>
    .card {
        border-radius: 15px;
        overflow: hidden;
    }

    .form-control,
    .btn {
        border-radius: 10px;
    }

    #filePreview img,
    #filePreview embed {
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

    .invoice-header {
        border-bottom: 2px solid #333;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }

    .invoice-number {
        font-size: 18px;
        color: #666;
    }

    .invoice-details {
        margin-bottom: 20px;
    }

    .invoice-body {
        margin-bottom: 20px;
    }
</style>
<?php include('partials/footer.php'); ?>