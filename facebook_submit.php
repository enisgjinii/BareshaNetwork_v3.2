<?php
include 'conn-d.php';

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Retrieve the submitted form data
    $emri_mbiemri = $_POST["emri_mbiemri"];
    $emri_faqes = $_POST["emri_faqes"];
    $dataKrijimit = $_POST["dataKrijimit"];
    $dataSkadimit = $_POST["dataSkadimit"];
    $linkuFaqes = $_POST["linkuFaqes"];
    $numriPersonal = $_POST["numriPersonal"];
    $adsAccount = $_POST["merre_adresen"];
    $kategoria = $_POST["kategoria"];
    $numriTelefonit = $_POST["numriTelefonit"];
    $perqindja = $_POST["perqindja"];
    $numriXhirollogarise = $_POST["numriXhirollogarise"];
    $adresa = $_POST["adresa"];
    $infoShtese = $_POST["infoShtese"];

    // Prepare the SQL statement
    $sql = "INSERT INTO facebook (emri_mbiemri, emri_faqes, dataKrijimit, dataSkadimit, linkuFaqes, numriPersonal, adsAccount, kategoria, numriTelefonit, perqindja, numriXhirollogarise, adresa, infoShtese)
          VALUES ('$emri_mbiemri', '$emri_faqes', '$dataKrijimit', '$dataSkadimit', '$linkuFaqes', '$numriPersonal', '$adsAccount', '$kategoria', '$numriTelefonit', '$perqindja', '$numriXhirollogarise', '$adresa', '$infoShtese')";

    // Execute the SQL statement
    if (mysqli_query($conn, $sql)) {
        echo '<script>window.location.href = "vegla_facebook.php"</script>';
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
}
