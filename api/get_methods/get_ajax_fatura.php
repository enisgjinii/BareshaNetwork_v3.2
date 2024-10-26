<?php
include('../../conn-d.php');

$data_array = [];
$query = "SELECT f.*, 
                 COALESCE(s.total_sales, 0) as total_sales, 
                 COALESCE(p.total_payments, 0) as total_payments
          FROM fatura f
          LEFT JOIN (
              SELECT fatura, SUM(totali) as total_sales 
              FROM shitje 
              GROUP BY fatura
          ) s ON f.fatura = s.fatura
          LEFT JOIN (
              SELECT fatura, SUM(shuma) as total_payments 
              FROM pagesat 
              GROUP BY fatura
          ) p ON f.fatura = p.fatura";

$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $shuma = $row['total_sales'];
        $shuma_e_paguar = $row['total_payments'];
        $obli = $shuma - $shuma_e_paguar;

        if ($obli > 0) {
            // Assuming 'emriartikullit' should come from a related table. 
            // If not, you might need to adjust this based on your database schema.
            // For now, it's set to 'emrifull' as per original code, but consider revising.
            $data_array[] = [
                'emrifull' => htmlspecialchars($row['emrifull']),
                'emriartikullit' => htmlspecialchars($row['emrifull']), // TODO: Update if necessary
                'fatura' => htmlspecialchars($row['fatura']) . "<br><br><button class='btn btn-primary open-modal text-white rounded-5 shadow-sm'><i class='fi fi-rr-badge-dollar fa-lg'></i></button>",
                'data' => htmlspecialchars($row['data']),
                'shuma' => number_format($shuma, 2, ',', '.'),
                'shuma_e_paguar' => number_format($shuma_e_paguar, 2, ',', '.'),
                'obli' => number_format($obli, 2, ',', '.'),
                'aksion' => "<a class='btn btn-primary btn-sm py-2' href='shitje.php?fatura={$row['fatura']}' target='_blank'><i class='fi fi-rr-edit'></i></a> 
                             <a class='btn btn-success btn-sm py-2' target='_blank' href='fatura.php?invoice={$row['fatura']}'><i class='fi fi-rr-print'></i></a> 
                             <a type='button' name='delete' class='btn btn-danger btn-xs delete py-2' id='{$row['fatura']}'><i class='fi fi-rr-trash'></i></a>"
            ];
        }
    }

    $stmt->close();
} else {
    // Handle query preparation error
    echo json_encode(['data' => [], 'error' => $conn->error]);
    exit;
}

// Removed the extra dot before the semicolon
echo json_encode(['data' => $data_array]);
