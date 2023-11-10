<?php
include 'conn-d.php';

$errors = array();
$success = "";

if (isset($_POST['delete_selected'])) {
    // Check if any contracts were selected
    if (!empty($_POST['selected_contracts'])) {

        // Loop through the selected contracts and delete them
        foreach ($_POST['selected_contracts'] as $contract_id) {
            // Perform the deletion query (you should also add error handling)
            $delete_query = "DELETE FROM kontrata_gjenerale WHERE id = $contract_id";
            $result = mysqli_query($conn, $delete_query);

            if (!$result) {
                $errors[] = "Error deleting contract with ID $contract_id: " . mysqli_error($conn);
            }

            if (empty($errors)) {
                // Deletion was successful
                $success = "Selected contracts have been deleted successfully.";
            }
        }
    }
}

// Redirect to lista_e_kontratave_gjenerale.php after successful deletion
if (!empty($success)) {
    header("Location: lista_kontratave_gjenerale.php");
    exit; // Make sure to exit to prevent further script execution
}
?>

<!-- Display Errors -->
<?php
if (!empty($errors)) {
    echo "<div class='error-message'>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul>";
    echo "</div>";
}

// Display Success Message
if (!empty($success)) {
    echo "<div class='success-message'>$success</div>";
}
?>
