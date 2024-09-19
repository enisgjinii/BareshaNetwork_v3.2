<?php
// Përfshini pjesët e nevojshme dhe krijoni lidhjen me bazën e të dhënave
include 'partials/header.php';
include 'conn-d.php'; // Sigurohuni që ky skedar krijon variablën $conn në mënyrë korrekte
// Inicializoni mesazhet
$error = '';
$success = '';
// Funksion për të marrë parametra GET në mënyrë të sigurt
function getIntParam($conn, $param, $default = 0)
{
    return isset($_GET[$param]) && is_numeric($_GET[$param]) ? (int)$_GET[$param] : $default;
}
// Merrni dhe verifikoni 'id'
$id = getIntParam($conn, 'id');
if ($id <= 0) {
    die('ID e klientit e pavlefshme.');
}
// Funksion për të marrë të dhëna me përdorimin e përgatitur të deklaratave
function fetchData($conn, $query, $types, ...$params)
{
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        die('Përgatitja dështoi: ' . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $data;
}
// Merrni të dhënat e klientit
$clientData = fetchData($conn, "SELECT * FROM klientet WHERE id = ?", "i", $id);
$client = $clientData[0] ?? null;
if (!$client) {
    die('Klienti nuk u gjet.');
}
// Verifikoni përqindjen bazë
$base_percentage = (float)$client['perqindja'];
if ($base_percentage < 0 || $base_percentage > 100) {
    die('Përqindja bazë për klientin është e pavlefshme.');
}
// Llogaritni përqindjen maksimale të lejuar për sub-kontot
$max_sub_percentage = 100 - $base_percentage;
// Merrni sub-kontot
$subaccounts = fetchData($conn, "SELECT * FROM client_subaccounts WHERE client_id = ? ORDER BY id ASC LIMIT 5", "i", $id);
// Menaxhoni dërgimin e formularit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subaccounts_input = $_POST['subaccounts'] ?? [];
    $valid = true;
    $total_percentage = 0;
    $processed_subaccounts = [];
    foreach ($subaccounts_input as $subaccount) {
        $name = trim($subaccount['name']);
        $percentage = floatval($subaccount['percentage']);
        if (empty($name)) {
            $error = "Emri i sub-kontës nuk mund të jetë bosh.";
            $valid = false;
            break;
        }
        if ($percentage < 0 || $percentage > $max_sub_percentage) {
            $error = "Përqindja për '{$name}' duhet të jetë midis 0 dhe {$max_sub_percentage}%.";
            $valid = false;
            break;
        }
        $total_percentage += $percentage;
        $processed_subaccounts[] = ['name' => $name, 'percentage' => $percentage];
    }
    if ($valid && $total_percentage > $max_sub_percentage) {
        $error = "Përqindja totale e sub-kontave nuk mund të kalojë {$max_sub_percentage}%. Aktualisht është {$total_percentage}%.";
        $valid = false;
    }
    if ($valid) {
        // Filloni transaksionin
        mysqli_begin_transaction($conn);
        try {
            // Fshini sub-kontat ekzistuese
            mysqli_query($conn, "DELETE FROM client_subaccounts WHERE client_id = {$id}");
            // Futni sub-kontat e reja
            if (!empty($processed_subaccounts)) {
                $stmt = mysqli_prepare($conn, "INSERT INTO client_subaccounts (client_id, name, percentage) VALUES (?, ?, ?)");
                foreach ($processed_subaccounts as $sub) {
                    mysqli_stmt_bind_param($stmt, "isd", $id, $sub['name'], $sub['percentage']);
                    mysqli_stmt_execute($stmt);
                }
                mysqli_stmt_close($stmt);
            }
            // Konfirmoni transaksionin
            mysqli_commit($conn);
            $success = "Sub-kontat u azhurnuan me sukses.";
            header("Location: {$_SERVER['PHP_SELF']}?id=$id&success=1");
            exit;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = "Ndodhi një gabim: " . $e->getMessage();
        }
    }
}
// Kontrolloni mesazhin e suksesit pas ridrejtimeve
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success = "Sub-kontat u azhurnuan me sukses.";
    
}
?>
<style>
    .badge-custom {
        font-size: 0.9em;
    }

    #toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1055;
    }

    .btn:hover {
        opacity: 0.9;
    }

    /* Heqja e këndeve të rrumbullakosura */
    .card {
        border-radius: 0;
    }

    .list-group-item {
        border-radius: 0;
    }

    /* Stilime të reja për butonat */
    .input-custom-css {
        /* Shtoni stilimet e nevojshme këtu */
    }

    .input-custom-css.px-3.py-2 {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
        padding-top: 0.5rem !important;
        padding-bottom: 0.5rem !important;
    }
