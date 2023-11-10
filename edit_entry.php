<?php
ob_start();
// Check if the ID parameter is set
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

// Connect to the database
include 'partials/header.php';
require_once "conn-d.php";

// Get the entry data based on the ID parameter
$id = mysqli_real_escape_string($conn, $_GET['id']);
$result = mysqli_query($conn, "SELECT * FROM filet WHERE id='$id'");
if (mysqli_num_rows($result) == 0) {
    header("Location: filet.php");
    exit();
}
$row = mysqli_fetch_assoc($result);

// Handle form submission
if (isset($_POST['update'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    mysqli_query($conn, "UPDATE filet SET pershkrimi='$name' WHERE id='$id'");
    header("Location: filet.php");
    exit();
}
ob_flush();
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <div class="p-5 rounded-5 shadow-sm mb-4 card">
                    <h4 class="card-title">P&euml;rditso pershkrimin e dokumentit</h4>
                    <form method="post">
                        <label>P&euml;rshkrimi:</label>
                        <input type="text" name="name" value="<?php echo $row['pershkrimi']; ?>" class="form-control rounded-5 shadow-sm border mt-2">
                        <br>
                        <input type="submit" name="update" value="P&euml;rditso" class="btn btn-light shadow-sm rounded-5" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'partials/footer.php'; ?>