<?php
// Include your database connection and other required configurations
include 'conn-d.php'; // Update with your actual database connection file

$results_per_page = isset($_GET['results_per_page']) ? $_GET['results_per_page'] : 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start_index = ($page - 1) * $results_per_page;

// Fetch data with pagination
$faturatQuery = "
    SELECT f.*, 
           COALESCE(SUM(s.totali), 0) AS shitje_totali_sum,
           COALESCE(SUM(p.shuma), 0) AS pagesa_sum
    FROM fatura f
    LEFT JOIN shitje s ON f.fatura = s.fatura
    LEFT JOIN pagesat p ON f.fatura = p.fatura
    GROUP BY f.id
    HAVING (shitje_totali_sum - pagesa_sum) > 0
    ORDER BY f.id DESC
    LIMIT $start_index, $results_per_page
";
$faturatResult = $conn->query($faturatQuery);
ob_start(); // Start buffering output

$faturatData = mysqli_fetch_all($faturatResult, MYSQLI_ASSOC);

// Calculate $obli values outside the loop
$obliArray = array();

for ($i = 0; $i < count($faturatData); $i++) {
    $fatura = $faturatData[$i];
    $obliArray[$i] = $fatura['shitje_totali_sum'] - $fatura['pagesa_sum'];
}

?>
<div class="p-3 shadow-sm rounded-5 mb-4 card m-0">
    <table class="table table-bordered caption-top">
        <thead class="table-dark">
            <tr>
                <th>Emri</th>
                <th>Emri aristik</th>
                <th>Borgji</th>
                <th>Fatura</th>
                <th>Data</th>
                <th>Shuma</th>
                <th>Shuma e paguar</th>
                <th>Obligim</th>
                <th>Aksion</th>
            </tr>
        </thead>
        <?php
        $faturaRow = mysqli_fetch_assoc($faturatResult); // Fetch the first row

        for ($i = 0; $i < count($faturatData); $i++) {
            $fatura = $faturatData[$i];
            $id = $fatura['emri']; // Assuming 'emri' is the ID for related data

            // Fetch related data using the ID
            $emriQuery = "SELECT * FROM klientet WHERE id='$id'";
            $emriResult = $conn->query($emriQuery);
            $emri = mysqli_fetch_array($emriResult);
            $emriFull = $emri['emri'];

            // Fetch data for other related queries
            $idKanalitQuery = "SELECT * FROM yinc WHERE kanali='$fatura[emri]'";
            $idKanalitResult = $conn->query($idKanalitQuery);
            $idKanalit = mysqli_fetch_array($idKanalitResult);

            $get_invoice_id = $fatura['fatura'];

            $shitjeTotaliQuery = "SELECT SUM(totali) as sum FROM shitje WHERE fatura='$get_invoice_id'";
            $shitjeTotaliResult = $conn->query($shitjeTotaliQuery);
            $shitjeTotali = mysqli_fetch_array($shitjeTotaliResult);

            $borgjeTotal = 0;
            $query = "SELECT * FROM yinc WHERE kanali='$emri[id]'";
            $nxerrjaEEmritKanalitResult = mysqli_query($conn, $query);

            while ($rowe = mysqli_fetch_array($nxerrjaEEmritKanalitResult)) {
                $amount = $rowe['shuma'] - $rowe['pagoi'];
                if ($amount > 0) {
                    $borgjeTotal += $amount;
                }
            }
            $pagesaQuery = mysqli_query($conn, "SELECT SUM(shuma) as sum FROM pagesat WHERE fatura='$fatura[fatura]'");
            $pagesa = mysqli_fetch_assoc($pagesaQuery);

            $obli = $shitjeTotali['sum'] - $pagesa['sum'];


            // Output table row
            echo "<tr>";
            echo "<td>$emriFull</td>";
            echo "<td>{$emri['emriart']}</td>";
            echo "<td>$borgjeTotal</td>";
            echo "<td>{$fatura['fatura']} <br><br> <button class='btn btn-primary btn-sm open-modal text-white rounded-5 shadow-sm'><i class='fi fi-rr-badge-dollar fa-lg'></i></button></td>";
            echo "<td>{$fatura['data']}</td>";
            echo "<td>{$shitjeTotali['sum']}</td>";
            echo "<td>{$pagesa['sum']}</td>";
            echo "<td>{$obli}</td>";
            echo "<td><a class='btn btn-primary btn-sm rounded-5 shadow-sm text-white' href='shitje.php?fatura={$fatura['fatura']}' target='_blank'><i class='fi fi-rr-edit'></i></a>
                <a class='btn btn-success btn-sm rounded-5 shadow-sm text-white' target='_blank' href='fatura.php?invoice={$fatura['fatura']}'><i class='fi fi-rr-print'></i></a>
                <a class='btn btn-danger btn-sm rounded-5 shadow-sm text-white delete' name='delete' id='{$fatura['fatura']}'><i class='fi fi-rr-trash'></i></a>
            </td>";
            echo "</tr>";
        }
        ?>
    </table>


    <div class="pagination">
        <?php
        $paginationQuery = "SELECT COUNT(*) AS total FROM fatura";
        $paginationResult = $conn->query($paginationQuery);
        $paginationData = mysqli_fetch_assoc($paginationResult);
        $total_records = $paginationData['total'];
        $total_pages = ceil($total_records / $results_per_page);

        // Previous link
        if ($page > 1) {
            echo "<a href='?page=" . ($page - 1) . "'>Previous</a>";
        } else {
            echo "<a class='disabled'>Previous</a>";
        }

        // Page numbers with ellipsis
        $numLinksToShow = 5; // Number of page links to show excluding ellipsis

        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i == $page) {
                echo "<a class='active'>$i</a>";
            } elseif ($i <= $numLinksToShow || $i > $total_pages - $numLinksToShow || abs($i - $page) < 2) {
                echo "<a href='?page=$i'>$i</a>";
            } elseif (abs($i - $page) === 2) {
                echo "<span>...</span>";
            }
        }

        // Next link
        if ($page < $total_pages) {
            echo "<a href='?page=" . ($page + 1) . "'>Next</a>";
        } else {
            echo "<a class='disabled'>Next</a>";
        }
        ?>
    </div>
</div>

<?php
$content = ob_get_clean(); // Get buffered content and clear buffer
echo $content; // Output the content
?>