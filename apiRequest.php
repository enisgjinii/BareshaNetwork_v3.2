<?php
// Load the Google API PHP client library
require_once __DIR__ . '/google-api/vendor/autoload.php';

// Set up the Google_Client object
$client = new Google_Client();
$client->setApplicationName('API code samples');
$client->setScopes([
  'https://www.googleapis.com/auth/youtube.readonly',
  'https://www.googleapis.com/auth/yt-analytics.readonly',
  'https://www.googleapis.com/auth/yt-analytics-monetary.readonly',
]);
$client->setAuthConfig('client.json');

// Define the channel IDs and associated refresh tokens
$channels = array(
  'UC8DDPm51bRB1_HuuKfmGSGA' => '1//09wrJNMr-4VgSCgYIARAAGAkSNwF-L9IrxraWkvmWE3w8zMPwKaKdW89z0ZfxY6IahHNJVut77AnsUHKTw5UCgcSdZcwALxPTb9g',
  'UCeWJaLh0evsmCEB1LiZmcHw' => '1//09mIlipLHyDg-CgYIARAAGAkSNgF-L9IraiNzMqg5Gj_cOBXmTrnQyxrYyaLLCLjTDNP67KCYXezX_V2_f5vkbXCzNazJrMgh9w'
);

// Create an empty array to store the revenue data
$revenueData = array();

// Loop through each channel and retrieve the estimated revenue
foreach ($channels as $channelId => $refreshToken) {
  // Set the refresh token in the Google_Client object
  $client->fetchAccessTokenWithRefreshToken($refreshToken);
  $accessToken = $client->getAccessToken();

  // Create an instance of the YouTube Analytics service
  $analyticsService = new Google_Service_YouTubeAnalytics($client);

  // Set the query parameters for revenue retrieval
  $queryParams = [
    'currency' => 'EUR',
    'dimensions' => 'day',
    'endDate' => date('Y-m-d'),
    'ids' => 'channel==' . $channelId,
    'metrics' => 'estimatedRevenue',
    'startDate' => '2006-01-01'
  ];

  // Retrieve the revenue data using the query parameters
  $response = $analyticsService->reports->query($queryParams);

  // Process the revenue data and store it in the array
  foreach ($response->rows as $row) {
    $revenueData[] = array(
      'channelId' => $channelId,
      'date' => $row[0],
      'revenue' => $row[1]
    );
  }
}

// Display the revenue data as a table
echo "<table>";
echo "<tr><th>Channel ID</th><th>Date</th><th>Revenue</th></tr>";
foreach ($revenueData as $data) {
  echo "<tr>";
  echo "<td>{$data['channelId']}</td>";
  echo "<td>{$data['date']}</td>";
  echo "<td>{$data['revenue']}</td>";
  echo "</tr>";
}
echo "</table>";
