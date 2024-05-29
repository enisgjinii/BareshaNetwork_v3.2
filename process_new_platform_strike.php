<?php
header('Content-Type: application/json');
include 'conn-d.php';
try {
    // Kontrolloni nëse të gjithë parametrat e kërkuar POST janë vendosur
    if (!isset($_POST['platforma'], $_POST['titulli'], $_POST['pershkrimi'], $_POST['data_e_krijimit'], $_POST['email_used'])) {
        throw new Exception('Fushat e formularit të kërkuara mungojnë.');
    }
    // Caktoni parametrat POST te variablat
    $platforma = $_POST['platforma'];
    $titulli = $_POST['titulli'];
    $pershkrimi = $_POST['pershkrimi'];
    $data_e_krijimit = $_POST['data_e_krijimit'];
    $email_used = $_POST['email_used'];
    // Validoni të dhënat e hyrjes (validimi bazë)
    if (empty($platforma) || empty($titulli) || empty($pershkrimi) || empty($data_e_krijimit) || empty($email_used)) {
        throw new Exception('Të gjitha fushat e formularit duhet të plotësohen.');
    }
    // Përgatitni deklaratën SQL
    $stmt = $conn->prepare("INSERT INTO platforms (platforma, titulli, pershkrimi, data_e_krijimit,email_used ) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception('Deklarata e përgatitjes dështoi: ' . $conn->error);
    }
    // Lidhni parametrat me kërkesën SQL
    $stmt->bind_param("sssss", $platforma, $titulli, $pershkrimi, $data_e_krijimit, $email_used);
    // Ekzekutoni deklaratën
    if (!$stmt->execute()) {
        throw new Exception('Ekzekutimi i deklaratës dështoi: ' . $stmt->error);
    }
    // Ktheni përgjigjen e suksesit
    echo json_encode(['sukses' => true]);
} catch (Exception $e) {
    // Ktheni përgjigjen e gabimit
    echo json_encode(['sukses' => false, 'mesazhi' => $e->getMessage()]);
} finally {
    // Mbyllni deklaratën dhe lidhjen
    if (isset($stmt) && $stmt instanceof mysqli_stmt) {
        $stmt->close();
    }
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
