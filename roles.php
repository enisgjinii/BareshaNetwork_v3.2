<?php
function generateCSRFToken()
{
    return bin2hex(random_bytes(32));
}
$csrf_token = generateCSRFToken();
setcookie('csrf_token', $csrf_token, time() + 3600, '/', '', false, true);
?>
<?php include 'partials/header.php'; ?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="breadcrumb bg-white rounded border">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a class="text-reset text-decoration-none">Menaxhimi</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="invoice.php" class="text-reset text-decoration-none">Rolet</a>
                    </li>
                </ol>
            </nav>
            <ul class="nav nav-pills mb-3 bg-white rounded mx-1" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">
                        <i class="fi fi-rr-smile-plus me-2 fa fa-lg"></i> Krijo rol të ri
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">
                        <i class="fi fi-rr-puzzle me-2 fa fa-lg"></i> Lista e roleve
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">
                        <i class="fi fi-rr-book-user me-2 fa fa-lg"></i> Lista e stafit dhe roleve
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded" id="pills-giveRole-tab" data-bs-toggle="pill" data-bs-target="#pills-giveRole" type="button" role="tab" aria-controls="pills-giveRole" aria-selected="false">
                        <i class="fi fi-rr-key me-2 fa fa-lg"></i> Akses i ri
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <!-- Krijo rol të ri -->
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                    <form method="post" action="api/post_methods/post_roles.php" class="my-3">
                        <div class="card rounded p-4">
                            <div class="mb-3">
                                <label class="form-label text-dark">Shkruani emrin e rolit</label>
                                <input type="text" name="role_name" class="form-control rounded" required oninvalid="this.setCustomValidity('Ju lutem plotësoni këtë fushë')" oninput="this.setCustomValidity('')">
                            </div>
                            <div class="d-flex justify-content-end mb-3">
                                <button type="button" class="btn btn-secondary me-2" onclick="checkAll()">Selekto të gjitha</button>
                                <button type="button" class="btn btn-secondary me-2" onclick="uncheckAll()">Hiqe selektimin</button>
                                <button type="submit" class="btn btn-primary">Krijo rolin</button>
                            </div>
                            <table id="tabelaEFaqeve" class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Emri i faqes</th>
                                        <th>Zgjedhe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    function format_page_name($page)
                                    {
                                        $button_html = '<br><a href="' . $page . '" target="_blank" class="btn btn-sm btn-outline-primary">Vizito faqen</a>';
                                        $descriptions = [
                                            'index.php' => 'Shtepia<br><p class="text-muted">Faqja e panelit i cila i shfaq te dhenat ne menyre te permbledhure</p>' . $button_html,
                                            'roles.php' => 'Rolet<br><p class="text-muted">Faqja në të cilen jepen rolet e stafit</p>' . $button_html,
                                            // Add other pages as needed
                                        ];
                                        return $descriptions[$page] ?? ucfirst($page) . $button_html;
                                    }
                                    $pages = [
                                        'stafi.php',
                                        'roles.php',
                                        'klient.php',
                                        'kategorit.php',
                                        'ads.php',
                                        'emails.php',
                                        'shtoy.php',
                                        'listang.php',
                                        'tiketa.php',
                                        'listat.php',
                                        'claim.php',
                                        // Add other pages as needed
                                    ];
                                    foreach ($pages as $page) {
                                        echo '<tr><td>' . format_page_name($page) . '</td><td><input type="checkbox" name="pages[]" value="' . $page . '"></td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
                <!-- Lista e roleve -->
                <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                    <div class="card rounded p-4 my-3">
                        <table id="example" class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Emri i rolit</th>
                                    <th>Faqet e aksesuara</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $loggedInUserRole = "Administrator";
                                $sql = 'SELECT roles.id AS role_id, roles.name AS role_name, GROUP_CONCAT(role_pages.page) AS pages
                                        FROM roles
                                        LEFT JOIN role_pages ON roles.id = role_pages.role_id
                                        GROUP BY roles.id';
                                if ($result = $conn->query($sql)) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<tr>';
                                        echo '<td>
                                                <a class="btn btn-warning btn-sm me-2" href="edit_page.php?role_id=' . $row['role_id'] . '"><i class="fi fi-rr-edit"></i></a>';
                                        if ($row['role_name'] != $loggedInUserRole) {
                                            echo '<button class="btn btn-danger btn-sm delete-row" data-role-id="' . $row['role_id'] . '"><i class="fi fi-rr-trash"></i></button>';
                                        }
                                        echo '</td>';
                                        echo '<td>' . htmlspecialchars($row['role_name']) . '</td>';
                                        echo '<td>
                                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#pagesModal-' . $row['role_id'] . '">Shfaq</button>
                                                <div class="modal fade" id="pagesModal-' . $row['role_id'] . '" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Faqet e rolit: ' . htmlspecialchars($row['role_name']) . '</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <ul>';
                                        foreach (explode(',', $row['pages']) as $page) {
                                            echo '<li>' . htmlspecialchars($page) . '</li>';
                                        }
                                        echo '              </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>';
                                        echo '</tr>';
                                    }
                                    $result->free();
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Lista e stafit dhe roleve -->
                <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                    <div class="card rounded p-4 my-3">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Emri i përdoruesit</th>
                                    <th>Emri i rolit</th>
                                    <th>Faqet</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = 'SELECT googleauth.firstName AS user_name, roles.name AS role_name, roles.id AS role_id, GROUP_CONCAT(DISTINCT role_pages.page) AS pages
                                        FROM googleauth
                                        LEFT JOIN user_roles ON googleauth.id = user_roles.user_id
                                        LEFT JOIN roles ON user_roles.role_id = roles.id
                                        LEFT JOIN role_pages ON roles.id = role_pages.role_id
                                        GROUP BY googleauth.id, roles.id';
                                if ($result = $conn->query($sql)) {
                                    while ($row = $result->fetch_assoc()) {
                                        if (!empty($row['pages'])) {
                                            echo '<tr>';
                                            echo '<td>' . htmlspecialchars($row['user_name']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['role_name']) . '</td>';
                                            echo '<td>
                                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#pagesAndStafModal-' . $row['role_id'] . '">Shfaq</button>
                                                    <div class="modal fade" id="pagesAndStafModal-' . $row['role_id'] . '" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-xl">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Faqet e rolit: ' . htmlspecialchars($row['role_name']) . '</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row">';
                                            foreach (explode(',', $row['pages']) as $page) {
                                                echo "<div class='col-3'><div class='p-2 bg-light rounded border mb-2'>" . htmlspecialchars($page) . "</div></div>";
                                            }
                                            echo '                  </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>';
                                            echo '</tr>';
                                        }
                                    }
                                    $result->free();
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Akses i ri -->
                <div class="tab-pane fade" id="pills-giveRole" role="tabpanel" aria-labelledby="pills-giveRole-tab">
                    <div class="card rounded p-4 my-3">
                        <form method="post" action="api/post_methods/post_save_user_role.php">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="mb-3">
                                <label for="user_id" class="form-label text-dark">Zgjidhni përdoruesin:</label>
                                <select class="form-select rounded" id="user_id" name="user_id" required>
                                    <?php
                                    $sql = "SELECT id, firstName, last_name, email FROM googleauth";
                                    if ($result = $conn->query($sql)) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['firstName'] . ' ' . $row['last_name']) . " - " . htmlspecialchars($row['email']) . "</option>";
                                        }
                                        $result->free();
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="role_id" class="form-label text-dark">Zgjidhni rolin:</label>
                                <select class="form-select rounded" id="role_id" name="role_id" required>
                                    <?php
                                    $sql = "SELECT id, name FROM roles";
                                    if ($result = $conn->query($sql)) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                                        }
                                        $result->free();
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Ruaj rolin e përdoruesit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'partials/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function checkAll() {
            document.querySelectorAll('input[name="pages[]"]').forEach(checkbox => checkbox.checked = true);
        }

        function uncheckAll() {
            document.querySelectorAll('input[name="pages[]"]').forEach(checkbox => checkbox.checked = false);
        }
        $(document).ready(function() {
            $('.delete-row').click(function() {
                var roleId = $(this).data('role-id');
                Swal.fire({
                    title: 'A je i sigurt?',
                    text: "Ju nuk do të jeni në gjendje ta ktheni këtë!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Po, fshini atë!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'api/delete_methods/delete_row.php',
                            type: 'POST',
                            data: {
                                role_id: roleId
                            },
                            success: function() {
                                Swal.fire('I fshirë!', 'Rreshti është fshirë.', 'success').then(() => location.reload());
                            },
                            error: function() {
                                Swal.fire('Error!', 'An error occurred while deleting the row.', 'error');
                            }
                        });
                    }
                });
            });
            $('#tabelaEFaqeve, #example').DataTable({
                responsive: true,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json"
                },
                buttons: [{
                        extend: 'pdfHtml5',
                        text: '<i class="fi fi-rr-file-pdf fa-lg"></i> PDF',
                        className: 'btn btn-sm btn-outline-secondary me-2'
                    },
                    {
                        extend: 'copyHtml5',
                        text: '<i class="fi fi-rr-copy fa-lg"></i> Kopjo',
                        className: 'btn btn-sm btn-outline-secondary me-2'
                    },
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fi fi-rr-file-excel fa-lg"></i> Excel',
                        className: 'btn btn-sm btn-outline-secondary me-2'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fi fi-rr-print fa-lg"></i> Printo',
                        className: 'btn btn-sm btn-outline-secondary me-2'
                    }
                ],
                dom: 'Bfrtip',
                initComplete: function() {
                    $('.dt-buttons').addClass('mb-3');
                }
            });
            $('.nav-link[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
                localStorage.setItem('activeTab', $(e.target).attr('id'));
            });
            var activeTab = localStorage.getItem('activeTab');
            if (activeTab) {
                var triggerEl = document.getElementById(activeTab);
                var tab = new bootstrap.Tab(triggerEl);
                tab.show();
            }
        });
    </script>
    <script>
        new Selectr('#user_id', {
            searchable: true
        });
        new Selectr('#role_id', {
            searchable: true
        });
    </script>
</div>