<?php
include 'conn-d.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $registruesi = $_POST['recipient-name'];
    $pershkrimi = $_POST['message'];
    $shuma = $_POST['amount'];

    $query = "UPDATE expenses SET registruesi='$registruesi', pershkrimi='$pershkrimi', shuma='$shuma' WHERE id='$id'";
    if ($conn->query($query) === TRUE) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }
}
