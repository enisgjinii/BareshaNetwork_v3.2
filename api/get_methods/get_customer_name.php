<?php
// Include your database connection here

include('../../conn-d.php');

// Get the customer_id from the POST request
$customer_id = $_POST['customer_id'];

// Query the database to retrieve the customer name based on customer_id
$sql = "SELECT emri FROM klientet WHERE id = $customer_id";
$result = mysqli_query($conn, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $customerName = $row['emri'];
} else {
    $customerName = 'N/A';
    echo "MySQL Error: " . mysqli_error($conn); // Add this line for error details
}


// Return the customer name as the response
echo $customerName;
