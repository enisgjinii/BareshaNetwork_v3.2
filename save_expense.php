<?php
include 'conn-d.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registruesi = $_POST['recipient-name'];
    $pershkrimi = $_POST['message'];
    $shuma = $_POST['amount'];
    $dokumenti = $_FILES['file']['name'];

    // File upload handling
    if ($dokumenti) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($dokumenti);
        move_uploaded_file($_FILES['file']['tmp_name'], $target_file);
    }

    $query = "INSERT INTO expenses (registruesi, pershkrimi, shuma, dokumenti) VALUES ('$registruesi', '$pershkrimi', '$shuma', '$dokumenti')";
    if ($conn->query($query) === TRUE) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }
}
