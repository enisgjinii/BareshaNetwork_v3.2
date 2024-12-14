<?php
// Kontrolloni nëse forma është paraqitur
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validoni dhe pastrojini hyrjet
    $row_id = filter_input(INPUT_POST, 'row_id', FILTER_SANITIZE_NUMBER_INT);
    $email_ads_edit = sanitize_input($_POST['email_ads_edit']);
    $adsID_edit = sanitize_input($_POST['adsID_edit']);
    $shteti_edit = sanitize_input($_POST['shteti_edit']);

    // Kontrolloni nëse të gjitha fushat e nevojshme janë dhënë
    if (!$row_id || !$email_ads_edit || !$adsID_edit || !$shteti_edit) {
        echo "Të gjitha fushat janë të domosdoshme.";
        exit;
    }

    // Lidhuni me bazën e të dhënave dhe përgatitni pyetjen SQL
    include '../../conn-d.php';

    $sql = "UPDATE facebook_ads SET email = ?, ads_id = ?, shteti = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    // Kontrolloni nëse ka ndonjë gabim në përgatitjen e deklaratës së përgatitur
    if (!$stmt) {
        echo "Gabim: " . mysqli_error($conn);
        exit;
    }

    // Lidhni parametrat
    mysqli_stmt_bind_param($stmt, "sssi", $email_ads_edit, $adsID_edit, $shteti_edit, $row_id);

    // Ekzekutoni deklaratën e përgatitur
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        header("Location: vegla_facebook.php");
        exit;
    } else {
        echo "Gabim: " . mysqli_error($conn);
    }
} else {
    echo "Kërkesë e pavlefshme.";
}

// Funksioni për pastërtimin dhe validimin e stringjeve
function sanitize_input($input)
{
    // Pastrojeni stringun nga karakteret e panevojshme dhe pastaj kthejeni
    return preg_replace('/[^A-Za-z0-9\s\-]/', '', trim($input));
}
