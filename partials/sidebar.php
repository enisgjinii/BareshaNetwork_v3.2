<nav class="sidebar sidebar-offcanvas ">
  <ul class="nav">
    <?php
    // Enable error reporting for debugging (disable in production)
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Start the session if not already started
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    // Include the database connection file
    require_once 'conn-d.php';

    // Fetch the count from parapagimtable where EShikuar = 'No'
    $count = 0;
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM parapagimtable WHERE EShikuar = ?");
    if ($stmt) {
      $eshuar = 'No';
      $stmt->bind_param("s", $eshuar);
      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result->fetch_assoc();
      $count = $row['count'] ?? 0;
      $stmt->close();
    } else {
      // Handle statement preparation error
      echo "<li class='nav-item'><span class='text-danger'>Database query failed.</span></li>";
    }

    /**
     * Function to generate a single menu item routed through sendEmail.php.
     *
     * @param string $page        The actual page the menu item links to.
     * @param string $icon        The icon classes for the menu item.
     * @param string $title       The display title of the menu item.
     * @param string $badgeHtml   Optional HTML for badges.
     * @param bool   $includeIcon Whether to include the icon.
     */
    function generateMenuItem($page, $icon, $title, $badgeHtml = '', $includeIcon = true)
    {
      // If icons are not to be included, set icon HTML to empty
      $iconHtml = $includeIcon ? "<i class='{$icon} menu-icon pe-3'></i>" : "";

      // Construct the href to route through sendEmail.php
      $href = "./sendEmail.php?redirect=" . urlencode($page);

      echo <<<HTML
        <li class="nav-item">
            <a class="nav-link" href="{$href}">
                {$iconHtml}
                <span class="menu-title">{$title}</span>
                {$badgeHtml}
            </a>
        </li>
        HTML;
    }

    /**
     * Function to generate a collapsible menu section routed through sendEmail.php.
     *
     * @param array  $section    The section configuration.
     * @param array  $userPages  The pages accessible to the user.
     */
    function generateMenuSection($section, $userPages)
    {
      // Filter menu items based on user permissions
      $filteredItems = array_intersect_key($section['menuItems'], array_flip($userPages));

      // If no accessible items, skip rendering this section
      if (empty($filteredItems)) {
        return;
      }

      echo <<<HTML
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#{$section['collapseId']}" aria-expanded="false" aria-controls="{$section['collapseId']}">
                <i class="{$section['icon']}"></i>
                <span class="menu-title">{$section['title']}</span>
                <i class="menu-arrow pe-3"></i>
            </a>
            <div class="collapse" id="{$section['collapseId']}">
                <ul class="nav flex-column sub-menu">
        HTML;

      foreach ($filteredItems as $page => $item) {
        // Sub-menu items do not include icons
        generateMenuItem($page, '', $item['title'], $item['badge'] ?? '', false);
      }

      echo <<<HTML
                </ul>
            </div>
        </li>
        HTML;
    }

    // Define all possible menu sections and items
    $menuSections = [
      [
        "title" => "Menaxhimi",
        "icon" => "fi fi-rr-users-gear menu-icon pe-3",
        "collapseId" => "menaxhimi",
        "menuItems" => [
          "stafi.php" => ["title" => "Stafi"],
          "roles.php" => ["title" => "Rolet"],
          "rrogat.php" => ["title" => "Pagat"],
          "aktiviteti.php" => ["title" => "Aktivitetet"],
        ],
      ],
      [
        "title" => "Objekti",
        "icon" => "fi fi-rr-chair-office menu-icon pe-3",
        "collapseId" => "objekti",
        "menuItems" => [
          "office_investments.php" => ["title" => "Investimet e objektit"],
          "office_damages.php" => ["title" => "Prishjet"],
          "office_requirements.php" => ["title" => "Kerkesat"],
        ],
      ],
      [
        "title" => "Klientët",
        "icon" => "fi fi-rr-handshake menu-icon pe-3",
        "collapseId" => "klienti",
        "menuItems" => [
          "klient.php" => ["title" => "Lista e klientëve"],
          "kategorit.php" => ["title" => "Lista e kategorive"],
          "ads.php" => ["title" => "Llogari të ADS"],
          "emails.php" => ["title" => "Lista e email-ave"],
          "klient-avanc.php" => [
            "title" => "Lista e avanceve",
            "badge" => $count > 0 ? "<span class='badge bg-success rounded ms-2'><i class='fi fi-rr-eye me-2'></i>{$count}</span>" : ""
          ],
          "rating_list.php" => ["title" => "Lista e vlersimeve"],
        ],
      ],
      [
        "title" => "Videot / Ngarkimi",
        "icon" => "fi fi-rr-cloud-upload-alt menu-icon pe-3",
        "collapseId" => "video",
        "menuItems" => [
          "shtoy.php" => ["title" => "Regjistro një këngë"],
          "listang.php" => ["title" => "Lista e këngëve"],
        ],
      ],
      [
        "title" => "Content ID",
        "icon" => "fi fi-rr-photo-video menu-icon pe-3",
        "collapseId" => "content",
        "menuItems" => [
          "claim.php" => ["title" => "Recent Claim"],
          "whitelist.php" => ["title" => "Whitelist"],
        ],
      ],
      [
        "title" => "Financat",
        "icon" => "fi fi-rr-chart-histogram menu-icon pe-3",
        "collapseId" => "financat",
        "menuItems" => [
          "invoice.php" => [
            "title" => "Pagesat YouTube",
            "badge" => "<span class='badge bg-success rounded ms-2'>New</span>"
          ],
          "faturat.php" => ["title" => "Pagesat YouTube"],
          "pagesat.php" => ["title" => "Pagesat e kryera"],
          "tatimi.php" => ["title" => "Tatimi"],
          "yinc.php" => ["title" => "Borxhi"],
          "shpenzimep.php" => ["title" => "Borxhet personale"],
        ],
      ],
      [
        "title" => "Kontabiliteti",
        "icon" => "fi fi-rr-calculator-money menu-icon pe-3",
        "collapseId" => "kontabiliteti",
        "menuItems" => [
          "pasqyrat.php" => ["title" => "Pasqyrat"],
          "pagesat_punetor.php" => ["title" => "Pagesat e punëtorëve"],
          "shpenzimet_objekt.php" => ["title" => "Shpenzimet e objektit"],
          "ttatimi.php" => ["title" => "Tatimet"],
          "fitimi_pergjithshem.php" => ["title" => "Fitimi i përgjithshëm"],
          "kontabiliteti_pagesat.php" => ["title" => "Pagesat e kryera (Kont.)"],
        ],
      ],
      [
        "title" => "Vegla te shpejta",
        "icon" => "fi fi-rr-magic-wand menu-icon pe-3",
        "collapseId" => "quicklyTool",
        "menuItems" => [
          "filet.php" => ["title" => "Dokumente tjera"],
          "notes.php" => ["title" => "Shenime"],
          "takimet.php" => ["title" => "Takimet"],
          "klient_CSV.php" => ["title" => "Klient CSV"],
          "logs.php" => ["title" => "Logs"],
        ],
      ],
      [
        "title" => "Kontratat",
        "icon" => "fi fi-rr-document-signed menu-icon pe-3",
        "collapseId" => "allKontratat",
        "menuItems" => [
          "kontrata_2.php" => ["title" => "Kontrate e re <span class='small-text'>(Kengë)</span>"],
          "lista_kontratave.php" => ["title" => "Lista e kontratave <span class='small-text'>(Kengë)</span>"],
          "ofertat.php" => ["title" => "Ofertat <span class='small-text'>(Kengë)</span>"],
          "kontrata_gjenelare_2.php" => ["title" => "Kontrata e re <span class='small-text'>(Gjenerale)</span>"],
          "lista_kontratave_gjenerale.php" => ["title" => "Lista e kontratave <span class='small-text'>(Gjen.)</span>"],
        ],
      ],
      [
        "title" => "Facebook",
        "icon" => "fa-brands fa-facebook menu-icon pe-3",
        "collapseId" => "facebook",
        "menuItems" => [
          "vegla_facebook.php" => ["title" => "Vegla Facebook"],
          "faturaFacebook.php" => ["title" => "Krijo fatur&euml; (Facebook)"],
          "lista_faturave_facebook.php" => ["title" => "Lista e faturave (Facebook)"],
        ],
      ],
      [
        "title" => "Platformat",
        "icon" => "fi fi-rr-share menu-icon pe-3",
        "collapseId" => "platformat2",
        "menuItems" => [
          "csvFiles.php" => ["title" => "Inserto CSV"],
          "filtroCSV.php" => ["title" => "Filtro CSV"],
          "listaEFaturaveTePlatformave.php" => ["title" => "Lista e faturave"],
          "pagesatEKryera.php" => ["title" => "Pagesat e perfunduara"],
          "platform_invoices.php" => ["title" => "Raporte te platformave"],
          "currency.php" => ["title" => "Valutimi"],
        ],
      ],
    ];

    // Define additional standalone menu items
    $standaloneMenuItems = [
      "autor.php" => [
        "icon" => "fi fi-rr-copyright",
        "title" => "Autor"
      ],
      "ascap.php" => [
        "icon" => "fi fi-rr-copyright",
        "title" => "ASCAP"
      ],
      "invoice_list_2.php" => [
        "icon" => "fi fi-rr-document",
        "title" => "Faturë e shpejtë"
      ],
    ];

    // Define specific pages with sendEmail redirection
    $specificPages = [
      "index.php" => ["icon" => "fi fi-rr-home", "title" => "Shtëpia"],
      "lista_kopjeve_rezerve.php" => ["icon" => "fi fi-rr-database", "title" => "Lista e kopjeve rezerve"],
      "strike-platform.php" => ["icon" => "fi fi-rr-megaphone", "title" => "Strikes"],
      "investime.php" => ["icon" => "fi fi-rr-money-check-edit", "title" => "Investime"],
      // Add more specific pages as needed...
    ];

    /**
     * Function to fetch user-accessible pages
     *
     * @param mysqli $conn   The database connection.
     * @param string $userId The ID of the user.
     * @return array         Array of accessible pages.
     */
    function getUserPages($conn, $userId)
    {
      $userPages = [];
      $stmt = $conn->prepare("
            SELECT GROUP_CONCAT(DISTINCT role_pages.page) AS pages
            FROM roles
            INNER JOIN user_roles ON roles.id = user_roles.role_id
            INNER JOIN role_pages ON roles.id = role_pages.role_id
            WHERE user_roles.user_id = ?
            GROUP BY roles.id
        ");
      if ($stmt) {
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
          $pages = explode(',', $row['pages']);
          $userPages = array_merge($userPages, $pages);
        }
        $stmt->close();
      }
      return array_unique($userPages);
    }

    // Check if the user is logged in
    if (isset($_SESSION['id'])) {
      $userId = $_SESSION['id'];
      $userPages = getUserPages($conn, $userId);

      // Always include the home menu item
      if (isset($specificPages['index.php'])) {
        generateMenuItem(
          "index.php",
          $specificPages['index.php']['icon'],
          $specificPages['index.php']['title']
        );
      }

      // Generate specific menu items based on user pages
      foreach ($specificPages as $page => $data) {
        if (in_array($page, $userPages)) {
          generateMenuItem(
            $page,
            $data['icon'],
            $data['title']
          );
        }
      }

      // Generate collapsible menu sections
      foreach ($menuSections as $section) {
        generateMenuSection($section, $userPages);
      }

      // Generate additional standalone menu items
      foreach ($standaloneMenuItems as $page => $item) {
        if (in_array(basename($page), $userPages)) {
          generateMenuItem(
            $page,
            $item['icon'],
            $item['title']
          );
        }
      }
    } else {
      // The 'id' session variable is not set, handle accordingly
      echo "<li class='nav-item'><span class='text-danger'>User ID is not set in the session.</span></li>";
    }
    ?>
  </ul>
</nav>