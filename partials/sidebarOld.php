<nav class="sidebar sidebar-offcanvas">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link" href="index.php">
                <i class="fi fi-rr-home menu-icon pe-3"></i>
                <span class="menu-title">Sht&euml;pia</span>
            </a>
        </li>
        <?php if ($_SESSION['acc'] == '1') {
        ?>

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#menaxhimi" aria-expanded="false" aria-controls="menaxhimi">
                    <i class="fi fi-rr-users-gear menu-icon pe-3"></i>
                    <span class="menu-title">Menaxhimi</span>
                    <i class="menu-arrow pe-3"></i>
                </a>
                <div class="collapse" id="menaxhimi">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="stafi.php">
                                <i class="fi fi-rr-users-alt menu-icon pe-3"></i>
                                <span class="menu-title">Stafi</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="rolet.php">
                                <i class="fi fi-rr-user-gear menu-icon pe-3"></i>
                                <span class="menu-title">Rolet</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>



            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#klienti" aria-expanded="false" aria-controls="klienti">
                    <i class="fi fi-rr-handshake menu-icon pe-3"></i>
                    <span class="menu-title">Klient&euml;t</span>
                    <i class="menu-arrow pe-3"></i>
                </a>
                <div class="collapse" id="klienti">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="klient.php">Lista e klient&euml;ve</a></li>
                        <li class="nav-item"><a class="nav-link" href="kategorit.php">Lista e kategorive</a></li>
                        <li class="nav-item"><a class="nav-link" href="ads.php">Llogarit&euml; e ADS</a></li>
                        <li class="nav-item"><a class="nav-link" href="emails.php">Lista e email-ave</a></li>
                    </ul>
                </div>
            </li> <?php } ?>
        <?php if ($_SESSION['acc'] == '4' || $_SESSION['acc'] == '1') {

        ?>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#video" aria-expanded="false" aria-controls="video">
                    <i class="fi fi-rr-cloud-upload-alt menu-icon pe-3"></i>
                    <span class="menu-title">Videot / Ngarkimi</span>
                    <i class="menu-arrow pe-3"></i>
                </a>
                <div class="collapse" id="video">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="shtoy.php">Regjistro nj&euml; k&euml;ng&euml;</a></li>
                        <li class="nav-item"><a class="nav-link" href="listang.php">Lista e k&euml;ng&euml;ve</a></li>
                    </ul>
                </div>
            </li>
        <?php }
        ?>

        <?php if ($_SESSION['acc'] == '1') {

        ?>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#tiketat" aria-expanded="false" aria-controls="tiketat">
                    <i class="fi fi-rr-ticket menu-icon pe-3"></i>
                    <span class="menu-title">Tiketat</span>
                    <i class="menu-arrow pe-3"></i>
                </a>
                <div class="collapse" id="tiketat">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="tiketa.php">Tiket e re</a></li>
                        <li class="nav-item"><a class="nav-link" href="listat.php">Lista e tiketave</a></li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#content" aria-expanded="false" aria-controls="content">
                    <i class="fi fi-rr-photo-video menu-icon pe-3"></i>
                    <span class="menu-title">Content ID</span>
                    <i class="menu-arrow pe-3"></i>
                </a>
                <div class="collapse" id="content">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="claim.php">Recent Claim</a></li>
                        <li class="nav-item"><a class="nav-link" href="whitelist.php">Whitelist</a></li>
                    </ul>
                </div>
            </li>
        <?php
        }
        ?>
        <?php if ($_SESSION['acc'] == '3' || $_SESSION['acc'] == '1') { ?>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#financat" aria-expanded="false" aria-controls="financat">
                    <i class="fi fi-rr-chart-histogram menu-icon pe-3"></i>
                    <span class="menu-title">Financat</span>
                    <i class="menu-arrow  pe-3"></i>
                </a>
                <div class="collapse" id="financat">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="rrogat.php">Pagat</a></li>
                        <li class="nav-item"><a class="nav-link" href="tatimi.php">Tatimi</a></li>
                        <li class="nav-item"><a class="nav-link" href="yinc.php">Shpenzimet</a></li>
                        <li class="nav-item"><a class="nav-link" href="shpenzimep.php">Shpenzimet Personale</a></li>
                        <li class="nav-item"><a class="nav-link" href="faturat.php">Pagesat YouTube</a></li>
                        <li class="nav-item"><a class="nav-link" href="pagesat.php">Pagesat e kryera</a></li>
                        <li class="nav-item"><a class="nav-link" href="faturat2.php">Platformat Tjera</a></li>
                        <!-- <li class="nav-item"><a class="nav-link" href="csvFiles.php">Faturat Platforma</a></li> -->
                    </ul>
                </div>
            </li>
        <?php } ?>

        <?php if ($_SESSION['acc'] == '1') {

        ?>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#quicklyTool" aria-expanded="false" aria-controls="admin">
                    <i class="fi fi-rr-magic-wand menu-icon pe-3"></i>
                    <span class="menu-title">Vegla te shpejta</span>
                    <i class="menu-arrow pe-3"></i>
                </a>
                <div class="collapse" id="quicklyTool">
                    <ul class="nav flex-column sub-menu list-unstyled">
                        <li class="nav-item">
                            <a class="nav-link" href="filet.php">
                                <i class="fi fi-rr-folders menu-icon pe-3"></i>
                                <span class="menu-title">Dokumente Tjera</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="notes.php">
                                <i class="fi fi-rr-notes menu-icon pe-3"></i>
                                <span class="menu-title">Shenime</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="github_logs.php">
                                <i class="fi fi-rr-code-branch menu-icon pe-3"></i>
                                <span class="menu-title">Aktiviteti ne Github</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="todo_list.php">
                                <i class="fi fi-rr-checkbox menu-icon pe-3"></i>
                                <span class="menu-title">To do</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="takimet.php">
                                <i class="fi fi-rr-video-camera-alt menu-icon pe-3"></i>
                                <span class="menu-title">Takimet</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="klient_CSV.php">
                                <i class="fi fi-rr-file-chart-line menu-icon pe-3"></i>
                                <span class="menu-title">Klient CSV</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logs.php">
                                <i class="fi fi-rr-time-past menu-icon pe-3"></i>
                                <!-- <i class="fa-solid fa-clock-rotate-left menu-icon pe-3"></i> -->
                                <span class="menu-title">Logs</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>




            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#kontratat" aria-expanded="false" aria-controls="kontratat">
                    <i class="fi fi-rr-document-signed menu-icon pe-3"></i>
                    <span class="menu-title">Kontrata</span>
                    <i class="menu-arrow pe-3"></i>
                </a>
                <div class="collapse" id="kontratat">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="kontrata_2.php">Kontrate e re</a></li>
                        <li class="nav-item"><a class="nav-link" href="lista_kontratave.php">Lista e kontratave</a></li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="dataYT.php">
                    <i class="fi fi-rr-dashboard menu-icon pe-3"></i>
                    <span class="menu-title">Statistikat nga YouTube</span>
                </a>
            </li>



        <?php } ?>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#platformat" aria-expanded="false" aria-controls="financat">
                <i class="fi fi-rr-share menu-icon pe-3"></i>
                <span class="menu-title">Platformat</span>
                <i class="menu-arrow  pe-3"></i>
            </a>
            <div class="collapse" id="platformat">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="csvFiles.php"><i class="fi fi-rr-file-excel menu-icon"></i>Inserto CSV</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="filtroCSV.php"><i class="fi fi-rr-filter menu-icon"></i>Filtro CSV</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="listaEFaturaveTePlatformave.php"><i class="fi fi-rr-list-timeline menu-icon"></i>Lista e faturave</a>
                    </li>
                    <li class="nav-item">

                        <a class="nav-link" href="pagesatEKryera.php"><i class="fi fi-rr-memo-circle-check menu-icon"></i>Pagesat e perfunduara</a>
                    </li>
                </ul>
            </div>
        </li>





        <li class="nav-item">
            <a class="nav-link" href="check_musics.php">
                <i class="fi fi-rr-list-check menu-icon pe-3"></i>
                <span class="menu-title">Konfirmimi i kengeve</span>
            </a>
        </li>
    </ul>
</nav>