<?php include 'partials/header.php'; ?>
<?php
$total_client_query = $conn->query("SELECT COUNT(id) FROM klientet");
$total_result = $total_client_query->fetch_assoc();
$total_clients = $total_result["COUNT(id)"];
$monetized_query = $conn->query("SELECT COUNT(monetizuar) FROM klientet WHERE monetizuar = 'PO'");
$monetized_result = $monetized_query->fetch_assoc();
$monetized_clients = $monetized_result["COUNT(monetizuar)"];
$non_monetized_query = $conn->query("SELECT COUNT(monetizuar) FROM klientet WHERE monetizuar = 'JO'");
$non_monetized_result = $non_monetized_query->fetch_assoc();
$non_monetized_clients = $non_monetized_result["COUNT(monetizuar)"];
$monetized_percentage = ($monetized_clients / $total_clients) * 100;
$non_monetized_percentage = ($non_monetized_clients / $total_clients) * 100;
?>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <nav class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Klientët</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">
            <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">
              Lista e klientëve
            </a>
          </li>
      </nav>
      <div class="row mb-3">
        <div>
          <a style="text-transform: none;text-decoration:none;" class="input-custom-css px-3 py-2" href="shtok.php"><i class="fi fi-rr-add"></i>
            &nbsp;
            Shto klientë
          </a>
          <button type="button" class="input-custom-css px-3 py-2 mx-2" style="text-transform: none" data-bs-toggle="modal" data-bs-target="#modal_of_monetized">
            <i class="fi fi-rr-user-check"></i> &nbsp; Lista e klienteve te monetizuar
          </button>
          <button type="button" class="input-custom-css px-3 py-2 me-2" style="text-transform: none" data-bs-toggle="modal" data-bs-target="#modal_of_non_monetized">
            <i class="fi fi-rr-user-time"></i> &nbsp; Lista e klienteve te pa-monetizuar
          </button>
          <button type="button" class="input-custom-css px-3 py-2" style="text-transform: none" data-bs-toggle="modal" data-bs-target="#list_of_passive">
            <i class="fi fi-rr-user-lock"></i> &nbsp; Lista e klienteve pasiv
          </button>
        </div>
      </div>
      <div class="progress mx-1" style="height: 20px">
        <div class="progress-bar bg-success rounded-5" role="progressbar" style="width: <?php echo $monetized_percentage; ?>%" aria-valuenow="<?php echo $monetized_percentage; ?>" aria-valuemin="0" aria-valuemax="100">
          Nr. i klientëve të monetizuar: <?php echo $monetized_clients ?>
        </div>
        <div class="progress-bar bg-danger rounded-5 ms-2" role="progressbar" style="width: <?php echo $non_monetized_percentage; ?>%" aria-valuenow="<?php echo $non_monetized_percentage; ?>" aria-valuemin="0" aria-valuemax="100">
          Nr. i klientëve të pa-monetizuar: <?php echo $non_monetized_clients ?>
        </div>
      </div>
      <div class="row text-center mb-3">
        <div class="modal fade" id="modal_of_monetized" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Lista e klientëve te monetizuar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body text-start">
                <div class="table-responsive">
                  <table class="table table-bordered" width="100%" id="monetized_clients">
                    <thead class="bg-light">
                      <tr>
                        <th>Emri dhe mbiemri</th>
                        <th>Statusi</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="modal_of_non_monetized" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Lista e klientëve te pa-monetizuar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body text-start">
                <div class="table-responsive">
                  <table class="table table-bordered" width="100%" id="non_monetized_clients">
                    <thead class="bg-light">
                      <tr>
                        <th>Emri dhe mbiemri</th>
                        <th>Statusi</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="list_of_passive" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Lista e klientëve pasiv</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body text-start">
                <div class="table-responsive">
                  <table class="table table-bordered" width="100%" id="list_of_passive_clients">
                    <thead class="bg-light">
                      <tr>
                        <th>Emri dhe mbiemri</th>
                        <th>Statusi i klientit</th>
                        <th>Vepro</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card rounded-5 shadow-sm">
        <div class="card-body">
          <div class="row">
            <div class="table-responsive">
              <table id="example" class="table">
                <thead class="bg-light">
                  <tr>
                    <th>Emri & Mbiemri</th>
                    <th>Emri artistik</th>
                    <th>Adresa e email-it</th>
                    <th>Datat e kontrates ( Fillim - Skadim )</th>
                    <th>Data e kontrates ( Versioni i ri )</th>
                    <th>Data e skadimit ( Versioni i ri )</th>
                    <th>Veprim</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include 'partials/footer.php'; ?>
