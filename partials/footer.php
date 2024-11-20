<?php
// config.php
define('APP_VERSION', '3.2.0');
?>
<?php
// Close content wrappers
echo '</div></div>';
// Footer section
?>
<footer class="footer">
  <div class="d-sm-flex justify-content-between justify-content-sm-between">
    <span class="text-muted">
      &copy; <?php echo date("Y"); ?>
      <a href="" target="_blank">BareshaNetwork</a>. All rights reserved.
    </span>
    <span>
      <b>Version:</b> <?php echo APP_VERSION; ?>
    </span>
  </div>
</footer>
<?php
// Array of script URLs
$scripts = [
  // JavaScript libraries and custom scripts
  'https://code.jquery.com/jquery-3.6.3.js' => [
    'defer' => true,
    'integrity' => 'sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=',
    'crossorigin' => 'anonymous'
  ],
  'vendors/chart.js/Chart.min.js',
  'js/off-canvas.js',
  'js/hoverable-collapse.js',
  'js/template.js',
  'js/dashboard.js',
  'js/data-table.js',
  'js/jquery.cookie.js',
  // DataTables and related plugins
  'https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js',
  'https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js',
  'https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json',
  // DataTables extensions
  'https://cdn.datatables.net/autofill/2.5.1/js/dataTables.autoFill.min.js',
  'https://cdn.datatables.net/autofill/2.5.1/js/autoFill.bootstrap5.min.js',
  'https://cdn.datatables.net/buttons/2.3.3/js/dataTables.buttons.min.js',
  'https://cdn.datatables.net/buttons/2.3.3/js/buttons.bootstrap5.min.js',
  'https://cdn.datatables.net/buttons/3.1.2/js/buttons.colVis.min.js',
  'https://cdn.datatables.net/buttons/2.3.3/js/buttons.html5.min.js',
  'https://cdn.datatables.net/buttons/2.3.3/js/buttons.print.min.js',
  'https://cdn.datatables.net/colreorder/1.6.1/js/dataTables.colReorder.min.js',
  'https://cdn.datatables.net/datetime/1.2.0/js/dataTables.dateTime.min.js',
  'https://cdn.datatables.net/fixedcolumns/4.2.1/js/dataTables.fixedColumns.min.js',
  'https://cdn.datatables.net/fixedheader/3.3.1/js/dataTables.fixedHeader.min.js',
  'https://cdn.datatables.net/keytable/2.8.0/js/dataTables.keyTable.min.js',
  'https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js',
  'https://cdn.datatables.net/responsive/2.4.0/js/responsive.bootstrap5.min.js',
  'https://cdn.datatables.net/rowgroup/1.3.0/js/dataTables.rowGroup.min.js',
  'https://cdn.datatables.net/rowreorder/1.3.1/js/dataTables.rowReorder.min.js',
  'https://cdn.datatables.net/scroller/2.0.7/js/dataTables.scroller.min.js',
  'https://cdn.datatables.net/searchbuilder/1.4.0/js/dataTables.searchBuilder.min.js',
  'https://cdn.datatables.net/searchbuilder/1.4.0/js/searchBuilder.bootstrap5.min.js',
  'https://cdn.datatables.net/searchpanes/2.1.0/js/dataTables.searchPanes.min.js',
  'https://cdn.datatables.net/searchpanes/2.1.0/js/searchPanes.bootstrap5.min.js',
  'https://cdn.datatables.net/select/1.5.0/js/dataTables.select.min.js',
  // Additional libraries
  'https://cdn.jsdelivr.net/npm/pdfmake@0.1.36/build/pdfmake.min.js',
  'https://cdn.jsdelivr.net/npm/pdfmake@0.1.36/build/vfs_fonts.js',
  'https://cdn.jsdelivr.net/npm/moment/moment.min.js',
];
// Function to generate script tags
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
  // Function to enable dark mode
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
  // Function to disable dark mode
  function disableDarkMode() {
    DarkReader.disable();
    document.getElementById('modeIcon').className = 'fi fi-rr-moon';
    localStorage.setItem('darkMode', 'light');
  }
  // Function to toggle dark mode
  function toggleDarkMode() {
    const isDarkMode = localStorage.getItem('darkMode') !== 'dark';
    if (isDarkMode) {
      enableDarkMode();
    } else {
      disableDarkMode();
    }
  }
  function checkDarkModePreference() {
    const userPreference = localStorage.getItem('darkMode');
    if (userPreference === 'dark') {
      enableDarkMode();
    } else {
      disableDarkMode();
    }
  }
  // Initialize dark mode based on preference
  window.addEventListener('load', checkDarkModePreference);
  // Toggle dark mode on button click
  document.getElementById('darkModeButton').addEventListener('click', toggleDarkMode);
</script>
<script>
  function confirmLogout(event) {
    event.preventDefault(); // Prevent the default action
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
      if (result.isConfirmed) {
        // User confirmed logout
        window.location.href = 'logout.php';
      } else if (result.dismiss === Swal.DismissReason.timer) {
        // Timer ran out
        window.location.href = 'logout.php';
      } else {
        // User canceled logout
        // Do nothing
      }
    });
  }
</script>