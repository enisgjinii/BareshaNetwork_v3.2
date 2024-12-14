<?php
session_start(); // Start the session to handle messages
include('conn-d.php');
ob_start(); // Start output buffering

// Load Composer's autoloader
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialize PHPMailer
$mail = new PHPMailer(true);

// SMTP Configuration
try {
    //Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'egjini@bareshamusic.com';
    $mail->Password   = 'lcgglivhzwmpuyui';
    $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587; // TCP port to connect to     // TCP port to connect to
    $mail->CharSet    = 'UTF-8';                                // Set email encoding
} catch (Exception $e) {
    // Log SMTP connection errors
    error_log("PHPMailer SMTP Connection Error: " . $mail->ErrorInfo);
    // Optionally, you can handle this error differently
}

// Define the recipient for error notifications
define('ADMIN_EMAIL', 'egjini17@gmail.com'); // Replace with your admin email
define('ADMIN_NAME', 'Admin');

// Sanitize and retrieve form inputs
$emri = isset($_POST['emri']) ? mysqli_real_escape_string($conn, trim($_POST['emri'])) : '';
$mbiemri = isset($_POST['mbiemri']) ? mysqli_real_escape_string($conn, trim($_POST['mbiemri'])) : '';
$numri_tel = isset($_POST['numri_tel']) ? mysqli_real_escape_string($conn, trim($_POST['numri_tel'])) : '';
$numri_personal = isset($_POST['numri_personal']) ? mysqli_real_escape_string($conn, trim($_POST['numri_personal'])) : '';
$artisti = isset($_POST['artisti']) ? mysqli_real_escape_string($conn, trim($_POST['artisti'])) : '';
$email = isset($_POST['email']) ? mysqli_real_escape_string($conn, trim($_POST['email'])) : '';
$youtube_id = isset($_POST['youtube_id']) ? mysqli_real_escape_string($conn, trim($_POST['youtube_id'])) : '';
$tvsh = isset($_POST['tvsh']) ? intval($_POST['tvsh']) : 0;
$pronari_xhiroBanka = isset($_POST['pronari_xhiroBanka']) ? mysqli_real_escape_string($conn, trim($_POST['pronari_xhiroBanka'])) : '';
$numri_xhiroBanka = isset($_POST['numri_xhiroBanka']) ? mysqli_real_escape_string($conn, trim($_POST['numri_xhiroBanka'])) : '';
$kodi_swift = isset($_POST['kodi_swift']) ? mysqli_real_escape_string($conn, trim($_POST['kodi_swift'])) : '';
$iban = isset($_POST['iban']) ? mysqli_real_escape_string($conn, trim($_POST['iban'])) : '';
$emri_bankes = isset($_POST['emri_bankes']) ? mysqli_real_escape_string($conn, trim($_POST['emri_bankes'])) : '';
$adresa_bankes = isset($_POST['adresa_bankes']) ? mysqli_real_escape_string($conn, trim($_POST['adresa_bankes'])) : '';
$shteti = isset($_POST['shteti']) ? mysqli_real_escape_string($conn, trim($_POST['shteti'])) : '';
$kohezgjatja = isset($_POST['kohezgjatja']) ? intval($_POST['kohezgjatja']) : 0;
$shenim = isset($_POST['shenim']) ? mysqli_real_escape_string($conn, trim($_POST['shenim'])) : '';
$lloji_dokumentit = isset($_POST['lloji_dokumentit']) ? mysqli_real_escape_string($conn, trim($_POST['lloji_dokumentit'])) : '';

// Generate invoice number
$currentYear = date('Y');
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
    'Nov' => 'Nën',
    'Dec' => 'Dhj'
];
$currentMonth = date('M');
$albanianMonthAbbreviation = isset($albanianMonths[$currentMonth]) ? $albanianMonths[$currentMonth] : '??';

