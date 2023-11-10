<?php include 'partials/header.php'; ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <!-- Back button with left arrow icon -->
                <a href="check_musics.php" class="btn btn-primary text-white rounded-5 mb-3 shadow-0">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <?php
                // Check if the 'id' parameter is provided in the URL
                if (isset($_GET['id'])) {
                    // Sanitize and retrieve the 'id' from the query string
                    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

                    // Query the database to fetch details based on the provided 'id'
                    $query = "SELECT * FROM ngarkimi WHERE id = $id";
                    $result = mysqli_query($conn, $query);

                    if ($result && mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);

                        // Assuming you have columns named emri, kengtari, and other details
                        $emri = $row['emri'];
                        $kengtari = $row['kengetari'];

                        // Fetch user registered songs
                        $query = "SELECT * FROM klientet WHERE id = '$row[klienti]'";
                        $result = mysqli_query($conn, $query);
                        $rowUser = mysqli_fetch_assoc($result);

                        // Construct the URL for the Spotify RapidAPI service
                        $searchTermSpotify = urlencode($emri . ' ' . $kengtari);
                        $urlSpotify = 'https://spotify23.p.rapidapi.com/search/?q=' . $searchTermSpotify . '&type=tracks&offset=0&limit=10&numberOfTopResults=1'; // Limit to only 1 result

                        // Set up cURL to make the Spotify API request
                        $chSpotify = curl_init($urlSpotify);
                        curl_setopt($chSpotify, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($chSpotify, CURLOPT_HTTPHEADER, [
                            'X-RapidAPI-Key: 335200c4afmsh64cfbbf7fdf4cf2p1aae94jsn05a3bad585de',
                            'X-RapidAPI-Host: spotify23.p.rapidapi.com',
                        ]);

                        // Execute the Spotify cURL request
                        $responseSpotify = curl_exec($chSpotify);

                        // Check if the Spotify request was successful
                        if ($responseSpotify !== false) {
                            $resultSpotify = json_decode($responseSpotify, true);

                            // Extract the first (best) match from Spotify
                            $bestMatchSpotify = $resultSpotify['tracks']['items'][0];

                            if (!empty($bestMatchSpotify)) {
                                $trackNameSpotify = $bestMatchSpotify['data']['name'];
                                $albumNameSpotify = $bestMatchSpotify['data']['albumOfTrack']['name'];
                                $artistNameSpotify = $bestMatchSpotify['data']['artists']['items'][0]['profile']['name'];
                                $shareUrlSpotify = $bestMatchSpotify['data']['albumOfTrack']['sharingInfo']['shareUrl'];
                            } else {
                                // No tracks found in Spotify
                                $trackNameSpotify = "N/A";
                                $albumNameSpotify = "N/A";
                                $artistNameSpotify = "N/A";
                                $shareUrlSpotify = "#";
                            }
                        } else {
                            // Error making the Spotify API request
                            $trackNameSpotify = "Error";
                            $albumNameSpotify = "Error";
                            $artistNameSpotify = "Error";
                            $shareUrlSpotify = "#";
                        }

                        // Close the Spotify cURL session
                        curl_close($chSpotify);

                        // Construct the URL for the Deezer RapidAPI service
                        $searchTermDeezer = urlencode($emri . ' ' . $kengtari);
                        $urlDeezer = 'https://deezerdevs-deezer.p.rapidapi.com/search?q=' . $searchTermDeezer;

                        // Set up cURL to make the Deezer API request
                        $chDeezer = curl_init();
                        curl_setopt_array($chDeezer, [
                            CURLOPT_URL => $urlDeezer,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "GET",
                            CURLOPT_HTTPHEADER => [
                                "X-RapidAPI-Host: deezerdevs-deezer.p.rapidapi.com",
                                "X-RapidAPI-Key: 335200c4afmsh64cfbbf7fdf4cf2p1aae94jsn05a3bad585de"
                            ],
                        ]);

                        // Execute the Deezer cURL request
                        $responseDeezer = curl_exec($chDeezer);

                        // Check if the Deezer request was successful
                        if ($responseDeezer !== false) {
                            $resultDeezer = json_decode($responseDeezer, true);

                            // Extract the first (best) match from Deezer
                            if (isset($resultDeezer['data'][0])) {
                                $trackNameDeezer = $resultDeezer['data'][0]['title'];
                                $albumNameDeezer = $resultDeezer['data'][0]['album']['title'];
                                $artistNameDeezer = $resultDeezer['data'][0]['artist']['name'];
                                $shareUrlDeezer = $resultDeezer['data'][0]['link'];
                            } else {
                                // No tracks found in Deezer
                                $trackNameDeezer = "N/A";
                                $albumNameDeezer = "N/A";
                                $artistNameDeezer = "N/A";
                                $shareUrlDeezer = "#";
                            }
                        } else {
                            // Error making the Deezer API request
                            $trackNameDeezer = "Error";
                            $albumNameDeezer = "Error";
                            $artistNameDeezer = "Error";
                            $shareUrlDeezer = "#";
                        }

                        // Close the Deezer cURL session
                        curl_close($chDeezer);
                ?>
                        <div class="col-md-12">
                            <div class="card px-4 py-3 rounded-5 border-1">
                                <p>Emri i k&euml;ng&euml;s: <?php echo $emri ?></p>
                                <p>K&euml;ng&euml;tari: <?php echo $kengtari ?></p>
                                <p>Klienti: <?php echo $rowUser['emri'] ?></p>
                                <p>K&euml;nga &euml;sht&euml; e publikuar n&euml; : <?php echo $row['platformat'] ?></p>
                                <hr>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="card p-3 rounded-5">
                                            <h4 class="card-title">Spotify</h4>
                                            <p class="card-text">Track Name: <?php echo $trackNameSpotify; ?></p>
                                            <p class="card-text">Album Name: <?php echo $albumNameSpotify; ?></p>
                                            <p class="card-text">Artist Name: <?php echo $artistNameSpotify; ?></p>
                                            <p class="card-text">Share URL: <a href="<?php echo $shareUrlSpotify; ?>" target="_blank"><?php echo $shareUrlSpotify; ?></a></p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card p-3 rounded-5">
                                            <h4 class="card-title">Deezer</h4>
                                            <p class="card-text">Track Name: <?php echo $trackNameDeezer; ?></p>
                                            <p class="card-text">Album Name: <?php echo $albumNameDeezer; ?></p>
                                            <p class="card-text">Artist Name: <?php echo $artistNameDeezer; ?></p>
                                            <p class="card-text">Share URL: <a href="<?php echo $shareUrlDeezer; ?>" target="_blank"><?php echo $shareUrlDeezer; ?></a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php
                    } else {
                        ?>
                        <div class="col-md-12">
                            <p class="alert alert-danger">Item not found.</p>
                        </div>
                    <?php
                    }
                } else {
                    ?>
                    <div class="col-md-12">
                        <p class="alert alert-danger">Invalid request. Please provide an ID.</p>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
