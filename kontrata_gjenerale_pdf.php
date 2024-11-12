<?php
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database Connection using Prepared Statements
include 'conn-d.php';

// Function to sanitize output
function sanitize_output($data)
{
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Fetch Contract Data Securely
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Invalid contract ID.");
}

$stmt = $conn->prepare("SELECT * FROM kontrata_gjenerale WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No contract found with the provided ID.");
}

$row = $result->fetch_assoc();

// Parse the 'artisti' field
$artisti = isset($row['artisti']) ? explode("|", $row['artisti']) : ['Unknown Artist'];
$artistName = sanitize_output($artisti[0]);
$artistEmail = isset($artisti[1]) ? sanitize_output($artisti[1]) : '';

// Contract Details
$contractDetails = [
    'id_kontrates' => sanitize_output($row['id_kontrates']),
    'data_e_krijimit' => sanitize_output(date('d/m/Y', strtotime($row['data_e_krijimit']))),
    'shteti' => sanitize_output($row['shteti']),
    'numri_personal' => sanitize_output($row['numri_personal']),
    'youtube_id' => sanitize_output($row['youtube_id']),
    'kohezgjatja' => sanitize_output($row['kohezgjatja']),
    'tvsh' => sanitize_output($row['tvsh']),
    'pronari_xhirollogarise' => sanitize_output($row['pronari_xhirollogarise']),
    'numri_xhirollogarise' => sanitize_output($row['numri_xhirollogarise']),
    'kodi_swift' => sanitize_output($row['kodi_swift']),
    'iban' => sanitize_output($row['iban']),
    'emri_bankes' => sanitize_output($row['emri_bankes']),
    'adresa_bankes' => sanitize_output($row['adresa_bankes']),
    'emri' => sanitize_output($row['emri']),
    'nenshkrimi' => sanitize_output($row['nenshkrimi']),
    'shenim' => sanitize_output($row['shenim'])
];

// Contract Sections Organized in Arrays
$contractSections = [
    [
        'title_eng' => 'CONTRACT ON COOPERATION',
        'title_sq' => 'KONTRATË BASHKPUNIMI',
        'content_eng' => [
            "This document specifies the terms and conditions of the agreement between <b>Baresha Music SH.P.K</b>, located at Rr. Brigada 123 nr. 23 in Suharekë, represented by <b>AFRIM KOLGECI, CEO-FOUNDER of Baresha Music</b>, and <b>ARTIST: {$artistName}</b>, a citizen of <b>{$contractDetails['shteti']}</b>, with personal identification number <b>{$contractDetails['numri_personal']}</b>. {$artistName} will be representing themselves on the other side of this agreement through their YouTube channel identified by the",
            "YouTube ID - <b>{$contractDetails['youtube_id']}</b> and the Channel name - <b>{$artistName}</b>.",
            "The terms and conditions outlined in this contract pertain to the contractual relationship as a whole between the two parties."
        ],
        'content_sq' => [
            "Ky dokument specifikon kushtet dhe kushtëzimet e marrëveshjes midis <b>Baresha Music SH.P.K</b>, me adresë Rr. Brigada 123 nr. 23 në Suharekë, e përfaqësuar nga <b>AFRIM KOLGECI, CEO-FOUNDER i Baresha Music</b>, dhe <b>ARTISTI: {$artistName}</b>, qytetar i <b>{$contractDetails['shteti']}</b>, me numër personal identifikimi <b>{$contractDetails['numri_personal']}</b>. {$artistName} do të përfaqësohet nga ana e tyre në këtë marrëveshje përmes kanalit të tyre në YouTube të identifikuar me",
            "YouTube ID - <b>{$contractDetails['youtube_id']}</b> dhe emrin e kanalit - <b>{$artistName}</b>.",
            "Kushtet dhe kushtëzimet e përcaktuara në këtë kontratë lidhen me marrëdhënien kontraktuale në tërësi midis dy palëve."
        ]
    ]
];