do {
    $nextInvoiceNumber = mt_rand(1, 9999);
    $invoiceNumber = sprintf('BAR%s – %s-%04d', strtoupper($albanianMonthAbbreviation), $currentYear, $nextInvoiceNumber);
    $sql_check = "SELECT COUNT(*) AS count FROM kontrata_gjenerale WHERE id_kontrates = ?";
    $stmt_check = $conn->prepare($sql_check);
    if (!$stmt_check) {
        $errorMessage = "Gabim në përgatitjen e pyetjes: " . $conn->error;
        $_SESSION['error'] = $errorMessage;
        sendErrorEmail($emri, $mbiemri, $errorMessage);
        header("Location: kontrata_gjenerale_2.php");
        exit();
    }
    $stmt_check->bind_param("s", $invoiceNumber);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $row_check = $result_check->fetch_assoc();
    $count = $row_check['count'];
    $stmt_check->close();
} while ($count > 0);

// Get the current date in the 'Y-m-d' format
$currentDateFormatted = date('Y-m-d');

// Handle File Uploads
$uploadedFilePaths = [];
$uploadDirectory = 'uploads/documents/'; // Ensure this directory exists and is writable

if (!is_dir($uploadDirectory)) {
    if (!mkdir($uploadDirectory, 0755, true)) {
        $errorMessage = "Gabim në krijimin e dosjes së ngarkimit.";
        $_SESSION['error'] = $errorMessage;
        sendErrorEmail($emri, $mbiemri, $errorMessage);
        header("Location: kontrata_gjenerale_2.php");
        exit();
    }
}

if (isset($_FILES['documents']) && $_FILES['documents']['error'][0] != UPLOAD_ERR_NO_FILE) {
    $allowedTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/jpeg',
        'image/png'
    ];
    $maxSize = 5 * 1024 * 1024; // 5MB per file

    foreach ($_FILES['documents']['tmp_name'] as $key => $tmpName) {
        $fileName = basename($_FILES['documents']['name'][$key]);
        $fileSize = $_FILES['documents']['size'][$key];
        $fileType = mime_content_type($tmpName);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validate file type
        if (!in_array($fileType, $allowedTypes)) {
            $errorMessage = "Lloji i skedarit '$fileName' nuk është i lejuar.";
            $_SESSION['error'] = $errorMessage;
            sendErrorEmail($emri, $mbiemri, $errorMessage);
            header("Location: kontrata_gjenerale_2.php");
            exit();
        }

        // Validate file size
        if ($fileSize > $maxSize) {
            $errorMessage = "Skedari '$fileName' tejkalon madhësinë maksimale prej 5MB.";
            $_SESSION['error'] = $errorMessage;
            sendErrorEmail($emri, $mbiemri, $errorMessage);
            header("Location: kontrata_gjenerale_2.php");
            exit();
        }

        // Generate a unique file name to prevent overwriting
        $newFileName = uniqid() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $fileName);
        $targetFilePath = $uploadDirectory . $newFileName;

        // Move the file to the target directory
        if (move_uploaded_file($tmpName, $targetFilePath)) {
            $uploadedFilePaths[] = $targetFilePath;
        } else {
            $errorMessage = "Gabim gjatë ngarkimit të skedarit '$fileName'.";
            $_SESSION['error'] = $errorMessage;
            sendErrorEmail($emri, $mbiemri, $errorMessage);
            header("Location: kontrata_gjenerale_2.php");
            exit();
        }
    }

    // Encode the file paths as JSON for storage
    $documentPathsJson = json_encode($uploadedFilePaths);
} else {
    $documentPathsJson = NULL; // No documents uploaded
}

