<?php
include 'conn-d.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $conn->autocommit(false); // Çaktivizoni autokomitimin për të filluar transaksionin manualisht

        $query = "DELETE FROM expenses WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id); // 'i' tregon që variabla është integer
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $conn->commit(); // Kryejini transaksionin nëse nuk ka asnjë gabim
            echo 'success';
        } else {
            $conn->rollback(); // Zhbëjini transaksionin nëse nuk gjendet asnjë rresht për të fshirë
            echo 'No record found for deletion.';
        }
    } catch (Exception $e) {
        $conn->rollback(); // Zhbëjini transaksionin nëse ndodh një gabim
        echo 'Database error: ' . $e->getMessage();
    } finally {
        $conn->autocommit(true); // Rikthej autokomitimin në gjendjen fillestare
    }
}
?>
