<?php
// Use require_once to ensure files are included only once and are mandatory
require_once 'partials/header.php';
require_once 'page_access_controller.php';

// Ensure $conn is defined
if (!isset($conn)) {
    // Handle the missing connection appropriately
    error_log("Database connection not established.");
    die("Internal server error. Please try again later.");
}

// Fetch clients data with error handling
$sql = "SELECT * FROM klientet ORDER BY emri ASC";
$result = $conn->query($sql);

if ($result === false) {
    // Log the error and handle gracefully
    error_log("Database query failed: " . $conn->error);
    $clients = [];
    // Optionally, display a user-friendly message
    echo "<div class='alert alert-danger'>Failed to load clients. Please try again later.</div>";
} else {
    $clients = $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

// Close the database connection if not needed further
// $conn->close(); // Uncomment if appropriate
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                                // Escape output to prevent XSS
                                $label = htmlspecialchars($field['label'], ENT_QUOTES, 'UTF-8');
                                $name = htmlspecialchars($field['name'], ENT_QUOTES, 'UTF-8');
                                $type = htmlspecialchars($field['type'], ENT_QUOTES, 'UTF-8');
                                $placeholder = htmlspecialchars($field['placeholder'], ENT_QUOTES, 'UTF-8');
                                echo "<div class='mb-3'>
                                        <label class='form-label fw-semibold' for='{$name}'>{$label}</label>
                                        <input type='{$type}' name='{$name}' id='{$name}' class='form-control rounded-4 border' placeholder='{$placeholder}'>
                                      </div>";
                            }
                            ?>
                            <!-- Klienti Select -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="klienti22">Klienti</label>
                                <select name="klienti" id="klienti22" class="form-select rounded-4 border">
                                    <option value="" disabled selected>Zgjidhni një klient</option>
                                    <?php
                                    foreach ($clients as $client) {
                                        // Ensure the client data is properly escaped
                                        $emri = htmlspecialchars($client['emri'], ENT_QUOTES, 'UTF-8');
                                        $emailadd = htmlspecialchars($client['emailadd'], ENT_QUOTES, 'UTF-8');
                                        $emriart = htmlspecialchars($client['emriart'], ENT_QUOTES, 'UTF-8');
                                        // Use a delimiter that won't appear in the data or consider using JSON encoding
                                        echo "<option value='{$emri}|{$emailadd}|{$emriart}'>{$emri}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <?php
                            $additionalFields = [
                                ["label" => "Adresa e email-it", "name" => "email", "type" => "email", "placeholder" => "Sheno adresen e email-it"],
                                ["label" => "Emri artistik", "name" => "emriartistik", "type" => "text", "placeholder" => "Sheno emrin artistik"],
                                ["label" => "Vepra", "name" => "vepra", "type" => "text", "placeholder" => "Sheno veprën"],
                                ["label" => "Data", "name" => "data", "type" => "date"],
                                ["label" => "Ngarko kontratën", "name" => "pdf_file", "type" => "file", "attributes" => "accept='.docx,.pdf' onchange='validateFile(this)'"],
                                ["label" => "Përqindja (Baresha)", "name" => "perqindja", "type" => "number", "placeholder" => "Sheno përqindjen", "attributes" => "onchange='updatePerqindjaOther()'"],
                                ["label" => "Përqindja (Klienti)", "name" => "perqindja_other", "type" => "number", "readonly" => true],
                                ["label" => "Shënime", "name" => "shenime", "type" => "textarea", "rows" => 5, "placeholder" => "Shëno..."]
                            ];
                            foreach ($additionalFields as $field) {
                                // Escape output
                                $label = htmlspecialchars($field['label'], ENT_QUOTES, 'UTF-8');
                                $name = htmlspecialchars($field['name'], ENT_QUOTES, 'UTF-8');
                                $type = htmlspecialchars($field['type'], ENT_QUOTES, 'UTF-8');
                                $placeholder = isset($field['placeholder']) ? htmlspecialchars($field['placeholder'], ENT_QUOTES, 'UTF-8') : '';
                                $attributes = isset($field['attributes']) ? ' ' . $field['attributes'] : '';
                                $readonly = isset($field['readonly']) && $field['readonly'] ? ' readonly' : '';
                                // Removed the required attribute

                                if ($type == 'textarea') {
                                    $rows = isset($field['rows']) ? (int)$field['rows'] : 3;
                                    echo "<div class='mb-3'>
                                            <label class='form-label fw-semibold' for='{$name}'>{$label}</label>
                                            <textarea name='{$name}' id='{$name}' class='form-control rounded-4 border' rows='{$rows}' placeholder='{$placeholder}'></textarea>
                                          </div>";
                                } else {
                                    echo "<div class='mb-3'>
                                            <label class='form-label fw-semibold' for='{$name}'>{$label}</label>
                                            <input type='{$type}' name='{$name}' id='{$name}' class='form-control rounded-4 border' placeholder='{$placeholder}'{$attributes}{$readonly}>
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
<?php require_once 'partials/footer.php'; ?>
<script>
    $(document).ready(function() {
        // Initialize Toastr options
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000",
        };

        // Event listeners for form inputs to update preview
        $('#contractForm').on('input change', 'input, select, textarea', updatePreview);

        // Show email and artist name based on selected client
        $('#klienti22').on('change', function() {
            const [emri, email, emriartistik] = $(this).val().split("|") || [];
            $(this).val()
                ? (email && emriartistik
                    ? (
                        $('#email').val(email || "Klienti që keni zgjedhur nuk ka adresë te emailit"),
                        $('#emriartistik').val(emriartistik || ""),
                        updatePreview()
                      )
                    : (
                        toastr.error('Të dhënat e klientit janë të pavlefshme.'),
                        $('#email').val(""),
                        $('#emriartistik').val("")
                      )
                  )
                : toastr.warning('Ju lutem zgjidhni një klient.');
        });

        // Update the 'perqindja_other' field based on 'perqindja'
        $('#perqindja').on('change', function() {
            const perqindja = parseFloat($(this).val());
            const $perqindjaOther = $('#perqindja_other');
            !$('#perqindja').length || !$perqindjaOther.length
                ? toastr.error('Fusha përqindja nuk është gjetur.')
                : isNaN(perqindja)
                ? (
                    $perqindjaOther.val(""),
                    toastr.warning('Ju lutem shkruani një vlerë të vlefshme për përqindjen.')
                  )
                : (perqindja < 0 || perqindja > 100)
                ? (
                    $perqindjaOther.val(""),
                    toastr.warning('Përqindja duhet të jetë midis 0 dhe 100.')
                  )
                : (
                    $perqindjaOther.val((100 - perqindja).toFixed(2)),
                    updatePreview()
                  );
        });

        // Update the contract preview by fetching from the server
        function updatePreview() {
            const formData = new FormData($('#contractForm')[0]);
            $.ajax({
                url: 'preview-contract.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    $('#contractContent').html(data);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error fetching contract preview:', errorThrown);
                    toastr.error('Dështoi për të ngarkuar preview-n e kontratës.');
                }
            });
        }

        // Initialize Selectr for the klienti select element (Ensure Selectr library is loaded)
        if (typeof Selectr !== 'undefined') {
            new Selectr('#klienti22', { searchable: true });
        } else {
            console.warn('Selectr library is not loaded.');
        }

        // Initialize flatpickr for the date input (Ensure flatpickr library is loaded)
        if (typeof flatpickr !== 'undefined') {
            flatpickr("input[name='data']", {
                dateFormat: "Y-m-d",
                maxDate: "today",
                onChange: updatePreview
            });
        } else {
            console.warn('flatpickr library is not loaded.');
        }

        // Validate the uploaded file
        window.validateFile = function(input) {
            const file = input.files[0];
            const maxSize = 10 * 1024 * 1024; // 10MB
            const allowedTypes = [
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/pdf'
            ];

            file
                ? (
                    allowedTypes.includes(file.type)
                        ? (file.size <= maxSize
                            ? updatePreview()
                            : (
                                toastr.error('Madhësia e skedarit tejkalon limitin (10MB).'),
                                $(input).val('')
                              )
                          )
                        : (
                            toastr.error('Ju lutem zgjidhni një lloj skedari të vlefshëm (docx ose pdf).'),
                            $(input).val('')
                          )
                  )
                : toastr.warning('Ju lutem zgjidhni një skedar.');
        };

        // Function to view documents in the modal
        $(document).on('click', '.view-document', function(e) {
            e.preventDefault();
            const file = decodeURIComponent($(this).data('file') || '');
            const name = decodeURIComponent($(this).data('name') || '');

            file && name
                ? (
                    $('#documentModalLabel').text(name),
                    $('#documentViewer').attr('src', file),
                    $('#documentModal').modal('show')
                  )
                : toastr.error('Dokumenti është i pavlefshëm.');
        });

        // Function to show the replace modal (Ensure you have a corresponding modal)
        window.showReplaceModal = function(id) {
            id
                ? toastr.info('Replace modal functionality to be implemented.')
                : toastr.error('Identifikimi i kontratës është i pavlefshëm.');
        };

        // Function to confirm deletion (Implement deletion logic)
        window.confirmDelete = function(id) {
            id
                ? (
                    confirm('A jeni të sigurtë që dëshironi të fshini këtë kontratë?')
                        ? $.ajax({
                            url: `delete-contract.php?id=${encodeURIComponent(id)}`,
                            type: 'DELETE',
                            contentType: 'application/json',
                            success: function(data) {
                                data.success
                                    ? (
                                        toastr.success('Kontrata u fshi me sukses.'),
                                        location.reload()
                                      )
                                    : toastr.error(data.message || 'Dështoi fshirja e kontratës.')
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.error('Error deleting contract:', errorThrown);
                                toastr.error('Dështoi fshirja e kontratës.');
                            }
                        })
                        : null
                  )
                : toastr.error('Identifikimi i kontratës është i pavlefshëm.');
        };
    });
</script>
<?php require_once 'partials/footer.php'; ?>
