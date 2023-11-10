<?php
    // Make a connection to the database
    include("conn-d.php");

    // Check if the form was submitted
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // Get the form data
        $id = $_POST['id'];
        $emri_ofertes = $_POST['emri_ofertes'];
        $klienti = $_POST['klienti'];
        $kohezgjatja = $_POST['kohezgjatja'];

        // Update the row with the new data
        $query = "UPDATE ofertat SET emri_ofertes='$emri_ofertes', klienti='$klienti', kohezgjatja='$kohezgjatja' WHERE id='$id'";
        $result = mysqli_query($conn, $query);

        // Redirect to the original page with the updated data
        header("Location: ofertat.php");
        exit();
    }
?>
