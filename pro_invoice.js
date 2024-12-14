// Kjo funksion ekzekutohet kur dokumenti është gati
$(document).ready(function () {
  // Funksioni për dërgimin e një fature të vetme
  function dergoFaturen(url, successMessage) {
    return function (e) {
      e.preventDefault();
      var invoiceId = $(this).data("id");

      // Dërgo kërkesën AJAX
      $.ajax({
        url: url + "?id=" + invoiceId,
        type: "GET",
        success: function (response) {
          if (response.includes("Email sent successfully")) {
            shfaqMesazhSukses(successMessage);
          } else {
            shfaqMesazhGabimi("Dështoi dërgimi i faturës.");
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          trajtoPergigjenMeGabim(textStatus, errorThrown);
        },
      });
    };
  }

  // Funksioni për shfaqjen e mesazhit të suksesit
  function shfaqMesazhSukses(mesazhi) {
    Swal.fire({
      icon: "success",
      title: mesazhi,
      showConfirmButton: false,
      timer: 1500,
    }).then(function () {
      window.location.href = "invoice.php?success=sent";
    });
  }

  // Funksioni për shfaqjen e mesazhit të gabimit
  function shfaqMesazhGabimi(mesazhi) {
    Swal.fire({
      icon: "error",
      title: "Ndodhi një gabim",
      text: mesazhi,
    }).then(function () {
      window.location.href = "invoice.php?success=error";
    });
  }

  // Funksioni për trajtimin e përgjigjeve me gabim
  function trajtoPergigjenMeGabim(textStatus, errorThrown) {
    var errorMessage =
      "Dështoi dërgimi i faturës. Gabimi: " + textStatus + ", " + errorThrown;
    Swal.fire({
      icon: "error",
      title: "Ndodhi një gabim",
      text: errorMessage,
    }).then(function () {
      window.location.href =
        "invoice.php?success=error&message=" + encodeURIComponent(errorMessage);
    });
  }

  // Lidhja e ngjarjeve me butonat përkatës
  $(document).on(
    "click",
    ".send-invoice",
    dergoFaturen("dergofakturen.php", "Emaili u dërgua me sukses")
  );
  $(document).on(
    "click",
    ".send-invoices",
    dergoFaturen(
      "dergofakturenTekKontabilisti.php",
      "Emaili u dërgua me sukses tek kontabilisti"
    )
  );
});
