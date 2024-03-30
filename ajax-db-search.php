<?php
require_once "conn-d.php";
if (isset($_GET['term'])) {
    $term = $_GET['term'] . '%';
    $query = "SELECT * FROM ngarkimi WHERE kengetari LIKE ? GROUP BY kengetari";

    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $term);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $res = array();
        while ($user = mysqli_fetch_array($result)) {
            $res[] = $user['kengetari'];
        }

        mysqli_stmt_close($stmt);

        // Return JSON response
        echo json_encode($res);
    } else {
        // Handle database error
        echo json_encode(array("error" => "Database error."));
    }
}
