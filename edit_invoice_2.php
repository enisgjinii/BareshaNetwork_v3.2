<?php
session_start();
include('partials/header.php');
include 'Invoice_2.php';
$invoice = new Invoice();
// $invoice->checkLoggedIn();
if (!empty($_POST['companyName']) && $_POST['companyName'] && !empty($_POST['invoiceId']) && $_POST['invoiceId']) {
	$invoice->updateInvoice($_POST);
	header("Location:invoice_list_2.php");
}
if (!empty($_GET['update_id']) && $_GET['update_id']) {
	$invoiceValues = $invoice->getInvoice($_GET['update_id']);
	$invoiceItems = $invoice->getInvoiceItems($_GET['update_id']);
}
?>
<script src="invoice.js"></script>
<link href="css/style.css" rel="stylesheet">
<?php include('inc/container.php'); ?>
<div class="container content-invoice">
	<form action="" id="invoice-form" method="post" class="invoice-form card p-3" role="form" novalidate="">
		<div class="load-animate animated fadeInUp">
			<!-- <div class="row">
				<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
					<?php //include('menu_2.php'); 
					?>
				</div>
			</div> -->
			<input id="currency" type="hidden" value="$">
			<div class="row">
				<div class="col-xs-12 col-sm-4 col-md-6 col-lg-6">
					<h3>Nga,</h3>
					Baresha Music
					<br>
					Shirokë <br>
					Suharekë,KS<br>
					Kosov - 23000 <br>
					Telefoni : 00383 (0) 49 605 655<br>
					Email: info@bareshamusic.com<br>
					Tax ID : 811499228
				</div>
				<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 pull-right">
					<h3>Për,</h3>
					<div class="form-group">
						<input value="<?php echo $invoiceValues['order_receiver_name']; ?>" type="text" class="form-control rounded-5 border border-1" name="companyName" id="companyName" placeholder="Company Name" autocomplete="off">
					</div>
					<div class="form-group">
						<textarea class="form-control rounded-5 border border-1" rows="3" name="address" id="address" placeholder="Your Address"><?php echo $invoiceValues['order_receiver_address']; ?></textarea>
					</div>
					<div class="form-group">
						<!-- Telephone number -->
						<input value="<?php echo $invoiceValues['mobile']; ?>" type="text" class="form-control rounded-5 border border-1" name="mobile" id="mobile" placeholder="Mobile" autocomplete="off">
					</div>
					<!-- Email -->
					<div class="form-group">
						<input value="<?php echo $invoiceValues['email']; ?>" type="text" class="form-control rounded-5 border border-1" name="email" id="email" placeholder="Email" autocomplete="off">
					</div>
					<!-- Tax ID -->
					<div class="form-group">
						<input value="<?php echo $invoiceValues['tax_id']; ?>" type="text" class="form-control rounded-5 border border-1" name="taxId" id="taxId" placeholder="Tax ID" autocomplete="off">
					</div>

				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<table class="table table-bordered table-hover" id="invoiceItem">
						<tr>
							<th width="2%"><input id="checkAll" class="formcontrol" type="checkbox"></th>
							<th width="15%">Artikulli Nr</th>
							<th width="38%">Emri i artikullit</th>
							<th width="15%">Sasia</th>
							<th width="15%">Çmimi</th>
							<th width="15%">Total</th>
						</tr>
						<?php
						$count = 0;
						foreach ($invoiceItems as $invoiceItem) {
							$count++;
						?>
							<tr>
								<td><input class="itemRow" type="checkbox"></td>
								<td><input type="text" value="<?php echo $invoiceItem["item_code"]; ?>" name="productCode[]" id="productCode_<?php echo $count; ?>" class="form-control rounded-5 border border-1" autocomplete="off"></td>
								<td><input type="text" value="<?php echo $invoiceItem["item_name"]; ?>" name="productName[]" id="productName_<?php echo $count; ?>" class="form-control rounded-5 border border-1" autocomplete="off"></td>
								<td><input type="number" value="<?php echo $invoiceItem["order_item_quantity"]; ?>" name="quantity[]" id="quantity_<?php echo $count; ?>" class="form-control rounded-5 border border-1 quantity" autocomplete="off"></td>
								<td><input type="number" value="<?php echo $invoiceItem["order_item_price"]; ?>" name="price[]" id="price_<?php echo $count; ?>" class="form-control rounded-5 border border-1 price" autocomplete="off"></td>
								<td><input type="number" value="<?php echo $invoiceItem["order_item_final_amount"]; ?>" name="total[]" id="total_<?php echo $count; ?>" class="form-control rounded-5 border border-1 total" autocomplete="off"></td>
								<input type="hidden" value="<?php echo $invoiceItem['order_item_id']; ?>" class="form-control" name="itemId[]">
							</tr>
						<?php } ?>
					</table>
				</div>
			</div>
			<div class="row my-3">
				<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
					<button class="input-custom-css px-3 py-2 delete input-custom-css px-3 py-2" id="removeRows" type="button">- Fshije</button>
					<button class="input-custom-css px-3 py-2" id="addRows" type="button">+ Shto më shumë</button>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
					<h5>Shënime: </h5>
					<div class="form-group">
						<textarea class="form-control rounded-5 border border-1 txt" rows="5" name="notes" id="notes" placeholder="Shënimet tuaja"><?php echo $invoiceValues['note']; ?></textarea>
					</div>
					<div class="form-group">
						<input type="hidden" value="<?php echo $_SESSION['userid']; ?>" class="form-control rounded-5 border border-1" name="userId">
						<input type="hidden" value="<?php echo $invoiceValues['order_id']; ?>" class="form-control rounded-5 border border-1" name="invoiceId" id="invoiceId">
						<input data-loading-text="Përditësimi i faturës..." type="submit" name="invoice_btn" value="Ruaj faturën" class=" input-custom-css px-3 py-2 submit_btn invoice-save-btm">
					</div>

				</div>
				<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
					<span class="form-inline">
						<div class="form-group">
							<label>Nëntotali: &nbsp;</label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text currency">$</span>
								</div>
								<input value="<?php echo $invoiceValues['order_total_before_tax']; ?>" type="number" class="form-control rounded-5 border border-1" name="subTotal" id="subTotal" placeholder="Nëntotali">
							</div>
						</div>
						<div class="form-group">
							<label>Norma e tatimit: &nbsp;</label>
							<div class="input-group">
								<input value="<?php echo $invoiceValues['order_tax_per']; ?>" type="number" class="form-control rounded-5 border border-1" name="taxRate" id="taxRate" placeholder="Norma e tatimit">
								<div class="input-group-append">
									<span class="input-group-text">%</span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label>Shuma e tatimit: &nbsp;</label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text currency">$</span>
								</div>
								<input value="<?php echo $invoiceValues['order_total_tax']; ?>" type="number" class="form-control rounded-5 border border-1" name="taxAmount" id="taxAmount" placeholder="Shuma e tatimit">
							</div>
						</div>
						<div class="form-group">
							<label>Total: &nbsp;</label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text currency">$</span>
								</div>
								<input value="<?php echo $invoiceValues['order_total_after_tax']; ?>" type="number" class="form-control rounded-5 border border-1" name="totalAftertax" id="totalAftertax" placeholder="Total">
							</div>
						</div>
						<div class="form-group">
							<label>Shuma e paguar: &nbsp;</label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text currency">$</span>
								</div>
								<input value="<?php echo $invoiceValues['order_amount_paid']; ?>" type="number" class="form-control rounded-5 border border-1" name="amountPaid" id="amountPaid" placeholder="Shuma e paguar">
							</div>
						</div>
						<div class="form-group">
							<label>Shuma e detyrimit: &nbsp;</label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text currency">$</span>
								</div>
								<input value="<?php echo $invoiceValues['order_total_amount_due']; ?>" type="number" class="form-control rounded-5 border border-1" name="amountDue" id="amountDue" placeholder="Shuma e detyrimit">
							</div>
						</div>
					</span>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</form>
</div>
</div>
<?php include('inc/footer.php'); ?>