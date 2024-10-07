<?php
// Include the database connection
include '../../conn-d.php';

// Check if 'id' is set and is a valid number
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch the dokument path before deletion to remove the file
    $stmt = $conn->prepare("SELECT dokument FROM tatimi WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($dokument_path);
    $stmt->fetch();
    $stmt->close();

    if ($dokument_path) {
        // Delete the record from the database
        $stmt = $conn->prepare("DELETE FROM tatimi WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            // Delete the file from the server
            if (file_exists($dokument_path)) {
                unlink($dokument_path);
            }

            // Redirect back with a success message
            header("Location: ../../ttatimi.php?msg=Rekordi u fshi me sukses");
            exit();
        } else {
            die("Error: " . $stmt->error);
        }
    } else {
        die("Error: Rekordi nuk u gjet.");
    }

    $stmt->close();
    $conn->close();
} else {
    // Invalid request
    header("Location: ../../ttatimi.php?msg=Rekordi i pavlefshëm");
    exit();
}
?>
