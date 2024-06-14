<?php include 'partials/header.php' ?>
<?php
include 'conn-d.php';
function format_page_name($page)
{
    switch ($page) {
        case 'index.php':
            return 'Shtepia';
        case 'strike-platform.php':
            return 'Strikes';
        case 'roles.php':
            return 'Rolet';
        case 'stafi.php':
            return 'Stafi';
        case 'ads.php':
            return 'Llogarit&euml; e ADS';
        case 'emails.php':
            return 'Lista e email-eve';
        case 'klient.php':
            return 'Lista e klient&euml;ve';
        case 'kategorit.php':
            return 'Lista e kategorive';
        case 'claim.php':
            return 'Recent Claim';
        case 'tiketa.php':
            return 'Lista e tiketave';
        case 'listang.php':
            return 'Lista e k&euml;ng&euml;ve';
        case 'shtoy.php':
            return 'Regjistro k&euml;ng&euml;';
        case 'listat.php':
            return 'Lista e tiketave';
        case 'tiketa.php':
            return 'Tiket e re';
        case 'whitelist.php':
            return 'Whitelist';
        case 'faturat.php':
            return 'Pagesat Youtube';
        case 'invoice.php':
            return 'Faturat ( New )';
        case 'pagesat.php':
            return 'Pagesat e kryera';
        case 'rrogat.php':
            return 'Pagat';
        case 'shpenzimep.php':
            return 'Shpenzimet personale';
        case 'tatimi.php':
            return 'Tatimi';
        case 'yinc.php':
            return 'Shpenzimet';
        case 'filet.php':
            return 'Dokumente tjera';
        case 'github_logs.php':
            return 'Aktiviteti ne Github';
        case 'klient_CSV.php':
            return 'Klient CSV';
        case 'logs.php':
            return 'Logs';
        case 'notes.php':
            return 'Shenime';
        case 'takimet.php':
            return 'Takimet';
        case 'todo_list.php':
            return 'To Do';
        case 'kontrata_2.php':
            return 'Kontrata e re';
        case 'lista_kontratave.php':
            return 'Lista e kontratave';
        case 'csvFiles.php':
            return 'Inserto CSV';
        case 'filtroCSV.php':
            return 'Filtro CSV';
        case 'listaEFaturaveTePlatformave.php':
            return 'Lista e faturave';
        case 'pagesatEKryera.php':
            return 'Pagesat e perfunduara';
        case 'dataYT.php':
            return 'Statistikat nga Youtube';
        case 'ofertat.php':
            return 'Ofertat';
        case 'kontrata_gjenelare_2.php':
            return 'Kontrate e re ( Gjenerale )';
        case 'lista_kontratave_gjenerale.php':
            return 'Lista e kontratave ( Gjenerale )';
        case 'vegla_facebook.php':
            return 'Vegla Facebook';
        case 'lista_faturave_facebook.php':
            return 'Lista e faturave (Facebook)';
        case 'autor.php':
            return 'Autor';
        case 'lista_kopjeve_rezerve.php':
            return 'Lista e kopjeve rezerve';
        case 'investime.php':
            return 'Investime';
        case 'faturaFacebook.php':
            return 'Krijo fatur&euml; ( Facebook )';
        case 'ascap.php':
            return 'Ascap';
        case 'klient-avanc.php':
            return 'Lista e avanceve te klienteve';
        case 'list_of_invoices.php':
            return 'Pagesat YouTube ( New )';
        case 'office_investments.php':
            return 'Investimet e objektit';
        case 'office_damages.php':
            return 'Prishjet';
        case 'office_requirements.php':
            return 'Kerkesat';
        case 'platform_invoices.php':
            return 'Fature e shpejte e platformave';
        case 'currency.php':
            return 'Valutimi';
        case 'rating_list.php':
            return 'Lista e vlersimeve';
        case 'invoice_list_2.php':
            return 'Faturë e shpejtë';
        case 'authenticated_channels.php':
            return 'Kanalet e autentifikuara';
        case 'pagesat_punetor.php':
            return 'Pagesat e punetorit';
        case 'shpenzimet_objekt.php':
            return 'Shpenzimet e objektit';
        case 'ttatimi.php':
            return 'Tatimi';
        case 'pasqyrat.php':
            return 'Pasqyrat';
        default:
            return 'Faqja nuk ekziston';
    }
}

