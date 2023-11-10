<?php
// Include the required database connection and query code
include 'conn-d.php';
$startMicrotime = microtime(true);

// Define pagination parameters
$start = isset($_POST['start']) ? $_POST['start'] : 0;
$length = isset($_POST['length']) ? $_POST['length'] : 5; // Default page length

// Construct the base query
$query = "SELECT f.fatura, f.data, k.id AS klient_id, k.emri AS klient_emri, k.emriart AS klient_emriart, 
    IFNULL(s.totali, 0) AS shitje_totali, IFNULL(p.shuma, 0) AS pagesa_shuma
    FROM fatura AS f
    LEFT JOIN klientet AS k ON f.emri = k.id
    LEFT JOIN (SELECT fatura, SUM(totali) AS totali FROM shitje GROUP BY fatura) AS s ON f.fatura = s.fatura
    LEFT JOIN pagesat AS p ON f.fatura = p.fatura";

// Add search criteria if provided
$searchTerm = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
if (!empty($searchTerm)) {
    $query .= " WHERE f.fatura LIKE '%$searchTerm%' OR k.emri LIKE '%$searchTerm%' OR k.emriart LIKE '%$searchTerm%'";
}

$query .= " ORDER BY f.id DESC"; // No need to limit records here

// Execute the query
$result = $conn->query($query);

$filteredData = []; // Array to store filtered and consolidated invoice data

do {
    $row = mysqli_fetch_assoc($result);
    if ($row) {
        $invoice_fatura = $row['fatura'];
        $shitje_totali = $row['shitje_totali'];
        $pagesa_shuma = $row['pagesa_shuma'];
        $obligim = $shitje_totali - $pagesa_shuma;

        // Fetch data from yinc
        $borgjeTotal = 0;
        $yinc_query = "SELECT shuma, pagoi FROM yinc WHERE kanali = '{$row['klient_id']}'";
        $yinc_result = $conn->query($yinc_query);

        while ($yinc_row = mysqli_fetch_assoc($yinc_result)) {
            $amount = $yinc_row['shuma'] - $yinc_row['pagoi'];
            if ($amount > 0) {
                $borgjeTotal += $amount;
            }
        }

        // Update the yinc data
        $row['yinc_data'] = $borgjeTotal;

        if ($obligim > 0) {
            if (isset($filteredData[$invoice_fatura])) {
                // Update existing invoice data
                $filteredData[$invoice_fatura]['shitje_totali'] += $shitje_totali;
                $filteredData[$invoice_fatura]['pagesa_shuma'] += $pagesa_shuma;
            } else {
                // Add new entry for this invoice
                $filteredData[$invoice_fatura] = [
                    'fatura' => $invoice_fatura,
                    'shitje_totali' => $shitje_totali,
                    'pagesa_shuma' => $pagesa_shuma,
                    'klient_emri' => $row['klient_emri'],
                    'klient_emriart' => $row['klient_emriart'],
                    'data' => $row['data'],
                    'yinc_data' => $row['yinc_data'] ?? ''
                ];
            }
        }
    }
} while ($row); // Convert filtered data to DataTables format

$data = array(); // Initialize an array to store modified data

$filteredDataKeys = array_keys($filteredData); // Get the keys of the $filteredData array

for ($i = 0; $i < count($filteredDataKeys); $i++) {
    $invoice = $filteredDataKeys[$i];
    $row = $filteredData[$invoice];

    $obligim = $row['shitje_totali'] - $row['pagesa_shuma'];
    $row['obligim'] = $obligim;

    $actions = '<a class="btn btn-primary btn-sm rounded-5 text-white" href="fatura_details.php?invoice_fatura=' . $invoice . '">Detaje</a>'
        . '<a class="btn btn-primary btn-sm rounded-5 shadow-sm text-white" href="shitje.php?fatura=' . $invoice . '" target="_blank"><i class="fi fi-rr-edit"></i></a>'
        . '<a class="btn btn-success btn-sm rounded-5 shadow-sm text-white" target="_blank" href="fatura.php?invoice=' . $invoice . '"><i class="fi fi-rr-print"></i></a>'
        . '<a class="btn btn-danger btn-sm rounded-5 shadow-sm text-white delete" name="delete" id="' . $invoice . '"><i class="fi fi-rr-trash"></i></a>';

    $row['actions'] = $actions;

    $data[] = $row;
}

// Slice the data for the current page
$pagedData = array_slice($data, $start, $length);

// Update total and filtered records count
$totalRecords = count($filteredData);
$filteredRecords = $totalRecords;

// Measure execution time
$executionTime = microtime(true) - $startMicrotime;

// Slice the data for the current page
$pagedData = array_slice($data, $start, $length);

// Update total and filtered records count
$totalRecords = count($filteredData);
$filteredRecords = $totalRecords;

// Send JSON response
header('Content-Type: application/json');
echo json_encode([
    'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $filteredRecords,
    'data' => $pagedData,
    'executionTime' => $executionTime // Include execution time in the response
]);
