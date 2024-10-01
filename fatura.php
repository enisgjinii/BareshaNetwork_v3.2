<?php
include 'conn-d.php';

$id = $_GET['invoice'];

// Retrieve invoice data
$invoiceQuery = $conn->query("SELECT f.*, k.emri as klient_emri, k.adresa, k.emailadd, k.nrtel
                               FROM fatura f
                               INNER JOIN klientet k ON f.emri = k.id
                               WHERE f.fatura='$id'");
$invoiceData = mysqli_fetch_array($invoiceQuery);

// Retrieve total sales
$totalSalesQuery = $conn->query("SELECT SUM(totali) as total, SUM(mbetja) as mbetja
                                 FROM shitje
                                 WHERE fatura='$id'");
$totalSales = mysqli_fetch_array($totalSalesQuery);

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<!-- Google Fonts -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
	<!-- MDB -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.css">
	<!-- UIcons -->
	<link rel="stylesheet" href="assets/uicons-regular-rounded/css/uicons-regular-rounded.css">
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
	<style>
		* {
			font-family: 'Inter', sans-serif;
		}

		body {
			margin: 0;
			padding: 0;
			font-family: 'Roboto', sans-serif;
			background-color: #f0f0f0;
		}

		.container {
			max-width: 800px;
			margin: 20px auto;
			padding: 20px;
			background-color: #fff;
			border-radius: 5px;
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
		}

		.header {
			text-align: left;
		}

		.header h1 {
			margin-bottom: 10px;
		}

		.logo {
			text-align: left;
			margin-top: 20px;
		}

		.logo img {
			width: 100px;
		}

		.address {
			text-align: left;

		}

		.address p {
			margin: 5px 0;
		}

		.customer-info {
			margin-top: 20px;
		}

		.invoice-details {
			margin-top: 20px;
		}

		.invoice-details .row {
			justify-content: space-between;
		}

		.sales-table {
			margin-top: 20px;
			width: 100%;
			border-collapse: collapse;
		}

		.sales-table th,
		.sales-table td {
			border: 1px solid #ddd;
			padding: 10px;
			text-align: left;
		}

		.sales-table th {
			background-color: #f2f2f2;
		}

		.total {
			margin-top: 10px;
			text-align: right;
			font-weight: bold;
		}

		.btn-container {
			text-align: right;
			margin-top: 20px;
		}

		@media print {
			.container {
				max-width: 1900px;
				margin: 0px auto;
				padding: 0px;
				background-color: #fff;
				border-radius: 0px;
				box-shadow: 0 0px 0px rgba(0, 0, 0, 0.2);
			}

			.btn-container {
				display: none;
			}
		}
	</style>
	<title>Fatura - <?php echo $_GET['invoice']; ?></title>
</head>

<body>

	<div class="btn-container fixed-top px-3 py-2 bg-light rounded-5 border ms-1" style="width: fit-content;">
		<a href="faturat.php" class="btn btn-sm btn-light border shadow-0 rounded-5" style="text-transform: none;"><i class="fa fa-angle-left me-2"></i>Kthehu</a>
		<a href="javascript:window.print()" style="text-transform: none;" class="btn btn-sm btn-success shadow-0 rounded-5"><i class="fa fa-print"></i> Printo</a>

		<?php if (!empty($invoiceData['emailadd'])) { ?>
			<button type="button" class="btn btn-sm btn-primary shadow-0 rounded-5" style="text-transform: none;" data-mdb-toggle="modal" data-mdb-target="#dergoFaturen">
				<i class="fi fi-rr-paper-plane"></i>
				D&euml;rgo</button>
		<?php } else {  ?>

			<a href="#" class="btn btn-sm btn-primary disabled shadow-0 rounded-5" style="text-transform: none;">
				<i class="fi fi-rr-paper-plane"></i>
				D&euml;rgo</a>
		<?php } ?>
	</div>

	<!-- Modal -->


	<script>
		// JavaScript to copy the email into the input field and paragraph
		document.addEventListener('DOMContentLoaded', function() {
			// Replace 'invoiceData.emailadd' with the actual PHP variable that holds the email address
			var recipientEmail = "<?php echo $invoiceData['emailadd']; ?>";
			var invoiceId = "<?php echo $_GET['invoice']; ?>";

			// Update the paragraph and input field with the recipient's email
			document.getElementById('recipient-email').textContent = recipientEmail;
			document.getElementById('to').value = recipientEmail;

			document.getElementById('subject').textContent = invoiceId;
			document.getElementById('subject').value = 'Fatura juaj nga Baresha Network , #' + invoiceId;
		});
	</script>

	<div class="container">
		<div class="row">
			<div class="logo text-left">
				<img src="images/brand-icon.png" alt="Company Logo">
			</div>
			<div class="col text-start">
				<h4 class="text-muted text-left my-3">Baresha Network</h4>
				<div class="address ">
					<p><i class="fi fi-rr-marker pe-2"></i> 8RVC+762, R118, Shiroke, Suhareke</p>
					<p><i class="fi fi-rr-envelope pe-2"></i> info@bareshamusic.com</p>
					<p><i class="fi fi-rr-phone-call pe-2"></i> +383 (049) 605 655</p>
				</div>
			</div>
			<div class="col text-end">
				<h4 class="text-muted text-left my-3">Numri i fatur&euml;s </h4>
				<div class="address text-end">
					<p># <?php echo $id; ?></p>
				</div>
			</div>
		</div>
		<hr style="border: 1px dashed red;">
		<div class="row">
			<div class="col">
				<div>
					<?php if (!empty($invoiceData['klient_emri'])) : ?>
						<p class="text-muted m-0 p-0" style="font-size: 12px;">Faturuar p&euml;r :</p>
						<h6 class="text-dark"><?php echo $invoiceData['klient_emri']; ?></h6>
					<?php endif; ?>
					<?php if (!empty($invoiceData['adresa'])) : ?>
						<p class="text-muted m-0 p-0" style="font-size: 12px;">Adresa :</p>
						<h6 class="text-dark"><?php echo $invoiceData['adresa']; ?></h6>
					<?php endif; ?>
					<?php if (!empty($invoiceData['emailadd'])) : ?>
						<p class="text-muted m-0 p-0" style="font-size: 12px;">Email-i :</p>
						<h6 class="text-dark"><?php echo $invoiceData['emailadd']; ?></h6>
					<?php endif; ?>
					<?php if (!empty($invoiceData['nrtel'])) : ?>
						<p class="text-muted m-0 p-0" style="font-size: 12px;">Numri i telefonit :</p>
						<h6 class="text-dark"><?php echo $invoiceData['nrtel']; ?></h6>
					<?php endif; ?>
				</div>
			</div>

			<div class="col text-end">


				<p class="text-muted m-0 p-0" style="font-size: 12px;">Numri i fatur&euml;s :</p>
				<h6 class="text-dark"><?php echo $id; ?></h6>


				<p class="text-muted m-0 p-0" style="font-size: 12px;">Data e fatur&euml;s :</p>
				<h6 class="text-dark"><?php echo $invoiceData['data']; ?></h6>


				<p class="text-muted m-0 p-0" style="font-size: 12px;">Numri rendit&euml;s :</p>
				<h6 class="text-dark"><?php echo $invoiceData['id']; ?></h6>



			</div>
		</div>
		<table class="sales-table">
			<thead>
				<tr>
					<th>No.</th>
					<th>Em&euml;rtimi</th>
					<th>Çmimi</th>
					<th>Perqindja</th>
					<th>Shuma</th>
					<th>Mbetja</th>
					<th class="text-right">Totali</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$rendori = 1;
				$salesQuery = $conn->query("SELECT * FROM shitje WHERE fatura='$id'");
				while ($row_shitje = $salesQuery->fetch_assoc()) {
				?>
					<tr>
						<td><?php echo $rendori . "."; ?></td>
						<td>
							<h6><?php echo $row_shitje['emertimi']; ?></h6>
							<?php if (!empty($row_shitje['kengetari'])) : ?>
								<p class="text-muted m-0 p-0" style="font-size: 12px;">Kengetari/ja: <?php echo $row_shitje['kengetari']; ?></p>
							<?php endif; ?>
						</td>
						<td><?php echo $row_shitje['qmimi']; ?>€</td>
						<td><?php echo $row_shitje['perqindja']; ?>%</td>
						<td><?php echo $row_shitje['klientit']; ?>€</td>
						<td><?php echo $row_shitje['mbetja']; ?>€</td>
						<td class="text-right"><?php echo $row_shitje['totali']; ?>€</td>
					</tr>
				<?php
					$rendori++;
				} ?>
			</tbody>
		</table>
		<hr style="border: 1px dashed red;">
		<div class="total">
			<h4>Totali : <?php echo $totalSales['total']; ?> €</h4>
		</div>


	</div>
	<!-- MDB -->
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.1/mdb.min.js"></script>
</body>

</html>