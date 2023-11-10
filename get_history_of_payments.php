<?php

$draw = $_POST['draw'];
$start = $_POST['start'];
$length = $_POST['length'];
$search = $_POST['search']['value'];

include('conn-d.php');

$sql = "SELECT *, (SELECT SUM(payment_amount) FROM payments p2 WHERE p2.invoice_id = payments.invoice_id) AS total_payment_amount
        FROM payments
        ORDER BY payment_id DESC";

$result = mysqli_query($conn, $sql);

$totalRecords = mysqli_num_rows($result);

if ($search !== "") {
    $sql .= " WHERE customer_id LIKE '%$search%' OR invoice_id LIKE '%$search%'";
    $result = mysqli_query($conn, $sql);
}

$filteredRecords = mysqli_num_rows($result);

$sql .= " LIMIT $start, $length";

$result = mysqli_query($conn, $sql);

$data = array();

while ($row = mysqli_fetch_assoc($result)) {
    $invoice_id = $row["invoice_id"];
    $sql2 = "SELECT customer_id, invoice_number FROM invoices WHERE id = $invoice_id";
    $result2 = mysqli_query($conn, $sql2);

    if ($result2 && mysqli_num_rows($result2) > 0) {
        $row2 = mysqli_fetch_assoc($result2);
        $customer_name = $row2["customer_id"];

        $sql3 = "SELECT * FROM klientet WHERE id = $customer_name";
        $result3 = mysqli_query($conn, $sql3);

        if ($result3 && mysqli_num_rows($result3) > 0) {
            $row3 = mysqli_fetch_assoc($result3);
            $data[] = array(
                $row3["emri"],
                $row2["invoice_number"],
                $row["invoice_id"],
                $row["payment_amount"],
                $row["payment_date"]
            );
        }
    }
}

mysqli_close($conn);

$response = array(
    "draw" => intval($draw),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $filteredRecords,
    "data" => $data
);

echo json_encode($response);
