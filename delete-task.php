<?php
include('conn-d.php');

// Check if task ID has been provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete task from database
    $sql = "DELETE FROM detyra WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        echo "Task deleted successfully";
    } else {
        echo "Error deleting task: " . mysqli_error($conn);
    }
}

// Redirect to task list page
header("Location: todo_list.php");
exit();
?>