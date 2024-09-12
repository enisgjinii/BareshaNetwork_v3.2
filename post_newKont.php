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
function handleFileUpload($file)
{
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($file["name"]);
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if (!in_array($fileType, ['pdf', 'png', 'jpg', 'jpeg'])) {
        displayError('Lejohen vetëm skedarët PDF, PNG, JPG ose JPEG.');
        return false;
    }
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $target_file;
    } else {
        displayError('Ka pasur një gabim gjatë ngarkimit të skedarit tuaj.');
        return false;
    }
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
    // Handle file upload
    $document_path = null;
    if (!empty($_FILES["document"]["name"])) {
        $document_path = handleFileUpload($_FILES["document"]);
        if (!$document_path) {
            exit;
        }
    }
    // Insert data into MySQL database
    $sql = "INSERT INTO invoices_kont (invoice_creation_date, invoice_date, description, more_details, registrant, category, company_name, invoice_number, document_path, vlera_faktura)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssss", $invoice_creation_date, $invoice_date, $description, $more_details, $registrant, $category, $companyName, $invoice_number, $document_path, $valueOfInvoice);
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
                            <form method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label>Data e faturës <i class="fi fi-rr-interrogation text-info ml-1" data-toggle="tooltip" title="Data kur është lëshuar fatura nga furnitori"></i></label>
                                    <input type="date" class="form-control rounded-5 border border-2" name="invoice_date" id="invoice_date" required oninput="updatePreview()">
                                </div>
                                <div class="form-group">
                                    <label>Përshkrimi <i class="fi fi-rr-interrogation text-info ml-1" data-toggle="tooltip" title="Një përshkrim i shkurtër i faturës"></i></label>
                                    <input type="text" class="form-control rounded-5 border border-2" name="description" id="description" required oninput="updatePreview()">
                                </div>
                                <div class="form-group">
                                    <label>Detaje shtesë <i class="fi fi-rr-interrogation text-info ml-1" data-toggle="tooltip" title="Informacione shtesë për faturën"></i></label>
                                    <textarea class="form-control rounded-5 border border-2" name="more_details" id="more_details" rows="3" oninput="updatePreview()"></textarea>
                                </div>
                                <div class="form-group" hidden>
                                    <label>Regjistruesi <i class="fi fi-rr-interrogation text-info ml-1" data-toggle="tooltip" title="Personi që po regjistron faturën"></i></label>
                                    <input type="text" class="form-control rounded-5 border border-2" name="registrant" id="registrant" value="<?php echo $user_info['givenName'] . ' ' . $user_info['familyName']; ?>" readonly oninput="updatePreview()">
                                </div>
                                <div class="form-group">
                                    <label>Kategoria <i class="fi fi-rr-interrogation text-info ml-1" data-toggle="tooltip" title="Kategoria e faturës"></i></label>
                                    <select class="form-control rounded-5 border border-2" name="category" id="category" oninput="updatePreview()">
                                        <option value="Shpenzimet">Shpenzimet</option>
                                        <option value="Investimet">Investimet</option>
                                        <option value="Obligime">Obligime</option>
                                        <option value="Tjetër">Tjetër</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Emri i firmës <i class="fi fi-rr-interrogation text-info ml-1" data-toggle="tooltip" title="Emri i kompanisë që ka lëshuar faturën"></i></label>
                                    <select class="form-control rounded-5 border border-2" name="company_name" id="company_name_select" required onchange="checkNewCompany(); updatePreview();">
                                        <option value="">Zgjidh një kompani ekzistuese</option>
                                        <?php
                                        $sql = "SELECT DISTINCT company_name FROM invoices_kont";
                                        $stmt = $conn->query($sql);
                                        while ($row = $stmt->fetch_assoc()) {
                                            echo "<option value='" . htmlspecialchars($row['company_name']) . "'>" . htmlspecialchars($row['company_name']) . "</option>";
                                        }
                                        ?>
                                        <option value="new">Shto një kompani të re</option>
                                    </select>
                                    <input type="text" class="form-control rounded-5 border border-2 mt-2" name="new_company_name" id="new_company_name" placeholder="Shkruaj një kompani të re" style="display:none;">
                                </div>
                                <div class="form-group">
                                    <label>Numri i faturës <i class="fi fi-rr-interrogation text-info ml-1" data-toggle="tooltip" title="Numri unik i faturës"></i></label>
                                    <input type="text" class="form-control rounded-5 border border-2" name="invoice_number" id="invoice_number" required oninput="updatePreview()">
                                </div>
                                <div class="form-group">
                                    <label>Vlera e fatures <i class="fi fi-rr-interrogation text-info ml-1" data-toggle="tooltip" title="Vlera e fatures"></i></label>
                                    <input type="text" class="form-control rounded-5 border border-2" name="valueOfInvoice" id="valueOfInvoice" required oninput="updatePreview()">
                                </div>
                                <div class="form-group">
                                    <label>Ngarko dokument <i class="fi fi-rr-interrogation text-info ml-1" data-toggle="tooltip" title="Ngarko një kopje të faturës (pdf, png, jpg, jpeg)"></i></label>
                                    <input type="file" class="form-control rounded-5 border border-2-5" name="document" accept=".pdf,.png,.jpg,.jpeg">
                                </div>
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
                                        <!-- <div class="col-md-6">
                                            <strong>Data e Krijimit:</strong>
                                            <p id="preview_invoice_creation_date">[Data e Krijimit]</p>
                                        </div> -->
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
                                <!-- <div class="invoice-footer">
                                    <p><strong>Regjistruar nga:</strong> <span id="preview_registrant"><?php echo $user_info['givenName'] . ' ' . $user_info['familyName']; ?></span></p>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<!-- JavaScript për Përditësimin e Parapamjes së Faturës -->
