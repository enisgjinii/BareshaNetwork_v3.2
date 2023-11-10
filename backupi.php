<?php
// Check if the current time is 11:50 PM
if (date('H:i') === '23:50') {
    performDatabaseBackup();
} else {
    echo "Backup will be performed at 11:50 PM.";
}

function performDatabaseBackup() {
    include 'conn-d.php';

    // Set the backup filename and path
    $backupFile = 'backup_' . date('d-m-Y') . '.sql';
    $backupPath = 'backups/' . $backupFile;

    // Retrieve the list of tables in the database
    $tables = [];
    $result = mysqli_query($conn, 'SHOW TABLES');
    if (!$result) {
        die("Error: " . mysqli_error($conn));
    }
    while ($row = mysqli_fetch_row($result)) {
        $tables[] = $row[0];
    }

    // Open the backup file for writing
    $file = fopen($backupPath, 'w');
    if (!$file) {
        die("Error: Could not open backup file for writing.");
    }

    // Iterate over each table and write its data to the backup file
    foreach ($tables as $table) {
        // Fetch the table structure
        $createTableQuery = mysqli_query($conn, 'SHOW CREATE TABLE ' . $table);
        if (!$createTableQuery) {
            die("Error: " . mysqli_error($conn));
        }
        $tableData = mysqli_fetch_row($createTableQuery);

        // Write the table creation statement to the backup file
        fwrite($file, $tableData[1] . ";\n\n");

        // Fetch the table data
        $tableDataQuery = mysqli_query($conn, 'SELECT * FROM ' . $table);
        if (!$tableDataQuery) {
            die("Error: " . mysqli_error($conn));
        }
        while ($rowData = mysqli_fetch_row($tableDataQuery)) {
            $rowValues = array_map('addslashes', $rowData);
            fwrite($file, 'INSERT INTO ' . $table . ' VALUES (\'' . implode('\', \'', $rowValues) . '\');' . "\n");
        }

        fwrite($file, "\n");
    }

    // Close the backup file
    fclose($file);

    // Display a success message
    echo "Backup created successfully.";
}