</style>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <!-- Breadcrumb Navigation -->
            <nav class="bg-white px-2 rounded-0" style="width:fit-content;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a class="text-reset" style="text-decoration: none;">Klientët</a></li>
                    <li class="breadcrumb-item"><a class="text-reset" style="text-decoration: none;">Lista e Klientëve</a></li>
                    <li class="breadcrumb-item"><a href="klient.php" class="text-reset" style="text-decoration: none;">Edito klientin <?= htmlspecialchars($client['emri']) ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <span class="text-reset" style="text-decoration: none;">
                            Shpërndarja e Përqindjeve për <?= htmlspecialchars($client['emri']) ?>
                        </span>
                    </li>
                </ol>
            </nav>
            <!-- Container Kartash -->
            <div class="card p-4 rounded-5">
                <!-- Seksioni i Headerit -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6>Shpërndarja e Përqindjeve për <span class="text-primary"><?= htmlspecialchars($client['emri']) ?></span></h3>
                        <span class="badge bg-secondary badge-custom rounded-5">
                            ID e klientit : <?= htmlspecialchars($client['id']) ?>
                        </span>
                </div>
                <!-- Kontenieri i Toast-eve -->
                <div id="toast-container"></div>
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-subaccounts" role="tabpanel" aria-labelledby="pills-subaccounts-tab">
                        <!-- Fillimi i Formularit -->
                        <form method="POST" id="subaccount-form" novalidate>
                            <!-- Seksioni i Futjes së Bashkëpunëtorëve -->
                            <div class="mb-4">
                                <label for="split-pay-collaborator" class="form-label">
                                    Bashkëpunëtor me Pagesë të Ndarë
                                </label>
                                <input type="text" id="split-pay-collaborator" class="form-control border border-2 rounded-5 mb-2" placeholder="Shkruani emrin e bashkëpunëtorit">
                                <button type="button" id="add-collaborator" class="input-custom-css px-3 py-2">
                                    <i class="bi bi-plus-circle"></i> Shto
                                </button>
                                <div class="form-text text-danger" id="input-error"></div>
                            </div>
                            <!-- Lista e Sub-Kontave -->
                            <div class="mb-4">
                                <ul class="list-group" id="split-pay-list">
                                    <!-- Rreshti i Header-it -->
                                    <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                                        <span class="fw-bold">Sub-Kontë</span>
                                        <span class="fw-bold">Përqindja (%)</span>
                                        <span class="fw-bold">Veprimet</span>
                                    </li>
                                    <!-- Rreshti i Përqindjes Bazë -->
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="fw-semibold">Përqindja Bazë (<?= htmlspecialchars($base_percentage) ?>%)</span>
                                        <span class="fw-semibold"><?= htmlspecialchars($base_percentage) ?>%</span>
                                        <span class="badge bg-primary rounded-5">Bazë</span>
                                    </li>
                                    <!-- Sub-Kontat ekzistuese -->
                                    <?php foreach ($subaccounts as $index => $subaccount): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <input type="text" class="form-control me-2" name="subaccounts[<?= $index ?>][name]" value="<?= htmlspecialchars($subaccount['name']) ?>" placeholder="Emri i Bashkëpunëtorit" required>
                                            <input type="number" name="subaccounts[<?= $index ?>][percentage]" value="<?= htmlspecialchars($subaccount['percentage']) ?>" class="form-control me-2 percentage-input" min="0" max="<?= htmlspecialchars($max_sub_percentage) ?>" step="0.01" placeholder="Përqindja (%)" required>
                                            <button type="button" class="remove-collaborator input-custom-css px-3 py-2">
                                                <i class="fi fi-rr-trash"></i>
                                            </button>
                                        </li>
                                    <?php endforeach; ?>
                            </div>
                    </div>
                    <hr>
                    <!-- Seksioni i Përqindjes Totale -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>Përqindja Totale e Bashkëpunëtorëve:</strong>
                            <span id="total-percentage" class="badge bg-info rounded-5">0%</span>
                        </div>
                        <div class="form-text text-danger" id="total-error"></div>
                        <div class="form-text">Përqindja totale e bashkëpunëtorëve nuk duhet të kalojë <?= htmlspecialchars($max_sub_percentage) ?>%.</div>
                    </div>
                    <!-- Butoni i Ruajtjes -->
                    <div class="d-flex justify-content-end">
                        <button type="submit" class=" input-custom-css px-3 py-2">
                            Ruaj
                        </button>
                    </div>
                    </form>
                    <!-- Fundi i Formularit -->
                </div>
                <!-- Shtoni më shumë pane nëse keni pills shtesë -->
            </div>
        </div>
        <!-- Fundi i Kartës -->
    </div>
