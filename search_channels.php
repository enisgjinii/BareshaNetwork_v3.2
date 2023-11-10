<?php
// search_channels.php

include 'conn-d.php';

if (isset($_GET['search_query'])) {
    $searchQuery = $conn->real_escape_string($_GET['search_query']);

    // Prepare the SQL query with the search condition
    $sql = "SELECT channel_id, channel_name FROM youtube_refresh_tokens WHERE channel_name LIKE '%$searchQuery%'";
    $result = $conn->query($sql);
    $channels = array();
    while ($row = $result->fetch_assoc()) {
        $channels[] = array(
            'channel_id' => $row['channel_id'],
            'channel_name' => $row['channel_name']
        );
    }

    $conn->close();

    // Return the search results as JSON data
    echo json_encode($channels);
}
