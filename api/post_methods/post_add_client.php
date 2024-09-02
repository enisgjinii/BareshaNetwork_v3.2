<?php

include '../../conn-d.php';

// Check if the form is submitted
if (isset($_POST['submit'])) {

    if (empty($_POST['min'])) {
        $mon = "JO";
    } else {
        $mon = $_POST['min'];
    }
    $emri_mbiemri = $_POST['emri_mbiemri'] !== '' ? $_POST['emri_mbiemri'] : null;
    $emri_faqes = $_POST['emri_faqes'] !== '' ? $_POST['emri_faqes'] : null;
    $dataKrijimit = $_POST['dataKrijimit'] !== '' ? $_POST['dataKrijimit'] : null;
    $dataSkadimit = $_POST['dataSkadimit'] !== '' ? $_POST['dataSkadimit'] : null;
    $linkuFaqes = $_POST['linkuFaqes'] !== '' ? $_POST['linkuFaqes'] : null;
    $numriPersonal = $_POST['numriPersonal'] !== '' ? $_POST['numriPersonal'] : null;
    $adsAccount = $_POST['merre_adresen'] !== '' ? $_POST['merre_adresen'] : null;
    $kategoria = $_POST['kategoria'] !== '' ? $_POST['kategoria'] : null;
    $numriTelefonit = $_POST['numriTelefonit'] !== '' ? $_POST['numriTelefonit'] : null;
    $perqindja = $_POST['perqindja'] !== '' ? $_POST['perqindja'] : null;
    $numriXhirollogarise = $_POST['numriXhirollogarise'] !== '' ? $_POST['numriXhirollogarise'] : null;
    $adresa = $_POST['adresa'] !== '' ? $_POST['adresa'] : null;
    $infoShtese = $_POST['infoShtese'] !== '' ? $_POST['infoShtese'] : null;


    // Prepare and execute the SQL statement
    $sql = "INSERT INTO facebook (emri_mbiemri, emri_faqes, dataKrijimit, dataSkadimit, linkuFaqes, numriPersonal, adsAccount, kategoria, numriTelefonit, perqindja, numriXhirollogarise, adresa, infoShtese,monetizuar) 
            VALUES ('$emri_mbiemri', '$emri_faqes', '$dataKrijimit', '$dataSkadimit', '$linkuFaqes', '$numriPersonal', '$adsAccount', '$kategoria', '$numriTelefonit', '$perqindja', '$numriXhirollogarise', '$adresa', '$infoShtese','$mon')";

    if (mysqli_query($conn, $sql)) {
        echo "Record inserted successfully.";
        header("Location: ../../vegla_facebook.php");
        exit(); // Make sure to include this line after the redirect
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
}
