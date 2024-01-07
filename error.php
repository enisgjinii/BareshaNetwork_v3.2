<?php
// Start session
session_start();

// Include config
include 'conn-d.php';
$user_credentials = $_SESSION['id'];

// Merr URL-në aktuale
$current_url = $_SERVER['REQUEST_URI'];

// Merr emrin e skedarit nga URL-ja aktuale
$filename = basename($current_url);

// Kërkesa SQL e përgatitur me deklaratën e përgatitur
$stmt = $conn->prepare("SELECT googleauth.firstName AS user_name, roles.name AS role_name, roles.id AS role_id, GROUP_CONCAT(DISTINCT role_pages.page) AS pages
FROM googleauth
LEFT JOIN user_roles ON googleauth.id = user_roles.user_id
LEFT JOIN roles ON user_roles.role_id = roles.id
LEFT JOIN role_pages ON roles.id = role_pages.role_id
WHERE googleauth.id = ?
GROUP BY googleauth.id, roles.id");

$stmt->bind_param("i", $user_credentials);
$stmt->execute();
$result = $stmt->get_result();

$accessible_pages = array();

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $menu_pages = explode(',', $row['pages']);
        $accessible_pages = $menu_pages;
    }

    $result->free();
}
?>


<!DOCTYPE html>
<html lang="sq">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Qasja e Ndaluara</title>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.css" rel="stylesheet" />

    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            /* height: 100vh; */
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/autofill/2.6.0/css/autoFill.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/colreorder/1.7.0/css/colReorder.bootstrap5.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/datetime/1.5.1/css/dataTables.dateTime.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.bootstrap5.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.bootstrap5.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/keytable/2.11.0/css/keyTable.bootstrap5.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/rowgroup/1.4.1/css/rowGroup.bootstrap5.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/rowreorder/1.4.1/css/rowReorder.bootstrap5.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/scroller/2.3.0/css/scroller.bootstrap5.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/searchbuilder/1.6.0/css/searchBuilder.bootstrap5.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/searchpanes/2.2.0/css/searchPanes.bootstrap5.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/select/1.7.0/css/select.bootstrap5.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/staterestore/1.3.0/css/stateRestore.bootstrap5.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="text-center">
            <h1> Qasja e ndaluar!</h1>
            <img src="images/warning.gif" alt="" style="width: 100px">
        </div>
        <hr>
        <p>
            Na vjen keq, por ju nuk keni leje për të hyrë në këtë faqe.
        </p>
        <p>
            Kjo mund të ndodhë për shkak të mungesës së faqes që po kërkoni ose për shkak të mungesës së lejeve të nevojshme.
        </p>
        <p>Refuzimi juaj ndodhi në faqen '<i><?php echo $_SESSION['page']; ?></i>'</p>
        <!-- Display a table where the user has access -->
        <h5>Faqet në të cilat keni qasje:</h5>

        <table class="table table-bordered" id="example">
            <thead>
                <tr>
                    <th>Faqja</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accessible_pages as $page) : ?>
                    <tr>
                        <td><?php echo $page; ?></td>
                        <td><a href="<?php echo $page; ?>" class="btn btn-primary rounded-5"><i class="fas fa-external-link-alt"></i></a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="index.php" class="btn btn-primary mb-3" style="text-transform:none;">Kthehu në panel</a>
    </div>



    <!-- MDB -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/autofill/2.6.0/js/dataTables.autoFill.js"></script>
    <script src="https://cdn.datatables.net/autofill/2.6.0/js/autoFill.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.js"></script>
    <script src="https://cdn.datatables.net/colreorder/1.7.0/js/dataTables.colReorder.js"></script>
    <script src="https://cdn.datatables.net/datetime/1.5.1/js/dataTables.dateTime.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.js"></script>
    <script src="https://cdn.datatables.net/keytable/2.11.0/js/dataTables.keyTable.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/rowgroup/1.4.1/js/dataTables.rowGroup.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.4.1/js/dataTables.rowReorder.js"></script>
    <script src="https://cdn.datatables.net/scroller/2.3.0/js/dataTables.scroller.js"></script>
    <script src="https://cdn.datatables.net/searchbuilder/1.6.0/js/dataTables.searchBuilder.js"></script>
    <script src="https://cdn.datatables.net/searchbuilder/1.6.0/js/searchBuilder.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/searchpanes/2.2.0/js/dataTables.searchPanes.js"></script>
    <script src="https://cdn.datatables.net/searchpanes/2.2.0/js/searchPanes.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.js"></script>
    <script src="https://cdn.datatables.net/staterestore/1.3.0/js/dataTables.stateRestore.js"></script>
    <script src="https://cdn.datatables.net/staterestore/1.3.0/js/stateRestore.bootstrap5.js"></script>

    <script>
        $(document).ready(function() {
            $('#example').DataTable({
                dom: 'Bfrtip',
                lengthMenu: [
                    [3, 7, 15, -1],
                    [3, 7, 15, "Show all"]
                ],
                buttons: [{
                    extend: 'pageLength',
                    className: 'btn btn-primary'
                }],
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
                },
            });
        });
    </script>


</body>

</html>