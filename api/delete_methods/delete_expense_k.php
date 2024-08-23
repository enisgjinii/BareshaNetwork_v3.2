<?php
include '../../conn-d.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "DELETE FROM expenses WHERE id='$id'";
    if ($conn->query($query) === TRUE) {
        echo 'success';
    } else {
        echo 'error';
    }
}