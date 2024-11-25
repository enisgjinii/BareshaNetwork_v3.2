<?php
session_start();
include('partials/header.php');
include 'Invoice_2.php';
$invoice = new Invoice();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['companyName']) && !empty($_POST['invoiceId'])) {
	$invoice->updateInvoice($_POST);
	header("Location: invoice_list_2.php");
	exit();
}

// Fetch invoice data if updating
if (!empty($_GET['update_id'])) {
	$invoiceValues = $invoice->getInvoice($_GET['update_id']);
	$invoiceItems = $invoice->getInvoiceItems($_GET['update_id']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Update Invoice</title>
	<link href="css/style.css" rel="stylesheet">
	<script src="invoice.js" defer></script>
</head>

<body>
	<div class="main-panel">
		<div class="content-wrapper">
			<div class="container-fluid">
				<nav class="bg-white px-2 rounded-5" style="width:fit-content;" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a class="text-reset" style="text-decoration: none;">Faturë e shpejtë</a></li>
						<li class="breadcrumb-item active" aria-current="page"><a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">Edito faturën e shpejtë me id <?= $_GET['update_id'] ?></a></li>
					</ol>
				</nav>
				<form action="" id="invoice-form" method="post" class="invoice-form card p-3 rounded-5" novalidate>
					<input type="hidden" id="currency" value="$">
					<!-- Sender and Receiver Information -->
					<div class="row mb-3">
						<div class="col-md-6">
							<h5>Nga,</h5>
							<p>
								Baresha Music<br>
								Shirokë, Suharekë, KS<br>
								Kosov - 23000<br>
								Telefoni: 00383 (0) 49 605 655<br>
								Email: info@bareshamusic.com<br>
								Tax ID: 811499228
							</p>
						</div>
						<div class="col-md-6">
							<h5>Për,</h5>
							<div class="form-group mb-2">
								<input
									type="text"
									class="form-control form-control-sm rounded-5"
									name="companyName"
									id="companyName"
									placeholder="Company Name"
									value="<?= htmlspecialchars($invoiceValues['order_receiver_name'] ?? '') ?>"
									required>
							</div>
							<div class="form-group mb-2">
								<textarea
									class="form-control form-control-sm rounded-5"
									rows="2"
									name="address"
									id="address"
									placeholder="Your Address"
									required><?= htmlspecialchars($invoiceValues['order_receiver_address'] ?? '') ?></textarea>
							</div>
							<div class="form-group mb-2">
								<input
									type="text"
									class="form-control form-control-sm rounded-5"
									name="mobile"
									id="mobile"
									placeholder="Mobile"
									value="<?= htmlspecialchars($invoiceValues['mobile'] ?? '') ?>"
									required>
							</div>
							<div class="form-group mb-2">
								<input
									type="email"
									class="form-control form-control-sm rounded-5"
									name="email"
									id="email"
									placeholder="Email"
									value="<?= htmlspecialchars($invoiceValues['email'] ?? '') ?>"
									required>
							</div>
							<div class="form-group mb-2">
								<input
									type="text"
									class="form-control form-control-sm rounded-5"
									name="taxId"
									id="taxId"
									placeholder="Tax ID"
									value="<?= htmlspecialchars($invoiceValues['tax_id'] ?? '') ?>"
									required>
							</div>
						</div>
					</div>
					<!-- Invoice Items Table -->
					<div class="table-responsive mb-3">
						<table class="table table-bordered table-sm" id="invoiceItem">
							<thead class="thead-light">
								<tr>
									<th style="width: 5%;"><input id="checkAll" class="form-check-input" type="checkbox"></th>
									<th style="width: 15%;">Artikulli Nr</th>
									<th style="width: 40%;">Emri i artikullit</th>
									<th style="width: 10%;">Sasia</th>
									<th style="width: 15%;">Çmimi</th>
									<th style="width: 15%;">Total</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$count = 0;
								if (!empty($invoiceItems)) {
									foreach ($invoiceItems as $item) {
										$count++;
								?>
										<tr>
											<td><input class="itemRow form-check-input" type="checkbox"></td>
											<td>
												<input
													type="text"
													name="productCode[]"
													class="form-control form-control-sm rounded-5"
													value="<?= htmlspecialchars($item["item_code"]) ?>"
													required>
											</td>
											<td>
												<input
													type="text"
													name="productName[]"
													class="form-control form-control-sm rounded-5"
													value="<?= htmlspecialchars($item["item_name"]) ?>"
													required>
											</td>
											<td>
												<input
													type="number"
													name="quantity[]"
													class="form-control form-control-sm rounded-5 quantity"
													value="<?= htmlspecialchars($item["order_item_quantity"]) ?>"
													min="1"
													required>
											</td>
											<td>
												<input
													type="number"
													name="price[]"
													class="form-control form-control-sm rounded-5 price"
													value="<?= htmlspecialchars($item["order_item_price"]) ?>"
													step="0.01"
													min="0"
													required>
											</td>
											<td>
												<input
													type="number"
													name="total[]"
													class="form-control form-control-sm rounded-5 total"
													value="<?= htmlspecialchars($item["order_item_final_amount"]) ?>"
													step="0.01"
													min="0"
													required>
											</td>
											<input type="hidden" name="itemId[]" value="<?= htmlspecialchars($item['order_item_id']) ?>">
										</tr>
								<?php
									}
								}
								?>
							</tbody>
						</table>
					</div>
					<!-- Add/Remove Buttons -->
					<div class="mb-3">
						<button type="button" class="btn btn-danger btn-sm me-2" id="removeRows">- Fshije</button>
						<button type="button" class="btn btn-success btn-sm" id="addRows">+ Shto më shumë</button>
					</div>
					<!-- Notes and Invoice Totals -->
					<div class="row">
						<!-- Notes Section -->
						<div class="col-lg-8 mb-3">
							<div class="form-group">
								<label for="notes"><strong>Shënime:</strong></label>
								<textarea
									class="form-control form-control-sm rounded-5"
									rows="3"
									name="notes"
									id="notes"
									placeholder="Shënimet tuaja"><?= htmlspecialchars($invoiceValues['note'] ?? '') ?></textarea>
							</div>
							<!-- Hidden Fields and Submit Button -->
							<input type="hidden" name="userId" value="<?= htmlspecialchars($_SESSION['userid'] ?? '') ?>">
							<input type="hidden" name="invoiceId" value="<?= htmlspecialchars($invoiceValues['order_id'] ?? '') ?>">
							<button type="submit" name="invoice_btn" class="btn btn-primary btn-sm">Ruaj faturën</button>
						</div>
						<!-- Invoice Totals Section -->
						<div class="col-lg-4">
							<!-- Qmimi pa TVSH (Subtotal) -->
							<div class="form-group mb-2">
								<label for="subTotal">Qmimi pa TVSH:</label>
								<div class="input-group input-group-sm">
									<span class="input-group-text">$</span>
									<input
										type="number"
										class="form-control rounded-5"
										name="subTotal"
										id="subTotal"
										value="<?= htmlspecialchars($invoiceValues['order_total_before_tax'] ?? '') ?>"
										step="0.01"
										min="0"
										required>
								</div>
							</div>

							<!-- TVSH-ja (Tax Amount) -->
							<div class="form-group mb-2">
								<label for="taxAmount">TVSH-ja:</label>
								<div class="input-group input-group-sm">
									<span class="input-group-text">$</span>
									<input
										type="number"
										class="form-control rounded-5"
										name="taxAmount"
										id="taxAmount"
										value="<?= htmlspecialchars($invoiceValues['order_total_tax'] ?? '') ?>"
										step="0.01"
										min="0"
										required>
								</div>
							</div>

							<!-- Totali (Total after Tax) -->
							<div class="form-group mb-2">
								<label for="totalAftertax">Totali:</label>
								<div class="input-group input-group-sm">
									<span class="input-group-text">$</span>
									<input
										type="number"
										class="form-control rounded-5"
										name="totalAftertax"
										id="totalAftertax"
										value="<?= htmlspecialchars($invoiceValues['order_total_after_tax'] ?? '') ?>"
										step="0.01"
										min="0"
										required>
								</div>
							</div>

							<!-- Existing Totals Fields -->
							<div class="form-group mb-2">
								<label for="amountPaid">Shuma e paguar:</label>
								<div class="input-group input-group-sm">
									<span class="input-group-text">$</span>
									<input
										type="number"
										class="form-control rounded-5"
										name="amountPaid"
										id="amountPaid"
										value="<?= htmlspecialchars($invoiceValues['order_amount_paid'] ?? '') ?>"
										step="0.01"
										min="0"
										required>
								</div>
							</div>
							<div class="form-group mb-2">
								<label for="amountDue">Shuma e detyrimit:</label>
								<div class="input-group input-group-sm">
									<span class="input-group-text">$</span>
									<input
										type="number"
										class="form-control rounded-5"
										name="amountDue"
										id="amountDue"
										value="<?= htmlspecialchars($invoiceValues['order_total_amount_due'] ?? '') ?>"
										step="0.01"
										min="0"
										required>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<?php include 'partials/footer.php'; ?>
</body>

</html>