<?php
include 'conn-d.php';
$table = "invoices";

function convertCurrency($amount, $month)
{
    // Modify this function to include month-based conversion logic if needed
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

if (isset($_POST['convert'])) {
    $selectedMonth = $_POST['pickMonthForConverting'];

    // Fetch invoices for the selected month
    $sql_for_converting = "SELECT id, total_amount_after_percentage FROM $table WHERE MONTHNAME(item) = ? ORDER BY id DESC";
    $stmt = $conn->prepare($sql_for_converting);
    $stmt->bind_param("s", $selectedMonth);
    $stmt->execute();
    $result_for_converting = $stmt->get_result();

    if ($result_for_converting->num_rows > 0) {
        while ($row = $result_for_converting->fetch_assoc()) {
            $id = $row['id'];
            $total_amount_after_percentage = $row['total_amount_after_percentage'];
            $conversionResult = convertCurrency($total_amount_after_percentage, $selectedMonth);

            if (isset($conversionResult['error'])) {
                $log[] = array("id" => $id, "status" => "error", "message" => $conversionResult['error']);
                continue;
            }

            $convertedAmount = $conversionResult['result'];

            if (is_numeric($convertedAmount)) {
                $update_sql = "UPDATE $table SET total_amount_after_percentage = ? WHERE id = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("di", $convertedAmount, $id);
                if ($stmt->execute()) {
                    $log[] = array("id" => $id, "status" => "success", "message" => "Record updated successfully");
                } else {
                    $log[] = array("id" => $id, "status" => "error", "message" => "Error updating record: " . $stmt->error);
                }
            } else {
                $log[] = array("id" => $id, "status" => "error", "message" => "Invalid converted amount: $convertedAmount");
            }
        }
    } else {
        $log[] = array("status" => "info", "message" => "No results found for selected month");
    }

    $stmt->close();
}

$conn->close();

$logFile = 'update_log.json';
file_put_contents($logFile, json_encode($log, JSON_PRETTY_PRINT));

echo "Operation completed. Log saved to $logFile.\n";
?>
