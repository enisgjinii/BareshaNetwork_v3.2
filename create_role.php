<?php
// Get the selected pages and role name from the form
$pages = $_POST['pages'];
$role_name = $_POST['role_name'];

include 'conn-d.php';

// Check connection
if ($conn->connect_errno) {
  echo 'Failed to connect to MySQL: ' . $conn->connect_error;
  exit();
}

// Create the role in the database
$stmt = $conn->prepare('INSERT INTO roles (name) VALUES (?)');
$stmt->bind_param('s', $role_name);
$stmt->execute();
$role_id = $conn->insert_id;

// Add the selected pages to the role
$stmt = $conn->prepare('INSERT INTO role_pages (role_id, page) VALUES (?, ?)');
foreach ($pages as $page) {
  $stmt->bind_param('is', $role_id, $page);
  $stmt->execute();
}

// Close statement and database connection
$stmt->close();
$conn->close();

// Redirect back to the page that lists all roles
header('Location: roles.php');
exit;
