<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BareshaNetwork - <?php echo date("Y"); ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.css" rel="stylesheet" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">

    <!-- Fav Icon ne formatin .png -->
    <link rel="shortcut icon" href="images/favicon.png" />
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
        <div class="float-start"><a href="lista_kontratave_gjenerale.php" class='btn btn-light text-capitalize border border-1 shadow-2' id="backBtn" data-mdb-toggle="tooltip" title="Shko prapa"><i class="fas fa-arrow-left "></i> Back</a></div>
        <div class="float-end">
            <button class="btn btn-light text-capitalize border border-1 shadow-2" data-mdb-ripple-color="dark" id="printBtn" onClick="printData()"><i class="fas fa-print text-primary "></i> Print</button>
        </div>

        <script>
            function printData() {
                var printContent = document.getElementById('contractContent').innerHTML;
                var originalContent = document.body.innerHTML;
                document.body.innerHTML = printContent;
                window.print();
                document.body.innerHTML = originalContent;
            }
        </script>
    </div>
    <?php
    include 'conn-d.php';


    $id = $_GET['id'];

    $query = "SELECT * FROM kontrata_gjenerale WHERE id = $id";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

    ?>
        <div id="contractContent" class="container my-5">



            <div class="py-5 px-5">
                <h4 class='fw-bold text-center'>CONTRACT ON COOPERATION</h4>
                <h4 class='fw-bold text-center'>KONTRATE BASHKPUNIMI</h4>

                <div class="row">
                    <div class="float-start">
                        <p class="fw-bold">No. Nr. : <?php echo $row['id_kontrates'] ?> </p>
                    </div>
                    <div class="float-start">
                        <p class="fw-bold">Date – Dat&euml;: <?php echo date('d/m/Y', strtotime($row['data_e_krijimit'])); ?></p>
                    </div>
                </div>

                <br>

                <div class="row">
                    <p class="fw-bold">Eng. :</p>
                    <p>This document specifies the terms and conditions of the agreement between <b>Baresha Music SH.P.K</b> , located at Rr. Brigada 123 nr. 23 in Suharek&euml;, represented by <b>AFRIM KOLGECI, CEO-FOUNDER of Baresha Music</b>, and <b>ARTIST: <?php
                                                                                                                                                                                                                                                                $nameAndEmail = explode("|", $row['artisti']);
                                                                                                                                                                                                                                                                echo $nameAndEmail[0];
                                                                                                                                                                                                                                                                ?></b>, a citizen of <b><?php echo $row['shteti'] ?></b>, with personal identification number <b><?php echo $row['numri_personal'] ?></b>. <?php echo $nameAndEmail[0]; ?> will be representing themselves on the other side of this agreement through their YouTube channel identified by the</p>
                    <p>YouTube ID - <b><?php echo $row['youtube_id'] ?></b> and the Channel name - <b><?php echo $nameAndEmail[0]; ?></b></p>
                    <p>The terms and conditions outlined in this contract pertain to the contractual relationship as a whole between the two parties.</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p>Ky dokument specifikon kushtet dhe kusht&euml;zimet e marr&euml;veshjes midis Baresha Music SH.P.K, me adres&euml; Rr. Brigada 123 nr. 23 n&euml; Suharek&euml;, e p&euml;rfaq&euml;suar nga <b>AFRIM KOLGECI, CEO-FOUNDER i Baresha Music</b>, dhe <b>ARTISTI: <?php echo $nameAndEmail[0]; ?></b>, qytetar i <b> <?php echo $row['shteti'] ?></b>, me num&euml;r personal identifikimi <b> <?php echo $row['numri_personal'] ?> </b>.<?php echo $nameAndEmail[0]; ?> do t&euml; p&euml;rfaq&euml;sohet nga ana e tyre n&euml; k&euml;t&euml; marr&euml;veshje p&euml;rmes kanalit t&euml; tyre n&euml; YouTube t&euml; identifikuar me YouTube ID - <b><?php echo $row['youtube_id'] ?></b> dhe emrin e kanalit - <b>Baresha Music.</b></p>
                    <p>Kushtet dhe kusht&euml;zimet e p&euml;rcaktuara n&euml; k&euml;t&euml; kontrat&euml; lidhen me marr&euml;dh&euml;nien kontraktuale n&euml; t&euml;r&euml;si midis dy pal&euml;ve.</p>
                    <p class="fw-bold">ARTICLE 1 – DEFINITIONS</p>
                    <p class="fw-bold">NENI 1 – DEFINICIONET</p>
                    <p>Eng. :</p>
                    <p><b>1.1. Artist - Copyright Owner </b>– refers to a natural or legal person that represents himself or a group or a band, that authorizes Baresha Music SH.P.K.</p>
                    <p><b>1.1. Artisti - Pronari i t&euml; Drejtave </b>- p&euml;rfaqsion nj&euml; person fizik ose juridik q&euml; p&euml;rfaq&euml;son veten, nj&euml; grup ose nj&euml; band&euml;, q&euml; autorizon Baresha Music SH.P.K.</p>
                    <p>Eng. :</p>
                    <p><b>1.2. Baresha Music SH.P.K </b>- Copyright User - refers to the company that holds exclusive rights to distribute, sell, and publish audio and video masters on YouTube platforms and digital stores under the terms of this contract.</p>
                    <p>Shqip. :</p>
                    <p><b>1.2. Baresha Music SH.P.K </b>- P&euml;rdoruesi i t&euml; Drejtave - p&euml;rfaqson kompanin&euml; q&euml; mbart t&euml; drejta ekskluzive p&euml;r shp&euml;rndarjen, shitjen dhe publikimin e masterit audio dhe video n&euml; platformat e YouTube dhe dyqanet dixhitale n&euml;n k&euml;t&euml; kontrat&euml; siq cek&euml;t n&euml; nenin 1.3.</p>
                    <p><b>1.3. Digital Stores – Shitore Dixhitale</b></p>
                    <ul style="margin-left:50px;">
                        <li>Spotify</li>
                        <li>Apple Music</li>
                        <li>YouTube Music</li>
                        <li>Deezer</li>
                        <li>Amazon Music</li>
                        <li>Etc – Etj</li>
                    </ul>

                </div>
                <div class="clearfix"></div>
                <div class="row">
                    <p><b>ARTICLE 2 - OBJECT OF THE CONTRACT</b></p>
                    <p><b>NENI 2 – OBJEKTI I KONTRATES</b></p>
                    <p><b>Eng. :</b></p>
                    <p><b>2.1.</b> The copyright owner hereby GRANTS, namely awards EXCLUSIVE RIGHTS to Baresha Music SH.P.K (The Copyright User), for the distribution, sale and publication of audio materials and video masters on the artist's channel on YouTube and digital stores, as well as for the use of the artist's name, logo, photographs and biography on the artist's channel on YouTube and in all digital stores.</p>
                    <p><b>Shqip. :</b></p>
                    <p><b>2.1.</b> Pronari i t&euml; drejtave k&euml;tu AUTORIZON, q&euml; do t&euml; thot&euml; i jep t&euml; drejtat ekskluzive p&euml;r Baresha Music SH.P.K (P&euml;rdoruesi i t&euml; Drejtave), p&euml;r shp&euml;rndarjen, shitjen dhe publikimin e materialeve dhe masterit audio dhe video n&euml; kanalin e Artistit n&euml; YouTube dhe dyqanet dixhitale, si dhe p&euml;r p&euml;rdorimin e emrit, logos, fotografive dhe biografis&euml; s&euml; Artistit n&euml; kanalin e Artistit n&euml; YouTube dhe n&euml; t&euml; gjitha dyqanet dixhitale.</p>
                </div>
                <div class="row">
                    <p><b>ARTICLE 3 – THE RIGHT OS USE</b></p>
                    <p><b>NENI 3 – TE DREJTAT E PERDORIMIT</b></p>
                    <p><b>Eng. :</b></p>
                    <p><b>3.1.</b> <b>The Artist (Copyright Owner)</b> will grant authorization for their <b> previous and future works </b> potentially uploaded on their YouTube channel to <b> Baresha Music SH.P.K (Copyright User) </b>, under a special contract. This contract will provide the Copyright User with the right to use the works without any fixed duration. In the event of the termination of the Cooperation Contract, <b>Baresha Music SH.P.K (Copyright User)</b> shall return all rights to the b Artist (Copyright Owner) within 30 days of the termination of the Cooperation Contract. </p>
                    <p>
                    <p><b>Shqip. :</b></p>
                    <p><b>3.1. Artisti (Pronari i t&euml; Drejtave)</b> do t&euml; autorizoj&euml; <b> veprat e tij t&euml; m&euml;parshme dhe t&euml; ardhshme</b>, t&euml; ngarkuara potencialisht n&euml; kanalin e tij n&euml; YouTube, n&euml; dispozicion t&euml; <b> Baresha Music SH.P.K (P&euml;rdoruesi i t&euml; Drejtave)</b>, n&euml;n nj&euml; kontrat&euml; t&euml; veqant. Kontrata e t&euml; Drejtave do t'i jap&euml; P&euml;rdoruesit t&euml; Copyright-it t&euml; drejt&euml;n p&euml;r t&euml; p&euml;rdorur veprat pa nj&euml; koh&euml;zgjatje t&euml; caktuar. N&euml; rast t&euml; nd&euml;rprerjes s&euml; Kontrat&euml;s s&euml; Bashk&euml;punimit,<b> Baresha Music SH.P.K (P&euml;rdoruesi i Copyright-it)</b> do t'i kthej&euml; t&euml; gjitha t&euml; drejtat Artistit (Pronarit t&euml; Drejtave) brenda 30 dit&euml;ve nga nd&euml;rprerja e Kontrat&euml;s s&euml; Bashk&euml;punimit. </p>
                    </p>
                    <p><b>Eng. :</b></p>
                    <p><b>3.2.</b> Through the execution of this contract, the Artist hereby grants authorization to Baresha Music SH.P.K to utilize all of their channel videos for promotional purposes pertaining to other clients of Baresha Music SH.P.K through the use of "End Screens" and "Cards".</p>
                    <p><b>Shqip. :</b></p>
                    <p><b>3.2.</b> N&euml;p&euml;rmjet n&euml;nshkrimit t&euml; k&euml;saj kontrate, Artisti autorizon Baresha Music SH.P.K p&euml;r t&euml; p&euml;rdorur t&euml; gjitha videot n&euml; kanalin e tij p&euml;r promovimin e klient&euml;ve t&euml; tjer&euml; n&euml; Baresha Music SH.P.K duke p&euml;rdorur "End Screens" dhe "Cards". </p>
                    <p><b>Eng. :</b></p>
                    <p><b>3.3.</b> By signing this contract, the Artist acknowledges that they have read and understood these rules and accepts responsibility for complying with this article and the contract as a whole. The Artist may only use purchased instrumentals that come with a license. For each publication, the Artist is required to provide the license if the beat is sourced from YouTube or the Internet. The Artist may also use melody lines, instrumentals, videos and lyrics that have been authorized by their respective authors, producers and songwriters. The use of any material that is not licensed or authorized is strictly prohibited on the Artist’s channel:
                    <ul style="margin-left:50px;">
                        <li>It is not allowed and it is strictly forbidden to publish songs that contain YouTube “Free” beats or “Free” beats anywhere on the internet.</li>
                        <li>
                            It is strictly forbidden and not permitted to use the Instrumental: Beats, Melodies, or Lyrics which are from the Internet or YouTube, but that the Artist does not have the authorship/authorization of the authors, producers or songwriters.
                        </li>
                        <li>
                            “Covers” or “Remix” are strictly prohibited to be published if the Artist doesn’t have the direct authorization from the (Artist, Artists, Authors, Producers) of the song recording and composition.
                        </li>
                        <li>
                            Music projects that the Artist owns and/or are located on his channel, in which they include either “Free” beat or beat without a license, then the Artist must delete projects or create a New Channel if he want to collaborate with Baresha Music SH.P.K
                        </li>
                    </ul>
                    <p>If the artist sends to Baresha Music SH.P.K any music projects such as (Audio, Video) of any kind or publishes by himself on his/her channel while he is in COOPERATION with Baresha Music SH.P.K containing.</p>
                    <ul style="margin-left:50px;list-style-type:none;">
                        <li> - Free Beats (Without License)</li>
                        <li>
                            - Instrumental and also Beats (Without License)
                        </li>
                        <li>
                            - Vocals Containing other Artist work (Without Clearance from The Original Owner)
                        </li>
                        <li>
                            - Unauthorized Lyrics containing other Artist work (Without their Approval)
                        </li>
                        <li>
                            - Videos Containing other Artist work (Without their Approval)
                        </li>
                    </ul>
                    <p>If the rules mentioned above are to happen the artist must pay to Baresha Music SH.P.K between 20.000 and 40.000 Euros for the damages caused to Baresha Music.</p>
                    </p>

                    <p><b>Shqip. :</b></p>
                    <p><b>3.3.</b> Me n&euml;nshkrimin e k&euml;saj kontrate, Artisti pranon se i ka lexuar dhe kuptuar k&euml;to rregulla dhe pranon p&euml;rgjegj&euml;sin&euml; p&euml;r respektimin e k&euml;tij neni dhe kontrat&euml;s n&euml; t&euml;r&euml;si. Artisti mund t&euml; p&euml;rdor&euml; vet&euml;m instrumente t&euml; blera q&euml; vijn&euml; me licenc&euml;. P&euml;r çdo publikim, Artistit i k&euml;rkohet t&euml; jap&euml; licenc&euml;n n&euml;se beat e ka burimin nga YouTube ose interneti. Artisti mund t&euml; p&euml;rdor&euml; gjithashtu vija melodike, instrumente, video dhe tekste q&euml; jan&euml; autorizuar nga autor&euml;t, producent&euml;t dhe kompozitor&euml;t e tyre p&euml;rkat&euml;s. P&euml;rdorimi i çdo materiali q&euml; nuk &euml;sht&euml; i licencuar ose i autorizuar &euml;sht&euml; rrept&euml;sisht i ndaluar n&euml; kanalin e Artistit:
                    <ul style="margin-left:50px;">
                        <li>Nuk lejohet dhe ndalohet rrept&euml;sisht publikimi i k&euml;ng&euml;ve q&euml; p&euml;rmbajn&euml; beat “Free” ose “Free” t&euml; YouTube apo kudo n&euml; internet.</li>
                        <li>Ndalohet rrept&euml;sisht dhe nuk lejohet p&euml;rdorimi i instrumenteve: Beats, Melodi, apo Tekste q&euml; jan&euml; nga Interneti apo YouTube, por q&euml; Artisti nuk ka autor&euml;sin&euml;/autorizimin e artistit, producent&euml;ve apo, tekst shkrues&euml;ve.</li>
                        <li>Projektet muzikore q&euml; Artisti zot&euml;ron dhe/ose ndodhen n&euml; kanalin e tij, n&euml; t&euml; cilat p&euml;rfshijn&euml; beat "Falas" ose beat pa licenc&euml;, at&euml;her&euml; Artisti duhet t&euml; fshij&euml; projektet ose t&euml; krijoj&euml; nj&euml; Kanal t&euml; Ri n&euml;se d&euml;shiron t&euml; bashk&euml;punoj&euml; me Baresha Music SH.P.K.</li>
                    </ul>
                    <p>N&euml;se artisti i d&euml;rgon Baresha Music SH.P.K ndonj&euml; projekt muzikor si (Audio, Video) t&euml; çfar&euml;do lloji ose publikon vet&euml; n&euml; kanalin e tij/saj gjat&euml; koh&euml;s q&euml; &euml;sht&euml; n&euml; BASHKËPUNIM me Baresha Music SH.P.K q&euml; p&euml;rmban.</p>
                    <ul style="margin-left:50px;list-style-type:none;">
                        <li>- Free Beats (Pa licenc&euml;)</li>
                        <li>
                            - Instrumentale dhe gjithashtu Beats (Pa licenc&euml;)
                        </li>
                        <li>- Vokale q&euml; p&euml;rmbajn&euml; vepra t&euml; tjera t&euml; artistit (Pa aprovim nga pronari original)</li>
                        <li>- Tekste t&euml; paautorizuara q&euml; p&euml;rmbajn&euml; vepra t&euml; tjera artist&euml;sh (Pa miratimin e tyre)</li>
                        <li>- Video q&euml; p&euml;rmbajn&euml; vepra t&euml; tjera t&euml; artist&euml;ve (Pa miratimin e tyre)</li>



                    </ul>
                    <p> N&euml;se ndodh&euml; q&euml; rregullat e m&euml;sip&euml;rme t&euml; thyhen, artisti duhet t'i paguaj&euml; Baresha Music SH.P.K nga 20.000 deri n&euml; 40.000 Euro p&euml;r d&euml;met e shkaktuara n&euml; Baresha Music.</p>

                    </p>

                    <p><b>Eng. :</b></p>
                    <p><b>3.4.</b> The artist hereby affirms that they have carefully reviewed the fundamental regulations and guidelines presented herein, and by affixing their signature to this agreement, they confirm their acknowledgement and agreement to abide by these terms:</p>
                    <p><b>3.4.1</b> Will not alter or manipulate any aspect related to YouTube, and explicitly declare that I will refrain from modifying, editing or creating content for the "Tags" section, "Metadata," "Description," "Hashtags," "Channel Tags," or "Thumbnail" of any material.
                        The use of names belonging to other artists or trademarks without the written authorization of the respective owner is strictly prohibited. Non-compliance with this rule by the ARTIST may result in legal liability for any potential damages incurred, including financial compensation.
                    </p>
                    <p><b>3.4.2</b> The ARTIST shall refrain from uploading any content to YouTube that includes any material, including but not limited to audio, video, instrumental, melody, text, photo, image, person, logo, or trademark that is not their own and for which they lack written authorization from the respective owner. Failure to comply with this rule may result in legal liability for any potential damages incurred, including financial compensation.</p>

                    <p><b>3.4.3</b> The ARTIST is prohibited from removing any Baresha Music representative from their designated role as "Owner," "Manager," or "Editor" without executing a contract. In the event of such an occurrence, the ARTIST must restore the removed individual(s) within 48 hours. Failure to do so will result in a daily penalty of 20 Euros for each day beyond the two-day limit until the roles of "Owner," "Manager," or "Editor" are returned to the Baresha Music representatives.</p>
                    <p><b>Shqip. :</b></p>
                    <p><b>3.4.</b> Artisti me k&euml;t&euml; deklaron se ka shqyrtuar me kujdes rregulloret dhe udh&euml;zimet themelore t&euml; paraqitura n&euml; k&euml;t&euml; marr&euml;veshje, dhe duke vendosur n&euml;nshkrimin e tyre n&euml; k&euml;t&euml; marr&euml;veshje, ata konfirmojn&euml; pranimin dhe pajtimin e tyre p&euml;r t&euml; respektuar kushtet e saj siq jan me posht&euml;.</p>
                    <p><b>3.4.1</b> Nuk do t&euml; ndryshoj ose manipuloj asnj&euml; aspekt t&euml; lidhur me YouTube-n, dhe deklaron me qart&euml;si se do t&euml; ndaloj t&euml; modifikoj, redaktoj ose krijoj p&euml;rmbajtje p&euml;r "Tags", "Metadata", "Description", "Hashtags", "Channel Tags" ose "Thumbnail" t&euml; çdo materiali.
                        P&euml;rdorimi i emrave q&euml; i takojn&euml; artist&euml;ve tjer&euml; ose markave t&euml; regjistruara pa autorizimin me shkrim t&euml; pronarit t&euml; tyre &euml;sht&euml; kategorikisht i ndaluar. Mosrespektimi i k&euml;saj rregulle nga ARTISTI mund t&euml; çoj&euml; n&euml; p&euml;rgjegj&euml;si ligjore p&euml;r çdo d&euml;m potencial q&euml; mund t&euml; shkaktohet, duke p&euml;rfshir&euml; d&euml;mshp&euml;rblim financiar.

                    </p>
                    <p><b>3.4.2</b> ARTISTI duhet t&euml; ndaloj&euml; ngarkimin e çdo lloj p&euml;rmbajtjeje n&euml; YouTube q&euml; p&euml;rfshin çdo material, duke p&euml;rfshir&euml; por jo duke u kufizuar me audio, video, instrumentale, melodi, tekst, foto, imazh, person, logo ose mark&euml; tregtare q&euml; nuk i takojn&euml; atij dhe p&euml;r t&euml; cilat ai nuk ka autorizim me shkrim nga pronari i tyre. Mosrespektimi i k&euml;saj rregulle mund t&euml; çoj&euml; n&euml; p&euml;rgjegj&euml;si ligjore p&euml;r çdo d&euml;m potencial q&euml; mund t&euml; shkaktohet, duke p&euml;rfshir&euml; d&euml;mshp&euml;rblim financiar.</p>

                    <p><b>3.4.3</b> ARTISTI &euml;sht&euml; i ndaluar t&euml; heq&euml; nga roli i caktuar si "Owner", "Manager" ose "Editor" i caktuar nga Baresha Music pa n&euml;nshkrimin e nj&euml; kontrate. N&euml; rast se ndodh nj&euml; ngjarje e till&euml;, ARTISTI duhet t&euml; rikthej&euml; personat e hequr brenda 48 or&euml;ve. Mosrespektimi i k&euml;saj rregulle do t&euml; rezultoj&euml; n&euml; nj&euml; gjob&euml; ditore prej 20 Euro p&euml;r çdo dit&euml; pas limitit t&euml; dy dit&euml;ve deri n&euml; momentin q&euml; rolet e "Owner", "Manager" ose "Editor" kthehen te p&euml;rfaq&euml;suesit e Baresha Music.</p>

                    <p><b>Eng. :</b></p>
                    <p>
                        <b> 3.4. </b>In the event that the artist fails to comply with these rules, Baresha Music SH.P.K hereby reserves the right to terminate the cooperation agreement, as well as any other agreements in place with the artist, with immediate effect. It is understood that the artist, as the rightful owner of the rights, shall be solely responsible and liable to you for any and all financial and other damages resulting from non-compliance with these rules.
                        In the event of non-compliance with these Rules, the Artist shall be solely responsible for any potential harm, including financial and consequential damages, arising from such non-compliance, as well as any other consequences resulting from the breach of the aforementioned provisions.
                    </p>

                    <p><b> Shqip. : </b></p>
                    <p><b>3.4.</b> N&euml; rast se artisti nuk i bindet k&euml;tyre rregullave, Baresha Music SH.P.K rezervon k&euml;tu t&euml; drejt&euml;n p&euml;r t&euml; nd&euml;rprer&euml; marr&euml;veshjen e bashk&euml;punimit, si dhe çdo marr&euml;veshje tjet&euml;r n&euml; vend me artistin, me efekt t&euml; menj&euml;hersh&euml;m. Ësht&euml; kuptuar se artisti, si pronar i t&euml; drejtave t&euml; autorit, do t&euml; jet&euml; i vet&euml;m p&euml;rgjegj&euml;s dhe p&euml;r çdo d&euml;m financiar dhe d&euml;me tjera q&euml; shkaktohen nga mosbindja ndaj k&euml;tyre rregullave.
                        N&euml; rast se nuk respektohen k&euml;to Rregulla, Artisti do t&euml; jet&euml; i vet&euml;m p&euml;rgjegj&euml;s p&euml;r çdo d&euml;m potencial, duke p&euml;rfshir&euml; d&euml;met financiare dhe d&euml;met e tjera, si dhe p&euml;r çdo pasoj&euml; tjet&euml;r q&euml; mund t&euml; rezultoj&euml; nga mosrespektimi i dispozitave t&euml; p&euml;rmendura m&euml; lart dhe m&euml; posht&euml;.
                    </p>

                    <p><b>Eng. :</b></p>
                    <p>
                        <b>3.5.</b> Baresha Music SH.P.K further reserves the right to terminate the contract at any time if the ARTIST's actions on their YouTube channel endanger Baresha Music's operations, such as receiving unresolved Copyright Strikes or engaging in any activity that violates YouTube's rules, terms, and conditions. In the event of such termination, Baresha Music SH.P.K is obligated to liquidate any outstanding payments and release all clients' audio-visual materials from the use of Baresha Music SH.P.K within a period of four months.
                    </p>

                    <p><b>Shqip. : </p></b>
                    <p><b>3.5.</b> Baresha Music SH.P.K gjithashtu rezervon t&euml; drejt&euml;n p&euml;r t&euml; nd&euml;rprer&euml; kontrat&euml;n n&euml; çdo koh&euml; n&euml;se veprimet e ARTIST n&euml; kanalin e tyre n&euml; YouTube v&euml;shtir&euml;sojn&euml; veprimtarin&euml; e Baresha Music (p&euml;r shembull duke marr&euml; shkelje t&euml; drejtave t&euml; autorit pa u zgjidhur, apo duke b&euml;r&euml; çdo veprim q&euml; shkel rregullat, kushtet dhe kushtetutat e YouTube). N&euml; rast se ky rast ndodh, Baresha Music SH.P.K &euml;sht&euml; i detyruar q&euml; n&euml; nj&euml; periudh&euml; prej kat&euml;r muajsh t&euml; likuidoj&euml; çdo pages&euml; q&euml; nuk &euml;sht&euml; realizuar dhe t&euml; liroj&euml; t&euml; gjith&euml; materialet audiovizive t&euml; klient&euml;ve nga p&euml;rdorimi i Baresha Music SH.P.K.
                    </p>

                </div>
                <div class="row">
                    <p class="fw-bold">ARTICLE 4 – RIGHTS AND OBLIGATIONS OF THE COPYRIGHT USER – BARESHA MUSIC SH.P.K</p>
                    <p class="fw-bold"> ARTICLE 4 – TË DREJTAT DHE OBLIGIMET E PËRDORUESIT E TË DREJTAVE AUTORIALE – BARESHA MUSIC SH.P.K</p>
                    <p><b>Eng. :</b></p>
                    <p><b>4.1.</b> Baresha Music SH.P.K shall hold the exclusive right to distribute the audio and video content of the Artist on the Artist's YouTube channel as well as on all digital platforms.</p>
                    <p><b>Shqip. :</p></b>
                    <p><b>4.1.</b> Baresha Music SH.P.K do t&euml; mbaj&euml; t&euml; drejt&euml;n ekskluzive p&euml;r t&euml; shp&euml;rndar&euml; p&euml;rmbajtjen audio dhe video t&euml; Artistit n&euml; kanalin e tij t&euml; YouTube-s si dhe n&euml; t&euml; gjitha platformat dixhitale.
                    </p>
                    <p><b>Eng. :</b></p>
                    <p><b>4.2.</b> Baresha Music SH.P.K is authorized to distribute the materials provided by the artist on YouTube and other digital platforms, in accordance with the terms and conditions outlined in this contractual agreement.</p>
                    <p><b>Shqip. :</b></p>
                    <p><b>4.2.</b> Baresha Music SH.P.K do t&euml; shp&euml;rndaj&euml; materialet e d&euml;rguara nga artisti n&euml; YouTube dhe dyqanet dixhitale, siç &euml;sht&euml; specifikuar n&euml; k&euml;t&euml; kontrat&euml;.</p>
                    <p><b>Eng. :</b></p>
                    <p><b>4.3.</b> With the objective of advancing the promotion of the Artist's materials, Baresha Music SH.P.K. shall employ various strategies including "Cross-Promotion", "Tag Promotion", "Thumbnail Optimization", and any other available means at the company's disposal to achieve this objective.</p>
                    <p><b>Shqip. :</b></p>
                    <p>
                        <b>4.3.</b> Me q&euml;llim t&euml; promovimit t&euml; materialeve t&euml; artistit, Baresha Music SH.P.K do t&euml; p&euml;rdor&euml; strategjit&euml; e ndryshme si "Cross-Promotion", "Tag Promotion", "Thumbnail Optimization" dhe çdo form&euml; tjet&euml;r n&euml; dispozicion t&euml; kompanis&euml; Baresha Music SH.P.K. p&euml;r t&euml; arritur k&euml;t&euml; q&euml;llim.
                    </p>

                    <p><b>Eng. : </b></p>
                    <p><b>4.4.</b> Baresha Music SH.P.K will provide a maximum of one (1) video per "Instagram Story" and one (1) video for the "Instagram Feed" for each artist or performer featured in a song. These provisions are subject to the regulations outlined in Article 3.3.</p>
                    <p><b>Shqip. :</b></p>
                    <p><b>4.4.</b> Baresha Music SH.P.K do t'i d&euml;rgoj&euml; Artistit 1 (nj&euml;) video p&euml;r "Instagram Story" maksimumi dhe 1 (nj&euml;) video p&euml;r "Instagram Feed" p&euml;r secilin Artist n&euml; nj&euml; k&euml;ng&euml;, me rregullat e lart cekur t&euml; nenit 3.3.</p>
                    <p class="fw-bold">Eng. : </p>
                    <p><b>4.5.1.</b> For YouTube - To promote their song, Baresha Music SH.P.K may display advertisements in the form of banners or advertising clips on or before Copyright Owner's works (or albums) on YouTube.</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>4.5.1.</b> P&euml;r YouTube - Q&euml; t&euml; promovojn&euml; k&euml;ng&euml;n e tyre, Baresha Music SH.P.K mund t&euml; shfaq reklama n&euml; form&euml;n e banerave ose klipave reklamuese n&euml; ose para materialit (ose albumeve) t&euml; Pronarit t&euml; t&euml; Drejtave t&euml; Autorit n&euml; YouTube.</p>
                    <p class="fw-bold">Eng. : </p>
                    <p><b>4.5.2.</b> Baresha Music SH.P.K will conduct a thorough review process to detect and block any videos of works uploaded by unauthorized third parties. Alternatively, the company may choose to allow these works and monetize them accordingly.</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>4.5.2.</b> Baresha Music SH.P.K do t&euml; kryej&euml; nj&euml; proces t&euml; holl&euml;sish&euml;m vler&euml;suese p&euml;r t&euml; zbuluar dhe bllokuar çdo video t&euml; materialit t&euml; ngarkuara nga pal&euml; t&euml; treta pa autorizim. N&euml; rast se k&euml;to pun&euml; lejohen, kompania mund t&euml; vendos&euml; t'i monetizoj&euml; ato.</p>
                    <p class="fw-bold">Eng. : </p>
                    <p><b>4.5.3.</b> Baresha Music SH.P.K is committed to promptly removing any videos of works that are uploaded by unauthorized third parties.</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>4.5.3.</b> Baresha Music SH.P.K &euml;sht&euml; e vendosur q&euml; t&euml; heq&euml; menj&euml;her&euml; çdo video t&euml; materialit q&euml; ngarkohen nga pal&euml; t&euml; treta pa autorizim.</p>
                    <p class="fw-bold">Eng. : </p>
                    <p><b>4.5.4.</b> Baresha Music SH.P.K is committed to safeguarding the reputation of its artists. To this end, the company will remove any links from Google search results that redirect visitors to sites containing false information or that could potentially harm the image of the artist in question.</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>4.5.4.</b> Baresha Music SH.P.K &euml;sht&euml; e vendosur q&euml; t&euml; mbroj&euml; reputacionin e artist&euml;ve t&euml; saj. Me k&euml;t&euml; q&euml;llim, kompania do t&euml; heq&euml; çdo lloj linku nga rezultatet e k&euml;rkimit n&euml; Google q&euml; ridrejtojn&euml; vizitor&euml;t n&euml; faqe t&euml; internetit q&euml; p&euml;rmbajn&euml; informacione t&euml; rreme ose q&euml; mund t&euml; d&euml;mtojn&euml; imazhin e artistit n&euml; fjal&euml;.</p>
                    <p class="fw-bold">Eng. : </p>

                    <p><b>4.5.5.</b> Baresha Music SH.P.K is committed to maintaining the integrity of its artists' image and reputation. To this end, the company will remove any illegal materials published in connection with the artist on YouTube and other "User Generated Content" platforms such as SoundCloud, DailyMotion, etc.</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>4.5.5.</b> Baresha Music SH.P.K &euml;sht&euml; e vendosur t&euml; mbaj&euml; integritetin e imazhit dhe reputacionit t&euml; artist&euml;ve t&euml; saj. Me k&euml;t&euml; q&euml;llim, kompania do t&euml; heq&euml; çdo material t&euml; paligjsh&euml;m q&euml; publikohet n&euml; lidhje me artistin n&euml; YouTube dhe platforma t&euml; tjera t&euml; "P&euml;rmbajtjes s&euml; Krijuar nga P&euml;rdoruesit" si SoundCloud, DailyMotion etj.</p>

                    <p class="fw-bold">Eng. : </p>
                    <p><b>4.6.</b> Baresha Music SH.P.K is hereby granted authorization by the Artist, who is the Copyright Owner, to exclusively exercise the right to profit from the following:</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>4.6.</b> Baresha Music SH.P.K merr autorizimin e nevojsh&euml;m nga Artisti, i cili &euml;sht&euml; pronari i t&euml; drejtave t&euml; autorit, p&euml;r t&euml; ushtruar ekskluzivisht t&euml; drejt&euml;n p&euml;r t&euml; p&euml;rfituar nga t&euml; ardhurat e m&euml;poshtme:</p>
                    <p class="fw-bold">Eng. : </p>
                    <p><b>4.6.1.</b> During the term of this contractual agreement, the company shall be authorized to engage in the distribution, publication, and sale of the audio and video content of the Artist on the Artist's YouTube channel, as well as on all digital stores.</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>4.6.1.</b> Gjat&euml; koh&euml;zgjatjes s&euml; k&euml;saj marr&euml;veshje kontraktuale, kompania do t&euml; jet&euml; e autorizuar p&euml;r t&euml; angazhuar veten n&euml; shp&euml;rndarjen, publikimin dhe shitjen e p&euml;rmbajtjes audio dhe video t&euml; Artistit n&euml; kanalin YouTube t&euml; Artistit, si dhe n&euml; t&euml; gjitha dyqanet dixhitale.</p>
                    <p class="fw-bold">Eng. : </p>
                    <p><b>4.6.2.</b> The placement of advertisements, including banners or clips, prior to or during the display of the Artist's works or albums, as well as all other materials submitted by the Artist that generate profits, as specified in this agreement, shall be authorized.</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>4.6.2.</b> Vendosja e reklamave, duke p&euml;rfshir&euml; flamuj ose klipet, para ose gjat&euml; shfaqjes s&euml; pun&euml;ve ose albumeve t&euml; Artistit, si dhe t&euml; gjitha materialet e tjera t&euml; paraqitura nga Artisti q&euml; gjenerojn&euml; fitime, ashtu siç &euml;sht&euml; specifikuar n&euml; k&euml;t&euml; marr&euml;veshje, do t&euml; autorizohet.</p>
                    <p class="fw-bold">Eng. : </p>
                    <p><b>4.7.</b> Baresha Music SH.P.K is obligated to provide timely notification to the Artist every four months, as outlined in the terms of this contractual agreement, for all payments exceeding 100 Euro made on Digital Stores. If this minimum threshold has not been met, the payment shall be deferred to the next payment period until the threshold has been achieved. This same protocol applies for YouTube payments, with payments are made on a monthly basis.</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>4.7.</b> Baresha Music SH.P.K &euml;sht&euml; e detyruar t&euml; njoftoj&euml; Artistin n&euml; koh&euml; çdo kat&euml;r muaj, siç &euml;sht&euml; p&euml;rcaktuar n&euml; kushtet e k&euml;saj marr&euml;veshje kontraktuale, p&euml;r t&euml; gjitha pagesat q&euml; tejkalojn&euml; 100 euro dhe jan&euml; b&euml;r&euml; n&euml; Dyqanet Dixhitale. N&euml;se kjo shum&euml; minimale nuk &euml;sht&euml; arritur, pagesa do t&euml; shtyhet p&euml;r n&euml; periudh&euml;n e pagesave t&euml; ardhshme, derisa kufiri minimal t&euml; arrihet. Ky protokoll i nj&euml;jt&euml; aplikohet edhe p&euml;r pagesat e YouTube, ku pagesat b&euml;hen çdo muaj.</p>
                    <p class="fw-bold">Eng. : </p>
                    <p><b>4.8.</b> As per the terms of this agreement, Baresha Music SH.P.K reserves the right to take leave and observe official holidays of the Republic of Kosovo. During such periods, the company shall not accept any new publications or releases. Hence, any publications or releases must be submitted to us at least one day prior to the intended release date.</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>4.8.</b> Sipas kushteve t&euml; k&euml;saj marr&euml;veshje, Baresha Music SH.P.K rezervon t&euml; drejt&euml;n p&euml;r t&euml; marr&euml; pushime dhe p&euml;r t&euml; festuar dit&euml;t zyrtare t&euml; Republik&euml;s s&euml; Kosov&euml;s dhe gjat&euml; k&euml;tyre dit&euml;ve ne nuk pranojm&euml; asnj&euml; publikim t&euml; ri - prandaj, çdo publikim duhet t&euml; d&euml;rgohet nj&euml; dit&euml; m&euml; par&euml;.</p>
                    <p class="fw-bold">Eng. : </p>
                    <p><b>4.9.</b> In the event that the artist decides to sell their channel to another artist or company, Baresha Music SH.P.K reserves the right to retain 100% of the earnings for that particular month. Furthermore, we request that a contract outlining the terms and conditions of the sale be submitted to us for our review and approval. This is to ensure that the new owner of the channel is aware of their obligations to Baresha Music SH.P.K and that any future earnings are directed to the appropriate party.</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>4.9.</b> N&euml; rast se artisti vendos t&euml; shes&euml; kanalin e tij nj&euml; artist tjet&euml;r ose nj&euml; kompanie tjet&euml;r, Baresha Music SH.P.K rezervon t&euml; drejt&euml;n p&euml;r t&euml; mbajtur 100% t&euml; ardhurave p&euml;r at&euml; muaj t&euml; caktuar. N&euml; t&euml; nj&euml;jt&euml;n koh&euml;, k&euml;rkojm&euml; q&euml; nj&euml; kontrat&euml; q&euml; p&euml;rmban kushtet dhe parimet e shitjes t&euml; paraqitet p&euml;r shqyrtim dhe aprovim tek ne. Kjo &euml;sht&euml; p&euml;r t&euml; siguruar q&euml; pronari i ri i kanalit &euml;sht&euml; i vet&euml;dijsh&euml;m p&euml;r obligimet e tij ndaj Baresha Music SH.P.K dhe q&euml; t&euml; ardhurat e ardhshme drejtohen tek pala e duhur.
                    <p class="fw-bold">Eng. : </p>
                    <p><b>4.10.</b> In the event that the artist does not release any new content within a year, Baresha Music SH.P.K reserves the right to terminate this contract as well as any other existing contracts with the artist. In this case, the channel will also lose monetization.
                    </p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>4.10.</b> N&euml; rast se artisti nuk publikon asnj&euml; lloj p&euml;rmbajtjeje t&euml; re brenda nj&euml; viti, Baresha Music SH.P.K rezervon t&euml; drejt&euml;n p&euml;r t&euml; shfuqizuar k&euml;t&euml; kontrat&euml; dhe çdo kontrat&euml; tjet&euml;r me artistin. N&euml; k&euml;t&euml; rast, kanali do t&euml; humbas&euml; monetizimin.
                    </p>
                </div>

                <div class="row">
                    <p class="fw-bold">ARTICLE 5 – TË DREJTAT DHE OBLIGIMET E ARTISTIT</p>
                    <p class="fw-bold"> NENI 5 – RIGHTS AND OBLIGATIONS OF THE ARTIST </p>
                    <p class="fw-bold">Eng. :</p>
                    <p><b>5.1.</b> It is stipulated that during the term of this contract, the artist is prohibited from entering into contractual agreements with any other distribution companies that operate through YouTube and digital shops. This restriction only applies to the artist's own YouTube channel, which is identified by its YouTube ID as specified in both this contract and another agreement. In the event that the artist knowingly enters into a new agreement with Baresha Music SH.P.K,and he has a running contract with other distribution companies, any resulting consequences will be the sole responsibility of the artist.</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>5.1.</b> Ësht&euml; parashikuar q&euml; gjat&euml; koh&euml;s s&euml; k&euml;saj kontrate, artisti &euml;sht&euml; i ndaluar nga hyrja n&euml; marr&euml;veshje kontraktuale me çdo kompani tjet&euml;r t&euml; shp&euml;rndarjes q&euml; operon p&euml;rmes YouTube dhe dyqaneve dixhitale. Kufizimi i k&euml;saj parashtruesje zbatohet vet&euml;m n&euml; kanalin e YouTube t&euml; artistit, i cili identifikohet me identifikuesin e tij t&euml; YouTube siç &euml;sht&euml; specifikuar n&euml; k&euml;t&euml; kontrat&euml; dhe n&euml; nj&euml; marr&euml;veshje tjet&euml;r. N&euml; rast se artisti me dije n&euml;nshkruan nj&euml; marr&euml;veshje t&euml; re me Baresha Music SH.P.K dhe ka nj&euml; kontrat&euml; aktive me kompani t&euml; tjera t&euml; shp&euml;rndarjes, at&euml;her&euml; çdo pasoj&euml; q&euml; rezulton &euml;sht&euml; e p&euml;rgjegj&euml;si e artistit.</p>
                    <p class="fw-bold">Eng. :</p>
                    <p><b>5.2.</b> During the validity of this contract, the artist is obliged to grant Baresha Music SH.P.K access to his/her YouTube channel. In the event that the artist decides to assume control over his/her YouTube channel and/or opts to revoke Baresha Music SH.P.K's access, any potential damage incurred to the YouTube channel or video content shall be borne by the artist. It is important to note that the channel is under joint management with Baresha Music, therefore the artist is expected to comply with the rules outlined in Article 3 of this agreement. Failure to comply may result in the artist being held accountable for any and all resulting consequences.
                    </p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>5.2.</b> Gjat&euml; vlefshm&euml;ris&euml; s&euml; k&euml;saj kontrate, artisti &euml;sht&euml; i detyruar t&euml; jap&euml; akses Baresha Music SH.P.K n&euml; kanalin e tij/ saj t&euml; YouTube. N&euml; rast se artisti vendos t&euml; marr&euml; kontrollin e kanalit t&euml; tij/saj t&euml; YouTube dhe/ose zgjedh t&euml; t&euml;rheq&euml; aksesin e Baresha Music SH.P.K, çdo d&euml;m potencial q&euml; mund t&euml; ndodh&euml; n&euml; kanalin e YouTube ose p&euml;rmbajtjen e videove do t&euml; mbulohet nga artisti. Ësht&euml; e r&euml;nd&euml;sishme t&euml; theksohet se kanali &euml;sht&euml; n&euml; menaxhim t&euml; p&euml;rbashk&euml;t me Baresha Music, prandaj pritet q&euml; artisti t&euml; p&euml;rmbahet nga rregullat e p&euml;rmendura n&euml; Nenin 3 t&euml; k&euml;saj marr&euml;veshje. N&euml; rast se artisti nuk i ndjek k&euml;to rregulla, ai/ajo do t&euml; jet&euml; i/e p&euml;rgjegjsh&euml;m p&euml;r çdo pasoj&euml; q&euml; ndodhin si pasoj&euml; e k&euml;saj.</p>
                    <p class="fw-bold">Eng. :</p>
                    <p><b>5.3.</b> As per the terms of this agreement, the artist is entitled to receive proceeds from the distribution and sale of audio and/or video masters by Baresha Music SH.P.K on YouTube and all other digital platforms.</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>5.3.</b> Sipas kushteve t&euml; k&euml;saj marr&euml;veshje, artisti ka t&euml; drejt&euml; t&euml; marr&euml; t&euml; ardhurat nga shp&euml;rndarja dhe shitja e master audio dhe / ose video nga Baresha Music SH.P.K n&euml; YouTube dhe n&euml; t&euml; gjitha dyqanet dixhitale.</p>
                    <p class="fw-bold">Eng. :</p>
                    <p><b>5.4.</b> The artist shall be responsible for the publication, distribution, and sale of their audio and video master recordings, exclusively through Baresha Music Sh.P.K, both on their YouTube channel and in various digital stores for the contractual period.</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>5.4.</b> Artisti do t&euml; publikoj&euml;, distribuoj&euml; dhe shes&euml; master audio dhe video n&euml; kanalin e tij t&euml; YouTube dhe n&euml; dyqanet dixhitale vet&euml;m p&euml;rmes Baresha Music Sh.P.K p&euml;r koh&euml;n e kontrat&euml;s.
                    </p>
                    <p class="fw-bold">Eng. :</p>
                    <p><b>5.5.</b> The owner of the rights, herein referred to as "the artist," hereby grants full authorization to Baresha Music to post any audio, video, or photographic content on Instagram, Facebook, and any other relevant internet platform. Baresha Music is also authorized to generate profits from the artist's content, subject to the profit-sharing agreement outlined in Article Sections 6.1 of this contract. Should the artist terminate this contract, they may request the removal of any previously uploaded audio, video, or photographic content from Baresha Music's platform. It is hereby declared that the artist shall not authorize any other company or individual to submit a copyright strike or any similar actions against Baresha Music personally. Any such actions that harm or damage Baresha Music will result in the artist being solely responsible for all damages, including any additional losses incurred.</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>5.5.</b> Pronari i t&euml; drejtave, n&euml; vijim i quajtur "artisti", i jep plot&euml;sisht autorizimin Baresha Music p&euml;r t&euml; postuar cilindo lloj p&euml;rmbajtjeje audio, video ose fotografike n&euml; Instagram, Facebook dhe çdo platform&euml; tjet&euml;r n&euml; internet. Baresha Music gjithashtu &euml;sht&euml; autorizuar p&euml;r t&euml; generuar fitime nga p&euml;rmbajtja e artistit, n&euml; p&euml;rputhje me marr&euml;veshjen e ndarjes s&euml; fitimit t&euml; p&euml;rcaktuar n&euml; Seksionet 6.1 t&euml; k&euml;saj kontrate. N&euml;se artisti nd&euml;rpren k&euml;t&euml; kontrat&euml;, at&euml;her&euml; ata mund t&euml; k&euml;rkojn&euml; largimin e çdo p&euml;rmbajtjeje audio, video ose fotografike q&euml; ka qen&euml; e ngarkuar m&euml; par&euml; nga Baresha Music. Deklarohet se artisti nuk do t&euml; autorizoj&euml; ndonj&euml; kompani ose individ p&euml;r t&euml; d&euml;rguar nj&euml; "Copyright Strike" ose ndonj&euml; veprim t&euml; ngjash&euml;m kund&euml;r Baresha Music personalisht. Çdo veprim i till&euml; q&euml; d&euml;mton Baresha Music do t&euml; rezultoj&euml; n&euml; faktin q&euml; artisti do t&euml; jet&euml; i vetmi p&euml;rgjegj&euml;s p&euml;r t&euml; gjitha d&euml;met, duke p&euml;rfshir&euml; humbjet shtes&euml;.
                    </p>
                    <p class="fw-bold">Eng. :</p>
                    <p><b> 5.6.</b> The artist hereby declares and warrants that for the duration of this contract, they shall refrain from opening any new YouTube channels or digital stores (including but not limited to Spotify, Apple Music, etc.). The artist further declares that they shall not do so either as an individual or through another company or individual.
                        Furthermore, the artist declares and warrants that they shall not publish any song(s) on another YouTube channel or any other channel on a digital store, whether through another company or individual or themselves, for the duration of this contract. The artist affirms that they shall not enter into any agreement with another company or individual, nor shall they do so themselves, for the purpose of publishing on YouTube or digital stores while this contract is in effect.
                    </p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b> 5.6.</b> Artisti deklaron dhe garanton se gjat&euml; koh&euml;s q&euml; kjo kontrat&euml; &euml;sht&euml; n&euml; fuqi, ai nuk do t&euml; hap&euml; nj&euml; kanal t&euml; ri n&euml; YouTube ose dyqane dixhitale (si Spotify, Apple Music, etj.). Artisti deklaron se nuk do ta b&euml;j&euml; k&euml;t&euml; as si person dhe as p&euml;rmes nj&euml; kompanie tjet&euml;r ose nj&euml; individi tjet&euml;r.
                        N&euml; m&euml;nyr&euml; t&euml; ngjashme, artisti deklaron dhe garanton se nuk do t&euml; publikoj&euml; nj&euml; ose m&euml; shum&euml; k&euml;ng&euml; nga nj&euml; kanal tjet&euml;r i YouTube ose ndonj&euml; kanal tjet&euml;r n&euml; dyqanin dixhital, n&euml;p&euml;rmjet nj&euml; kompanie tjet&euml;r ose individi tjet&euml;r ose vet&euml; as gjat&euml; koh&euml;s q&euml; kjo kontrat&euml; &euml;sht&euml; n&euml; fuqi. Artisti deklaron se nuk do t&euml; hyj&euml; n&euml; nj&euml; marr&euml;veshje me nj&euml; kompani tjet&euml;r ose individ p&euml;r publikime n&euml; YouTube dhe n&euml; dyqanin dixhital, nd&euml;rsa kjo kontrat&euml; &euml;sht&euml; n&euml; fuqi.
                    </p>
                    <p class="fw-bold">Eng. :</p>
                    <p><b>5.7.</b> It is mandatory for the artist to provide Baresha Music with their material/video/audio at least 24 hours prior to the intended release date. This will enable Baresha Music to complete the necessary marketing processes to ensure a successful content/release launch.
                    </p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>5.7.</b> Esht&euml; detyr&euml; e artistit t&euml; d&euml;rgoj&euml; materialet e tij / video / audio tek Baresha Music, 24 or&euml; para dat&euml;s s&euml; planifikuar t&euml; lansimit. Kjo do ti jap&euml; koh&euml; Baresha Music p&euml;r t&euml; finalizuar procesin e marketingut t&euml; p&euml;rmbajtjes / p&euml;r t&euml; siguruar nj&euml; lansim t&euml; suksessh&euml;m.
                    </p>
                    <p class="fw-bold">Eng. :</p>
                    <p>
                        <b>5.8.</b> It is mandatory for the artist to ensure that their channel does not contain any content that is not their own, and to promptly remove such content to comply with YouTube's rules on "Reused Content".
                    </p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>5.8.</b> Artisti &euml;sht&euml; i detyruar t&euml; kontrolloj&euml; n&euml;se kanali i tij p&euml;rmban materiale q&euml; nuk jan&euml; t&euml; tij dhe t'i largoj&euml; ato nga kanali n&euml; m&euml;nyr&euml; q&euml; t&euml; p&euml;rputhen me rregullat e YouTube p&euml;r "Reused Content".
                    </p>
                </div>

                <div class="row">
                    <p class="fw-bold">ARTICLE 6 – EARNINGS, EXPENDITURES, AND COMISSIONS</p>
                    <p class="fw-bold">NENI 6 – TË HYRAT SHPENZIMET DHE PROVIZIONET</p>
                    <p class="fw-bold">Eng. :</p>
                    <p>Revenues generated by the distribution, publication and sale of the Artist’s audio and video master on his YouTube channel and digital stores shall be transferred to the bank account designated by Baresha Music SH.PK</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p>Te ardhurat e gjeneruara nga shp&euml;rndarja, publikimi dhe shitja e masterit audio dhe video t&euml; artistit n&euml; kanalin e tij t&euml; YouTube dhe dyqanet dixhitale do t&euml; transferohen n&euml; llogarin&euml; bankare t&euml; caktuar nga Baresha Music SH.P.K.
                    </p>
                    <p class="fw-bold">Eng. :</p>
                    <p><b>6.1.</b> Baresha Music SH.P.K shall pay the artist with a sum of <?php echo $row['tvsh']?>.00 % of gross income for YouTube and 50% for Digital Store sales.</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>6.1.</b> Baresha Music SH.P.K do t&euml; paguaj&euml; artistin me nj&euml; shum&euml; prej <?php echo $row['tvsh']?>.00 % t&euml; t&euml; ardhurave bruto p&euml;r shitjet n&euml; YouTube dhe 50% p&euml;r shitjet n&euml; shitoret dixhitale.</p>
                    <p class="fw-bold">Eng. :</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p>Payments by Baresha Music SH.P.K for the Artist shall be done through through the designated bank accounts as follows:</p>
                    <p>Pagesat p&euml;r Artistin nga Baresha Music SH.P.K do t&euml; b&euml;hen n&euml;p&euml;rmjet llogaris bankare t&euml; cekur si m&euml; posht&euml;:</p>
                    <p>Account Holder – Pronari I Xhiro-Llgaris&euml;: <b><?php echo $row['pronari_xhirollogarise'] ?></b></p>
                    <p> Account Number - Numri Xhiro-Llgaris&euml;: <b><?php echo $row['numri_xhirollogarise'] ?></b> </p>
                    <p>Swift Code – Kodi Swift: <b><?php echo $row['kodi_swift'] ?></b></p>
                    <p>IBAN: <b><?php echo $row['iban'] ?></b></p>
                    <p>Bank Name – Emri Bank&euml;s: <b><?php echo $row['emri_bankes'] ?></b></p>
                    <p>Bank Address – Adresa Bank&euml;s: <b><?php echo $row['adresa_bankes'] ?></b> </p>
                    <p class="fw-bold">Eng. :</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>6.2. Expenses / Shpenzimet</b></p>
                    <p class="fw-bold">Eng. :</p>
                    <p><b>6.2.1.</b> The costs associated with the publication, distribution, marketing, optimization of the Artist’s content, and upkeep of their account on YouTube and digital stores, totaling 100 EURO/annum, shall be the responsibility of the Artist. Administrative costs for the first year must be paid in advance.</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>6.2.1.</b> Artisti duhet t&euml; b&euml;j&euml; pages prej&euml; 100 EURO/vit p&euml;r botim, shp&euml;rndarje, zhvillim t&euml;marketingut, rritjen e suksesit t&euml; k&euml;ng&euml;s, shitjen dhe mir&euml;mbajtjen e llogaris&euml; s&euml; artistit n&euml; YouTube dhe dyqane dixhitale. Pagesa p&euml;r koston administrative p&euml;r vitin e par&euml; duhet t&euml; b&euml;het paraprakisht.</p>
                    <p class="fw-bold">Eng. :</p>
                    <p><b>6.2.2.</b> The artist shall bear the responsibility of covering the banking commissions associated with the transfer of funds.</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>6.2.2.</b> Komisionet bankare p&euml;r transferimin e fondeve do t&euml; paguhen nga artisti.</p>


                </div>

                <div class="row">
                    <p class="fw-bold">ARTICLE 7 – PARTIES’ COVENANTS AND OWNERSHIP</p>
                    <p class="fw-bold">NENI 7 – GARANICTË E PALËVE DHE PRONËSIA</p>
                    <p><b>Eng. : </b></p>
                    <p><b>7.1.</b> Each party to the contract is responsible and liable for paying taxes on the income earned under this contract. In accordance with Law No. 06/L-105 on Corporate Income Tax, Article 31.2 stipulates that "each taxpayer who pays interest or royalties to residents or non-residents shall withhold tax at a rate of ten percent (10%) at the time of payment or credit."
                    </p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>7.1.</b> Secila pal&euml; kontraktuese &euml;sht&euml; p&euml;rgjegj&euml;se dhe e detyruar t&euml; paguaj&euml; detyrimet tatimore q&euml; dalin nga t&euml; ardhurat q&euml; fitohen n&euml;n k&euml;t&euml; kontrat&euml;. Bazuar n&euml; Ligjin nr. 06/L-105 p&euml;r Tatimin mbi t&euml; Ardhurat, n&euml; p&euml;rputhje me Nenin 31.2 "Tatimpaguesit, q&euml; paguajn t&euml; drejta pun&euml;sore (Royalties) p&euml;r personat rezident&euml; ose jo-rezident&euml;, duhet t&euml; mbaj&euml; nj&euml; taks&euml; prej dhjet&euml; p&euml;r qind (10%) n&euml; koh&euml;n e pages&euml;s ose kreditit".</p>
                </div>

                <div class="row">
                    <p class="fw-bold">ARTICLE 8 – PARTIES’ COVENANTS AND OWNERSHIP</p>
                    <p class="fw-bold">NENI 8 – GARANICTË E PALËVE DHE PRONËSIA</p>
                    <p class="fw-bold">Eng. :</p>
                    <p><b>8.1.</b> Upon execution of this contract, the Artist, who is the Copyright Owner, hereby affirms that they possess all the necessary rights to utilize, publish, distribute, and sell any audio and/or video masters that they intend to distribute and publish on their YouTube channel and Digital Stores. The Artist warrants that all requisite arrangements, whether verbal or written, with third parties have been duly completed, and that such parties have granted the Artist the necessary permission to authorize Baresha Music SH.P.K to distribute and publish any audio and video masters provided by the Artist on their YouTube channel and Digital Stores.</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>8.1.</b> Duke n&euml;nshkruar k&euml;t&euml; kontrat&euml;, Artisti - Pronari i t&euml; Drejtave d&euml;shmon se ai posedon t&euml; gjitha t&euml; drejtat p&euml;r t&euml; p&euml;rdorur, publikuar, distribuar, shitur si dhe t&euml; drejtat, p&euml;r çdo material audio dhe / ose video q&euml; Artisti do t&euml; distribuoj&euml; dhe publikoj&euml; n&euml; kanalin e tij n&euml; YouTube dhe n&euml; Dyqanet Dixhitale, p&euml;r t&euml; cilat Artisti ka b&euml;r&euml; t&euml; gjitha marr&euml;veshjet e nevojshme, verbale ose t&euml; shkruara me pal&euml; t&euml; treta, dhe q&euml; t&euml; gjith&euml; k&euml;to pal&euml; kan&euml; lejuar me vler&euml; t&euml; plot&euml; Artistin p&euml;r t&euml; autorizuar Baresha Music SH.P.K p&euml;r t&euml; distribuar / publikuar çdo material audio dhe video q&euml; artisti jep p&euml;r ta publikuar n&euml; kanalin e tij n&euml; YouTube dhe n&euml; Dyqanet Dixhitale.</p>
                    <p class="fw-bold">Eng. :</p>
                    <p><b>8.2.</b> By signing this contract, the Artist – The Copyright Owner, CERTIFIES that:</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>8.2.</b> Artisti – Pronari i t&euml; Drejtave, me n&euml;nshkrimin e k&euml;saj kontrate VËRTETON se:</p>
                    <p class="fw-bold">Eng. :</p>
                    <p><b>8.2.1.</b> The Artist affirms that they bear no financial liability towards third parties and have fulfilled all financial obligations owed to such parties. Any future financial obligations that may arise shall be the Artist's sole responsibility, and they will be held personally liable for any such potential obligations.</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>8.2.1.</b> Artisti konfirmon se nuk ka asnj&euml; p&euml;rgjegj&euml;si financiare ndaj pal&euml;ve t&euml; treta dhe se Artisti ka kryer t&euml; gjitha obligimet financiare ndaj pal&euml;ve t&euml; treta. N&euml; rast se ndonj&euml; obligim financiar i till&euml; do t&euml; lind&euml; n&euml; t&euml; ardhmen, Artisti do t&euml; jet&euml; i p&euml;rgjegjsh&euml;m personalisht p&euml;r çdo detyrim potencial t&euml; till&euml;.</p>
                    <p class="fw-bold">Eng. :</p>
                    <p><b>8.2.2.</b> The Artist warrants that they do not have any existing contractual agreements with any other YouTube or audio distribution companies. In the event that the Artist has such an agreement and still chooses to execute this contract with Baresha Music SH.P.K, they will assume full liability for any potential damages, whether financial or otherwise, that may arise.
                    </p>
                    <p class="fw-bold">Shqip. :</p>
                    <p>
                        <b>8.2.2.</b> Artisti garanton se nuk ka asnj&euml; marr&euml;veshje kontraktuale t&euml; tjera me asnj&euml; kompani distributive t&euml; tjera n&euml; YouTube dhe audio dhe n&euml;se ai / ajo ka nj&euml; marr&euml;veshje t&euml; till&euml; dhe ende n&euml;nshkruan k&euml;t&euml; kontrat&euml; me Baresha Music SH.P.K, at&euml;her&euml; Artisti &euml;sht&euml; i p&euml;rgjegjsh&euml;m p&euml;r çdo d&euml;m potencial (financiar dhe çdo lloj d&euml;mi q&euml; mund t&euml; lind&euml;).
                    </p>
                    <p class="fw-bold">Eng. :</p>
                    <p>
                        <b>8.2.3.</b> During the term of this contract, the Artist is prohibited from entering into any agreements with other distribution or publication companies for the release of audio and/or video masters on their YouTube channel and digital stores.
                    </p>
                    <p class="fw-bold">Shqip. :</p>
                    <p>
                        <b>8.2.3.</b> Gjat&euml; afatit t&euml; k&euml;saj kontrate, Artisti &euml;sht&euml; i ndaluar t&euml; hyj&euml; n&euml; marr&euml;veshje t&euml; tjera me kompani t&euml; tjera distributive ose botuese p&euml;r publikimin e materialeve audio dhe / ose video n&euml; kanalin e tyre n&euml; YouTube dhe dyqanet e tyre digjitale.
                    </p>
                </div>

                <div class="row">
                    <p class="fw-bold">ARTICLE 9 – DURATION OF THE CONTRACT</p>
                    <p class="fw-bold">NENI 9 – KOHËZGJATJA E KONTRATËS</p>
                    <p class="fw-bold">Eng. :</p>
                    <p><b>9.1.</b> The Cooperation Agreement shall be valid for a period of <?php echo $row['kohezgjatja'] ?> months from the day the contract is signed and shall be automatically renewed for subsequent <?php echo $row['kohezgjatja'] ?>-month periods unless otherwise terminated. Either the Artist (Copyright Owner) or Baresha Music SH.P.K (Copyright User) may terminate this Cooperation Agreement by providing written notice of termination at least 90 days prior to the end of each term. In the event of termination, the Agreement shall be deemed to have ended on the date on which it would have naturally expired. This Agreement may not be terminated prior to the end of any term, except in cases where the Artist provides written notice of termination with a valid reason via email, at least 3 months in advance.</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p><b>9.1.</b> Marr&euml;veshja p&euml;r bashk&euml;punim do t&euml; jet&euml; e vlefshme p&euml;r nj&euml; periudh&euml; prej <?php echo $row['kohezgjatja'] ?> muajsh nga data e n&euml;nshkrimit dhe do t&euml; rinovohet automatikisht p&euml;r periudha t&euml; m&euml;tejshme prej <?php echo $row['kohezgjatja'] ?> muajsh, p&euml;rveç rasteve t&euml; nd&euml;rprerjes. As artisti (pronari i t&euml; drejtave t&euml; kopjimit) dhe as Baresha Music SH.P.K (p&euml;rdoruesi i t&euml; drejtave t&euml; kopjimit) mund t&euml; nd&euml;rprejn&euml; k&euml;t&euml; marr&euml;veshje p&euml;r bashk&euml;punim duke siguruar njoftim me shkrim t&euml; nd&euml;rprerjes s&euml; saj t&euml; pakt&euml;n 90 dit&euml; para p&euml;rfundimit t&euml; secil&euml;s periudh&euml;. N&euml; rast t&euml; nd&euml;rprerjes, marr&euml;veshja do t&euml; konsiderohet se ka p&euml;rfunduar n&euml; dat&euml;n n&euml; t&euml; cil&euml;n ajo do t&euml; kishte p&euml;rfunduar natyrsh&euml;m. Kjo marr&euml;veshje nuk mund t&euml; nd&euml;rpritet p&euml;rpara p&euml;rfundimit t&euml; çdo periudhe, p&euml;rveç rasteve kur artisti jep njoftim me shkrim t&euml; nd&euml;rprerjes s&euml; saj me arsyetim t&euml; vlefsh&euml;m n&euml;p&euml;rmjet email-it, t&euml; pakt&euml;n 3 muaj p&euml;rpara p&euml;rfundimit t&euml; marr&euml;veshjes.</p>

                    <p class="fw-bold">Eng. :</p>
                    <p>The undersigned parties hereby declare that they are entering into this contract voluntarily, without any form of coercion, misrepresentation or deceit. By affixing their signatures to this document, the parties affirm that they have thoroughly read and understood the contents of this contract, and have no objections to the terms and conditions stated herein.</p>
                    <p class="fw-bold">Shqip. :</p>
                    <p>Pal&euml;t n&euml;nshkruese d&euml;shmojn&euml; se po n&euml;nshkruajn&euml; k&euml;t&euml; kontrat&euml; me vullnet t&euml; lir&euml;, pa ndonj&euml; form&euml; prekjeje, mashtrimi ose g&euml;njeshtr&euml;. Duke n&euml;nshkruar k&euml;t&euml; kontrat&euml;, pal&euml;t d&euml;shmojn&euml; se kan&euml; lexuar me kujdes kontrat&euml;n dhe nuk kan&euml; asnj&euml; objeksion n&euml;n kushtet dhe parimet e n&euml; t&euml; shkruar.</p>
                    <p class="fw-bold">Eng. :</p>
                    <p class="fw-bold">Shqip. :</p>

                    <p>This Contract is Signed on – Kjo kontrat&euml; n&euml;shkruhet m&euml; - : <?php echo date('d/m/Y'); ?></p>


                    <p> Baresha Music SH.P.K – P&euml;r Baresha Music SH.PK Artist - Artisti </p>
                    <div class="row">
                        <div class="col">
                            <p class="fw-bold">AFRIM KOLGECI</p>
                        </div>
                        <div class="col text-end">
                            <p class="fw-bold"><?php echo $row['emri'] ?> <?php echo $row['mbiemri'] ?></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <p class="fw-bold text-start"> Nenshkrimi <br> </p>
                        </div>
                        <div class="col-6">
                            <p class="fw-bold text-end"> Nenshkrimi <br> </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4">
                            <p class="border-bottom float-start w-50 text-start">
                                <img src="signatures/34.png" style="width: 150px; height: auto;">
                            </p>
                        </div>
                        <div class="col-4 text-center">
                            <img src="images/vula.png" style="width: 150px; height: auto;margin-top:-45px">
                        </div>
                        <div class="col-4">
                            <p class="border-bottom float-end w-50 text-end"> <?php $file_path = $row['nenshkrimi'];
                                                                                echo '<img src="' . $file_path . '" style="width: 150px; height: auto;">'; ?> </p>
                        </div>
                    </div>




                    <hr>
                    <div class="row mt-5">
                        <?php if (!($row['shenim'] == " ")) { ?>
                            <div class="my-5 border rounded-5 py-3">
                                <h6>Shenime</h6>
                                <?php echo $row['shenim'] ?>
                            </div>
                        <?php } ?>
                    </div>


                </div>



            </div>
        </div>

        </div>

    <?php } ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script><!-- MDB -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.js"></script>
</body>

</html>