// Prepare the INSERT statement with parameter binding to prevent SQL injection
$sql_insert = "INSERT INTO kontrata_gjenerale (
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
    kohezgjatja,
    shenim,
    lloji_dokumentit
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt_insert = $conn->prepare($sql_insert);
if ($stmt_insert) {
    // Define the types for bind_param
    // s = string, i = integer, etc.
    // Adjust the types based on your database schema
    $stmt_insert->bind_param(
        "ssssssissssssssissss",
        $emri,
        $mbiemri,
        $invoiceNumber,
        $currentDateFormatted,
        $youtube_id,
        $artisti,
        $tvsh,
        $pronari_xhiroBanka,
        $numri_xhiroBanka,
        $kodi_swift,
        $iban,
        $emri_bankes,
        $adresa_bankes,
        $numri_tel,
        $numri_personal,
        $email,
        $shteti,
        $kohezgjatja,
        $shenim,
        $lloji_dokumentit
    );

    if ($stmt_insert->execute()) {
        // Success Message
        $_SESSION['success'] = 'Kontrata u krijua me sukses!';

        // Send Success Email
        sendSuccessEmail($emri, $mbiemri, $invoiceNumber, $email);

        header("Location: lista_kontratave_gjenerale.php");
        exit();
    } else {
        // Error during execution
        $errorMessage = "Gabim gjatë krijimit të kontratës: " . $stmt_insert->error;
        $_SESSION['error'] = $errorMessage;

        // Send Error Email
        sendErrorEmail($emri, $mbiemri, $errorMessage);

        header("Location: kontrata_gjenerale_2.php");
        exit();
    }

    $stmt_insert->close();
} else {
    // Error preparing statement
    $errorMessage = "Gabim në përgatitjen e pyetjes: " . $conn->error;
    $_SESSION['error'] = $errorMessage;

    // Send Error Email
    sendErrorEmail($emri, $mbiemri, $errorMessage);

    header("Location: kontrata_gjenerale_2.php");
    exit();
}

$conn->close();
ob_end_flush(); // Flush the output buffer and turn off output buffering

/**
 * Function to send success email
 */
function sendSuccessEmail($emri, $mbiemri, $invoiceNumber, $clientEmail)
{
    global $mail;

    try {
        //Recipients
        $mail->setFrom('egjini17@gmail.com', 'Your Company Name'); // Replace with your sender info
        $mail->addAddress(ADMIN_EMAIL, ADMIN_NAME);

        // Content
        $mail->isHTML(true);                                        // Set email format to HTML
        $mail->Subject = 'Kontrata Juaj u Krijua me Sukses';
        $mail->Body    = "
            <h2>Përshëndetje, $emri $mbiemri!</h2>
            <p>Kontrata juaj me numër <strong>$invoiceNumber</strong> u krijua me sukses në sistemin tonë.</p>
            <p>Ju lutemi kontrolloni detajet në seksionin tuaj të klientit.</p>
            <p>Faleminderit për besimin tuaj!</p>
            <hr>
            <p>Me respekt,<br>Your Company Name</p>
        ";
        $mail->AltBody = "Përshëndetje, $emri $mbiemri!\n\nKontrata juaj me numër $invoiceNumber u krijua me sukses në sistemin tonë.\n\nJu lutemi kontrolloni detajet në seksionin tuaj të klientit.\n\nFaleminderit për besimin tuaj!\n\nMe respekt,\nYour Company Name";

        $mail->send();
    } catch (Exception $e) {
        // Log email sending errors
        error_log("PHPMailer Error (Success Email): " . $mail->ErrorInfo);
    }
}

/**
 * Function to send error email
 */
function sendErrorEmail($emri, $mbiemri, $errorMessage)
{
    global $mail;

    try {
        //Recipients
        $mail->setFrom('egjini17@gmail.com', 'Your Company Name'); // Replace with your sender info
        $mail->addAddress(ADMIN_EMAIL, ADMIN_NAME);                  // Admin recipient

        // Content
        $mail->isHTML(true);                                        // Set email format to HTML
        $mail->Subject = 'Gabim gjatë Krijimit të Kontratës';
        $mail->Body    = "
            <h2>Gabim Gjatë Krijimit të Kontratës</h2>
            <p><strong>Klienti:</strong> $emri $mbiemri</p>
            <p><strong>Gabimi:</strong> $errorMessage</p>
            <p>Ju lutemi kontrolloni sistemin tuaj për detaje të mëtejshme.</p>
            <hr>
            <p>Me respekt,<br>Your System</p>
        ";
        $mail->AltBody = "Gabim Gjatë Krijimit të Kontratës\n\nKlienti: $emri $mbiemri\nGabimi: $errorMessage\n\nJu lutemi kontrolloni sistemin tuaj për detaje të mëtejshme.\n\nMe respekt,\nYour System";

        $mail->send();
    } catch (Exception $e) {
        // Log email sending errors
        error_log("PHPMailer Error (Error Email): " . $mail->ErrorInfo);
    }
}
