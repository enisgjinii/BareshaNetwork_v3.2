<?php
session_start();
include 'Invoice_2.php';
$invoice = new Invoice();
// $invoice->checkLoggedIn();
if (!empty($_GET['invoice_id']) && $_GET['invoice_id']) {
	$invoiceValues = $invoice->getInvoice($_GET['invoice_id']);
	$invoiceItems = $invoice->getInvoiceItems($_GET['invoice_id']);
}

$months = array(
	'Jan' => 'Janar',
	'Feb' => 'Shkurt',
	'Mar' => 'Mars',
	'Apr' => 'Prill',
	'May' => 'Maj',
	'Jun' => 'Qershor',
	'Jul' => 'Korrik',
	'Aug' => 'Gusht',
	'Sep' => 'Shtator',
	'Oct' => 'Tetor',
	'Nov' => 'Nëntor',
	'Dec' => 'Dhjetor'
);

$orderDate = date("d M Y, H:i:s", strtotime($invoiceValues['order_date']));
$shortMonth = date("M", strtotime($invoiceValues['order_date']));
$albanianMonth = $months[$shortMonth];
$invoiceDate = str_replace($shortMonth, $albanianMonth, $orderDate);
?>
<!DOCTYPE html>
<html lang="sq">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Fatura</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
	<style>
		/* Custom CSS for invoice */
		.company-logo {
			max-width: 150px;
			margin-bottom: 20px;
		}

		.invoice-header {
			background-color: #f8f9fa;
			padding: 20px;
		}

		.invoice-header h2 {
			margin-bottom: 0;
		}

		.invoice-header p {
			margin-bottom: 5px;
		}

		.invoice-details {
			margin-top: 30px;
			margin-bottom: 20px;
		}

		.invoice-details p {
			margin-bottom: 5px;
		}

		.invoice-table th,
		.invoice-table td {
			vertical-align: middle;
		}

		.invoice-total {
			font-weight: bold;
		}

		/* use media print */
		@media print {
			* {
				margin: 0;
				border: none;
			}

			.card {
				border-style: none;
			}
		}
	</style>
</head>

<body>
	<div class="container-fluid">
		<div class="row justify-content-center mt-5">
			<div class="col-md-8">
				<div class="card">
					<div class="invoice-header text-center">
						<img src="images/logob.png" alt="Company Logo" class="company-logo">
						<h2 class="mb-0">Fatura - <?php echo $invoiceValues['invoice_number']; ?></h2>
						<p>Shërbimi i ofruar nga: Baresha Music</p> <!-- Add your company name here -->
						<p>Numri i telefonit: +383 (0) 49 605 655</p>
						<p>Tax ID : 811499228</p>
					</div>
					<hr>
					<div class="card-body">
						<div class="row">
							<div class="col-md-6">
								<p class="mb-1">Për,</p>
								<p class="mb-1"><strong>MARRËSI (FATURA PËR)</strong></p>
								<p class="mb-1">Emri: <?php echo $invoiceValues['order_receiver_name']; ?></p>
								<p class="mb-1">Adresa e faturimit: <?php echo $invoiceValues['order_receiver_address']; ?></p>
							</div>
							<div class="col-md-6">
								<p class="mb-1">Numri i faturës: <?php echo $invoiceValues['invoice_number']; ?></p>
								<p class="mb-1">ID e faturës: <?php echo $invoiceValues['order_id']; ?></p>
								<p class="mb-1">Data e faturës: <?php echo $invoiceDate; ?></p>
								<p class="mb-1"> Numri i telefonit: <?php echo $invoiceValues['mobile']; ?></p>
								<p class="mb-1"> Email: <?php echo $invoiceValues['email']; ?></p>
								<p class="mb-1">Tax ID: <?php echo $invoiceValues['tax_id']; ?></p>
							</div>
						</div>
						<div class="invoice-details">
							<p class="mb-1"><strong>Detajet e faturës:</strong></p>
							<!-- <p class="mb-1">Nën-totali: <?php echo $invoiceValues['order_total_before_tax']; ?></p> -->
							<!-- Add more invoice details here -->
						</div>
						<div class="table-responsive mt-3">
							<table class="table table-bordered invoice-table">
								<thead>
									<tr>
										<th>Nr.</th>
										<th>Kodi i Artikullit</th>
										<th>Emri i Artikullit</th>
										<th>Sasia</th>
										<th>Çmimi</th>
										<th>Shuma Aktuale</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$count = 0;
									foreach ($invoiceItems as $invoiceItem) {
										$count++;
										echo '
                                        <tr>
                                            <td>' . $count . '</td>
                                            <td>' . $invoiceItem["item_code"] . '</td>
                                            <td>' . $invoiceItem["item_name"] . '</td>
                                            <td>' . $invoiceItem["order_item_quantity"] . '</td>
                                            <td>' . $invoiceItem["order_item_price"] . '</td>
                                            <td>' . $invoiceItem["order_item_final_amount"] . '</td>
                                        </tr>';
									}
									?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="5" class="text-end invoice-total">Totali:</td>
										<td><?php echo $invoiceValues['order_total_after_tax']; ?></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>

</html>