<nav class="sidebar sidebar-offcanvas border border-2 rounded-5" style="margin-top:30px;margin-left:8px;height:fit-content;">
  <ul class="nav">
    <?php
    error_reporting(0);
    session_start();
    require_once 'conn-d.php';
    $invoice_count = 0;
    $stmt = $conn->prepare("
      SELECT COUNT(*) as count FROM invoices
      WHERE paid_amount < COALESCE(total_amount_after_percentage, total_amount_in_eur_after_percentage, total_amount)
    ");
    if ($stmt) {
      $stmt->execute();
      $result = $stmt->get_result();
      if ($row = $result->fetch_assoc()) {
        $invoice_count = $row['count'] ?? 0;
      }
      $stmt->close();
    } else {
      echo "<li class='nav-item'><span class='text-danger'>Database query failed.</span></li>";
    }
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
            "badge" => $count > 0 ? "<span class='badge bg-success rounded-pill ms-2'><i class='fi fi-rr-eye me-2'></i>{$count}</span>" : "",
          ],
          "rating_list.php" => ["title" => "Lista e vlersimeve"],
        ],
      ],
      [
        "title" => "Videot & Ngarkimi",
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
            "badge" => $invoice_count > 0 ? "<span class='badge bg-success rounded-pill ms-2'>{$invoice_count}</span>" : "",
          ],
          "faturat.php" => ["title" => "Pagesat YouTube"],
          "pagesat.php" => ["title" => "Pagesat e kryera"],
          "yinc.php" => ["title" => "Borxhi"],
          "shpenzimep.php" => ["title" => "Borxhet personale"],
        ],
      ],
      [
        "title" => "Kontabiliteti",
        "icon" => "fi fi-rr-calculator-money menu-icon pe-3",
        "collapseId" => "kontabiliteti",
        "menuItems" => [
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
          "platform_invoices.php" => ["title" => "Raporte te platformave"],
        ],
      ],
    ];
    $standaloneMenuItems = [
      "autor.php" => [
        "icon" => "fi fi-rr-copyright",
        "title" => "Autor",
      ],
      "invoice_list_2.php" => [
        "icon" => "fi fi-rr-document",
        "title" => "Faturë e shpejtë",
      ],
    ];
    $specificPages = [
      "index.php" => ["icon" => "fi fi-rr-home", "title" => "Shtëpia"],
      "lista_kopjeve_rezerve.php" => ["icon" => "fi fi-rr-database", "title" => "Lista e kopjeve rezerve"],
      "strike-platform.php" => ["icon" => "fi fi-rr-megaphone", "title" => "Strikes"],
      "investime.php" => ["icon" => "fi fi-rr-money-check-edit", "title" => "Investime"],
    ];
    function getUserPages($conn, $userId)
    {
      if (isset($_SESSION['user_pages'])) {
        return $_SESSION['user_pages'];
      }
      $userPages = [];
      $stmt = $conn->prepare("
        SELECT DISTINCT role_pages.page
        FROM roles
        INNER JOIN user_roles ON roles.id = user_roles.role_id
        INNER JOIN role_pages ON roles.id = role_pages.role_id
        WHERE user_roles.user_id = ?
      ");
      if ($stmt) {
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
          $userPages[] = $row['page'];
        }
        $stmt->close();
      }
      $_SESSION['user_pages'] = array_unique($userPages);
      return $_SESSION['user_pages'];
    }
    function generateMenuItem($page, $icon, $title, $badgeHtml = '', $includeIcon = true)
    {
      $iconHtml = $includeIcon ? "<i class='{$icon} menu-icon pe-3'></i>" : "";
      $href = "./{$page}";
      echo "<li class='nav-item'>
        <a class='nav-link rounded d-flex align-items-center' href='{$href}'>
          {$iconHtml}
          <span class='menu-title flex-grow-1'>{$title}</span>
          {$badgeHtml}
        </a>
      </li>";
    }
    function generateMenuSection($section, $userPages)
    {
      $filteredItems = array_intersect_key($section['menuItems'], array_flip($userPages));
      if (empty($filteredItems)) {
        return;
      }
      echo "<li class='nav-item'>
        <a class='nav-link rounded d-flex align-items-center' data-bs-toggle='collapse' href='#{$section['collapseId']}' aria-expanded='false' aria-controls='{$section['collapseId']}'>
          <i class='{$section['icon']}'></i>
          <span class='menu-title flex-grow-1'>{$section['title']}</span>
          <i class='menu-arrow pe-3'></i>
        </a>
        <div class='collapse' id='{$section['collapseId']}'>
          <ul class='nav flex-column sub-menu'>";
      foreach ($filteredItems as $page => $item) {
        generateMenuItem($page, '', $item['title'], $item['badge'] ?? '', $includeIcon = false);
      }
      echo "</ul>
        </div>
      </li>";
    }
    if (isset($_SESSION['id'])) {
      $userId = $_SESSION['id'];
      $userPages = getUserPages($conn, $userId);
      if (isset($specificPages['index.php'])) {
        generateMenuItem(
          "index.php",
          $specificPages['index.php']['icon'],
          $specificPages['index.php']['title']
        );
      }
      foreach ($specificPages as $page => $data) {
        if (in_array($page, $userPages)) {
          generateMenuItem(
            $page,
            $data['icon'],
            $data['title']
          );
        }
      }
      foreach ($menuSections as $section) {
        generateMenuSection($section, $userPages);
      }
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
      echo "<li class='nav-item'><span class='text-danger'>User ID is not set in the session.</span></li>";
    }
    ?>
    <hr class="mx-auto" style="width:80%;display:block;color:white">
    <div class="text-center">
      <a class="input-custom-css px-3 py-2 m-2" style="text-transform:none;text-decoration:none;width:fit-content" href="account.php">
        <i class="fi fi-rr-user"></i>
      </a>
      <!-- <a class="input-custom-css-disabled px-3 py-2 m-2" style="text-transform:none;text-decoration:none;width:fit-content" href="link2.php">
        <i class="fi fi-rr-settings"></i>
      </a> -->
      <button id="darkModeButton" class="input-custom-css px-3 py-2" style="border-radius: 6px;">
        <i id="modeIcon" class="fi fi-rr-brightness"></i>
      </button>
      <a class="input-custom-css px-3 py-2 m-2" style="text-transform:none;text-decoration:none;width:fit-content" href="#" onclick="confirmLogout(event)">
        <i class="fi fi-rr-exit"></i>
      </a>
    </div>
    <br>
  </ul>
</nav>