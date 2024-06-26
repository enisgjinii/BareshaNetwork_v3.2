

var table = $('#tableOfContributions').DataTable({
    dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
        "<'row'<'col-md-12'tr>>" +
        "<'row'<'col-md-6'><'col-md-6'p>>",
    initComplete: function () {
        var btns = $(".dt-buttons");
        btns.addClass("").removeClass("dt-buttons btn-group");
        var lengthSelect = $("div.dataTables_length select");
        lengthSelect.addClass("form-select");
        lengthSelect.css({
            width: "auto",
            margin: "0 8px",
            padding: "0.375rem 1.75rem 0.375rem 0.75rem",
            lineHeight: "1.5",
            border: "1px solid #ced4da",
            borderRadius: "0.25rem",
        });
        // Calculate and display the initial sum
        updateTotalValue();
    },
    language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
    },
    buttons: [{
        extend: "pdf",
        text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
        titleAttr: "Eksporto tabelen ne formatin PDF",
        className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
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
    },
    {
        extend: "print",
        text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
        titleAttr: "Printo tabel&euml;n",
        className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
    },
    ],
    stripeClasses: ["stripe-color"],
});
$('#periodFilter').on('change', function () {
    table.column(3).search(this.value).draw();
    updateTotalValue(); // Update the total value when the period filter changes

});

function updateTotalValue() {
    var total = 0;
    // Calculate the sum of the values in column 4
    table.column(4, {
        search: 'applied'
    }).data().each(function (value) {
        total += parseFloat(value) || 0;
    });
    // Display the total value
    $('#totalValue').text(total.toFixed(2));
}