<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $invoiceId = $_POST["invoiceId"];
    $paymentAmount = $_POST["paymentAmount"];

    // Validation: Check if the payment amount is a positive number.
    if (!is_numeric($paymentAmount) || $paymentAmount <= 0) {
        echo 'Invalid payment amount. Please enter a valid amount.';
    } else {

        include('conn-d.php');


        // Retrieve the current invoice details
        $sql = "SELECT * FROM invoices WHERE id = $invoiceId";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);

            // Calculate the new paid amount
            $newPaidAmount = $row["paid_amount"] + $paymentAmount;
            $totalAmount = $row["total_amount"];
            $status = ($newPaidAmount == $totalAmount) ? 'I paguar' : 'I pjesshëm';

            // Insert a new payment record into the payments table
            $paymentDate = date("Y-m-d"); // You can customize the date format
            $insertSql = "INSERT INTO payments (invoice_id, payment_amount, payment_date) VALUES ($invoiceId, $paymentAmount, '$paymentDate')";

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
