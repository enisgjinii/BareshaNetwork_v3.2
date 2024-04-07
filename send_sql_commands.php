<?php
require './vendor/autoload.php'; // Make sure this points to autoload.php from Composer

include 'conn-d.php';

// Prevent direct script access
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get SQL commands
    $sqlCommands = urldecode($_POST['sqlCommands']);
    // Split commands by semicolon and new line
    $commands = explode(";\n", $sqlCommands);
    $commandsArray = array(); // Array to store SQL commands
    $logContent = ''; // Initialize log content

    // Timestamp for the start of the process
    $startTime = microtime(true);

    // Prepare a single query to execute all SQL commands
    $sql = implode(";\n", $commands);

    if ($conn->multi_query($sql)) {
        $i = 0;
        do {
            if ($result = $conn->store_result()) {
                while ($row = $result->fetch_assoc()) {
                    $emri = $row["emri"];
                    $logContent .= "- Emri: " . $emri . "<br>";
                }
                $result->free();
            }
            $logContent .= "- SQL command $i executed successfully<br>";
            $i++;
        } while ($conn->next_result());
    } else {
        $errorMessage = "Gabim: {$conn->error}";
        $logContent .= "- $errorMessage<br>";

        // Generate error log file name based on today's date
        $errorLogFile = "error_log_" . date("Y-m-d") . ".md";

        // Save error to the error log file
        file_put_contents($errorLogFile, "## Error Log - " . date("Y-m-d H:i:s") . "\n$errorMessage\n\n", FILE_APPEND);
    }

    // Timestamp for the end of the process
    $endTime = microtime(true);
    $executionTime = round(($endTime - $startTime) * 1000, 2); // Execution time in milliseconds

    // Include execution time in the log content
    $logContent .= "- Koha e ekzekutimit: $executionTime ms<br>";

    // Save log content to the activity log file
    $activityLogFile = "activity_log_" . date("Y-m-d") . ".md";
    $activityLogEntry = "## Activity Log - " . date("Y-m-d H:i:s") . "\n\n";
    $activityLogEntry .= "| Emri | Koha e ekzekutimit (ms) |\n";
    $activityLogEntry .= "|------|------------------------|\n";
    // Parse SQL commands and add them to the table
    foreach ($commands as $command) {
        $activityLogEntry .= "| " . trim($command) . " |  | \n";
    }
    $activityLogEntry .= "| Regjistër i ri u krijua me sukses | $executionTime |\n";

    file_put_contents($activityLogFile, $activityLogEntry, FILE_APPEND);

    // Save SQL commands to a JSON file
    file_put_contents('commands.json', json_encode($commandsArray, JSON_PRETTY_PRINT));
} else {
    echo "<div class='alert alert-warning' role='alert'>Qasja drejtpërdrejtë në skript nuk lejohet.</div>";
}
