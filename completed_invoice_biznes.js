class DataTableManager {
  constructor() {
    this.initFlatpickr();
    this.initDataTable();
    this.setupEventListeners();
  }
  initFlatpickr() {
    flatpickr("#startDateBiznes", {
      dateFormat: "Y-m-d",
      allowInput: true,
      locale: "sq",
    }).set("maxDate", new Date() - 3 * 24 * 60 * 60 * 1000);
    flatpickr("#endDateBiznes", {
      dateFormat: "Y-m-d",
      allowInput: true,
      locale: "sq",
    }).set("maxDate", new Date());
  }
  initDataTable() {
    this.columns = [
      { data: "customer_name" },
      { data: "invoice_id" },
      { data: "total_payment_amount" },
      { data: "payment_date" },
      { data: "bank_info" },
      { data: "type_of_pay" },
      { data: "description" },
      { data: "total_invoice_amount" },
      { data: "action" },
    ];
    this.paymentsTableBiznes = $("#paymentsTableBiznes").DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      order: [[3, "desc"]],
      dom:
        "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
        "<'row'<'col-md-12'tr>>" +
        "<'row'<'col-md-6'><'col-md-6'p>>",
      buttons: this.initButtons(),
      lengthMenu: [
        [10, 25, 50, -1],
        [10, 25, 50, "All"],
      ],
      search: true,
      ajax: {
        url: "complete_invoices_biznes.php",
        type: "POST",
        data: (d) => {
          d.startDateBiznes = $("#startDateBiznes").val();
          d.endDateBiznes = $("#endDateBiznes").val();
        },
      },
      columns: this.columns,
      columnDefs: [
        {
          targets: [0, 1, 2, 3, 4, 5, 6, 7, 8],
          render: (data, type, row) => {
            return type === "display" && data !== null
              ? '<div style="white-space: normal;">' + data + "</div>"
              : data;
          },
        },
      ],
      stripeClasses: ["stripe-color"],
    });
  }
  initButtons() {
    const currentDate = this.getCurrentDate();
    return [
      {
        extend: "pdfHtml5",
        text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
        titleAttr: "Eksporto tabelen ne formatin PDF",
        className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
        filename: "faturat_e_kryera_" + currentDate + "",
      },
      {
        extend: "copyHtml5",
        text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
        titleAttr: "Kopjo tabelen ne formatin Clipboard",
        className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
        filename: "faturat_e_kryera_" + currentDate + "",
      },
      {
        extend: "excelHtml5",
        text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
        titleAttr: "Eksporto tabelen ne formatin Excel",
        className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
        exportOptions: {
          modifier: {
            search: "applied",
            order: "applied",
            page: "all",
          },
        },
        filename: "faturat_e_kryera_" + currentDate + "",
      },
      {
        extend: "print",
        text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
        titleAttr: "Printo tabel&euml;n",
        className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
        filename: "faturat_e_kryera_" + currentDate + "",
      },
    ];
  }
  setupEventListeners() {
    $("#clearFiltersBtnBiznes").click(() => {
      $("#startDateBiznes").val("");
      $("#endDateBiznes").val("");
      this.paymentsTableBiznes.search("").draw();
    });
    $("#startDateBiznes, #endDateBiznes").change(() => {
      this.filterByDateRange();
    });
    $("#paymentsTableBiznes").on("click", ".delete-btn", (event) => {
      console.log("Delete button clicked");
      var invoiceId = $(event.currentTarget).data("invoice-id");
      console.log("Invoice ID:", invoiceId);
      this.handleDelete(invoiceId);
    });
  }
  filterByDateRange() {
    console.log("Filtering by date range");
    this.paymentsTableBiznes.ajax.reload();
  }
  handleDelete(invoiceId) {
    console.log("Deleting invoice with ID:", invoiceId);
    Swal.fire({
      title: "Jeni të sigurt?",
      text: "Nuk do të jeni në gjendje të rikuperoni këtë regjistrim!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Po, fshij!",
      cancelButtonText: "Anulo",
      reverseButtons: true,
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "delete_invoice_completed.php",
          method: "POST",
          data: {
            invoice_id: invoiceId,
          },
          success: (response) => {
            if (response.success) {
              console.log("Invoice deleted successfully");
              Swal.fire("Fshirë!", "Regjistrimi është fshirë.", "success");
              this.paymentsTableBiznes.ajax.reload();
            } else {
              const errorMessage = response.error || "Gabim i panjohur";
              console.log("Error deleting invoice:", errorMessage);
              Swal.fire(
                "Gabim!",
                "Gabim në fshirjen e regjistrimit: " + errorMessage,
                "error"
              );
            }
          },
          error: (xhr, status, error) => {
            console.log("Error deleting invoice:", error);
            Swal.fire(
              "Gabim!",
              "Gabim në fshirjen e regjistrimit: " + error,
              "error"
            );
          },
        });
      }
    });
  }
  getCurrentDate() {
    const now = new Date();
    const year = now.getFullYear();
    let month = now.getMonth() + 1;
    month = month < 10 ? "0" + month : month;
    let day = now.getDate();
    day = day < 10 ? "0" + day : day;
    return year + "-" + month + "-" + day;
  }
}
$(document).ready(() => {
  new DataTableManager();
});
