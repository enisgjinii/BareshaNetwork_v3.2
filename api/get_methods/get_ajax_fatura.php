<?php
include('../../conn-d.php');

// Initialize an empty array to store the data
$data_array = [];

// Optimized SQL query
$query = "
    SELECT 
        f.emrifull, w
        f.fatura, 
        f.data, 
        COALESCE(s.total_sales, 0) AS total_sales, 
        COALESCE(p.total_payments, 0) AS total_payments,
        COALESCE(s.total_sales, 0) - COALESCE(p.total_payments, 0) AS obli
    FROM fatura f
    LEFT JOIN (
        SELECT fatura, SUM(totali) AS total_sales 
        FROM shitje 
        GROUP BY fatura
    ) s ON f.fatura = s.fatura
    LEFT JOIN (
        SELECT fatura, SUM(shuma) AS total_payments 
        FROM pagesat 
        GROUP BY fatura
    ) p ON f.fatura = p.fatura
    WHERE COALESCE(s.total_sales, 0) - COALESCE(p.total_payments, 0) > 0
";

// Prepare and execute the SQL statement
$stmt = $conn->prepare($query);
if (!$stmt) {
    // Handle errors with preparing the statement
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}

$stmt->execute();

// Fetch the result set
$result = $stmt->get_result();

// Iterate through each row in the result set
while ($row = $result->fetch_assoc()) {
    $shuma = $row['total_sales'];
    $shuma_e_paguar = $row['total_payments'];
    $obli = $row['obli']; // Already calculated in SQL

    // Build the data array
    $data_array[] = [
        'emrifull' => htmlspecialchars($row['emrifull'], ENT_QUOTES, 'UTF-8'),
        'emriartikullit' => htmlspecialchars($row['emrifull'], ENT_QUOTES, 'UTF-8'), // Verify if this should be a different field
        'fatura' => htmlspecialchars($row['fatura'], ENT_QUOTES, 'UTF-8') . "<br><br>
            <button class='input-custom-css px-3 py-2 open-modal rounded-5 shadow-sm'>
                <i class='fi fi-rr-badge-dollar fa-lg'></i>
            </button>",
        'data' => htmlspecialchars($row['data'], ENT_QUOTES, 'UTF-8'),
        'shuma' => number_format($shuma, 2, ',', '.'), // Format numbers as needed
        'shuma_e_paguar' => number_format($shuma_e_paguar, 2, ',', '.'),
        'obli' => number_format($obli, 2, ',', '.'),
        'aksion' => "
            <a class='input-custom-css px-3 py-2' style='text-decoration:none' href='shitje.php?fatura=" . urlencode($row['fatura']) . "' target='_blank'>
                <i class='fi fi-rr-edit'></i>
            </a> 
            <a class='input-custom-css px-3 py-2' style='text-decoration:none' target='_blank' href='fatura.php?invoice=" . urlencode($row['fatura']) . "'>
                <i class='fi fi-rr-print'></i>
            </a> 
            <a type='button' name='delete' class='input-custom-css px-3 py-2 delete py-2' id='" . htmlspecialchars($row['fatura'], ENT_QUOTES, 'UTF-8') . "' style='text-decoration:none'>
                <i class='fi fi-rr-trash'></i>
            </a>"
    ];
}

// Free the result set and close the statement
$result->free();
$stmt->close();

// Close the database connection
$conn->close();

// Output the JSON-encoded data
echo json_encode(['data' => $data_array]);
