<?php

// Set environment variable
$env = "local"; // Set this to "online" when deploying the code online

// Define database credentials based on the environment variable
if ($env == "local") {
  $db_host = "localhost";
  $db_user = "root";
  $db_pass = "";
  $db_name = "bareshao_f";
} else {
  $db_host = "192.250.231.19";
  $db_user = "bareshao_f";
  $db_pass = "pg07#cN40";
  $db_name = "bareshao_f";
}

// Create database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_errno) {
  echo "Lidhja me MySQL dështoi: " . $conn->connect_error;
  exit();
}


