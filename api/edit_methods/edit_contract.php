<?php
include '../../conn-d.php';

// Sanitize and validate input
$id = intval($_POST['id']); // Make sure id is an integer
$emri = htmlspecialchars(trim($_POST['emri']), ENT_QUOTES, 'UTF-8');
$mbiemri = htmlspecialchars(trim($_POST['mbiemri']), ENT_QUOTES, 'UTF-8');
$tvsh = htmlspecialchars(trim($_POST['tvsh']), ENT_QUOTES, 'UTF-8');
$numri_personal = htmlspecialchars(trim($_POST['numri_personal']), ENT_QUOTES, 'UTF-8');
$pronari_xhirollogarise = htmlspecialchars(trim($_POST['pronari_xhirollogarise']), ENT_QUOTES, 'UTF-8');
$numri_xhirollogarise = htmlspecialchars(trim($_POST['numri_xhirollogarise']), ENT_QUOTES, 'UTF-8');
$kodi_swift = htmlspecialchars(trim($_POST['kodi_swift']), ENT_QUOTES, 'UTF-8');
$iban = htmlspecialchars(trim($_POST['iban']), ENT_QUOTES, 'UTF-8');
$emri_bankes = htmlspecialchars(trim($_POST['emri_bankes']), ENT_QUOTES, 'UTF-8');
$adresa_bankes = htmlspecialchars(trim($_POST['adresa_bankes']), ENT_QUOTES, 'UTF-8');
$kohezgjatja = htmlspecialchars(trim($_POST['kohezgjatja']), ENT_QUOTES, 'UTF-8');

// Prepare the SQL query using prepared statements
$query = "UPDATE kontrata_gjenerale 
          SET emri = ?, mbiemri = ?, tvsh = ?, numri_personal = ?, pronari_xhirollogarise = ?, 
              numri_xhirollogarise = ?, kodi_swift = ?, iban = ?, emri_bankes = ?, adresa_bankes = ?, 
              kohezgjatja = ?
          WHERE id = ?";

// Initialize the prepared statement
if ($stmt = mysqli_prepare($conn, $query)) {
    // Bind the parameters to the prepared statement
    mysqli_stmt_bind_param($stmt, "sssssssssssi", $emri, $mbiemri, $tvsh, $numri_personal, $pronari_xhirollogarise, $numri_xhirollogarise, $kodi_swift, $iban, $emri_bankes, $adresa_bankes, $kohezgjatja, $id);

    // Execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }

    // Close the statement
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['status' => 'error']);
}
