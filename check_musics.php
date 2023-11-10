<?php include 'partials/header.php'; ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <div class="row">
                    <?php
                    $query = "SELECT * FROM ngarkimi ORDER BY id DESC LIMIT 40";
                    $result = mysqli_query($conn, $query);
                    $count = 0; // To keep track of the number of cards in the current row

                    while ($row = mysqli_fetch_assoc($result)) {
                        // Assuming you have columns named emri, kengtari, and id in your table
                        $emri = $row['emri'];
                        $kengtari = $row['kengetari'];
                        $id = $row['id'];
                    ?>

                        <div class="col-md-3 mb-4">
                            <div class="card px-4 py-3 rounded-5 border-1">
                                <p class="card-text"><?php echo $emri; ?></p>
                                <p class="card-text"><?php echo $kengtari; ?></p>
                                <p class="card-text"><?php echo $id; ?></p>
                                <!-- "See More" button for each card -->
                                <div>
                                    <a href="details_page.php?id=<?php echo $id; ?>" class="btn btn-primary btn-sm rounded-5 text-white" style="text-transform: none;">See More</a>
                                </div>
                            </div>
                        </div>

                    <?php
                        $count++;
                        // If we've displayed 4 cards, close the row and start a new one
                        if ($count % 4 == 0) {
                            echo '</div><div class="row">';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            order: [],
        });
    });
</script>
<?php include 'partials/footer.php'; ?>