$pages = array(
    'stafi.php' => format_page_name('stafi.php'),
    'roles.php' => format_page_name('roles.php'),
    'klient.php' => format_page_name('klient.php'),
    'kategorit.php' => format_page_name('kategorit.php'),
    'ads.php' => format_page_name('ads.php'),
    'emails.php' => format_page_name('emails.php'),
    'shtoy.php' => format_page_name('shtoy.php'),
    'listang.php' => format_page_name('listang.php'),
    'tiketa.php' => format_page_name('tiketa.php'),
    'listat.php' => format_page_name('listat.php'),
    'claim.php' => format_page_name('claim.php'),
    'whitelist.php' => format_page_name('whitelist.php'),
    'rrogat.php' => format_page_name('rrogat.php'),
    'tatimi.php' => format_page_name('tatimi.php'),
    'yinc.php' => format_page_name('yinc.php'),
    'shpenzimep.php' => format_page_name('shpenzimep.php'),
    'faturat.php' => format_page_name('faturat.php'),
    'invoice.php' => format_page_name('invoice.php'),
    'pagesat.php' => format_page_name('pagesat.php'),
    'filet.php' => format_page_name('filet.php'),
    'notes.php' => format_page_name('notes.php'),
    'github_logs.php' => format_page_name('github_logs.php'),
    'todo_list.php' => format_page_name('todo_list.php'),
    'takimet.php' => format_page_name('takimet.php'),
    'klient_CSV.php' => format_page_name('klient_CSV.php'),
    'logs.php' => format_page_name('logs.php'),
    'kontrata_2.php' => format_page_name('kontrata_2.php'),
    'lista_kontratave.php' => format_page_name('lista_kontratave.php'),
    'csvFiles.php' => format_page_name('csvFiles.php'),
    'filtroCSV.php' => format_page_name('filtroCSV.php'),
    'listaEFaturaveTePlatformave.php' => format_page_name('listaEFaturaveTePlatformave.php'),
    'pagesatEKryera.php' => format_page_name('pagesatEKryera.php'),
    'dataYT.php' => format_page_name('dataYT.php'),
    'channel_selection.php' => format_page_name('channel_selection.php'),
    'ofertat.php' => format_page_name('ofertat.php'),
    'youtube_studio.php' => format_page_name('youtube_studio.php'),
    'kontrata_gjenelare_2.php' => format_page_name('kontrata_gjenelare_2.php'),
    'lista_kontratave_gjenerale.php' => format_page_name('lista_kontratave_gjenerale.php'),
    'vegla_facebook.php' => format_page_name('vegla_facebook.php'),
    'lista_faturave_facebook.php' => format_page_name('lista_faturave_facebook.php'),
    'autor.php' => format_page_name('autor.php'),
    'faturaFacebook.php' => format_page_name('faturaFacebook.php'),
    'ascap.php' => format_page_name('ascap.php'),
    'lista_kopjeve_rezerve.php' => format_page_name('lista_kopjeve_rezerve.php'),
    'investime.php' => format_page_name('investime.php'),
    'pagesat_youtube.php' => format_page_name('pagesat_youtube.php'),
    'klient-avanc.php' => format_page_name('klient-avanc.php'),
    'list_of_invoices.php' => format_page_name('list_of_invoices.php'),
    'office_investments.php' => format_page_name('office_investments.php'),
    'office_damages.php' => format_page_name('office_damages.php'),
    'office_requirements.php' => format_page_name('office_requirements.php'),
    'platform_invoices.php' => format_page_name('platform_invoices.php'),
    'currency.php' => format_page_name('currency.php'),
    'rating_list.php' => format_page_name('rating_list.php'),
    'invoice_list_2.php' => format_page_name('invoice_list_2.php'),
    'authenticated_channels.php' => format_page_name('authenticated_channels.php'),
    'strike-platform.php' => format_page_name('strike-platform.php'),
    'pagesat_punetor.php' => format_page_name('pagesat_punetor.php'),
    'shpenzimet_objekt.php' => format_page_name('shpenzimet_objekt.php'),
    'ttatimi.php' => format_page_name('ttatimi.php'),
    'pasqyrat.php' => format_page_name('pasqyrat.php'),
);
// Check if the role ID is present in the URL
if (isset($_GET['role_id'])) {
    // Retrieve the role ID from the URL parameter
    $roleId = $_GET['role_id'];
    // You can use the $roleId to fetch the role data from the database or perform any other necessary actions
    // Example: Fetch role details and associated pages from the database based on the role ID
    // Assuming you have a database connection established
    // Replace this code with your own logic to retrieve role details and associated pages
    $sql = "SELECT roles.name AS role_name, role_pages.page AS page_name
            FROM roles
            LEFT JOIN role_pages ON roles.id = role_pages.role_id
            WHERE roles.id = $roleId";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // Role found, process the data
        $row = $result->fetch_assoc();
        $roleName = $row['role_name'];
        // Get the selected pages as an associative array
        $selectedPages = [];
        do {
            $selectedPages[] = $row['page_name'];
        } while ($row = $result->fetch_assoc());
        // You can display other form elements or perform any other necessary actions based on the role ID
    } else {
        // Role not found
        echo "Role not found.";
    }
} else {
    // Role ID not provided in the URL
    echo "Invalid role ID.";
}
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="p-5 rounded-5 shadow-sm mb-4 card">
                <p>ID e rolit :
                    <?php echo $roleId ?>
                </p>
                <p>Emri i rolit :
                    <?php echo $roleName ?>
                </p>
                <br>
                <form method="POST" action="update_page.php" id="pageForm">
                    <table class="table table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>Faqja</th>
                                <th>Aksesi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($pages as $page => $formattedPage) {
                                $isChecked = in_array($page, $selectedPages);
                                echo '<tr>';
                                echo "<td>$formattedPage</td>";
                                echo "<td><input type='checkbox' name='page[]' value='$page' " . ($isChecked ? "checked" : "") . "></td>";
                                echo '</tr>';
                            } ?>
                        </tbody>
                        <tfoot>
                            <?php
                            echo '<tr><td colspan="2"><input type="hidden" name="role_id" value="' . $roleId . '">Emri i rolit : ' . $roleName . ' </td></tr>';
                            ?>
                        </tfoot>
                    </table>
                    <br>
                    <div id="submitContainer" style="display: none;">
                        <input class="btn btn-primary shadow-sm rounded-5 text-white shadow-2" style="text-transform: none;" type="submit" value="P&euml;rditso">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    // Function to check the checked status of checkboxes
    function checkCheckedStatus() {
        var checkboxes = document.querySelectorAll('input[type="checkbox"]');
        var submitContainer = document.getElementById('submitContainer');
        var isChecked = false;
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                isChecked = true;
                break;
            }
        }
        // Show/hide the submit button based on the checked status
        if (isChecked) {
            submitContainer.style.display = 'block';
        } else {
            submitContainer.style.display = 'none';
        }
    }
    // Add event listener to checkboxes
    var checkboxes = document.querySelectorAll('input[type="checkbox"]');
    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].addEventListener('change', checkCheckedStatus);
    }
    // Call the function initially to check the checked status
    checkCheckedStatus();
</script>
<?php include 'partials/footer.php' ?>