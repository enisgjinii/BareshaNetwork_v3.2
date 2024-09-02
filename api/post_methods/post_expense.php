<?php
include '../../conn-d.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registruesi = $_POST['recipient-name'];
    $pershkrimi = $_POST['message'];
    $shuma = $_POST['amount'];
    $dokumenti = $_FILES['file']['name'];
    $target_dir = "../../uploads/";
    $target_file = $target_dir . basename($dokumenti);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Centralized file upload validations
    if ($_FILES["file"]["size"] > 500000) {
        echo "Sorry, skedari juaj është shumë i madh.";
        $uploadOk = 0;
    }

    if (file_exists($target_file)) {
        echo "Sorry, skedari ekziston tashmë.";
        $uploadOk = 0;
    }

    // Whitelist approach for allowed file types
    $allowed_file_types = array("jpg", "jpeg", "png", "gif", "bmp", "svg");
    if (!in_array($imageFileType, $allowed_file_types)) {
        echo "Sorry, vetëm formatet JPG, JPEG, PNG, GIF, BMP dhe SVG janë të lejuara.";
        $uploadOk = 0;
    }

    // More specific error messages
    if ($uploadOk == 0) {
        echo "Skedari juaj nuk u ngarkua. Ju lutem korrigjoni gabimet.";
    } else {
        // Move the uploaded file after all checks pass
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            echo "Skedari " . basename($_FILES["file"]["name"]) . " është ngarkuar me sukses.";

            // Insert data into the database only when upload is successful
            $query = "INSERT INTO expenses (registruesi, pershkrimi, shuma, dokumenti) VALUES ('$registruesi', '$pershkrimi', '$shuma', '$dokumenti')";
            if ($conn->query($query) === TRUE) {
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            } else {
                echo "Error: " . $query . "<br>" . $conn->error;
            }
        } else {
            echo "Sorry, ndodhi një problem gjatë ngarkimit të skedarit.";
        }
    }
}
