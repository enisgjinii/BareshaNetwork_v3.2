<?php
// Connect to the database

include_once 'conn-d.php';

if (isset($_POST['submit'])) {
    // Get the values of the form inputs
    $id = $_POST['id'];
    $note = $_POST['note'];
    $date = $_POST['date'];

    // Encrypt the note using OpenSSL
    $encrypted_shenimi = openssl_encrypt($note, 'AES-256-CBC', 'encryption_key');

    // Update the note in the database
    $sql = "UPDATE shenime SET shenimi = ?, data = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ssi', $encrypted_shenimi, $date, $id);
    mysqli_stmt_execute($stmt);

    // Close the statement
    mysqli_stmt_close($stmt);

    // Redirect back to the index page
    header("Location: notes.php");
    exit();
} else {
    // Get the ID of the note to be edited
    $id = $_GET['id'];

    // Fetch the note from the database
    $sql = "SELECT * FROM shenime WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    // Decrypt the note using OpenSSL
    $encrypted_shenimi = $row['shenimi'];
    $decrypted_shenimi = openssl_decrypt($encrypted_shenimi, 'AES-256-CBC', 'encryption_key');
    $row['shenimi'] = $decrypted_shenimi;

    // Close the statement
    mysqli_stmt_close($stmt);

    // Display the form to edit the note
    echo '<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <div class="row">
                    <div class="card p-4">
                        <h3>P&euml;rditso shenimin</h3>
                        <hr>
                        <form action="edit_note.php" method="post">
                            <div class="form-group">
                                <label for="note">Shenimi:</label>
                                <input type="text" class="form-control" id="note" name="note"
                                    value="' . $row['shenimi'] . '">
                            </div>
                            <div class="form-group">
                                <label for="date">Data dhe koha e shenimit:</label>
                                <input type="datetime-local" class="form-control" id="date" name="date"
                                    value="' . $row['data'] . '">
                            </div>
                            <input type="hidden" name="id" value="' . $row['id'] . '">
                            <button type="submit" name="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>';
}

ob_end_flush(); // flush output buffer and send output to the browser

?>

<?php include('partials/footer.php'); ?>