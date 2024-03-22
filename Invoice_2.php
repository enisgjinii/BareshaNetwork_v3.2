<?php
class Invoice
{
	private $host  = '198.38.83.75';
	private $user  = 'bareshao_f';
	private $password   = "prishtin134?";
	private $database  = "bareshao_f";
	private $invoiceUserTable = 'invoice_user';
	private $invoiceOrderTable = 'invoice_order';
	private $invoiceOrderItemTable = 'invoice_order_item';

	private $userTable = 'users_quick_invoice';
	private $dbConnect = false;
	public function __construct()
	{
		if (!$this->dbConnect) {
			$conn = new mysqli($this->host, $this->user, $this->password, $this->database);
			if ($conn->connect_error) {
				die("Error failed to connect to MySQL: " . $conn->connect_error);
			} else {
				$this->dbConnect = $conn;
			}
		}
	}
	private function getData($sqlQuery)
	{
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		if (!$result) {
			die('Error in query: ' . mysqli_error());
		}
		$data = array();
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$data[] = $row;
		}
		return $data;
	}
	private function getNumRows($sqlQuery)
	{
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		if (!$result) {
			die('Error in query: ' . mysqli_error());
		}
		$numRows = mysqli_num_rows($result);
		return $numRows;
	}
	public function loginUsers($email, $password)
	{
		$sqlQuery = "
			SELECT id, email, first_name, last_name, address, mobile 
			FROM " . $this->invoiceUserTable . " 
			WHERE email='" . $email . "' AND password='" . $password . "'";
		return  $this->getData($sqlQuery);
	}
	public function checkLoggedIn()
	{
		if (!$_SESSION['userid']) {
			header("Location:index.php");
		}
	}
	public function saveInvoice($postData)
	{
		// Extract data from $postData for easier access
		$userId = isset($_COOKIE['user_id']) ? $_COOKIE['user_id'] : ''; // Get userId from cookie
		$companyName = $postData['companyName'];
		$address = $postData['address'];
		$mobile = $postData['mobile'];
		$email = $postData['email'];
		$taxId = $postData['taxId'];
		$subTotal = $postData['subTotal'];
		$taxAmount = $postData['taxAmount'];
		$taxRate = $postData['taxRate'];
		$totalAfterTax = $postData['totalAftertax'];
		$amountPaid = $postData['amountPaid'];
		$amountDue = $postData['amountDue'];
		$notes = $postData['notes'];
		$productCodes = $postData['productCode'];
		$productNames = $postData['productName'];
		$quantities = $postData['quantity'];
		$prices = $postData['price'];
		$totals = $postData['total'];

		// Generate invoice number in the format "BN-{randomNumber-Actualdate)"
		$invoiceNumber = "BN-" . rand(1000, 9999) . "-" . date("Ymd");

		// Insert invoice order data
		$sqlInsertOrder = "INSERT INTO {$this->invoiceOrderTable} (user_id, invoice_number, order_receiver_name, order_receiver_address, 
		mobile, email, tax_id, order_total_before_tax, order_total_tax, order_tax_per, order_total_after_tax, order_amount_paid, order_total_amount_due, note) 
                      VALUES ('$userId', '$invoiceNumber', '$companyName', '$address', '$mobile', '$email', '$taxId', '$subTotal', '$taxAmount', '$taxRate', '$totalAfterTax', '$amountPaid', '$amountDue', '$notes')";
		$insertOrderResult = mysqli_query($this->dbConnect, $sqlInsertOrder);
		$lastInsertId = mysqli_insert_id($this->dbConnect);

		// Insert user informations 
		$sqlInsertUser = "INSERT INTO {$this->userTable} (user_id, name, address, mobile, email) VALUES ('$userId', '$companyName', '$address', '$mobile', '$email')";

		// Insert invoice order item data
		foreach ($productCodes as $key => $productCode) {
			$productName = $productNames[$key];
			$quantity = $quantities[$key];
			$price = $prices[$key];
			$total = $totals[$key];

			$sqlInsertItem = "INSERT INTO {$this->invoiceOrderItemTable} (order_id, item_code, item_name, order_item_quantity, order_item_price, order_item_final_amount) 
                         VALUES ('$lastInsertId', '$productCode', '$productName', '$quantity', '$price', '$total')";
			mysqli_query($this->dbConnect, $sqlInsertItem);
		}
	}


	public function updateInvoice($postData)
	{
		// Extract data from $postData for easier access
		$invoiceId = $postData['invoiceId'];
		$companyName = $postData['companyName'];
		$address = $postData['address'];
		$mobile = $postData['mobile'];
		$email = $postData['email'];
		$taxId = $postData['taxId'];
		$subTotal = $postData['subTotal'];
		$taxAmount = $postData['taxAmount'];
		$taxRate = $postData['taxRate'];
		$totalAfterTax = $postData['totalAftertax'];
		$amountPaid = $postData['amountPaid'];
		$amountDue = $postData['amountDue'];
		$notes = $postData['notes'];
		$productCodes = $postData['productCode'];
		$productNames = $postData['productName'];
		$quantities = $postData['quantity'];
		$prices = $postData['price'];
		$totals = $postData['total'];

		// Update invoice order data
		$sqlUpdateOrder = "UPDATE {$this->invoiceOrderTable} 
                      SET order_receiver_name = '$companyName', 
						  mobile = '$mobile',
						  email = '$email',
						  tax_id = '$taxId',
                          order_receiver_address = '$address', 
                          order_total_before_tax = '$subTotal', 
                          order_total_tax = '$taxAmount', 
                          order_tax_per = '$taxRate', 
                          order_total_after_tax = '$totalAfterTax', 
                          order_amount_paid = '$amountPaid', 
                          order_total_amount_due = '$amountDue', 
                          note = '$notes' 
                      WHERE order_id = '$invoiceId'";
		mysqli_query($this->dbConnect, $sqlUpdateOrder);

		// Delete existing invoice items
		$this->deleteInvoiceItems($invoiceId);

		// Insert updated invoice items
		for ($i = 0; $i < count($productCodes); $i++) {
			$sqlInsertItem = "INSERT INTO {$this->invoiceOrderItemTable} (order_id, item_code, item_name, order_item_quantity, order_item_price, order_item_final_amount) 
                          VALUES ('$invoiceId', '{$productCodes[$i]}', '{$productNames[$i]}', '{$quantities[$i]}', '{$prices[$i]}', '{$totals[$i]}')";
			mysqli_query($this->dbConnect, $sqlInsertItem);
		}
	}

	public function getInvoiceList()
	{
		$sqlQuery = "SELECT * FROM " . $this->invoiceOrderTable;
		return $this->getData($sqlQuery);
	}

	public function getInvoice($invoiceId)
	{
		$sqlQuery = "
			SELECT * FROM " . $this->invoiceOrderTable . " 
			WHERE  order_id = '$invoiceId'";
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		return $row;
	}
	public function getInvoiceItems($invoiceId)
	{
		$sqlQuery = "
			SELECT * FROM " . $this->invoiceOrderItemTable . " 
			WHERE order_id = '$invoiceId'";
		return  $this->getData($sqlQuery);
	}
	public function deleteInvoiceItems($invoiceId)
	{
		$sqlQuery = "
			DELETE FROM " . $this->invoiceOrderItemTable . " 
			WHERE order_id = '" . $invoiceId . "'";
		mysqli_query($this->dbConnect, $sqlQuery);
	}
	public function deleteInvoice($invoiceId)
	{
		// Delete related records from invoice_order_item table
		$this->deleteInvoiceItems($invoiceId);

		// Delete record from invoice_order table
		$sqlQuery = "
        DELETE FROM " . $this->invoiceOrderTable . " 
        WHERE order_id = '" . $invoiceId . "'";
		mysqli_query($this->dbConnect, $sqlQuery);

		return 1;
	}
}
