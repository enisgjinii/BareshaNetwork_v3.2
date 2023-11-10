<?php
include 'conn-d.php';

// Check if a POST request has been made
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if all necessary data has been provided
    if (isset($_POST['edit_emri_mbiemri'], $_POST['edit_emri_kenges'], $_POST['edit_minutasha_kenges'], $_POST['edit_link_platforma'], $_POST['edit_kompania'], $_POST['edit_puntori_regjistrues'], $_POST['edit_info_shtese'], $_POST['edit_roli'], $_POST['edit_link_youtube'])) {
        $edit_emri_mbiemri = $_POST['edit_emri_mbiemri'];
        $edit_emri_kenges = $_POST['edit_emri_kenges'];
        $edit_minutasha_kenges = $_POST['edit_minutasha_kenges'];
        $edit_link_platforma = $_POST['edit_link_platforma'];
        $edit_kompania = $_POST['edit_kompania'];
        $edit_puntori_regjistrues = $_POST['edit_puntori_regjistrues'];
        $edit_info_shtese = $_POST['edit_info_shtese'];
        $edit_roli = $_POST['edit_roli'];
        $edit_link_youtube = $_POST['edit_link_youtube'];

        // Get the ID of the row to be edited
        $row_id = $_POST['row_id'];

        // Update the row in the "kenget_autori" table
        $query = "UPDATE kenget_autori SET emri_autorit='$edit_emri_mbiemri', emri_i_kenges='$edit_emri_kenges', minutasha_e_kenges='$edit_minutasha_kenges', link_platforma='$edit_link_platforma', kompania='$edit_kompania', puntori_regjistrues='$edit_puntori_regjistrues', info_shtese='$edit_info_shtese', roli='$edit_roli', link_youtube='$edit_link_youtube' WHERE id='$row_id'";

        if (mysqli_query($conn, $query)) {
            // Row updated successfully
            header("Location: autor.php");
            exit();
        } else {
            // Error while updating the row
            echo "Error updating row: " . mysqli_error($conn);
        }

        // Close the database connection
        mysqli_close($conn);
    } else {
        // Not all required form data has been provided
        echo "Please fill in all the necessary fields.";
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
    // Check if a GET request with delete_id parameter has been made
    $delete_id = $_GET['delete_id'];

    // Delete the row from the "kenget_autori" table
    $delete_query = "DELETE FROM kenget_autori WHERE id='$delete_id'";

    if (mysqli_query($conn, $delete_query)) {
        // Row deleted successfully
        header("Location: autor.php");
        exit();
    } else {
        // Error while deleting the row
        echo "Error deleting row: " . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
} else {
    // Invalid request method or missing parameters
    echo "Invalid request.";
}
