<?php
// Start session
session_start();

// Include database connection and Invoice class
include('../../conn-d.php');
include('../../Invoice_2.php');

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Create new instance of Invoice class
    $invoice = new Invoice();

    // Get data from the form
    $postData = array(
        'companyName' => isset($_POST['companyName']) ? $_POST['companyName'] : '',
        'address' => isset($_POST['address']) ? $_POST['address'] : '',
        'mobile' => isset($_POST['mobile']) ? $_POST['mobile'] : '',
        'email' => isset($_POST['email']) ? $_POST['email'] : '',
        'taxId' => isset($_POST['taxId']) ? $_POST['taxId'] : '',
        'invoiceDate' => isset($_POST['invoiceDate']) ? $_POST['invoiceDate'] : '',
        'subTotal' => isset($_POST['subTotal']) ? $_POST['subTotal'] : 0,
        'taxAmount' => isset($_POST['taxAmount']) ? $_POST['taxAmount'] : 0,
        'taxRate' => isset($_POST['taxRate']) ? $_POST['taxRate'] : 0,
        'totalAftertax' => isset($_POST['totalAftertax']) ? $_POST['totalAftertax'] : 0,
        'amountPaid' => isset($_POST['amountPaid']) ? $_POST['amountPaid'] : 0,
        'amountDue' => isset($_POST['amountDue']) ? $_POST['amountDue'] : 0,
        'notes' => isset($_POST['notes']) ? $_POST['notes'] : '',
        'productCode' => isset($_POST['productCode']) ? $_POST['productCode'] : array(),
        'productName' => isset($_POST['productName']) ? $_POST['productName'] : array(),
        'quantity' => isset($_POST['quantity']) ? $_POST['quantity'] : array(),
        'price' => isset($_POST['price']) ? $_POST['price'] : array(),
        'total' => isset($_POST['total']) ? $_POST['total'] : array()
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
