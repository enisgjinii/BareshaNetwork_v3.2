<?php
// Include the database connection class
include '../../conn-d.php';

class DataHandler
{
    private $conn;

    // Constructor to initialize the database connection
    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    // Method to insert data into the database
    public function insertData($emri, $mbiemri, $emri_i_kenges, $shenim)
    {
        $sql = "INSERT INTO investimi (emri, mbiemri, emri_i_kenges, shenim) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        // Bind parameters
        $stmt->bind_param("ssss", $emri, $mbiemri, $emri_i_kenges, $shenim);

        // Execute the statement
        $stmt->execute();

        // Check if the execution was successful
        if ($stmt->affected_rows > 0) {
            $response['status'] = 'success';
            $response['message'] = 'Të dhënat u regjistruan me sukses.';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Gabim: ' . $stmt->error;
        }

        // Close the statement
        $stmt->close();

        return $response;
    }
}

// Get data from the form
$emri = $_POST['emri'];
$mbiemri = $_POST['mbiemri'];
$emri_i_kenges = $_POST['emri_i_kenges'];
$shenim = $_POST['shenim'];

// Handle database interaction using OOP
$dataHandler = new DataHandler($conn);
$response = $dataHandler->insertData($emri, $mbiemri, $emri_i_kenges, $shenim);

// Close the database connection
$conn->close();

// Convert the PHP array to JSON and return it as the response
header('Content-Type: application/json');
echo json_encode($response);
