<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientId = intval($_GET['id']);

    // Connect to the database
    include 'conn-d.php';

    // Fetch the client data before deletion
    $stmt = $conn->prepare('SELECT * FROM klientet WHERE id = ?');
    $stmt->bind_param('i', $clientId);
    $stmt->execute();
    $result = $stmt->get_result();
    $clientData = $result->fetch_assoc();
    $stmt->close();

    if ($clientData) {
        // Convert the client data to JSON format and save it to a file
        $jsonFileName = 'deleted_clients/client_' . $clientId . '_' . time() . '.json';
        file_put_contents($jsonFileName, json_encode($clientData, JSON_PRETTY_PRINT));

        // Now proceed to delete the client record
        $stmt = $conn->prepare('DELETE FROM klientet WHERE id = ?');
        $stmt->bind_param('i', $clientId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Client data saved and deleted.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete client.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Client not found.']);
    }

    $conn->close();
}
