<?php

include '../../conn-d.php';

// Get the role id from the POST request
$roleId = $_POST['role_id'];

// Query the pages for the selected role
$result = mysqli_query($conn, "SELECT * FROM role_pages WHERE role_id = $roleId");

// Convert the result to an array of pages
$pages = array();
while ($row = mysqli_fetch_array($result)) {
    $pages[] = array(
        'id' => $row['id'],
        'name' => $row['page']
    );
}

// Return the pages in JSON format
echo json_encode($pages);
?>
