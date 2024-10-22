<?php include 'partials/header.php'; ?>
<?php
include 'conn-d.php';

/**
 * Formats page name based on the provided page filename.
 *
 * @param string $page The filename of the page.
 *
 * @return string The formatted page name.
 */
function format_page_name($page)
{
    $pageNames = [
        'index.php' => 'Shtepia',
        'strike-platform.php' => 'Strikes',
        'roles.php' => 'Rolet',
        'stafi.php' => 'Stafi',
        'ads.php' => 'Llogarit&euml; e ADS',
        'emails.php' => 'Lista e email-eve',
        'klient.php' => 'Lista e klient&euml;ve',
        'kategorit.php' => 'Lista e kategorive',
        'claim.php' => 'Recent Claim',
        'tiketa.php' => 'Lista e tiketave',
        'listang.php' => 'Lista e k&euml;ng&euml;ve',
        'shtoy.php' => 'Regjistro k&euml;ng&euml;',
        'listat.php' => 'Lista e tiketave',
        'whitelist.php' => 'Whitelist',
        'faturat.php' => 'Pagesat Youtube',
        'invoice.php' => 'Faturat ( New )',
        'pagesat.php' => 'Pagesat e kryera',
        'rrogat.php' => 'Pagat',
        'shpenzimep.php' => 'Shpenzimet personale',
        'yinc.php' => 'Shpenzimet',
        'filet.php' => 'Dokumente tjera',
        'github_logs.php' => 'Aktiviteti ne Github',
        'klient_CSV.php' => 'Klient CSV',
        'logs.php' => 'Logs',
        'notes.php' => 'Shenime',
        'takimet.php' => 'Takimet',
        'todo_list.php' => 'To Do',
        'kontrata_2.php' => 'Kontrata e re',
        'lista_kontratave.php' => 'Lista e kontratave',
        'csvFiles.php' => 'Inserto CSV',
        'filtroCSV.php' => 'Filtro CSV',
        'listaEFaturaveTePlatformave.php' => 'Lista e faturave',
        'pagesatEKryera.php' => 'Pagesat e perfunduara',
        'dataYT.php' => 'Statistikat nga Youtube',
        'channel_selection.php' => 'Channel Selection',
        'ofertat.php' => 'Ofertat',
        'youtube_studio.php' => 'YouTube Studio',
        'kontrata_gjenelare_2.php' => 'Kontrate e re ( Gjenerale )',
        'lista_kontratave_gjenerale.php' => 'Lista e kontratave ( Gjenerale )',
        'vegla_facebook.php' => 'Vegla Facebook',
        'lista_faturave_facebook.php' => 'Lista e faturave (Facebook)',
        'autor.php' => 'Autor',
        'faturaFacebook.php' => 'Krijo fatur&euml; ( Facebook )',
        'ascap.php' => 'Ascap',
        'lista_kopjeve_rezerve.php' => 'Lista e kopjeve rezerve',
        'investime.php' => 'Investime',
        'pagesat_youtube.php' => 'Pagesat YouTube',
        'klient-avanc.php' => 'Lista e avanceve te klienteve',
        'list_of_invoices.php' => 'Pagesat YouTube ( New )',
        'office_investments.php' => 'Investimet e objektit',
        'office_damages.php' => 'Prishjet',
        'office_requirements.php' => 'Kerkesat',
        'platform_invoices.php' => 'Fature e shpejte e platformave',
        'currency.php' => 'Valutimi',
        'rating_list.php' => 'Lista e vlersimeve',
        'invoice_list_2.php' => 'Faturë e shpejtë',
        'strike-platform.php' => 'Strikes Platform',
        'pagesat_punetor.php' => 'Pagesat e punetorit',
        'shpenzimet_objekt.php' => 'Shpenzimet e objektit',
        'ttatimi.php' => 'Tatimi',
        'pasqyrat.php' => 'Pasqyrat',
        'aktiviteti.php' => 'Aktivitetet',
        'kontabiliteti_pagesat.php' => 'Pagesat e kryera',
        // Add more pages as needed
    ];
    return isset($pageNames[$page]) ? $pageNames[$page] : 'N/A';
}

/**
 * Define pages grouped by categories for better organization.
 */