<script>
  $('#example').DataTable({
    ordering: false,
    searching: true,
    processing: true,
    serverSide: true,
    lengthMenu: [
      [10, 25, 50, 100, 500, 1000],
      [10, 25, 50, 100, 500, 1000],
    ],
    ajax: {
      url: 'get-clients.php', // Replace with your server-side script URL
      type: 'GET',
    },
    columns: [{
        data: 'emri',
        render: function(data, type, row) {
          if (row.monetizuar == 'PO') {
            return '<p>' + data + '</p>' +
              '<span class="text-success">Klient i monetizuar </span>';
          } else {
            return '<p>' + data + '</p>' + '<span class="text-danger rounded-5">Klient i pa-monetizuar </span>';
          }
        }
      }, {
        data: 'emriart'
      },
      {
        data: 'emailadd'
      },
      {
        data: null,
        render: function(data, type, row) {
          return row.dk + ' - ' + row.dks;
        }
      },
      {
        data: 'data_e_krijimit',
        render: function(data, type, row) {
          // Set Albanian locale for moment.js
          moment.locale('sq');
          if (!data) {
            return '<span class="text-danger">Nuk u gjet asnjë datë.</span>';
          } else {
            var contractStartDate = moment(data); // Assuming data_e_krijimit is the contract start date
            if (!contractStartDate.isValid()) {
              return '<span class="text-danger">Data e fillimit të kontratës e pavlefshme</span>';
            }
            // Format the contract creation date in Albanian
            var creationDateFormatted = contractStartDate.format('dddd, D MMMM YYYY');
            return '<span>' + creationDateFormatted + '</span>';
          }
        }
      },
      {
        data: 'kohezgjatja',
        render: function(data, type, row) {
          // Check if the contract duration is null or empty
          if (data == null || data === '') {
            return '<span class="text-danger">Nuk u gjet asnjë datë.</span>'; // Handle null or empty values
          } else {
            var months = parseInt(data);
            if (isNaN(months) || months <= 0) {
              return '<span class="text-danger">Data e skadimit jo-valide</span>'; // Handle invalid values
            }
            var years = Math.floor(months / 12);
            var remainingMonths = months % 12;
            var durationHTML = '';
            if (years === 0) {
              // If less than a year, display only months
              durationHTML = '<p>' + data + ' Muaj</p>';
            } else if (remainingMonths === 0) {
              // If exact years, display only years
              durationHTML = '<p>' + years + ' Vjet</p>';
            } else {
              // Display both years and remaining months
              durationHTML = '<p>' + years + ' Vjet ' + remainingMonths + ' Muaj</p>';
            }
            // Set contract start date and calculate expiration date
            var contractDate = moment(row.data_e_krijimit); // Assuming data_e_krijimit is the contract start date
            if (!contractDate.isValid()) {
              return '<span class="text-danger">Data e fillimit të kontratës e pavlefshme</span>'; // Handle invalid date
            }
            var expirationDate = contractDate.clone().add(months, 'months');
            expirationDate.locale('sq');
            var expirationDateFormatted = expirationDate.format('dddd, LL');
            // Set current date and calculate days until expiration
            var today = moment();
            var daysUntilExpiration = expirationDate.diff(today, 'days');
            // Define thresholds for near and far expiration
            var nearExpirationThreshold = 30; // 30 days
            var farExpirationThreshold = 90; // 90 days
            // Determine contract status based on expiration date
            var contractStatus, statusClass, statusMessage;
            if (daysUntilExpiration < 0) {
              contractStatus = 'Skaduar';
              statusClass = 'text-warning';
              statusMessage = 'Kontrata është skaduar';
            } else if (daysUntilExpiration <= nearExpirationThreshold) {
              contractStatus = 'Afër skadimit';
              statusClass = 'text-danger';
              statusMessage = 'Skadon shumë shpejt';
            } else if (daysUntilExpiration <= farExpirationThreshold) {
              contractStatus = 'Pranë skadimit';
              statusClass = 'text-warning';
              statusMessage = 'Skadon në një të ardhme të afërt';
            } else {
              contractStatus = 'Aktive';
              statusClass = 'text-success';
              statusMessage = 'Aktive';
            }
            // Return formatted output with contract status
            if (contractStatus === 'Skaduar') {
              return durationHTML + '<span class="' + statusClass + '">' + statusMessage + '</span>';
            } else {
              return durationHTML + '<span class="' + statusClass + '">' + expirationDateFormatted + ' (' + statusMessage + ')</span>';
            }
          }
        }
      },
      { // Custom column for buttons
        data: 'id', // Assuming 'id' is the property containing the ID
        render: function(data, type, row) {
          var buttonsHtml = `
            <a style="text-transform: none;text-decoration:none;" class="input-custom-css px-3 py-2" href="editk.php?id=${data}"><i class="fi fi-rr-edit"></i></a>
            <a style="text-transform: none; text-decoration:none;" class="input-custom-css px-3 py-2" onclick="konfirmoDeaktivizimin(${data})"><i class="fi fi-rr-user-slash"></i></a>
        `;
          return buttonsHtml;
        }
      }
      //  <a class="btn btn-sm btn-primary py-2 px-2 rounded-5 shadow-sm text-white" data-bs-toggle="modal" data-bs-target="#pass${data}"><i class="fi fi-rr-lock"></i></a>
      // <a class="btn btn-sm btn-danger py-2 px-2 rounded-5 shadow-sm text-white" href="klient.php?blocked=${data}&block=${row.blockii}"><i class="fi fi-rr-ban"></i></a>
    ],
    columnDefs: [{
      "targets": [0, 1, 2, 3, 4, 5, 6], // Indexes of the columns you want to apply the style to
      "render": function(data, type, row) {
        // Apply the style to the specified columns
        return type === 'display' && data !== null ? '<div style="white-space: normal;">' + data + '</div>' : data;
      }
    }],
    dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
      "<'row'<'col-md-12'tr>>" +
      "<'row'<'col-md-6'i><'col-md-6'p>>",
    buttons: [{
      extend: 'pdfHtml5',
      text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
      titleAttr: 'Eksporto tabelen ne formatin PDF',
      className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
    }, {
      extend: 'excelHtml5',
      text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
      titleAttr: 'Eksporto tabelen ne formatin CSV',
      className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
    }, {
      extend: "copyHtml5",
      text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
      titleAttr: "Kopjo tabelen ne formatin Clipboard",
      className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
    }, {
      extend: 'print',
      text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
      titleAttr: 'Printo tabelën',
      className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
    }, ],
    initComplete: function() {
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
    },
    fixedHeader: true,
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
    },
    stripeClasses: ['stripe-color'],
    "ordering": false
  })
  $(document).ready(function() {
    // Initialize the DataTable
    $('#non_monetized_clients').DataTable({
      dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
        "<'row'<'col-md-12'tr>>" +
        "<'row'<'col-md-6'i><'col-md-6'p>>",
      lengthMenu: [
        [10, 25, 50, 100, 500, 1000],
        [10, 25, 50, 100, 500, 1000]
      ],
      buttons: [{
        extend: 'pdfHtml5',
        text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
        titleAttr: 'Eksporto tabelen ne formatin PDF',
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
      }, {
        extend: 'excelHtml5',
        text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
        titleAttr: 'Eksporto tabelen ne formatin CSV',
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
      }, {
        extend: "copyHtml5",
        text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
        titleAttr: "Kopjo tabelen ne formatin Clipboard",
        className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
      }, {
        extend: 'print',
        text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
        titleAttr: 'Printo tabelën',
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
      }, ],
      "processing": true,
      "serverSide": true,
      "ajax": {
        "url": "ajax_get_non_monetized_clients.php",
        "type": "POST",
      },
      "columns": [{
          "data": "emri"
        },
        {
          "data": "monetizuar"
        }
      ],
      initComplete: function() {
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
      },
      fixedHeader: true,
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
      },
      stripeClasses: ['stripe-color'],
    });
  });
  $(document).ready(function() {
    // Initialize the DataTable
    $('#list_of_passive_clients').DataTable({
      dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
        "<'row'<'col-md-12'tr>>" +
        "<'row'<'col-md-6'i><'col-md-6'p>>",
      lengthMenu: [
        [10, 25, 50, 100, 500, 1000],
        [10, 25, 50, 100, 500, 1000]
      ],
      buttons: [{
        extend: 'pdfHtml5',
        text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
        titleAttr: 'Eksporto tabelen ne formatin PDF',
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
      }, {
        extend: 'excelHtml5',
        text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
        titleAttr: 'Eksporto tabelen ne formatin CSV',
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
      }, {
        extend: "copyHtml5",
        text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
        titleAttr: "Kopjo tabelen ne formatin Clipboard",
        className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
      }, {
        extend: 'print',
        text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
        titleAttr: 'Printo tabelën',
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
      }, ],
      "processing": true,
      "serverSide": true,
      "ajax": {
        "url": "ajax_get_passive_clients.php",
        "type": "POST",
      },
      "columns": [{
          "data": "emri"
        },
        {
          "data": "aktiv",
          "render": function(data, type, row) {
            if (data === 1) {
              return '<span class="badge bg-success rounded-5">Statusi - Aktiv</span>';
            } else {
              return '<span class="badge bg-danger rounded-5">Statusi - Pasiv</span>';
            }
          }
        },
        {
          "data": null,
          "render": function(data, type, row) {
            if (row['aktiv'] === 1) {
              return '<a href="#" onclick="confirmDeactivation(' + row['id'] + ')" style="text-decoration:none;text-transform:none" class="input-custom-css px-3 py-2">Pasivizoje</a>';
            } else {
              return '<a href="#" onclick="confirmActivation(' + row['id'] + ')" style="text-decoration:none;text-transform:none" class="input-custom-css px-3 py-2">Aktivizoje</a>';
            }
          }
        }
      ],
      initComplete: function() {
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
      },
      fixedHeader: true,
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
      },
      stripeClasses: ['stripe-color'],
    });
  });
  $(document).ready(function() {
    // Initialize the DataTable
    $('#monetized_clients').DataTable({
      dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
        "<'row'<'col-md-12'tr>>" +
        "<'row'<'col-md-6'i><'col-md-6'p>>",
      lengthMenu: [
        [10, 25, 50, 100, 500, 1000],
        [10, 25, 50, 100, 500, 1000]
      ],
      buttons: [{
        extend: 'pdfHtml5',
        text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
        titleAttr: 'Eksporto tabelen ne formatin PDF',
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
      }, {
        extend: 'excelHtml5',
        text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
        titleAttr: 'Eksporto tabelen ne formatin CSV',
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
      }, {
        extend: "copyHtml5",
        text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
        titleAttr: "Kopjo tabelen ne formatin Clipboard",
        className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
      }, {
        extend: 'print',
        text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
        titleAttr: 'Printo tabelën',
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
      }, ],
      "processing": true,
      "serverSide": true,
      "ajax": {
        "url": "ajax_get_monetized_clients.php",
        "type": "POST",
      },
      "columns": [{
          "data": "emri"
        },
        {
          "data": "monetizuar"
        }
      ],
      initComplete: function() {
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
      },
      fixedHeader: true,
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
      },
      stripeClasses: ['stripe-color'],
    });
  });

  function confirmActivation(clientId) {
    Swal.fire({
      title: 'A jeni i sigurt?',
      text: 'Jeni duke u përgatitur për të aktivizuar këtë klient!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Po, aktivizo!',
      cancelButtonText: 'Anulo',
      reverseButtons: true,
      showLoaderOnConfirm: true,
      preConfirm: () => {
        return new Promise((resolve) => {
          setTimeout(() => {
            resolve();
          }, 2000); // Add a delay to simulate server-side action
        });
      },
      allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Aktivizuar!',
          text: 'Klienti është aktivizuar me sukses.',
          icon: 'success',
          confirmButtonColor: '#3085d6',
          confirmButtonText: 'OK'
        }).then(() => {
          // Redirect to the deactivation script with the client ID
          window.location.href = 'activate_client.php?id=' + clientId;
        });
      }
    });
  }

  function konfirmoDeaktivizimin(clientId) {
    Swal.fire({
      title: 'A jeni i sigurt?',
      text: 'Jeni duke u përgatitur për të deaktivizuar këtë klient!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Po, deaktivizoje!',
      cancelButtonText: 'Anulo',
      reverseButtons: true,
      showLoaderOnConfirm: true,
      preConfirm: () => {
        return new Promise((resolve) => {
          setTimeout(() => {
            resolve();
          }, 2000); // Add a delay to simulate server-side action
        });
      },
      allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Deaktivizuar!',
          text: 'Klienti është deaktivizuar me sukses.',
          icon: 'success',
          confirmButtonColor: '#3085d6',
          confirmButtonText: 'OK'
        }).then(() => {
          // Redirect to the deactivation script with the client ID
          window.location.href = 'passive_client.php?id=' + clientId;
        });
      }
    });
  }
</script>