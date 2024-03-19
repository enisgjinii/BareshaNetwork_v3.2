<?php
include 'conn-d.php';
$id = $_GET['invoice'];
$resultas = $conn->query("SELECT SUM(`totali`) as `sum` FROM `shitjeplatforma` WHERE fatura='$id'");
$rowas = mysqli_fetch_array($resultas);
$fgfg = $rowas['sum'];
$resultas = $conn->query("SELECT SUM(`mbetja`) as `sum` FROM `shitjeplatforma` WHERE fatura='$id'");
$rowass = mysqli_fetch_array($resultas);
$fgfgg = $rowass['sum'];

?>

<?php
$gin = $conn->query("SELECT * FROM faturaplatformes WHERE fatura='$id'");
$ginfo = mysqli_fetch_array($gin);
$stafi =  $ginfo['emri'];
$gstai = $conn->query("SELECT * FROM klientet WHERE id='$stafi'");
$gstai2 = mysqli_fetch_array($gstai);
?>
<html>

<head>
    <!-- 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 -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatura - <?php echo $_GET['invoice']; ?></title>

    <!-- <script language="javascript">
		function Clickheretoprint() {
			var disp_setting = "toolbar=yes,location=no,directories=yes,menubar=yes,";
			disp_setting += "scrollbars=yes,width=1000, height=500, left=100, top=25";
			var content_vlue = document.getElementById("content").innerHTML;

			var docprint = window.open("", "", disp_setting);
			docprint.document.open();
			docprint.document.write('</head><body onLoad="self.print()" style="width: 1000px; font-size:11px; font-family:arial; font-weight:normal;">');
			docprint.document.write(content_vlue);
			docprint.document.close();
			docprint.focus();
		}
	</script> -->
    <!-- Z -->

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.css" rel="stylesheet" />

    <!-- UIcons -->
    <link rel="stylesheet" href="assets/uicons-regular-rounded/css/uicons-regular-rounded.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" integrity="sha512-qZvrmS2ekKPF2mSznTQsxqPgnpkI4DNTlrdUmTzrDgektczlKNRRhy5X5AAOnx5S09ydFYWWNSfcEqDTTHgtNA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>

</head>