$pages = [
    'User Management' => [
        'stafi.php' => format_page_name('stafi.php'),
        'roles.php' => format_page_name('roles.php'),
    ],
    'Client Management' => [
        'klient.php' => format_page_name('klient.php'),
        'kategorit.php' => format_page_name('kategorit.php'),
        'klient-avanc.php' => format_page_name('klient-avanc.php'),
    ],
    'Finance' => [
        'rrogat.php' => format_page_name('rrogat.php'),
        'shpenzimep.php' => format_page_name('shpenzimep.php'),
        'faturat.php' => format_page_name('faturat.php'),
        'invoice.php' => format_page_name('invoice.php'),
        'pagesat.php' => format_page_name('pagesat.php'),
        'invoice_list_2.php' => format_page_name('invoice_list_2.php'),
        'pagesatEKryera.php' => format_page_name('pagesatEKryera.php'),
        'kontabiliteti_pagesat.php' => format_page_name('kontabiliteti_pagesat.php'),
        'pagesat_punetor.php' => format_page_name('pagesat_punetor.php'),
        'shpenzimet_objekt.php' => format_page_name('shpenzimet_objekt.php'),
        'ttatimi.php' => format_page_name('ttatimi.php'),
        'pasqyrat.php' => format_page_name('pasqyrat.php'),
    ],
    'Content Management' => [
        'ads.php' => format_page_name('ads.php'),
        'emails.php' => format_page_name('emails.php'),
        'shtoy.php' => format_page_name('shtoy.php'),
        'listang.php' => format_page_name('listang.php'),
        'filtroCSV.php' => format_page_name('filtroCSV.php'),
        'csvFiles.php' => format_page_name('csvFiles.php'),
    ],
    'Support' => [
        'tiketa.php' => format_page_name('tiketa.php'),
        'listat.php' => format_page_name('listat.php'),
        'claim.php' => format_page_name('claim.php'),
        'whitelist.php' => format_page_name('whitelist.php'),
        'logs.php' => format_page_name('logs.php'),
        'github_logs.php' => format_page_name('github_logs.php'),
        'notes.php' => format_page_name('notes.php'),
        'takimet.php' => format_page_name('takimet.php'),
        'aktiviteti.php' => format_page_name('aktiviteti.php'),
    ],
    'Tools' => [
        'vegla_facebook.php' => format_page_name('vegla_facebook.php'),
        'lista_faturave_facebook.php' => format_page_name('lista_faturave_facebook.php'),
        'autor.php' => format_page_name('autor.php'),
        'faturaFacebook.php' => format_page_name('faturaFacebook.php'),
        'ascap.php' => format_page_name('ascap.php'),
        'lista_kopjeve_rezerve.php' => format_page_name('lista_kopjeve_rezerve.php'),
        'investime.php' => format_page_name('investime.php'),
    ],
    'Reporting' => [
        'dataYT.php' => format_page_name('dataYT.php'),
        'list_of_invoices.php' => format_page_name('list_of_invoices.php'),
        'listaEFaturaveTePlatformave.php' => format_page_name('listaEFaturaveTePlatformave.php'),
    ],
    'Platform Management' => [
        'platform_invoices.php' => format_page_name('platform_invoices.php'),
        'currency.php' => format_page_name('currency.php'),
        'rating_list.php' => format_page_name('rating_list.php'),
        'ofertat.php' => format_page_name('ofertat.php'),
        'kontrata_gjenelare_2.php' => format_page_name('kontrata_gjenelare_2.php'),
        'lista_kontratave_gjenerale.php' => format_page_name('lista_kontratave_gjenerale.php'),
    ],
    'Office Management' => [
        'office_investments.php' => format_page_name('office_investments.php'),
        'office_damages.php' => format_page_name('office_damages.php'),
        'office_requirements.php' => format_page_name('office_requirements.php'),
    ],
    // Add more categories and pages as needed
];

// Initialize variables
$roleId = null;
$roleName = '';
$selectedPages = [];

// Check if the role ID is present in the URL
if (isset($_GET['role_id'])) {
    // Retrieve and sanitize the role ID from the URL parameter
    $roleId = intval($_GET['role_id']);

    if ($roleId > 0) {
        // Prepare the SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT roles.name AS role_name, role_pages.page AS page_name
                                FROM roles
                                LEFT JOIN role_pages ON roles.id = role_pages.role_id
                                WHERE roles.id = ?");
        $stmt->bind_param("i", $roleId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Role found, process the data
            while ($row = $result->fetch_assoc()) {
                $roleName = htmlspecialchars($row['role_name'], ENT_QUOTES, 'UTF-8');
                if (!empty($row['page_name'])) {
                    $selectedPages[] = $row['page_name'];
                }
            }
        } else {
            // Role not found
            echo "<div class='alert alert-danger' role='alert'>Role not found.</div>";
            exit;
        }

        $stmt->close();
    } else {
        // Invalid role ID
        echo "<div class='alert alert-danger' role='alert'>Invalid role ID.</div>";
        exit;
    }
} else {
    // Role ID not provided in the URL
    echo "<div class='alert alert-danger' role='alert'>Role ID not provided.</div>";
    exit;
}
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="p-5 rounded-5 shadow-sm mb-4 card">
                <h3 class="mb-4">Manage Role Permissions</h3>
                <div class="mb-3">
                    <p><strong>ID e rolit:</strong> <?php echo $roleId; ?></p>
                    <p><strong>Emri i rolit:</strong> <?php echo $roleName; ?></p>
                </div>
                <form method="POST" action="api/post_methods/post_update_page.php" id="pageForm">
                    <!-- Select All Checkbox -->
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="selectAll">
                        <label class="form-check-label" for="selectAll"><strong>Select All</strong></label>
                    </div>
                    <!-- Search Input -->
                    <div class="mb-3">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search pages...">
                    </div>
                    <!-- Pages Grouped by Categories -->
                    <?php foreach ($pages as $category => $pagesInCategory): ?>
                        <div class="card mb-3">
                            <div class="card-header">
                                <input type="checkbox" class="form-check-input category-checkbox" id="category-<?php echo md5($category); ?>">
                                <label class="form-check-label" for="category-<?php echo md5($category); ?>"><strong><?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></strong></label>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Faqja</th>
                                            <th class="text-center">Aksesi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pagesInCategory as $page => $formattedPage): ?>
                                            <?php
                                            $isChecked = in_array($page, $selectedPages);
                                            $formattedPageEscaped = htmlspecialchars($formattedPage, ENT_QUOTES, 'UTF-8');
                                            ?>
                                            <tr class="page-row">
                                                <td><?php echo $formattedPageEscaped; ?></td>
                                                <td class="text-center">
                                                    <input type="checkbox" class="form-check-input page-checkbox" name="page[]" value="<?php echo $page; ?>" <?php echo $isChecked ? 'checked' : ''; ?>>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <!-- Hidden Role ID and Submit Button -->
                    <div class="d-flex justify-content-end">
                        <input type="hidden" name="role_id" value="<?php echo $roleId; ?>">
                        <button type="submit" class="btn btn-primary shadow-sm rounded-5 text-white" id="submitButton" disabled>P&euml;rditso</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Enhanced Functionality -->
