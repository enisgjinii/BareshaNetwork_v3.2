<?php
header("Content-Type: application/json");

// Fetch the data with a modified URL (show=1000)
$json = file_get_contents('https://bareshamusic.sourceaudio.com/api/contentid/claims?token=6636-66f549fbe813b2087a8748f2b8243dbc&show=1000');

echo $json;
?>