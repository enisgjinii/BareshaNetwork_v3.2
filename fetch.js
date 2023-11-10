$(document).ready(function () {
  fetch_data();

  function fetch_data() {
    var dataTables = $("#employeeList").DataTable({
      dom: "Bfrtip",
      buttons: ["copy", "csv", "excel", "pdf", "print"],
      processing: true,
      serverSide: true,
      order: [],
      ajax: {
        url: "api/fetch.php",
        type: "POST",
      },
    });
    setInterval(function () {
      dataTables.ajax.reload(null, false); // user paging is not reset on reload
    }, 5000);
  }

  $(document).on("click", ".delete", function () {
    var id = $(this).attr("id");
    if (confirm("Are you sure you want to remove this?")) {
      $.ajax({
        url: "/api/deletefat.php",
        method: "POST",
        data: { id: id },
        success: function (data) {
          $("#alert_message").html(
            '<div class="alert alert-success">' + data + "</div>"
          );
          $("#user_data").DataTable().destroy();
          fetch_data();
        },
      });
      setInterval(function () {
        $("#alert_message").html("");
      }, 5000);
    }
  });
});
