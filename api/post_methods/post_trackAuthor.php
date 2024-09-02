<?php
include '../../conn-d.php';

// Check if a POST request has been made
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if all necessary data has been provided
    if (isset($_POST['emri_mbiemri'], $_POST['emri_kenges'], $_POST['minutasha_kenges'], $_POST['link_youtube'], $_POST['link_platforma'], $_POST['roli'], $_POST['puntori_regjistrues'])) {
        $emri_mbiemri = mysqli_real_escape_string($conn, $_POST['emri_mbiemri']);
        $emri_kenges = mysqli_real_escape_string($conn, $_POST['emri_kenges']);
        $minutasha_kenges = mysqli_real_escape_string($conn, $_POST['minutasha_kenges']);
        $link_youtube = mysqli_real_escape_string($conn, $_POST['link_youtube']);
        $link_platforma = mysqli_real_escape_string($conn, $_POST['link_platforma']);
        $roli = mysqli_real_escape_string($conn, $_POST['roli']);
        $kompania = mysqli_real_escape_string($conn, $_POST['kompania']);
        $puntori_regjistrues = mysqli_real_escape_string($conn, $_POST['puntori_regjistrues']);
        $info_shtese = mysqli_real_escape_string($conn, $_POST['info_shtese']);

        // Create the query to insert the record into the "kenget_autori" table
        $query = "INSERT INTO kenget_autori (emri_autorit, emri_i_kenges, minutasha_e_kenges, link_youtube, link_platforma, roli, kompania, puntori_regjistrues, info_shtese) 
                  VALUES ('$emri_mbiemri', '$emri_kenges', '$minutasha_kenges', '$link_youtube', '$link_platforma', '$roli', '$kompania', '$puntori_regjistrues', '$info_shtese')";

        // Execute the query to insert the record
        if (mysqli_query($conn, $query)) {
            // Record added successfully
            header("Location: ../../autor.php");
            exit();
        } else {
            // Error while adding the record
            echo "Error adding record: " . mysqli_error($conn);
        }

        // Close the database connection
        mysqli_close($conn);
    } else {
        // Not all required form data has been provided
        echo "Please fill in all the necessary fields.";
    }
} else {
    // Request is not a POST request
    echo "Invalid request method.";
}
