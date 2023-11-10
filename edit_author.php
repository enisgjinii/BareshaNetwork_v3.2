<?php
// Check if the form is submitted
if (isset($_POST['submit'])) {
    $id = $_POST['id'];

    // Retrieve the form data
    $emriMbiemri = $_POST['emri_mbiemri'];
    $kategoria = $_POST['kategoria'];
    $publikuesi = $_POST['publikuesi'];
    $numriPersonal = $_POST['numriPersonal'];
    $kompania = $_POST['Kompania'];
    $ipiNumber = $_POST['ipiNumber'];

    // Perform the database update operation
    // Assuming you have established a database connection
    include 'conn-d.php';

    // Prepare the update query
    $query = "UPDATE autori SET emriDheMbiemriAutorit='$emriMbiemri', kategoria='$kategoria', publikuesi='$publikuesi', numriPersonal='$numriPersonal', kompania='$kompania',ipiNumber='$ipiNumber' WHERE id = '$id'";

    // Execute the update query
    if (mysqli_query($conn, $query)) {
        header("Location: autor.php");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
