<?php

// Set environment variable
$env = "online"; // Set this to "online" when deploying the code online

// Define database credentials based on the environment variable
if ($env == "local") {
  $db_host = "localhost";
  $db_user = "root";
  $db_pass = "";
  $db_name = "bareshao_f";
} else {
  $db_host = "198.38.83.75";
  $db_user = "bareshao_f";
  $db_pass = "eeAo883?1";
  $db_name = "bareshao_f";
}

// Create database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_errno) {
  echo "Lidhja me MySQL d&euml;shtoi: " . $conn->connect_error;
  exit();
}

// Backup logic
$backupFolder = 'backups/';
$timestamp = date('Y-m-d_H');
$backupFile = $backupFolder . 'backup_' . $timestamp . '.sql';
$zipBackupFile = $backupFolder . 'backup_' . $timestamp . '.zip';

// Fetch all tables in the database
$tables = array();
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
  $tables[] = $row[0];
}

// Ensure the backup folder exists
if (!file_exists($backupFolder)) {
  mkdir($backupFolder, 0755, true);
}

// Loop through each table and export its structure and data
$handle = fopen($backupFile, 'w');
foreach ($tables as $table) {
  // Export table structure
  $createTableSQL = "SHOW CREATE TABLE $table";
  $result = $conn->query($createTableSQL);
  $createTable = $result->fetch_row();
  fwrite($handle, $createTable[1] . ";\n");

  // Check if the table has a primary key
  $primaryKeySQL = "SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'";
  $primaryKeyResult = $conn->query($primaryKeySQL);
  $primaryKeyRow = $primaryKeyResult->fetch_assoc();

  // If the table has a primary key, export the most recent 500 rows based on it
  if ($primaryKeyRow !== null && isset($primaryKeyRow['Column_name'])) {
    $primaryKeyColumn = $primaryKeyRow['Column_name'];
    $selectDataSQL = "SELECT * FROM $table ORDER BY $primaryKeyColumn DESC LIMIT 500";
  } else {
    // If the table doesn't have a primary key, export the most recent 500 rows without ordering
    $selectDataSQL = "SELECT * FROM $table LIMIT 500";
  }

  $result = $conn->query($selectDataSQL);
  while ($row = $result->fetch_assoc()) {
    $rowValues = array_map(array($conn, 'real_escape_string'), $row);
    $rowValues = "'" . implode("', '", $rowValues) . "'";
    $insertDataSQL = "INSERT INTO $table VALUES ($rowValues);";
    fwrite($handle, $insertDataSQL . "\n");
  }
}

// Close the file handle
fclose($handle);

// Create a ZIP archive
$zip = new ZipArchive();
if ($zip->open($zipBackupFile, ZipArchive::CREATE) === TRUE) {
  $zip->addFile($backupFile, 'backup.sql');
  $zip->close();

  // Remove the uncompressed SQL file
  unlink($backupFile);

} else {
}
