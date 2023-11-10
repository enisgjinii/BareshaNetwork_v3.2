</div>
</div>
<footer class="footer">
  <div class="d-sm-flex justify-content-between justify-content-sm-between">
    <span class="text-muted">Copyright ©
      <?php echo date("Y"); ?> <a href="" target="_blank">BareshaNetwork</a>. All rights reserved.
    </span>
    <span><b>Version: </b> 3.0 </span>
  </div>
</footer>




<!-- This is a script tag that loads the jQuery library from a Content Delivery Network (CDN) hosted at https://code.jquery.com. -->
<script src="https://code.jquery.com/jquery-3.6.3.js" defer integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>

<!-- This script tag loads a vendor bundle that contains common dependencies used throughout the project. -->
<!-- <script src="vendors/base/vendor.bundle.base.js" defer ></script> -->

<!-- This script tag loads Chart.js, a library for creating charts and graphs. -->
<script src="vendors/chart.js/Chart.min.js" defer></script>

<!-- These script tags load custom JavaScript files used in the project. -->
<script src="js/off-canvas.js" defer></script>
<script src="js/hoverable-collapse.js" defer></script>
<script src="js/template.js" defer></script>
<script src="js/dashboard.js" defer></script>
<script src="js/data-table.js" defer></script>

<!-- These script tags load jQuery DataTables, a plugin for adding advanced interaction controls to HTML tables. -->
<script src="js/jquery.dataTables.js" defer></script>
<script src="js/dataTables.bootstrap4.js" defer></script>

<!-- This script tag loads jQuery Cookie, a plugin for working with cookies in JavaScript. -->
<script src="js/jquery.cookie.js" defer type="text/javascript"></script>


<script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json"></script>

<script src="https://cdn.jsdelivr.net/npm/pdfmake@0.1.36/build/pdfmake.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/pdfmake@0.1.36/build/vfs_fonts.js" defer></script>

<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js" defer></script>
<script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js" defer></script>

<script src="https://cdn.datatables.net/autofill/2.5.1/js/dataTables.autoFill.min.js" defer></script>
<script src="https://cdn.datatables.net/autofill/2.5.1/js/autoFill.bootstrap5.min.js" defer></script>

<script src="https://cdn.datatables.net/buttons/2.3.3/js/dataTables.buttons.min.js" defer></script>
<script src="https://cdn.datatables.net/buttons/2.3.3/js/buttons.bootstrap5.min.js" defer></script>
<script src="https://cdn.datatables.net/buttons/2.3.3/js/buttons.colVis.min.js" defer></script>
<script src="https://cdn.datatables.net/buttons/2.3.3/js/buttons.html5.min.js" defer></script>
<script src="https://cdn.datatables.net/buttons/2.3.3/js/buttons.print.min.js" defer></script>

<script src="https://cdn.datatables.net/colreorder/1.6.1/js/dataTables.colReorder.min.js" defer></script>

<script src="https://cdn.datatables.net/datetime/1.2.0/js/dataTables.dateTime.min.js" defer></script>

<script src="https://cdn.datatables.net/fixedcolumns/4.2.1/js/dataTables.fixedColumns.min.js" defer></script>

<script src="https://cdn.datatables.net/fixedheader/3.3.1/js/dataTables.fixedHeader.min.js" defer></script>

<script src="https://cdn.datatables.net/keytable/2.8.0/js/dataTables.keyTable.min.js" defer></script>

<script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js" defer></script>
<script src="https://cdn.datatables.net/responsive/2.4.0/js/responsive.bootstrap5.min.js" defer></script>

<script src="https://cdn.datatables.net/rowgroup/1.3.0/js/dataTables.rowGroup.min.js" defer></script>

<script src="https://cdn.datatables.net/rowreorder/1.3.1/js/dataTables.rowReorder.min.js" defer></script>

<script src="https://cdn.datatables.net/scroller/2.0.7/js/dataTables.scroller.min.js" defer></script>

<script src="https://cdn.datatables.net/searchbuilder/1.4.0/js/dataTables.searchBuilder.min.js" defer></script>
<script src="https://cdn.datatables.net/searchbuilder/1.4.0/js/searchBuilder.bootstrap5.min.js" defer></script>

<script src="https://cdn.datatables.net/searchpanes/2.1.0/js/dataTables.searchPanes.min.js" defer></script>
<script src="https://cdn.datatables.net/searchpanes/2.1.0/js/searchPanes.bootstrap5.min.js" defer></script>

<script src="https://cdn.datatables.net/select/1.5.0/js/dataTables.select.min.js" defer></script>
<script>
  // Check if the user's preference is already stored in local storage
  const userPreference = localStorage.getItem('darkMode');

  // If the preference is set, update the toggle accordingly
  if (userPreference === 'dark') {
    document.getElementById('toggle-mode').checked = true;
    DarkReader.enable({
      brightness: 100,
      contrast: 90,
      sepia: 10
    });
  }

  const toggleMode = document.getElementById('toggle-mode');

  toggleMode.addEventListener('change', function() {
    if (this.checked) {
      // Enable Dark Reader with specific settings
      DarkReader.enable({
        brightness: 100,
        contrast: 90,
        sepia: 10
      });
      // Save the user's preference to local storage
      localStorage.setItem('darkMode', 'dark');
    } else {
      // Disable Dark Reader
      DarkReader.disable();
      // Save the user's preference to local storage
      localStorage.setItem('darkMode', 'light');
    }
  });
</script>
</body>

</html>