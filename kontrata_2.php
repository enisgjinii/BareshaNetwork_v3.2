<?php
include 'partials/header.php';
include 'page_access_controller.php';

// Fetch clients data
$sql = "SELECT * FROM klientet ORDER BY emri ASC";
$result = $conn->query($sql);
$clients = $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<script src="ajax.js"></script>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <!-- Breadcrumb Navigation -->
            <nav class="bg-white px-3 py-2 rounded-5 mb-4" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#" class="text-reset text-decoration-none">Kontratat</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Kontrata e re (Këngë)
                    </li>
                </ol>
            </nav>

            <!-- Contract Form Card -->
            <div class="card p-5 shadow-sm rounded-5">
                <div class="row">
                    <!-- Form Section -->
                    <div class="col-lg-6 mb-4">
                        <form id="contractForm" enctype="multipart/form-data">
                            <?php
                            $fields = [
                                ["label" => "Emri", "name" => "emri", "type" => "text", "placeholder" => "Sheno emrin"],
                                ["label" => "Mbiemri", "name" => "mbiemri", "type" => "text", "placeholder" => "Sheno mbiemrin"],
                                ["label" => "Numri i telefonit", "name" => "numri_tel", "type" => "text", "placeholder" => "Sheno numrin e telefonit"],
                                ["label" => "Numri personal", "name" => "numri_personal", "type" => "text", "placeholder" => "Sheno numrin personal"],
                            ];
                            foreach ($fields as $field) {
                                echo "<div class='mb-3'>
                                        <label class='form-label fw-semibold' for='{$field['name']}'>{$field['label']}</label>
                                        <input type='{$field['type']}' name='{$field['name']}' id='{$field['name']}' class='form-control rounded-4 border' required placeholder='{$field['placeholder']}'>
                                      </div>";
                            }
                            ?>
                            <!-- Klienti Select -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="klienti22">Klienti</label>
                                <select name="klienti" id="klienti22" class="form-select rounded-4 border" onchange="showEmail(this)" required>
                                    <option value="" disabled selected>Zgjidhni një klient</option>
                                    <?php foreach ($clients as $client) {
                                        echo "<option value='{$client['emri']}|{$client['emailadd']}|{$client['emriart']}'>{$client['emri']}</option>";
                                    } ?>
                                </select>
                            </div>
                            <?php
                            $additionalFields = [
                                ["label" => "Adresa e email-it", "name" => "email", "type" => "email", "placeholder" => "Sheno adresen e email-it"],
                                ["label" => "Emri artistik", "name" => "emriartistik", "type" => "text", "placeholder" => "Sheno emrin artistik"],
                                ["label" => "Vepra", "name" => "vepra", "type" => "text", "placeholder" => "Sheno veprën"],
                                ["label" => "Data", "name" => "data", "type" => "date"],
                                ["label" => "Ngarko kontratën", "name" => "pdf_file", "type" => "file", "attributes" => "accept='.docx,.pdf' onchange='validateFile(this)'"],  // Removed 'required'
                                ["label" => "Përqindja (Baresha)", "name" => "perqindja", "type" => "number", "placeholder" => "Sheno përqindjen", "attributes" => "onchange='updatePerqindjaOther()'"],
                                ["label" => "Përqindja (Klienti)", "name" => "perqindja_other", "type" => "number", "readonly" => true],
                                ["label" => "Shënime", "name" => "shenime", "type" => "textarea", "rows" => 5, "placeholder" => "Shëno..."]
                            ];
                            foreach ($additionalFields as $field) {
                                if ($field['type'] == 'textarea') {
                                    echo "<div class='mb-3'>
                                            <label class='form-label fw-semibold' for='{$field['name']}'>{$field['label']}</label>
                                            <textarea name='{$field['name']}' id='{$field['name']}' class='form-control rounded-4 border' rows='{$field['rows']}' placeholder='{$field['placeholder']}' required></textarea>
                                          </div>";
                                } else {
                                    $attributes = isset($field['attributes']) ? $field['attributes'] : '';
                                    $readonly = isset($field['readonly']) ? 'readonly' : '';
                                    $required = isset($field['required']) && $field['required'] ? 'required' : '';
                                    echo "<div class='mb-3'>
                                            <label class='form-label fw-semibold' for='{$field['name']}'>{$field['label']}</label>
                                            <input type='{$field['type']}' name='{$field['name']}' id='{$field['name']}' class='form-control rounded-4 border' placeholder='{$field['placeholder']}' {$attributes} {$readonly} {$required}>
                                          </div>";
                                }
                            }
                            ?>
                            <button type="submit" class="btn btn-primary w-100 py-2 rounded-4">
                                <i class="fi fi-rr-paper-plane me-2"></i>Dërgo
                            </button>
                        </form>
                    </div>

                    <!-- Preview Section -->
                    <div class="col-lg-6">
                        <h5 class="mb-3">Preview e Kontratës</h5>
                        <div id="contractContent" class="p-3 border rounded-4 bg-light" style="min-height: 300px;">
                            <!-- Live preview will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Viewing Documents -->
    <div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="documentModalLabel" class="modal-title">Dokumenti</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe id="documentViewer" src="" frameborder="0" style="width: 100%; height: 500px;"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('partials/footer.php'); ?>
