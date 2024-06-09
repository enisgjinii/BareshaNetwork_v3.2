<?php

// Connect to the database (include 'conn-d.php' where your database connection is defined)
include 'conn-d.php';

// Retrieve the values from the hidden field
$allValues = $_POST['allValues'];

// Split the values by the delimiter (semicolon)
$valuesArray = explode(";", $allValues);

// Iterate through the values and insert them into the database
foreach ($valuesArray as $valueString) {
    $values = explode(", ", $valueString);

    // Extract the values from the array
    $invoiceNumber = $values[0];
    $customerId = $values[1];
    $item = $values[2];
    $totalAmount = $values[3];
    $totalAmountAfterPercentage = $values[4];
    $date = $values[5];

    // Perform the database insertion (replace with your actual database table and columns)
    $sql = "INSERT INTO invoices (invoice_number, customer_id, item, total_amount, total_amount_after_percentage,created_date,total_amount_in_eur,total_amount_in_eur_after_percentage) VALUES ('$invoiceNumber', '$customerId', '$item', '$totalAmount', '$totalAmountAfterPercentage', '$date', '$totalAmountAfterPercentage', '$totalAmountAfterPercentage')";

    if ($conn->query($sql) !== TRUE) {
        echo "Error inserting data: " . $conn->error;
    }
}

// Close the database connection (if necessary)

// Redirect back to the previous page or another page as needed
header("Location: invoice.php");
exit();

?>