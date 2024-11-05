<?php include 'partials/header.php'; ?>
<?php
// Include your database connection file
include 'conn-d.php';

// Initialize variables for error handling (optional)
$errors = [];

// Get the ID from the URL and sanitize it
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch the record from the database
$query = "SELECT * FROM kontrata WHERE id = $id";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("No record found with ID: $id");
}

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Sanitize and retrieve text inputs
    $emri = mysqli_real_escape_string($conn, $_POST['emri']);
    $mbiemri = mysqli_real_escape_string($conn, $_POST['mbiemri']);
    $perqindja = intval($_POST['perqindja']);
    $numri_personal = mysqli_real_escape_string($conn, $_POST['numri_personal']);
    $numri_i_telefonit = mysqli_real_escape_string($conn, $_POST['numri_i_telefonit']);
    $vepra = mysqli_real_escape_string($conn, $_POST['vepra']);
    $data = mysqli_real_escape_string($conn, $_POST['data']);
    $shenim = mysqli_real_escape_string($conn, $_POST['shenim']);
    $klienti = mysqli_real_escape_string($conn, $_POST['klienti']);
    $klient_email = mysqli_real_escape_string($conn, $_POST['klient_email']);
    $emriartistik = mysqli_real_escape_string($conn, $_POST['emriartistik']);

    // Update the record in the database without file fields
    $updateQuery = "
        UPDATE kontrata SET 
            emri = '$emri',
            mbiemri = '$mbiemri',
            perqindja = $perqindja,
            numri_personal = '$numri_personal',
            numri_i_telefonit = '$numri_i_telefonit',
            vepra = '$vepra',
            data = '$data',
            shenim = '$shenim',
            klienti = '$klienti',
            klient_email = '$klient_email',
            emriartistik = '$emriartistik'
        WHERE id = $id
    ";

    if (mysqli_query($conn, $updateQuery)) {
        echo "<div class='alert alert-success'>Kontrata është përditësuar me sukses.</div>";
    } else {
        echo "<div class='alert alert-danger'>Gabim gjatë përditësimit: " . mysqli_error($conn) . "</div>";
    }
}
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="p-5 mb-4 card shadow-sm rounded-5">
                <h1 class="card-title" style="text-transform:none;">Modifiko t&euml; dh&euml;nat që do të shfaqen në kontratë</h1>
                <form action="" method="post">
                    <!-- Personal Information -->
                    <div class="row">
                        <div class="col">
                            <label for="emri" class="form-label">Emri</label>
                            <input type="text" class="form-control shadow-sm rounded-5" name="emri" id="emri" value="<?php echo htmlspecialchars($row['emri']); ?>" required>
                        </div>
                        <div class="col">
                            <label for="mbiemri" class="form-label">Mbiemri</label>
                            <input type="text" class="form-control shadow-sm rounded-5" name="mbiemri" id="mbiemri" value="<?php echo htmlspecialchars($row['mbiemri']); ?>" required>
                        </div>
                    </div>

                    <!-- Additional Personal Information -->
                    <div class="row mt-3">
                        <div class="col">
                            <label for="numri_i_telefonit" class="form-label">Numri i Telefonit</label>
                            <input type="text" class="form-control shadow-sm rounded-5" name="numri_i_telefonit" id="numri_i_telefonit" value="<?php echo htmlspecialchars($row['numri_i_telefonit']); ?>" required>
                        </div>
                        <div class="col">
                            <label for="klient_email" class="form-label">Email Klienti</label>
                            <input type="email" class="form-control shadow-sm rounded-5" name="klient_email" id="klient_email" value="<?php echo htmlspecialchars($row['klient_email']); ?>" required>
                        </div>
                    </div>

                    <!-- Contract Details -->
                    <div class="row mt-3">
                        <div class="col">
                            <label for="perqindja" class="form-label">P&euml;rqindja</label>
                            <input type="number" class="form-control shadow-sm rounded-5" name="perqindja" id="perqindja" value="<?php echo htmlspecialchars($row['perqindja']); ?>" required>
                        </div>
                        <div class="col">
                            <label for="numri_personal" class="form-label">Numri Personal</label>
                            <input type="text" class="form-control shadow-sm rounded-5" name="numri_personal" id="numri_personal" value="<?php echo htmlspecialchars($row['numri_personal']); ?>" required>
                        </div>
                    </div>

                    <!-- Work Details -->
                    <div class="row mt-3">
                        <div class="col">
                            <label for="vepra" class="form-label">Vepra</label>
                            <input type="text" class="form-control shadow-sm rounded-5" name="vepra" id="vepra" value="<?php echo htmlspecialchars($row['vepra']); ?>" required>
                        </div>
                        <div class="col">
                            <label for="data" class="form-label">Data</label>
                            <input type="date" class="form-control shadow-sm rounded-5" name="data" id="data" value="<?php echo htmlspecialchars($row['data']); ?>" required>
                        </div>
                    </div>

                    <!-- Additional Details -->
                    <div class="row mt-3">
                        <div class="col">
                            <label for="shenim" class="form-label">Sh&euml;nim</label>
                            <textarea class="form-control shadow-sm rounded-5" name="shenim" id="shenim" rows="3" required><?php echo htmlspecialchars($row['shenim']); ?></textarea>
                        </div>
                        <div class="col">
                            <label for="klienti" class="form-label">Klienti</label>
                            <input type="text" class="form-control shadow-sm rounded-5" name="klienti" id="klienti" value="<?php echo htmlspecialchars($row['klienti']); ?>" required>
                        </div>
                    </div>

                    <!-- Artistic Name -->
                    <div class="row mt-3">
                        <div class="col">
                            <label for="emriartistik" class="form-label">Emri Artistik</label>
                            <input type="text" class="form-control shadow-sm rounded-5" name="emriartistik" id="emriartistik" value="<?php echo htmlspecialchars($row['emriartistik']); ?>" required>
                        </div>
                        <!-- Removed PDF File Field -->
                    </div>

                    <!-- Removed File Upload Fields -->

                    <input type="submit" class="btn btn-primary text-white shadow-sm rounded-5 mt-4" name="submit" style="text-transform:none;" value="P&euml;rditso">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('partials/footer.php') ?>