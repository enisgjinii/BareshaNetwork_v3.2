<?php
include 'partials/header.php';
// Define your YouTube Data API key here
$api_key = "AIzaSyBQeKTHOfJHUc92IYtvHzQvj-vFXysqMqQ"; // <-- Replace with your actual API key
// Function to sanitize output
function sanitize_output($data)
{
  return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
// Handle AJAX request for fetching comments
if (isset($_GET['video_id']) && !empty($_GET['video_id'])) {
  header('Content-Type: application/json');
  $video_id = sanitize_output($_GET['video_id']);
  // Function to fetch comments with caching
  function getComments($api_key, $video_id)
  {
    $cache_dir = 'cache';
    if (!is_dir($cache_dir)) {
      mkdir($cache_dir, 0755, true);
    }
    $cache_file = $cache_dir . '/comments_' . $video_id . '.json';
    $cache_time = 3600; // 1 hour
    if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_time)) {
      $comments = json_decode(file_get_contents($cache_file), true);
    } else {
      $comments = [];
      $nextPageToken = '';
      $maxComments = 20; // Limit to first 20 comments
      do {
        $apiUrl = 'https://www.googleapis.com/youtube/v3/commentThreads?key=' . $api_key . '&textFormat=plainText&part=snippet&videoId=' . $video_id . '&maxResults=100&pageToken=' . $nextPageToken;
        $response = @file_get_contents($apiUrl);
        if ($response === false) {
          return [
            'success' => false,
            'message' => 'Failed to fetch comments.'
          ];
        }
        $data = json_decode($response, true);
        if (!empty($data['items'])) {
          foreach ($data['items'] as $item) {
            $comment = [
              'author' => sanitize_output($item['snippet']['topLevelComment']['snippet']['authorDisplayName']),
              'text' => sanitize_output($item['snippet']['topLevelComment']['snippet']['textDisplay']),
              'publishedAt' => date('F j, Y, g:i a', strtotime($item['snippet']['topLevelComment']['snippet']['publishedAt']))
            ];
            $comments[] = $comment;
            if (count($comments) >= $maxComments) {
              break 2; // Exit both foreach and do-while
            }
          }
        }
        $nextPageToken = isset($data['nextPageToken']) ? $data['nextPageToken'] : '';
      } while (!empty($nextPageToken) && count($comments) < $maxComments);
      // Cache the comments
      file_put_contents($cache_file, json_encode($comments));
    }
    return [
      'success' => true,
      'comments' => $comments
    ];
  }
  $result = getComments($api_key, $video_id);
  echo json_encode($result);
  exit;
}
// Handle normal page rendering
// Validate the channel ID parameter
if (isset($_GET['id']) && !empty($_GET['id'])) {
  $channel_id = sanitize_output($_GET['id']);
} else {
  // Display error message and exit
  echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Invalid Channel ID</title>
    </head>
    <body>
        <div class="container mt-5">
            <div class="alert alert-danger" role="alert">Invalid channel ID.</div>
        </div>
    </body>
    </html>';
  exit;
}
// Function to fetch channel information with caching
function getChannelInfo($api_key, $channel_id)
{
  $cache_dir = 'cache';
  if (!is_dir($cache_dir)) {
    mkdir($cache_dir, 0755, true);
  }
  $cache_file = $cache_dir . '/channel_' . $channel_id . '.json';
  $cache_time = 3600; // 1 hour
  if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_time)) {
    $data = file_get_contents($cache_file);
  } else {
    $api_url = 'https://www.googleapis.com/youtube/v3/channels?part=snippet,statistics&id=' . $channel_id . '&key=' . $api_key;
    $data = @file_get_contents($api_url);
    if ($data !== false) {
      file_put_contents($cache_file, $data);
    }
  }
  return json_decode($data, true);
}
// Function to fetch all videos from the channel with caching
function getAllVideos($api_key, $channel_id)
{
  $cache_dir = 'cache';
  if (!is_dir($cache_dir)) {
    mkdir($cache_dir, 0755, true);
  }
  $cache_file = $cache_dir . '/videos_' . $channel_id . '.json';
  $cache_time = 3600; // 1 hour
  if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_time)) {
    $videos = json_decode(file_get_contents($cache_file), true);
  } else {
    $videos = array();
    $nextPageToken = '';
    do {
      // Fetch videos from YouTube Data API
      $apiUrl = 'https://www.googleapis.com/youtube/v3/search?key=' . $api_key . '&channelId=' . $channel_id . '&part=snippet,id&order=date&maxResults=50&pageToken=' . $nextPageToken;
      $apiData = @file_get_contents($apiUrl);
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
              $gc = @file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=statistics&id=' . $item['id']['videoId'] . '&key=' . $api_key);
              $gcc = json_decode($gc, true);
              if (!empty($gcc['items'])) {
                $video['viewCount'] = isset($gcc['items'][0]['statistics']['viewCount']) ? number_format($gcc['items'][0]['statistics']['viewCount']) : 'N/A';
                $video['likeCount'] = isset($gcc['items'][0]['statistics']['likeCount']) ? number_format($gcc['items'][0]['statistics']['likeCount']) : 'N/A';
                $video['dislikeCount'] = isset($gcc['items'][0]['statistics']['dislikeCount']) ? number_format($gcc['items'][0]['statistics']['dislikeCount']) : 'N/A';
                $video['commentCount'] = isset($gcc['items'][0]['statistics']['commentCount']) ? number_format($gcc['items'][0]['statistics']['commentCount']) : 'N/A';
              }
              $videos[] = $video;
            }
          }
        }
        $nextPageToken = isset($api_response_decoded['nextPageToken']) ? $api_response_decoded['nextPageToken'] : '';
      } else {
        // Handle API request failure
        echo '<!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <title>Error</title>
                </head>
                <body>
                    <div class="container mt-5">
                        <div class="alert alert-danger" role="alert">Failed to fetch videos from YouTube. Please try again later.</div>
                    </div>
                </body>
                </html>';
        exit;
      }
    } while (!empty($nextPageToken));
    // Cache the videos data
    file_put_contents($cache_file, json_encode($videos));
  }
  return $videos;
}
// Fetch channel information
$apid = getChannelInfo($api_key, $channel_id);
// Check if channel information is empty
if (empty($apid['items'])) {
  echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Channel Not Found</title>
    </head>
    <body>
        <div class="container mt-5">
            <div class="alert alert-danger" role="alert">Channel not found.</div>
        </div>
    </body>
    </html>';
  exit;
}
$channel_info = $apid['items'][0];
// Fetch videos from the channel
$videos = getAllVideos($api_key, $channel_id);
// Check if videos are empty
if (empty($videos)) {
  echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>No Videos Found</title>
    </head>
    <body>
        <div class="container mt-5">
            <div class="alert alert-warning" role="alert">No videos found for this channel.</div>
        </div>
    </body>
    </html>';
  exit;
}
// Define cache directory
$cache_dir = 'cache';
if (!is_dir($cache_dir)) {
  mkdir($cache_dir, 0755, true);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title><?php echo sanitize_output($channel_info['snippet']['title']); ?> - YouTube Channel</title>
  <!-- <style>
    /* Custom CSS */
    .rounded-custom {
      border-radius: 15px !important;
    }
    .input-custom-css {
      background-color: #007bff;
      /* Primary color */
      color: #fff;
      border: none;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .input-custom-css:hover {
      background-color: #0056b3;
      /* Darker shade on hover */
    }
    .badge-primary text-dark  {
      background-color: #f8f9fa;
      /* bg-light equivalent */
      border: 1px solid #dee2e6;
      /* border border-1 equivalent */
      color: #6c757d;
      /* Optional: text color */
    }
    .card-custom {
      border: none;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .accordion-button-custom {
      background-color: #f8f9fa;
      border: none;
      border-radius: 0.375rem;
      color: #495057;
    }
    .accordion-button-custom:not(.collapsed) {
      background-color: #e2e6ea;
    }
    /* Loading Spinner */
    .spinner-border-custom {
      width: 3rem;
      height: 3rem;
      border-width: 0.3em;
    }
    /* Ensure images do not exceed their container */
    .card-img-top {
      object-fit: cover;
      height: 200px;
    }
    /* Truncate text with ellipsis */
    .card-text {
      overflow: hidden;
      text-overflow: ellipsis;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      /* Number of lines to show */
      -webkit-box-orient: vertical;
    }
  </style> -->
</head>

<body>
  <!-- Begin Page Content -->
  <div class="container-fluid mt-5">
    <!-- Channel Info -->
    <div class="row justify-content-center mt-4">
      <div class="col-md-8">
        <div class="card card-custom rounded-custom">
          <div class="row g-0">
            <div class="col-md-4 text-center p-4">
              <img src="<?php echo sanitize_output($channel_info['snippet']['thumbnails']['high']['url']); ?>" class="img-fluid rounded-circle border border-3 border-primary" alt="Channel Thumbnail">
            </div>
            <div class="col-md-8">
              <div class="card-body">
                <h3 class="card-title"><?php echo sanitize_output($channel_info['snippet']['title']); ?></h3>
                <p class="card-text"><?php echo sanitize_output($channel_info['snippet']['description']); ?></p>
                <p>
                  <span class="badge badge-primary text-dark  me-2">Subscribers: <?php echo number_format($channel_info['statistics']['subscriberCount'], 0, '.', ','); ?></span>
                  <span class="badge badge-primary text-dark  me-2">Views: <?php echo number_format($channel_info['statistics']['viewCount'], 0, '.', ','); ?></span>
                  <span class="badge badge-primary text-dark ">Videos: <?php echo number_format($channel_info['statistics']['videoCount'], 0, '.', ','); ?></span>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <hr>
    <!-- Video List -->
    <div class="row" id="video-container">
      <?php
      // Display only the first 3 videos initially
      $initial_videos = array_slice($videos, 0, 3);
      foreach ($initial_videos as $index => $video) {
      ?>
        <div class="col-md-4 mb-4">
          <div class="card card-custom rounded-custom h-100">
            <img src="<?php echo sanitize_output($video['thumbnail']); ?>" class="card-img-top rounded-custom" alt="Video Thumbnail">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title">
                <a href="https://www.youtube.com/watch?v=<?php echo sanitize_output($video['id']); ?>" target="_blank" class="text-decoration-none"><?php echo sanitize_output($video['title']); ?></a>
              </h5>
              <p class="card-text"><?php echo sanitize_output(substr($video['description'], 0, 100)) . '...'; ?></p>
              <div class="mt-auto">
                <span class="badge badge-primary text-dark  me-1">Views: <?php echo $video['viewCount']; ?></span>
                <span class="badge badge-primary text-dark  me-1">Likes: <?php echo $video['likeCount']; ?></span>
                <span class="badge badge-primary text-dark  me-1">Dislikes: <?php echo $video['dislikeCount']; ?></span>
                <span class="badge badge-primary text-dark ">Comments: <?php echo $video['commentCount']; ?></span>
              </div>
              <!-- Comments Accordion -->
              <?php if ($video['commentCount'] > 0): ?>
                <div class="accordion mt-3" id="accordionComments<?php echo $index; ?>">
                  <div class="accordion-item">
                    <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                      <button class="accordion-button accordion-button-custom collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>" aria-expanded="false" aria-controls="collapse<?php echo $index; ?>" aria-label="Toggle comments">
                        View Comments
                      </button>
                    </h2>
                    <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#accordionComments<?php echo $index; ?>">
                      <div class="accordion-body">
                        <div id="comments<?php echo $index; ?>">
                          <div class="d-flex justify-content-center align-items-center">
                            <div class="spinner-border spinner-border-custom text-primary" role="status">
                              <span class="visually-hidden">Loading...</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php
      }
      ?>
    </div>
    <!-- Load More Button -->
    <?php if (count($videos) > 3): ?>
      <div class="text-center mb-5">
        <button id="load-more" class="btn input-custom-css rounded-pill px-3 py-2">Load More</button>
      </div>
    <?php endif; ?>
  </div>
  <!-- /.container-fluid -->
  <!-- Bootstrap JS and dependencies (Popper.js) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Remaining videos after initial load
      let videos = <?php echo json_encode(array_slice($videos, 3)); ?>;
      let loaded = 0;
      const loadCount = 3; // Number of videos to load each time
      const videoContainer = document.getElementById('video-container');
      const loadMoreButton = document.getElementById('load-more');
      if (videos.length === 0) {
        if (loadMoreButton) {
          loadMoreButton.style.display = 'none';
        }
      }
      loadMoreButton?.addEventListener('click', function() {
        const nextVideos = videos.slice(loaded, loaded + loadCount);
        nextVideos.forEach((video, index) => {
          const globalIndex = loaded + index;
          // Create video card
          const col = document.createElement('div');
          col.className = 'col-md-4 mb-4';
          const card = document.createElement('div');
          card.className = 'card card-custom rounded-custom h-100';
          const img = document.createElement('img');
          img.src = video.thumbnail;
          img.className = 'card-img-top rounded-custom';
          img.alt = 'Video Thumbnail';
          const cardBody = document.createElement('div');
          cardBody.className = 'card-body d-flex flex-column';
          const title = document.createElement('h5');
          title.className = 'card-title';
          const link = document.createElement('a');
          link.href = `https://www.youtube.com/watch?v=${video.id}`;
          link.target = '_blank';
          link.className = 'text-decoration-none';
          link.textContent = video.title;
          title.appendChild(link);
          const description = document.createElement('p');
          description.className = 'card-text';
          description.textContent = video.description.length > 100 ? video.description.substring(0, 100) + '...' : video.description;
          const badgesDiv = document.createElement('div');
          badgesDiv.className = 'mt-auto';
          const viewsBadge = document.createElement('span');
          viewsBadge.className = 'badge badge-primary text-dark  me-1';
          viewsBadge.textContent = `Views: ${video.viewCount}`;
          const likesBadge = document.createElement('span');
          likesBadge.className = 'badge badge-primary text-dark  me-1';
          likesBadge.textContent = `Likes: ${video.likeCount}`;
          const dislikesBadge = document.createElement('span');
          dislikesBadge.className = 'badge badge-primary text-dark  me-1';
          dislikesBadge.textContent = `Dislikes: ${video.dislikeCount}`;
          const commentsBadge = document.createElement('span');
          commentsBadge.className = 'badge badge-primary text-dark ';
          commentsBadge.textContent = `Comments: ${video.commentCount}`;
          badgesDiv.appendChild(viewsBadge);
          badgesDiv.appendChild(likesBadge);
          badgesDiv.appendChild(dislikesBadge);
          badgesDiv.appendChild(commentsBadge);
          // Comments Accordion
          if (video.commentCount > 0) {
            const accordion = document.createElement('div');
            accordion.className = 'accordion mt-3';
            accordion.id = `accordionComments${globalIndex}`;
            const accordionItem = document.createElement('div');
            accordionItem.className = 'accordion-item';
            const accordionHeader = document.createElement('h2');
            accordionHeader.className = 'accordion-header';
            accordionHeader.id = `heading${globalIndex}`;
            const accordionButton = document.createElement('button');
            accordionButton.className = 'accordion-button accordion-button-custom collapsed';
            accordionButton.type = 'button';
            accordionButton.setAttribute('data-bs-toggle', 'collapse');
            accordionButton.setAttribute('data-bs-target', `#collapse${globalIndex}`);
            accordionButton.setAttribute('aria-expanded', 'false');
            accordionButton.setAttribute('aria-controls', `collapse${globalIndex}`);
            accordionButton.setAttribute('aria-label', 'Toggle comments');
            accordionButton.textContent = 'View Comments';
            accordionHeader.appendChild(accordionButton);
            accordionItem.appendChild(accordionHeader);
            const accordionCollapse = document.createElement('div');
            accordionCollapse.id = `collapse${globalIndex}`;
            accordionCollapse.className = 'accordion-collapse collapse';
            accordionCollapse.setAttribute('aria-labelledby', `heading${globalIndex}`);
            accordionCollapse.setAttribute('data-bs-parent', `#accordionComments${globalIndex}`);
            const accordionBody = document.createElement('div');
            accordionBody.className = 'accordion-body';
            const commentsDiv = document.createElement('div');
            commentsDiv.id = `comments${globalIndex}`;
            commentsDiv.innerHTML = `
                            <div class="d-flex justify-content-center align-items-center">
                                <div class="spinner-border spinner-border-custom text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        `;
            accordionBody.appendChild(commentsDiv);
            accordionCollapse.appendChild(accordionBody);
            accordionItem.appendChild(accordionCollapse);
            accordion.appendChild(accordionItem);
            cardBody.appendChild(accordion);
          }
          cardBody.appendChild(title);
          cardBody.appendChild(description);
          cardBody.appendChild(badgesDiv);
          card.appendChild(img);
          card.appendChild(cardBody);
          col.appendChild(card);
          videoContainer.appendChild(col);
        });
        loaded += loadCount;
        // Hide the button if all videos are loaded
        if (loaded >= videos.length) {
          loadMoreButton.style.display = 'none';
        }
      });
      // Function to fetch and display comments
      function fetchComments(videoId, commentsContainer) {
        fetch(`channel.php?video_id=${videoId}`)
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              if (data.comments.length === 0) {
                commentsContainer.innerHTML = '<p>No comments available.</p>';
              } else {
                let commentsHtml = '';
                data.comments.forEach(comment => {
                  commentsHtml += `
                                        <div class="mb-3">
                                            <h6>${comment.author}</h6>
                                            <p>${comment.text}</p>
                                            <small class="text-muted">${comment.publishedAt}</small>
                                            <hr>
                                        </div>
                                    `;
                });
                commentsContainer.innerHTML = commentsHtml;
              }
            } else {
              commentsContainer.innerHTML = `<p class="text-danger">${data.message}</p>`;
            }
          })
          .catch(error => {
            console.error('Error fetching comments:', error);
            commentsContainer.innerHTML = '<p class="text-danger">Failed to load comments.</p>';
          });
      }
      // Event delegation for dynamically added accordions
      document.body.addEventListener('shown.bs.collapse', function(event) {
        const collapseElement = event.target;
        const commentsDiv = collapseElement.querySelector('div[id^="comments"]');
        if (commentsDiv && commentsDiv.innerHTML.includes('spinner-border')) {
          // Extract video ID from the card
          const card = collapseElement.closest('.card');
          const videoLink = card.querySelector('.card-title a');
          const videoId = new URL(videoLink.href).searchParams.get('v');
          if (videoId) {
            fetchComments(videoId, commentsDiv);
          } else {
            commentsDiv.innerHTML = '<p class="text-danger">Invalid video ID.</p>';
          }
        }
      });
    });
  </script>
</body>

</html>