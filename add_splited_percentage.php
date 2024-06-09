<?php
include 'conn-d.php';

// Get POST data and sanitize
$splited_percentage = isset($_POST['splited_percentage']) ? mysqli_real_escape_string($conn, $_POST['splited_percentage']) : '';
$client_id = isset($_POST['client_id']) ? (int)$_POST['client_id'] : 0;

if ($client_id > 0 && !empty($splited_percentage)) {
    // Validate the input format
    $splits = explode(',', $splited_percentage);
    $valid = true;
    $total_split_percentage = 0;

    foreach ($splits as $split) {
        // Remove any leading or trailing whitespace and the percentage symbol
        $split = trim($split);
        $split = rtrim($split, '%');

        if (strpos($split, '-') === false) {
            $valid = false;
            break;
        }

        list($email, $percentage) = explode('-', $split);
        $email = trim($email);
        $percentage = trim($percentage);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !is_numeric($percentage) || (int)$percentage < 0 || (int)$percentage > 100) {
            $valid = false;
            break;
        }

        $total_split_percentage += (int)$percentage;
    }

    if ($valid) {
        // Get company percentage for the client
        $query = "SELECT perqindja FROM klientet WHERE id = $client_id";
        $result = mysqli_query($conn, $query);
        $company_percentage = 0;

        if ($result && $row = mysqli_fetch_array($result)) {
            $company_percentage = (int)$row['perqindja'];
        }

        // Calculate the new perqindja_e_klientit
        $client_percentage = 100 - $company_percentage;
        $remaining_percentage = $client_percentage - $total_split_percentage;

        if ($remaining_percentage >= 0) {
            // Update splited_percentage in the database
            $update_query = "UPDATE klientet SET splited_percentage = '$splited_percentage', perqindja_e_klientit = $client_percentage WHERE id = $client_id";
            if (mysqli_query($conn, $update_query)) {
                echo "<div class='alert alert-success rounded'>Përqindja e ndarë u përditësua me sukses dhe përqindja e klientit u llogarit.</div>";
            } else {
                echo "<div class='alert alert-danger rounded'>Gabim gjatë përditësimit të përqindjes së klientit: " . mysqli_error($conn) . "</div>";
            }
        } else {
            echo "<div class='alert alert-warning rounded'>Përqindja totale e ndarjes tejkalon përqindjen totale të klientit.</div>";
        }
    } else {
        echo "<div class='alert alert-warning rounded'>Formati i dhënë është i pavlefshëm.</div>";
    }
} else {
    echo "<div class='alert alert-warning rounded'>Të dhëna të pavlefshme të inputit.</div>";
}

mysqli_close($conn);
