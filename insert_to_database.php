<?php
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "123456";
$dbname = "bareshao_f";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the form was submitted
if (isset($_POST['submit'])) {
    // Retrieve data from the form
    
    $date = $_POST['date_created'];
    $invoice_id = $_POST['invoice_id'];
    
    // Insert data into the database
    $sql = "INSERT INTO invoices (invoice_date,invoice_id) VALUES ('$date','$invoice_id')";
    
    if (mysqli_query($conn, $sql)) {
        echo "Payment successfully processed!";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

// Close the database connection
mysqli_close($conn);
?>
