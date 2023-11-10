<?php
// Include necessary configurations and database connections
include "conn-d.php";

$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $limit; // Offset for database query
$query = "SELECT * FROM fatura ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($query);

// Generate the updated table content
ob_start();
?>
<table class="table table-bordered">
    <thead class="bg-light">
        <tr>
            <th>Emri</th>
            <th>Emri artistik</th>
            <th>Fatura</th>
            <th>Data</th>
            <th>Shuma</th>
            <th>Shuma e paguar</th>
            <th>Obligime</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) {
            $id_of_client = $row['emri'];
            $invoice_id = $row['fatura'];


            $query_for_client = "SELECT * FROM klientet WHERE id='$id_of_client'";
            $asking_database = $conn->query($query_for_client);
            $row_for_client = $asking_database->fetch_assoc();


            $shuma = $conn->query("SELECT SUM(`totali`) as `sum` FROM `shitje` WHERE fatura='$invoice_id'");
            $fetch_shuma = mysqli_fetch_array($shuma);

            $merrpagesen = $conn->query("SELECT SUM(`shuma`) as `sum` FROM `pagesat` WHERE fatura='$invoice_id'");
            $merrep = mysqli_fetch_array($merrpagesen);




            $obli = $fetch_shuma['sum'] - $merrep['sum'];
        ?>
            <tr>
                <td>

                    <!-- <a class="btn btn-outline-primary btn-sm" href="kanal.php?kid=<?php echo $row['emri'] ?>">Shih profilin  |</a> -->
                    <?php echo $row_for_client['emri'] ?>
                </td>
                <td><?php echo $row_for_client['emriart'] ?></td>
                <td><?php echo $row['fatura'] ?> <button class='btn btn-primary open-modal text-white rounded-5 shadow-sm'><i class='fi fi-rr-badge-dollar fa-lg'></i></button></td>
                <td><?php echo $row['data'] ?></td>
                <td><?php echo $fetch_shuma['sum'] ?></td>
                <td><?php echo $merrep['sum'] ?></td>
                <td><?php echo $obli ?></td>

            </tr>
        <?php } ?>
    </tbody>
</table>
<br>

<?php if ($total_pages > 1) { ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">

            <?php if ($page > 1) { ?>
                <li class="page-item me-1"><a class="page-link" href="?page=<?php echo ($page - 1) ?>">E m&euml;parshme</a></li>
            <?php } ?>

            <?php for ($i = $start_page; $i <= $end_page; $i++) {
                $active = ($i == $page) ? 'active' : '';
            ?>
                <li class="page-item <?php echo $active ?> me-1"><a class="page-link" href="?page=<?php echo $i ?>"><?php echo $i ?></a></li>
            <?php } ?>

            <?php if ($page < $total_pages) { ?>
                <li class="page-item "><a class="page-link" href="?page=<?php echo ($page + 1) ?>">Tjetra</a></li>
            <?php } ?>

        </ul>
    </nav>
<?php } ?>
<?php
$tableContent = ob_get_clean();

// Return the updated table content
echo $tableContent;

// Close the database connection
$conn->close();
?>