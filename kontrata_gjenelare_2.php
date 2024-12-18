<?php
include 'partials/header.php';

// Initialize messages
$mesazhi_sukses = $mesazhi_error = "";
// Prepare the SQL statement with the corrected WHERE clause
$sql = "SELECT emri, emailadd, emriart, youtube, nrllog, (100 - perqindja) AS perqindja, np, nrtel 
        FROM klientet 
        WHERE aktiv != ? OR aktiv IS NULL
        ORDER BY id DESC";
// Initialize the prepared statement
$stmt = $conn->prepare($sql);
if ($stmt) {
    // Bind the parameter. Assuming 'aktiv' is a string. Adjust the type if necessary.
    $aktiv_value = '1';
    $stmt->bind_param("s", $aktiv_value);
    // Execute the statement
    if ($stmt->execute()) {
        // Get the result set from the executed statement
        $result = $stmt->get_result();
        // Fetch all clients as an associative array
        $clients = $result->fetch_all(MYSQLI_ASSOC);
        // Optional: Check if clients are found
        if (empty($clients)) {
            $mesazhi_error = "Nuk u gjetën kliente të përshtatshme.";
        }
        // Free the result set
        $result->free();
    } else {
        // Execution failed
        $mesazhi_error = "Gabim në ekzekutimin e pyetjes: " . $stmt->error;
    }
    // Close the prepared statement
    $stmt->close();
} else {
    // Preparation failed
    $mesazhi_error = "Gabim në përgatitjen e pyetjes: " . $conn->error;
}
// Close the database connection if no further database operations are needed
// If other parts of your application still need the connection, consider removing this line
$conn->close();
// Now, you can use the $clients array in your HTML below
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col">
                    <!-- Breadcrumb Navigation -->
                    <nav class="bg-white px-3 py-2 rounded-5" aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#" class="text-reset text-decoration-none">Kontratat</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><a href="<?= __FILE__ ?>" class="text-reset text-decoration-none">Kontrata e re (Gjenerale)</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="row">
                <!-- Contract Creation Form -->
                <div class="col-lg-6">
                    <div class="card rounded-5 border">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Krijimi i Kontratës së Re</h4>
                            <form id="contractForm" method="post" action="dorzoKontraten.php" enctype="multipart/form-data" onsubmit="return validateForm()">
                                <div class="row g-3">
                                    <!-- Client Selection Dropdown -->
                                    <div class="col-md-12">
                                        <label for="artisti" class="form-label">Klienti</label>
                                        <select name="artisti" id="artisti" class="form-select rounded-5" onchange="populateFields(this);" data-bs-toggle="tooltip" data-bs-placement="right" title="Zgjidhni një klient nga lista. Zgjedhja automatikisht plotëson disa fusha të tjera si Emri, Email, dhe Përqindja e Klientit.">
                                            <option value="" selected disabled>Zgjidhni një klient</option>
                                            <?php foreach ($clients as $client): ?>
                                                <option value="<?= htmlspecialchars(json_encode($client)) ?>">
                                                    <?= htmlspecialchars($client['emri']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <!-- **Added Count Below Dropdown** -->
                                        <?php if (!empty($clients)): ?>
                                            <small class="form-text text-muted mt-2">
                                                <strong>Total Kliente:</strong> <?= count($clients) ?>
                                            </small>
                                        <?php else: ?>
                                            <small class="form-text text-muted mt-2">
                                                <strong>Total Kliente:</strong> 0
                                            </small>
                                        <?php endif; ?>
                                        <!-- End of Added Count -->
                                        <div class="invalid-feedback">
                                            Ju lutem zgjidhni një klient.
                                        </div>
                                    </div>
                                    <script>
                                        new Selectr('#artisti', {
                                            'placeholder': 'Zgjidhni një klient',
                                            'search': true,
                                            'searchPlaceholder': 'Zgjidhni një klient',
                                            'searchable': true
                                        })
                                    </script>
                                    <!-- Input Fields for Contract Details -->
                                    <?php
                                    $fields = [
                                        'emri' => 'Emri dhe mbiemri',
                                        'numri_tel' => 'Numri i telefonit',
                                        'numri_personal' => 'Numri personal',
                                        'email' => 'Adresa e email-it',
                                        'youtube_id' => 'ID-ja e kanalit në YouTube',
                                        'emriartistik' => 'Emri artistik',
                                        'numri_xhiroBanka' => 'Numri i xhirollogarisë bankare',
                                        'tvsh' => 'Përqindja (Klientit)',
                                        'pronari_xhiroBanka' => 'Pronari i xhirollogarisë bankare',
                                        'kodi_swift' => 'Kodi SWIFT',
                                        'iban' => 'IBAN',
                                        'emri_bankes' => 'Emri i bankës',
                                        'adresa_bankes' => 'Adresa e bankës'
                                    ];
                                    foreach ($fields as $name => $label): ?>
                                        <div class="col-md-6">
                                            <label for="<?= $name ?>" class="form-label"><?= $label ?></label>
                                            <input type="text" name="<?= $name ?>" id="<?= $name ?>" class="form-control rounded-5" placeholder="Shëno <?= strtolower($label) ?>"
                                                <?= in_array($name, ['emri', 'numri_tel', 'numri_personal', 'email', 'youtube_id', 'pronari_xhiroBanka', 'numri_xhiroBanka', 'tvsh', 'emriartistik']) ? 'readonly' : '' ?>
                                                data-bs-toggle="tooltip" data-bs-placement="right" title="<?= getTooltip($name, $label) ?>">
                                            <div class="invalid-feedback">
                                                Ju lutem plotësoni këtë fushë.
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <!-- Duration of the Contract -->
                                    <div class="col-md-6">
                                        <label for="kohezgjatja" class="form-label">Kohëzgjatja në muaj</label>
                                        <input type="number" name="kohezgjatja" id="kohezgjatja" class="form-control rounded-5" placeholder="Shëno kohëzgjatjen e kontratës" min="1"
                                            data-bs-toggle="tooltip" data-bs-placement="right" title="Vendosni numrin e muajve për sa kohë do të qëndrojë kontrata aktive. P.sh., 12 për një vit.">
                                        <div class="invalid-feedback">
                                            Ju lutem vendosni një kohëzgjatje të vlefshme.
                                        </div>
                                    </div>
                                    <!-- File Upload Field -->
                                    <div class="col-md-12">
                                        <label for="fileUpload" class="form-label">Ngarkoni Dokumentet</label>
                                        <div class="dropzone" id="dropzone" data-bs-toggle="tooltip" data-bs-placement="right" title="Ngarkoni dokumentet e nevojshme për kontratën, si kontratat e nënshkruara, identifikimi, etj. Mbështeten format .pdf, .doc, .docx, .jpg, .jpeg, dhe .png.">
                                            <input type="file" name="documents[]" id="fileUpload" class="form-control" multiple accept=".pdf, .doc, .docx, .jpg, .jpeg, .png">
                                            <p class="mt-2">Drag & Drop files këtu ose klikoni për të zgjedhur.</p>
                                        </div>
                                        <div id="filePreview" class="mt-3"></div>
                                        <div class="invalid-feedback" id="fileUploadFeedback">
                                            Ju lutem ngarkoni të paktën një dokument.
                                        </div>
                                    </div>
                                    <!-- Additional Fields -->
                                    <div class="col-md-12">
                                        <label for="shenim" class="form-label">Shenim</label>
                                        <textarea name="shenim" id="shenim" class="form-control rounded-5" placeholder="Shëno shenimin e kontratës" rows="3" data-bs-toggle="tooltip" data-bs-placement="right" title="Shtoni çdo shënim të nevojshëm për kontratën, si kushtet specifike apo detajet shtesë që mund të jenë të rëndësishme."></textarea>
                                    </div>
                                </div>
                                <!-- Submit Button -->
                                <div class="mt-4 d-flex justify-content-end">
                                    <button type="submit" class="input-custom-css px-3 py-2">
                                        <i class="fi fi-rr-memo-circle-check me-2"></i>Krijo kontratën
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Live Preview Section -->
                <div class="col-lg-6 mt-4 mt-lg-0">
                    <div class="card rounded-5 border">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Parashikim i Kontratës</h5>
                            <div id="contractPreview" class="p-3 border rounded-3" style="background-color: #f8f9fa; min-height: 300px;">
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
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="filePreviewModalLabel">Preview e Dokumentit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Mbyll"></button>
                    </div>
                    <div class="modal-body">
                        <iframe id="filePreviewIframe" src="" width="100%" height="500px"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('partials/footer.php'); ?>
<!-- Additional JavaScript for Enhanced Features -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
        // Live Preview Functionality
        const formFields = {
            emri: 'previewEmri',
            email: 'previewEmail',
            emriartistik: 'previewEmriArtistik',
            youtube_id: 'previewYouTube',
            numri_tel: 'previewNumriTel',
            numri_personal: 'previewNumriPersonal',
            numri_xhiroBanka: 'previewNumriXhiroBanka',
            tvsh: 'previewPerqindja',
            kohezgjatja: 'previewKohezgjatja',
            shenim: 'previewShenim'
        };
        Object.keys(formFields).forEach(field => {
            const input = document.getElementById(field);
            const preview = document.getElementById(formFields[field]);
            if (input && preview) {
                input.addEventListener('input', () => {
                    preview.textContent = input.value.trim() || '---';
                });
            }
        });
        // Function to populate form fields based on selected client
        window.populateFields = function(select) {
            const selectedData = JSON.parse(select.value);
            const mapping = {
                emri: selectedData.emri || '',
                email: selectedData.emailadd || '',
                emriartistik: selectedData.emriart || '',
                youtube_id: selectedData.youtube || '',
                numri_tel: selectedData.nrtel || '',
                numri_personal: selectedData.np || '',
                numri_xhiroBanka: selectedData.nrllog || '',
                tvsh: selectedData.perqindja || ''
            };
            for (const [field, value] of Object.entries(mapping)) {
                const input = document.getElementById(field);
                if (input) {
                    input.value = sanitize(value);
                    if (value) {
                        input.setAttribute('readonly', 'readonly');
                    } else {
                        input.removeAttribute('readonly');
                    }
                }
                // Update live preview
                const preview = document.getElementById(formFields[field]);
                if (preview) {
                    preview.textContent = value.trim() || '---';
                }
            }
            // Handle additional fields that are not part of the initial mapping
            const additionalFields = ['pronari_xhiroBanka', 'kodi_swift', 'iban', 'emri_bankes', 'adresa_bankes'];
            additionalFields.forEach(field => {
                const input = document.getElementById(field);
                if (input) {
                    input.value = ''; // You can map additional data if available
                    input.removeAttribute('readonly');
                }
            });
        }
        // Sanitize function to prevent XSS
        function sanitize(value) {
            const temp = document.createElement('div');
            temp.textContent = value;
            return temp.innerHTML;
        }
        // Form Validation Function
        window.validateForm = function() {
            let isValid = true;
            const form = document.getElementById('contractForm');
            // Remove the requiredFields array as we no longer require fields
            // const requiredFields = ['artisti', 'kohezgjatja', 'fileUpload'];
            // requiredFields.forEach(field => {
            //     const input = document.getElementById(field);
            //     if (input && !input.value.trim()) {
            //         input.classList.add('is-invalid');
            //         isValid = false;
            //     } else if (input) {
            //         input.classList.remove('is-invalid');
            //     }
            // });
            // Additional custom validations
            const perqindja = parseFloat(document.getElementById('tvsh').value);
            if (!isNaN(perqindja) && (perqindja < 0 || perqindja >= 100)) {
                document.getElementById('tvsh').classList.add('is-invalid');
                isValid = false;
            } else {
                document.getElementById('tvsh').classList.remove('is-invalid');
            }
            // File Upload Validation (optional)
            const fileInput = document.getElementById('fileUpload');
            if (fileInput.files.length > 0) {
                // Validate file types and sizes
                const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'];
                const maxSize = 5 * 1024 * 1024; // 5MB per file
                for (let i = 0; i < fileInput.files.length; i++) {
                    const file = fileInput.files[i];
                    if (!allowedTypes.includes(file.type)) {
                        alert(`Lloji i skedarit ${file.name} nuk është i lejuar.`);
                        isValid = false;
                        break;
                    }
                    if (file.size > maxSize) {
                        alert(`Skedari ${file.name} tejkalon madhësinë maksimale prej 5MB.`);
                        isValid = false;
                        break;
                    }
                }
                if (isValid) {
                    document.getElementById('fileUpload').classList.remove('is-invalid');
                }
            }
            // Remove the alert for incomplete required fields
            // if (!isValid) {
            //     alert('Ju lutem plotësoni të gjitha fushat e kërkuara dhe kontrolloni gabimet.');
            // }
            return isValid;
        }
        // File Upload Preview Functionality
        const fileUpload = document.getElementById('fileUpload');
        const filePreview = document.getElementById('filePreview');
        fileUpload.addEventListener('change', handleFileSelect);

        function handleFileSelect(event) {
            const files = event.target.files;
            filePreview.innerHTML = ''; // Clear previous previews
            if (files.length === 0) {
                filePreview.innerHTML = '<p>Nuk ka skedarë të ngarkuar.</p>';
                return;
            }
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const fileURL = URL.createObjectURL(file);
                const fileType = file.type.startsWith('image/') ? 'image' : 'document';
                const fileContainer = document.createElement('div');
                fileContainer.classList.add('mb-3', 'd-flex', 'align-items-center');
                if (fileType === 'image') {
                    const img = document.createElement('img');
                    img.src = fileURL;
                    img.alt = file.name;
                    img.classList.add('img-thumbnail', 'me-2');
                    img.style.width = '100px';
                    img.style.height = '100px';
                    img.style.objectFit = 'cover';
                    fileContainer.appendChild(img);
                } else {
                    const icon = document.createElement('i');
                    icon.classList.add('fi', 'fi-rr-file-alt', 'fs-3', 'me-2');
                    fileContainer.appendChild(icon);
                }
                const fileName = document.createElement('span');
                fileName.textContent = file.name;
                fileName.style.cursor = 'pointer';
                fileName.style.textDecoration = 'underline';
                fileName.style.color = '#0d6efd';
                fileName.addEventListener('click', () => {
                    if (fileType === 'image') {
                        // Show image in modal
                        const iframe = document.getElementById('filePreviewIframe');
                        iframe.src = fileURL;
                        const myModal = new bootstrap.Modal(document.getElementById('filePreviewModal'));
                        myModal.show();
                    } else {
                        // Show document in iframe
                        const iframe = document.getElementById('filePreviewIframe');
                        iframe.src = fileURL;
                        const myModal = new bootstrap.Modal(document.getElementById('filePreviewModal'));
                        myModal.show();
                    }
                });
                fileContainer.appendChild(fileName);
                filePreview.appendChild(fileContainer);
            }
        }
        // Drag-and-Drop Functionality
        const dropzone = document.getElementById('dropzone');
        dropzone.addEventListener('dragover', (event) => {
            event.preventDefault();
            dropzone.classList.add('bg-light');
        });
        dropzone.addEventListener('dragleave', (event) => {
            event.preventDefault();
            dropzone.classList.remove('bg-light');
        });
        dropzone.addEventListener('drop', (event) => {
            event.preventDefault();
            dropzone.classList.remove('bg-light');
            const files = event.dataTransfer.files;
            fileUpload.files = files;
            handleFileSelect({
                target: {
                    files: files
                }
            });
        });
        // Prevent default behavior for drag events on the document
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            document.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
            });
        });
    });
