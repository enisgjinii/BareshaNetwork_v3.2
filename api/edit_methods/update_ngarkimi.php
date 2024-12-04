<?php
// update_ngarkimi.php

include '../../conn-d.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $kengetari = $_POST['kengetari'];
    $emri = $_POST['emri'];
    $teksti = $_POST['teksti'];
    $muzika = $_POST['muzika'];
    $orkestra = $_POST['orkestra'];
    $data = $_POST['data'];
    // Add other fields as necessary

    // Prepare the SQL update statement
    $sql = "UPDATE ngarkimi SET 
                kengetari = ?, 
                emri = ?, 
                teksti = ?, 
                muzika = ?, 
                orkestra = ?, 
                data = ?
                -- Add other fields as necessary
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        // Bind parameters (s = string, i = integer, etc.)
        mysqli_stmt_bind_param($stmt, 'ssssssi', $kengetari, $emri, $teksti, $muzika, $orkestra, $data, $id);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['status' => 'success', 'message' => 'Record updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error updating record: ' . mysqli_stmt_error($stmt)]);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare the SQL statement']);
    }

    mysqli_close($conn);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
