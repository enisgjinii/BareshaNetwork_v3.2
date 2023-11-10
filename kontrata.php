<?php
include 'conn-d.php';
session_start();
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $m = $conn->query("SELECT * FROM klientet WHERE id='$id'");
    $m2 = mysqli_fetch_array($m);
}
?>
<html>

<head><!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" /> -->
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.1.0/mdb.min.css" rel="stylesheet" />
    <title>Kontrata -
        <?php echo $id; ?>
    </title>
    <style>
        @media print {
            #print-button {
                display: none;
            }

            #clear-button {
                display: none;
            }

            #submit-button {
                display: none;
            }
        }

        .page-break {
            display: block;
            break-before: page;
        }

        .signature-pad {
            border: 1px solid #c3c3c3;
        }
    </style>
    <!-- <style>
        body {
            font-family: arial;
            font-size: 12px;

        }

        .clearfix {
            clear: both;
        }

        table.gridtable th {
            padding: 5px;
        }

        table.gridtable td {
            padding: 5px;
        }

        #page-container {
            position: relative;
            min-height: 100vh;
        }

        #content-wrap {
            padding-bottom: 2.5rem;
            /* Footer height */
        }

        #footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            text-align: center;
            color: black;
        }

        #footer p {
            padding: 2px;
        }

        #footer hr {
            color: black;
        }

        @media all {
            .page-break {
                display: none;
            }
        }

        @media print {
            .page-break {
                display: block;
                page-break-before: always;
            }
        }
    </style> -->
    <script language="javascript" type="0bce25757d6dd3c012c757df-text/javascript">
        function Clickheretoprint() {
            var disp_setting = "toolbar=yes,location=no,directories=yes,menubar=yes,";
            disp_setting += "scrollbars=yes,width=1000, height=500, left=100, top=25";
            var content_vlue = document.getElementById("content").innerHTML;

            var docprint = window.open("", "", disp_setting);
            docprint.document.open();
            docprint.document.write('</head><body onLoad="self.print()" style="width: 1000px; font-size:11px; font-family:arial; font-weight:normal;">');
            docprint.document.write(content_vlue);
            docprint.document.close();
            docprint.focus();
        }
    </script>
</head>

