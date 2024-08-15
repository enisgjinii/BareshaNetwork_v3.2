<?php
include 'partials/header.php';
include 'page_access_controller.php';

// Fetch clients using a prepared statement
$clients = $conn->query("SELECT emri, emailadd, emriart, youtube, nrllog, (100 - perqindja) AS perqindja, np, nrtel FROM klientet ORDER BY emri ASC")
    ?->fetch_all(MYSQLI_ASSOC) ?? [];

// Close the database connection
$conn->close();
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <!-- Breadcrumb Navigation -->
                    <nav class="bg-white px-2 rounded-5" style="width:fit-content;" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a class="text-reset" style="text-decoration: none;">Kontratat</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><a href="<?= __FILE__ ?>" class="text-reset" style="text-decoration: none;">Kontrata e re (Gjenerale)</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="card rounded-5">
                <div class="card-body">
                    <h4 class="card-title mb-4">Krijimi i Kontratës së Re</h4>
                    <!-- Contract Creation Form -->
                    <form method="post" action="dorzoKontraten.php" enctype="multipart/form-data" onsubmit="return validateForm()">
                        <div class="row g-3">
                            <!-- Client Selection Dropdown -->
                            <div class="col-md-6">
                                <label for="artisti" class="form-label">Klienti</label>
                                <select name="artisti" id="artisti" class="form-select rounded-5" onchange="showEmail(this); updatePerqindja()" required>
                                    <option value="" selected disabled>Zgjidhni një klient</option>
                                    <?php foreach ($clients as $client): ?>
                                        <option value="<?= htmlspecialchars(implode('|', $client)) ?>">
                                            <?= htmlspecialchars($client['emri']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
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
                                'tvsh' => 'Përqindja ( Klientit )',
                                'pronari_xhiroBanka' => 'Pronari i xhirollogarisë bankare',
                                'kodi_swift' => 'Kodi SWIFT',
                                'iban' => 'IBAN',
                                'emri_bankes' => 'Emri i bankës',
                                'adresa_bankes' => 'Adresa e bankës'
                            ];

                            foreach ($fields as $name => $label): ?>
                                <div class="col-md-6">
                                    <label for="<?= $name ?>" class="form-label"><?= $label ?></label>
                                    <input type="text" name="<?= $name ?>" id="<?= $name ?>" class="form-control rounded-5"
                                        placeholder="Shëno <?= $label ?>" <?= in_array($name, ['emri', 'numri_tel', 'numri_personal', 'email', 'youtube_id', 'pronari_xhiroBanka', 'numri_xhiroBanka', 'tvsh', 'emriartistik']) ? 'readonly' : '' ?>>
                                </div>
                            <?php endforeach; ?>

                            <!-- Duration of the Contract -->
                            <div class="col-md-6">
                                <label for="kohezgjatja" class="form-label">Kohëzgjatja në muaj</label>
                                <input type="number" name="kohezgjatja" id="kohezgjatja" class="form-control rounded-5" placeholder="Shëno kohëzgjatjen e kontratës" min="1">
                                <small class="form-text text-muted">Vendos vetëm një numër pozitiv</small>
                            </div>
                        </div>
                        <!-- Submit Button -->
                        <div class="mt-4">
                            <button type="submit" class="input-custom-css px-3 py-2">
                                <i class="fi fi-rr-memo-circle-check me-2"></i>Krijo kontratën
                            </button>
                        </div>
                    </form>
                    <script>
                        function updatePerqindja() {
                            const perqindjaInput = document.getElementById('tvsh');
                            const perqindjaValue = parseFloat(perqindjaInput.value);

                            if (perqindjaInput) {
                                perqindjaInput.value = isNaN(perqindjaValue) || perqindjaValue < 0 || perqindjaValue >= 100 ? '' : (100 - perqindjaValue).toFixed(2);
                            }
                        }

                        function showEmail(select) {
                            const values = select.value.split("|");
                            const fields = ['emri', 'email', 'emriartistik', 'youtube_id', 'numri_xhiroBanka', 'tvsh', 'numri_personal', 'numri_tel'];

                            fields.forEach((field, index) => {
                                const inputElement = document.getElementById(field);
                                if (inputElement) {
                                    const value = values[index] ? sanitize(values[index]) : '';
                                    inputElement.value = value;
                                    if (value === '') {
                                        inputElement.removeAttribute('readonly');
                                    } else {
                                        inputElement.setAttribute('readonly', 'readonly');
                                    }
                                }
                            });

                            // Handle pronari_xhiroBanka field
                            const pronariXhiroBankaField = document.getElementById('pronari_xhiroBanka');
                            const pronariValue = sanitize(values[2] || '');
                            pronariXhiroBankaField.value = pronariValue;
                            if (pronariValue === '') {
                                pronariXhiroBankaField.removeAttribute('readonly');
                            } else {
                                pronariXhiroBankaField.setAttribute('readonly', 'readonly');
                            }

                            // Handle tvsh field
                            const perqindja = parseFloat(values[5]);
                            const tvshField = document.getElementById('tvsh');
                            const tvshValue = isNaN(perqindja) ? '' : (100 - perqindja).toFixed(2);
                            tvshField.value = tvshValue;
                            if (tvshValue === '') {
                                tvshField.removeAttribute('readonly');
                            } else {
                                tvshField.setAttribute('readonly', 'readonly');
                            }

                            // Enable SWIFT, IBAN, Bank Name, and Bank Address fields if they are empty
                            ['kodi_swift', 'iban', 'emri_bankes', 'adresa_bankes'].forEach((field) => {
                                const fieldElement = document.getElementById(field);
                                if (fieldElement.value === '') {
                                    fieldElement.removeAttribute('readonly');
                                } else {
                                    fieldElement.setAttribute('readonly', 'readonly');
                                }
                            });
                        }

                        function sanitize(value) {
                            return value.replace(/</g, '&lt;').replace(/>/g, '&gt;');
                        }

                        function validateForm() {
                            const fields = ['emri', 'numri_tel', 'numri_personal', 'email', 'youtube_id', 'emriartistik', 'numri_xhiroBanka', 'tvsh', 'pronari_xhiroBanka', 'kodi_swift', 'iban', 'emri_bankes', 'adresa_bankes', 'kohezgjatja'];
                            return fields.every(field => document.getElementById(field).value.trim() !== '' || !document.getElementById(field).hasAttribute('required')) || (alert('Ju lutem plotësoni të gjitha fushat e kërkuara.'), false);
                        }

                        document.addEventListener('DOMContentLoaded', () => new Selectr('#artisti', {
                            searchable: true,
                            width: 300
                        }));
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('partials/footer.php'); ?>