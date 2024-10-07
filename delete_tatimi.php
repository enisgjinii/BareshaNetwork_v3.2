<?php
// delete_tatimi.php

// Set the response header to JSON
header('Content-Type: application/json');

// Include the database connection file
include 'conn-d.php'; // Adjust the path based on your directory structure

// Initialize the response array
$response = [
    'status' => '',
    'message' => ''
];

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize the 'id' parameter
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    // Validate the 'id'
    if ($id <= 0) {
        $response['status'] = 'error';
        $response['message'] = 'Rekordi i pavlefshëm.';
        echo json_encode($response);
        exit();
    }

    // Fetch the dokument path before deletion to remove the file
    $stmt = $conn->prepare("SELECT dokument FROM tatimi WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($dokument_path);
        $stmt->fetch();
        $stmt->close();

        if ($dokument_path) {
            // Delete the record from the database
            $stmt = $conn->prepare("DELETE FROM tatimi WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    // Delete the file from the server
                    if (file_exists($dokument_path)) {
                        unlink($dokument_path);
                    }

                    // Success response
                    $response['status'] = 'success';
                    $response['message'] = 'Rekordi u fshi me sukses.';
                } else {
                    // Error during deletion
                    $response['status'] = 'error';
                    $response['message'] = 'Gabim gjatë fshirjes së rekordit: ' . $stmt->error;
                }
                $stmt->close();
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Gabim në përgatitjen e pyetjes së SQL: ' . $conn->error;
            }
        } else {
            // Rekordi nuk u gjet
            $response['status'] = 'error';
            $response['message'] = 'Rekordi nuk u gjet.';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Gabim në përgatitjen e pyetjes së SQL: ' . $conn->error;
    }

    // Close the database connection
    $conn->close();

    // Return the response as JSON
    echo json_encode($response);
} else {
    // If the request method is not POST
    $response['status'] = 'error';
    $response['message'] = 'Kërkesa e pavlefshme.';
    echo json_encode($response);
    exit();
}
