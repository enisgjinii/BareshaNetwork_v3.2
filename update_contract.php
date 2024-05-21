<?php
include 'conn-d.php';
$id = $_POST['id'];
$emri = $_POST['emri'];
$mbiemri = $_POST['mbiemri'];
$tvsh = $_POST['tvsh'];
$numri_personal = $_POST['numri_personal'];
$pronari_xhirollogarise = $_POST['pronari_xhirollogarise'];
$numri_xhirollogarise = $_POST['numri_xhirollogarise'];
$kodi_swift = $_POST['kodi_swift'];
$iban = $_POST['iban'];
$emri_bankes = $_POST['emri_bankes'];
$adresa_bankes = $_POST['adresa_bankes'];
$kohezgjatja = $_POST['kohezgjatja'];
$query = "UPDATE kontrata_gjenerale SET emri = '$emri', mbiemri = '$mbiemri', tvsh = '$tvsh', numri_personal = '$numri_personal', pronari_xhirollogarise = '$pronari_xhirollogarise', numri_xhirollogarise = '$numri_xhirollogarise', kodi_swift = '$kodi_swift', iban = '$iban', emri_bankes = '$emri_bankes', adresa_bankes = '$adresa_bankes', kohezgjatja = '$kohezgjatja' WHERE id = $id";
if (mysqli_query($conn, $query)) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error']);
}