<script>
    // Function to check the checked status of checkboxes and toggle submit button
    function checkCheckedStatus() {
        const checkboxes = document.querySelectorAll('.page-checkbox');
        const submitButton = document.getElementById('submitButton');
        let isChecked = false;
        checkboxes.forEach(function(checkbox) {
            if (checkbox.checked) {
                isChecked = true;
            }
        });
        // Enable or disable the submit button based on the checked status
        submitButton.disabled = !isChecked;
    }

    // Function to handle "Select All" checkbox
    function handleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const allPageCheckboxes = document.querySelectorAll('.page-checkbox');
        const allCategoryCheckboxes = document.querySelectorAll('.category-checkbox');
        allPageCheckboxes.forEach(function(checkbox) {
            checkbox.checked = selectAll.checked;
        });
        allCategoryCheckboxes.forEach(function(checkbox) {
            checkbox.checked = selectAll.checked;
        });
        checkCheckedStatus();
    }

    // Function to handle category "Select All" checkbox
    function handleCategorySelect(categoryCheckbox) {
        const categoryCard = categoryCheckbox.closest('.card');
        const pageCheckboxes = categoryCard.querySelectorAll('.page-checkbox');
        pageCheckboxes.forEach(function(checkbox) {
            checkbox.checked = categoryCheckbox.checked;
        });
        // Update the global "Select All" checkbox
        updateSelectAllCheckbox();
        checkCheckedStatus();
    }

    // Function to update category checkbox based on individual page checkboxes
    function updateCategoryCheckbox(categoryCard) {
        const categoryCheckbox = categoryCard.querySelector('.category-checkbox');
        const pageCheckboxes = categoryCard.querySelectorAll('.page-checkbox');
        const allChecked = Array.from(pageCheckboxes).every(cb => cb.checked);
        categoryCheckbox.checked = allChecked;
    }

    // Function to update the global "Select All" checkbox based on all page checkboxes
    function updateSelectAllCheckbox() {
        const allPageCheckboxes = document.querySelectorAll('.page-checkbox');
        const selectAll = document.getElementById('selectAll');
        const allChecked = Array.from(allPageCheckboxes).every(cb => cb.checked);
        selectAll.checked = allChecked;
    }

    // Function to initialize the state of "Select All" and category checkboxes
    function initializeCheckboxes() {
        // Initialize "Select All" checkbox
        updateSelectAllCheckbox();

        // Initialize category checkboxes
        const categoryCards = document.querySelectorAll('.card');
        categoryCards.forEach(function(card) {
            updateCategoryCheckbox(card);
        });

        // Initialize submit button state
        checkCheckedStatus();
    }

    // Event Listener for "Select All" checkbox
    document.getElementById('selectAll').addEventListener('change', handleSelectAll);

    // Event Listeners for category checkboxes
    const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
    categoryCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            handleCategorySelect(checkbox);
        });
    });

    // Event Listeners for individual page checkboxes
    const pageCheckboxes = document.querySelectorAll('.page-checkbox');
    pageCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            // Update category checkbox based on page checkboxes
            const categoryCard = checkbox.closest('.card');
            updateCategoryCheckbox(categoryCard);

            // Update global "Select All" checkbox
            updateSelectAllCheckbox();

            // Update submit button state
            checkCheckedStatus();
        });
    });

    // Event Listener for Search Input
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const pageRows = document.querySelectorAll('.page-row');
        pageRows.forEach(function(row) {
            const pageName = row.querySelector('td:first-child').textContent.toLowerCase();
            if (pageName.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Initialize checkboxes on page load
    document.addEventListener('DOMContentLoaded', initializeCheckboxes);

    // Initial check for submit button state
    checkCheckedStatus();
</script>

<?php include 'partials/footer.php'; ?>