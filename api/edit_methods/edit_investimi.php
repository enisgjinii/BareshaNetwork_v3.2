<?php
// Include your database connection code (e.g., connect to MySQL)
include '../../conn-d.php';

class DataHandler
{
    private $conn;

    // Constructor to initialize the database connection
    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    // Method to update data in the database
    public function updateData($id, $emri, $mbiemri, $emri_i_kenges, $shenim)
    {
        // Prepare the SQL statement with placeholders for updating the record
        $sql = "UPDATE investimi SET emri = ?, mbiemri = ?, emri_i_kenges = ?, shenim = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            // Handle prepare error
            $response['status'] = 'error';
            $response['message'] = 'Gabim gjatë përgatitjes së deklaratës SQL: ' . $this->conn->error;
            return $response;
        }

        // Bind the parameters securely
        $stmt->bind_param("sssss", $emri, $mbiemri, $emri_i_kenges, $shenim, $id);

        // Execute the statement
        if (!$stmt->execute()) {
            // Handle execute error
            $response['status'] = 'error';
            $response['message'] = 'Gabim gjatë ekzekutimit të deklaratës SQL: ' . $stmt->error;
            $stmt->close();
            return $response;
        }

        $response = array();

        // Check if the execution was successful
        if ($stmt->affected_rows > 0) {
            $response['status'] = 'success';
            $response['message'] = 'Regjistrimi u përditësua me sukses.';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Gabim: Regjistrimi nuk u përditësua.';
        }

        // Close the statement
        $stmt->close();

        return $response;
    }
}

// Function to sanitize input using regular expressions
function sanitizeInput($input)
{
    return preg_replace('/[^a-zA-Z0-9\s\-]/', '', $input);
}

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the data from the form and sanitize it
    $id = sanitizeInput($_POST['id']);
    $emri = sanitizeInput($_POST['emri']);
    $mbiemri = sanitizeInput($_POST['mbiemri']);
    $emri_i_kenges = sanitizeInput($_POST['emri_i_kenges']);
    $shenim = sanitizeInput($_POST['shenim']);

    // Validate input data
    if (empty($id) || empty($emri) || empty($mbiemri) || empty($emri_i_kenges) || empty($shenim)) {
        $response = array('status' => 'error', 'message' => 'Të dhënat hyrëse janë të pasakta.');
        header('Content-Type: application/json');
        echo json_encode($response);
        exit; // Terminate script execution
    }

    // Handle database interaction using OOP
    $dataHandler = new DataHandler($conn);
    $response = $dataHandler->updateData($id, $emri, $mbiemri, $emri_i_kenges, $shenim);

    // Convert the PHP array to JSON and return it as the response
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // If the request is not a POST request, return an error response
    $response = array('status' => 'error', 'message' => 'Metoda e pavlefshme e kërkesës.');
    header('Content-Type: application/json');
    echo json_encode($response);
}
