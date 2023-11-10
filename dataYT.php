<?php
ob_start();
include 'partials/header.php';
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="container">
        <?php
        error_reporting(0);
        ini_set('display_errors', 0);
        if (file_exists(__DIR__ . '/google-api/vendor/autoload.php')) {
          require_once __DIR__ . '/google-api/vendor/autoload.php';
          $client = new Google_Client();
          $client->setApplicationName('API code samples');
          $client->setScopes([
            'https://www.googleapis.com/auth/youtube.readonly',
            'https://www.googleapis.com/auth/yt-analytics.readonly',
            'https://www.googleapis.com/auth/yt-analytics-monetary.readonly',
            'https://www.googleapis.com/auth/yt-analytics-monetary.readonly',
            'https://www.googleapis.com/auth/youtube.upload',
          ]);
          $client->setAuthConfig('client.json');
          $client->setAccessType('offline');
          $client->setApprovalPrompt('force');
          $authUrl = $client->createAuthUrl();
        }
        ?>
        <div class="p-5 mb-4 card rounded-5 shadow-sm">
          <div>
            <?php printf("\n%s\n", "<a style='text-transform:none;' class='btn btn-primary text-white shadow-sm rounded-5' href='" . $authUrl . "'><i class='fa-brands fa-google me-4'></i>Regjistro kanal</a> ");
            printf("<br>"); ?>
          </div>
        </div>

        <?php
        $authCode = $_GET['code'];
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
        $client->setAccessToken($accessToken);
        $service = new Google_Service_YouTubeAnalytics($client);
        $queryParams = [
          'currency' => 'EUR',
          'dimensions' => 'day',
          'endDate' => date('Y-m-d'),
          'ids' => 'channel==MINE',
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

        $service = new Google_Service_YouTube($client);
        $channel = $service->channels->listChannels('snippet', array('mine' => true));
        $channelTitle = $channel['items'][0]['snippet']['title'];
        $channelId = $channel['items'][0]['id'];

        include 'conn-d.php';

        // foreach ($data as $row) {
        //   // Check if the date already exists in the database for this user
        //   $sql = "SELECT COUNT(*) FROM monetizimi_youtube WHERE emri_kanalit = ? AND data = ?";
        //   $stmt = $conn->prepare($sql);
        //   $stmt->bind_param('ss', $channelTitle, $row['date']);
        //   $stmt->execute();
        //   $stmt->bind_result($count);
        //   $stmt->fetch();
        //   $stmt->close();

        //   // If the date doesn't exist, insert the data into the database
        //   if ($count == 0) {
        //     $sql = "INSERT INTO monetizimi_youtube (emri_kanalit, id_kanalit, revenue, data) VALUES (?, ?, ?, ?)";
        //     $stmt = $conn->prepare($sql);
        //     $stmt->bind_param('ssss', $channelTitle, $channelId, $row['revenue'], $row['date']);
        //     $stmt->execute();
        //     $stmt->close();
        //   }
        // }

        // Save the refresh token in the database
        $refreshToken = $accessToken['refresh_token'];

        // Insert channel name and refresh token into youtube_refresh_tokens table
        $sql = "INSERT INTO youtube_refresh_tokens (channel_id, channel_name, refresh_token) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $channelId, $channelTitle, $refreshToken);
        $stmt->execute();

        // Redirect to youtube_studio.php
        header("Location: youtube_studio.php");
        exit();


        // ob_flush();
        ?>
      </div>
    </div>
  </div>
</div>

<?php include 'partials/footer.php'; ?>