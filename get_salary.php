<?php
// get_salary.php
require_once('conn-d.php');

if (isset($_GET['id'])) {
    $employeeId = $_GET['id'];

    // Fetch salary from the database based on the selected employee ID
    $get_salary = $conn->query("SELECT salary FROM googleauth WHERE id = $employeeId");

    if ($get_salary->num_rows > 0) {
        $row = $get_salary->fetch_assoc();
        echo $row['salary'];
    } else {
        echo 'Salary not available';
    }
} else {
    echo 'Invalid request';
}
?>
