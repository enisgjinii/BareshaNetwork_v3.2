<?php
    // Connect to the database
    include('conn-d.php');
    
    // Get the data from the form
    $note_id = $_POST['note_id'];
    $new_shenimi = $_POST['new_shenimi'];
    $new_data = $_POST['new_data'];

    // Update the note in the database
    $sql = "UPDATE shenime SET shenimi = '$new_shenimi', data = '$new_data' WHERE id = '$note_id'";
    mysqli_query($conn, $sql);

    // Redirect to the notes list page
    header('Location: notes.php');
?>
