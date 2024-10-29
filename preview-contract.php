<?php
// preview-contract.php

// Set the appropriate header for HTML content
header('Content-Type: text/html; charset=utf-8');

// Function to sanitize and escape output
function sanitize_output($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Initialize variables with default values
$emri = $mbiemri = $numri_tel = $numri_personal = $klienti = $email = $emriartistik = $vepra = $data = $perqindja = $perqindja_other = $shenime = "";

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and assign POST data
    $emri = sanitize_output($_POST['emri'] ?? '');
    $mbiemri = sanitize_output($_POST['mbiemri'] ?? '');
    $numri_tel = sanitize_output($_POST['numri_tel'] ?? '');
    $numri_personal = sanitize_output($_POST['numri_personal'] ?? '');
    $klienti = sanitize_output($_POST['klienti'] ?? '');
    $email = sanitize_output($_POST['email'] ?? '');
    $emriartistik = sanitize_output($_POST['emriartistik'] ?? '');
    $vepra = sanitize_output($_POST['vepra'] ?? '');
    $data = sanitize_output($_POST['data'] ?? '');
    $perqindja = sanitize_output($_POST['perqindja'] ?? '');
    $perqindja_other = sanitize_output($_POST['perqindja_other'] ?? '');
    $shenime = sanitize_output($_POST['shenime'] ?? '');
}

// Function to format the date
function format_date($date_str)
{
    if (empty($date_str)) return 'N/A';
    $date = DateTime::createFromFormat('Y-m-d', $date_str);
    if ($date) {
        return $date->format('d/m/Y');
    }
    return 'N/A';
}

// Extract client details if 'klienti' is in the format "emri|email|emriartistik"
$klienti_name = $klienti_email = $klienti_emriartistik = '';
if (!empty($klienti)) {
    $klienti_parts = explode('|', $klienti);
    $klienti_name = sanitize_output($klienti_parts[0] ?? '');
    $klienti_email = sanitize_output($klienti_parts[1] ?? '');
    $klienti_emriartistik = sanitize_output($klienti_parts[2] ?? '');
}
?>

<div class="contract-preview">
    <h3 class="text-center mb-4">Preview i Kontratës</h3>
    <div class="card p-4">
        <h5 class="mb-3">Informacioni Personal</h5>
        <table class="table table-bordered">
            <tr>
                <th>Emri</th>
                <td><?php echo $emri ?: 'N/A'; ?></td>
            </tr>
            <tr>
                <th>Mbiemri</th>
                <td><?php echo $mbiemri ?: 'N/A'; ?></td>
            </tr>
            <tr>
                <th>Numri i Telefonit</th>
                <td><?php echo $numri_tel ?: 'N/A'; ?></td>
            </tr>
            <tr>
                <th>Numri Personal</th>
                <td><?php echo $numri_personal ?: 'N/A'; ?></td>
            </tr>
        </table>

        <h5 class="mt-4 mb-3">Informacioni i Klientit</h5>
        <table class="table table-bordered">
            <tr>
                <th>Emri i Klientit</th>
                <td><?php echo $klienti_name ?: 'N/A'; ?></td>
            </tr>
            <tr>
                <th>Adresa e Email-it të Klientit</th>
                <td><?php echo $klienti_email ?: 'N/A'; ?></td>
            </tr>
            <tr>
                <th>Emri Artistik i Klientit</th>
                <td><?php echo $klienti_emriartistik ?: 'N/A'; ?></td>
            </tr>
        </table>

        <h5 class="mt-4 mb-3">Detajet e Kontratës</h5>
        <table class="table table-bordered">
            <tr>
                <th>Vepra</th>
                <td><?php echo $vepra ?: 'N/A'; ?></td>
            </tr>
            <tr>
                <th>Data</th>
                <td><?php echo format_date($data); ?></td>
            </tr>
            <tr>
                <th>Përqindja (Baresha)</th>
                <td><?php echo is_numeric($perqindja) ? number_format((float)$perqindja, 2) . '%' : 'N/A'; ?></td>
            </tr>
            <tr>
                <th>Përqindja (Klienti)</th>
                <td><?php echo is_numeric($perqindja_other) ? number_format((float)$perqindja_other, 2) . '%' : 'N/A'; ?></td>
            </tr>
            <tr>
                <th>Shënime</th>
                <td><?php echo nl2br($shenime) ?: 'N/A'; ?></td>
            </tr>
        </table>

        <?php if (!empty($email)): ?>
            <h5 class="mt-4 mb-3">Kontakti</h5>
            <p><strong>Email:</strong> <?php echo $email; ?></p>
        <?php endif; ?>

        <?php if (!empty($emriartistik)): ?>
            <p><strong>Emri Artistik:</strong> <?php echo $emriartistik; ?></p>
        <?php endif; ?>
    </div>
</div>

<!-- Optional: Add some basic styling for the preview -->
<style>
    .contract-preview {
        font-family: Arial, sans-serif;
    }

    .contract-preview .card {
        background-color: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 10px;
    }

    .contract-preview h3 {
        color: #0d6efd;
    }

    .contract-preview table th {
        width: 30%;
        background-color: #f8f9fa;
    }

    .contract-preview table td {
        width: 70%;
    }
</style>