<?php
// Include your database connection code (e.g., connect to MySQL)
include 'conn-d.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the data from the form
    $id = $_POST['id'];
    $emri = $_POST['emri'];
    $mbiemri = $_POST['mbiemri'];
    $emri_i_kenges = $_POST['emri_i_kenges'];
    $shenim = $_POST['shenim'];

    // Prepare the SQL statement with placeholders for updating the record
    $sql = "UPDATE investimi SET emri = ?, mbiemri = ?, emri_i_kenges = ?, shenim = ? WHERE id = ?";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind the parameters and execute the statement
    $stmt->bind_param("sssss", $emri, $mbiemri, $emri_i_kenges, $shenim, $id);

    $response = array();

    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'Regjistrimi u p&euml;rdit&euml;sua me sukses.';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Gabim gjat&euml; p&euml;rdit&euml;simit t&euml; rekordit: ' . $stmt->error;
        error_log($stmt->error); // Log the error to the server's error log for debugging
    }


    // Close the statement
    $stmt->close();

    // Close the database connection
    $conn->close();

    // Convert the PHP array to JSON and return it as the response
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // If the request is not a POST request, return an error response
    $response = array('status' => 'error', 'message' => 'Metoda e pavlefshme e k&euml;rkes&euml;s.');
    header('Content-Type: application/json');
    echo json_encode($response);
}
