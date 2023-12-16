<?php
// Include your database connection code here
include 'conn-d.php';
// Retrieve the selected client ID from the AJAX request
$selectedClientId = $_POST['id_of_client'];

// Fetch additional data for the selected client from the database
$clientResult = $conn->query("SELECT * FROM klientet WHERE id = $selectedClientId");
$selectedClientData = mysqli_fetch_array($clientResult);

// Display additional data in a table
echo '<br><table class="table table-bordered">';
echo '<tr><td>ID</td><td>' . $selectedClientData['id'] . '</td></tr>';
echo '<tr><td>Emri</td><td>' . $selectedClientData['emri'] . '</td></tr>';
echo '<tr><td>Perqindja</td><td><input type="text" class="form-control rounded-5 border border-2 shadow-none" id="perqindja" value="' . $selectedClientData['perqindja'] . '"/></td></tr>';
// Add more rows for additional fields as needed
echo '</table>';
