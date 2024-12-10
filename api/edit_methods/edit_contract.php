<?php
include '../../conn-d.php';

// Retrieve POST data directly without sanitization
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
$shenim = $_POST['shenim'];
$data_e_krijimit = $_POST['data_e_krijimit'];
$lloji_dokumentit = $_POST['lloji_dokumentit'];
// Prepare the SQL query without prepared statements
$query = "UPDATE kontrata_gjenerale 
          SET emri = '$emri', 
              mbiemri = '$mbiemri', 
              tvsh = '$tvsh', 
              numri_personal = '$numri_personal', 
              pronari_xhirollogarise = '$pronari_xhirollogarise', 
              numri_xhirollogarise = '$numri_xhirollogarise', 
              kodi_swift = '$kodi_swift', 
              iban = '$iban', 
              emri_bankes = '$emri_bankes', 
              adresa_bankes = '$adresa_bankes', 
              kohezgjatja = '$kohezgjatja', 
              shenim = '$shenim', 
              data_e_krijimit = '$data_e_krijimit',
              lloji_dokumentit = '$lloji_dokumentit'
          WHERE id = '$id'";

// Execute the query
if (mysqli_query($conn, $query)) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
}

// Close the connection
mysqli_close($conn);
