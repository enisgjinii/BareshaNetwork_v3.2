<?php
include('conn-d.php');
ob_start(); // Start output buffering

$emri = isset($_POST['emri']) ? $_POST['emri'] : '';
$mbiemri = isset($_POST['mbiemri']) ? $_POST['mbiemri'] : '';
$numri_tel = isset($_POST['numri_tel']) ? $_POST['numri_tel'] : '';
$numri_personal = isset($_POST['numri_personal']) ? $_POST['numri_personal'] : '';
$artisti = isset($_POST['artisti']) ? $_POST['artisti'] : '';
$adresa_emailit = isset($_POST['emailadresa']) ? $_POST['emailadresa'] : '';
$youtube_id = isset($_POST['youtube_id']) ? $_POST['youtube_id'] : '';

// $vat = isset($_POST['vat']) ? $_POST['vat'] : '';
$tvsh = isset($_POST['tvsh']) ? $_POST['tvsh'] : '';
$pronari_xhiroBanka = isset($_POST['pronari_xhiroBanka']) ? $_POST['pronari_xhiroBanka'] : '';
$numri_xhiroBanka = isset($_POST['numri_xhiroBanka']) ? $_POST['numri_xhiroBanka'] : '';
$kodi_swift = isset($_POST['kodi_swift']) ? $_POST['kodi_swift'] : '';
$iban = isset($_POST['iban']) ? $_POST['iban'] : '';
$emri_bankes = isset($_POST['emri_bankes']) ? $_POST['emri_bankes'] : '';
$adresa_bankes = isset($_POST['adresa_bankes']) ? $_POST['adresa_bankes'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$selectedCountry = isset($_POST['shteti']) ? $_POST['shteti'] : '';
$kohezgjatja = isset($_POST['kohezgjatja']) ? $_POST['kohezgjatja'] : '';

// Get the current year
$currentYear = date('Y');

// Get the current month abbreviation in Albanian
$albanianMonths = [
    'Jan' => 'Jan',
    'Feb' => 'Shk',
    'Mar' => 'Mar',
    'Apr' => 'Pri',
    'May' => 'Maj',
    'Jun' => 'Qer',
    'Jul' => 'Kor',
    'Aug' => 'Gsh',
    'Sep' => 'Sht',
    'Oct' => 'Tet',
    'Nov' => 'N&euml;n',
    'Dec' => 'Dhj'
];
$currentMonth = date('M');
$albanianMonthAbbreviation = $albanianMonths[$currentMonth];

// Generate a random number
$nextInvoiceNumber = mt_rand(1, 9999);

// Check if the random number already exists in the database
$sql = "SELECT COUNT(*) AS count FROM kontrata_gjenerale WHERE id_kontrates LIKE '%$nextInvoiceNumber'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$count = $row['count'];

// Generate a new random number if the current one already exists
while ($count > 0) {
    $nextInvoiceNumber = mt_rand(1, 9999);
    $sql = "SELECT COUNT(*) AS count FROM kontrata_gjenerale WHERE id_kontrates LIKE '%$nextInvoiceNumber'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $count = $row['count'];
}

// Generate the invoice number with leading zeros and uppercase "BAR"
$invoiceNumber = sprintf('BAR%s – %s-%04d', strtoupper($albanianMonthAbbreviation), $currentYear, $nextInvoiceNumber);


// Get the current date in the 'd/m/Y' format
$currentDate = date('d/m/Y');

// Rearrange the date format to 'Y-m-d' for database insertion
list($day, $month, $year) = explode('/', $currentDate);
$currentDateFormatted = $year . '-' . $month . '-' . $day;
$sql = "INSERT INTO kontrata_gjenerale (
    emri, 
    mbiemri, 
    id_kontrates, 
    data_e_krijimit, 
    youtube_id, 
    artisti, 
    tvsh,
    pronari_xhirollogarise,
    numri_xhirollogarise,
    kodi_swift,
    iban,
    emri_bankes,
    adresa_bankes,
    numri_tel,
    numri_personal,
    email,
    shteti,
    kohezgjatja
    ) VALUES ('$emri', '$mbiemri', '$invoiceNumber', '$currentDateFormatted','$youtube_id', '$artisti', '$tvsh','$pronari_xhiroBanka','$numri_xhiroBanka','$kodi_swift','$iban','$emri_bankes','$adresa_bankes','$numri_tel','$numri_personal','$email','$selectedCountry','$kohezgjatja')";

$result = mysqli_query($conn, $sql);
if ($result) {
    echo '<script>window.location.href = "lista_kontratave_gjenerale.php"</script>';
} else {
    echo '<script>alert("There was an error submitting the signature");</script>';
}

ob_end_flush(); // Flush the output buffer and turn off output buffering
