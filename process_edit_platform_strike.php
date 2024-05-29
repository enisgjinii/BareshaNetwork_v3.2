<?php

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header('Content-Type: application/json'); // Set response type to JSON

    // Check if strike ID is provided
    if (isset($_POST['strike_id'])) {
        $strike_id = $_POST['strike_id'];

        // Connect to the database
        include 'conn-d.php';

        // Update strike details in the database
        $platforma = $_POST['platforma'];
        $titulli = $_POST['titulli'];
        $pershkrimi = $_POST['pershkrimi'];
        $data_e_krijimit = $_POST['data_e_krijimit'];
        $emaili = $_POST['emaili'];

        $update_query = "UPDATE platforms SET platforma='$platforma', titulli='$titulli', pershkrimi='$pershkrimi', data_e_krijimit='$data_e_krijimit', email_used='$emaili' WHERE id=$strike_id";

        if (mysqli_query($conn, $update_query)) {
            echo json_encode(["message" => "Të dhënat u përditësuan me sukses."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Gabim gjatë përditësimit të të dhënave: " . mysqli_error($conn)]);
        }

        // Close database connection
        mysqli_close($conn);
    } else {
        http_response_code(400);
        echo json_encode(["error" => "ID e goditjes nuk është dhënë."]);
    }
} else {
    // Redirect back to the main editing page if accessed directly
    header("Location: edit_platform_strike.php");
    exit;
}
