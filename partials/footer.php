<?php
// Definoni versionin e aplikacionit
define('APP_VERSION', '3.2.0');

// Mbyllni elementet div
echo '</div></div>';
?>

<!-- Footer i faqes -->
<footer class="footer">
  <div class="d-sm-flex justify-content-between justify-content-sm-between">
    <!-- Tregoni të drejtat e rezervuara dhe linkun për "BareshaNetwork" -->
    <span class="text-muted">&copy; <?php echo date("Y"); ?><a href="" target="_blank">BareshaNetwork</a>. Të gjitha të drejtat janë të rezervuara.</span>
    <!-- Tregoni versionin aktual të aplikacionit -->
    <span><b>Version:</b> <?php echo APP_VERSION; ?></span>
  </div>
</footer>

<?php
// Definoni skedarët JavaScript që duhen ngarkuar
$scripts = [
  'https://code.jquery.com/jquery-3.6.3.js' => ['defer' => true, 'integrity' => 'sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=', 'crossorigin' => 'anonymous'],
  'vendors/chart.js/Chart.min.js',
  'js/off-canvas.js',
  'js/hoverable-collapse.js',
  'js/template.js',
  'js/dashboard.js',
  'js/jquery.cookie.js',
  'https://cdn.datatables.net/2.1.8/js/dataTables.min.js',
  'https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js',
  'https://cdn.jsdelivr.net/npm/moment/moment.min.js'
];

// Ngarkoni çdo skedar me atribute përkatëse
foreach ($scripts as $key => $value) {
  if (is_array($value)) {
    $src = $key;
    $attributes = '';
    foreach ($value as $attr => $attrValue) {
      $attributes .= " $attr=\"$attrValue\"";
    }
  } else {
    $src = $value;
    $attributes = ' defer';
  }
  echo "<script src=\"$src\"$attributes></script>\n";
}
?>

<script>
  // Funksion për të aktivizuar modalitetin e errët
  function enableDarkMode() {
    DarkReader.enable({
      brightness: 100,
      contrast: 90,
      sepia: 10,
      darkSchemeBackgroundColor: '#0a0e27',
      darkSchemeTextColor: '#cdd3ff'
    });
    document.getElementById('modeIcon').className = 'fi fi-rr-brightness';
    localStorage.setItem('darkMode', 'dark');
  }

  // Funksion për të çaktivizuar modalitetin e errët
  function disableDarkMode() {
    DarkReader.disable();
    document.getElementById('modeIcon').className = 'fi fi-rr-moon';
    localStorage.setItem('darkMode', 'light');
  }

  // Funksion për të ndërruar mes modalitetit të errët dhe të ndritshëm
  function toggleDarkMode() {
    const isDarkMode = localStorage.getItem('darkMode') !== 'dark';
    if (isDarkMode) {
      enableDarkMode();
    } else {
      disableDarkMode();
    }
  }

  // Kontrolloni preferencat e përdoruesit për modalitetin e errët
  function checkDarkModePreference() {
    const userPreference = localStorage.getItem('darkMode');
    if (userPreference === 'dark') {
      enableDarkMode();
    } else {
      disableDarkMode();
    }
  }

  // Kur faqja të ngarkohet, kontrolloni preferencat e modalitetit
  window.addEventListener('load', checkDarkModePreference);

  // Lidheni butonin për ndërrimin e modalitetit me funksionin përkatës
  document.getElementById('darkModeButton').addEventListener('click', toggleDarkMode);
</script>

<script>
  // Funksion për të konfirmuar daljen nga llogaria
  function confirmLogout(event) {
    event.preventDefault();
    Swal.fire({
      title: 'Jeni të sigurt që doni të dilni?',
      text: 'Ju do të dilni nga llogaria juaj pas 10 sekondash.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Po, dilni',
      cancelButtonText: 'Anulo',
      reverseButtons: true,
      timer: 10000,
      timerProgressBar: true,
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    }).then((result) => {
      if (result.isConfirmed || result.dismiss === Swal.DismissReason.timer) {
        window.location.href = 'logout.php';
      }
    });
  }
</script>