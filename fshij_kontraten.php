<?php
// Include your database connection file
include 'conn-d.php';

// Check if ID is provided and is numeric
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Get the ID from the URL
    $id = $_GET['id'];

    // Prepare a statement to select the record to be deleted
    $select_query = "SELECT * FROM kontrata WHERE id = ?";
    $select_stmt = mysqli_prepare($conn, $select_query);
    mysqli_stmt_bind_param($select_stmt, 'i', $id);
    mysqli_stmt_execute($select_stmt);
    $result = mysqli_stmt_get_result($select_stmt);

    // Check if record exists
    if (mysqli_num_rows($result) > 0) {
        // Fetch the record
        $row = mysqli_fetch_assoc($result);

        // Prepare a statement to move the record to a recovery table
        $insert_query = "INSERT INTO kontrata_recovery (id, emri, mbiemri, numri_i_telefonit, numri_personal, vepra, data, shenim, nenshkrimi, kontrata_PDF, perqindja, klienti, klient_email, emriartistik, pdf_file) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $insert_stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, 'isssssssssissss', $row['id'], $row['emri'], $row['mbiemri'], $row['numri_i_telefonit'], $row['numri_personal'], $row['vepra'], $row['data'], $row['shenim'], $row['nenshkrimi'], $row['kontrata_PDF'], $row['perqindja'], $row['klienti'], $row['klient_email'], $row['emriartistik'], $row['pdf_file']);
        mysqli_stmt_execute($insert_stmt);

        // Prepare a statement to delete the record from the original table
        $delete_query = "DELETE FROM kontrata WHERE id = ?";
        $delete_stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($delete_stmt, 'i', $id);
        mysqli_stmt_execute($delete_stmt);

        // Close prepared statements
        mysqli_stmt_close($select_stmt);
        mysqli_stmt_close($insert_stmt);
        mysqli_stmt_close($delete_stmt);

        // Redirect to the main page
        header('Location: lista_kontratave.php');
        exit(); // Stop further execution
    } else {
        // Record not found, redirect to error page or handle accordingly
        header('Location: error.php');
        exit(); // Stop further execution
    }
} else {
    // ID is missing or not numeric, redirect to error page or handle accordingly
    header('Location: error.php');
    exit(); // Stop further execution
}