</div>
</div>
<!-- Bootstrap JS dhe varësitë -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const splitPayList = document.getElementById('split-pay-list');
        const totalDisplay = document.getElementById('total-percentage');
        const inputError = document.getElementById('input-error');
        const totalError = document.getElementById('total-error');
        const addCollaboratorBtn = document.getElementById('add-collaborator');
        const splitPayCollaboratorInput = document.getElementById('split-pay-collaborator');
        const form = document.getElementById('subaccount-form');
        const toastContainer = document.getElementById('toast-container');
        const maxSubPercentage = <?= json_encode($max_sub_percentage) ?>;
        // Funksion për të krijuar dhe treguar një toast
        const showToast = (message, type = 'success') => {
            const toastId = `toast-${Date.now()}`;
            const toastHTML = `
                <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;
            toastContainer.insertAdjacentHTML('beforeend', toastHTML);
            const toastElement = document.getElementById(toastId);
            const bsToast = new bootstrap.Toast(toastElement, {
                delay: 5000
            });
            bsToast.show();
            toastElement.addEventListener('hidden.bs.toast', () => {
                toastElement.remove();
            });
        };
        // Trego mesazhet nga serveri me toast-e
        <?php if ($error): ?>
            showToast("<?= addslashes($error) ?>", 'danger');
        <?php endif; ?>
        <?php if ($success): ?>
            showToast("<?= addslashes($success) ?>", 'success');
        <?php endif; ?>
        // Funksion për të përditësuar përqindjen totale
        const updateTotal = () => {
            const percentages = document.querySelectorAll('.percentage-input');
            let total = 0;
            percentages.forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            total = Math.round(total * 100) / 100; // Rrotullo në 2 decimalë
            totalDisplay.textContent = `${total}%`;
            if (total > maxSubPercentage) {
                totalDisplay.classList.remove('bg-info', 'bg-success');
                totalDisplay.classList.add('bg-danger');
                totalError.textContent = `Përqindja totale nuk mund të kalojë ${maxSubPercentage}%.`;
            } else if (total === maxSubPercentage) {
                totalDisplay.classList.remove('bg-info', 'bg-danger');
                totalDisplay.classList.add('bg-success');
                totalError.textContent = '';
            } else {
                totalDisplay.classList.remove('bg-danger', 'bg-success');
                totalDisplay.classList.add('bg-info');
                totalError.textContent = '';
            }
        };
        // Funksion për të shtuar një bashkëpunëtor të ri
        const addCollaborator = () => {
            const name = splitPayCollaboratorInput.value.trim();
            if (!name) {
                showToast('Emri i bashkëpunëtorit nuk mund të jetë bosh.', 'danger');
                return;
            }
            const currentCollaborators = splitPayList.querySelectorAll('li').length - 2; // Përjashtoni header-in dhe bazën
            if (currentCollaborators >= 5) {
                showToast('Nuk mund të shtoni më shumë se 5 bashkëpunëtorë.', 'warning');
                return;
            }
            const remaining = maxSubPercentage - Array.from(document.querySelectorAll('.percentage-input'))
                .reduce((acc, input) => acc + (parseFloat(input.value) || 0), 0);
            if (remaining <= 0) {
                showToast('Nuk ka përqindje të mbetur për të shtuar bashkëpunëtorë.', 'warning');
                return;
            }
            // Krijoni një rresht të ri në listë
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            const index = splitPayList.querySelectorAll('li').length - 2; // Përshtat indeksin
            li.innerHTML = `
                <input type="text" class="form-control me-2" name="subaccounts[${index}][name]" value="${name}" placeholder="Emri i Bashkëpunëtorit" required>
                <input type="number" name="subaccounts[${index}][percentage]" value="0" class="form-control me-2 percentage-input" min="0" max="${maxSubPercentage}" step="0.01" placeholder="Përqindja (%)" required>
                <button type="button" class="remove-collaborator input-custom-css px-3 py-2">
                    <i class="fi fi-rr-trash"></i>
                </button>
            `;
            splitPayList.appendChild(li);
            // Pastroni fushën e input-it dhe përditësoni totalin
            splitPayCollaboratorInput.value = '';
            updateTotal();
            showToast('Bashkëpunëtor u shtua me sukses.', 'success');
        };
        // Funksion për të hequr një bashkëpunëtor
        const removeCollaborator = (event) => {
            if (event.target.closest('.remove-collaborator')) {
                const li = event.target.closest('li');
                splitPayList.removeChild(li);
                updateTotal();
                showToast('Bashkëpunëtor u heq me sukses.', 'info');
            }
        };
        // Funksion për të validuar input-in e përqindjes
        const validatePercentage = (input) => {
            const value = parseFloat(input.value) || 0;
            const max = parseFloat(input.getAttribute('max')) || maxSubPercentage;
            if (value < 0 || value > max) {
                input.setCustomValidity(`Vlera duhet të jetë midis 0 dhe ${max}%.`);
            } else {
                input.setCustomValidity('');
            }
            input.reportValidity();
            updateTotal();
        };
        // Shtoni event listeners
        addCollaboratorBtn.addEventListener('click', addCollaborator);
        splitPayList.addEventListener('click', removeCollaborator);
        splitPayList.addEventListener('input', (event) => {
            if (event.target.classList.contains('percentage-input')) {
                validatePercentage(event.target);
            }
        });
        // Validimi i dërgimit të formularit dhe rifreskimi i faqes
        form.addEventListener('submit', (event) => {
            const total = parseFloat(totalDisplay.textContent);
            if (total > maxSubPercentage) {
                event.preventDefault();
                showToast(`Nuk mund të vazhdoni. Përqindja totale nuk mund të kalojë ${maxSubPercentage}%.`, 'danger');
                totalDisplay.classList.replace('bg-info', 'bg-danger');
            } else {
                // Lejoni dërgimin e formularit dhe rifreskimin e faqes
                // Opsionale: Shtoni një indikator ngarkimi këtu
            }
        });
        // Përditësoni totalin fillestar
        updateTotal();
    });
</script>
</body>

</html>
<?php include 'partials/footer.php'; ?>