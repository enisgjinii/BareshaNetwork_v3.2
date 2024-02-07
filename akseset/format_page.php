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
        return 'Llogarit&euml; e ADS';
    }
    if ($page == 'emails.php') {
        return 'Lista e email-eve';
    }
    if ($page == 'klient.php') {
        return 'Lista e klient&euml;ve';
    }
    if ($page == 'klient2.php') {
        return 'Lista e klient&euml;ve tjer&euml;';
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
        return 'Lista e k&euml;ng&euml;ve';
    }
    if ($page == 'shtoy.php') {
        return 'Regjistro k&euml;ng&euml;';
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
    if ($page == 'faturat2.php') {
        return 'Platformat Tjera';
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
    if ($page == 'github_logs.php') {
        return 'Aktiviteti ne Github';
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
    if ($page == 'check_musics.php') {
        return 'Konfirmimi i kengeve';
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
    if ($page == 'youtube_studio.php') {
        return 'Baresha analytics';
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
        return 'Krijo fatur&euml; (Facebook)';
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
}
$pages = array(
    'stafi.php',
    'roles.php',
    'klient.php',
    'klient2.php',
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
    'faturat2.php',
    'filet.php',
    'notes.php',
    'github_logs.php',
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
    'check_musics.php',
    'dataYT.php',
    'ofertat.php',
    'youtube_studio.php',
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
    'office_requirements.php'
);
