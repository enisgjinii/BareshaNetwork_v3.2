<?php
include 'conn-d.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registruesi = $_POST['recipient-name'];
    $pershkrimi = $_POST['message'];
    $shuma = $_POST['amount'];
    $dokumenti = $_FILES['file']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($dokumenti);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Kontrolloni nëse është skedar i vërtetë
    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["file"]["tmp_name"]);
        if ($check !== false) {
            echo "Skedari është një imazh - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "Skedari nuk është një imazh.";
            $uploadOk = 0;
        }
    }

    // Kontrolloni nëse skedari ekziston
    if (file_exists($target_file)) {
        echo "Sorry, skedari ekziston tashmë.";
        $uploadOk = 0;
    }

    // Kontrolloni madhësinë e skedarit
    if ($_FILES["file"]["size"] > 500000) {
        echo "Sorry, skedari juaj është shumë i madh.";
        $uploadOk = 0;
    }

    // Lejoni vetëm formatet e caktuara të skedarit
    if (
        $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif"
    ) {
        echo "Sorry, vetëm formatet JPG, JPEG, PNG & GIF janë të lejuara.";
        $uploadOk = 0;
    }

    // Kontrolloni $uploadOk për gabime
    if ($uploadOk == 0) {
        echo "Sorry, skedari juaj nuk u ngarkua.";
        // Ngarko skedarin nëse nuk ka gabime
    } else {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            echo "Skedari " . basename($_FILES["file"]["name"]) . " është ngarkuar me sukses.";
        } else {
            echo "Sorry, ndodhi një problem gjatë ngarkimit të skedarit.";
        }
    }

    // Shto informacionin në bazën e të dhënave nëse nuk ka gabime
    if ($uploadOk != 0) {
        $query = "INSERT INTO expenses (registruesi, pershkrimi, shuma, dokumenti) VALUES ('$registruesi', '$pershkrimi', '$shuma', '$dokumenti')";
        if ($conn->query($query) === TRUE) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            echo "Error: " . $query . "<br>" . $conn->error;
        }
    }
}
