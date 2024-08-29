<?php
include '../../conn-d.php';

$errors = array();
$success = "";

if (isset($_POST['delete_selected'])) {
    // Check if any contracts were selected
    if (!empty($_POST['selected_contracts'])) {

        // Prepare a delete statement
        $delete_query = "DELETE FROM kontrata_gjenerale WHERE id = ?";
        $stmt = mysqli_prepare($conn, $delete_query);

        if ($stmt) {
            // Bind the parameter for the contract id
            mysqli_stmt_bind_param($stmt, "i", $contract_id);

            // Loop through the selected contracts and delete them
            foreach ($_POST['selected_contracts'] as $contract_id) {
                // Execute the delete statement
                mysqli_stmt_execute($stmt);

                // Check for errors
                if (mysqli_stmt_errno($stmt) != 0) {
                    $errors[] = "Error deleting contract with ID $contract_id: " . mysqli_stmt_error($stmt);
                }
            }

            // Close the prepared statement
            mysqli_stmt_close($stmt);

            // Check if there are no errors
            if (empty($errors)) {
                $success = "Selected contracts have been deleted successfully.";

                // Redirect to lista_e_kontratave_gjenerale.php after successful deletion
                header("Location: ../../lista_kontratave_gjenerale.php");
                exit; // Make sure to exit to prevent further script execution
            }
        } else {
            $errors[] = "Error preparing delete statement: " . mysqli_error($conn);
        }
    }
}

// Display Success Message
if (!empty($success)) {
    echo "<div class='success-message'>$success</div>";
}

// Display Errors
if (!empty($errors)) {
    echo "<div class='error-message'>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul>";
    echo "</div>";
}
