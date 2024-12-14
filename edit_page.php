<?php include 'partials/header.php'; ?>
<?php include 'conn-d.php'; ?>
<?php
function format_page_name($page)
{
    $pageNames = [
        'index.php' => 'Shtepia',
        'strike-platform.php' => 'Strikes Platform',
        'roles.php' => 'Rolet',
        'stafi.php' => 'Stafi',
        'ads.php' => 'Llogaritë e ADS',
        'emails.php' => 'Lista e email-eve',
        'klient.php' => 'Lista e klientëve',
        'kategorit.php' => 'Lista e kategorive',
        'claim.php' => 'Claim i Ri',
        'tiketa.php' => 'Lista e tiketave',
        'listang.php' => 'Lista e këngëve',
        'shtoy.php' => 'Regjistro këngë',
        'listat.php' => 'Lista e tiketave',
        'whitelist.php' => 'Whitelist',
        'faturat.php' => 'Pagesat YouTube',
        'invoice.php' => 'Faturat (Re)',
        'pagesat.php' => 'Pagesat e Kryera',
        'rrogat.php' => 'Pagat',
        'shpenzimep.php' => 'Shpenzimet personale',
        'yinc.php' => 'Shpenzimet',
        'filet.php' => 'Dokumente tjera',
        'github_logs.php' => 'Aktiviteti në Github',
        'klient_CSV.php' => 'Klient CSV',
        'logs.php' => 'Logs',
        'notes.php' => 'Shënime',
        'takimet.php' => 'Takimet',
        'todo_list.php' => 'To Do',
        'kontrata_2.php' => 'Kontrata e Re',
        // 'checking.php' => 'Kontrollim i Këngëve',
        'lista_kontratave.php' => 'Lista e Kontratave',
        // 'csvFiles.php' => 'Inserto CSV',
        // 'filtroCSV.php' => 'Filtro CSV',
        'listaEFaturaveTePlatformave.php' => 'Lista e Faturave',
        'pagesatEKryera.php' => 'Pagesat e Përfunduara',
        'dataYT.php' => 'Statistikat nga YouTube',
        'channel_selection.php' => 'Zgjedhja e Kanalit',
        'ofertat.php' => 'Ofertat',
        'youtube_studio.php' => 'YouTube Studio',
        'kontrata_gjenelare_2.php' => 'Kontrate e Re (Gjenerale)',
        'lista_kontratave_gjenerale.php' => 'Lista e Kontratave (Gjenerale)',
        'vegla_facebook.php' => 'Vegla Facebook',
        'lista_faturave_facebook.php' => 'Lista e Faturave (Facebook)',
        'autor.php' => 'Autor',
        'faturaFacebook.php' => 'Krijo Faturë (Facebook)',
        'ascap.php' => 'Ascap',
        'lista_kopjeve_rezerve.php' => 'Lista e Kopjeve Rezerve',
        'investime.php' => 'Investime',
        'pagesat_youtube.php' => 'Pagesat YouTube',
        'klient-avanc.php' => 'Lista e Avanceve të Klienteve',
        'list_of_invoices.php' => 'Pagesat YouTube (Re)',
        'office_investments.php' => 'Investimet e Objektit',
        'office_damages.php' => 'Prishjet',
        'office_requirements.php' => 'Kërkesat',
        'platform_invoices.php' => 'Faturat e Shpejta të Platformave',
        'currency.php' => 'Valutimi',
        'rating_list.php' => 'Lista e Vlerësimeve',
        'invoice_list_2.php' => 'Faturë e Shpejtë',
        'pagesat_punetor.php' => 'Pagesat e Punëtorit',
        'shpenzimet_objekt.php' => 'Shpenzimet e Objektit',
        'ttatimi.php' => 'Tatimi',
        'pasqyrat.php' => 'Pasqyrat',
        'aktiviteti.php' => 'Aktivitetet',
        'kontabiliteti_pagesat.php' => 'Pagesat e Kryera',
        'waiting_clients.php' => 'Lista e Klienteve në Pritje për Bashkëpunim',
    ];
    return $pageNames[$page] ?? 'N/A';
}
$pages = [
    'Menaxhimi i Përdoruesve' => [
        'stafi.php' => format_page_name('stafi.php'),
        'roles.php' => format_page_name('roles.php'),
    ],
    'Menaxhimi i Klientit' => [
        'klient.php' => format_page_name('klient.php'),
        'kategorit.php' => format_page_name('kategorit.php'),
        'klient-avanc.php' => format_page_name('klient-avanc.php'),
        'waiting_clients.php' => format_page_name('waiting_clients.php'),
    ],
    'Financa' => [
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
    'Menaxhimi i Përmbajtjes' => [
        'ads.php' => format_page_name('ads.php'),
        'emails.php' => format_page_name('emails.php'),
        'shtoy.php' => format_page_name('shtoy.php'),
        'listang.php' => format_page_name('listang.php'),
        // 'filtroCSV.php' => format_page_name('filtroCSV.php'),
        // 'csvFiles.php' => format_page_name('csvFiles.php'),
    ],
    'Mbështetje' => [
        'tiketa.php' => format_page_name('tiketa.php'),
        'listat.php' => format_page_name('listat.php'),
        'claim.php' => format_page_name('claim.php'),
        'whitelist.php' => format_page_name('whitelist.php'),
        'logs.php' => format_page_name('logs.php'),
        'github_logs.php' => format_page_name('github_logs.php'),
        'notes.php' => format_page_name('notes.php'),
        'takimet.php' => format_page_name('takimet.php'),
        'aktiviteti.php' => format_page_name('aktiviteti.php'),
        // 'checking.php' => format_page_name('checking.php'),
    ],
    'Veglat' => [
        'vegla_facebook.php' => format_page_name('vegla_facebook.php'),
        'lista_faturave_facebook.php' => format_page_name('lista_faturave_facebook.php'),
        'autor.php' => format_page_name('autor.php'),
        'faturaFacebook.php' => format_page_name('faturaFacebook.php'),
        'ascap.php' => format_page_name('ascap.php'),
        'lista_kopjeve_rezerve.php' => format_page_name('lista_kopjeve_rezerve.php'),
        'investime.php' => format_page_name('investime.php'),
    ],
    'Raportimi' => [
        'dataYT.php' => format_page_name('dataYT.php'),
        'list_of_invoices.php' => format_page_name('list_of_invoices.php'),
        'listaEFaturaveTePlatformave.php' => format_page_name('listaEFaturaveTePlatformave.php'),
    ],
    'Menaxhimi i Platformës' => [
        'platform_invoices.php' => format_page_name('platform_invoices.php'),
        'currency.php' => format_page_name('currency.php'),
        'rating_list.php' => format_page_name('rating_list.php'),
        'ofertat.php' => format_page_name('ofertat.php'),
        'kontrata_gjenelare_2.php' => format_page_name('kontrata_gjenelare_2.php'),
        'lista_kontratave_gjenerale.php' => format_page_name('lista_kontratave_gjenerale.php'),
        'kontrata_2.php' => format_page_name('kontrata_2.php'),
        'lista_kontratave.php' => format_page_name('lista_kontratave.php'),
    ],
    'Menaxhimi i Zyrës' => [
        'office_investments.php' => format_page_name('office_investments.php'),
        'office_damages.php' => format_page_name('office_damages.php'),
        'office_requirements.php' => format_page_name('office_requirements.php'),
    ],
];
$roleId = null;
$roleName = '';
$selectedPages = [];
if (isset($_GET['role_id'])) {
    $roleId = intval($_GET['role_id']);
    if ($roleId > 0) {
        $stmt = $conn->prepare("SELECT roles.name AS role_name, role_pages.page AS page_name FROM roles LEFT JOIN role_pages ON roles.id = role_pages.role_id WHERE roles.id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $roleId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $roleName = htmlspecialchars($row['role_name'], ENT_QUOTES, 'UTF-8');
                    if (!empty($row['page_name'])) $selectedPages[] = $row['page_name'];
                }
            } else {
                echo "<div class='alert alert-danger' role='alert'>Roli nuk u gjet.</div>";
                exit;
            }
            $stmt->close();
        } else {
            echo "<div class='alert alert-danger' role='alert'>Dështoi përgatitja e kërkimit në bazën e të dhënave.</div>";
            exit;
        }
    } else {
        echo "<div class='alert alert-danger' role='alert'>ID e rolit e pavlefshme.</div>";
        exit;
    }
} else {
    echo "<div class='alert alert-danger' role='alert'>ID e rolit nuk u siguroi.</div>";
    exit;
}
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card p-4 shadow-sm rounded-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">Menaxho Aksesin e Roleve</h3>
                    <a href="roles.php" class="btn btn-secondary">Kthehu te Rolet</a>
                </div>
                <div class="mb-3">
                    <h5>Detajet e Rolit</h5>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>ID e Rolit:</strong> <?php echo $roleId; ?></li>
                        <li class="list-group-item"><strong>Emri i Rolit:</strong> <?php echo $roleName; ?></li>
                    </ul>
                </div>
                <form method="POST" action="api/post_methods/post_update_page.php" id="pageForm">
                    <div class="d-flex justify-content-end mb-3">
                        <button type="submit" class="btn btn-primary me-2" id="submitButton" disabled>Përditëso</button>
                        <button type="reset" class="btn btn-outline-secondary" id="resetButton">Anulo</button>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                                <label class="form-check-label fw-bold" for="selectAll">Zgjidh të Gjitha</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <input type="text" id="searchInput" class="form-control" placeholder="Kërko faqe...">
                        </div>
                    </div>
                    <div class="accordion" id="permissionsAccordion">
                        <?php foreach ($pages as $category => $pagesInCategory): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-<?php echo md5($category); ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo md5($category); ?>" aria-expanded="false" aria-controls="collapse-<?php echo md5($category); ?>">
                                        <?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>
                                    </button>
                                </h2>
                                <div id="collapse-<?php echo md5($category); ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?php echo md5($category); ?>" data-bs-parent="#permissionsAccordion">
                                    <div class="accordion-body">
                                        <div class="form-check mb-2">
                                            <input type="checkbox" class="form-check-input category-checkbox" id="category-<?php echo md5($category); ?>">
                                            <label class="form-check-label fw-bold" for="category-<?php echo md5($category); ?>">Zgjidh të Gjitha në <?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></label>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Faqja</th>
                                                        <th class="text-center">Akses</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($pagesInCategory as $page => $formattedPage):
                                                        $isChecked = in_array($page, $selectedPages);
                                                    ?>
                                                        <tr class="page-row">
                                                            <td><?php echo htmlspecialchars($formattedPage, ENT_QUOTES, 'UTF-8'); ?></td>
                                                            <td class="text-center">
                                                                <input type="checkbox" class="form-check-input page-checkbox" name="page[]" value="<?php echo htmlspecialchars($page, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $isChecked ? 'checked' : ''; ?>>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" name="role_id" value="<?php echo $roleId; ?>">
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'partials/footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function checkCheckedStatus() {
        const checkboxes = document.querySelectorAll('.page-checkbox');
        const submitButton = document.getElementById('submitButton');
        submitButton.disabled = !Array.from(checkboxes).some(cb => cb.checked);
    }

    function handleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        document.querySelectorAll('.page-checkbox, .category-checkbox').forEach(cb => cb.checked = selectAll.checked);
        checkCheckedStatus();
    }

    function handleCategorySelect(categoryCheckbox) {
        const categoryCard = categoryCheckbox.closest('.accordion-item');
        categoryCard.querySelectorAll('.page-checkbox').forEach(cb => cb.checked = categoryCheckbox.checked);
        updateSelectAllCheckbox();
        checkCheckedStatus();
    }

    function updateCategoryCheckbox(categoryCard) {
        const categoryCheckbox = categoryCard.querySelector('.category-checkbox');
        const pageCheckboxes = categoryCard.querySelectorAll('.page-checkbox');
        categoryCheckbox.checked = Array.from(pageCheckboxes).every(cb => cb.checked);
    }

    function updateSelectAllCheckbox() {
        const allPageCheckboxes = document.querySelectorAll('.page-checkbox');
        const selectAll = document.getElementById('selectAll');
        selectAll.checked = Array.from(allPageCheckboxes).every(cb => cb.checked);
    }

    function initializeCheckboxes() {
        updateSelectAllCheckbox();
        document.querySelectorAll('.accordion-item').forEach(updateCategoryCheckbox);
        checkCheckedStatus();
    }
    document.getElementById('selectAll').addEventListener('change', handleSelectAll);
    document.querySelectorAll('.category-checkbox').forEach(cb => cb.addEventListener('change', () => handleCategorySelect(cb)));
    document.querySelectorAll('.page-checkbox').forEach(cb => cb.addEventListener('change', () => {
        const categoryCard = cb.closest('.accordion-item');
        updateCategoryCheckbox(categoryCard);
        updateSelectAllCheckbox();
        checkCheckedStatus();
    }));
    document.getElementById('searchInput').addEventListener('input', function() {
        const term = this.value.toLowerCase();
        document.querySelectorAll('.page-row').forEach(row => {
            const pageName = row.querySelector('td').textContent.toLowerCase();
            row.style.display = pageName.includes(term) ? '' : 'none';
        });
    });
    document.getElementById('resetButton').addEventListener('click', initializeCheckboxes);
    document.addEventListener('DOMContentLoaded', initializeCheckboxes);
    checkCheckedStatus();
</script>