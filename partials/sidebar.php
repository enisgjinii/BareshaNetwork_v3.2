<nav class="sidebar sidebar-offcanvas">
  <ul class="nav">
    <?php

    // Include the database connection file
    include 'conn-d.php';

    // Check if the 'id' session variable is set
    if (isset($_SESSION['id'])) {
      // You can now use $_SESSION['id'] in this script
      $userName = $_SESSION['id'];
    } else {
      // The 'id' session variable is not set, handle the situation accordingly
      echo "User ID is not set in the session.";
    }
    // Enable error reporting (for debugging purposes)
    error_reporting(1);


    // Define the SQL query to retrieve user roles and associated pages
    $sql = "SELECT roles.name AS role_name, GROUP_CONCAT(DISTINCT role_pages.page) AS pages
        FROM roles
        LEFT JOIN user_roles ON roles.id = user_roles.role_id
        LEFT JOIN role_pages ON roles.id = role_pages.role_id
        WHERE user_roles.user_id = '$userName'
        GROUP BY roles.id";


    if ($result = $conn->query($sql)) {
      while ($row = $result->fetch_assoc()) {
        $menu_title = $row['role_name'] . ': ' . $row['user_name'];
        $menu_url = 'rolet.php?user_id=' . $row['role_name'];
        $menu_pages = explode(',', $row['pages']);

        // echo '<li class="nav-item">';
        // // echo '<a class="nav-link" href="' . $menu_url . '">';
        // echo '<i class="fi fi-rr-user-gear menu-icon pe-3"></i>';
        // echo '<span class="menu-title">' . $menu_title . '</span>';
        // echo '</a>';

        if (!empty($menu_pages)) {
          echo '<li class="nav-item">
                  <a class="nav-link" href="index.php">
                    <i class="fi fi-rr-home menu-icon pe-3"></i>
                    <span class="menu-title">Sht&euml;pia</span>
                  </a>
                </li>';
        }
      }
    }
    ?>

    <?php
    $i = 0;
    while ($i < count($menu_pages)) {
      if ($menu_pages[$i] == "lista_kopjeve_rezerve.php") {
        echo '<li class="nav-item">
                            <a class="nav-link " href="' . $menu_pages[$i] . '">
                            <i class="fi fi-rr-database menu-icon pe-3"></i>
                                <span class="menu-title">Lista e kopjeve rezerve</span>
                            </a>
                        </li>';
      }
      $i++;
    }
    ?>
    <?php
    $i = 0;
    while ($i < count($menu_pages)) {
      if ($menu_pages[$i] == "investime.php") {
        echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                            <i class="fi fi-rr-money-check-edit menu-icon pe-3"></i>
                                <span class="menu-title">Investime</span>
                            </a>
                        </li>';
      }
      $i++;
    }
    ?>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#menaxhimi" aria-expanded="false" aria-controls="menaxhimi">
        <i class="fi fi-rr-users-gear menu-icon pe-3"></i>
        <span class="menu-title">Menaxhimi</span>
        <i class="menu-arrow pe-3"></i>
      </a>
      <div class="collapse" id="menaxhimi">
        <ul class="nav flex-column sub-menu">
          <?php
          $i = 0;
          while ($i < count($menu_pages)) {
            if ($menu_pages[$i] == "stafi.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Stafi</span>
                            </a>
                        </li>';
            }
            if ($menu_pages[$i] == "roles.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Rolet</span>
                            </a> 
                        </li>';
            }
            $i++;
          }
          ?>
        </ul>
      </div>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#objekti" aria-expanded="false" aria-controls="objekti">
        <i class="fi fi-rr-chair-office menu-icon pe-3"></i>
        <span class="menu-title">Objekti</span>
        <i class="menu-arrow pe-3"></i>
      </a>
      <div class="collapse" id="objekti">
        <ul class="nav flex-column sub-menu">
          <?php
          $i = 0;
          while ($i < count($menu_pages)) {
            if ($menu_pages[$i] == "office_investments.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Investimet e objektit</span>
                            </a>
                        </li>';
            }
            if ($menu_pages[$i] == "office_damages.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Prishjet</span>
                            </a> 
                        </li>';
            }
            if ($menu_pages[$i] == "office_requirements.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Kerkesat</span>
                            </a> 
                        </li>';
            }
            $i++;
          }
          ?>
        </ul>
      </div>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#klienti" aria-expanded="false" aria-controls="klienti">
        <i class="fi fi-rr-handshake menu-icon pe-3"></i>
        <span class="menu-title">Klient&euml;t</span>
        <i class="menu-arrow pe-3"></i>
      </a>
      <div class="collapse" id="klienti">
        <ul class="nav flex-column sub-menu">
          <?php
          $i = 0;
          while ($i < count($menu_pages)) {
            if ($menu_pages[$i] == "klient.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Lista e klient&euml;ve</span>
                            </a>
                        </li>';
            }
            if ($menu_pages[$i] == "klient2.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Lista e klient&euml;ve tjer</span>
                            </a> 
                        </li>';
            }
            if ($menu_pages[$i] == "kategorit.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Lista e kategorive</span>
                            </a> 
                        </li>';
            }
            if ($menu_pages[$i] == "ads.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Llogarit&euml; e ADS</span>
                            </a> 
                        </li>';
            }
            if ($menu_pages[$i] == "emails.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Lista e email-ave</span>
                            </a> 
                        </li>';
            }
            if ($menu_pages[$i] == "klient-avanc.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Lista e avanceve te klienteve</span>
                            </a> 
                        </li>';
            }
            $i++;
          }
          ?>
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#video" aria-expanded="false" aria-controls="menaxhimi">
        <i class="fi fi-rr-cloud-upload-alt menu-icon pe-3"></i>
        <span class="menu-title">Videot / Ngarkimi</span>
        <i class="menu-arrow pe-3"></i>
      </a>
      <div class="collapse" id="video">
        <ul class="nav flex-column sub-menu">
          <?php
          $i = 0;
          while ($i < count($menu_pages)) {
            if ($menu_pages[$i] == "shtoy.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Regjistro nj&euml; k&euml;ng&euml;</span>
                            </a>
                        </li>';
            }
            if ($menu_pages[$i] == "listang.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Lista e k&euml;ng&euml;ve</span>
                            </a> 
                        </li>';
            }
            $i++;
          }
          ?>
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
          <?php
          $i = 0;
          while ($i < count($menu_pages)) {
            if ($menu_pages[$i] == "claim.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Recent Claim</span>
                            </a>
                        </li>';
            }
            if ($menu_pages[$i] == "whitelist.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Whitelist</span>
                            </a> 
                        </li>';
            }
            $i++;
          }
          ?>
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#financat" aria-expanded="false" aria-controls="financat">
        <i class="fi fi-rr-chart-histogram menu-icon pe-3"></i>
        <span class="menu-title">Financat</span>
        <i class="menu-arrow pe-3"></i>
      </a>
      <div class="collapse" id="financat">
        <ul class="nav flex-column sub-menu">
          <?php
          $i = 0;
          while ($i < count($menu_pages)) {
            if ($menu_pages[$i] == "rrogat.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Pagat</span>
                            </a>
                        </li>';
            }
            if ($menu_pages[$i] == "tatimi.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Tatimi</span>
                            </a> 
                        </li>';
            }
            if ($menu_pages[$i] == "yinc.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Shpenzimet</span>
                            </a> 
                        </li>';
            }
            if ($menu_pages[$i] == "shpenzimep.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Shpenzimet personale</span>
                            </a> 
                        </li>';
            }
            if ($menu_pages[$i] == "faturat.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Pagesat YouTube</span>
                            </a> 
                        </li>';
            }
            if ($menu_pages[$i] == "invoice.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Pagesat YouTube <span class="badge bg-success rounded">New</span>
                                </span>
                            </a> 
                        </li>';
            }




            if ($menu_pages[$i] == "pagesat_youtube.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Pagesat YouTube ( Faza Test )</span>
                            </a> 
                        </li>';
            }
            if ($menu_pages[$i] == "pagesat.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Pagesat e kryera</span>
                            </a> 
                        </li>';
            }
            if ($menu_pages[$i] == "faturat2.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Platformat Tjera</span>
                            </a> 
                        </li>';
            }
            $i++;
          }
          ?>
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#quicklyTool" aria-expanded="false" aria-controls="quicklyTool">
        <i class="fi fi-rr-magic-wand menu-icon pe-3"></i>
        <span class="menu-title">Vegla te shpejta</span>
        <i class="menu-arrow pe-3"></i>
      </a>
      <div class="collapse" id="quicklyTool">
        <ul class="nav flex-column sub-menu">
          <?php
          $i = 0;
          while ($i < count($menu_pages)) {
            if ($menu_pages[$i] == "filet.php") {
              echo '<li class="nav-item">
                    <a class="nav-link" href="' . $menu_pages[$i] . '">
                        <span class="menu-title">Dokumente tjera</span>
                    </a>
                </li>';
            } elseif ($menu_pages[$i] == "notes.php") {
              echo '<li class="nav-item">
                    <a class="nav-link" href="' . $menu_pages[$i] . '">
                        <span class="menu-title">Shenime</span>
                    </a> 
                </li>';
            } elseif ($menu_pages[$i] == "todo_list.php") {
              echo '<li class="nav-item">
                    <a class="nav-link" href="' . $menu_pages[$i] . '">
                        <span class="menu-title">To do</span>
                    </a> 
                </li>';
            } elseif ($menu_pages[$i] == "takimet.php") {
              echo '<li class="nav-item">
                    <a class="nav-link" href="' . $menu_pages[$i] . '">
                        <span class="menu-title">Takimet</span>
                    </a> 
                </li>';
            } elseif ($menu_pages[$i] == "klient_CSV.php") {
              echo '<li class="nav-item">
                    <a class="nav-link" href="' . $menu_pages[$i] . '">
                        <span class="menu-title">Klient CSV</span>
                    </a> 
                </li>';
            } elseif ($menu_pages[$i] == "logs.php") {
              echo '<li class="nav-item">
                    <a class="nav-link" href="' . $menu_pages[$i] . '">
                        <span class="menu-title">Logs</span>
                    </a> 
                </li>';
            } else {
              // Handle the case where $menu_pages[$i] doesn't match any condition
              // For example, display a default menu item or take other action
            }
            $i++;
          }
          ?>

        </ul>
      </div>
    </li>


    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#kontratat" aria-expanded="false" aria-controls="kontratat">
        <i class="fi fi-rr-document-signed menu-icon pe-3"></i>
        <span class="menu-title">Kontrata (Keng&euml;)</span>
        <i class="menu-arrow pe-3"></i>
      </a>
      <div class="collapse" id="kontratat">
        <ul class="nav flex-column sub-menu">
          <?php
          $i = 0;
          while ($i < count($menu_pages)) {
            if ($menu_pages[$i] == "kontrata_2.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Kontrate e re</span>
                            </a>
                        </li>';
            }
            if ($menu_pages[$i] == "lista_kontratave.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Lista e kontratave</span>
                            </a> 
                        </li>';
            }
            if ($menu_pages[$i] == "ofertat.php") {
              echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                                <span class="menu-title">Ofertat</span>
                            </a> 
                        </li>';
            }
            $i++;
          }
          ?>
        </ul>
      </div>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#kontratatGjenerale" aria-expanded="false" aria-controls="kontratatGjenerale">
        <i class="fi fi-rr-poll-h menu-icon pe-3"></i>
        <span class="menu-title">Kontrata (Gjenerale)</span>
        <i class="menu-arrow pe-3"></i>
      </a>
      <div class="collapse" id="kontratatGjenerale">
        <ul class="nav flex-column sub-menu">
          <?php

          $i = 0;
          while ($i < count($menu_pages)) {
            if ($menu_pages[$i] == "kontrata_gjenelare_2.php") {
              echo '<li class="nav-item">
                        <a class="nav-link" href="' . $menu_pages[$i] . '">
                            <span class="menu-title">Kontrata e re</span>
                        </a>
                    </li>';
            }
            if ($menu_pages[$i] == "lista_kontratave_gjenerale.php") {
              echo '<li class="nav-item">
                        <a class="nav-link" href="' . $menu_pages[$i] . '">
                            <span class="menu-title">Lista e kontratave</span>
                        </a> 
                    </li>';
            }

            $i++;
          }
          ?>
        </ul>
      </div>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#facebook" aria-expanded="false" aria-controls="facebook">
        <i class="fa-brands fa-facebook menu-icon pe-3"></i>
        <span class="menu-title">Facebook</span>
        <i class="menu-arrow pe-3"></i>
      </a>
      <div class="collapse" id="facebook">
        <ul class="nav flex-column sub-menu">
          <?php

          $i = 0;
          while ($i < count($menu_pages)) {
            if ($menu_pages[$i] == "facebook.php") {
              echo '<li class="nav-item">
                        <a class="nav-link" href="' . $menu_pages[$i] . '">
                            <span class="menu-title">Vegla Facebook</span>
                        </a>
                    </li>';
            }


            if ($menu_pages[$i] == "faturaFacebook.php") {
              echo '<li class="nav-item">
                        <a class="nav-link" href="' . $menu_pages[$i] . '">
                            <span class="menu-title">Krijo fatur&euml; (Facebook)</span>
                        </a> 
                    </li>';
            }

            if ($menu_pages[$i] == "lista_faturave_facebook.php") {
              echo '<li class="nav-item">
                        <a class="nav-link" href="' . $menu_pages[$i] . '">
                            <span class="menu-title">Lista e faturave (Facebook)</span>
                        </a> 
                    </li>';
            }
            $i++;
          }
          ?>
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#platformat2" aria-expanded="false" aria-controls="platformat2">
        <i class="fi fi-rr-share menu-icon pe-3"></i>
        <span class="menu-title">Platformat</span>
        <i class="menu-arrow pe-3"></i>
      </a>
      <div class="collapse" id="platformat2">
        <ul class="nav flex-column sub-menu">
          <?php

          $i = 0;
          while ($i < count($menu_pages)) {
            if ($menu_pages[$i] == "csvFiles.php") {
              echo '<li class="nav-item">
                        <a class="nav-link" href="' . $menu_pages[$i] . '">
                            <span class="menu-title">Inserto CSV</span>
                        </a>
                    </li>';
            }
            if ($menu_pages[$i] == "filtroCSV.php") {
              echo '<li class="nav-item">
                        <a class="nav-link" href="' . $menu_pages[$i] . '">
                            <span class="menu-title">Filtro CSV</span>
                        </a> 
                    </li>';
            }
            if ($menu_pages[$i] == "listaEFaturaveTePlatformave.php") {
              echo '<li class="nav-item">
                        <a class="nav-link" href="' . $menu_pages[$i] . '">
                            <span class="menu-title">Lista e faturave</span>
                        </a> 
                    </li>';
            }
            if ($menu_pages[$i] == "pagesatEKryera.php") {
              echo '<li class="nav-item">
                        <a class="nav-link" href="' . $menu_pages[$i] . '">
                            <span class="menu-title">Pagesat e perfunduara</span>
                        </a> 
                    </li>';
            }
            if ($menu_pages[$i] == "quick_platform_invoice.php") {
              echo '<li class="nav-item">
                        <a class="nav-link" href="' . $menu_pages[$i] . '">
                            <span class="menu-title">Raporte te platformave</span>
                        </a> 
                    </li>';
            }
            $i++;
          }
          ?>
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#youtube" aria-expanded="false" aria-controls="youtube">
        <i class="fa-brands fa-youtube menu-icon pe-3"></i>
        <span class="menu-title">Youtube Dashboard</span>
        <i class="menu-arrow pe-3"></i>
      </a>
      <div class="collapse" id="youtube">
        <ul class="nav flex-column sub-menu">
          <?php

          $i = 0;
          while ($i < count($menu_pages)) {
            if ($menu_pages[$i] == "youtube_studio.php") {
              echo '<li class="nav-item">
                        <a class="nav-link" href="' . $menu_pages[$i] . '">
                            <span class="menu-title">Baresha Analystic</span>
                        </a>
                    </li>';
            }
            if ($menu_pages[$i] == "dataYT.php") {
              echo '<li class="nav-item">
                        <a class="nav-link" href="' . $menu_pages[$i] . '">
                            <span class="menu-title">Regjistro kanal</span>
                        </a> 
                    </li>';
            }


            if ($menu_pages[$i] == "channel_selection.php") {
              echo '<li class="nav-item">
                        <a class="nav-link" href="' . $menu_pages[$i] . '">
                            <span class="menu-title">Kanalet</span>
                        </a> 
                    </li>';
            }
            $i++;
          }
          ?>
        </ul>
      </div>
    </li>



    <?php
    $i = 0;
    while ($i < count($menu_pages)) {
      if ($menu_pages[$i] == "autor.php") {
        echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                            <i class="fi fi-rr-copyright menu-icon pe-3"></i>
                                <span class="menu-title">Autor</span>
                            </a>
                        </li>';
      }
      $i++;
    }
    ?>
    <?php
    $i = 0;
    while ($i < count($menu_pages)) {
      if ($menu_pages[$i] == "ascap.php") {
        echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                            <i class="fi fi-rr-copyright menu-icon pe-3"></i>
                                <span class="menu-title">ASCAP</span>
                            </a>
                        </li>';
      }
      $i++;
    }
    ?>







    <?php
    $i = 0;
    while ($i < count($menu_pages)) {
      if ($menu_pages[$i] == "check_musics.php") {
        echo '<li class="nav-item">
                            <a class="nav-link" href="' . $menu_pages[$i] . '">
                            <i class="fi fi-rr-list-check menu-icon pe-3"></i>

                                <span class="menu-title">Konfirmimi i kengeve</span>
                            </a>
                        </li>';
      }
      $i++;
    }
    ?>






  </ul>
</nav>