<?php

include 'conn-d.php';

// Get id from POST data
$id = $_POST['id'];

// Query
$query = "SELECT * FROM platform_invoices WHERE id = $id";

// Execute
$result = mysqli_query($conn, $query);

// Fetch
while ($row = mysqli_fetch_array($result)) {
    $id = $row['id'];
    $client_id = $row['client_id'];
    // Query for client details
    $client_query = "SELECT * FROM klientet WHERE id = $client_id";
    $client_result = mysqli_query($conn, $client_query);
    $client_row = mysqli_fetch_array($client_result);
    $client_emri = $client_row['emri'];
    $client_perqindja = $client_row['perqindja'];
    $platform = $row['platform'];
    $platform_income = $row['platform_income'];
    $platform_income_after_percentage = $row['platform_income_after_percentage'];
    $date = $row['date'];
    $description = $row['description'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BareshaNetwork -
        <?php echo date("Y"); ?>
    </title>
    <!-- Fav Icon ne formatin .png -->
    <link rel="shortcut icon" href="images/favicon.png" />
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.css" rel="stylesheet" />
    <!-- Add some style in printing -->
    <style>
        @media print {
            .btn {
                display: none;
            }

            /* Make that row to be in two col in print */
            #invoice {
                display: flex;
                flex-wrap: wrap;
                /* Ensure flex items wrap to the next line */
                justify-content: space-between;
                /* Add space between columns */
            }

            #invoice>div {
                width: 48%;
                /* Adjust the width to leave some space between columns */
                margin-bottom: 10px;
                /* Add some space between rows */
            }

            #cardInfo {
                border-style: 0px solid transparent;
                padding: 0;
                margin: 0;
                box-shadow: none;
            }

            .titles {
                color: black !important;
            }
        }
    </style>

</head>

<body class="bg-light">
    <!--  <p><?php echo $id; ?></p>
    <p><?php echo $client_emri; ?></p>
    <p><?php echo $platform; ?></p>
    <p><?php echo $platform_income; ?></p>
    <p><?php echo $platform_income_after_percentage; ?></p>
    <p><?php echo $date; ?></p> -->
    <!-- Add a print button -->


    <div class="container-fluid mt-5">
        <div class="d-flex justify-content-center row">
            <div class="col-md-8 border border-1 bg-white rounded-4" id="cardInfo">
                <br>
                <button class="btn btn-white shadow-sm border rounded-5" style="text-transform: none;" onclick="window.print()">Printo raportin &nbsp; <i class="fas fa-print"></i></button>
                <div class="p-3">
                    <div class="text-center mb-3">
                        <img src="images/favicon.png" alt="" class="img-fluid" width="75">
                    </div>
                    <div class="row" id="invoice">
                        <div class="col-md-6">
                            <h5 class="text-center titles">Raport i të ardhurave</h5>
                            <hr>
                            <div class="billed"><span class="text-muted">Raporti lëshohet për: </span><span class="ml-1"><?php echo $client_emri; ?></span></div>
                            <div class="billed">
                                <span class="text-muted">Data: </span>
                                <span class="ml-1"><?php echo date('d/m/Y', strtotime($date)); ?></span>
                            </div>
                            <div class="billed"><span class="text-muted">Platforma: </span><span class="ml-1"><?php echo $platform; ?>&nbsp;
                                    <?php
                                    switch ($platform) {
                                        case 'YouTube':
                                            echo '<i class="fab fa-youtube"></i>';
                                            break;
                                        case 'Facebook':
                                            echo '<i class="fab fa-facebook"></i>';
                                            break;
                                        case 'Instagram':
                                            echo '<i class="fab fa-instagram"></i>';
                                            break;
                                        case 'Twitter':
                                            echo '<i class="fab fa-twitter"></i>';
                                            break;
                                        case 'LinkedIn':
                                            echo '<i class="fab fa-linkedin"></i>';
                                            break;
                                        case 'Spotify':
                                            echo '<i class="fab fa-spotify"></i>';
                                            break;
                                        case 'Tiktok':
                                            echo '<i class="fab fa-tiktok"></i>';
                                            break;
                                        case 'Snapchat':
                                            echo '<i class="fab fa-snapchat"></i>';
                                            break;
                                        case 'Twitch':
                                            echo '<i class="fab fa-twitch"></i>';
                                            break;
                                        case 'WhatsApp':
                                            echo '<i class="fab fa-whatsapp"></i>';
                                            break;
                                        case 'Reddit':
                                            echo '<i class="fab fa-reddit"></i>';
                                            break;
                                        case 'Tjera':
                                            echo '<i class="fas fa-globe"></i>';
                                            break;
                                        default:
                                            echo '<i class="fas fa-question"></i>';
                                            break;
                                    }
                                    ?>
                                </span>
                            </div>
                            <!-- Icon of Platform -->
                            <div class="billed"><span class="ml-1">
                                </span>
                            </div>

                            <div class="billed"><span class="text-muted">ID-ja e porosisë: </span><span class="ml-1"><?php echo $id;
                                                                                                                        5 ?></span></div>

                        </div>
                        <div class="col-md-6 text-end">
                            <h5 class="text-danger text-center titles">Baresha Network</h5>
                            <hr>
                            <p class="mb-0 text-muted">Website : <a href="https://www.bareshanetwork.com" class="text-dark">www.bareshanetwork.com</a></p>
                            <span class="mb-0 text-muted">Tel:
                                <span class="text-dark">+386 (0) 49 605 655</span>
                            </span>
                            <br>
                            <span class="mb-0 text-muted">Email:
                                <span class="text-dark">info@bareshamusic.com</span>
                            </span>
                            <br>
                            <span class="mb-0 text-muted">Adresa:
                                <span class="text-dark">8RVC+762, R118, Shiroke, Suhareke</span>
                            </span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Platforma</th>
                                        <th>Të ardhurat</th>
                                        <th>Përshkrimi</th>
                                        <th>Data e leshimit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?php echo $id; ?></td>
                                        <td><?php echo $platform; ?></td>
                                        <td><?php echo $platform_income_after_percentage; ?></td>
                                        <td><?php echo $description; ?></td>
                                        <td><?php echo $date; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="text-center mb-3"><i class="text-muted" style="font-size: 12px;">Faleminderit për zgjedhjen tuaj për bashkëpunim me Baresha Network</i></div>
                </div>
            </div>
        </div>
    </div>
    <!-- MDB -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.umd.min.js"></script>
</body>

</html>