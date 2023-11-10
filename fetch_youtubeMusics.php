<?php
// Include the connection to the database
include 'conn-d.php';

// Set up the SQL query to retrieve data from the database
$sql = "SELECT * FROM musicsyoutube ORDER BY id DESC";
$result = $conn->query($sql);

// Build an array of data to be returned as JSON
$data = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $title = $row['titulli'];
        $link = $row['linku'];
        $data[] = array(
            "id" => $id,
            "titulli" => $title,
            "linku" => $link,

        );
    }
}


// Return the data as JSON
echo json_encode($data);
