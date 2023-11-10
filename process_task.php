<?php
// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $task = $_POST['task'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $due_time = $_POST['due_time'];
    $priority = $_POST['priority'];
    $project = $_POST['project'];
    $labels = $_POST['labels'];
    $assignee = $_POST['assignee'];

    // Concatenate due_date and due_time if due_time is set
    if (!empty($due_time)) {
        $due_date_time = $due_date . ' ' . $due_time;
    } else {
        $due_date_time = $due_date;
    }

    // Connect to database
    include('conn-d.php');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert task into database
    $sql = "INSERT INTO tasks (task, description, due_date, priority, project, assignee, labels)
            VALUES ('$task', '$description', '$due_date_time', '$priority', '$project', '$assignee', '" . implode(',', $labels) . "')";

    if ($conn->query($sql) === TRUE) {
        // Redirect back to add_task.php with success message
        header("Location: add_task.php?success=1");
    } else {
        // Redirect back to add_task.php with error message
        header("Location: add_task.php?error=1");
    }

    $conn->close();
}
?>