</script>
<!-- Include Bootstrap JS (Ensure this is included if not already in your footer.php) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Include Font Icons (Ensure you have the correct icon library) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fonticons/1.0.0/fonticons.min.css" integrity="sha512-YOUR_INTEGRITY_HASH" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    /* Custom styles for the drag-and-drop area */
    .dropzone {
        border: 2px dashed #ced4da;
        border-radius: 0.25rem;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .dropzone.bg-light {
        background-color: #e9ecef;
    }

    /* File preview styles */
    #filePreview img {
        margin-bottom: 10px;
    }

    #filePreview i {
        color: #6c757d;
    }
</style>
<?php
// Function to get more descriptive tooltips
function getTooltip($fieldName, $label)
{
    $tooltips = [
        'emri' => "Emri dhe Mbiemri: Shkruani emrin e plotë të klientit, si Emri dhe Mbiemri.",
        'numri_tel' => "Numri i Telefonit: Shkruani një numër telefoni të vlefshëm për kontakt me klientin. P.sh., +383 44 444 444",
        'numri_personal' => "Numri Personal: Shkruani numrin personal të identifikimit të klientit (NIPT ose një numër tjetër identifikues).",
        'email' => "Adresa e Email-it: Shkruani një adresë email-i të vlefshme për komunikim me klientin. P.sh., klienti@example.com.",
        'youtube_id' => "ID-ja e Kanalit në YouTube: Shkruani identifikuesin unik të kanalit të YouTube të klientit. P.sh., UCPveY6zZb2O3YUSI20JftyQ.",
        'emriartistik' => "Emri Artistik: Shkruani emrin artistik të klientit nëse ai/ajo përdor një emër tjetër për publikun.",
        'numri_xhiroBanka' => "Numri i Xhirollogarisë Bankare: Shkruani numrin e saktë të llogarisë bankare të klientit për transaksione të sigurta.",
        'tvsh' => "Përqindja (Klientit): Shkruani përqindjen që klienti merr nga fitimet ose xhiro. Sigurohuni që përqindja të jetë midis 0 dhe 100.",
        'pronari_xhiroBanka' => "Pronari i Xhirollogarisë Bankare: Shkruani emrin e pronarit të llogarisë bankare të klientit, nëse është e nevojshme.",
        'kodi_swift' => "Kodi SWIFT: Shkruani kodin SWIFT të bankës së klientit për transaksione ndërkombëtare.",
        'iban' => "IBAN: Shkruani numrin e llogarisë bankare ndërkombëtare (IBAN) të klientit për transferime të shpejta dhe të sigurta.",
        'emri_bankes' => "Emri i Bankës: Shkruani emrin e plotë të bankës ku klienti ka llogarinë.",
        'adresa_bankes' => "Adresa e Bankës: Shkruani adresën fizike të bankës ku klienti ka llogarinë.",
        'kohezgjatja' => "Kohëzgjatja në Muaj: Shkruani numrin e muajve për sa kohë do të qëndrojë kontrata aktive. P.sh., 12 për një vit.",
        'shenim' => "Shenim: Shtoni çdo shënim të nevojshëm për kontratën, si kushtet specifike apo detajet shtesë që mund të jenë të rëndësishme."
    ];
    return isset($tooltips[$fieldName]) ? $tooltips[$fieldName] : "Plotësoni këtë fushë.";
}
?>