// Contract Articles Organized in Arrays
$contractArticles = [
    [
        'article_number' => '1',
        'title_eng' => 'DEFINITIONS',
        'title_sq' => 'DEFINICIONET',
        'content_eng' => [
            "<b>1.1. Artist - Copyright Owner</b> – refers to a natural or legal person that represents himself or a group or a band, that authorizes Baresha Music SH.P.K.",
            "<b>1.1. Artisti - Pronari i të Drejtave</b> - përfaqson një person fizik ose juridik që përfaqëson veten, një grup ose një bandë, që autorizon Baresha Music SH.P.K.",
            "<b>1.2. Baresha Music SH.P.K</b> - Copyright User - refers to the company that holds exclusive rights to distribute, sell, and publish audio and video masters on YouTube platforms and digital stores under the terms of this contract.",
            "<b>1.2. Baresha Music SH.P.K</b> - Përdoruesi i të Drejtave - përfaqson kompaninë që mbart të drejta ekskluzive për shpërndarjen, shitjen dhe publikimin e masterit audio dhe video në platformat e YouTube dhe dyqanet dixhitale nën këtë kontratë siq cekët në nenin 1.3.",
            "<b>1.3. Digital Stores – Shitore Dixhitale</b>",
            "<ul style=\"margin-left:50px;\">",
            "<li>Spotify</li>",
            "<li>Apple Music</li>",
            "<li>YouTube Music</li>",
            "<li>Deezer</li>",
            "<li>Amazon Music</li>",
            "<li>Etc – Etj</li>",
            "</ul>"
        ],
        'content_sq' => [
            "<b>1.1. Artisti - Pronari i të Drejtave</b> – përfaqson një person fizik ose juridik që përfaqëson veten, një grup ose një bandë, që autorizon Baresha Music SH.P.K.",
            "<b>1.1. Artisti - Pronari i të Drejtave</b> - përfaqson një person fizik ose juridik që përfaqëson veten, një grup ose një bandë, që autorizon Baresha Music SH.P.K.",
            "<b>1.2. Baresha Music SH.P.K</b> - Përdoruesi i të Drejtave - përfaqson kompaninë që mbart të drejta ekskluzive për shpërndarjen, shitjen dhe publikimin e masterit audio dhe video në platformat e YouTube dhe dyqanet dixhitale nën këtë kontratë siq cekët në nenin 1.3.",
            "<b>1.2. Baresha Music SH.P.K</b> - Përdoruesi i të Drejtave - përfaqson kompaninë që mbart të drejta ekskluzive për shpërndarjen, shitjen dhe publikimin e masterit audio dhe video në platformat e YouTube dhe dyqanet dixhitale nën këtë kontratë siq cekët në nenin 1.3.",
            "<b>1.3. Shitore Dixhitale – Digital Stores</b>",
            "<ul style=\"margin-left:50px;\">",
            "<li>Spotify</li>",
            "<li>Apple Music</li>",
            "<li>YouTube Music</li>",
            "<li>Deezer</li>",
            "<li>Amazon Music</li>",
            "<li>Etc – Etj</li>",
            "</ul>"
        ]
    ],
    [
        'article_number' => '2',
        'title_eng' => 'OBJECT OF THE CONTRACT',
        'title_sq' => 'OBJEKTI I KONTRATES',
        'content_eng' => [
            "<b>2.1.</b> The copyright owner hereby GRANTS, namely awards EXCLUSIVE RIGHTS to Baresha Music SH.P.K (The Copyright User), for the distribution, sale and publication of audio materials and video masters on the artist's channel on YouTube and digital stores, as well as for the use of the artist's name, logo, photographs and biography on the artist's channel on YouTube and in all digital stores."
        ],
        'content_sq' => [
            "<b>2.1.</b> Pronari i të drejtave këtu AUTORIZON, që do të thotë i jep të drejtat ekskluzive për Baresha Music SH.P.K (Përdoruesi i të Drejtave), për shpërndarjen, shitjen dhe publikimin e materialeve dhe masterit audio dhe video në kanalin e Artistit në YouTube dhe dyqanet dixhitale, si dhe për përdorimin e emrit, logos, fotografive dhe biografisë së Artistit në kanalin e Artistit në YouTube dhe në të gjitha dyqanet dixhitale."
        ]
    ],
    [
        'article_number' => '3',
        'title_eng' => 'THE RIGHT OF USE',
        'title_sq' => 'TE DREJTAT E PERDORIMIT',
        'content_eng' => [
            "<b>3.1.</b> <b>The Artist (Copyright Owner)</b> will grant authorization for their <b>previous and future works</b> potentially uploaded on their YouTube channel to <b>Baresha Music SH.P.K (Copyright User)</b>, under a special contract. This contract will provide the Copyright User with the right to use the works without any fixed duration. In the event of the termination of the Cooperation Contract, <b>Baresha Music SH.P.K (Copyright User)</b> shall return all rights to the Artist (Copyright Owner) within 30 days of the termination of the Cooperation Contract.",
            "<b>3.2.</b> Through the execution of this contract, the Artist hereby grants authorization to Baresha Music SH.P.K to utilize all of their channel videos for promotional purposes pertaining to other clients of Baresha Music SH.P.K through the use of \"End Screens\" and \"Cards\".",
            "<b>3.3.</b> By signing this contract, the Artist acknowledges that they have read and understood these rules and accepts responsibility for complying with this article and the contract as a whole. The Artist may only use purchased instrumentals that come with a license. For each publication, the Artist is required to provide the license if the beat is sourced from YouTube or the Internet. The Artist may also use melody lines, instrumentals, videos and lyrics that have been authorized by their respective authors, producers and songwriters. The use of any material that is not licensed or authorized is strictly prohibited on the Artist’s channel:
                <ul style=\"margin-left:50px;\">",
            "<li>It is not allowed and it is strictly forbidden to publish songs that contain YouTube “Free” beats or “Free” beats anywhere on the internet.</li>",
            "<li>It is strictly forbidden and not permitted to use the Instrumental: Beats, Melodies, or Lyrics which are from the Internet or YouTube, but that the Artist does not have the authorship/authorization of the authors, producers or songwriters.</li>",
            "<li>“Covers” or “Remix” are strictly prohibited to be published if the Artist doesn’t have the direct authorization from the (Artist, Artists, Authors, Producers) of the song recording and composition.</li>",
            "<li>Music projects that the Artist owns and/or are located on his channel, in which they include either “Free” beat or beat without a license, then the Artist must delete projects or create a New Channel if he want to collaborate with Baresha Music SH.P.K</li>",
            "</ul>",
            "<b>3.4.</b> In the event that the artist fails to comply with these rules, Baresha Music SH.P.K hereby reserves the right to terminate the cooperation agreement, as well as any other agreements in place with the artist, with immediate effect. It is understood that the artist, as the rightful owner of the rights, shall be solely responsible and liable to you for any and all financial and other damages resulting from non-compliance with these rules.
                In the event of non-compliance with these Rules, the Artist shall be solely responsible for any potential harm, including financial and consequential damages, arising from such non-compliance, as well as any other consequences resulting from the breach of the aforementioned provisions.",
            "<b>3.5.</b> Baresha Music SH.P.K further reserves the right to terminate the contract at any time if the ARTIST's actions on their YouTube channel endanger Baresha Music's operations, such as receiving unresolved Copyright Strikes or engaging in any activity that violates YouTube's rules, terms, and conditions. In the event of such termination, Baresha Music SH.P.K is obligated to liquidate any outstanding payments and release all clients' audio-visual materials from the use of Baresha Music SH.P.K within a period of four months."
        ],
        'content_sq' => [
            "<b>3.1. Artisti (Pronari i të Drejtave)</b> do të autorizojë <b>veprat e tij të mëparshme dhe të ardhshme</b>, të ngarkuara potencialisht në kanalin e tij në YouTube, në dispozicion të <b>Baresha Music SH.P.K (Përdoruesi i të Drejtave)</b>, nën një kontratë të veqant. Kontrata e të Drejtave do t'i japë Përdoruesit të Copyright-it të drejtën për të përdorur veprat pa një kohëzgjatje të caktuar. Në rast të ndërprerjes së Kontratës së Bashkëpunimit,<b>Baresha Music SH.P.K (Përdoruesi i Copyright-it)</b> do t'i kthejë të gjitha të drejtat Artistit (Pronarit të Drejtave) brenda 30 ditëve nga ndërprerja e Kontratës së Bashkëpunimit.",
            "<b>3.2.</b> Nëpërmjet nënshkrimit të kësaj kontrate, Artisti autorizon Baresha Music SH.P.K për të përdorur të gjitha videot në kanalin e tij për promovimin e klientëve të tjerë në Baresha Music SH.P.K duke përdorur \"End Screens\" dhe \"Cards\".",
            "<b>3.3.</b> Me nënshkrimin e kësaj kontrate, Artisti pranon se i ka lexuar dhe kuptuar këto rregulla dhe pranon përgjegjësinë për respektimin e këtij neni dhe kontratës në tërësi. Artisti mund të përdorë vetëm instrumente të blera që vijnë me licencë. Për çdo publikim, Artistit i kërkohet të japë licencën nëse beat e ka burimin nga YouTube ose interneti. Artisti mund të përdorë gjithashtu vija melodike, instrumente, video dhe tekste që janë autorizuar nga autorët, producentët dhe kompozitorët e tyre përkatës. Përdorimi i çdo materiali që nuk është i licencuar ose i autorizuar është rreptësisht i ndaluar në kanalin e Artistit:
                <ul style=\"margin-left:50px; list-style-type: none;\">",
            "<li>Nuk lejohet dhe ndalohet rreptësisht publikimi i këngëve që përmbajnë beat “Free” ose “Free” të YouTube apo kudo në internet.</li>",
            "<li>Ndalohet rreptësisht dhe nuk lejohet përdorimi i instrumenteve: Beats, Melodi, apo Tekste që janë nga Interneti apo YouTube, por që Artisti nuk ka autorësinë/autorizimin e artistit, producentëve apo, tekst shkruesëve.</li>",
            "<li>Projektet muzikore që Artisti zotëron dhe/ose ndodhen në kanalin e tij, në të cilat përfshijnë beat \"Falas\" ose beat pa licencë, atëherë Artisti duhet të fshijë projektet ose të krijojë një Kanal të Ri nëse dëshiron të bashkëpunojë me Baresha Music SH.P.K.</li>",
            "</ul>",
            "<b>3.4.</b> Në rast se artisti nuk i bindet këtyre rregullave, Baresha Music SH.P.K këtu rezervon të drejtën për të ndërprerë marrëveshjen e bashkëpunimit, si dhe çdo marrëveshje tjetër në vend me artistin, me efekt të menjëhershëm. Është kuptuar se artisti, si pronar i të drejtave të autorit, do të jetë i vetëm përgjegjës dhe për çdo dëm financiar dhe dëme tjera që shkaktohen nga mosbindja ndaj këtyre rregullave.
                Në rast se nuk respektohen këto Rregulla, Artisti do të jetë i vetëm përgjegjës për çdo dëm potencial, duke përfshirë dëmet financiare dhe dëmet e tjera, si dhe për çdo pasojë tjetër që mund të rezultojë nga mosrespektimi i dispozitave të përmendura më lart dhe më poshtë.",
            "<b>3.5.</b> Baresha Music SH.P.K gjithashtu rezervon të drejtën për të ndërprerë kontratën në çdo kohë nëse veprimet e ARTIST's në kanalin e tyre në YouTube vështirësojnë veprimtarinë e Baresha Music (për shembull duke marrë shkelje të drejtave të autorit pa u zgjidhur, apo duke bërë çdo veprim që shkel rregullat, kushtet dhe kushtetatat e YouTube). Në rast se ky rast ndodh, Baresha Music SH.P.K është i detyruar që në një periudhë prej katër muajsh të likuidojë çdo pagesë që nuk është realizuar dhe të lirojë të gjithë materialet audiovizive të klientëve nga përdorimi i Baresha Music SH.P.K.",
        ],
        'content_sq' => [
            "<b>3.1.</b> Artisti (Pronari i të Drejtave) do të autorizojë <b>veprat e tij të mëparshme dhe të ardhshme</b>, të ngarkuara potencialisht në kanalin e tij në YouTube, në dispozicion të <b>Baresha Music SH.P.K (Përdoruesi i të Drejtave)</b>, nën një kontratë të veqant. Kontrata e të Drejtave do t'i japë Përdoruesit të Copyright-it të drejtën për të përdorur veprat pa një kohëzgjatje të caktuar. Në rast të ndërprerjes së Kontratës së Bashkëpunimit, <b>Baresha Music SH.P.K (Përdoruesi i Copyright-it)</b> do t'i kthejë të gjitha të drejtat Artistit (Pronarit të Drejtave) brenda 30 ditëve nga ndërprerja e Kontratës së Bashkëpunimit.",
            "<b>3.2.</b> Nëpërmjet nënshkrimit të kësaj kontrate, Artisti autorizon Baresha Music SH.P.K për të përdorur të gjitha videot në kanalin e tij për promovimin e klientëve të tjerë në Baresha Music SH.P.K duke përdorur \"End Screens\" dhe \"Cards\".",
            "<b>3.3.</b> Me nënshkrimin e kësaj kontrate, Artisti pranon se i ka lexuar dhe kuptuar këto rregulla dhe pranon përgjegjësinë për respektimin e këtij neni dhe kontratës në tërësi. Artisti mund të përdorë vetëm instrumente të blera që vijnë me licencë. Për çdo publikim, Artistit i kërkohet të japë licencën nëse beat e ka burimin nga YouTube ose interneti. Artisti mund të përdorë gjithashtu vija melodike, instrumente, video dhe tekste që janë autorizuar nga autorët, producentët dhe kompozitorët e tyre përkatës. Përdorimi i çdo materiali që nuk është i licencuar ose i autorizuar është rreptësisht i ndaluar në kanalin e Artistit:
                <ul style=\"margin-left:50px; list-style-type: none;\">",
            "<li>Nuk lejohet dhe ndalohet rreptësisht publikimi i këngëve që përmbajnë beat “Free” ose “Free” të YouTube apo kudo në internet.</li>",
            "<li>Ndalohet rreptësisht dhe nuk lejohet përdorimi i instrumenteve: Beats, Melodi, apo Tekste që janë nga Interneti apo YouTube, por që Artisti nuk ka autorësinë/autorizimin e artistit, producentëve apo, tekst shkruesëve.</li>",
            "<li>Projektet muzikore që Artisti zotëron dhe/ose ndodhen në kanalin e tij, në të cilat përfshijnë beat \"Falas\" ose beat pa licencë, atëherë Artisti duhet të fshijë projektet ose të krijojë një Kanal të Ri nëse dëshiron të bashkëpunojë me Baresha Music SH.P.K.</li>",
            "</ul>",
            "<b>3.4.</b> Në rast se artisti nuk i bindet këtyre rregullave, Baresha Music SH.P.K këtu rezervon të drejtën për të ndërprerë marrëveshjen e bashkëpunimit, si dhe çdo marrëveshje tjetër në vend me artistin, me efekt të menjëhershëm. Është kuptuar se artisti, si pronar i të drejtave të autorit, do të jetë i vetëm përgjegjës dhe për çdo dëm financiar dhe dëme tjera që shkaktohen nga mosbindja ndaj këtyre rregullave.
                Në rast se nuk respektohen këto Rregulla, Artisti do të jetë i vetëm përgjegjës për çdo dëm potencial, duke përfshirë dëmet financiare dhe dëmet e tjera, si dhe për çdo pasojë tjetër që mund të rezultojë nga mosrespektimi i dispozitave të përmendura më lart dhe më poshtë.",
            "<b>3.5.</b> Baresha Music SH.P.K gjithashtu rezervon të drejtën për të ndërprerë kontratën në çdo kohë nëse veprimet e ARTIST's në kanalin e tyre në YouTube vështirësojnë veprimtarinë e Baresha Music (për shembull duke marrë shkelje të drejtave të autorit pa u zgjidhur, apo duke bërë çdo veprim që shkel rregullat, kushtet dhe kushtetatat e YouTube). Në rast se ky rast ndodh, Baresha Music SH.P.K është i detyruar që në një periudhë prej katër muajsh të likuidojë çdo pagesë që nuk është realizuar dhe të lirojë të gjithë materialet audiovizive të klientëve nga përdorimi i Baresha Music SH.P.K."
        ]
    ],
    [
        'article_number' => '4',
        'title_eng' => 'RIGHTS AND OBLIGATIONS OF THE COPYRIGHT USER – BARESHA MUSIC SH.P.K',
        'title_sq' => 'TE DREJTAT DHE OBLIGIMET E PËRDORUESIT E TË DREJTAVE AUTORIALE – BARESHA MUSIC SH.P.K',
        'content_eng' => [
            "<b>4.1.</b> Baresha Music SH.P.K shall hold the exclusive right to distribute the audio and video content of the Artist on the Artist's YouTube channel as well as on all digital platforms.",
            "<b>4.2.</b> Baresha Music SH.P.K is authorized to distribute the materials provided by the artist on YouTube and other digital platforms, in accordance with the terms and conditions outlined in this contractual agreement.",
            "<b>4.3.</b> With the objective of advancing the promotion of the Artist's materials, Baresha Music SH.P.K. shall employ various strategies including \"Cross-Promotion\", \"Tag Promotion\", \"Thumbnail Optimization\", and any other available means at the company's disposal to achieve this objective.",
            "<b>4.4.</b> Baresha Music SH.P.K will provide a maximum of one (1) video per \"Instagram Story\" and one (1) video for the \"Instagram Feed\" for each artist or performer featured in a song. These provisions are subject to the regulations outlined in Article 3.3.",
            "<b>4.5.1.</b> For YouTube - To promote their song, Baresha Music SH.P.K may display advertisements in the form of banners or advertising clips on or before Copyright Owner's works (or albums) on YouTube.",
            "<b>4.5.2.</b> Baresha Music SH.P.K will conduct a thorough review process to detect and block any videos of works uploaded by unauthorized third parties. Alternatively, the company may choose to allow these works and monetize them accordingly.",
            "<b>4.5.3.</b> Baresha Music SH.P.K is committed to promptly removing any videos of works that are uploaded by unauthorized third parties.",
            "<b>4.5.4.</b> Baresha Music SH.P.K is committed to safeguarding the reputation of its artists. To this end, the company will remove any links from Google search results that redirect visitors to sites containing false information or that could potentially harm the image of the artist in question.",
            "<b>4.5.5.</b> Baresha Music SH.P.K is committed to maintaining the integrity of its artists' image and reputation. To this end, the company will remove any illegal materials published in connection with the artist on YouTube and other \"User Generated Content\" platforms such as SoundCloud, DailyMotion, etc.",
            "<b>4.6.</b> Baresha Music SH.P.K is hereby granted authorization by the Artist, who is the Copyright Owner, to exclusively exercise the right to profit from the following:",
            "<b>4.6.1.</b> During the term of this contractual agreement, the company shall be authorized to engage in the distribution, publication, and sale of the audio and video content of the Artist on the Artist's YouTube channel, as well as on all digital stores.",
            "<b>4.6.2.</b> The placement of advertisements, including banners or clips, prior to or during the display of the Artist's works or albums, as well as all other materials submitted by the Artist that generate profits, as specified in this agreement, shall be authorized.",
            "<b>4.7.</b> Baresha Music SH.P.K is obligated to provide timely notification to the Artist every four months, as outlined in the terms of this contractual agreement, for all payments exceeding 100 Euro made on Digital Stores. If this minimum threshold has not been met, the payment shall be deferred to the next payment period until the threshold has been achieved. This same protocol applies for YouTube payments, with payments are made on a monthly basis.",
            "<b>4.8.</b> As per the terms of this agreement, Baresha Music SH.P.K reserves the right to take leave and observe official holidays of the Republic of Kosovo. During such periods, the company shall not accept any new publications or releases. Hence, any publications or releases must be submitted to us at least one day prior to the intended release date.",
            "<b>4.9.</b> In the event that the artist decides to sell their channel to another artist or company, Baresha Music SH.P.K reserves the right to retain 100% of the earnings for that particular month. Furthermore, we request that a contract outlining the terms and conditions of the sale be submitted to us for our review and approval. This is to ensure that the new owner of the channel is aware of their obligations to Baresha Music SH.P.K and that any future earnings are directed to the appropriate party.",
            "<b>4.10.</b> In the event that the artist does not release any new content within a year, Baresha Music SH.P.K reserves the right to terminate this contract as well as any other existing contracts with the artist. In this case, the channel will also lose monetization."
        ],
        'content_sq' => [
            "<b>4.1.</b> Baresha Music SH.P.K do të mbajë të drejtën ekskluzive për të shpërndarë përmbajtjen audio dhe video të Artistit në kanalin e tij të YouTube-s si dhe në të gjitha platformat dixhitale.",
            "<b>4.2.</b> Baresha Music SH.P.K do të shpërndajë materialet e dërguara nga artisti në YouTube dhe dyqanet dixhitale, siç është specifikuar në këtë kontratë.",
            "<b>4.3.</b> Me qëllim të promovimit të materialeve të artistit, Baresha Music SH.P.K do të përdorë strategjitë e ndryshme si \"Cross-Promotion\", \"Tag Promotion\", \"Thumbnail Optimization\" dhe çdo formë tjetër në dispozicion të kompanisë Baresha Music SH.P.K. për të arritur këtë qëllim.",
            "<b>4.4.</b> Baresha Music SH.P.K do t'i dërgojë Artistit 1 (një) video për \"Instagram Story\" maksimumi dhe 1 (një) video për \"Instagram Feed\" për secilin Artist në një këngë, me rregullat e lart cekur të nenit 3.3.",
            "<b>4.5.1.</b> Për YouTube - Që të promovojnë këngën e tyre, Baresha Music SH.P.K mund të shfaq reklama në formën e banerave ose klipave reklamuese në ose para materialit (ose albumeve) të Pronarit të të Drejtave të Autorit në YouTube.",
            "<b>4.5.2.</b> Baresha Music SH.P.K do të kryejë një proces të hollësishëm vlerësuese për të zbuluar dhe bllokuar çdo video të materialit të ngarkuara nga palë të treta pa autorizim. Në rast se këto punë lejohen, kompania mund të vendosë t'i monetizojë ato.",
            "<b>4.5.3.</b> Baresha Music SH.P.K është e vendosur që të heqë menjëherë çdo video të materialit që ngarkohen nga palë të treta pa autorizim.",
            "<b>4.5.4.</b> Baresha Music SH.P.K është e vendosur që të mbrojë reputacionin e artistëve të saj. Me këtë qëllim, kompania do të heqë çdo lloj linku nga rezultatet e kërkimit në Google që ridrejtojnë vizitorët në faqe të internetit që përmbajnë informacione të rreme ose që mund të dëmtojnë imazhin e artistit në fjalë.",
            "<b>4.5.5.</b> Baresha Music SH.P.K është e vendosur të mbajë integritetin e imazhit dhe reputacionit të artistëve të saj. Me këtë qëllim, kompania do të heqë çdo material të paligjshëm që publikohet në lidhje me artistin në YouTube dhe platforma të tjera të \"Përmbajtjes së Krijuar nga Përdoruesit\" si SoundCloud, DailyMotion etj.",
            "<b>4.6.</b> Baresha Music SH.P.K merr autorizimin e nevojshëm nga Artisti, i cili është pronari i të drejtave të autorit, për të ushtruar ekskluzivisht të drejtën për të përfituar nga të ardhurat e mëposhtme:",
            "<b>4.6.1.</b> Gjatë kohëzgjatjes së kësaj marrëveshje kontraktuale, kompania do të jetë e autorizuar për të angazhuar veten në shpërndarjen, publikimin dhe shitjen e përmbajtjes audio dhe video të Artistit në kanalin YouTube të Artistit, si dhe në të gjitha dyqanet dixhitale.",
            "<b>4.6.2.</b> Vendosja e reklamave, duke përfshirë flamuj ose klipet, para ose gjatë shfaqjes së punëve ose albumeve të Artistit, si dhe të gjitha materialet e tjera të paraqitura nga Artisti që gjenerojnë fitime, ashtu siç është specifikuar në këtë marrëveshje, do të autorizohet.",
            "<b>4.7.</b> Baresha Music SH.P.K është e detyruar të njoftojë Artistin në kohë çdo katër muaj, siç është përcaktuar në kushtet e kësaj marrëveshje kontraktuale, për të gjitha pagesat që tejkalojnë 100 Euro dhe janë bërë në Dyqanet Dixhitale. Nëse kjo shumë minimale nuk është arritur, pagesa do të shtyhet për në periudhën e pagesave të ardhshme, derisa kufiri minimal të arrihet. Ky protokoll i njëjtë aplikohet edhe për pagesat e YouTube, ku pagesat bëhen çdo muaj.",
            "<b>4.8.</b> Sipas kushteve të kësaj marrëveshje, Baresha Music SH.P.K rezervon të drejtën për të marrë pushime dhe për të festuar ditët zyrtare të Republikës së Kosovës dhe gjatë këtyre ditëve ne nuk pranojmë asnjë publikim të ri - prandaj, çdo publikim duhet të dërgohet një ditë më parë.",
            "<b>4.9.</b> Në rast se artisti vendos të shesë kanalin e tij një artist tjetër ose një kompanie tjetër, Baresha Music SH.P.K rezervon të drejtën për të mbajtur 100% të ardhurave për atë muaj të caktuar. Në të njëjtën kohë, kërkojmë që një kontratë që përmban kushtet dhe parimet e shitjes të paraqitet për shqyrtim dhe aprovim tek ne. Kjo është për të siguruar që pronari i ri i kanalit është i vetëdijshëm për obligimet e tij ndaj Baresha Music SH.P.K dhe që të ardhurat e ardhshme drejtohen tek pala e duhur.",
            "<b>4.10.</b> Në rast se artisti nuk publikon asnjë lloj përmbajtjeje të re brenda një viti, Baresha Music SH.P.K rezervon të drejtën për të shfuqizuar këtë kontratë dhe çdo kontratë tjetër me artistin. Në këtë rast, kanali do të humbasë monetizimin."
        ]
    ],
    [
        'article_number' => '5',
        'title_eng' => 'RIGHTS AND OBLIGATIONS OF THE ARTIST',
        'title_sq' => 'RIGHTS AND OBLIGATIONS OF THE ARTIST',
        'content_eng' => [
            "<b>5.1.</b> It is stipulated that during the term of this contract, the artist is prohibited from entering into contractual agreements with any other distribution companies that operate through YouTube and digital shops. This restriction only applies to the artist's own YouTube channel, which is identified by its YouTube ID as specified in both this contract and another agreement. In the event that the artist knowingly enters into a new agreement with Baresha Music SH.P.K,and he has a running contract with other distribution companies, any resulting consequences will be the sole responsibility of the artist.",
            "<b>5.2.</b> During the validity of this contract, the artist is obliged to grant Baresha Music SH.P.K access to his/her YouTube channel. In the event that the artist decides to assume control over his/her YouTube channel and/or opts to revoke Baresha Music SH.P.K's access, any potential damage incurred to the YouTube channel or video content shall be borne by the artist. It is important to note that the channel is under joint management with Baresha Music, therefore the artist is expected to comply with the rules outlined in Article 3 of this agreement. Failure to comply may result in the artist being held accountable for any and all resulting consequences.",
            "<b>5.3.</b> As per the terms of this agreement, the artist is entitled to receive proceeds from the distribution and sale of audio and/or video masters by Baresha Music SH.P.K on YouTube and all other digital platforms.",
            "<b>5.4.</b> The artist shall be responsible for the publication, distribution, and sale of their audio and video master recordings, exclusively through Baresha Music Sh.P.K, both on their YouTube channel and in various digital stores for the contractual period.",
            "<b>5.5.</b> The owner of the rights, herein referred to as \"the artist,\" hereby grants full authorization to Baresha Music to post any audio, video, or photographic content on Instagram, Facebook, and any other relevant internet platform. Baresha Music is also authorized to generate profits from the artist's content, subject to the profit-sharing agreement outlined in Article Sections 6.1 of this contract. Should the artist terminate this contract, they may request the removal of any previously uploaded audio, video, or photographic content from Baresha Music's platform. It is hereby declared that the artist shall not authorize any other company or individual to submit a copyright strike or any similar actions against Baresha Music personally. Any such actions that harm or damage Baresha Music will result in the artist being solely responsible for all damages, including any additional losses incurred.",
            "<b>5.6.</b> The artist hereby declares and warrants that for the duration of this contract, they shall refrain from opening any new YouTube channels or digital stores (including but not limited to Spotify, Apple Music, etc.). The artist further declares that they shall not do so either as an individual or through another company or individual.
                Furthermore, the artist declares and warrants that they shall not publish any song(s) on another YouTube channel or any other channel on a digital store, whether through another company or individual or themselves, for the duration of this contract. The artist affirms that they shall not enter into any agreement with another company or individual, nor shall they do so themselves, for the purpose of publishing on YouTube or digital stores while this contract is in effect.",
            "<b>5.7.</b> It is mandatory for the artist to provide Baresha Music with their material/video/audio at least 24 hours prior to the intended release date. This will enable Baresha Music to complete the necessary marketing processes to ensure a successful content/release launch.",
            "<b>5.8.</b> It is mandatory for the artist to ensure that their channel does not contain any content that is not their own, and to promptly remove such content to comply with YouTube's rules on \"Reused Content\"."
        ],
        'content_sq' => [
            "<b>5.1.</b> Është parashikuar që gjatë kohës së kësaj kontrate, artisti është i ndaluar nga hyrja në marrëveshje kontraktuale me çdo kompani tjetër të shpërndarjes që operon përmes YouTube dhe dyqaneve dixhitale. Kufizimi i kësaj parashtruesje zbatohet vetëm në kanalin e YouTube të artistit, i cili identifikohet me identifikuesin e tij të YouTube siç është specifikuar në këtë kontratë dhe në një marrëveshje tjetër. Në rast se artisti me dije nënshkruan një marrëveshje të re me Baresha Music SH.P.K dhe ka një kontratë aktive me kompani të tjera të shpërndarjes, atëherë çdo pasojë që rezulton është e përgjegjësi e artistit.",
            "<b>5.2.</b> Gjatë vlefshmërisë së kësaj kontrate, artisti është i detyruar të japë akses Baresha Music SH.P.K në kanalin e tij/saj të YouTube. Në rast se artisti vendos të marrë kontrollin e kanalit të tij/saj të YouTube dhe/ose zgjedh të tërheqë aksesin e Baresha Music SH.P.K, çdo dëm potencial që mund të ndodhë në kanalin e YouTube ose përmbajtjen e videove do të mbulohet nga artisti. Është e rëndësishme të theksohet se kanali është në menaxhim të përbashkët me Baresha Music, prandaj pritet që artisti të përmbahet nga rregullat e përmendura në Nenin 3 të kësaj marrëveshje. Në rast se artisti nuk i ndjek këto rregulla, ai/ajo do të jetë i/e përgjegjshëm për çdo pasojë që ndodhin si pasojë e kësaj.",
            "<b>5.3.</b> Sipas kushteve të kësaj marrëveshje, artisti ka të drejtë të marrë të ardhurat nga shpërndarja dhe shitja e master audio dhe / ose video nga Baresha Music SH.P.K në YouTube dhe në të gjitha dyqanet dixhitale.",
            "<b>5.4.</b> Artisti do të publikojë, distribuojë dhe shesë master audio dhe video në kanalin e tij të YouTube dhe në dyqanet dixhitale vetëm përmes Baresha Music Sh.P.K për kohën e kontratës.",
            "<b>5.5.</b> Pronari i të drejtave, në vijim i quajtur \"artisti\", i jep plotësisht autorizimin Baresha Music për të postuar cilindo lloj përmbajtjeje audio, video ose fotografike në Instagram, Facebook dhe çdo platformë tjetër në internet. Baresha Music gjithashtu është autorizuar për të generuar fitime nga përmbajtja e artistit, në përputhje me marrëveshjen e ndarjes së fitimit të përcaktuar në Seksionet 6.1 të kësaj kontrate. Nëse artisti ndërpren këtë kontratë, atëherë ata mund të kërkojnë largimin e çdo përmbajtjeje audio, video ose fotografike që ka qenë e ngarkuar më parë nga Baresha Music. Deklarohet se artisti nuk do të autorizojë ndonjë kompani ose individ për të dërguar një \"Copyright Strike\" ose ndonjë veprim të ngjashëm kundër Baresha Music personalisht. Çdo veprim i tillë që dëmton Baresha Music do të rezultojë në faktin që artisti do të jetë i vetmi përgjegjës për të gjitha dëmet, duke përfshirë humbjet shtesë.",
            "<b>5.6.</b> Artisti deklaron dhe garanton se gjatë kohës që kjo kontratë është në fuqi, ai nuk do të hapë një kanal të ri në YouTube ose dyqane dixhitale (si Spotify, Apple Music, etj.). Artisti deklaron se nuk do ta bëjë këtë as si person dhe as përmes një kompanie tjetër ose një individi tjetër.
                Në mënyrë të ngjashme, artisti deklaron dhe garanton se nuk do të publikojë një ose më shumë këngë nga një kanal tjetër i YouTube ose ndonjë kanal tjetër në dyqanin dixhital, nëpërmjet një kompanie tjetër ose individi tjetër ose vetë as gjatë kohës që kjo kontratë është në fuqi. Artisti deklaron se nuk do të hyjë në një marrëveshje me një kompani tjetër ose individ për publikime në YouTube dhe në dyqanin dixhital, ndërsa kjo kontratë është në fuqi.",
            "<b>5.7.</b> Eshtë detyrë e artistit të dërgojë materialet e tij / video / audio tek Baresha Music, 24 orë para datës së planifikuar të lansimit. Kjo do ti japë kohë Baresha Music për të finalizuar procesin e marketingut të përmbajtjes / për të siguruar një lansim të suksesshëm.",
            "<b>5.8.</b> Artisti është i detyruar të kontrollojë nëse kanali i tij përmban materiale që nuk janë të tij dhe t'i largojë ato nga kanali në mënyrë që të përputhen me rregullat e YouTube për \"Reused Content\"."
        ]
    ],
    [
        'article_number' => '6',
        'title_eng' => 'EARNINGS, EXPENDITURES, AND COMMISSIONS',
        'title_sq' => 'TË HYRAT SHPENZIMET DHE PROVIZIONET',
        'content_eng' => [
            "Revenues generated by the distribution, publication and sale of the Artist’s audio and video master on his YouTube channel and digital stores shall be transferred to the bank account designated by Baresha Music SH.PK",
            "<b>6.1.</b> Baresha Music SH.P.K shall pay the artist with a sum of {$contractDetails['tvsh']}.00 % of gross income for YouTube and 50% for Digital Store sales.",
            "Payments by Baresha Music SH.P.K for the Artist shall be done through the designated bank accounts as follows:",
            "Account Holder – Pronari I Xhiro-Llgarisë: <b>{$contractDetails['pronari_xhirollogarise']}</b>",
            "Account Number - Numri Xhiro-Llgarisë: <b>{$contractDetails['numri_xhirollogarise']}</b>",
            "Swift Code – Kodi Swift: <b>{$contractDetails['kodi_swift']}</b>",
            "IBAN: <b>{$contractDetails['iban']}</b>",
            "Bank Name – Emri Bankës: <b>{$contractDetails['emri_bankes']}</b>",
            "Bank Address – Adresa Bankës: <b>{$contractDetails['adresa_bankes']}</b>",
            "<b>6.2. Expenses / Shpenzimet</b>",
            "<b>6.2.1.</b> The costs associated with the publication, distribution, marketing, optimization of the Artist’s content, and upkeep of their account on YouTube and digital stores, totaling 100 EURO/annum, shall be the responsibility of the Artist. Administrative costs for the first year must be paid in advance.",
            "<b>6.2.2.</b> The artist shall bear the responsibility of covering the banking commissions associated with the transfer of funds."
        ],
        'content_sq' => [
            "Te ardhurat e gjeneruara nga shpërndarja, publikimi dhe shitja e masterit audio dhe video të artistit në kanalin e tij të YouTube dhe dyqanet dixhitale do të transferohen në llogarinë bankare të caktuar nga Baresha Music SH.P.K.",
            "<b>6.1.</b> Baresha Music SH.P.K do të paguajë artistin me një shumë prej {$contractDetails['tvsh']}.00 % të të ardhurave bruto për shitjet në YouTube dhe 50% për shitjet në shitoret dixhitale.",
            "Pagesat për Artistin nga Baresha Music SH.P.K do të bëhen nëpërmjet llogaris bankare të cekur si më poshtë:",
            "Account Holder – Pronari I Xhiro-Llgarisë: <b>{$contractDetails['pronari_xhirollogarise']}</b>",
            "Account Number - Numri Xhiro-Llgarisë: <b>{$contractDetails['numri_xhirollogarise']}</b>",
            "Swift Code – Kodi Swift: <b>{$contractDetails['kodi_swift']}</b>",
            "IBAN: <b>{$contractDetails['iban']}</b>",
            "Bank Name – Emri Bankës: <b>{$contractDetails['emri_bankes']}</b>",
            "Bank Address – Adresa Bankës: <b>{$contractDetails['adresa_bankes']}</b>",
            "<b>6.2. Shpenzimet / Expenses</b>",
            "<b>6.2.1.</b> Artisti duhet të bëjë pages prejë 100 EURO/vit për botim, shpërndarje, zhvillim të marketingut, rritjen e suksesit të këngës, shitjen dhe mirëmbajtjen e llogarisës së artistit në YouTube dhe dyqane dixhitale. Pagesa për koston administrative për vitin e parë duhet të bëhet paraprakisht.",
            "<b>6.2.2.</b> Komisionet bankare për transferimin e fondeve do të paguhen nga artisti."
        ]
    ],
    [
        'article_number' => '7',
        'title_eng' => 'PARTIES’ COVENANTS AND OWNERSHIP',
        'title_sq' => 'GARANICTË E PALËVE DHE PRONËSIA',
        'content_eng' => [
            "<b>7.1.</b> Each party to the contract is responsible and liable for paying taxes on the income earned under this contract. In accordance with Law No. 06/L-105 on Corporate Income Tax, Article 31.2 stipulates that \"each taxpayer who pays interest or royalties to residents or non-residents shall withhold tax at a rate of ten percent (10%) at the time of payment or credit.\""
        ],
        'content_sq' => [
            "<b>7.1.</b> Secila palë kontraktuese është përgjegjëse dhe e detyruar të paguajë detyrimet tatimore që dalin nga të ardhurat që fitohen nën këtë kontratë. Bazuar në Ligjin nr. 06/L-105 për Tatimin mbi të Ardhurat, në përputhje me Nenin 31.2 \"Tatimpaguesit, që paguajn të drejta punësore (Royalties) për personat rezidentë ose jo-rezidentë, duhet të mbajë një taksë prej dhjetë për qind (10%) në kohën e pagesës ose kreditit\"."
        ]
    ],
    [
        'article_number' => '8',
        'title_eng' => 'PARTIES’ COVENANTS AND OWNERSHIP',
        'title_sq' => 'GARANICTË E PALËVE DHE PRONËSIA',
        'content_eng' => [
            "<b>8.1.</b> Upon execution of this contract, the Artist, who is the Copyright Owner, hereby affirms that they possess all the necessary rights to utilize, publish, distribute, and sell any audio and/or video masters that they intend to distribute and publish on their YouTube channel and Digital Stores. The Artist warrants that all requisite arrangements, whether verbal or written, with third parties have been duly completed, and that such parties have granted the Artist the necessary permission to authorize Baresha Music SH.P.K to distribute and publish any audio and video masters provided by the Artist on their YouTube channel and Digital Stores.",
            "<b>8.2.</b> By signing this contract, the Artist – The Copyright Owner, CERTIFIES that:",
            "<b>8.2.1.</b> The Artist affirms that they bear no financial liability towards third parties and have fulfilled all financial obligations owed to such parties. Any future financial obligations that may arise shall be the Artist's sole responsibility, and they will be held personally liable for any such potential obligations.",
            "<b>8.2.2.</b> The Artist warrants that they do not have any existing contractual agreements with any other YouTube or audio distribution companies. In the event that the Artist has such an agreement and still chooses to execute this contract with Baresha Music SH.P.K, they will assume full liability for any potential damages, whether financial or otherwise, that may arise.",
            "<b>8.2.3.</b> During the term of this contract, the Artist is prohibited from entering into any agreements with other distribution or publication companies for the release of audio and/or video masters on their YouTube channel and digital stores."
        ],
        'content_sq' => [
            "<b>8.1.</b> Duke nënshkruar këtë kontratë, Artisti - Pronari i të Drejtave dëshmon se ai posedon të gjitha të drejtat për të përdorur, publikuar, distribuar, shitur si dhe të drejtat, për çdo material audio dhe / ose video që Artisti do të distribuojë dhe publikojë në kanalin e tij në YouTube dhe në Dyqanet Dixhitale, për të cilat Artisti ka bërë të gjitha marrëveshjet e nevojshme, verbale ose të shkruara me palë të treta, dhe që të gjithë këto palë kanë lejuar me vlerë të plotë Artistin për të autorizuar Baresha Music SH.P.K për të distribuar / publikuar çdo material audio dhe video që artisti jep për ta publikuar në kanalin e tij në YouTube dhe në Dyqanet Dixhitale.",
            "<b>8.2.</b> Artisti – Pronari i të Drejtave, me nënshkrimin e kësaj kontrate VËRTETON se:",
            "<b>8.2.1.</b> Artisti konfirmon se nuk ka asnjë përgjegjësi financiare ndaj palëve të treta dhe se Artisti ka kryer të gjitha obligimet financiare ndaj palëve të treta. Në rast se ndonjë obligim financiar i tillë do të lindë në të ardhmen, Artisti do të jetë i përgjegjshëm personalisht për çdo detyrim potencial të tillë.",
            "<b>8.2.2.</b> Artisti garanton se nuk ka asnjë marrëveshje kontraktuale të tjera me asnjë kompani distributive të tjera në YouTube dhe audio dhe nëse ai / ajo ka një marrëveshje të tillë dhe ende nënshkruan këtë kontratë me Baresha Music SH.P.K, atëherë Artisti është i përgjegjshëm për çdo dëm potencial (financiar dhe çdo lloj dëmi që mund të lindë).",
            "<b>8.2.3.</b> Gjatë afatit të kësaj kontrate, Artisti është i ndaluar të hyjë në marrëveshje të tjera me kompani të tjera distributive ose botuese për publikimin e materialeve audio dhe / ose video në kanalin e tyre në YouTube dhe dyqanet e tyre digjitale."
        ]
    ],
    [
        'article_number' => '9',
        'title_eng' => 'DURATION OF THE CONTRACT',
        'title_sq' => 'KOHËZGJATJA E KONTRATËS',
        'content_eng' => [
            "<b>9.1.</b> The Cooperation Agreement shall be valid for a period of {$contractDetails['kohezgjatja']} months from the day the contract is signed and shall be automatically renewed for subsequent {$contractDetails['kohezgjatja']}-month periods unless otherwise terminated. Either the Artist (Copyright Owner) or Baresha Music SH.P.K (Copyright User) may terminate this Cooperation Agreement by providing written notice of termination at least 90 days prior to the end of each term. In the event of termination, the Agreement shall be deemed to have ended on the date on which it would have naturally expired. This Agreement may not be terminated prior to the end of any term, except in cases where the Artist provides written notice of termination with a valid reason via email, at least 3 months in advance."
        ],
        'content_sq' => [
            "<b>9.1.</b> Marrëveshja për bashkëpunim do të jetë e vlefshme për një periudhë prej {$contractDetails['kohezgjatja']} muajsh nga data e nënshkrimit dhe do të rinovohet automatikisht për periudha të mëtejshme prej {$contractDetails['kohezgjatja']} muajsh, përveç rasteve të ndërprerjes. As artisti (pronari i të drejtave të kopjimit) dhe as Baresha Music SH.P.K (përdoruesi i të drejtave të kopjimit) mund të ndërprejnë këtë marrëveshje për bashkëpunim duke siguruar njoftim me shkrim të ndërprerjes së saj të paktën 90 ditë para përfundimit të secilës periudhë. Në rast të ndërprerjes, marrëveshja do të konsiderohet se ka përfunduar në datën në të cilën ajo do të kishte përfunduar natyrshëm. Kjo marrëveshje nuk mund të ndërpritet përpara përfundimit të çdo periudhe, përveç rasteve kur artisti jep njoftim me shkrim të ndërprerjes së saj me arsyetim të vlefshëm nëpërmjet email-it, të paktën 3 muaj përpara përfundimit të marrëveshjes."
        ]
    ]
    // Add more articles as needed
];

