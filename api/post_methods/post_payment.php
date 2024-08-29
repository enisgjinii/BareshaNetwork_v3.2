<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $invoiceId = filter_input(INPUT_POST, "invoiceId", FILTER_VALIDATE_INT);
    $paymentAmount = filter_input(INPUT_POST, "paymentAmount", FILTER_VALIDATE_FLOAT);
    $bankInfo = htmlspecialchars($_POST["bankInfo"], ENT_QUOTES, 'UTF-8');
    $type_of_pay = htmlspecialchars($_POST["type_of_pay"], ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');

    // Validation: Check if the invoice ID is valid
    if (!$invoiceId || $invoiceId <= 0) {
        echo 'Invalid invoice ID. Please provide a valid invoice ID.';
    } elseif (!$paymentAmount || $paymentAmount <= 0) {
        echo 'Invalid payment amount. Please enter a valid amount.';
    } elseif (empty($bankInfo)) {
        echo 'Invalid bank information. Please select a valid bank.';
    } else {
        include('../../conn-d.php');

        // Retrieve the current invoice details
        $sql = "SELECT * FROM invoices WHERE id = $invoiceId";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);

            // Calculate the new paid amount
            $newPaidAmount = $row["paid_amount"] + $paymentAmount;
            $totalAmount = $row["total_amount"];
            $status = ($newPaidAmount == $totalAmount) ? 'I paguar' : 'I pjesshëm';

            // Insert a new payment record into the payments table
            $paymentDate = date("Y-m-d"); // You can customize the date format
            $insertSql = "INSERT INTO payments (invoice_id, payment_amount, payment_date, bank_info, type_of_pay, description) VALUES ($invoiceId, $paymentAmount, '$paymentDate', '$bankInfo', '$type_of_pay', '$description')";

            if (mysqli_query($conn, $insertSql)) {
                // Update the paid amount and status in the invoices table
                $updateSql = "UPDATE invoices SET paid_amount = $newPaidAmount WHERE id = $invoiceId";

                if (mysqli_query($conn, $updateSql)) {
                    echo 'success';
                } else {
                    echo 'Error updating invoice: ' . mysqli_error($conn);
                }
            } else {
                echo 'Error inserting payment: ' . mysqli_error($conn);
            }
        } else {
            echo 'Invoice not found.';
        }

        mysqli_close($conn);
    }
} else {
    echo 'Invalid request.';
}
