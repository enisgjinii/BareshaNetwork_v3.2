

<?php


$env = getenv('DB_ENV') ?: 'local';

if ($env == "local") {
  $db_host = getenv('DB_HOST') ?: 'localhost';
  $db_user = getenv('DB_USER') ?: 'root';
  $db_pass = getenv('DB_PASS') ?: '';
  $db_name = getenv('DB_NAME') ?: 'bareshao_f';
} else {
  $db_host = getenv('DB_HOST') ?: '192.250.231.19';
  $db_user = getenv('DB_USER') ?: 'bareshao_f';
  $db_pass = getenv('DB_PASS') ?: 'pg07#cN40';
  $db_name = getenv('DB_NAME') ?: 'bareshao_f';
}

// Create database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_errno) {
  // Log the error instead of displaying it
  error_log("Failed to connect to MySQL: " . $conn->connect_error);
  exit("Database connection error. Please try again later.");
}

// Set the character set to utf8mb4
if (!$conn->set_charset("utf8mb4")) {
  error_log("Error loading character set utf8mb4: " . $conn->error);
  exit("Character set error. Please contact support.");
}
?>
