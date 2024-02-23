
<?php

// Database connection
include 'conn-d.php';

// Process submitted data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $platforms = $_POST['platforms'];
    $videoId = $_POST['video_id'];
    $title = $_POST['title'];
    $youtube = in_array("youtube", $platforms) ? 1 : 0;
    $spotify = in_array("spotify", $platforms) ? 1 : 0;
    $apple_music = in_array("apple-music", $platforms) ? 1 : 0;
    $deezer = in_array("deezer", $platforms) ? 1 : 0;
    $soundcloud = in_array("soundcloud", $platforms) ? 1 : 0;
    $youtube_music = in_array("youtube-music", $platforms) ? 1 : 0;
    $amazon_music = in_array("amazon-music", $platforms) ? 1 : 0;
    $google_play_music = in_array("google-play-music", $platforms) ? 1 : 0;
    $stmt = $conn->prepare("INSERT INTO videos (video_id, title, youtube, spotify, apple_music, deezer, soundcloud, youtube_music, amazon_music, google_play_music) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiiiiiii", $videoId, $title, $youtube, $spotify, $apple_music, $deezer, $soundcloud, $youtube_music, $amazon_music, $google_play_music);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    exit();
}
?>