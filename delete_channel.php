<?php
// Include your database connection here (e.g., $conn)
include 'conn-d.php'; // Replace 'db_connection.php' with your actual database connection file

// Function to write data to a CSV file
function writeToCSV($data)
{
    $csv_file = 'deleted_channels.csv'; // Specify the CSV file name
    $csv_data = implode(',', $data) . "\n";
    file_put_contents($csv_file, $csv_data, FILE_APPEND);
}

// Check if the channel_id is present in the URL query parameter
if (isset($_GET["channel_id"])) {
    // Get the channel_id from the URL
    $channel_id = $_GET["channel_id"];

    // Retrieve the data to be deleted from the main table
    $sql_select = "SELECT * FROM refresh_tokens WHERE channel_id = ?";
    $stmt_select = mysqli_prepare($conn, $sql_select);
    mysqli_stmt_bind_param($stmt_select, "s", $channel_id);
    mysqli_stmt_execute($stmt_select);
    $result = mysqli_stmt_get_result($stmt_select);

    // Fetch the data
    $deleted_data = mysqli_fetch_assoc($result);

    // Write the deleted data to a CSV file
    writeToCSV($deleted_data);

    // Perform a query to delete the channel from the main table
    $sql_delete_from_main = "DELETE FROM refresh_tokens WHERE channel_id = ?";
    $stmt_delete_from_main = mysqli_prepare($conn, $sql_delete_from_main);
    mysqli_stmt_bind_param($stmt_delete_from_main, "s", $channel_id);

    // Execute the query
    if (mysqli_stmt_execute($stmt_delete_from_main)) {
        // Channel deleted successfully
        header("Location: invoice.php"); // Redirect back to your page with the table
        exit();
    } else {
        // Error occurred while deleting the channel
        echo "Error: " . mysqli_error($conn);
    }

    // Close the statements
    mysqli_stmt_close($stmt_select);
    mysqli_stmt_close($stmt_delete_from_main);
}

// Close the database connection (if you opened one)
mysqli_close($conn);
