<?php
// Include the header and database connection
include 'partials/header.php';

// Fetch sign-up data from the 'waitlist' table
// Adjust the table name and column names as per your database schema
$query = "SELECT id, fullName, email, phoneNumber, username, created_at FROM waitlist ORDER BY created_at DESC";
$result = $conn->query($query);

// Check for query execution errors
if (!$result) {
    die("Database query failed: " . $conn->error);
}
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <!-- Breadcrumb Navigation -->
            <nav class="bg-white px-2 rounded-5" style="width:fit-content;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="text-reset" style="text-decoration: none;">Klientet</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="#" class="text-reset" style="text-decoration: none;">Lista e Sign-Up</a></li>
                </ol>
            </nav>

            <!-- Table Container -->
            <div class="mt-4">
                <div class="table-responsive">
                    <table id="signupTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Emri i Plotë</th>
                                <th>Email</th>
                                <th>Numri i Telefonit</th>
                                <th>Emri i Përdoruesit</th>
                                <th>Data e Regjistrimit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['fullName']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['phoneNumber']); ?></td>
                                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                                        <td><?php echo htmlspecialchars(date("d/m/Y H:i", strtotime($row['created_at']))); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Nuk ka të dhëna për të treguar.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Free the result set and close the database connection
$result->free();
$conn->close();

// Include the footer
include 'partials/footer.php';
?>

<!-- Initialize DataTables -->
<script>
    $(document).ready(function() {
        $('#signupTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/sq.json" // Albanian language file (ensure the path is correct)
            },
            "paging": true,
            "searching": true,
            "ordering": true,
            "order": [
                [0, "desc"]
            ], // Order by ID descending
            "responsive": true,
            "lengthChange": true,
            "pageLength": 10, // Number of entries per page
        });
    });
</script>