<script>
    function checkNewCompany() {
        var selectBox = document.getElementById("company_name_select");
        var newCompanyInput = document.getElementById("new_company_name");
        newCompanyInput.style.display = (selectBox.value === "new") ? "block" : "none";
    }
    function updatePreview() {
        // Define Albanian month names
        const albanianMonths = [
            'Janar', 'Shkurt', 'Mars', 'Prill', 'Maj', 'Qershor',
            'Korrik', 'Gusht', 'Shtator', 'Tetor', 'Nëntor', 'Dhjetor'
        ];
        // Function to format date
        function formatDate(dateString) {
            if (dateString) {
                var parts = dateString.split('-'); // Expecting yyyy-mm-dd format
                var day = parts[2];
                var month = albanianMonths[parseInt(parts[1], 10) - 1];
                var year = parts[0];
                return `${day} ${month} ${year}`;
            }
            return '';
        }
        // Update invoice issue date
        var invoiceDateInput = document.getElementById('invoice_date').value;
        var formattedInvoiceDate = formatDate(invoiceDateInput);
        document.getElementById('preview_invoice_date').innerText = formattedInvoiceDate || '[Data e Faturës]';
        // Update other fields in the preview
        document.getElementById('preview_description').innerText = document.getElementById('description').value || '[Përshkrimi]';
        document.getElementById('preview_more_details').innerText = document.getElementById('more_details').value || '[Detaje Shtesë]';
        document.getElementById('preview_category').innerText = document.getElementById('category').value || '[Kategoria]';
        document.getElementById('preview_invoice_number').innerText = document.getElementById('invoice_number').value || '[Numri i Faturës]';
        // Update company name in preview based on whether a new company is selected or an existing one
        var companyNameSelect = document.getElementById('company_name_select').value;
        if (companyNameSelect === 'new') {
            // If "new" is selected, use the value of the new company input field
            var newCompanyNameInput = document.getElementById('new_company_name').value;
            document.getElementById('preview_company_name').innerText = newCompanyNameInput || '[Emri i Firmës]';
        } else {
            // Otherwise, use the selected existing company name from the dropdown
            var selectedCompany = document.getElementById('company_name_select').options[document.getElementById('company_name_select').selectedIndex].text;
            document.getElementById('preview_company_name').innerText = selectedCompany || '[Emri i Firmës]';
        }
    }
    // Initialize tooltips
    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
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
    .invoice-footer {
        border-top: 1px solid #ddd;
        padding-top: 15px;
    }
    .fi-rr-interrogation {
        cursor: help;
    }
</style>
<?php include('partials/footer.php'); ?>