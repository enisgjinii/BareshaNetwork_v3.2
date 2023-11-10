<?php

include('conn-d.php');
// Check if form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve task data from form
    $task = $_POST['task'];
    $label = $_POST['label'];
    $due_date = $_POST['due_date'];

    // Insert task into database
    $sql = "INSERT INTO detyra (detyra, etiketa, data) VALUES ('$task', '$label', '$due_date')";

    if (mysqli_query($conn, $sql)) {
        echo "Task added successfully";
        header("Location: todo_list.php");
        exit();
    } else {
        echo "Error adding task: " . mysqli_error($conn);
    }
}
?>