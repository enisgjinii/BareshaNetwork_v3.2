<?php include('partials/header.php'); ?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <div class="p-5 rounded-5 shadow-sm mb-4 card">
                    <?php

                    // Set the repository owner and name
                    $owner = "enisgjini";
                    $name = "BareshaNetwork-3.0";

                    // Build the URL to fetch the commit history using the GitHub API
                    $url = "https://api.github.com/repos/{$owner}/{$name}/commits?per_page=1000";

                    // Set the HTTP headers to include your GitHub access token (optional)
                    $headers = array(
                        "User-Agent: My PHP App"
                    );

                    // Initialize a cURL session to fetch the data
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $data = curl_exec($ch);
                    curl_close($ch);

                    // Decode the JSON response into an array of commit data
                    $commits = json_decode($data, true);

                    // Set the number of commits to display per page
                    $perPage = 10;

                    // Calculate the total number of pages based on the number of commits and the number of commits per page
                    $totalPages = ceil(count($commits) / $perPage);

                    // Get the current page number from the query string (default to 1 if not set)
                    $pageNumber = isset($_GET['page']) ? $_GET['page'] : 1;

                    // Calculate the starting index of the current page
                    $startIndex = ($pageNumber - 1) * $perPage;

                    // Calculate the ending index of the current page
                    $endIndex = $startIndex + $perPage;

                    // Loop through the commits on the current page and display them in a table

                    echo "<table class='table table-bordered w-100'>";
                    echo "<thead class='bg-light'><tr><th>Angazhimi i Mesazhit</th><th>Autori</th><th>Data</th><th>Ora</th></tr></thead>";
                    echo "<tbody>";

                    for ($i = $startIndex; $i < $endIndex && $i < count($commits); $i++) {
                        $commit = $commits[$i];
                        $message = $commit["commit"]["message"];
                        $author = $commit["commit"]["author"]["name"];
                        $dateTime = explode("T", $commit["commit"]["author"]["date"]);
                        $date = $dateTime[0];
                        $time = substr($dateTime[1], 0, 8);
                        $sha = $commit["sha"];
                        $url = "https://github.com/{$owner}/{$name}/commit/{$sha}";

                        echo "<tr><td><a href='{$url}' target='_blank'>{$message}</a></td><td>{$author}</td><td>{$date} </td><td>{$time}</td></tr>";
                    }

                    echo "</tbody>";
                    echo "</table><br>";

                    // Display pagination links
                    echo "<nav aria-label='Commit history pagination'><ul class='pagination'>";
                    for ($i = 1; $i <= $totalPages; $i++) {
                        $activeClass = ($i == $pageNumber) ? " active" : "";
                        echo "<li class='page-item{$activeClass}'><a class='page-link me-2' href='?page={$i}'>{$i}</a></li>";
                    }
                    echo "</ul></nav>";

                    ?>

                </div>

            </div>
        </div>
    </div>
</div>
<?php include('partials/footer.php'); ?>