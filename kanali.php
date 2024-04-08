<?php include 'partials/header.php'; ?>
<!-- Begin Page Content -->
<div class="container-fluid mt-5">
  <!-- Page Heading -->
  <?php
  // Validate the channel ID parameter
  if (isset($_GET['id']) && !empty($_GET['id'])) {
    $channel_id = $_GET['id'];
  } else {
    echo '<div class="alert alert-danger" role="alert">Invalid channel ID.</div>';
    include 'partials/footer.php';
    exit; // Stop further execution
  }
  $api_key = "AIzaSyBQeKTHOfJHUc92IYtvHzQvj-vFXysqMqQ"; // Replace with your API key
  // Fetch channel information
  $apiu = @file_get_contents('https://www.googleapis.com/youtube/v3/channels?part=snippet,statistics&id=' . $channel_id . '&key=' . $api_key);
  if ($apiu === false) {
    echo '<div class="alert alert-danger" role="alert">Failed to fetch channel information. Please try again later.</div>';
    include 'partials/footer.php';
    exit;
  }
  $apid = json_decode($apiu, true);
  // Check if channel information is empty
  if (empty($apid['items'])) {
    echo '<div class="alert alert-danger" role="alert">Channel not found.</div>';
    include 'partials/footer.php';
    exit;
  }
  $channel_info = $apid['items'][0];
  // Fetch videos from the channel
  $videos = getAllVideos($api_key, $channel_id);
  // Check if videos are empty
  if (empty($videos)) {
    echo '<div class="alert alert-warning" role="alert">No videos found for this channel.</div>';
  }
  ?>
  <!-- Channel Info -->
  <div class="row justify-content-center mt-4">
    <div class="col-md-8">
      <div class="row align-items-center">
        <div class="col-md-4">
          <img src="<?php echo $channel_info['snippet']['thumbnails']['high']['url']; ?>" class="img-fluid" alt="Channel Thumbnail">
        </div>
        <div class="col-md-8">
          <h5><?php echo $channel_info['snippet']['title']; ?></h5>
          <p><?php echo $channel_info['snippet']['description']; ?></p>
          <p>
            <strong>Subscribers:</strong> <?php echo number_format($channel_info['statistics']['subscriberCount'], 2, '.', ','); ?> |
            <strong>Views:</strong> <?php echo number_format($channel_info['statistics']['viewCount'], 2, '.', ','); ?> |
            <strong>Videos:</strong> <?php echo $channel_info['statistics']['videoCount']; ?>
          </p>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <!-- Video List -->
  <div class="row">
    <div class="col-md-12">
      <ul class="list-group my-2">
        <?php
        foreach ($videos as $video) {
        ?>
          <li class="list-group-item">
            <div class="row align-items-center">
              <div class="col-md-2">
                <img src="<?php echo $video['thumbnail']; ?>" class="img-fluid" alt="Video Thumbnail">
              </div>
              <div class="col-md-10">
                <h5><a href="https://www.youtube.com/watch?v=<?php echo $video['id']; ?>"><?php echo $video['title']; ?></a></h5>
                <p class="mb-1"><?php echo $video['description']; ?></p>
                <small class="text-muted">
                  <strong>Views:</strong> <?php echo $video['viewCount']; ?> |
                  <strong>Likes:</strong> <?php echo $video['likeCount']; ?> |
                  <strong>Dislikes:</strong> <?php echo $video['dislikeCount']; ?> |
                  <strong>Comments:</strong> <?php echo $video['commentCount']; ?>
                </small>
              </div>
            </div>
          </li>
        <?php
        }
        ?>
      </ul>
    </div>
  </div>
</div>
<!-- /.container-fluid -->
<?php include 'partials/footer.php'; ?>
<?php
// Function to get all videos from the channel with pagination
function getAllVideos($api_key, $channel_id)
{
  $videos = array();
  $nextPageToken = '';
  do {
    // Fetch videos from YouTube Data API
    $apiData = @file_get_contents('https://www.googleapis.com/youtube/v3/search?key=' . $api_key . '&channelId=' . $channel_id . '&part=snippet,id&order=date&maxResults=50&pageToken=' . $nextPageToken);
    if ($apiData !== false) {
      $api_response_decoded = json_decode($apiData, true);
      if (!empty($api_response_decoded['items'])) {
        foreach ($api_response_decoded['items'] as $item) {
          if (isset($item['id']['videoId'])) {
            $video = array(
              'id' => $item['id']['videoId'],
              'title' => $item['snippet']['title'],
              'description' => $item['snippet']['description'],
              'thumbnail' => $item['snippet']['thumbnails']['high']['url']
            );
            // Fetch video statistics
            $gc = file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=statistics&id=' . $item['id']['videoId'] . '&key=' . $api_key);
            $gcc = json_decode($gc, true);
            if (!empty($gcc['items'])) {
              $video['viewCount'] = $gcc['items'][0]['statistics']['viewCount'];
              $video['likeCount'] = $gcc['items'][0]['statistics']['likeCount'];
              $video['dislikeCount'] = $gcc['items'][0]['statistics']['dislikeCount'];
              $video['commentCount'] = $gcc['items'][0]['statistics']['commentCount'];
            }
            $videos[] = $video;
          }
        }
      }
      $nextPageToken = isset($api_response_decoded['nextPageToken']) ? $api_response_decoded['nextPageToken'] : '';
    } else {
      // Error handling for API request failure
      echo '<div class="alert alert-danger" role="alert">Failed to fetch videos from YouTube. Please try again later.</div>';
      include 'partials/footer.php';
      exit;
    }
  } while (!empty($nextPageToken));
  return $videos;
}
?>