<?php
include 'conn-d.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $registruesi = $_POST['recipient-name'];
    $pershkrimi = $_POST['message'];
    $shuma = $_POST['amount'];

    // Kontrolloni nëse ID-ja është e vlefshme
    if (!is_numeric($id) || $id <= 0) {
        echo "ID jovalide. Ju lutem jepni një ID të vlefshme.";
        exit;
    }

    // Përdorni përgatitjen e deklaratës për të parandaluar sulmet SQL Injection
    $query = $conn->prepare("UPDATE expenses SET registruesi=?, pershkrimi=?, shuma=? WHERE id=?");
    $query->bind_param("sssi", $registruesi, $pershkrimi, $shuma, $id);

    // Ekzekutoni deklaratën dhe kontrolloni për gabime
    if ($query->execute()) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    } else {
        // Kapni dhe shfaqni gabimin
        echo "Gabim gjatë përditësimit: " . $query->error;
        exit;
    }
}
