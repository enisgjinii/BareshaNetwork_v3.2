<?php
require_once '../../conn-d.php';

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

$sql = "SELECT l.id, l.title, l.start_date, l.end_date, l.status, g.firstName, g.last_name 
        FROM leaves l
        JOIN googleauth g ON l.user_id = g.id";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred while preparing the statement']);
    exit;
}

if (!$stmt->execute()) {
    error_log("Execute failed: " . $stmt->error);
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred while executing the query']);
    exit;
}

$result = $stmt->get_result();
$events = [];

while ($row = $result->fetch_assoc()) {
    switch ($row['status']) {
        case 'Në pritje':
            $color = '#FFA500'; // Orange
            break;
        case 'Aprovuar':
            $color = '#28a745'; // Green
            break;
        case 'Refuzuar':
            $color = '#dc3545'; // Red
            break;
        default:
            $color = '#6c757d'; // Grey
    }

    $events[] = [
        'id' => $row['id'],
        'title' => htmlspecialchars($row['title'] . ' (' . $row['firstName'] . ' ' . $row['last_name'] . ')'),
        'start' => $row['start_date'],
        'end' => date('Y-m-d', strtotime($row['end_date'] . ' +1 day')), // Add one day to end date
        'color' => $color,
        'extendedProps' => [
            'status' => $row['status'],
            'firstName' => $row['firstName'],
            'lastName' => $row['last_name']
        ]
    ];
}

$stmt->close();
$conn->close();

echo json_encode($events);
