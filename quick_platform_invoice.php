<?php include 'partials/header.php' ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="container"> <!-- Added 'mt-4' for top margin -->
            <nav class="bg-white px-2 rounded-5" class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Platformat</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="invoice.php" class="text-reset" style="text-decoration: none;">
                            Raportet
                        </a>
                    </li>
            </nav>
            <!-- Button trigger modal -->
            <div>

                <button type="button" class="input-custom-css px-3 py-2 mb-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    <i class="fi fi-rr-add"></i> &nbsp; Krijo raport te ri
                </button>
                <!-- <button id="toggleViewButton" class="input-custom-css px-3 py-2" onclick="toggleView()">Toggle View</button> -->

                <!-- <button id="deleteRowsBtn" class="input-custom-css px-3 py-2 mb-2">Fshi rreshtat e përzgjedhur</button> -->
                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Krijo raport</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Form for reporting office damages -->
                                <form id="damageForm" action="process_new_platform_invoice.php" method="post">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="id_of_client" class="form-label">Zgjedh klientin</label>
                                            <select name="id_of_client" id="id_of_client" class="form-select rounded-5">
                                                <?php
                                                $result = $conn->query("SELECT * FROM klientet");
                                                while ($row = mysqli_fetch_array($result)) {
                                                    echo '<option value="' . $row['id'] . '">' . $row['emri'] . '</option>';
                                                }
                                                ?>
                                            </select>

                                            <!-- <div id="client-details">
                                        </div> -->


                                            <script>
                                                new Selectr('#id_of_client', {
                                                    searchable: true,
                                                });
                                            </script>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="title_of_song" class="form-label">Titulli i këngës</label>
                                            <input type="text" name="title_of_song" id="title_of_song" class="form-control rounded-5 border border-2">
                                        </div>
                                    </div>

                                    <!-- Add month and year selectors -->
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="month" class="form-label">Zgjedh muajin</label>
                                            <select name="month" id="month" class="form-select rounded-5">
                                                <!-- Add options for months -->
                                                <option value="Janar">Janar</option>
                                                <option value="Shkurt">Shkurt</option>
                                                <option value="Mars">Mars</option>
                                                <option value="Prill">Prill</option>
                                                <option value="Maj">Maj</option>
                                                <option value="Qershor">Qershor</option>
                                                <option value="Korrik">Korrik</option>
                                                <option value="Gusht">Gusht</option>
                                                <option value="Shtator">Shtator</option>
                                                <option value="Tetor">Tetor</option>
                                                <option value="Nëntor">Nëntor</option>
                                                <option value="Dhjetor">Dhjetor</option>


                                            </select>
                                        </div>

                                        <div class="col mb-3">
                                            <label for="year" class="form-label">Zgjedh vitin</label>
                                            <select name="year" id="year" class="form-select rounded-5">
                                                <option value="2017">2017</option>
                                                <option value="2018">2018</option>
                                                <option value="2019">2019</option>
                                                <option value="2020">2020</option>
                                                <option value="2021">2021</option>
                                                <option value="2022">2022</option>
                                                <option value="2023">2023</option>
                                            </select>
                                        </div>

                                    </div>

                                    <div class="row px-5">
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <label for="amazon_music_income" class="form-label">Amazon Music</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="amazon_music_income" id="amazon_music_income" class="form-control calculate rounded-5" placeholder="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="anghami_income" class="form-label">Anghami</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="anghami_income" id="anghami_income" class="form-control calculate rounded-5" placeholder="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="apple_music_income" class="form-label">Apple Music</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="apple_music_income" id="apple_music_income" class="form-control calculate rounded-5" placeholder="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="audiomack_income" class="form-label">Audiomack</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="audiomack_income" id="audiomack_income" class="form-control calculate rounded-5" placeholder="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="deezer_income" class="form-label">Deezer</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="deezer_income" id="deezer_income" class="form-control calculate rounded-5" placeholder="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="facebook_income" class="form-label">Facebook / Instagram</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="facebook_income" id="facebook_income" class="form-control calculate rounded-5" placeholder="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="iheartradio_income" class="form-label">iHeartRadio</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="iheartradio_income" id="iheartradio_income" class="form-control calculate rounded-5" placeholder="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="kkbox_income" class="form-label">KKBox</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="kkbox_income" id="kkbox_income" class="form-control calculate rounded-5" placeholder="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="medianet_income" class="form-label">MediaNet</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="medianet_income" id="medianet_income" class="form-control calculate rounded-5" placeholder="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="netease_income" class="form-label">NetEase</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="netease_income" id="netease_income" class="form-control calculate rounded-5" placeholder="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="qobuz_income" class="form-label">Qobuz</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="qobuz_income" id="qobuz_income" class="form-control calculate rounded-5" placeholder="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="resso_income" class="form-label">Resso</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="resso_income" id="resso_income" class="form-control calculate rounded-5" placeholder="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="saavn_income" class="form-label">Saavn</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="saavn_income" id="saavn_income" class="form-control calculate rounded-5" placeholder="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="soundtrack_income" class="form-label">Soundtrack Your Brand</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="soundtrack_income" id="soundtrack_income" class="form-control calculate rounded-5" placeholder="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="spotify_income" class="form-label">Spotify</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="spotify_income" id="spotify_income" class="form-control calculate rounded-5" placeholder="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="tencent_income" class="form-label">Tencent</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="tencent_income" id="tencent_income" class="form-control calculate rounded-5" placeholder="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="tidal_income" class="form-label">Tidal</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="tidal_income" id="tidal_income" class="form-control calculate rounded-5" placeholder="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="tiktok_income" class="form-label">TikTok</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="tiktok_income" id="tiktok_income" class="form-control calculate rounded-5" placeholder="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="youtube_income" class="form-label">YouTube</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="youtube_income" id="youtube_income" class="form-control calculate calculate rounded-5" placeholder="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="total_income" class="form-label" id="total">Të ardhurat totale</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control rounded-5" readonly id="totalAmount" name="totalAmount" value="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="tax" class="form-label">Tax withholding (Mbajtja në burim e tatimit)</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="tax" id="tax" class="form-control rounded-5" placeholder="0.00">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>


                                    <script>
                                        // JavaScript to calculate the total sum
                                        document.addEventListener('input', function(event) {
                                            if (event.target.matches('.calculate')) {
                                                calculateTotal();
                                            }
                                        });

                                        function calculateTotal() {
                                            var total = 0;
                                            var inputs = document.querySelectorAll('.calculate');

                                            inputs.forEach(function(input) {
                                                total += parseFloat(input.value) || 0;
                                            });

                                            document.getElementById('totalAmount').value = total.toFixed(2);
                                        }
                                    </script>

                                    <hr>
                                    <button type="submit" class="input-custom-css px-3 py-2 float-end">Dergo</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Table for Displaying Investments -->
                <div id="tableView" class="table-responsive" style="display:none;">
                    <table class="table table-striped table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Emri i muzikes</th>
                                <th>ID</th>
                                <th>Emri i klientit</th>
                                <th>Amazon Music</th>
                                <th>Anghami</th>
                                <th>Apple Music</th>
                                <th>Audiomack</th>
                                <th>Deezer</th>
                                <th>Spotify</th>
                                <th>Tencent</th>
                                <th>Tidal</th>
                                <th>TikTok</th>
                                <th>YouTube</th>
                                <th>Të ardhurat totale</th>
                                <th>Tax withhold</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM platform_invoices ORDER BY id DESC";
                            $result = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $title_of_song = $row['title_of_song'];
                                    $id = $row['id'];
                                    $client_name = $row['client_id'];
                                    // Make a query in table klientet
                                    $sql2 = "SELECT * FROM klientet WHERE id = $client_name";
                                    $result2 = mysqli_query($conn, $sql2);
                                    while ($row2 = mysqli_fetch_assoc($result2)) {
                                        $client_emri = $row2['emri'];
                                    }
                                    $amazon_music = $row['amazon_music_income'];
                                    $angahmi = $row['anghami_income'];
                                    $apple_music = $row['apple_music_income'];
                                    $audiomack = $row['audiomack_income'];
                                    $deezer = $row['deezer_income'];
                                    $facebook = $row['facebook_income'];
                                    $iheartradio = $row['iheartradio_income'];
                                    $kkbox = $row['kkbox_income'];
                                    $medianet = $row['medianet_income'];
                                    $netease = $row['netease_income'];
                                    $qobuz = $row['qobuz_income'];
                                    $resso = $row['resso_income'];
                                    $saavn = $row['saavn_income'];
                                    $soundtrack = $row['soundtrack_income'];
                                    $spotify = $row['spotify_income'];
                                    $tencent = $row['tencent_income'];
                                    $tidal = $row['tidal_income'];
                                    $tiktok = $row['tiktok_income'];
                                    $youtube = $row['youtube_income'];
                                    $total = $row['total_income'];
                                    $tax = $row['tax_withholding'];
                                    $month = $row['month'];
                                    $year = $row['year'];
                            ?>

                                    <tr>
                                        <td><?= $title_of_song ?></td>
                                        <td><?= $id ?></td>
                                        <td><?= $client_emri ?></td>
                                        <td><?= $amazon_music ?></td>
                                        <td><?= $angahmi ?></td>
                                        <td><?= $apple_music ?></td>
                                        <td><?= $audiomack ?></td>
                                        <td><?= $deezer ?></td>
                                        <td><?= $spotify ?></td>
                                        <td><?= $tencent ?></td>
                                        <td><?= $tidal ?></td>
                                        <td><?= $tiktok ?></td>
                                        <td><?= $youtube ?></td>
                                        <td><?= $total ?></td>
                                        <td><?= $tax ?></td>
                                    </tr>

                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <!-- Table for Displaying Investments -->
                <div class="accordion" id="platformAccordion">
                    <?php
                    $sql = "SELECT * FROM platform_invoices ORDER BY id DESC";
                    $result = mysqli_query($conn, $sql);
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $title_of_song = $row['title_of_song'];
                            $id = $row['id'];
                            $client_name = $row['client_id'];
                            // Make a query in table klientet
                            $sql2 = "SELECT * FROM klientet WHERE id = $client_name";
                            $result2 = mysqli_query($conn, $sql2);
                            while ($row2 = mysqli_fetch_assoc($result2)) {
                                $client_emri = $row2['emri'];
                            }
                            $amazon_music = $row['amazon_music_income'];
                            $angahmi = $row['anghami_income'];
                            $apple_music = $row['apple_music_income'];
                            $audiomack = $row['audiomack_income'];
                            $deezer = $row['deezer_income'];
                            $facebook = $row['facebook_income'];
                            $iheartradio = $row['iheartradio_income'];
                            $kkbox = $row['kkbox_income'];
                            $medianet = $row['medianet_income'];
                            $netease = $row['netease_income'];
                            $qobuz = $row['qobuz_income'];
                            $resso = $row['resso_income'];
                            $saavn = $row['saavn_income'];
                            $soundtrack = $row['soundtrack_income'];
                            $spotify = $row['spotify_income'];
                            $tencent = $row['tencent_income'];
                            $tidal = $row['tidal_income'];
                            $tiktok = $row['tiktok_income'];
                            $youtube = $row['youtube_income'];
                            $total = $row['total_income'];
                            $tax = $row['tax_withholding'];
                            $month = $row['month'];
                            $year = $row['year'];
                    ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading<?= $id ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $id ?>" aria-expanded="false" aria-controls="collapse<?= $id ?>">
                                        Raporti i klientit #<?= $id ?> - <?= $client_emri ?> (<?= $month ?> <?= $year ?>)
                                    </button>
                                </h2>
                                <div id="collapse<?= $id ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $id ?>" data-bs-parent="#platformAccordion">
                                    <div class="accordion-body">
                                        <!-- Delete button -->
                                        <form method="post" action="delete_platform_invoice.php">
                                            <input type="hidden" name="invoice_id" value="<?= $id ?>">
                                            <button type="submit" class="input-custom-css px-3 py-2">Fshije</button>

                                        </form>
                                        <br>
                                        <table class="table table-bordered  ">
                                            <tbody>
                                                <tr>
                                                    <td><strong>Titulli i muzikes</strong></td>
                                                    <td><?= $title_of_song ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Emri</strong></td>
                                                    <td><?= $client_emri ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Amazon Music</strong></td>
                                                    <td>$ <?= $amazon_music ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Anghami</strong></td>
                                                    <td>$ <?= $angahmi ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Apple Music</strong></td>
                                                    <td>$ <?= $apple_music ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Audiomack</strong></td>
                                                    <td>$ <?= $audiomack ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Deezer</strong></td>
                                                    <td>$ <?= $deezer ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Facebook</strong></td>
                                                    <td>$ <?= $facebook ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Iheartradio</strong></td>
                                                    <td>$ <?= $iheartradio ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>KKbox</strong></td>
                                                    <td>$ <?= $kkbox ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Medianet</strong></td>
                                                    <td>$ <?= $medianet ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Netease</strong></td>
                                                    <td>$ <?= $netease ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Qobuz</strong></td>
                                                    <td>$ <?= $qobuz ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Resso</strong></td>
                                                    <td>$ <?= $resso ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Saavn</strong></td>
                                                    <td>$ <?= $saavn ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Soundtrack</strong></td>
                                                    <td>$ <?= $soundtrack ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Spotify</strong></td>
                                                    <td>$ <?= $spotify ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Tencent</strong></td>
                                                    <td>$ <?= $tencent ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Tidal</strong></td>
                                                    <td>$ <?= $tidal ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Tiktok</strong></td>
                                                    <td>$ <?= $tiktok ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Youtube</strong></td>
                                                    <td>$ <?= $youtube ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Të ardhurat totale</strong></td>
                                                    <td>$ <?= $total ?></td>
                                                </tr>
                                                <?php if (!empty($tax)) { ?>
                                                    <tr>
                                                        <td><strong>Tax Withholding (Mbajtja e tatimit në burim)</strong></td>
                                                        <td>$ <?= $tax ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Të ardhurat totale pas tatimit</strong></td>
                                                        <td>$ <?= $total - $tax ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>



            </div>
        </div>
    </div>

    <?php include 'partials/footer.php'; ?>
    <script>
        // Use jQuery to handle change event on the select element
        $(document).ready(function() {
            $("#id_of_client").change(function() {
                // Retrieve the selected client ID
                var selectedClientId = $(this).val();

                // Use AJAX to fetch additional data for the selected client
                $.ajax({
                    type: "POST",
                    url: "get_client_details.php", // Create a new PHP file for handling AJAX requests
                    data: {
                        id_of_client: selectedClientId
                    },
                    success: function(response) {
                        console.log(response); // Log the response to the console
                        // Update the client details div with the fetched data
                        $("#client-details").html(response);

                        // After updating the client details, calculate the income after percentage
                        calculateIncomeAfterPercentage();
                    }

                });
            });
        });

        // Calculate income after percentage when platform income is input
        $("#platform_income").on('input', function() {
            calculateIncomeAfterPercentage();
        });

        // Function to calculate income after percentage
        function calculateIncomeAfterPercentage() {
            var platformIncome = parseFloat($("#platform_income").val());
            var percentage = parseFloat($("#perqindja").val());

            if (!isNaN(platformIncome) && !isNaN(percentage)) {
                var incomeAfterPercentage = platformIncome - (platformIncome * percentage / 100);
                $("#platform_income_after_percentage").val(incomeAfterPercentage.toFixed(2));
            }
        }

        // Call the function when the percentage input changes
        $("#perqindja").on("input", calculateIncomeAfterPercentage);
    </script>




    <!-- <script>
        function toggleView() {
            var tableView = document.getElementById("tableView");
            var accordionView = document.getElementById("platformAccordion");

            // Toggle the display style
            if (tableView.style.display === "none") {
                tableView.style.display = "block";
                accordionView.style.display = "none";
            } else {
                tableView.style.display = "none";
                accordionView.style.display = "block";
            }
        }
    </script> -->

    <!-- <script>
        $(document).ready(function() {
            var table = $('#platform_table').DataTable({
                stripeClasses: ['stripe-color'],
                dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
                    "<'row'<'col-md-12'tr>>" +
                    "<'row'<'col-md-6'><'col-md-6'p>>",
                buttons: [{
                        extend: "pdfHtml5",
                        text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                        titleAttr: "Eksporto tabelen ne formatin PDF",
                        className: "btn btn-light btn-sm bg-light border me-2 rounded-5",

                    },
                    {
                        extend: "copyHtml5",
                        text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
                        titleAttr: "Kopjo tabelen ne formatin Clipboard",
                        className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                    },
                    {
                        extend: "excelHtml5",
                        text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
                        titleAttr: "Eksporto tabelen ne formatin Excel",
                        className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                        exportOptions: {
                            modifier: {
                                search: "applied",
                                order: "applied",
                                page: "all",
                            },
                        },
                    },
                    {
                        extend: "print",
                        text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
                        titleAttr: "Printo tabel&euml;n",
                        className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                    },
                ],
                initComplete: function() {
                    var btns = $(".dt-buttons");
                    btns.addClass("").removeClass("dt-buttons btn-group");
                    var lengthSelect = $("div.dataTables_length select");
                    lengthSelect.addClass("form-select");
                    lengthSelect.css({
                        width: "auto",
                        margin: "0 8px",
                        padding: "0.375rem 1.75rem 0.375rem 0.75rem",
                        lineHeight: "1.5",
                        border: "1px solid #ced4da",
                        borderRadius: "0.25rem",
                    });
                },
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
                },
                serverSide: true,
                processing: true,
                ajax: {
                    "url": "fetch_platform_invoices.php", // Path to your PHP script
                    "dataSrc": "data" // Specify the data source as "data"
                },
                "columns": [{
                        data: null,
                        defaultContent: '<input type="checkbox" class="deleteCheckbox">'
                    },
                    {
                        "data": "id"
                    },
                    {
                        "data": "klient_emri"
                    },
                    {
                        "data": "platform"
                    },
                    {
                        "data": "platform_income"
                    },
                    {
                        "data": "platform_income_after_percentage"
                    },
                    {
                        "data": "date"
                    },
                    {
                        "data": "description"
                    },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return '<form action="view_platformInvoice.php" method="post" target="_blank">' +
                                '<input type="hidden" name="id" value="' + row.id + '">' +
                                '<button type="submit" class="btn btn-primary rounded-5 px-2 py-2 text-white"><i class="fi fi-rr-print"></i></button>' +
                                '</form>';
                        }
                    }
                ],
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                "pageLength": 10,
                "order": [
                    [1, 'asc']
                ], // Order by the second column (change as needed)
                "searching": true,
                "paging": true
            });
        });
    </script> -->