// Function to Render Contract Sections
function renderContractSections($sections, $contractDetails)
{
    foreach ($sections as $section) {
        echo "<div class=\"row mb-4\">";
        echo "<h4 class='fw-bold text-center'>{$section['title_eng']}</h4>";
        echo "<h4 class='fw-bold text-center'>{$section['title_sq']}</h4>";
        echo "<div class=\"col\">";

        // Render English Content
        echo "<p class='fw-bold'>Eng. :</p>";
        foreach ($section['content_eng'] as $paragraph) {
            echo "<p>{$paragraph}</p>";
        }

        // Render Albanian Content
        echo "<p class='fw-bold'>Shqip. :</p>";
        foreach ($section['content_sq'] as $paragraph) {
            echo "<p>{$paragraph}</p>";
        }

        echo "</div>";
        echo "</div>";
    }
}

// Function to Render Contract Articles
function renderContractArticles($articles, $contractDetails)
{
    foreach ($articles as $article) {
        echo "<div class=\"row mb-4\">";
        echo "<p class=\"fw-bold\">ARTICLE {$article['article_number']} – {$article['title_eng']}</p>";
        echo "<p class=\"fw-bold\">NENI {$article['article_number']} – {$article['title_sq']}</p>";

        // Render English Content
        echo "<p class='fw-bold'>Eng. :</p>";
        foreach ($article['content_eng'] as $paragraph) {
            echo "<p>{$paragraph}</p>";
        }

        // Render Albanian Content
        echo "<p class='fw-bold'>Shqip. :</p>";
        foreach ($article['content_sq'] as $paragraph) {
            echo "<p>{$paragraph}</p>";
        }

        echo "</div>";
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <!-- Meta Tags and CSS Links -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BareshaNetwork - <?php echo date("Y"); ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.css" rel="stylesheet" />
    <!-- Favicon -->
    <link rel="shortcut icon" href="images/favicon.png" />
    <!-- Custom Styles -->
    <style>
        * {
            font-family: 'Montserrat', sans-serif;
            font-size: 13px;
        }

        @media print {
            body {
                background-image: url('/images/vula.png');
                background-repeat: no-repeat;
                background-size: cover;
                background-position: center;
                padding: 0;
                margin: 0;
            }

            .page-break {
                page-break-before: always;
            }

            header,
            footer,
            title {
                display: none !important;
            }
        }

        header,
        footer,
        title {
            display: none !important;
        }
    </style>
</head>

<body>
    <div class="container my-5 py-3">
        <div class="float-start">
            <a href="lista_kontratave_gjenerale.php" class='btn btn-light text-capitalize border border-1 shadow-2' id="backBtn" data-mdb-toggle="tooltip" title="Shko prapa">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
        <div class="float-end">
            <button class="btn btn-light text-capitalize border border-1 shadow-2" data-mdb-ripple-color="dark" id="printBtn" onclick="printData()">
                <i class="fas fa-print text-primary"></i> Print
            </button>
        </div>
    </div>

    <!-- Print Script -->
    <script>
        function printData() {
            var printContent = document.getElementById('contractContent').innerHTML;
            var originalContent = document.body.innerHTML;
            document.body.innerHTML = printContent;
            window.print();
            document.body.innerHTML = originalContent;
        }
    </script>

    <?php
    // Render Contract Sections
    renderContractSections($contractSections, $contractDetails);

    // Render Contract Articles
    renderContractArticles($contractArticles, $contractDetails);
    ?>

    <!-- Signature Section -->
    <div id="signatureSection" class="container my-5">
        <div class="py-5 px-5">
            <p>This Contract is Signed on – Kjo kontratë nëshkruhet më - : <?php echo date('d/m/Y'); ?></p>
            <p>Baresha Music SH.P.K – Për Baresha Music SH.PK &nbsp;&nbsp;&nbsp; Artist - Artisti</p>
            <div class="row">
                <div class="col">
                    <p class="fw-bold">AFRIM KOLGECI</p>
                </div>
                <div class="col text-end">
                    <p class="fw-bold"><?php echo $contractDetails['emri']; ?></p>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <p class="fw-bold text-start">Nënshkrimi <br> </p>
                </div>
                <div class="col-6">
                    <p class="fw-bold text-end">Nënshkrimi <br> </p>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <p class="border-bottom float-start w-50 text-start">
                        <img src="signatures/34.png" alt="Baresha Music Signature" style="width: 150px; height: auto;">
                    </p>
                </div>
                <div class="col-4 text-center">
                    <img src="images/vula.png" alt="Seal" style="width: 150px; height: auto; margin-top:-45px">
                </div>
                <div class="col-4">
                    <p class="border-bottom float-end w-50 text-end">
                        <?php
                        if (!empty($contractDetails['nenshkrimi'])) {
                            echo '<img src="' . sanitize_output($contractDetails['nenshkrimi']) . '" alt="Artist Signature" style="width: 150px; height: auto;">';
                        } else {
                            echo 'Signature not provided';
                        }
                        ?>
                    </p>
                </div>
            </div>
            <hr>
            <?php if (!empty(trim($contractDetails['shenim']))) { ?>
                <div class="row mt-5">
                    <div class="my-5 border rounded-5 py-3">
                        <h6>Shënime</h6>
                        <p><?php echo nl2br($contractDetails['shenim']); ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Bootstrap JS and MDB -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.js"></script>
</body>

</html>