$(document).ready(function () {
  $("#contractForm").submit(function (event) {
    event.preventDefault(); // Prevent form submission

    // Generate CSRF token
    var csrfToken = $('meta[name="csrf-token"]').attr("content");

    // Serialize form data
    var formData = new FormData(this);
    formData.append("csrf_token", csrfToken); // Append CSRF token to form data

    // Client-side input validation
    var emri = formData.get("emri");
    var mbiemri = formData.get("mbiemri");
    var numri_tel = formData.get("numri_tel");
    var numri_personal = formData.get("numri_personal");
    var perqindja = formData.get("perqindja");
    var klienti = formData.get("klienti");
    var vepra = formData.get("vepra");
    var data = formData.get("data");
    var shenime = formData.get("shenime");
    var email = formData.get("email");
    var emri_artistik = formData.get("emriartistik");

    if (
      !emri ||
      !mbiemri ||
      !numri_tel ||
      !numri_personal ||
      !perqindja ||
      !klienti ||
      !vepra ||
      !data ||
      !shenime ||
      !email ||
      !emri_artistik
    ) {
      // Display error message for missing fields
      Swal.fire({
        icon: "error",
        title: "Gabim",
        text: "Ju lutemi plotësoni të gjitha fushat e detyrueshme",
      });
      return;
    }

    // AJAX request
    $.ajax({
      type: "POST",
      url: "submitSignature.php",
      data: formData,
      dataType: "json",
      processData: false,
      contentType: false,
      success: function (response) {
        if (response.success) {
          // Show success message with two buttons using SweetAlert2
          Swal.fire({
            icon: "success",
            title: "Kontrata është krijuar me sukses",
            text: "Zgjidhni veprimin më poshtë:",
            showCancelButton: true,
            confirmButtonText: "Qëndro në këtë faqe",
            cancelButtonText: "Shko te lista e kontratave",
          }).then(function (result) {
            // Check which button was clicked
            if (result.isConfirmed) {
              // Reload the page
              location.reload();
            } else if (result.dismiss === Swal.DismissReason.cancel) {
              // Redirect to the contracts list page
              window.location.href = "lista_kontratave.php";
            }
          });
        } else {
          // Show error message with SweetAlert2
          Swal.fire({
            icon: "error",
            title: "Gabim",
            text: "Ka ndodhur një gabim gjatë dërgimit të nënshkrimit",
          });
        }
      },
      error: function (xhr, status, error) {
        // Show error message if AJAX request fails
        Swal.fire({
          icon: "error",
          title: "Gabim",
          text: "Ka ndodhur një gabim gjatë përpunimit të kërkesës",
        });
      },
    });
  });
});
