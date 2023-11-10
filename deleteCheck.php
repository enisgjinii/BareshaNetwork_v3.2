<?php

include 'conn-d.php';

$id = $_GET['id'];

$sql = "DELETE FROM music_links WHERE id=$id";
$result = mysqli_query($conn, $sql);

header("Location: check_musics.php"); // Redirect back to the main page
