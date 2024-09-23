<?php
// Start output buffering to prevent "headers already sent" errors
ob_start();
// Include necessary files and establish database connection
require_once 'conn-d.php'; // Assumes this sets up $conn
require_once 'partials/header.php'; // Should not produce output before processing
// Initialize messages
$messages = [
    'error' => '',
    'success' => '',
];
// Function to safely retrieve integer GET parameters
function getIntParam($param, $default = 0)
{
    return isset($_GET[$param]) && is_numeric($_GET[$param]) ? (int)$_GET[$param] : $default;
}
// Function to execute prepared statements and fetch data
function executeQuery($conn, $query, $types = null, $params = [])
{
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception('Preparation failed: ' . mysqli_error($conn));
    }
    if ($types && $params) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    mysqli_stmt_close($stmt);
    return $data;
}
// Function to process form submission
function processForm($conn, $id, $max_sub_percentage, &$messages)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }
    $subaccounts_input = $_POST['subaccounts'] ?? [];
    $processed_subaccounts = [];
    $total_percentage = 0;
    // Validate each subaccount
    foreach ($subaccounts_input as $index => $subaccount) {
        $name = trim($subaccount['name'] ?? '');
        $percentage = floatval($subaccount['percentage'] ?? 0);
        if ($name === '') {
            $messages['error'] = "Emri i sub-kontës në rreshtin {$index} nuk mund të jetë bosh.";
            return;
        }
        if ($percentage < 0 || $percentage > $max_sub_percentage) {
            $messages['error'] = "Përqindja për '{$name}' në rreshtin {$index} duhet të jetë midis 0 dhe {$max_sub_percentage}%.";
            return;
        }
        $total_percentage += $percentage;
        $processed_subaccounts[] = ['name' => $name, 'percentage' => $percentage];
    }
    if ($total_percentage > $max_sub_percentage) {
        $messages['error'] = "Përqindja totale e sub-kontave nuk mund të kalojë {$max_sub_percentage}%. Aktualisht është {$total_percentage}%.";
        return;
    }
    // Begin transaction
    mysqli_begin_transaction($conn);
    try {
        // Delete existing subaccounts
        executeQuery($conn, "DELETE FROM client_subaccounts WHERE client_id = ?", "i", [$id]);
        // Insert new subaccounts
        if (!empty($processed_subaccounts)) {
            $insert_query = "INSERT INTO client_subaccounts (client_id, name, percentage) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insert_query);
            if (!$stmt) {
                throw new Exception('Preparation failed: ' . mysqli_error($conn));
            }
            foreach ($processed_subaccounts as $sub) {
                mysqli_stmt_bind_param($stmt, "isd", $id, $sub['name'], $sub['percentage']);
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception('Execution failed: ' . mysqli_stmt_error($stmt));
                }
            }
            mysqli_stmt_close($stmt);
        }
        // Commit transaction
        mysqli_commit($conn);
        // Redirect with success parameter
        header("Location: " . $_SERVER['PHP_SELF'] . "?id={$id}&success=1");
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $messages['error'] = "Ndodhi një gabim: " . $e->getMessage();
    }
}
try {
    // Retrieve and validate 'id'
    $id = getIntParam('id');
    if ($id <= 0) {
        throw new Exception('ID e klientit e pavlefshme.');
    }
    // Fetch client data
    $clientData = executeQuery($conn, "SELECT * FROM klientet WHERE id = ?", "i", [$id]);
    $client = $clientData[0] ?? null;
    if (!$client) {
        throw new Exception('Klienti nuk u gjet.');
    }
    // Validate base percentage
    $base_percentage = floatval($client['perqindja'] ?? 0);
    if ($base_percentage < 0 || $base_percentage > 100) {
        throw new Exception('Përqindja bazë për klientin është e pavlefshme.');
    }
    // Calculate maximum allowed subpercentage
    $max_sub_percentage = 100 - $base_percentage;
    // Process form if submitted
    processForm($conn, $id, $max_sub_percentage, $messages);
    // Fetch subaccounts after potential updates
    $subaccounts = executeQuery($conn, "SELECT * FROM client_subaccounts WHERE client_id = ? ORDER BY id ASC LIMIT 5", "i", [$id]);
    // Prepare data for JavaScript
    $subaccounts_js = json_encode($subaccounts);
    // Check for success message
    if (isset($_GET['success']) && $_GET['success'] == 1) {
        $messages['success'] = "Sub-kontat u azhurnuan me sukses.";
    }
} catch (Exception $e) {
    $messages['error'] = $e->getMessage();
}
// End output buffering and flush output
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="sq">

