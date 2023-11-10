
<?php
include 'conn-d.php';
$merri = $conn->query("SELECT * FROM users ORDER BY (aksesi) ASC");
$data = array();
while ($k = mysqli_fetch_array($merri)) {
    $data[] = array(
        $k['name'],
        $k['perdoruesi'],
        $k['aksesi'],
        '<a class="btn btn-danger shadow-sm" href="delete.php?id=' . $k['id'] . ' data-bs-toggle="modal" data-bs-target="#deleteModal">
    <i class="fi fi-rr-trash"></i>
  </a>
  <a class="btn btn-primary shadow-sm" href="edit.php?id=' . $k['id'] . ' data-bs-toggle="modal" data-target="#editModal">
    <i class="fi fi-rr-edit"></i>
  </a>'

    );
}

echo json_encode(array('data' => $data));
?>
