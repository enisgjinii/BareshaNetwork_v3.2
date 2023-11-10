<?php
include 'conn-d.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kid = $_POST['kid'];

    // Retrieve the updated values from the form
    $emri_mbiemri = $_POST['emri_mbiemri'];
    $emri_faqes = $_POST['emri_faqes'];
    $data_krijimit = $_POST['data_krijimit'];
    $data_skadimit = $_POST['data_skadimit'];
    $linku_faqes = $_POST['linku_faqes'];
    $numri_personal = $_POST['numri_personal'];
    $ads_account = $_POST['ads_account'];
    $kategoria = $_POST['kategoria'];
    $numri_telefonit = $_POST['numri_telefonit'];
    $perqindja = $_POST['perqindja'];
    $numri_xhirollogarise = $_POST['numri_xhirollogarise'];
    $adresa = $_POST['adresa'];
    $info_shtese = $_POST['info_shtese'];
    $monetizuar = $_POST['monetizuar'];

    // Update the values in the database
    $query = "UPDATE facebook SET 
                emri_mbiemri = '$emri_mbiemri',
                emri_faqes = '$emri_faqes',
                dataKrijimit = '$data_krijimit',
                dataSkadimit = '$data_skadimit',
                linkuFaqes = '$linku_faqes',
                numriPersonal = '$numri_personal',
                adsAccount = '$ads_account',
                kategoria = '$kategoria',
                numriTelefonit = '$numri_telefonit',
                perqindja = '$perqindja',
                numriXhirollogarise = '$numri_xhirollogarise',
                adresa = '$adresa',
                infoShtese = '$info_shtese',
                monetizuar = '$monetizuar'
              WHERE id = $kid";

    if (mysqli_query($conn, $query)) {
        // The update was successful
        echo "Update successful!";
        $redirectURL = "facebook-account.php?kid=" . $kid;
        header("Location: " . $redirectURL);
        exit(); // Make sure to include this line after the redirect
    } else {
        echo "Update failed: " . mysqli_error($conn);
    }

}
?>