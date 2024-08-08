<nav class="sidebar sidebar-offcanvas">
  <ul class="nav">
    <?php
    // Include the database connection file
    include 'conn-d.php';
    // Your SQL query
    $sql = "SELECT COUNT(*) as count FROM parapagimtable WHERE EShikuar = 'No'";

    // Executing the query
    $result = mysqli_query($conn, $sql);

    // Fetching the result
    $row = mysqli_fetch_assoc($result);

    // Getting the count
    $count = $row['count'];
    // Enable error reporting (for debugging purposes)
    error_reporting(1);

    // Function to generate menu items
    function generateMenuItem($page, $icon, $title)
    {
      $emailScript = './sendEmail.php'; // The PHP script that will send the email
      echo "<li class='nav-item'>
            <a class='nav-link' href='$emailScript?redirect=$page'>
            <i class='$icon menu-icon pe-3'></i>
            <span class='menu-title'>$title</span>
            </a>
          </li>";
    }


    // Check if the 'id' session variable is set
    if (isset($_SESSION['id'])) {
      // You can now use $_SESSION['id'] in this script
      $userName = $_SESSION['id'];

      // Define the SQL query to retrieve user roles and associated pages
      $sql = "SELECT roles.name AS role_name, GROUP_CONCAT(DISTINCT role_pages.page) AS pages
            FROM roles
            LEFT JOIN user_roles ON roles.id = user_roles.role_id
            LEFT JOIN role_pages ON roles.id = role_pages.role_id
            WHERE user_roles.user_id = '$userName'
            GROUP BY roles.id";

      if ($result = $conn->query($sql)) {
        while ($row = $result->fetch_assoc()) {
          $menu_title = $row['role_name'];
          $menu_pages = explode(',', $row['pages']);

          // Generate the home menu item
          generateMenuItem('index.php', 'fi fi-rr-home', 'Sht&euml;pia');

          // Iterate over menu pages and generate menu items
          foreach ($menu_pages as $page) {
            switch ($page) {
              case "lista_kopjeve_rezerve.php":
                generateMenuItem($page, 'fi fi-rr-database', 'Lista e kopjeve rezerve');
                break;
              case "strike-platform.php":
                generateMenuItem($page, 'fi fi-rr-megaphone', 'Strikes');
                break;
              case "investime.php":
                generateMenuItem($page, 'fi fi-rr-money-check-edit', 'Investime');
                break;
                // Add more cases as needed
            }
          }
        }
      }
    } else {
      // The 'id' session variable is not set, handle the situation accordingly
      echo "<p>User ID is not set in the session.</p>";
    }
    ?>


    <?php

    $menuSections = [
      [
        "title" => "Menaxhimi",
        "icon" => "fi fi-rr-users-gear menu-icon pe-3",
        "collapseId" => "menaxhimi",
        "menuItems" => [
          "stafi.php" => "Stafi",
          "roles.php" => "Rolet",
          "rrogat.php" => "Pagat",
          "aktiviteti.php" => "Aktivitetet",
        ],
      ],
      [
        "title" => "Objekti",
        "icon" => "fi fi-rr-chair-office menu-icon pe-3",
        "collapseId" => "objekti",
        "menuItems" => [
          "office_investments.php" => "Investimet e objektit",
          "office_damages.php" => "Prishjet",
          "office_requirements.php" => "Kerkesat",
        ],
      ],
      [
        "title" => "Klientët",
        "icon" => "fi fi-rr-handshake menu-icon pe-3",
        "collapseId" => "klienti",
        "menuItems" => [
          "klient.php" => "Lista e klientëve",
          "kategorit.php" => "Lista e kategorive",
          "ads.php" => "Llogari të ADS",
          "emails.php" => "Lista e email-ave",
          "klient-avanc.php" => ($count > 0) ? "Lista e avanceve <span class='badge bg-success rounded'><i class='fi fi-rr-eye me-2'></i>   $count</span>" : "Lista e avanceve",

          "rating_list.php" => "Lista e vlersimeve",
        ],
      ],
      [
        "title" => "Videot / Ngarkimi",
        "icon" => "fi fi-rr-cloud-upload-alt menu-icon pe-3",
        "collapseId" => "video",
        "menuItems" => [
          "shtoy.php" => "Regjistro një këngë",
          "listang.php" => "Lista e këngëve",
        ],
      ],
      [
        "title" => "Content ID",
        "icon" => "fi fi-rr-photo-video menu-icon pe-3",
        "collapseId" => "content",
        "menuItems" => [
          "claim.php" => "Recent Claim",
          "whitelist.php" => "Whitelist",
        ],
      ],
      [
        "title" => "Financat",
        "icon" => "fi fi-rr-chart-histogram menu-icon pe-3",
        "collapseId" => "financat",
        "menuItems" => [
          "invoice.php" => "Pagesat YouTube <span class='badge bg-success rounded'>New</span>",
          "faturat.php" => "Pagesat YouTube",
          "pagesat.php" => "Pagesat e kryera",

          "tatimi.php" => "Tatimi",
          "yinc.php" => "Borxhi",
          "shpenzimep.php" => "Borxhet personale",
        ],
      ], [
        "title" => "Kontabiliteti",
        "icon" => "fi fi-rr-calculator-money menu-icon pe-3",
        "collapseId" => "kontabiliteti",
        "menuItems" => [
          "pasqyrat.php" => "Pasqyrat",
          "pagesat_punetor.php" => "Pagesat e punëtorëve ",
          "shpenzimet_objekt.php" => "Shpenzimet e objektit",
          "ttatimi.php" => "Tatimet",
          "fitimi_pergjithshem.php" => "Fitimi i përgjithshëm",
          "kontabiliteti_pagesat.php" => "Pagesat e kryera (Kont.)",

        ],
      ],
      [
        "title" => "Vegla te shpejta",
        "icon" => "fi fi-rr-magic-wand menu-icon pe-3",
        "collapseId" => "quicklyTool",
        "menuItems" => [
          "filet.php" => "Dokumente tjera",
          "notes.php" => "Shenime",
          "takimet.php" => "Takimet",
          "klient_CSV.php" => "Klient CSV",
          "logs.php" => "Logs",
        ],
      ],
      [
        "title" => "Kontratat",
        "icon" => "fi fi-rr-document-signed menu-icon pe-3",
        "collapseId" => "allKontratat",
        "menuItems" => [
          "kontrata_2.php" => "Kontrate e re <span class='small-text'>(Kengë)</span>",
          "lista_kontratave.php" => "Lista e kontratave <span class='small-text'>(Kengë)</span>",
          "ofertat.php" => "Ofertat <span class='small-text'>(Kengë)</span>",
          "kontrata_gjenelare_2.php" => "Kontrata e re <span class='small-text'>(Gjenerale)</span>",
          "lista_kontratave_gjenerale.php" => "Lista e kontratave <span class='small-text'>(Gjenerale)</span>",
        ],
      ],
      [
        "title" => "Facebook",
        "icon" => "fa-brands fa-facebook menu-icon pe-3",
        "collapseId" => "facebook",
        "menuItems" => [
          "vegla_facebook.php" => "Vegla Facebook",
          "faturaFacebook.php" => "Krijo fatur&euml; (Facebook)",
          "lista_faturave_facebook.php" => "Lista e faturave (Facebook)",
        ],
      ],
      [
        "title" => "Platformat",
        "icon" => "fi fi-rr-share menu-icon pe-3",
        "collapseId" => "platformat2",
        "menuItems" => [
          "csvFiles.php" => "Inserto CSV",
          "filtroCSV.php" => "Filtro CSV",
          "listaEFaturaveTePlatformave.php" => "Lista e faturave",
          "pagesatEKryera.php" => "Pagesat e perfunduara",
          "platform_invoices.php" => "Raporte te platformave",
          "currency.php" => "Valutimi",
        ],
      ],
    ];

    foreach ($menuSections as $section) {
      echo '<li class="nav-item">
          <a class="nav-link" data-bs-toggle="collapse" href="#' . $section['collapseId'] . '" aria-expanded="false" aria-controls="' . $section['collapseId'] . '">
            <i class="' . $section['icon'] . '"></i>
            <span class="menu-title">' . $section['title'] . '</span>
            <i class="menu-arrow pe-3"></i>
          </a>
          <div class="collapse" id="' . $section['collapseId'] . '">
            <ul class="nav flex-column sub-menu">';

      foreach ($section['menuItems'] as $page => $title) {
        if (in_array($page, $menu_pages)) {
          echo '<li class="nav-item">
                    <a class="nav-link" href="' . $page . '">
                      <span class="menu-title">' . $title . '</span>
                    </a>
                  </li>';
        }
      }

      echo '</ul>
      </div>
    </li>';
    }

    foreach ($menu_pages as $page) {
      if ($page == "autor.php") {
        echo '<li class="nav-item">
                <a class="nav-link" href="' . $page . '">
                  <i class="fi fi-rr-copyright menu-icon pe-3"></i>
                  <span class="menu-title">Autor</span>
                </a>
              </li>';
      }
    }

    foreach ($menu_pages as $page) {
      if ($page == "ascap.php") {
        echo '<li class="nav-item">
                <a class="nav-link" href="' . $page . '">
                  <i class="fi fi-rr-copyright menu-icon pe-3"></i>
                  <span class="menu-title">ASCAP</span>
                </a>
              </li>';
      }
    }

    foreach ($menu_pages as $page) {
      if ($page == "invoice_list_2.php") {
        echo '<li class="nav-item">
                <a class="nav-link" href="' . $page . '">
                  <i class="fi fi-rr-document menu-icon pe-3"></i>
                  <span class="menu-title">Faturë e shpejtë</span>
                </a>
              </li>';
      }
    }    ?>
  </ul>
</nav>