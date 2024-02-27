// Pasi dokumenti të jetë gati, lidh ngjarjen e klikimit të butonit "delete-button"
$(document).ready(function () {
  $(".delete-button").click(function (e) {
    // Parandalo ngarkimin e faqes nga veprimi i parazgjedhur i butonit
    e.preventDefault();
    // Merrni ID-në e kanalit nga atributi data-channelid i butonit
    const channelID = $(this).data("channelid");
    // Trego një dialog për konfirmim për fshirjen e kanalit
    Swal.fire({
      title: "Konfirmo Fshirjen",
      text: "A jeni të sigurt se dëshironi të fshini këtë kanal?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Po, fshijeni!",
      cancelButtonText: "Anulo",
    }).then((result) => {
      // Nëse përdoruesi konfirmon fshirjen
      if (result.isConfirmed) {
        // Bëni një kërkesë AJAX për të fshirë kanalin
        $.ajax({
          // Përditësoni URL-në për të përfshirë ID-në e kanalit
          url: `delete_channel.php?channel_id=${channelID}`,
          type: "POST",
          // Në rast të suksesit, trego një mesazh suksesi dhe rifresko faqen
          success: function (response) {
            Swal.fire("Sukses", "Kanali është fshirë me sukses!", "success");
            setTimeout(function () {
              location.reload();
            }, 4000);
          },
          // Në rast të një gabimi, trego një mesazh gabimi
          error: function () {
            Swal.fire(
              "Gabim",
              "Ndodhi një gabim gjatë fshirjes së kanalit.",
              "error"
            );
          },
        });
      }
    });
  });
});
