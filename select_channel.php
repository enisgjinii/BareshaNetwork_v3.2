<?php
include 'partials/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedChannel = $_POST['channel'];

    // Fetch the refresh token for the selected channel
    include 'conn-d.php';
    $sql = "SELECT refresh_token FROM youtube_refresh_tokens WHERE channel_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $selectedChannel);
    $stmt->execute();
    $stmt->bind_result($refreshToken);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    // Use the refresh token to fetch the metrics
    require_once __DIR__ . '/google-api/vendor/autoload.php';

    $client = new Google_Client();
    $client->setApplicationName('API code samples');
    $client->setScopes([
        'https://www.googleapis.com/auth/youtube.readonly',
        'https://www.googleapis.com/auth/yt-analytics.readonly',
        'https://www.googleapis.com/auth/yt-analytics-monetary.readonly',
    ]);
    $client->setAuthConfig('client.json');
    $client->setAccessType('offline');
    $client->refreshToken($refreshToken);

    $service = new Google_Service_YouTubeAnalytics($client);
    $queryParams = [
        'currency' => 'EUR',
        'dimensions' => 'day',
        'endDate' => date('Y-m-d'),
        'ids' => 'channel==' . $selectedChannel,
        'metrics' => 'estimatedRevenue',
        'startDate' => '2006-01-01'
    ];
    $response = $service->reports->query($queryParams);
    $data = array();
    foreach ($response->rows as $row) {
        $data[] = array(
            'date' => $row[0],
            'revenue' => $row[1],
        );
    }

    // Display the metrics data
    echo "<h1>YouTube Studio</h1>";
    echo "<h2>Channel: " . $selectedChannel . "</h2>";
    echo "<table>";
    echo "<tr><th>Date</th><th>Revenue</th></tr>";
    foreach ($data as $row) {
        echo "<tr><td>" . $row['date'] . "</td><td>" . $row['revenue'] . "</td></tr>";
    }
    echo "</table>";
} else {
    // Display the channel selection form
    include 'conn-d.php';

    $sql = "SELECT channel_id FROM youtube_refresh_tokens";
    $result = $conn->query($sql);
    $channels = array();
    while ($row = $result->fetch_assoc()) {
        $channels[] = $row['channel_id'];
    }
    $conn->close();

    // HTML form to select the channel
    echo "<h1>Select Channel</h1>";
    echo "<form method='post' action=''>";
    echo "<label for='channel'>Select a channel:</label>";
    echo "<select name='channel' id='channel'>";
    foreach ($channels as $channel) {
        echo "<option value='" . $channel . "'>" . $channel . "</option>";
    }
    echo "</select>";
    echo "<br>";
    echo "<button type='submit'>Fetch Metrics</button>";
    echo "</form>";
}

include 'partials/footer.php';
