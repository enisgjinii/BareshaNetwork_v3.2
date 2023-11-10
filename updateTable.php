<?php
include 'conn-d.php';




// Check if form is submitted, and update the table in the database
if (isset($_POST['submit'])) {
    $id = $_POST['id'];  // get the id of the row you want to update

    // get new values from the form fields
    $edit_youtube = isset($_POST['edit_youtube']) ? 1 : 0;
    $edit_spotify = isset($_POST['edit_spotify']) ? 1 : 0;
    $edit_apple = isset($_POST['edit_apple']) ? 1 : 0;
    $edit_deezer = isset($_POST['edit_deezer']) ? 1 : 0;
    $edit_soundcloud = isset($_POST['edit_soundcloud']) ? 1 : 0;
    $edit_youtube_music = isset($_POST['edit_youtube_music']) ? 1 : 0;
    $edit_amazon = isset($_POST['edit_amazon']) ? 1 : 0;
    $edit_google = isset($_POST['edit_google']) ? 1 : 0;

    // update the row with the new values
    $sql = "UPDATE music_links SET 
        youtube = '$edit_youtube',
        spotify = '$edit_spotify',
        apple = '$edit_apple',
        deezer = '$edit_deezer',
        soundcloud = '$edit_soundcloud',
        youtubemusic = '$edit_youtube_music',
        amazon = '$edit_amazon',
        google = '$edit_google'
        WHERE id = $id";  // use the 'id' column to identify the row to update

    if ($conn->query($sql) === TRUE) {
        header("Location: check_musics.php");
        exit();
    } else {
        echo "Error updating row: " . $conn->error;
    }
    $conn->close();
}
