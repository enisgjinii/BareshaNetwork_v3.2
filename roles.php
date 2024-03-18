<?php
// Function to generate CSRF token
function generateCSRFToken()
{
    return bin2hex(random_bytes(32));
}

// Generate CSRF token and store it in a variable
$csrf_token = generateCSRFToken();

// Set CSRF token as a cookie
setcookie('csrf_token', $csrf_token, time() + 3600, '/', '', false, true); // Expiry time set to 1 hour

?>
<?php
include 'partials/header.php'; ?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Menaxhimi</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="invoice.php" class="text-reset" style="text-decoration: none;">
                            Rolet
                        </a>
                    </li>
            </nav>
            <ul class="nav nav-pills mb-3 bg-white rounded-5 mx-1" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-5 active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true" style="text-transform:none;"><i class="fi fi-rr-smile-plus me-2 fa fa-lg"></i> Krijo rol të ri</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-5" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false" style="text-transform:none;"><i class="fi fi-rr-puzzle me-2 fa fa-lg"></i>
                        Lista e roleve</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-5" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" aria-selected="false" style="text-transform:none;">
                        <i class="fi fi-rr-book-user me-2 fa fa-lg"></i> Lista e stafit dhe roleve</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-5" id="pills-giveRole-tab" data-bs-toggle="pill" data-bs-target="#pills-giveRole" type="button" role="tab" aria-controls="pills-giveRole" aria-selected="false" style="text-transform:none;">
                        <i class="fi fi-rr-key me-2 fa fa-lg"></i>Akses i ri</button>
                </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                    <form method="post" action="create_role.php" class="my-3">
                        <div class="card  rounded-5 p-5">
                            <div class="table-responsive">
                                <label>Shkruani emrin e rolit</label>
                                <div class="row my-3">
                                    <div class="col-6">
                                        <input type="text" name="role_name" class="form-control rounded-5  w-50" required>
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col">
                                        <button type="button" class="btn btn-primary rounded-5 float-right text-white" style="text-transform:none;" onclick="checkAll()">
                                            <i class="fi fi-rr-list-check me-2" style="display:inline-block;vertical-align:middle;"></i>
                                            <span style="display:inline-block;vertical-align:middle;">Selekto te
                                                gjitha</span>
                                        </button>
                                        <button type="button" class="btn btn-danger rounded-5 float-right text-white" style="text-transform:none;" onclick="uncheckAll()">
                                            <i class="fi fi-rr-list me-2" style="display:inline-block;vertical-align:middle;"></i>
                                            <span style="display:inline-block;vertical-align:middle;">Hiq
                                                selektimin</span>
                                        </button>
                                        <button type="submit" class="btn btn-warning text-white rounded-5 float-right" style="text-transform:none;">
                                            <i class="fi fi-rr-id-badge me-2" style="display:inline-block;vertical-align:middle;"></i>
                                            <span style="display:inline-block;vertical-align:middle;">Krijo rolin</span>
                                        </button>
                                    </div>
                                </div>
                                <br>
                                <?php
                                function format_page_name($page)
                                {
                                    if ($page == 'index.php') {
                                        return 'Shtepia';
                                    }
                                    if ($page == 'roles.php') {
                                        return 'Rolet';
                                    }
                                    if ($page == 'stafi.php') {
                                        return 'Klientet';
                                    }
                                    if ($page == 'ads.php') {
                                        return 'Llogaritë e ADS';
                                    }
                                    if ($page == 'emails.php') {
                                        return 'Lista e email-eve';
                                    }
                                    if ($page == 'klient.php') {
                                        return 'Lista e klientëve';
                                    }
                                    if ($page == 'kategorit.php') {
                                        return 'Lista e kategorive';
                                    }
                                    if ($page == 'claim.php') {
                                        return 'Recent Claim';
                                    }
                                    if ($page == 'tiketa.php') {
                                        return 'Lista e tiketave';
                                    }
                                    if ($page == 'listang.php') {
                                        return 'Lista e këngëve';
                                    }
                                    if ($page == 'shtoy.php') {
                                        return 'Regjistro këngë';
                                    }
                                    if ($page == 'listat.php') {
                                        return 'Lista e tiketave';
                                    }
                                    if ($page == 'tiketa.php') {
                                        return 'Tiket e re';
                                    }
                                    if ($page == 'whitelist.php') {
                                        return 'Whitelist';
                                    }
                                    if ($page == 'faturat.php') {
                                        return 'Pagesat Youtube';
                                    }
                                    if ($page == 'invoice.php') {
                                        return 'Pagesat Youtube_channel ( New )';
                                    }
                                    if ($page == 'pagesat_youtube.php') {
                                        return 'Pagesat YouTube ( Faza Test )';
                                    }
                                    if ($page == 'pagesat.php') {
                                        return 'Pagesat e kryera';
                                    }
                                    if ($page == 'rrogat.php') {
                                        return 'Pagat';
                                    }
                                    if ($page == 'shpenzimep.php') {
                                        return 'Shpenzimet personale';
                                    }
                                    if ($page == 'tatimi.php') {
                                        return 'Tatimi';
                                    }
                                    if ($page == 'yinc.php') {
                                        return 'Shpenzimet';
                                    }
                                    if ($page == 'filet.php') {
                                        return 'Dokumente tjera';
                                    }
                                    if ($page == 'klient_CSV.php') {
                                        return 'Klient CSV';
                                    }
                                    if ($page == 'logs.php') {
                                        return 'Logs';
                                    }
                                    if ($page == 'notes.php') {
                                        return 'Shenime';
                                    }
                                    if ($page == 'takimet.php') {
                                        return 'Takimet';
                                    }
                                    if ($page == 'todo_list.php') {
                                        return 'To Do';
                                    }
                                    if ($page == 'kontrata_2.php') {
                                        return 'Kontrata e re';
                                    }
                                    if ($page == 'lista_kontratave.php') {
                                        return 'Lista e kontratave';
                                    }
                                    if ($page == 'csvFiles.php') {
                                        return 'Inserto CSV';
                                    }
                                    if ($page == 'filtroCSV.php') {
                                        return 'Filtro CSV';
                                    }
                                    if ($page == 'listaEFaturaveTePlatformave.php') {
                                        return 'Lista e faturave';
                                    }
                                    if ($page == 'pagesatEKryera.php') {
                                        return 'Pagesat e perfunduara';
                                    }
                                    if ($page == 'dataYT.php') {
                                        return 'Statistikat nga Youtube';
                                    }
                                    if ($page == 'channel_selection.php') {
                                        return 'Kanalet';
                                    }
                                    if ($page == 'ofertat.php') {
                                        return 'Ofertat';
                                    }
                                    if ($page == 'kontrata_gjenelare_2.php') {
                                        return 'Kontrate e re ( Gjenerale )';
                                    }
                                    if ($page == 'lista_kontratave_gjenerale.php') {
                                        return 'Lista e kontratave ( Gjenerale )';
                                    }
                                    if ($page == 'facebook.php') {
                                        return 'Vegla Facebook';
                                    }
                                    if ($page == 'lista_faturave_facebook.php') {
                                        return 'Lista e faturave (Facebook)';
                                    }
                                    if ($page == 'autor.php') {
                                        return 'Autor';
                                    }
                                    if ($page == 'lista_kopjeve_rezerve.php') {
                                        return 'Lista e kopjeve rezerve';
                                    }
                                    if ($page == 'faturaFacebook.php') {
                                        return 'Krijo faturë (Facebook)';
                                    }
                                    if ($page == 'ascap.php') {
                                        return 'Ascap';
                                    }
                                    if ($page == 'klient-avanc.php') {
                                        return 'Lista e avanceve te klienteve';
                                    }
                                    if ($page == 'office_investments.php') {
                                        return 'Investimet e objektit';
                                    }
                                    if ($page == 'office_damages.php') {
                                        return 'Prishjet';
                                    }
                                    if ($page == 'office_requirements.php') {
                                        return 'Kerkesat';
                                    }
                                    if ($page == 'platform_invoices.php') {
                                        return 'Raportet e platformave';
                                    }
                                    if ($page == 'currency.php') {
                                        return 'Valutimi';
                                    }
                                    if ($page == 'rating_list.php') {
                                        return 'Lista e vlersimeve';
                                    }
                                    if ($page == 'invoice_list_2.php') {
                                        return 'Faturë e shpejtë';
                                    }
                                    if ($page == 'authenticated_channels.php') {
                                        return 'Kanalet e autentifikuara';
                                    }
                                }
                                $pages = array(
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
                                    'whitelist.php',
                                    'rrogat.php',
                                    'tatimi.php',
                                    'yinc.php',
                                    'shpenzimep.php',
                                    'faturat.php',
                                    'pagesat.php',
                                    'filet.php',
                                    'notes.php',
                                    'todo_list.php',
                                    'takimet.php',
                                    'klient_CSV.php',
                                    'logs.php',
                                    'kontrata_2.php',
                                    'lista_kontratave.php',
                                    'csvFiles.php',
                                    'filtroCSV.php',
                                    'listaEFaturaveTePlatformave.php',
                                    'pagesatEKryera.php',
                                    'dataYT.php',
                                    'ofertat.php',
                                    'kontrata_gjenelare_2.php',
                                    'lista_kontratave_gjenerale.php',
                                    'facebook.php',
                                    'lista_faturave_facebook.php',
                                    'autor.php',
                                    'faturaFacebook.php',
                                    'ascap.php',
                                    'klient-avanc.php',
                                    'office_investments.php',
                                    'office_damages.php',
                                    'office_requirements.php',
                                    'platform_invoices.php',
                                    'currency.php',
                                    'rating_list.php',
                                    'invoice_list_2.php',
                                    'authenticated_channels.php'
                                );
                                echo '<table id="tabelaEFaqeve" class="table table-bordered table-hover">';
                                echo '<thead class="bg-light"><tr><th>Emri i faqes</th><th>Zgjedhe</th></tr></thead>';
                                echo '<tbody>';
                                foreach ($pages as $page) {
                                    echo '<tr><td>' . format_page_name($page) . '</td><td><label><input type="checkbox" name="pages[]" value="' . $page . '"> </label></td></tr>';
                                }
                                echo '</tbody>';
                                echo '</table>';
                                ?>
                            </div>
                            <script>
                                function checkAll() {
                                    var checkboxes = document.getElementsByName('pages[]');
                                    for (var i = 0; i < checkboxes.length; i++) {
                                        checkboxes[i].checked = true;
                                    }
                                }
                            </script>
                            <script>
                                function uncheckAll() {
                                    var checkboxes = document.getElementsByName('pages[]');
                                    for (var i = 0; i < checkboxes.length; i++) {
                                        checkboxes[i].checked = false;
                                    }
                                }
                            </script>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                    <div class="card  rounded-5 p-5 my-3">
                        <div class="table-responsive">
                            <table id="example" class="table table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Emri i rolit</th>
                                        <th>Faqet e aksesuara</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Assuming $loggedInUserRole contains the role of the logged-in user
                                    $loggedInUserRole = "Administrator"; // Replace "Administrator" with the actual role of the logged-in user
                                    $sql = 'SELECT roles.id AS role_id, roles.name AS role_name, GROUP_CONCAT(role_pages.page) AS pages
            FROM roles
            LEFT JOIN role_pages ON roles.id = role_pages.role_id
            GROUP BY roles.id';
                                    if ($result = $conn->query($sql)) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo '<tr>';
                                            echo '<td>
                        <a class="btn btn-warning text-white rounded-5 edit-link me-2" href="edit_page.php?role_id=' . $row['role_id'] . '"><i class="fi fi-rr-edit"></i></a>';
                                            // Disable delete button if it's the Administrator role
                                            if ($row['role_name'] != $loggedInUserRole) {
                                                echo '<button class="btn btn-danger text-white rounded-5 delete-row" data-role-id="' . $row['role_id'] . '"><i class="fi fi-rr-trash"></i></button>';
                                            }
                                            echo '</td>';
                                            echo '<td>' . $row['role_name'] . '</td>';
                                            echo '<td>';
                                            $pages = explode(',', $row['pages']);
                                            echo '<button class="input-custom-css px-3 py-2 show-pages" data-bs-toggle="modal" data-bs-target="#pagesModal-' . $row['role_id'] . '">Shfaq</button>';
                                            echo '<div class="modal fade" id="pagesModal-' . $row['role_id'] . '" tabindex="-1" role="dialog" aria-labelledby="pagesModalLabel" aria-hidden="true">';
                                            echo '<div class="modal-dialog modal-lg" role="document">';
                                            echo '<div class="modal-content">';
                                            echo '<div class="modal-header">';
                                            echo '<h5 class="modal-title" id="pagesModalLabel">Lista e faqeve ne te cilat ka akses roli - ' . $row['role_name'] . '</h5>';
                                            echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                                            echo '</div>';
                                            echo '<div class="modal-body">';
                                            echo '<ul>';
                                            foreach ($pages as $page) {
                                                echo '<li>' . format_page_name(trim($page)) . '</li>';
                                            }
                                            echo '</ul>';
                                            echo '</div>';
                                            echo '</div>';
                                            echo '</div>';
                                            echo '</div>';
                                            echo '</td>';
                                            echo '</tr>';
                                        }
                                        // Free result set
                                        $result->free();
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- <button id="show5" class="btn btn-primary">Show only 5</button> -->
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                    <div class="card  rounded-5 p-5 my-3">
                        <div class="table-responsive">
                            <table class="table w-100 table-bordered">
                                <thead>
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
                                            // Check if pages are empty or null
                                            if (!empty($row['pages'])) {
                                                echo '<tr>';
                                                echo '<td>' . $row['user_name'] . '</td>';
                                                echo '<td>' . $row['role_name'] . '</td>';
                                                echo '<td>';
                                                echo "<button class='input-custom-css px-3 py-2 btn-sm' data-bs-toggle='modal' data-bs-target='#pagesAndStafModal-" . $row['role_id'] . "'>Shfaq te gjitha</button>";
                                                // Modal for displaying pages
                                                echo '<div class="modal fade" id="pagesAndStafModal-' . $row['role_id'] . '" tabindex="-1" aria-labelledby="pagesAndStafModalLabel" aria-hidden="true">';
                                                echo '<div class="modal-dialog modal-dialog-centered modal-xl">';
                                                echo '<div class="modal-content">';
                                                echo '<div class="modal-header">';
                                                echo '<h5 class="modal-title" id="pagesAndStafModalLabel">' . $row['role_name'] . ' Pages</h5>';
                                                echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                                                echo '</div>';
                                                echo '<div class="modal-body">';
                                                // Display pages here in grid layout
                                                echo '<div class="row">';
                                                $pages = explode(',', $row['pages']);
                                                foreach ($pages as $page) {
                                                    echo "<div class='col-3'><div class='m-1 p-2 bg-light rounded-2 border'>" . format_page_name(trim($page)) . '</div></div>';
                                                }
                                                echo '</div>'; // Close row
                                                echo '</div>'; // Close modal-body
                                                echo '</div>'; // Close modal-content
                                                echo '</div>'; // Close modal-dialog
                                                echo '</div>'; // Close modal fade
                                                echo '</td>';
                                                echo '</tr>';
                                            }
                                        }
                                        // Free result set 
                                        $result->free();
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <br>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-giveRole" role="tabpanel" aria-labelledby="pills-giveRole-tab">
                    <div class="card  rounded-5 p-5 my-3">
                        <div class="table-responsive">
                            <div class="container">
                                <form method="post" action="save_user_role.php">
                                    <!-- CSRF Token Field -->
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                                    <!-- Rest of the form -->
                                    <div class="form-group">
                                        <label for="user_id">Zgjidhni përdoruesin:</label>
                                        <select class="form-select rounded-5" id="user_id" name="user_id" required>
                                            <?php
                                            // Query to select all users
                                            $sql = "SELECT id, firstName, last_name, email FROM googleauth";
                                            $result = $conn->query($sql);
                                            // Loop through results and create option for each user
                                            while ($row = $result->fetch_assoc()) {
                                                // Sanitize user input
                                                $id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
                                                $firstName = htmlspecialchars($row['firstName'], ENT_QUOTES, 'UTF-8');
                                                $lastName = htmlspecialchars($row['last_name'], ENT_QUOTES, 'UTF-8');
                                                $email = htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8');
                                                echo "<option value='$id'>$firstName $lastName - $email</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="role_id">Zgjidhni rolin:</label>
                                        <select class="form-select rounded-5" id="role_id" name="role_id" required>
                                            <?php
                                            // Query to select all roles
                                            $sql = "SELECT id, name FROM roles";
                                            $result = $conn->query($sql);
                                            // Loop through results and create option for each role
                                            while ($row = $result->fetch_assoc()) {
                                                // Sanitize user input
                                                $id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
                                                $name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
                                                echo "<option value='$id'>$name</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary text-white rounded-5" style="text-transform:none; display: flex; align-items: center;">
                                        <i class="fi fi-rr-paper-plane me-2"></i> Ruaj rolin e përdoruesit
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <?php include 'partials/footer.php'; ?>
    <script>
        new Selectr('#user_id', {
            searchable: true
        });
        new Selectr('#role_id', {
            searchable: true
        })
    </script>
    <script>
        $(document).ready(function() {
            // Show/hide access pages
            $('.show-more').click(function() {
                var roleId = $(this).data('role-id');
                $('#more-pages-' + roleId).toggle();
            });
            // Show only 5 access pages
            $('#show5').click(function() {
                $('td:nth-child(3)').each(function() {
                    var $pages = $(this).find('.more-pages').children('p');
                    if ($pages.length > 5) {
                        $pages.slice(5).hide();
                        $(this).append('<button class="btn btn-primary show-more" data-role-id="' + $(this).prev().prev().children('.edit-row').data('role-id') + '">Show more</button>');
                    }
                });
                $(this).hide();
            });
        });
        $(document).ready(function() {
            $('.edit-row').click(function() {
                var roleId = $(this).data('role-id');
                $.ajax({
                    url: 'edit_row.php',
                    type: 'POST',
                    data: {
                        role_id: roleId
                    },
                    success: function(response) {
                        $('#edit-row-form').html(response);
                        $('#editRowModal').modal('show');
                    }
                });
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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
                            url: 'delete_row.php',
                            type: 'POST',
                            data: {
                                role_id: roleId
                            },
                            success: function() {
                                Swal.fire({
                                    title: 'I fshirë!',
                                    text: 'Rreshti është fshirë.',
                                    icon: 'success',
                                    timer: 1500
                                }).then(function() {
                                    location.reload();
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'An error occurred while deleting the row.',
                                    icon: 'error',
                                    timer: 1500
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Triggered when the role select changes
            $('#role-select').change(function() {
                // Get the selected role id
                var roleId = $(this).val();
                // Send an AJAX request to get the pages for the selected role
                $.ajax({
                    url: 'get_pages.php',
                    type: 'post',
                    data: {
                        role_id: roleId
                    },
                    dataType: 'json',
                    success: function(response) {
                        // Clear the options of the page select
                        $('#page-select').html('');
                        // Add the options for the pages
                        $.each(response, function(index, page) {
                            $('#page-select').append('<option value="' + page.id + '">' + page.name + '</option>');
                        });
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#tabelaEFaqeve').DataTable({
                "paging": false,
                "searching": false,
                "info": false,
                "order": [
                    [0, "asc"]
                ],
                stripeClasses: ['stripe-color'],
                "columnDefs": [{
                    "targets": [1],
                    "orderable": false
                }]
            });
        });
        $(document).ready(function() {
            var dataTables = $('#example').DataTable({
                responsive: false,
                searching: true,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
                    "<'row'<'col-md-12'tr>>" +
                    "<'row'<'col-md-6'><'col-md-6'p>>",
                buttons: [{
                    extend: 'pdfHtml5',
                    text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                    titleAttr: 'Eksporto tabelen ne formatin PDF',
                    className: 'btn btn-sm btn-light border rounded-5 me-2'
                }, {
                    extend: 'copyHtml5',
                    text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
                    titleAttr: 'Kopjo tabelen ne formatin Clipboard',
                    className: 'btn btn-sm btn-light border rounded-5 me-2'
                }, {
                    extend: 'excelHtml5',
                    text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
                    titleAttr: 'Eksporto tabelen ne formatin CSV',
                    className: 'btn btn-sm btn-light border rounded-5 me-2'
                }, {
                    extend: 'print',
                    text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
                    titleAttr: 'Printo tabel&euml;n',
                    className: 'btn btn-sm btn-light border rounded-5 me-2'
                }],
                initComplete: function() {
                    var btns = $(".dt-buttons");
                    btns.addClass("").removeClass("dt-buttons btn-group");
                    var lengthSelect = $("div.dataTables_length select");
                    lengthSelect.addClass("form-select");
                    lengthSelect.css({
                        width: "auto",
                        margin: "0 8px",
                        padding: "0.375rem 1.75rem 0.375rem 0.75rem",
                        lineHeight: "1.5",
                        border: "1px solid #ced4da",
                        borderRadius: "0.25rem",
                    });
                },
                fixedHeader: true,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
                },
                stripeClasses: ['stripe-color'],
            });
        });
    </script>