<head>
    <meta charset="UTF-8">
    <title>Shpërndarja e Përqindjeve</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Optional CSS for icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="container-fluid">
                <!-- Breadcrumb Navigation -->
                <nav class="bg-white px-2 rounded-0" style="width:fit-content;" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a class="text-reset" href="#" style="text-decoration: none;">Klientët</a></li>
                        <li class="breadcrumb-item"><a class="text-reset" href="#" style="text-decoration: none;">Lista e Klientëve</a></li>
                        <li class="breadcrumb-item"><a href="klient.php" class="text-reset" style="text-decoration: none;">Edito klientin <?= htmlspecialchars($client['emri']) ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <span class="text-reset" style="text-decoration: none;">
                                Shpërndarja e Përqindjeve për <?= htmlspecialchars($client['emri']) ?>
                            </span>
                        </li>
                    </ol>
                </nav>
                <!-- Card Container -->
                <div class="card p-4">
                    <!-- Header Section -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6>Shpërndarja e Përqindjeve për <span class="text-primary"><?= htmlspecialchars($client['emri']) ?></span></h6>
                        <span class="badge bg-secondary badge-custom rounded-5">
                            ID e klientit : <?= htmlspecialchars($client['id']) ?>
                        </span>
                    </div>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-subaccounts" role="tabpanel" aria-labelledby="pills-subaccounts-tab">
                            <!-- Start of Form -->
                            <form method="POST" id="subaccount-form" novalidate>
                                <!-- Collaborator Input Section -->
                                <div class="mb-4">
                                    <label for="split-pay-collaborator" class="form-label">
                                        Bashkëpunëtor me Pagesë të Ndarë
                                    </label>
                                    <div class="d-flex">
                                        <input type="text" id="split-pay-collaborator" class="form-control border border-2 me-2" placeholder="Shkruani emrin e bashkëpunëtorit">
                                        <button type="button" id="add-collaborator" class="input-custom-css px-3 py-2 ">
                                            Shto
                                        </button>
                                    </div>
                                    <div class="form-text text-danger" id="input-error"></div>
                                </div>
                                <!-- Subaccounts List -->
                                <div class="mb-4">
                                    <ul class="list-group" id="split-pay-list">
                                        <!-- Header Row -->
                                        <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                                            <span class="fw-bold">Sub-Kontë</span>
                                            <span class="fw-bold">Përqindja (%)</span>
                                            <span class="fw-bold">Veprimet</span>
                                        </li>
                                        <!-- Base Percentage Row -->
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="fw-semibold">Përqindja Bazë (<?= htmlspecialchars($base_percentage) ?>%)</span>
                                            <span class="fw-semibold"><?= htmlspecialchars($base_percentage) ?>%</span>
                                            <span class="badge bg-primary rounded-5">Bazë</span>
                                        </li>
                                        <!-- Existing Subaccounts -->
                                        <?php foreach ($subaccounts as $index => $subaccount): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <input type="text" class="form-control me-2" name="subaccounts[<?= $index ?>][name]" value="<?= htmlspecialchars($subaccount['name']) ?>" placeholder="Emri i Bashkëpunëtorit" required>
                                                <input type="number" name="subaccounts[<?= $index ?>][percentage]" value="<?= htmlspecialchars($subaccount['percentage']) ?>" class="form-control me-2 percentage-input" min="0" max="<?= htmlspecialchars($max_sub_percentage) ?>" step="0.01" placeholder="Përqindja (%)" required>
                                                <button type="button" class="remove-collaborator input-custom-css px-3 py-2 ">
                                                    <i class="fi fi-rr-trash"></i>
                                                </button>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <!-- Save Button -->
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="input-custom-css px-3 py-2">
                                        Ruaj
                                    </button>
                                </div>
                                <hr>
                                <!-- Total Percentage Section -->
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong>Përqindja Totale e Bashkëpunëtorëve:</strong>
                                        <span id="total-percentage" class="badge bg-info rounded-5">0%</span>
                                    </div>
                                    <div class="form-text text-danger" id="total-error"></div>
                                    <div class="form-text">Përqindja totale e bashkëpunëtorëve nuk duhet të kalojë <?= htmlspecialchars($max_sub_percentage) ?>%.</div>
                                </div>

                                <!-- Display Messages -->
                                <?php if ($messages['error'] || $messages['success']): ?>
                                    <div class="mb-4">
                                        <?php if ($messages['error']): ?>
                                            <div class="alert alert-danger"><?= htmlspecialchars($messages['error']) ?></div>
                                        <?php endif; ?>
                                        <?php if ($messages['success']): ?>
                                            <div class="alert alert-success"><?= htmlspecialchars($messages['success']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                            </form>
                            <!-- End of Form -->
                        </div>
                        <!-- Demo Section: Splitting of the Total Amount -->
                        <div class="mt-5">
                            <h6>Shpërndarja e Shumës Totale</h6>
                            <!-- Amount Input -->
                            <div class="mb-3 d-flex align-items-center">
                                <input type="number" id="demo-total-amount" class="form-control" value="1000" step="0.01" min="0" placeholder="Shuma Totale">
                                <button type="button" id="calculate-demo" class="input-custom-css px-3 py-2 ms-2">
                                    Kalkuloni
                                </button>
                            </div>
                            <table class="table table-bordered" id="demo-table">
                                <thead>
                                    <tr>
                                        <th>Përfituesi</th>
                                        <th>Përqindja (%)</th>
                                        <th>Shuma €</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Rows will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        <!-- End of Demo Section -->
                    </div>
                </div>
                <!-- End of Card -->
            </div>
        </div>
    </div>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const splitPayList = document.getElementById('split-pay-list');
            const totalDisplay = document.getElementById('total-percentage');
            const inputError = document.getElementById('input-error');
            const totalError = document.getElementById('total-error');
            const addCollaboratorBtn = document.getElementById('add-collaborator');
            const splitPayCollaboratorInput = document.getElementById('split-pay-collaborator');
            const form = document.getElementById('subaccount-form');
            const maxSubPercentage = <?= json_encode($max_sub_percentage) ?>;
            const basePercentage = <?= json_encode($base_percentage) ?>;
            const subaccountsData = <?= $subaccounts_js ?>;
            // Function to display SweetAlert2 notifications
            const showAlert = (message, type = 'success') => {
                Swal.fire({
                    icon: type,
                    title: message,
                    showConfirmButton: false,
                    timer: 3000,
                    toast: true,
                    position: 'top-end',
                    background: type === 'error' ? '#f8d7da' : type === 'warning' ? '#fff3cd' : '#d1e7dd',
                    color: type === 'error' ? '#842029' : type === 'warning' ? '#664d03' : '#0f5132',
                    iconColor: type === 'error' ? '#f5c2c7' : type === 'warning' ? '#ffecb5' : '#badbcc',
                });
            };
            // Display server-side messages using SweetAlert2
            <?php if ($messages['error']): ?>
                showAlert("<?= addslashes($messages['error']) ?>", 'error');
            <?php endif; ?>
            <?php if ($messages['success']): ?>
                showAlert("<?= addslashes($messages['success']) ?>", 'success');
            <?php endif; ?>
            // Function to update the total percentage display
            const updateTotal = () => {
                const percentages = document.querySelectorAll('.percentage-input');
                let total = Array.from(percentages).reduce((acc, input) => acc + (parseFloat(input.value) || 0), 0);
                total = Math.round(total * 100) / 100; // Round to 2 decimal places
                totalDisplay.textContent = `${total}%`;
                totalDisplay.classList.remove('bg-info', 'bg-success', 'bg-danger');
                totalError.textContent = '';
                if (total > maxSubPercentage) {
                    totalDisplay.classList.add('bg-danger');
                    totalError.textContent = `Përqindja totale nuk mund të kalojë ${maxSubPercentage}%.`;
                } else if (total === maxSubPercentage) {
                    totalDisplay.classList.add('bg-success');
                } else {
                    totalDisplay.classList.add('bg-info');
                }
            };
            // Function to add a new collaborator
            const addCollaborator = () => {
                const name = splitPayCollaboratorInput.value.trim();
                if (!name) {
                    showAlert('Emri i bashkëpunëtorit nuk mund të jetë bosh.', 'error');
                    return;
                }
                const currentCollaborators = splitPayList.querySelectorAll('li').length - 2; // Exclude header and base
                if (currentCollaborators >= 5) {
                    showAlert('Nuk mund të shtoni më shumë se 5 bashkëpunëtorë.', 'warning');
                    return;
                }
                const remaining = maxSubPercentage - Array.from(document.querySelectorAll('.percentage-input'))
                    .reduce((acc, input) => acc + (parseFloat(input.value) || 0), 0);
                if (remaining <= 0) {
                    showAlert('Nuk ka përqindje të mbetur për të shtuar bashkëpunëtorë.', 'warning');
                    return;
                }
                // Create a new list item for the collaborator
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                const index = splitPayList.querySelectorAll('li').length - 2; // Adjust index
                li.innerHTML = `
                    <input type="text" class="form-control me-2" name="subaccounts[${index}][name]" value="${name}" placeholder="Emri i Bashkëpunëtorit" required>
                    <input type="number" name="subaccounts[${index}][percentage]" value="0" class="form-control me-2 percentage-input" min="0" max="${maxSubPercentage}" step="0.01" placeholder="Përqindja (%)" required>
                    <button type="button" class="remove-collaborator input-custom-css px-3 py-2">
                        <i class="fi fi-rr-trash"></i>
                    </button>
                `;
                splitPayList.appendChild(li);
                // Clear the input field and update total
                splitPayCollaboratorInput.value = '';
                updateTotal();
                showAlert('Bashkëpunëtor u shtua me sukses.', 'success');
            };
            // Function to remove a collaborator
            const removeCollaborator = (event) => {
                if (event.target.closest('.remove-collaborator')) {
                    const li = event.target.closest('li');
                    splitPayList.removeChild(li);
                    updateTotal();
                    showAlert('Bashkëpunëtor u hoq me sukses.', 'info');
                }
            };
            // Function to validate percentage input
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
            // Event listeners
            addCollaboratorBtn.addEventListener('click', addCollaborator);
            splitPayList.addEventListener('click', removeCollaborator);
            splitPayList.addEventListener('input', (event) => {
                if (event.target.classList.contains('percentage-input')) {
                    validatePercentage(event.target);
                }
            });
            // Form submission validation
            form.addEventListener('submit', (event) => {
                const total = parseFloat(totalDisplay.textContent);
                if (total > maxSubPercentage) {
                    event.preventDefault();
                    showAlert(`Nuk mund të vazhdoni. Përqindja totale nuk mund të kalojë ${maxSubPercentage}%.`, 'error');
                    totalDisplay.classList.replace('bg-info', 'bg-danger');
                }
            });
            // Initialize total display
            updateTotal();
            // Demo Calculation
            const calculateDemoBtn = document.getElementById('calculate-demo');
            const demoTotalAmountInput = document.getElementById('demo-total-amount');
            const demoTableBody = document.querySelector('#demo-table tbody');
            const calculateDemo = () => {
                const totalAmount = parseFloat(demoTotalAmountInput.value) || 0;
                if (totalAmount <= 0) {
                    showAlert('Ju lutemi shkruani një shumë totale të vlefshme.', 'error');
                    return;
                }
                // Clear previous results
                demoTableBody.innerHTML = '';
                // Calculate Baresha's share
                const bareshaShare = (totalAmount * basePercentage) / 100;
                // Add Baresha's row
                const bareshaRow = `
                    <tr>
                        <td><strong>Baresha</strong></td>
                        <td>${basePercentage}%</td>
                        <td>${bareshaShare.toFixed(2)}</td>
                    </tr>
                `;
                demoTableBody.insertAdjacentHTML('beforeend', bareshaRow);
                // Get current subaccounts from the form
                const subaccountInputs = document.querySelectorAll('#split-pay-list li:not(:first-child):not(:nth-child(2))');
                let totalDistributed = bareshaShare;
                subaccountInputs.forEach(li => {
                    const nameInput = li.querySelector('input[name$="[name]"]');
                    const percentageInput = li.querySelector('input[name$="[percentage]"]');
                    const name = nameInput.value.trim();
                    const percentage = parseFloat(percentageInput.value) || 0;
                    const share = (totalAmount * percentage) / 100;
                    totalDistributed += share;
                    const subRow = `
                        <tr>
                            <td>${name}</td>
                            <td>${percentage}%</td>
                            <td>${share.toFixed(2)}</td>
                        </tr>
                    `;
                    demoTableBody.insertAdjacentHTML('beforeend', subRow);
                });
                // Add Total row
                const totalRow = `
                    <tr>
                        <th>Totali</th>
                        <th>100%</th>
                        <th>${totalDistributed.toFixed(2)}</th>
                    </tr>
                `;
                demoTableBody.insertAdjacentHTML('beforeend', totalRow);
            };
            calculateDemoBtn.addEventListener('click', calculateDemo);
            // Automatically calculate demo on page load
            calculateDemo();
        });
    </script>
</body>

</html>
<?php include 'partials/footer.php'; ?>