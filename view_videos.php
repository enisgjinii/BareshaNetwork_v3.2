<?php
include 'partials/header.php';

// YouTube API Key
$apiKey = "AIzaSyBQD3hhckJv5uxPcbRk3b8nlNogG9781Lk";

// Get the channel ID from the GET parameter
if (isset($_GET['channel_id']) && !empty($_GET['channel_id'])) {
    $channelId = htmlspecialchars($_GET['channel_id']);

    // Fetch channel details (optional, e.g., channel title)
    $channelApiUrl = "https://www.googleapis.com/youtube/v3/channels?part=snippet&id=$channelId&key=$apiKey";
    $channelResponse = file_get_contents($channelApiUrl);
    $channelData = json_decode($channelResponse, true);

    if (empty($channelData['items'])) {
        echo "<div class='alert alert-danger'>Invalid Channel ID.</div>";
        include "partials/footer.php";
        exit;
    }

    $channelTitle = $channelData['items'][0]['snippet']['title'];
} else {
    echo "<div class='alert alert-danger'>No Channel ID provided.</div>";
    include "partials/footer.php";
    exit;
}
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <h2>Latest Videos from <?php echo htmlspecialchars($channelTitle); ?></h2>
            <div class="row">
                <?php
                // Fetch latest 10 videos from the channel
                $apiUrl = "https://www.googleapis.com/youtube/v3/search?key=$apiKey&channelId=$channelId&part=snippet&type=video&order=date&maxResults=10";
                $response = file_get_contents($apiUrl);
                $data = json_decode($response, true);

                if (!empty($data['items'])) {
                    foreach ($data['items'] as $item) {
                        $videoId = $item['id']['videoId'];
                        $videoTitle = htmlspecialchars($item['snippet']['title']);
                        $videoThumbnail = htmlspecialchars($item['snippet']['thumbnails']['medium']['url']);
                        $videoUrl = "https://www.youtube.com/watch?v=" . $videoId;
                        $embedUrl = "https://www.youtube.com/embed/" . $videoId;
                ?>
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <a href="<?php echo $videoUrl; ?>" target="_blank">
                                    <img src="<?php echo $videoThumbnail; ?>" class="card-img-top" alt="<?php echo $videoTitle; ?>">
                                </a>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $videoTitle; ?></h5>
                                    <a href="<?php echo $embedUrl; ?>" target="_blank" class="btn btn-primary">Watch Video</a>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo "<div class='col-12'><p>No videos found for this channel.</p></div>";
                }
                ?>
            </div>
            <a href="javascript:history.back()" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
<?php include "partials/footer.php"; ?>