<?php

include 'conn-d.php';
$table = "invoices";  // Replace with your table name
$sql_for_converting = "SELECT * FROM $table ORDER BY id DESC LIMIT 10";
$result_for_converting = $conn->query($sql_for_converting);

// Function to call the API and get the converted amount
function convertCurrency($amount)
{
    $apiUrl = "https://api.exconvert.com/convert?from=USD&to=EUR&amount=" . $amount . "&access_key=7ac9d0d8-2c2a1729-0a51382b-b85cd112";
    $response = file_get_contents($apiUrl);

    if ($response === FALSE) {
        return array("error" => "API request failed");
    }

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return array("error" => "Error decoding JSON response: " . json_last_error_msg());
    }

    if (isset($data['result']['EUR'])) {
        return array("result" => $data['result']['EUR']);
    } else {
        return array("error" => "Unexpected API response structure");
    }
}

$log = array();

// Loop through the result and update the total_amount_after_percentage column
if ($result_for_converting->num_rows > 0) {
    while ($row = $result_for_converting->fetch_assoc()) {
        $id = $row['id'];
        $total_amount_after_percentage = $row['total_amount_after_percentage']; // Replace with the correct column name for the total amount after percentage

        $conversionResult = convertCurrency($total_amount_after_percentage);

        if (isset($conversionResult['error'])) {
            $log[] = array("id" => $id, "status" => "error", "message" => $conversionResult['error']);
            continue;
        }

        $convertedAmount = $conversionResult['result'];

        // Ensure the converted amount is a valid number before updating
        if (is_numeric($convertedAmount)) {
            // Update the total_amount_after_percentage column in the database with the converted value
            $update_sql = "UPDATE $table SET total_amount_after_percentage = $convertedAmount WHERE id = $id";
            if ($conn->query($update_sql) === TRUE) {
                $log[] = array("id" => $id, "status" => "success", "message" => "Record updated successfully");
            } else {
                $log[] = array("id" => $id, "status" => "error", "message" => "Error updating record: " . $conn->error);
            }
        } else {
            $log[] = array("id" => $id, "status" => "error", "message" => "Invalid converted amount: $convertedAmount");
        }
    }
} else {
    $log[] = array("status" => "info", "message" => "No results found");
}

$conn->close();

// Save log to a JSON file
$logFile = 'update_log.json';
file_put_contents($logFile, json_encode($log, JSON_PRETTY_PRINT));

echo "Operation completed. Log saved to $logFile.\n";
