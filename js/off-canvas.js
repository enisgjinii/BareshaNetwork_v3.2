/**
 * Sidebar Offcanvas Toggle Script
 *
 * This script toggles the "active" class on a sidebar element with the class
 * `.sidebar-offcanvas` when an element with the `data-mdb-toggle="offcanvas"` attribute is clicked.
 * 
 * Requirements:
 * - jQuery must be included in the project.
 * - The sidebar element must have the class `sidebar-offcanvas`.
 * - The button or trigger element must have the attribute `data-mdb-toggle="offcanvas"`.
 *
 * Usage:
 * Add the `data-mdb-toggle="offcanvas"` attribute to any clickable element.
 * Ensure the sidebar you want to toggle has the `sidebar-offcanvas` class.
 */

(function ($) {
  'use strict';

  // Wait until the DOM is fully loaded before running the script
  $(document).ready(function () {

    /**
     * Attach a click event listener to elements with `data-mdb-toggle="offcanvas"`.
     * When clicked, this will toggle the "active" class on the sidebar element.
     */
    $('[data-mdb-toggle="offcanvas"]').on("click", function () {
      // Cache the sidebar element to avoid querying the DOM multiple times
      const $sidebar = $('.sidebar-offcanvas');

      // Check if the sidebar element exists
      if ($sidebar.length) {
        // Toggle the "active" class to show or hide the sidebar
        $sidebar.toggleClass('active');
      } else {
        // Warn in the console if the sidebar element is not found
        console.warn('Sidebar element not found.');
      }
    });
  });

})(jQuery);
