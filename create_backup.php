<?php
// Include the connection file
include 'conn-d.php';

// Set the backup filename and path
$backupFile = date('d-m-Y') . '.sql';
$backupPath = 'backups/' . $backupFile;

// Check if backup file already exists for today
$existingBackup = glob('backups/' . date('d-m-Y') . '*.sql');
if (!empty($existingBackup)) {
    // Backup already exists, do not create a new one
    echo 'Kopja rezerv&euml; ekziston tashm&euml; p&euml;r sot.';
    exit;
}

// Retrieve the list of tables in the database
$tables = [];
$result = mysqli_query($conn, 'SHOW TABLES');
while ($row = mysqli_fetch_row($result)) {
    $tables[] = $row[0];
}

// Open the backup file for writing
$file = fopen($backupPath, 'w');

// Iterate over each table and write its data to the backup file
foreach ($tables as $table) {
    // Fetch the table structure
    $createTableQuery = mysqli_query($conn, 'SHOW CREATE TABLE ' . $table);
    $tableData = mysqli_fetch_row($createTableQuery);

    // Write the table creation statement to the backup file
    fwrite($file, $tableData[1] . ";\n\n");

    // Fetch the table data
    $tableDataQuery = mysqli_query($conn, 'SELECT * FROM ' . $table);
    while ($rowData = mysqli_fetch_row($tableDataQuery)) {
        $rowValues = array_map('addslashes', $rowData);
        fwrite($file, 'INSERT INTO ' . $table . ' VALUES (\'' . implode('\', \'', $rowValues) . '\');' . "\n");
    }

    fwrite($file, "\n");
}

// Close the backup file
fclose($file);

// Send a response to indicate successful backup creation
echo 'Kopja rezerv&euml; u krijua me sukses.';
?>