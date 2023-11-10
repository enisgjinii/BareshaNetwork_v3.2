<?php

include('conn-d.php');
// Get the value of the first select from the query string
$artistii = $_GET['artistii'];

// Escape the value to prevent SQL injection attacks
$escapedArtistii = mysqli_real_escape_string($conn, $artistii);

// Query the database to fetch the options for the second select based on the value of the first select
$sql = "SELECT DISTINCT AccountingPeriod FROM platformat_2 WHERE Emri = '$escapedArtistii'";
$result = mysqli_query($conn, $sql);

// Generate the HTML for the options
$optionsHtml = '';
while ($row = mysqli_fetch_assoc($result)) {
    $value = htmlspecialchars($row['AccountingPeriod'], ENT_QUOTES);
    $text = htmlspecialchars($row['AccountingPeriod'], ENT_QUOTES);
    $optionsHtml .= "<option value=\"$value\">$text</option>";
}

// Send the HTML back to the client
echo $optionsHtml;