<body>
    <div id="page-container">
        <div id="content-wrap"></div>
        <div class="content" id="content" style="width: 95%; margin: 10px auto;">
            <button id="print-button" class="btn btn-light shadow-2 border border-1 mb-3">Print</button>
            <script>
                const printButton = document.getElementById('print-button');
                printButton.addEventListener('click', () => {
                    window.print();
                });
            </script>

            <div class="card border border-1 p-3 shadow-2">
                <div class="row">
                    <div class="col">
                        <img src="images/brand-icon.png" width="150">
                    </div>
                    <div class="col text-end">
                        <p>Baresha Music SH.P.K</p>
                        <p>Shirok&euml; - Suharek&euml; | 23000, Rr.Ilirida</p>
                        <p>Numri i telefonit: +383 49 605 655 </p>
                        <p>Adresa email-it: info@bareshamusic.com</p>
                    </div>
                </div>
            </div>
            <hr>
            <div class="card border border-1 p-3 shadow-2">
                <div class="row">
                    <div class="col text-center">
                        <h3>Kontrat&euml; ne mes te artistit dhe Baresha Music</h3>
                        <p><i> Marr&euml;veshja mes artistit dhe Baresha Music</i></p>
                    </div>
                </div>
                <div class="row gap-2">
                    <div class="col border rounded p-3 m-3">
                        <p><b>Emri & Mbiemri <small>(Name & Last Name)</small></b> :
                            <?php echo $m2['emri']; ?>
                        </p>
                        <p><b>Emri Artistik <small>(Artistic name)</small></b> :
                            <?php echo $m2['emriart']; ?>
                        </p>
                        <p><b>ID Dokumentit <small>(Document ID)</small></b> :
                            <?php echo $m2['np']; ?>
                        </p>
                        <p><b>Adresa <small>(Address)</small></b> :
                            <?php echo $m2['adresa']; ?>
                        </p>
                        <p><b>Numri telefonit <small>(Phone number)</small></b> :
                            <?php echo $m2['nrtel']; ?>
                        </p>
                        <p><b>Adresa e email-it <small>(Email address)</small></b> :
                            <?php echo $m2['emailadd']; ?>
                        </p>
                    </div>
                    <div class="col border rounded p-3 m-3">
                        <p><b>Data e kontrat&euml;s :</b>
                            <?php echo $m2['dk']; ?>
                        </p>
                        <p><b>Data e mbarimit :</b>
                            <?php echo $m2['dks']; ?>
                        </p>
                    </div>
                </div>
            </div>







            <!-- <div style="float: left;width: 520px;margin-right: 30p; padding:5px;">
                    <span style="display: inline-block;width: 150px;text-align: left;padding-right: 20px;margin-bottom: 10px;font-weight: bold;width: 75px;"><br>
                        <span style="display: inline-block;width: 150px;text-align: left;padding-right: 20px;margin-bottom: 10px;font-weight: bold;width: 75px;">Emri Artistik <small>(Artistic name)</small>: </span> &nbsp;&nbsp;&nbsp; <?php echo $m2['emriart']; ?><br>
                        <span style="display: inline-block;width: 150px;text-align: left;padding-right: 20px;margin-bottom: 10px;font-weight: bold;width: 75px;">ID Dokumentit <small>(Document ID)</small>:</span>&nbsp;&nbsp;&nbsp;<?php echo $m2['np']; ?><br>
                        <span style="display: inline-block;width: 150px;text-align: left;padding-right: 20px;margin-bottom: 10px;font-weight: bold;width: 75px;">Adresa <small>(Address)</small>:</span> &nbsp;&nbsp;&nbsp; <?php echo $m2['adresa']; ?><br>
                        <span style="display: inline-block;width: 150px;text-align: left;padding-right: 20px;margin-bottom: 10px;font-weight: bold;width: 75px;">Numri telefonit <small>(Phone number)</small>: </span> &nbsp;&nbsp;&nbsp; <?php echo $m2['nrtel']; ?> <br>
                        <span style="display: inline-block;width: 150px;text-align: left;padding-right: 20px;margin-bottom: 10px;font-weight: bold;width: 75px;">Email: </span> &nbsp;&nbsp;&nbsp; <?php echo $m2['emailadd']; ?> <br>

                </div> -->
            <!-- <div style="float: right;width: 300px; margin-bottom: 20px; padding:5px;">
                    <span style="font-weight: bold; text-align: left; margin-bottom: 10px; display: inline-block;">Data e kontrates:</span><span style="float: right;"><?php echo $m2['dk']; ?></span><br>
                    <span style="font-weight: bold; text-align: left; margin-bottom: 10px; display: inline-block;">Data e mbarimit:</span><span style="float: right;"><?php echo $m2['dks']; ?></span><br>
                    <br>
                    <br>
                </div>
                <div class="clearfix"></div>
                ne tekstin e m&euml;tejm&euml; quhet n&euml; p&euml;rgjith&euml;si si
                <br>
                <i>
                    in the below text in general is known as
                </i>
                <br><br><br><br><br> -->
            <br>
            <div class="card border border-1 p-3 shadow-2">
                <div class="row">
                    <div class="col text-center">
                        <h5>„Artist- Performues“ me te drejta komerciale dhe Baresha Music si “P&euml;rfaq&euml;sues i
                            plotfuqish&euml;m i te drejtave komerciale”. </h5>
                        <p><i> Performer with the commercial rights and Baresha Music as the legal representative of
                                the commercial rights.
                            </i></p>
                    </div>
                </div>
            </div>
            <hr class="page-break">
            <br>





            <div class="card border border-1 p-3 shadow-2">
                <div class="row">
                    <div class="col">
                        <img src="images/brand-icon.png" width="150">
                    </div>
                    <div class="col text-end">
                        <p>Baresha Music SH.P.K</p>
                        <p>Shirok&euml; - Suharek&euml; | 23000, Rr.Ilirida</p>
                        <p>Numri i telefonit: +383 49 605 655 </p>
                        <p>Adresa email-it: info@bareshamusic.com</p>
                    </div>
                </div>
            </div>

            <br>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Pikat (Points)</th>
                        <th>Shqip (Albanian Language)</th>
                        <th>Anglish (English Language)</th>
                    </tr>


                </thead>
                <tr>
                    <td><b>1.</b></td>
                    <td>Objekti i Kontrat&euml;s</td>
                    <td>The object of the contract</td>
                </tr>
                <tr>
                    <td><b>1.1</b></td>
                    <td>Vepra e realizuar audio - vizuale te cilat publikohen ne te gjitha platformat e internetit
                        nga Baresha Music e nj&euml;jta i p&euml;rdor&euml; p&euml;r fitime komerciale.</td>
                    <td>Audio and visual work which will be published on the all integrated platforms of the
                        internet from Baresha Music, the same will be used for the commercial profits.</td>
                </tr>
                <tr>
                    <td><b>1.2</b></td>
                    <td>Kanali Youtube</td>
                    <td>Youtube Channel</td>
                </tr>
                <tr>
                    <td><b>1.3</b></td>
                    <td>Baresha Music i merr nga Artisti-Pronari te gjitha te drejtat p&euml;r p&euml;rfaq&euml;simin e veprave te
                        komercializuara ne te gjitha platformat e internetit.</td>
                    <td>Baresha Music takes from the Owner all the rights for representation of the commercialised
                        works in all platforms of the internet.</td>
                </tr>
                <tr>
                    <td><b>1.4</b></td>
                    <td>Baresha Music detyrohet qe veprat me te drejt&euml;n e transferuar p&euml;r p&euml;rfaq&euml;sim ti mbroj nga
                        p&euml;rdoruesit tjer&euml; te paautorizuar si publikimet ne formatet e ndryshme ne internet.</td>
                    <td>Baresha Music is obligated that works with the transfered right for the represent to cover
                        from the other unauthorised user such as publications in other formats in internet.</td>
                </tr>
                <tr>
                    <td><b>1.5</b></td>
                    <td>Artisti – Pronari garanton se veprat e transferuara p&euml;r p&euml;rfaq&euml;sim nuk jan&euml; dh&euml;n&euml; pal&euml;s se
                        tret&euml; ne te nj&euml;jt&euml;n koh&euml; po ashtu nuk ka ndonj&euml; kontrat&euml; te vlefshme me te, nj&euml;herit me
                        n&euml;nshkrimin e Artistit – Pronarit garantohet qe nga krijuesit si kompozitoret,
                        tekstshkruesit dhe studiot regjistruese jan&euml; marr p&euml;lqimet p&euml;r p&euml;rdorimin e se drejt&euml;s
                        komerciale.</td>
                    <td>Owner guarantees that the transfered work for the represent are not given to the third
                        parties in the same time, also he hasn’t any valid contract with the third parties.
                        Meanwhile the Owner guarantiees that the composer, lyricswriter and the records studio has
                        been granted the permissions for the commercial rights.</td>
                </tr>
                <tr>
                    <td><b>1.6</b></td>
                    <td>
                        Pjesa e fitimit te Artistit-Pronarit prej <b>
                            <?php echo 100 - $m2['perqindja']; ?>%
                        </b> do te i transferohet ne llogarin&euml; e saj/tij komerciale dhe Artisti-Pronari &euml;sht&euml;
                        obligues p&euml;r barazime tatimore ne shtetin ku jeton.
                    </td>
                    <td>The income portion of the Owner from <b>
                            <?php echo 100 - $m2['perqindja']; ?>%
                        </b> will be transfer on his commercial Bank Account and the Owner is obligated to pay the
                        income taxes of the Country where he/she lives.</td>
                </tr>
            </table>
            <hr class="page-break">
            <br>









            <!-- <b>1. Objekti i Kontrat&euml;s </b>
                <br>
                <i>The object of the contract </i>

                <br><br>

                <b>1.1 </b>
                <br>
                Vepra e realizuar audio - vizuale te cilat publikohen ne te gjitha platformat e internetit nga Baresha Music e nj&euml;jta i p&euml;rdor&euml; p&euml;r fitime komerciale.
                <br>
                <i>
                    Audio and visual work which will be published on the all integrated platforms of the internet from Baresha Music, the same will be used for the commercial profits.
                </i>
                <br><br>
                <b>
                    1.2
                </b>
                <br>
                <b>
                    Youtube_channel:


                    <br><br>
                    1.3
                </b>
                <br>
                Baresha Music i merr nga Artisti-Pronari te gjitha te drejtat p&euml;r p&euml;rfaq&euml;simin e veprave te komercializuara ne te gjitha platformat e internetit.
                <br><i><br>
                    Baresha Music takes from the Owner all the rights for representation of the commercialised works in all platforms of the internet.

                </i><br><br>
                <b>
                    1.4
                </b><br>

                Baresha Music detyrohet qe veprat me te drejt&euml;n e transferuar p&euml;r p&euml;rfaq&euml;sim ti mbroj nga p&euml;rdoruesit tjer&euml; te paautorizuar si publikimet ne formatet e ndryshme ne internet.
                <br><i><br>
                    Baresha Music is obligated that works with the transfered right for the represent to cover from the other unauthorised user such as publications in other formats in internet.
                </i>

                <br><b><br>
                    1.5
                </b><br>

                Artisti – Pronari garanton se veprat e transferuara p&euml;r p&euml;rfaq&euml;sim nuk jan&euml; dh&euml;n&euml; pal&euml;s se tret&euml; ne te nj&euml;jt&euml;n koh&euml; po ashtu nuk ka ndonj&euml; kontrat&euml; te vlefshme me te, nj&euml;herit me n&euml;nshkrimin e Artistit – Pronarit garantohet qe nga krijuesit si kompozitoret, tekstshkruesit dhe studiot regjistruese jan&euml; marr p&euml;lqimet p&euml;r p&euml;rdorimin e se drejt&euml;s komerciale.
                <br>
                <i>
                    Owner guarantees that the transfered work for the represent are not given to the third parties in the same time, also he hasn’t any valid contract with the third parties. Meanwhile the Owner guarantiees that the composer, lyricswriter and the records studio has been granted the permissions for the commercial rights.
                </i><br></br> -->

            <!-- <b>
                    1.6
                </b>
                <br>
                Pjesa e fitimit te Artistit-Pronarit prej___<u><?php echo 100 - $m2['perqindja']; ?></u>__% do te i transferohet ne llogarin&euml; e saj/tij komerciale dhe Artisti-Pronari &euml;sht&euml; obligues p&euml;r barazime tatimore ne shtetin ku jeton.
                <i><br>
                    The income portion of the Owner from __<?php echo 100 - $m2['perqindja']; ?></u>___% will be transfer on his commercial Bank Account and the Owner is obligated to pay the income taxes of the Country where he/she lives.
                </i> -->
            <div class="card border border-1 p-3 shadow-2">
                <div class="row">
                    <div class="col">
                        <img src="images/brand-icon.png" width="150">
                    </div>
                    <div class="col text-end">
                        <p>Baresha Music SH.P.K</p>
                        <p>Shirok&euml; - Suharek&euml; | 23000, Rr.Ilirida</p>
                        <p>Numri i telefonit: +383 49 605 655 </p>
                        <p>Adresa email-it: info@bareshamusic.com</p>
                    </div>
                </div>
            </div>

            <br>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Pikat (Points)</th>
                        <th>Shqip (Albanian Language)</th>
                        <th>Anglish (English Language)</th>
                    </tr>


                </thead>

                <tr>
                    <td><b>2.</b></td>
                    <td>P&euml;rmbledhja ligjore</td>
                    <td>The legal content
                    </td>
                </tr>
                <tr>
                    <td><b>2.1</b></td>
                    <td>Artisti-Pronari e transferon te drejt&euml;n e plot&euml; komerciale gjat&euml; gjith&euml; koh&euml;s sa &euml;sht&euml;
                        kontrata ne fuqi per veprat e cekura ne aneksin 1.2 te kontrates.</td>
                    <td>The Owner is transfered the whole commercial right during the all the time as the contract
                        is valid for the works described in the annex 1.2 of the contract.</td>
                </tr>

                <tr>
                    <td><b>2.2</b></td>
                    <td>Pal&euml;t jan&euml; te vet&euml;dijsh&euml;m qe ne p&euml;rdorimin online te k&euml;saj krijimtarie, e nj&euml;jta mund te
                        shikohet ne te gjith&euml; boten, p&euml;rpos n&euml;se artisti vendos qe mos ta shfaq&euml; ne territore te
                        caktuara.</td>
                    <td>Parties are aware that using online of this work, the same can be viewed in the whole world,
                        except if the Owner decide his/her not to be shown in particualr countries.</td>
                </tr>
                <tr>
                    <td><b>2.3</b></td>
                    <td>Artisti pajtohet qe gjat&euml; shfaqjeve online n&euml; te gjitha platformat e internetit do te
                        vendosen materiale propaganduese / reklamuese pa dallim gjuh&euml; apo figure (p&euml;rpos materiale
                        te cilat p&euml;rmbajn&euml; skena urrejtje, dhune, raciste etj.).</td>
                    <td>The Owner agrees that during online tune in all platforms of internet will be shown other
                        commercial/propagandistic content, no matter what language(except if materials containing
                        hate sceenes, racism or violence sceenes, etc).</td>
                </tr>
                <tr>
                    <td><b>3.1</b></td>
                    <td>
                        <?php
                        $df = $m2['dk'];
                        $ds = $m2['dks'];
                        $start = new DateTime('' . $df . ' 00:00:00');
                        $end = new DateTime('' . $ds . ' 00:00:00');
                        $diff = $start->diff($end);

                        $yearsInMonths = $diff->format('%r%y') * 12;
                        $months = $diff->format('%r%m');
                        $totalMonths = $yearsInMonths + $months;

                        ?>
                        Koh&euml;zgjatja e k&euml;saj kontrate &euml;sht&euml;
                        <?php echo $totalMonths; ?> muaj nga data e n&euml;nshkrimit nga te dy pal&euml;t. Kontrata zgjatet ne
                        m&euml;nyr&euml; automatike.
                    </td>
                    <td>Duration of this contract is set for
                        <?php echo $totalMonths; ?> Months from the signing date from the both parties. The contract
                        will be extended automaticlly.
                    </td>
                </tr>
                <tr>
                    <td><b>3.2</b></td>
                    <td>
                        Shk&euml;putja e kontrat&euml;s mund te behet me pajtimin e pal&euml;ve duke e njoftuar Baresha Music 1
                        muaj para skadimit te kontrat&euml;s.

                    </td>
                    <td>The termination of the contract can be done with agreement of both parties informing Baresha
                        Music one Month in advance from the date of the contract expire.
                    </td>
                </tr>
                <tr>
                    <td><b>3.3</b></td>
                    <td>
                        Baresha Music ka te drejt ta shk&euml;put kontrat&euml;n nj&euml;ansh&euml;m ne rast se Artisti-P&euml;rformuesi i
                        shkel marr&euml;veshjet e p&euml;rmendura dhe me k&euml;t&euml; Baresha Music ka te drejte ti k&euml;rkoj
                        d&euml;mshp&euml;rblim.
                    </td>
                    <td>Baresha Music has the right to terminate the contract unilateral in case the performer
                        violates the agreements set and based on this the Baresha Music has the right to request the
                        compensation from the performer.
                    </td>
                </tr>
                <tr>
                    <td><b>3.4</b></td>
                    <td>
                        Artistit-P&euml;rformuesit ka te drejt shk&euml;putjen e nj&euml;anshme me shkrim ne rast se Baresha Music
                        nuk e b&euml;n&euml; pjes&euml;n e pages&euml;s te cekur ne 1.5 brenda afatit 1 mujor ne muajin pasues.

                    </td>
                    <td>Performer has the right to terminate the contract unilateraly on writen form in case Baresha
                        Music does not accomplish the payments mentioned in 1.5 section within one month following
                        next month.
                    </td>
                </tr>

            </table>
            <hr class="page-break">
            <br>



            <!-- <b> 2. P&euml;rmbledhja ligjore </b><br>
                <i>The legal content</i><br><br>
                <b>2.1</b>
                <br><br>
                Artisti-Pronari e transferon te drejt&euml;n e plot&euml; komerciale gjat&euml; gjith&euml; koh&euml;s sa &euml;sht&euml; kontrata ne fuqi per veprat e cekura ne aneksin 1.2 te kontrates.
                <br><i>
                    The Owner is transfered the whole commercial right during the all the time as the contract is valid for the works described in the annex 1.2 of the contract.
                </i>
                <br><br><br>
                <b>2.2 </b><br><br>

                Pal&euml;t jan&euml; te vet&euml;dijsh&euml;m qe ne p&euml;rdorimin online te k&euml;saj krijimtarie, e nj&euml;jta mund te shikohet ne te gjith&euml; boten, p&euml;rpos n&euml;se artisti vendos qe mos ta shfaq&euml; ne territore te caktuara.
                <br>
                <i>
                    Parties are aware that using online of this work, the same can be viewed in the whole world, except if the Owner decide his/her not to be shown in particualr countries.
                </i>
                <br><br><br>
                <b> 2.3 </b><br><br>

                Artisti pajtohet qe gjat&euml; shfaqjeve online n&euml; te gjitha platformat e internetit do te vendosen materiale propaganduese / reklamuese pa dallim gjuh&euml; apo figure (p&euml;rpos materiale te cilat p&euml;rmbajn&euml; skena urrejtje, dhune, raciste etj.).
                <br>
                <i>
                    The Owner agrees that during online tune in all platforms of internet will be shown other commercial/propagandistic content, no matter what language(except if materials containing hate sceenes, racism or violence sceenes, etc).
                </i>


                <br><br><br>
                <b> Koh&euml;zgjatja e kontrat&euml;s </b><br>
                <i>
                    Duration of Contract </i><br><br>
                <b>3.1 </b><br><br> -->


            <!-- <?php
            $df = $m2['dk'];
            $ds = $m2['dks'];
            $start = new DateTime('' . $df . ' 00:00:00');
            $end = new DateTime('' . $ds . ' 00:00:00');
            $diff = $start->diff($end);

            $yearsInMonths = $diff->format('%r%y') * 12;
            $months = $diff->format('%r%m');
            $totalMonths = $yearsInMonths + $months;

            ?>
                Koh&euml;zgjatja e k&euml;saj kontrate &euml;sht&euml; <?php echo $totalMonths; ?> muaj nga data e n&euml;nshkrimit nga te dy pal&euml;t. Kontrata zgjatet ne m&euml;nyr&euml; automatike. <br>
                <i>
                    Duration of this contract is set for <?php echo $totalMonths; ?> Months from the signing date from the both parties. The contract will be extended automaticlly.
                </i>
                <br>
                <b>3.2 </b><br><br>



                Shk&euml;putja e kontrat&euml;s mund te behet me pajtimin e pal&euml;ve duke e njoftuar Baresha Music

                1 muaj para skadimit te kontrat&euml;s.
                <br>
                <i>
                    The termination of the contract can be done with agreement of both parties informing Baresha Music one Month in advance from the date of the contract expire.
                </i>
                <br>
                <b> 3.3 </b><br><br>

                Baresha Music ka te drejt ta shk&euml;put kontrat&euml;n nj&euml;ansh&euml;m ne rast se Artisti-P&euml;rformuesi i shkel marr&euml;veshjet e p&euml;rmendura dhe me k&euml;t&euml; Baresha Music ka te drejte ti k&euml;rkoj d&euml;mshp&euml;rblim.
                <br><i>
                    Baresha Music has the right to terminate the contract unilateral in case the performer violates the agreements set and based on this the Baresha Music has the right to request the compensation from the performer.
                </i><br>
                <b> 3.4</b><br><br>



                Artistit-P&euml;rformuesit ka te drejt shk&euml;putjen e nj&euml;anshme me shkrim ne rast se Baresha Music nuk e b&euml;n&euml; pjes&euml;n e pages&euml;s te cekur ne 1.5 brenda afatit 1 mujor ne muajin pasues.
                <br><i>
                    Performer has the right to terminate the contract unilateraly on writen form in case Baresha Music does not accomplish the payments mentioned in 1.5 section within one month following next month.
                </i>

                <div class="page-break"></div> -->
            <br>
            <div class="card border border-1 p-3 shadow-2">
                <div class="row">
                    <div class="col">
                        <img src="images/brand-icon.png" width="150">
                    </div>
                    <div class="col text-end">
                        <p>Baresha Music SH.P.K</p>
                        <p>Shirok&euml; - Suharek&euml; | 23000, Rr.Ilirida</p>
                        <p>Numri i telefonit: +383 49 605 655 </p>
                        <p>Adresa email-it: info@bareshamusic.com</p>
                    </div>
                </div>
            </div>

            <br>
            <table class="table table-bordered">
                <thead>
                    <tr>

                        <th>Shqip (Albanian Language)</th>
                        <th>Anglish (English Language)</th>
                    </tr>
                </thead>
                <tr>
                    <td>Mbrojtja e te dh&euml;nave</td>
                    <td>Data protection</td>
                </tr>
                <tr>
                    <td>Performuesi pranon q&euml; t&euml; dh&euml;nat e tij personale si emri, mbiemri, adresa, numri i telefonit,
                        emaili, mund t&euml; p&euml;rdoren p&euml;r arsye regjistrimi n&euml; regjistrimet e skedar&euml;ve nga Baresha
                        Music, nd&euml;rsa emri artistik mund t&euml; publikohet n&euml; t&euml; gjitha platformat e internetit.</td>
                    <td>Performer agrees that his/her personal data such as name, surname, address, phone number,
                        email, can be used for registration reason to the file records from the Baresha Music,
                        whereas the artistic name can be published in all platforms of the internet.</td>
                </tr>
                <tr>
                    <td>Pal&euml;t obligohet qe te p&euml;rdorin kodin e sekretit mbi marr&euml;veshjen ne k&euml;t&euml; kontrate.</td>
                    <td>Parties are obligated to use the secret over the agreement in this contract.</td>
                </tr>

                <tr>
                    <td>Marr&euml;veshjet plot&euml;suese ne mes te pal&euml;ve mund te beh&euml;n vet&euml;m me shkrim.</td>
                    <td>The additional agreements between the parties can be done only in writen form.</td>
                </tr>
            </table>
            <br>
            <!-- <table style="width:100%">
                <tr>
                    <td class="text-left"><b>(Baresha Music SH.P.K)<br>
                            <i>Owner / Pronari</i></b><br>
                        Afrim Kolgeci
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right;"><b>Artisti / Pronari<br>
                            <i>Artist / Owner</i></b><br>
                        <?php echo $m2['emri']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="text-left">______________</td>
                    <td></td>
                    <td class="text-center">V.V</td>
                    <td></td>
                    <td style="float: right;"><u><img src="signatures/<?php echo $id; ?>.png" width="250px"
                                style="float: right;"></u></td>
                </tr>
            </table> -->
            <hr class="page-break">
            <form method="post" action="submit.php">
                <div class="row">
                    <div class="col">
                        <p>(Baresha Music SH.P.K)</p>
                        <p> Owner / Pronari</p>
                        <p> Afrim Kolgeci</p>


                        <label for="signature">Signature:</label>
                        <div class="signature-pad">
                            <canvas id="signature-canvas" width="300" height="200"></canvas>
                        </div>
                        <br>
                        <button class="btn btn-light shadow-2 border border-1" type="button"
                            id="clear-button">Clear</button>
                    </div>

                    <div class="col">
                        <p>Artisti / Pronari</p>
                        <p>Artist / Owner</p>
                        <p>
                            <?php echo $m2['emri']; ?>
                        </p>
                        <label for="signature">Signature:</label>
                        <div class="signature-pad">
                            <canvas id="signature-canvas2" width="300" height="200"></canvas>
                        </div>
                        <br>
                        <button class="btn btn-light shadow-2 border border-1" type="button"
                            id="clear-button2">Clear</button>
                    </div>

                </div>

                <input type="hidden" id="signature-input" name="signature">
                <br>
                <button class="btn btn-light shadow-1 border border-1" type="submit" id="submit-button">Submit</button>
            </form>

            <script src="https://unpkg.com/signature_pad"></script>
            <script>
                const canvas = document.getElementById('signature-canvas');
                const canvas2 = document.getElementById('signature-canvas2');
                const signaturePad = new SignaturePad(canvas);
                const signaturePad2 = new SignaturePad(canvas2);
                const clearButton = document.getElementById('clear-button');
                const clearButton2 = document.getElementById('clear-button2');

                clearButton.addEventListener('click', () => {
                    signaturePad.clear();
                });
                clearButton2.addEventListener('click', () => {
                    signaturePad2.clear();
                });

                const form = document.querySelector('form');
                form.addEventListener('submit', (event) => {
                    const signatureInput = document.getElementById('signature-input');
                    signatureInput.value = signaturePad.toDataURL();
                });
            </script>

        </div>
    </div>
    <style>
        div.columns {
            width: 870px;
        }

        div.columns div {
            width: 270px;
            height: 100px;
            float: left;
        }

        div.clear {
            clear: both;
        }
    </style>
    <footer id="footer">

        <div class="clear"></div>
    </footer>
    <!-- MDB -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.1.0/mdb.min.js"></script>

</html>