<?php
// Database connection
include 'conn-d.php';
// Get form data
$emri_ofertes = $_POST['emri_ofertes'];
$emri_klientit = $_POST['emri_klientit'];
$koh&euml;zgjatja = $_POST['koh&euml;zgjatja'];
$p&euml;rshkrimi_ofertes = $_POST['pershkrimi_ofertes'];
$dataAktuale = $_POST['dataAktuale'];
// Prepare SQL statement
$sql = "INSERT INTO ofertat (emri_ofertes, klienti, kohezgjatja,pershkrimi_ofertes,data) VALUES (?,?,?,?,?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $emri_ofertes, $emri_klientit, $koh&euml;zgjatja, $p&euml;rshkrimi_ofertes, $dataAktuale);
$stmt->execute();
// Close database connection and redirect to success page
$stmt->close();
$conn->close();
header("Location: ofertat.php");
exit();
?>