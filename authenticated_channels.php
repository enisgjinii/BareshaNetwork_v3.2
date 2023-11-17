<?php
require_once 'vendor/autoload.php';

// Function to retrieve refresh tokens and channel information from the database
function getRefreshTokensFromDatabase()
{
    $config = require_once 'second_config.php';
    $conn = new mysqli($config['db_host'], $config['db_user'], $config['db_password'], $config['db_name']);

    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    $sql = "SELECT token, channel_id, channel_name, created_at FROM refresh_tokens";
    $result = $conn->query($sql);

    $refreshTokens = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $refreshTokens[] = $row;
        }
    }

    $conn->close();

    return $refreshTokens;
}

// Retrieve user-submitted start and end dates
$startDate = isset($_POST['startDate']) ? $_POST['startDate'] : '2016-01-01';
$endDate = isset($_POST['endDate']) ? $_POST['endDate'] : '2023-11-01';

$refreshTokens = getRefreshTokensFromDatabase();
?>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    <title>Authenticated Channels</title>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 50px;
        }

        .card {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h3 class="mb-4">Kanalet e autentifikuara</h3>
        <form method="post" class="mb-4">
            <div class="row">
                <div class="col">
                    <label for="startDate">Data e fillimit:</label>
                    <input type="text" class="form-control datepicker" id="startDate" name="startDate" value="<?= $startDate ?>" required>
                </div>
                <div class="col">
                    <label for="endDate">Data e përfundimit:</label>
                    <input type="text" class="form-control datepicker" id="endDate" name="endDate" value="<?= $endDate ?>" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-2">Dorëzoje</button>
        </form>


        <?php if (!empty($refreshTokens)) { ?>
            <div class="row">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col w-25">Arti i kopertinës</th>
                            <th scope="col">Emri i kanalit</th>
                            <th scope="col">ID-ja e kanalit</th>
                            <th scope="col">Te ardhurat nga Youtube</th>
                            <th scope="col">Veprimet</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($refreshTokens as $tokenInfo) { ?>
                            <tr>
                                <td>
                                    <?php // Get the cover art URL
                                    $coverArtUrl = getChannelDetails($tokenInfo['channel_id'], 'AIzaSyDKt-ziSnLKQfYGgAxqwjRtCc6ss-PFIaM');

                                    // Display cover art image
                                    if ($coverArtUrl) {
                                        echo '<img src="' . $coverArtUrl . '" class="figure-img img-fluid rounded w-25" alt="Channel Cover">';
                                    } ?>
                                </td>
                                <td><?= $tokenInfo['channel_name'] ?></td>
                                <td><?= $tokenInfo['channel_id'] ?></td>
                                <td>
                                    <?php
                                    $client = new Google_Client();
                                    $client->setClientId('727520120860-kebh087id1eb97tbeefpvkmvsj9nmek5.apps.googleusercontent.com');
                                    $client->setClientSecret('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');
                                    $client->refreshToken($tokenInfo['token']);
                                    $client->addScope([
                                        'https://www.googleapis.com/auth/youtube',
                                        'https://www.googleapis.com/auth/youtube.readonly',
                                        'https://www.googleapis.com/auth/youtubepartner',
                                        'https://www.googleapis.com/auth/yt-analytics-monetary.readonly',
                                        'https://www.googleapis.com/auth/yt-analytics.readonly'
                                    ]);

                                    $youtubeAnalytics = new Google\Service\YoutubeAnalytics($client);

                                    // Get the created date for that channel in YouTube using tokenInfo channel id
                                    $params = [
                                        'ids' => 'channel==' . $tokenInfo['channel_id'],
                                        'currency' => 'EUR',
                                        'startDate' => $startDate,
                                        'endDate' => $endDate,
                                        'metrics' => 'estimatedRevenue,views,estimatedAdRevenue,estimatedRedPartnerRevenue,grossRevenue,adImpressions,cpm,playbackBasedCpm,monetizedPlaybacks'
                                    ];

                                    $response = $youtubeAnalytics->reports->query($params);
                                    $row = $response->getRows()[0];

                                    echo '<p class="w-100 border px-3 py-3 bg-light rounded">';

                                    // Loop through all the metrics and display them
                                    foreach ($row as $index => $value) {
                                        echo ucfirst($response->getColumnHeaders()[$index]['name']) . ': ' . $value . '<br>';
                                    }

                                    echo '</p>';

                                    ?>
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm mb-3 text-white krijo-fature-btn" style="text-transform: none" data-bs-toggle="modal" data-bs-target="#newInvoice" data-revenue="<?= $youtubeAnalyticsValue ?>" data-channel-id="<?= $tokenInfo['channel_id'] ?>">Krijo faturë</button>
                                    <a class="btn btn-danger btn-sm mb-3" href="delete_refresh_token.php">Fshije</a>
                                    <a class="btn btn-info btn-sm mb-3" href="channel_details.php?channel_token=<?= $tokenInfo['token'] ?>">Shiko detajet</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th scope="col">Arti i kopertinës</th>
                            <th scope="col">Emri i kanalit</th>
                            <th scope="col">ID-ja e kanalit</th>
                            <th scope="col">Te ardhurat nga Youtube</th>
                            <th scope="col">Veprimet</th>

                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php } else { ?>
            <p>No refresh tokens found in the database.</p>
        <?php } ?>



        <!-- Modal Structure -->
        <div class="modal fade" id="newInvoice" tabindex="-1" aria-labelledby="newInvoiceLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newInvoiceLabel">Krijoni një faturë të re</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Your form goes here -->
                        <form action="create_invoice.php" method="POST">
                            <div class="mb-3">
                                <label for="invoice_number" class="form-label">Numri i faturës:</label>

                                <?php
                                // Call the generateInvoiceNumber function to get the invoice number
                                $invoiceNumber = generateInvoiceNumber();
                                ?>
                                <input type="text" class="form-control  shadow-sm py-3" id="invoice_number" name="invoice_number" value="<?php echo $invoiceNumber; ?>" required readonly>
                            </div>

                            <div class="mb-3">
                                <label for="customer_id" class="form-label">Emri i klientit:</label>
                                <p style="font-size: 12px" class="text-muted">Channel ID: <span id="channel_display"></span></p>
                                <select class="form-control shadow-sm py-3" id="customer_id" name="customer_id" required>
                                    <option value="">Zgjidhni klientin</option>
                                    <?php
                                    include "conn-d.php";

                                    $sql = "SELECT id, emri, perqindja, youtube FROM klientet ORDER BY id DESC";
                                    $result = mysqli_query($conn, $sql);

                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<option value='" . $row["id"] . "' data-percentage='" . $row["perqindja"] . "' data-youtube='" . $row["youtube"] . "'>" . $row["emri"] . "</option>";
                                        }
                                    }

                                    mysqli_close($conn);
                                    ?>
                                </select>


                            </div>

                            <div class="mb-3">
                                <label for="item" class="form-label">Përshkrimi:</label>
                                <textarea type="text" class="form-control  shadow-sm py-3" id="item" name="item" required> </textarea>
                            </div>
                            <div class="mb-3">
                                <label for="percentage" class="form-label">Përqindja:</label>
                                <input type="text" class="form-control  shadow-sm py-3" id="percentage" name="percentage" value="" required>
                            </div>
                            <div class="mb-3 row">
                                <div class="col">
                                    <label for="total_amount" class="form-label">Shuma e përgjithshme:</label>
                                    <input type="text" class="form-control  shadow-sm py-3" id="total_amount" name="total_amount" required>
                                </div>
                                <div class="col">
                                    <label for="total_amount_after_percentage" class="form-label">Shuma e përgjithshme pas përqindjes:</label>
                                    <input type="text" class="form-control  shadow-sm py-3" id="total_amount_after_percentage" name="total_amount_after_percentage" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="created_date" class="form-label">Data e krijimit të faturës:</label>
                                <input type="date" class="form-control  shadow-sm py-3" id="created_date" name="created_date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm text-white  shadow">Krijo faturë</button>
                        </form>
                    </div>
                </div>
            </div>


            <?php

            function getChannelDetails($channelId, $apiKey)
            {
                $url = "https://www.googleapis.com/youtube/v3/channels?part=snippet&id=$channelId&key=$apiKey";
                $response = file_get_contents($url);
                $data = json_decode($response, true);

                if (isset($data['items'][0]['snippet']['thumbnails']['high']['url'])) {
                    return $data['items'][0]['snippet']['thumbnails']['high']['url'];
                }

                return null;
            }
            ?>

        </div>
    </div>
    <!-- Add this script at the end of your body section -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize flatpickr for date inputs
            flatpickr('.datepicker', {
                dateFormat: 'Y-m-d',
                allowInput: true,
            });
        });
        document.getElementById('customer_id').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var percentage = selectedOption.getAttribute('data-percentage');
            document.getElementById('percentage').value = percentage;

            // Calculate Total Amount after Percentage
            var totalAmount = parseFloat(document.getElementById('total_amount').value);
            var totalAmountAfterPercentage = totalAmount - (totalAmount * (percentage / 100));
            document.getElementById('total_amount_after_percentage').value = totalAmountAfterPercentage.toFixed(2);
        });

        document.getElementById('total_amount').addEventListener('input', function() {
            // Calculate Total Amount after Percentage when Total Amount changes
            var totalAmount = parseFloat(this.value);
            var percentage = parseFloat(document.getElementById('percentage').value);
            var totalAmountAfterPercentage = totalAmount - (totalAmount * (percentage / 100));
            document.getElementById('total_amount_after_percentage').value = totalAmountAfterPercentage.toFixed(2);
        });
    </script>
    <?php function generateInvoiceNumber()
    {
        // Get the current date and time
        $currentDateTime = date("dmYHis");

        // Concatenate the prefix and date/time to create the invoice number
        $invoiceNumber = $currentDateTime;

        return $invoiceNumber;
    }
    ?>
    <script>
        $(document).ready(function() {
            $('.krijo-fature-btn').on('click', function() {
                // Get the channel ID from the clicked button
                var channelId = $(this).data('channel-id');

                // Get the YouTube Analytics Value
                var youtubeAnalyticsValue = $(this).data('revenue');

                // Update the modal content with the channel ID
                $('#channel_display').text(channelId);

                // Filter the select options based on the channel ID
                $('#customer_id option').each(function() {
                    var option = $(this);
                    var optionChannelId = option.data('youtube');

                    // Compare the channel IDs
                    if (optionChannelId === channelId) {
                        // Show the option if the channel IDs match
                        option.show();
                    } else {
                        // Hide the option if the channel IDs do not match
                        option.hide();
                    }
                });
                // Update the total_amount input field with the YouTube Analytics value
                $('#total_amount').val(youtubeAnalyticsValue);

                // Select the first visible option (assuming at least one option matches)
                var visibleOptions = $('#customer_id option:visible');
                if (visibleOptions.length > 0) {
                    visibleOptions.first().prop('selected', true);
                }
            });
        });
    </script>.


    <script>
        $(document).ready(function() {
            var myModal = new bootstrap.Modal(document.getElementById('newInvoice'));

            myModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var channelId = button.getAttribute('data-channel-id');

                // Assuming you have a way to get the YouTube Analytics response value
                var youtubeAnalyticsValue = "Replace with actual value";

                // Update the modal content with the channel ID
                $('#channel_display').text(channelId);

                // Set the default selected option to the second one
                $('#customer_id option:eq(2)').prop('selected', true);

                // Filter and select the corresponding option if there is a match
                $('#customer_id option').each(function() {
                    var option = $(this);
                    var optionChannelId = option.data('youtube');

                    if (optionChannelId === channelId) {
                        option.prop('selected', true);
                    }
                });

                // Update the total_amount input field
                $('#total_amount').val(youtubeAnalyticsValue);
            });

            document.querySelector('.krijo-fature-btn').addEventListener('click', function() {
                myModal.show();
            });
        });
    </script>








    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

</body>

</html>