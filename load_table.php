<?php
// Connect to the database
include 'conn-d.php';

// Pagination variables
$results_per_page = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start_index = ($page - 1) * $results_per_page;

// Your query to fetch data with pagination
$faturatQuery = "SELECT * FROM fatura ORDER BY id DESC LIMIT $start_index, $results_per_page";
$faturatResult = $conn->query($faturatQuery);

// Output the table content
while ($fatura = mysqli_fetch_array($faturatResult)) {
    $emriQuery = "SELECT * FROM klientet WHERE id='$fatura[emri]'";
    $emriResult = $conn->query($emriQuery);
    $emri = mysqli_fetch_array($emriResult);
    $emriFull = $emri['emri'];

    $idKanalitQuery = "SELECT * FROM yinc WHERE kanali='$fatura[emri]'";
    $idKanalitResult = $conn->query($idKanalitQuery);
    $idKanalit = mysqli_fetch_array($idKanalitResult);

    $get_invoice_id = $fatura['fatura'];

    $shitjeTotaliQuery = "SELECT SUM(totali) as sum FROM shitje WHERE fatura='$get_invoice_id'";
    $shitjeTotaliResult = $conn->query($shitjeTotaliQuery);
    $shitjeTotali = mysqli_fetch_array($shitjeTotaliResult);

    echo '<tr>';
    echo '<td>' . $emriFull . '</td>';
    echo '<td>' . $emri['emriart'] . '</td>';
    echo '<td>' . $fatura['fatura'] . '</td>';
    echo '<td>' . $fatura['data'] . '</td>';
    echo '<td>' . $shitjeTotali['sum'] . '</td>';
    echo '</tr>';
}

// Generate and output pagination links
$paginationQuery = "SELECT COUNT(*) AS total FROM fatura";
$paginationResult = $conn->query($paginationQuery);
$paginationData = mysqli_fetch_assoc($paginationResult);
$total_records = $paginationData['total'];
$total_pages = ceil($total_records / $results_per_page);

echo '<div class="pagination">';
if ($page > 1) {
    echo '<a href="?page=1">First</a>';
    echo '<a href="?page=' . ($page - 1) . '">Previous</a>';
}

for ($i = 1; $i <= $total_pages; $i++) {
    if ($i == $page) {
        echo '<a class="active" href="?page=' . $i . '">' . $i . '</a>';
    } else {
        echo '<a href="?page=' . $i . '">' . $i . '</a>';
    }
}

if ($page < $total_pages) {
    echo '<a href="?page=' . ($page + 1) . '">Next</a>';
}
echo '</div>';

// Close the database connection
$conn->close();
