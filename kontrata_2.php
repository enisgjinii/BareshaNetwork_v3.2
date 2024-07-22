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
            <nav class="bg-white px-2 rounded-5" style="width: fit-content" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="text-reset" style="text-decoration: none;">Kontratat</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">Kontrata e re (Këngë)</a>
                    </li>
                </ol>
            </nav>
            <div class="card p-5 shadow-sm rounded-5">
                <div class="row">
                    <div class="col-6">
                        <form id="contractForm" enctype="multipart/form-data">
                            <?php
                            $fields = [
                                ["label" => "Emri", "name" => "emri", "type" => "text", "placeholder" => "Sheno emrin"],
                                ["label" => "Mbiemri", "name" => "mbiemri", "type" => "text", "placeholder" => "Sheno mbiemrin"],
                                ["label" => "Numri i telefonit", "name" => "numri_tel", "type" => "text", "placeholder" => "Sheno numrin e telefonit"],
                                ["label" => "Numri personal", "name" => "numri_personal", "type" => "text", "placeholder" => "Sheno numrin personal"],
                            ];
                            foreach ($fields as $field) {
                                echo "<div class='col-md-12 mb-3'>
                                        <label class='form-label' for='{$field['name']}'>{$field['label']}</label>
                                        <input type='{$field['type']}' name='{$field['name']}' id='{$field['name']}' class='form-control rounded-5 border-1' required placeholder='{$field['placeholder']}'>
                                      </div>";
                            }
                            ?>
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="klienti">Klienti</label>
                                <select name="klienti" id="klienti22" class="form-select rounded-5 border-1" onchange="showEmail(this)" required>
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
                                ["label" => "Ngarko kontratën", "name" => "pdf_file", "type" => "file", "attributes" => "accept='.docx,.pdf' onchange='validateFile(this)'"],
                                ["label" => "Përqindja (Baresha)", "name" => "perqindja", "type" => "number", "placeholder" => "Sheno përqindjen", "attributes" => "onchange='updatePerqindjaOther()'"],
                                ["label" => "Përqindja (Klienti)", "name" => "perqindja_other", "type" => "number", "readonly" => true],
                                ["label" => "Shënime", "name" => "shenime", "type" => "textarea", "rows" => 5, "placeholder" => "Shëno..."]
                            ];
                            foreach ($additionalFields as $field) {
                                if ($field['type'] == 'textarea') {
                                    echo "<div class='col-md-12 mb-3'>
                                            <label class='form-label' for='{$field['name']}'>{$field['label']}</label>
                                            <textarea name='{$field['name']}' class='form-control rounded-5 border-1' rows='{$field['rows']}' placeholder='{$field['placeholder']}' required></textarea>
                                          </div>";
                                } else {
                                    $attributes = isset($field['attributes']) ? $field['attributes'] : '';
                                    $readonly = isset($field['readonly']) ? 'readonly' : '';
                                    echo "<div class='col-md-12 mb-3'>
                                            <label class='form-label' for='{$field['name']}'>{$field['label']}</label>
                                            <input type='{$field['type']}' name='{$field['name']}' id='{$field['name']}' class='form-control rounded-5 border-1' required placeholder='{$field['placeholder']}' {$attributes} {$readonly}>
                                          </div>";
                                }
                            }
                            ?>
                            <button type="submit" class="input-custom-css px-3 py-2 rounded-5">
                                <i class="fi fi-rr-paper-plane me-2"></i>Dërgo
                            </button>
                        </form>
                    </div>
                    <div class="col-6">
                        <div id="contractContent" class="container border"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('partials/footer.php'); ?>
<script>
    document.querySelectorAll('#contractForm input, #contractForm select, #contractForm textarea').forEach(input => {
        input.addEventListener('input', updatePreview);
    });

    function showEmail(select) {
        const [_, email, emriartistik] = select.value.split("|");
        document.getElementById("email").value = email || "Klienti që keni zgjedhur nuk ka adresë te emailit";
        document.getElementById("emriartistik").value = emriartistik || "";
        updatePreview();
    }

    function updatePerqindjaOther() {
        const perqindja = parseFloat(document.getElementById('perqindja').value);
        document.getElementById('perqindja_other').value = isNaN(perqindja) ? "" : 100 - perqindja;
        updatePreview();
    }

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
            .catch(error => console.error('Error:', error));
    }

    new Selectr('#klienti22', {
        searchable: true
    });

    $("input[name='data']").flatpickr({
        dateFormat: "Y-m-d",
        maxDate: new Date().fp_incr(0),
    });

    function validateFile(input) {
        const file = input.files[0];
        const maxSize = 10 * 1024 * 1024; // 10MB
        const allowedTypes = ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/pdf'];
        if (!file || !allowedTypes.includes(file.type) || file.size > maxSize) {
            const message = !file ? 'Ju lutem zgjidhni një skedar.' :
                !allowedTypes.includes(file.type) ? 'Ju lutem zgjidhni një lloj skedari të vlefshëm (docx ose pdf).' :
                'Madhësia e skedarit tejkalon limitin (10MB).';
            Swal.fire({
                icon: 'error',
                title: 'Ou...',
                text: message
            });
            input.value = '';
        } else {
            updatePreview();
        }
    }
</script>