<body>
    <div class="container my-3">




        <div style="display: inline-block;">
            <button id="print-button" type="button" class="btn btn-light border my-3" data-mdb-ripple-color="dark" style="text-transform:none;"><i class="fi fi-rr-print fa-xl me-2"></i>Printo</button>
            <button id="send-email-btn" type="button" class="btn btn-light border my-3" data-mdb-toggle="modal" data-mdb-target="#email-modal" data-mdb-ripple-color="dark" style="text-transform:none;"><i class="fi fi-rr-paper-plane fa-xl me-2"></i>D&euml;rgo</button>
            <button class="btn btn-light border my-3 dropdown-toggle" type="button" id="dropdownMenuButton" data-mdb-toggle="dropdown" aria-expanded="false">
                <i class="fi fi-rr-download fa-xl me-2"></i>Shkarko
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item" href="#" id="download-pdf-link"><i class="fi fi-rr-file-pdf fa-lg me-2"></i>PDF</a></li>
                <li><a id="download-excel-btn" class="dropdown-item" type="button">
                        <i class="fi fi-rr-file-excel fa-lg me-2"></i>
                        Excel</a> </li>
            </ul>
        </div>






        <!-- Modal -->
        <div class="modal fade" id="email-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Dergo fatur&euml;n n&euml; email t&euml; klientit</h5>
                        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <!-- <form id="my-form" action="send_email.php" method="POST" enctype="multipart/form-data">
							<label for="recipient">Email-i i klientit:</label>
							<input type="email" id="recipient" name="recipient" class="form-control rounded-5 shadow-1">
							<label for="subject">Subjekti:</label>
							<input type="text" id="subject" name="subject" class="form-control rounded-5 shadow-1">
							<label for="message">Mesazhi:</label>
							<textarea id="message" name="message" class="form-control" height="250px"></textarea>
							<label for="file">File:</label>
							<input type="file" id="file" name="file" class="form-control">
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
								<input type="submit" value="Send Email" class="btn btn-primary">
							</div>
						</form> -->

                    </div>
                </div>
            </div>
        </div>

        <div class="row gap-2 border rounded-3 p-2 shadow-2">
            <div class="col-12 text-center">
                <img src="images/logob.png" width="150" class="mt-3"><br>
            </div>

            <div class="col p-2 border rounded-3">
                <b>Te dh&euml;nat e kompanis&euml; "Baresha"</b>
                <hr>
                <p> Numri i telefonit : +383 (049) 605 655</p>
            </div>
            <div class="col p-2 border rounded-3 text-end">
                <b>Te dh&euml;nat e fatur&euml;s "<?php echo $_GET['invoice']; ?>"</b>
                <hr>
                <table class="table table-bordered" id="tabelaKlientit">
                    <tr>
                        <td>Numri identifikues i fatures</td>
                        <td class="text-end"><?php echo $_GET['invoice']; ?></td>
                    </tr>
                    <tr>
                        <td>Emri i klientit</td>
                        <td class="text-end"><?php echo $gstai2['emri']; ?></td>
                    <tr>
                        <td>Data e fatur&euml;s</td>
                        <td class="text-end"><?php echo $ginfo['data']; ?></td>
                    </tr>
                    <tr>
                        <td>Adresa</td>
                        <td class="text-end"><?php echo $gstai2['adresa']; ?></td>
                    </tr>
                    <tr>
                        <td>Numri identifikues i biznesit</td>
                        <td class="text-end"><?php echo $gstai2['emailadd']; ?></td>
                    </tr>
                    <tr>
                        <td>Data</td>
                        <td class="text-end"><?php echo date('d-m-Y'); ?></td>
                    </tr>
                    <tr>
                        <td>Mbetja</td>
                        <td class="text-end"><?php echo $fgfgg; ?> €</td>
                    </tr>
                    <tr>
                        <td>Vlera p&euml;r pages&euml;</td>
                        <td class="text-end"><?php echo $fgfg - 4 ; ?> €</td>
                    </tr>
                </table>
            </div>


        </div>

        <div class="row gap-2 mt-3 border rounded-2 p-2">
            <div class="col">
                <table class="table table-bordered caption-top" id="tableShitjeve">
                    <caption>
                        Lista me te dh&euml;nat e shitjeve
                    </caption>
                    <thead class="bg-light">
                        <tr>
                            <th>Nr.</th>
                            <th>Emertimi</th>
                            <th>Çmimi</th>
                            <th>P&euml;rqindja</th>
                            <th>Perqindja nga banka</th>
                            <th>Perqindja totale</th>
                            <th>Shuma</th>
                            <th>Mbetja</th>
                            <th>Totali</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $rendori = 1;
                        $stmt = $conn->prepare("SELECT * FROM shitjeplatforma WHERE fatura=?");
                        $stmt->bind_param('s', $id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_array()) {
                        ?>
                            <tr>
                                <td><?php echo $rendori . "."; ?></td>
                                <td><?php echo $row['emertimi']; ?></td>
                                <td><?php echo $row['qmimi']; ?></td>
                                <td><?php echo $row['perqindja']; ?>%</td>
                                <td><?php echo $row['perqindjaendalurPlatforma'] ?></td>
                                <td><?php echo $row['perqindjaTotale']; ?></td>
                                <td><?php echo $row['klientit']; ?></td>
                                <td><?php echo $row['mbetja']; ?></td>
                                <td><?php echo $row['totali']; ?> &euro;</td>
                            <?php
                            $rendori++;
                        }
                        $stmt->close();
                            ?>

                            <tr class="bg-light">
                                <td colspan="8" class="text-end"><b>Totali</b></td>
                                <td><b><?php echo $fgfg; ?> &euro;</b></td>
                            </tr>
                            <tr class="bg-light">
                                <td colspan="8" class="text-end"><b>Paypal</b></td>
                                <td><b>4 &euro;</b></td>

                            </tr>
                            <tr class="bg-light">
                                <td colspan="8" class="text-end"><b>Totali</b></td>
                                <td><b><?php echo $fgfg - 4 ?> &euro;</b></td>
                            </tr>
                    </tbody>

                </table>
            </div>
        </div>
        <div class="row gap-2 mt-3 border rounded-2 p-2">
            <table class="table table-bordered caption-top" id="tabelaPagesat">
                <caption>
                    Lista e pagesave t&euml; kryera p&euml;r klientin "<?php echo $gstai2['emri']; ?>"
                </caption>
                <thead class="bg-light">
                    <tr>
                        <th>M&euml;nyra e pages&euml;s</th>
                        <th>P&euml;rshkrimi</th>
                        <th>Shuma</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rendori = 1;
                    $stmt = $conn->prepare("SELECT * FROM pagesatplatformat WHERE fatura=?");
                    $stmt->bind_param('s', $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_array()) {
                    ?>
                        <tr>
                            <td><?php echo $row['menyra']; ?></td>
                            <td><?php echo $row['pershkrimi']; ?></td>
                            <td><?php echo $row['shuma']; ?></td>
                            <td><?php echo $row['data']; ?></td>
                        <?php
                        $rendori++;
                    }
                    $stmt->close();
                        ?>
                </tbody>
            </table>
        </div>


    </div>






































    <!-- MDB -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.js"></script>
    <!-- <script>
		const form = document.getElementById('my-form');
		form.addEventListener('submit', function(event) {
			event.preventDefault();
			const formData = new FormData(event.target);
			const recipient = formData.get('recipient');
			const subject = formData.get('subject');
			const message = formData.get('message');

			const options = {
				method: 'POST',
				headers: {
					'content-type': 'application/json',
					'X-RapidAPI-Key': '33f656218fmshd937dedf10c6d28p12bdbbjsnecb52a398f27',
					'X-RapidAPI-Host': 'rapidprod-sendgrid-v1.p.rapidapi.com'
				},
				body: JSON.stringify({
					personalizations: [{
						to: [{
							email: recipient
						}],
						subject
					}],
					from: {
						email: 'egjini17@gmail.com'
					},
					content: [{
						type: 'text/plain',
						value: message
					}]
				})
			};

			fetch('https://rapidprod-sendgrid-v1.p.rapidapi.com/mail/send', options)
				.then(response => response.json())
				.then(response => {

				})
				.catch(err => {

				});


		});
	</script> -->

    <script>
        // Get a reference to the button element
        const printButton = document.getElementById('print-button');

        // Add an event listener to the button
        printButton.addEventListener('click', () => {
            // Call the window.print() method to print the page
            window.print();
        });

        // Include the html2pdf library in your HTML file, like so:
        // 
    </script>

    <script>
        // Add an event listener to the "Action" link
        document.getElementById('download-pdf-link').addEventListener('click', function(e) {
            e.preventDefault(); // Prevent the default link behavior

            // Wait for 3 seconds before generating the PDF
            setTimeout(function() {
                // Use html2pdf to generate a PDF of the current page
                html2pdf().from(document.body).save('Fatura - <?php echo $_GET['invoice']; ?>.pdf');
            }, 3000);
        });
    </script>
    <script>
        // Add an event listener to the "Download Excel" button
        document.getElementById('download-excel-btn').addEventListener('click', function(e) {
            e.preventDefault(); // Prevent the default button behavior

            // Create a new workbook
            var wb = XLSX.utils.book_new();

            // Convert the first table to a worksheet
            var ws1 = XLSX.utils.table_to_sheet(document.getElementById('tabelaKlientit'), {
                autoWidth: true
            });

            // Add the first worksheet to the workbook
            XLSX.utils.book_append_sheet(wb, ws1, "Tabela me te dhenat e klientit");

            // Convert the second table to a worksheet
            var ws2 = XLSX.utils.table_to_sheet(document.getElementById('tableShitjeve'), {
                autoWidth: true
            });

            // Add the second worksheet to the workbook
            XLSX.utils.book_append_sheet(wb, ws2, "Tabela me te dhenat e shitjeve");

            // Convert the third table to a worksheet
            var ws3 = XLSX.utils.table_to_sheet(document.getElementById('tabelaPagesat'), {
                autoWidth: true
            });

            // Add the third worksheet to the workbook
            XLSX.utils.book_append_sheet(wb, ws3, "Tabela me te dhenat e pagesave");

            // Save the workbook as a file
            XLSX.writeFile(wb, "Fatura - <?php echo $_GET['invoice']; ?>.xlsx");
        });
    </script>




    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
</body>

</html>

<!-- 9960b9e2d2f27723d88549ba4b768f30169c4b3f -->