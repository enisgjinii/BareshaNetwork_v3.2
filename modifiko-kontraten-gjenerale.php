<?php
ob_start();
include 'partials/header.php';
include 'conn-d.php';

// Start session to access user information if needed
session_start();
$user_info = $_SESSION['user_info'] ?? [];

// Get the ID from the URL and sanitize it to prevent SQL injection
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch the record from the database using prepared statements for security
$stmt = $conn->prepare("SELECT * FROM kontrata_gjenerale WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

ob_flush();
ob_end_clean();

// Ensure 'data_e_krijimit' is in 'Y-m-d' format
$data_e_krijimit = date('Y-m-d', strtotime($row['data_e_krijimit']));
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="p-5 mb-4 card shadow-sm rounded-5">
                <h2 class="mb-4 text-center">Përditëso Kontratën Gjenerale</h2>
                <form id="updateForm" novalidate>
                    <div class="row g-3">
                        <!-- Emri & Mbiemri -->
                        <div class="col-md-6">
                            <label for="emri" class="form-label">
                                <i class="bi bi-person-fill me-1"></i> Emri
                            </label>
                            <input type="text" class="form-control rounded-5" name="emri" id="emri" value="<?= htmlspecialchars($row['emri']) ?>" required
                                data-bs-toggle="tooltip" title="Shkruani emrin tuaj të plotë.">
                            <small class="form-text text-muted">Pa numra ose simbole.</small>
                            <div class="invalid-feedback">Ju lutemi, shkruani emrin tuaj.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="mbiemri" class="form-label">
                                <i class="bi bi-person-fill-add me-1"></i> Mbiemri
                            </label>
                            <input type="text" class="form-control rounded-5" name="mbiemri" id="mbiemri" value="<?= htmlspecialchars($row['mbiemri']) ?>" required
                                data-bs-toggle="tooltip" title="Shkruani mbiemrin tuaj të plotë.">
                            <small class="form-text text-muted">Sigurohuni që mbiemri juaj të jetë i saktë.</small>
                            <div class="invalid-feedback">Ju lutemi, shkruani mbiemrin tuaj.</div>
                        </div>
                    </div>
                    <div class="row g-3 mt-3">
                        <!-- TVSH & Numri Personal -->
                        <div class="col-md-6">
                            <label for="tvsh" class="form-label">
                                <i class="bi bi-percent me-1"></i> P&euml;rqindja
                            </label>
                            <input type="number" step="0.01" class="form-control rounded-5" name="tvsh" id="tvsh" value="<?= htmlspecialchars($row['tvsh']) ?>" required
                                data-bs-toggle="tooltip" title="Përcaktoni p&euml;rqindjen e TVSH-së.">
                            <small class="form-text text-muted">Format: 20.00</small>
                            <div class="invalid-feedback">Shkruani një p&euml;rqindje të vlefshme.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="numri_personal" class="form-label">
                                <i class="bi bi-card-text me-1"></i> Numri personal
                            </label>
                            <input type="text" pattern="\d{10}" class="form-control rounded-5" name="numri_personal" id="numri_personal" value="<?= htmlspecialchars($row['numri_personal']) ?>" required
                                data-bs-toggle="tooltip" title="Shkruani numrin tuaj personal të identifikimit.">
                            <small class="form-text text-muted">10 shifra.</small>
                            <div class="invalid-feedback">Shkruani një numër personal të vlefshëm.</div>
                        </div>
                    </div>
                    <div class="row g-3 mt-3">
                        <!-- Bank Information -->
                        <div class="col-md-6">
                            <label for="pronari_xhirollogarise" class="form-label">
                                <i class="bi bi-bank2 me-1"></i> Pronari i xhirollogaris&euml; bankare
                            </label>
                            <input type="text" class="form-control rounded-5" name="pronari_xhirollogarise" id="pronari_xhirollogarise" value="<?= htmlspecialchars($row['pronari_xhirollogarise']) ?>" required
                                data-bs-toggle="tooltip" title="Shkruani emrin e pronarit të llogarisë bankare.">
                            <small class="form-text text-muted">Emri i plotë i pronarit të llogarisë.</small>
                            <div class="invalid-feedback">Shkruani emrin e pronarit të llogarisë.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="numri_xhirollogarise" class="form-label">
                                <i class="bi bi-credit-card me-1"></i> Numri i xhirollogaris&euml; bankare
                            </label>
                            <input type="text" class="form-control rounded-5" name="numri_xhirollogarise" id="numri_xhirollogarise" value="<?= htmlspecialchars($row['numri_xhirollogarise']) ?>" required
                                data-bs-toggle="tooltip" title="Shkruani numrin e llogarisë bankare.">
                            <small class="form-text text-muted">Numri i llogarisë duhet të jetë korrekt.</small>
                            <div class="invalid-feedback">Shkruani numrin e llogarisë bankare.</div>
                        </div>
                    </div>
                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <label for="kodi_swift" class="form-label">
                                <i class="bi bi-globe2 me-1"></i> Kodi SWIFT
                            </label>
                            <input type="text" class="form-control rounded-5" name="kodi_swift" id="kodi_swift" value="<?= htmlspecialchars($row['kodi_swift']) ?>" required
                                data-bs-toggle="tooltip" title="Shkruani kodin SWIFT të bankës.">
                            <small class="form-text text-muted">Kodi SWIFT për transaksione ndërkombëtare.</small>
                            <div class="invalid-feedback">Shkruani një kod SWIFT të vlefshëm.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="iban" class="form-label">
                                <i class="bi bi-credit-card-2-back me-1"></i> IBAN
                            </label>
                            <input type="text" class="form-control rounded-5" name="iban" id="iban" value="<?= htmlspecialchars($row['iban']) ?>" required
                                data-bs-toggle="tooltip" title="Shkruani IBAN-in tuaj bankar.">
                            <small class="form-text text-muted">Formati standard ndërkombëtar i numrit bankar (IBAN).</small>
                            <div class="invalid-feedback">Shkruani një IBAN të vlefshëm.</div>
                        </div>
                    </div>
                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <label for="emri_bankes" class="form-label">
                                <i class="bi bi-building me-1"></i> Emri i bank&euml;s
                            </label>
                            <input type="text" class="form-control rounded-5" name="emri_bankes" id="emri_bankes" value="<?= htmlspecialchars($row['emri_bankes']) ?>" required
                                data-bs-toggle="tooltip" title="Shkruani emrin e bankës.">
                            <small class="form-text text-muted">Emri i plotë i bankës ku është hapur llogaria.</small>
                            <div class="invalid-feedback">Shkruani emrin e bankës.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="adresa_bankes" class="form-label">
                                <i class="bi bi-geo-alt-fill me-1"></i> Adresa e bank&euml;s
                            </label>
                            <input type="text" class="form-control rounded-5" name="adresa_bankes" id="adresa_bankes" value="<?= htmlspecialchars($row['adresa_bankes']) ?>" required
                                data-bs-toggle="tooltip" title="Shkruani adresën e bankës.">
                            <small class="form-text text-muted">Adresa e plotë e bankës.</small>
                            <div class="invalid-feedback">Shkruani adresën e bankës.</div>
                        </div>
                    </div>
                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <label for="kohezgjatja" class="form-label">
                                <i class="bi bi-hourglass-split me-1"></i> Koh&euml;zgjatja në muaj
                            </label>
                            <input type="number" min="1" class="form-control rounded-5" name="kohezgjatja" id="kohezgjatja" value="<?= htmlspecialchars($row['kohezgjatja']) ?>" required
                                data-bs-toggle="tooltip" title="Shkruani kohëzgjatjen në muaj për kontratë.">
                            <small class="form-text text-muted">Numri i muajve të vlefshëm për këtë kontratë.</small>
                            <div class="invalid-feedback">Shkruani një kohëzgjatje të vlefshme.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="shenim" class="form-label">
                                <i class="bi bi-card-text me-1"></i> Shenime
                            </label>
                            <input type="text" class="form-control rounded-5" name="shenim" id="shenim" value="<?= htmlspecialchars($row['shenim']) ?>" required
                                data-bs-toggle="tooltip" title="Shkruani shenimet tuaja.">
                            <small class="form-text text-muted">Çdo shënim të rëndësishëm për këtë kontratë.</small>
                            <div class="invalid-feedback">Shkruani shënime të vlefshme.</div>
                        </div>
                    </div>
                    <div class="row g-3 mt-3">
                        <!-- Data e Krijimit (Creation Date) -->
                        <div class="col-md-6">
                            <label for="data_e_krijimit" class="form-label">
                                <i class="bi bi-calendar-fill me-1"></i> Data e Krijimit
                            </label>
                            <input type="text" class="form-control rounded-5" name="data_e_krijimit" id="data_e_krijimit" value="<?= htmlspecialchars($data_e_krijimit) ?>" required
                                data-bs-toggle="tooltip" title="Zgjidhni datën e krijimit të kontratës.">
                            <small class="form-text text-muted">Zgjidhni datën e krijimit të kontratës.</small>
                            <div class="invalid-feedback">Ju lutemi, zgjidhni një datë të vlefshme.</div>
                        </div>
                    </div>
                    <hr class="my-4">
                    <?php
                    $expiration_date = date('d F Y', strtotime($data_e_krijimit . ' + ' . $row['kohezgjatja'] . ' months'));
                    ?>
                    <div class="mt-4">
                        <p class="fw-bold text-primary">Kjo kontratë është valide deri me:</p>
                        <p class="bg-light p-3 rounded border border-primary d-flex align-items-center" id="expiration_date_display">
                            <i class="bi bi-calendar-check-fill me-2"></i> <?= htmlspecialchars($expiration_date) ?>
                        </p>
                    </div>
                    <!-- Submit Button -->
                    <div class="mt-4 text-center">
                        <button type="submit" class="btn btn-primary rounded-5 px-4 py-2" data-bs-toggle="tooltip" title="Klikoni për të përditësuar kontratën.">
                            P&euml;rditso
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Include Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<!-- Include Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>

<!-- Include Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize Bootstrap tooltips
        [...document.querySelectorAll('[data-bs-toggle="tooltip"]')].forEach(el => new bootstrap.Tooltip(el));

        // Initialize Flatpickr on 'data_e_krijimit'
        const flatpickrInstance = flatpickr("#data_e_krijimit", {
            dateFormat: "Y-m-d",
            defaultDate: "<?= htmlspecialchars($data_e_krijimit) ?>",
            onChange: updateExpirationDate
        });

        // Real-time expiration date update
        const kohezgjatjaInput = document.getElementById('kohezgjatja');
        const expirationDisplay = document.getElementById('expiration_date_display');
        const dataE_KrijimitInput = document.getElementById('data_e_krijimit');

        function updateExpirationDate(selectedDates, dateStr, instance) {
            const creationDate = selectedDates[0];
            let months = parseInt(kohezgjatjaInput.value);

            if (isNaN(months) || months < 1) {
                // Invalid input, do not update
                return;
            }

            // Calculate new expiration date
            let newExpiration = new Date(creationDate);
            newExpiration.setMonth(newExpiration.getMonth() + months);

            // Handle month overflow (e.g., adding 1 month to January 31 should give February 28/29)
            if (newExpiration.getDate() !== creationDate.getDate()) {
                newExpiration.setDate(0); // Last day of previous month
            }

            // Format date as 'd F Y' (e.g., 15 October 2024)
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            const formattedDate = newExpiration.toLocaleDateString('en-GB', options);

            // Update the expiration date display
            expirationDisplay.innerHTML = `<i class="bi bi-calendar-check-fill me-2"></i> ${formattedDate}`;
        }

        // Listen for changes in 'kohezgjatja'
        kohezgjatjaInput.addEventListener('input', () => {
            const creationDate = flatpickrInstance.selectedDates[0];
            let months = parseInt(kohezgjatjaInput.value);

            if (isNaN(months) || months < 1) {
                // Invalid input, do not update
                return;
            }

            // Calculate new expiration date
            let newExpiration = new Date(creationDate);
            newExpiration.setMonth(newExpiration.getMonth() + months);

            // Handle month overflow
            if (newExpiration.getDate() !== creationDate.getDate()) {
                newExpiration.setDate(0); // Last day of previous month
            }

            // Format date as 'd F Y' (e.g., 15 October 2024)
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            const formattedDate = newExpiration.toLocaleDateString('en-GB', options);

            // Update the expiration date display
            expirationDisplay.innerHTML = `<i class="bi bi-calendar-check-fill me-2"></i> ${formattedDate}`;
        });

        // Form validation and submission
        const form = document.getElementById('updateForm');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!form.checkValidity()) {
                e.stopPropagation();
                form.classList.add('was-validated');
                Swal.fire({
                    title: 'Gabim!',
                    text: 'Ju lutemi, plotësoni të gjitha fushat e kërkuara siç duhet.',
                    icon: 'warning',
                    confirmButtonText: 'Rivizo'
                });
                return;
            }

            const formData = new FormData(form);
            formData.append('id', '<?= intval($id) ?>');

            fetch('api/edit_methods/edit_contract.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Sukses!', 'Kontrata është përditësuar me sukses!', 'success')
                            .then(() => window.location.reload());
                    } else {
                        Swal.fire('Gabim!', data.message || 'Diçka shkoi keq!', 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Gabim!', 'Diçka shkoi keq gjatë përpunimit të kërkesës.', 'error');
                });
        });
    });
</script>
<style>
    /* Removed .blurred-input class since it's no longer needed */
    .form-label {
        font-weight: 600;
    }

    .btn-outline-secondary:hover,
    .btn-primary:hover {
        opacity: 0.85;
    }

    @media (max-width: 576px) {
        .input-group .btn {
            width: 100%;
            margin-top: 10px;
        }
    }

    .form-control {
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    .swal2-popup {
        font-size: 1.1rem !important;
    }
</style>
<?php include('partials/footer.php') ?>