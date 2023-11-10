<?php
// Check if a file was uploaded
if ($_FILES['csv_file']['error'] == UPLOAD_ERR_OK && $_FILES['csv_file']['type'] == 'text/csv') {
    // Open the uploaded file
    $handle = fopen($_FILES['csv_file']['tmp_name'], 'r');

    // Connect to the MySQL database
    include('conn-d.php');

    // Loop through the CSV rows and insert them into the database
    while (($data = fgetcsv($handle)) !== false) {
        // Skip the first row
        if ($data[0] == 'Emri') {
            continue;
        }

        $emri = $conn->real_escape_string($data[0]);
        $mbiemri = $conn->real_escape_string($data[1]);
        $nr_personal = $conn->real_escape_string($data[2]);
        $email = $conn->real_escape_string($data[3]);

        $conn->query("INSERT INTO klientCSV (emri, mbiemri, nr_personal, email) VALUES ('$emri', '$mbiemri', '$nr_personal','$email')");
    }

    // Close the file and database connections
    fclose($handle);
    $conn->close();

    // Redirect the user to a success page
    header('Location: klient_CSV.php');
    exit();
} else {
    // Handle any errors that occurred during the upload process
    // (e.g. file size limit exceeded, invalid file type, etc.)
    // Redirect the user to an error page or display an error message
}
?>
