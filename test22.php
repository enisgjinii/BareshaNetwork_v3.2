<?php
// Include your database connection here
include 'conn-d.php';
// Query to fetch invoice data
$sql_for_converting = "SELECT * FROM invoices LIMIT 10";
$result = $conn->query($sql_for_converting);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoices</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <?php
    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>API Response</th><th>Paid Amount in USD</th><th>Total Amount After Percentage in USD</th><th>Paid Amount in EUR</th><th>Total Amount After Percentage in EUR</th></tr>";
        while ($row = $result->fetch_assoc()) {
            $paid_amount = $row['paid_amount'];
            $total_amount_after_percentage = $row['total_amount_after_percentage'];
            // Convert the paid amount from USD to EUR
            $paid_amount_in_eur = convertCurrency($paid_amount, 'USD', 'EUR');
            // Convert the total amount after percentage from USD to EUR
            $total_amount_after_percentage_in_eur = convertCurrency($total_amount_after_percentage, 'USD', 'EUR');
            // Print the table row with API response and converted amounts
            echo "<tr>";
            echo "<td>";
            echo "API Response: " . print_r($paid_amount_in_eur, true) . "<br>";
            echo "API Response: " . print_r($total_amount_after_percentage_in_eur, true) . "<br>";
            echo "</td>";
            echo "<td>" . formatOutput($paid_amount) . "</td>";
            echo "<td>" . formatOutput($total_amount_after_percentage) . "</td>";
            echo "<td>" . formatOutput($paid_amount_in_eur) . "</td>";
            echo "<td>" . formatOutput($total_amount_after_percentage_in_eur) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No invoices found.";
    }
    $conn->close();
    function convertCurrency($amount, $from_currency, $to_currency)
    {
        $api_key = '7ac9d0d8-2c2a1729-0a51382b-b85cd112';
        $url = "https://api.exconvert.com/convert?from=$from_currency&to=$to_currency&amount=$amount&access_key=$api_key";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        // Handle curl errors
        if (curl_errno($ch)) {
            $error_msg = 'Curl error: ' . curl_error($ch);
            curl_close($ch);
            logError($error_msg);
            return $error_msg;
        }
        curl_close($ch);
        $data = json_decode($response, true);
        // Debug information
        if (json_last_error() !== JSON_ERROR_NONE) {
            $error_msg = "JSON decode error: " . json_last_error_msg();
            logError($error_msg);
            return $error_msg;
        }
        // Check API response
        if (!empty($data)) {
            return $data['result']['EUR']; // Return the EUR amount directly
        } else {
            $error_msg = "Error: API response is empty or invalid.";
            logError($error_msg);
            return $error_msg;
        }
    }
    function logError($message)
    {
        error_log($message, 3, 'errors.log');
        echo "An error occurred. Please check the log for details.<br>";
    }
    function formatOutput($output)
    {
        if (is_array($output)) {
            return "Error: Invalid output format.";
        } else {
            return htmlspecialchars($output);
        }
    }
    ?>
</body>
</html>