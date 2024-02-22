<?php
// Start session
session_start();

// Include database connection and Invoice class
include('conn-d.php');
include('Invoice_2.php');

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Create new instance of Invoice class
    $invoice = new Invoice();

    // Get data from the form
    $postData = array(
        'companyName' => $_POST['companyName'],
        'address' => $_POST['address'],
        'mobile' => $_POST['mobile'],
        'email' => $_POST['email'],
        'taxId' => $_POST['taxId'],
        'invoiceDate' => $_POST['invoiceDate'],
        'subTotal' => $_POST['subTotal'],
        'taxAmount' => $_POST['taxAmount'],
        'taxRate' => $_POST['taxRate'],
        'totalAftertax' => $_POST['totalAftertax'],
        'amountPaid' => $_POST['amountPaid'],
        'amountDue' => $_POST['amountDue'],
        'notes' => $_POST['notes'],
        'productCode' => $_POST['productCode'],
        'productName' => $_POST['productName'],
        'quantity' => $_POST['quantity'],
        'price' => $_POST['price'],
        'total' => $_POST['total']
    );

    // Save invoice
    $invoice->saveInvoice($postData);

    // Redirect back to invoice list or display success message
    header("Location: invoice_list_2.php");
    exit();
} else {
    // If form is not submitted, redirect back to the form page
    header("Location: invoice_form.php");
    exit();
}