<script>
    // Initialize Toastr options
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "5000",
    };

    // Event listeners for form inputs to update preview
    document.querySelectorAll('#contractForm input, #contractForm select, #contractForm textarea').forEach(input => {
        input.addEventListener('input', updatePreview);
    });

    // Show email and artist name based on selected client
    function showEmail(select) {
        const [_, email, emriartistik] = select.value.split("|");
        document.getElementById("email").value = email || "Klienti që keni zgjedhur nuk ka adresë te emailit";
        document.getElementById("emriartistik").value = emriartistik || "";
        updatePreview();
    }

    // Update the 'perqindja_other' field based on 'perqindja'
    function updatePerqindjaOther() {
        const perqindja = parseFloat(document.getElementById('perqindja').value);
        document.getElementById('perqindja_other').value = isNaN(perqindja) ? "" : 100 - perqindja;
        updatePreview();
    }

    // Update the contract preview by fetching from the server
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
                toastr.error('Dështoi për të ngarkuar preview-n e kontratës.');
            });
    }

    // Initialize Selectr for the klienti select element
    new Selectr('#klienti22', {
        searchable: true
    });

    // Initialize flatpickr for the date input
    $("input[name='data']").flatpickr({
        dateFormat: "Y-m-d",
        maxDate: "today",
    });

    // Validate the uploaded file
    function validateFile(input) {
        const file = input.files[0];
        const maxSize = 10 * 1024 * 1024; // 10MB
        const allowedTypes = [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/pdf'
        ];

        if (!file) {
            toastr.warning('Ju lutem zgjidhni një skedar.');
            return;
        }

        if (!allowedTypes.includes(file.type)) {
            toastr.error('Ju lutem zgjidhni një lloj skedari të vlefshëm (docx ose pdf).');
            input.value = '';
            return;
        }

        if (file.size > maxSize) {
            toastr.error('Madhësia e skedarit tejkalon limitin (10MB).');
            input.value = '';
            return;
        }

        // If validation passes, update the preview
        updatePreview();
    }

    // Handle form submission via AJAX
    document.getElementById('contractForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        fetch('submit-contract.php', { // Ensure this endpoint exists and handles the form submission
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success('Kontrata u krijua me sukses!');
                    form.reset();
                    updatePreview();
                } else {
                    toastr.error(data.message || 'Dështoi krijimi i kontratës.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error('Dështoi dërgimi i formularit.');
            });
    });

    // Function to view documents in the modal
    $(document).on('click', '.view-document', function(e) {
        e.preventDefault();
        const file = decodeURIComponent($(this).data('file'));
        const name = decodeURIComponent($(this).data('name'));
        $('#documentModalLabel').text(name);
        $('#documentViewer').attr('src', file);
    });

    // Function to show the replace modal (Ensure you have a corresponding modal)
    function showReplaceModal(id) {
        // Implement the logic to show the replace modal
        // For example, populate the modal with relevant data and then show it
        toastr.info('Replace modal functionality to be implemented.');
    }

    // Function to confirm deletion (Implement deletion logic)
    function confirmDelete(id) {
        if (confirm('A jeni të sigurtë që dëshironi të fshini këtë kontratë?')) {
            // Implement the deletion via AJAX
            fetch(`delete-contract.php?id=${id}`, { // Ensure this endpoint exists
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success('Kontrata u fshi me sukses.');
                        // Optionally, remove the contract from the UI or refresh the table
                        location.reload(); // Simple approach to refresh the page
                    } else {
                        toastr.error(data.message || 'Dështoi fshirja e kontratës.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('Dështoi dështoi fshirja e kontratës.');
                });
        }
    }
</script>