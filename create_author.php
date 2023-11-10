<?php
include 'conn-d.php';

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Retrieve form data
    $emriMbiemri = $_POST['emri_mbiemri'];
    $kategoria = $_POST['kategoria'];
    $publikuesi = $_POST['publikuesi'];
    $numriPersonal = $_POST['numriPersonal'];
    $kompania = $_POST['Kompania'];
    $ipiNumber = $_POST['ipiNumber'];

    // Handle the uploaded file
    if (isset($_FILES['dokument']) && $_FILES['dokument']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['dokument']['tmp_name'];
        $filename = $_FILES['dokument']['name'];
        $destination = 'uploadsAutor/' . $filename;

        // Move the uploaded file to the desired destination
        if (move_uploaded_file($file, $destination)) {
            // Insert the file path or unique identifier into the "autori" table
            $insertQuery = "INSERT INTO autori (emriDheMbiemriAutorit, kategoria, publikuesi, numriPersonal, dokument, kompania, ipiNumber) VALUES ('$emriMbiemri', '$kategoria', '$publikuesi', '$numriPersonal', '$destination', '$kompania', '$ipiNumber')";

            if (mysqli_query($conn, $insertQuery)) {
                header("Location: autor.php");
                exit();
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            echo "Error uploading the file.";
        }
    }
}
?>
