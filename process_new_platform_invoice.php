<?php
// Connect to the database
require_once "conn-d.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Client and time-related details
    $client_id = $_POST["id_of_client"];
    $title_of_song = $_POST["title_of_song"];
    $month = $_POST["month"];
    $year = $_POST["year"];

    // Prepare and execute the statement
    $stmt = $conn->prepare("INSERT INTO platform_invoices (client_id,title_of_song ,amazon_music_income, anghami_income, apple_music_income, audiomack_income, deezer_income, facebook_income, iheartradio_income, kkbox_income, medianet_income, netease_income, qobuz_income, resso_income, saavn_income, soundtrack_income, spotify_income, tencent_income, tidal_income, tiktok_income, youtube_income, total_income, tax_withholding, month, year) VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Bind parameters dynamically
    $stmt->bind_param(
        "sssssssssssssssssssssssss",
        $client_id,
        $title_of_song,
        $_POST["amazon_music_income"],
        $_POST["anghami_income"],
        $_POST["apple_music_income"],
        $_POST["audiomack_income"],
        $_POST["deezer_income"],
        $_POST["facebook_income"],
        $_POST["iheartradio_income"],
        $_POST["kkbox_income"],
        $_POST["medianet_income"],
        $_POST["netease_income"],
        $_POST["qobuz_income"],
        $_POST["resso_income"],
        $_POST["saavn_income"],
        $_POST["soundtrack_income"],
        $_POST["spotify_income"],
        $_POST["tencent_income"],
        $_POST["tidal_income"],
        $_POST["tiktok_income"],
        $_POST["youtube_income"],
        $_POST["totalAmount"],
        $_POST["tax"],
        $month,
        $year
    );

    $stmt->execute();
    $stmt->close();

    header("Location: quick_platform_invoice.php");
    exit();
}

$conn->close();
