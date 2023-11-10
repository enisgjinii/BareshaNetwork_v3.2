<?php

include '../conn-d.php';

if (isset($_POST['btn_save'])) {
    $fatura = $_POST['id_of_fatura'];
    $shuma = $_POST['shuma'];
    $data = $_POST['data'];
    $menyra = $_POST['menyra'];
    $pershkrimi = $_POST['pershkrimi'];

    // Check if the checkbox values are set and create a serialized string
    $kategorizimi = !empty($_POST['kategorizimi']) ? serialize($_POST['kategorizimi']) : serialize(['Ska']);

    if ($conn->query("INSERT INTO pagesat (fatura, shuma, menyra, data, pershkrimi, kategoria) VALUES ('$fatura', '$shuma', '$menyra', '$data', '$pershkrimi', '" . mysqli_real_escape_string($conn, $kategorizimi) . "')")) {
        echo 'Pagesa e fatur&euml;s ' . $fatura . ' u ruajt me sukses&euml;';
    } else {
        echo "Pagesa d&euml;shtoj";
    }
}
