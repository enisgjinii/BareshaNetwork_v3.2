<?php include 'partials/header.php'; ?>
<?php
// Include your database connection file
include 'conn-d.php';

// Get the ID from the URL
$id = $_GET['id'];

// Fetch the record from the database
$query = "SELECT * FROM kontrata WHERE id = $id";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Get the updated values from the form
    $emri = $_POST['emri'];
    $mbiemri = $_POST['mbiemri'];
    $perqindja = $_POST['perqindja'];
    $numri_personal = $_POST['numri_personal'];

    // Update the record in the database
    $query = "UPDATE kontrata SET emri = '$emri', mbiemri = '$mbiemri',perqindja = '$perqindja',numri_personal = '$numri_personal' WHERE id = $id";
    mysqli_query($conn, $query);
}
?>


<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="p-5 mb-4 card shadow-sm rounded-5">
                <h1 class="card-title" style="text-transform:none;">Modifiko t&euml; dh&euml;nat te cilat do te shfaqen ne kontrat&euml;</h1>
                <form action="" method="post">
                    <div class="row">
                        <div class="col">
                            <label for="emri" class="form-label">Emri</label>
                            <input type="text" class="form-control shadow-sm rounded-5" name="emri" id="emri" value="<?php echo $row['emri']; ?>">
                        </div>
                        <div class="col">
                            <label for="mbiemri" class="form-label">Mbiemri</label>
                            <input type="text" class="form-control shadow-sm rounded-5" name="mbiemri" id="mbiemri" value="<?php echo $row['mbiemri']; ?>">
                        </div>

                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <label for="perqindja" class="form-label">P&euml;rqindja</label>
                            <input type="text" class="form-control shadow-sm rounded-5" name="perqindja" id="perqindja" value="<?php echo $row['perqindja']; ?>">
                        </div>
                        <div class="col">
                            <label for="numri_personal" class="form-label">Numri personal</label>
                            <input type="text" class="form-control shadow-sm rounded-5" name="numri_personal" id="numri_personal" value="<?php echo $row['numri_personal']; ?>">
                        </div>
                    </div>

                    <input type="submit" class="btn btn-primary text-white shadow-sm rounded-5 mt-4" name="submit" style="text-transform:none;" value="P&euml;rditso">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('partials/footer.php') ?>