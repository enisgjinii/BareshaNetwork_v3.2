<?php
include 'partials/header.php';
include 'conn-d.php';
session_start();
session_regenerate_id();

// Anti-CSRF token generation
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Validate CSRF Token
function validateCSRFToken($token)
{
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Prepare statement
$stmt = $conn->prepare("UPDATE platformat_2 SET Fee = ? WHERE AccountingPeriod = ?");

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check for the CSRF token
    if (!validateCSRFToken($_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }

    // Validate and sanitize user inputs
    $selectedPeriod = $_POST['periods'];
    $feeValue = $_POST['add_currencyValue'];

    // Bind parameters
    $stmt->bind_param("ss", $feeValue, $selectedPeriod);

    // Execute the update statement
    if ($stmt->execute()) {
        // Return a success message
        echo json_encode(['success' => true, 'message' => 'Rreshtat u përditësuan me sukses!']);
        exit();
    } else {
        // Return an error message
        echo json_encode(['success' => false, 'message' => 'Gabim gjatë përditësimit të rreshtave.']);
        exit();
    }
}
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Platformat</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">
                            Ndrysho valutimin
                        </a>
                    </li>
            </nav>
            <div class="p-5 mb-4 card rounded-5 " id="upload-container">
                <form id="updateForm">
                    <!-- Add CSRF token to the form -->
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <div class="row">
                        <div class="col">
                            <label for="periods" class="form-label">Zgjidh periudhën</label>
                            <select name="periods" id="periods" class="form-select">
                                <?php
                                $sql = "SELECT DISTINCT AccountingPeriod FROM platformat_2";
                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $accountingPeriod = htmlspecialchars($row['AccountingPeriod']);
                                        echo "<option value='" . $accountingPeriod . "'>" . $accountingPeriod . "</option>";
                                    }
                                }
                                ?>
                            </select>
                            <p id="selected-period" class="mt-2"></p>
                        </div>
                        <div class="col">
                            <label for="add_currencyValue" class="form-label">Shto vlerën</label>
                            <input type="text" class="form-control rounded-5 border border-2" id="add_currencyValue" name="add_currencyValue" required>
                        </div>
                    </div>
                    <hr>
                    <button type="button" onclick="submitForm()" class="input-custom-css px-3 py-2"><i class="fi fi-rr-add-document fa-lg"></i>&nbsp; Shto valutimin e Bareshes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    new Selectr('#periods', {
        searchable: true,
    });
    document.getElementById('periods').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        document.getElementById('selected-period').innerHTML = 'Ju keni zgjedhur perioden: ' + selectedOption.value;
    });

    function submitForm() {
        console.log('Form submitted');
        var formData = new FormData(document.getElementById('updateForm'));
        fetch('currency_submit.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Server response:', data);
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses',
                        text: data.message,
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gabim',
                        text: data.message,
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gabim',
                    text: 'Gabim gjatë përpjekjes për të përditësuar rreshtat.',
                });
            });
    }
</script>

<?php include 'partials/footer.php'; ?>