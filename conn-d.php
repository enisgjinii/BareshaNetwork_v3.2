<?php

// Set environment variable
$env = "local"; // Set this to "online" when deploying the code online

// Define database credentials based on environment variable
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