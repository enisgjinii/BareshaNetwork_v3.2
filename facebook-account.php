<?php
include 'partials/header.php';
// Retrieve the value of 'kid' from the query string and sanitize it
$kid = isset($_GET['kid']) ? intval($_GET['kid']) : 0;
// Fetch the data associated with the provided 'kid' from the database
$query = "SELECT * FROM facebook WHERE id = $kid";
$result = mysqli_query($conn, $query);
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    // Sanitize output to prevent XSS
    $emri_mbiemri = htmlspecialchars($row['emri_mbiemri'], ENT_QUOTES, 'UTF-8');
    $emri_faqes = htmlspecialchars($row['emri_faqes'], ENT_QUOTES, 'UTF-8');
    // Retrieve and sanitize other required columns similarly
} else {
    echo "<div class='container mt-5'><div class='alert alert-warning'>Data not found.</div></div>";
    exit;
}
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="p-5 rounded-5 mb-4 border border-2">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="vegla_facebook.php" class="input-custom-css px-3 py-2 text-decoration-none" data-bs-toggle="tooltip" data-bs-placement="top" title="Kthehu në listë">
                        <i class="fi fi-rr-arrow-left"></i> Kthehu
                    </a>
                </div>
                <div class="table-responsive">
                    <form id="editFacebookForm" action="api/edit_methods/edit_facebook_account.php" method="POST" novalidate>
                        <input type="hidden" name="kid" value="<?php echo $kid; ?>">
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">ID</label>
                            <div class="col-sm-9">
                                <p class="form-control-plaintext"><?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="emri_mbiemri" class="form-label">Emri Mbiemri</label>
                                <input type="text" class="form-control shadow-sm rounded-5" id="emri_mbiemri" name="emri_mbiemri" value="<?php echo $emri_mbiemri; ?>" required data-bs-toggle="tooltip" data-bs-placement="top" title="Shkruani emrin dhe mbiemrin">
                                <div class="invalid-feedback">
                                    Ju lutemi, futni emrin dhe mbiemrin.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="emri_faqes" class="form-label">Emri Faqes</label>
                                <input type="text" class="form-control shadow-sm rounded-5" id="emri_faqes" name="emri_faqes" value="<?php echo $emri_faqes; ?>" required data-bs-toggle="tooltip" data-bs-placement="top" title="Shkruani emrin e faqes">
                                <div class="invalid-feedback">
                                    Ju lutemi, futni emrin e faqes.
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="data_krijimit" class="form-label">Data Krijimit</label>
                                <input type="date" class="form-control shadow-sm rounded-5" id="data_krijimit" name="data_krijimit" value="<?php echo htmlspecialchars($row['dataKrijimit'], ENT_QUOTES, 'UTF-8'); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni datën e krijimit">
                            </div>
                            <div class="col-md-6">
                                <label for="data_skadimit" class="form-label">Data Skadimit</label>
                                <input type="date" class="form-control shadow-sm rounded-5" id="data_skadimit" name="data_skadimit" value="<?php echo htmlspecialchars($row['dataSkadimit'], ENT_QUOTES, 'UTF-8'); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni datën e skadimit">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="linku_faqes" class="form-label">Linku Faqes</label>
                                <input type="url" class="form-control shadow-sm rounded-5" id="linku_faqes" name="linku_faqes" value="<?php echo htmlspecialchars($row['linkuFaqes'], ENT_QUOTES, 'UTF-8'); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Futni URL-në e faqes">
                                <div class="invalid-feedback">
                                    Ju lutemi, futni një URL të vlefshëm.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="numri_personal" class="form-label">Numri Personal</label>
                                <input type="text" class="form-control shadow-sm rounded-5" id="numri_personal" name="numri_personal" value="<?php echo htmlspecialchars($row['numriPersonal'], ENT_QUOTES, 'UTF-8'); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Futni numrin tuaj personal">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="ads_account" class="form-label">Ads Account</label>
                                <input type="text" class="form-control shadow-sm rounded-5" id="ads_account" name="ads_account" value="<?php echo htmlspecialchars($row['adsAccount'], ENT_QUOTES, 'UTF-8'); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Futni llogarinë e reklamave">
                            </div>
                            <div class="col-md-6">
                                <label for="kategoria" class="form-label">Kategoria</label>
                                <input type="text" class="form-control shadow-sm rounded-5" id="kategoria" name="kategoria" value="<?php echo htmlspecialchars($row['kategoria'], ENT_QUOTES, 'UTF-8'); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Futni kategorinë">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="numri_telefonit" class="form-label">Numri Telefonit</label>
                                <input type="tel" class="form-control shadow-sm rounded-5" id="numri_telefonit" name="numri_telefonit" value="<?php echo htmlspecialchars($row['numriTelefonit'], ENT_QUOTES, 'UTF-8'); ?>" pattern="[0-9]{10}" data-bs-toggle="tooltip" data-bs-placement="top" title="Futni numrin e telefonit (10 shifra)">
                                <div class="invalid-feedback">
                                    Ju lutemi, futni një numër telefoni të vlefshëm me 10 shifra.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="perqindja" class="form-label">Perqindja (%)</label>
                                <input type="number" step="0.01" class="form-control shadow-sm rounded-5" id="perqindja" name="perqindja" value="<?php echo htmlspecialchars($row['perqindja'], ENT_QUOTES, 'UTF-8'); ?>" min="0" max="100" data-bs-toggle="tooltip" data-bs-placement="top" title="Futni përqindjen (0-100)">
                                <div class="invalid-feedback">
                                    Ju lutemi, futni një përqindje të vlefshme midis 0 dhe 100.
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="numri_xhirollogarise" class="form-label">Numri Xhirollogarise</label>
                                <input type="text" class="form-control shadow-sm rounded-5" id="numri_xhirollogarise" name="numri_xhirollogarise" value="<?php echo htmlspecialchars($row['numriXhirollogarise'], ENT_QUOTES, 'UTF-8'); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Futni numrin e xhirollogarise">
                            </div>
                            <div class="col-md-6">
                                <label for="adresa" class="form-label">Adresa</label>
                                <input type="text" class="form-control shadow-sm rounded-5" id="adresa" name="adresa" value="<?php echo htmlspecialchars($row['adresa'], ENT_QUOTES, 'UTF-8'); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Futni adresën">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="info_shtese" class="form-label">Informacion Shtese</label>
                                <input type="text" class="form-control shadow-sm rounded-5" id="info_shtese" name="info_shtese" value="<?php echo htmlspecialchars($row['infoShtese'], ENT_QUOTES, 'UTF-8'); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Futni informacion shtesë">
                            </div>
                            <div class="col-md-6">
                                <label for="monetizuar" class="form-label">Monetizuar</label>
                                <select class="form-select shadow-sm rounded-5" id="monetizuar" name="monetizuar" data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni nëse është monetizuar">
                                    <option value="po" <?php echo ($row['monetizuar'] === 'po') ? 'selected' : ''; ?>>Po</option>
                                    <option value="jo" <?php echo ($row['monetizuar'] === 'jo') ? 'selected' : ''; ?>>Jo</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary shadow-sm rounded-5" data-bs-toggle="modal" data-bs-target="#confirmSubmitModal">
                                Përditso të dhënat
                            </button>
                        </div>
                        <!-- Confirmation Modal -->
                        <div class="modal fade" id="confirmSubmitModal" tabindex="-1" aria-labelledby="confirmSubmitModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="confirmSubmitModalLabel">Konfirmim</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        A jeni i sigurt që dëshironi të përditësoni të dhënat?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulo</button>
                                        <button type="submit" class="btn btn-primary">Po, përditëso</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End of Modal -->
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Optional: Include Bootstrap JS and dependencies if not already included in header.php -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-..." crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-..." crossorigin="anonymous"></script>
<script>
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (function() {
        'use strict'
        // Fetch the form we want to apply custom Bootstrap validation styles to
        var form = document.getElementById('editFacebookForm')
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
                // Show validation errors
            } else {
                // Let the modal handle the submission
                event.preventDefault()
                var submitButton = new bootstrap.Modal(document.getElementById('confirmSubmitModal'))
                submitButton.show()
            }
            form.classList.add('was-validated')
        }, false)
    })()
</script>