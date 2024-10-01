<?php
include '../../conn-d.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the role ID and pages are submitted
    if (isset($_POST['role_id']) && isset($_POST['page'])) {
        // Retrieve the role ID and pages from the form data
        $roleId = $_POST['role_id'];
        $selectedPages = $_POST['page'];
        // Delete existing role pages for the specified role ID
        $deleteSql = "DELETE FROM role_pages WHERE role_id = $roleId";
        $conn->query($deleteSql);
        // Insert the updated role pages
        foreach ($selectedPages as $page) {
            $insertSql = "INSERT INTO role_pages (role_id, page) VALUES ($roleId, '$page')";
            $conn->query($insertSql);
        }
        // Redirect back to the edit_page.php with the updated role ID
        header("Location: ../../roles.php");
        exit();
    }
}
?>
