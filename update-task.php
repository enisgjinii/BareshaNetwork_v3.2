<?php
// database connection code

include('conn-d.php');
// update task in database
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $task = $_POST["task"];
    $label = $_POST["label"];
    $due_date = $_POST["due_date"];

    $sql = "UPDATE detyra SET detyra='$task', etiketa='$label', data='$due_date' WHERE id='$id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: todo_list.php");
    } else {
        echo "Error updating task: " . mysqli_error($conn);
    }
}

// close database connection
mysqli_close($conn);
?>