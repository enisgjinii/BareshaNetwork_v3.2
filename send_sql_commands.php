<?php
include 'conn-d.php';
// Prevent direct access
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get the SQL commands
    $sqlCommands = urldecode($_POST['sqlCommands']);

    // Split the commands by semicolon and newline to execute them one by one
    $commands = explode(";\n", $sqlCommands);
    foreach ($commands as $command) {
        if (trim($command)) {
            if ($conn->query($command) === TRUE) {
                echo "New record created successfully";
            } else {
                echo "Error: " . $conn->error;
            }
        }
    }
} else {
    echo "No direct script access allowed.";
}
