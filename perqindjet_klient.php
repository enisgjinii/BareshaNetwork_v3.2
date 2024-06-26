<?php
include 'partials/header.php';

// Get id from URL
$id = $_GET['id'];

// Fetch client data
$sql = "SELECT * FROM klientet WHERE id = $id";
$result = mysqli_query($conn, $sql);
$client = mysqli_fetch_assoc($result);

// Fetch sub-accounts
$subaccounts_sql = "SELECT * FROM client_subaccounts WHERE client_id = $id ORDER BY id ASC LIMIT 5";
$subaccounts_result = mysqli_query($conn, $subaccounts_sql);

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        // Delete existing sub-accounts
        mysqli_query($conn, "DELETE FROM client_subaccounts WHERE client_id = $id");

        // Insert new sub-accounts
        $stmt = mysqli_prepare($conn, "INSERT INTO client_subaccounts (client_id, name, percentage) VALUES (?, ?, ?)");
        
        foreach ($_POST['subaccounts'] as $subaccount) {
            $name = $subaccount['name'];
            $percentage = $subaccount['percentage'];
            mysqli_stmt_bind_param($stmt, "isd", $id, $name, $percentage);
            mysqli_stmt_execute($stmt);
        }

        mysqli_stmt_close($stmt);

        // Commit transaction
        mysqli_commit($conn);

        // Redirect to avoid resubmission
        header("Location: {$_SERVER['PHP_SELF']}?id=$id");
        exit;
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        $error = "An error occurred: " . $e->getMessage();
    }
}
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card p-5 rounded-5">
                <h1 class="h5">Ndarja e përqindjes për <?php echo $client['emri']; ?></h1>
                <?php if (isset($error)) : ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="split-pay-collaborator" class="form-label">Bashkëpunëtor me pagesë të ndarë</label>
                        <input type="text" id="split-pay-collaborator" class="form-control rounded-5 shadow-none border border-2" placeholder="Shkruani emrin e bashkëpunëtorit">
                        <br>
                        <button type="button" id="add-collaborator" class="input-custom-css px-3 py-2">
                            <i class="fi fi-rr-add pt-3"></i>
                            Shto bashkëpunëtor
                        </button>
                    </div>
                    <br>
                    <div class="error" id="error"></div>
                    <br>
                    <ul class="list-group mb-3" id="split-pay-list">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <input type="text" class="form-control me-2 rounded-5" value="Perqindja e Bareshes" readonly>
                            <input type="number" class="form-control me-2 rounded-5 percentage" value="<?php echo $client['perqindja']; ?>" readonly>
                        </li>
                        <?php
                        $index = 0;
                        while ($subaccount = mysqli_fetch_assoc($subaccounts_result)) {
                            echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
                                    <input class='form-control me-2 rounded-5' type='text' name='subaccounts[{$index}][name]' value='{$subaccount['name']}' readonly>
                                    <input type='number' name='subaccounts[{$index}][percentage]' value='{$subaccount['percentage']}' class='form-control me-2 rounded-5 percentage-input percentage'>
                                    <button type='button' class='input-custom-css px-3 py-2 remove-collaborator'><i class='fi fi-rr-cross-circle pt-3'></i></button>
                                  </li>";
                            $index++;
                        }
                        ?>
                    </ul>
                    <div class="total fw-bold" id="total">Totali (duhet të jetë i barabartë me 100%): 100%</div>
                    <br>
                    <button type="submit" class="input-custom-css px-3 py-2" id="next-btn"><i class="fi fi-rr-file-export pt-3"></i> Ruaj</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const splitPayList = document.getElementById('split-pay-list');
    const totalDisplay = document.getElementById('total');
    const errorDisplay = document.getElementById('error');
    const addCollaboratorBtn = document.getElementById('add-collaborator');
    const splitPayCollaboratorInput = document.getElementById('split-pay-collaborator');
    const nextBtn = document.getElementById('next-btn');

    function updateTotal() {
        const percentages = document.querySelectorAll('.percentage');
        let total = 0;
        percentages.forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        totalDisplay.textContent = `Totali (duhet të jetë i barabartë me 100%): ${total.toFixed(2)}%`;
        if (total !== 100) {
            totalDisplay.style.color = 'red';
            errorDisplay.textContent = 'Përqindja totale duhet të jetë e barabartë 100%.';
        } else {
            totalDisplay.style.color = 'black';
            errorDisplay.textContent = '';
        }
    }

    function addCollaborator() {
        const collaboratorName = splitPayCollaboratorInput.value.trim();
        if (collaboratorName === '') {
            errorDisplay.textContent = 'Emri i bashkëpunëtorit nuk mund të jetë i zbrazët.';
            return;
        }
        if (document.querySelectorAll('#split-pay-list li').length >= 6) {
            errorDisplay.textContent = 'Nuk mund të shtoni më shumë se 5 bashkëpunëtorë.';
            return;
        }
        const index = document.querySelectorAll('#split-pay-list li').length - 1;
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center';
        li.innerHTML = `
            <input type="text" class="form-control me-2 rounded-5" name="subaccounts[${index}][name]" value="${collaboratorName}" readonly>
            <input type="number" name="subaccounts[${index}][percentage]" value="0" class="form-control me-2 rounded-5 percentage-input percentage">
            <button type="button" class="input-custom-css px-3 py-2 remove-collaborator"><i class="fi fi-rr-cross-circle pt-3"></i></button>
        `;
        splitPayList.appendChild(li);
        splitPayCollaboratorInput.value = '';
        updateTotal();
    }

    function removeCollaborator(event) {
        if (event.target.closest('.remove-collaborator')) {
            const li = event.target.closest('li');
            splitPayList.removeChild(li);
            updateTotal();
        }
    }

    function validatePercentage(input) {
        const percentages = document.querySelectorAll('.percentage');
        let total = 0;
        percentages.forEach(inp => {
            if (inp !== input) {
                total += parseFloat(inp.value) || 0;
            }
        });
        total += parseFloat(input.value) || 0;
        if (total > 100) {
            errorDisplay.textContent = 'Përqindja totale nuk mund të kalojë 100%.';
            input.value = 0;
        } else {
            errorDisplay.textContent = '';
        }
        updateTotal();
    }

    splitPayList.addEventListener('input', function(event) {
        if (event.target.classList.contains('percentage')) {
            validatePercentage(event.target);
        }
    });

    splitPayList.addEventListener('click', removeCollaborator);
    addCollaboratorBtn.addEventListener('click', addCollaborator);
    nextBtn.addEventListener('click', function(event) {
        const total = parseFloat(totalDisplay.textContent.match(/[\d.]+/)[0]);
        if (total !== 100) {
            event.preventDefault();
            errorDisplay.textContent = 'Cannot proceed. Total percentage must equal 100%.';
        } else {
            errorDisplay.textContent = '';
        }
    });

    updateTotal();
});
</script>
</body>
</html>