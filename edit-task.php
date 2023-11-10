<?php

include('conn-d.php');

// Get the task ID from the URL parameter
$id = $_GET['id'];

// Retrieve the task details from the database
$sql = "SELECT * FROM detyra WHERE id = $id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

// Extract the task details
$task = $row['detyra'];
$label = $row['etiketa'];
$due_date = $row['data'];

// Close the database connection
mysqli_close($conn);
?>

<?php include 'partials/header.php'; ?>

<body>
    <div class="main-panel">
    <div class="content-wrapper">
        <div class="container">
        <div class="p-5 rounded-5 shadow-sm border border-1 mb-4 card">
            <h2>Perditso detyren</h2>
            <hr>
            <form method="POST" action="update-task.php">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <div class="row">
                    <div class="col">
                        <input type="text" name="task" placeholder="Sheno detyren" class="form-control"
                            value="<?php echo $task; ?>">
                    </div>
                    <div class="col">
                        <input type="text" name="label" placeholder="Sheno etiket&euml;n" class="form-control"
                            value="<?php echo $label; ?>">
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col">
                        <input type="date" name="due_date" class="form-control" value="<?php echo $due_date; ?>">
                    </div>
                </div>
                <br>
                <button type="submit" class="btn btn-primary">Ruaj</button>
            </form></div>
        </div>
    </div>
</body>


        <?php include 'partials/footer.php'; ?>