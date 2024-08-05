<?php include 'partials/header.php'; ?>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <nav class="bg-white px-2 rounded-5" style="width:fit-content;" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Klientët</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">
            <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">
              Lista e klientëve
            </a>
          </li>
        </ol>
      </nav>
      <div class="row mb-3 d-none d-md-none d-lg-block">
        <div>
          <a style="text-transform: none;text-decoration:none;" class="input-custom-css px-3 py-2" href="shtok.php"><i class="fi fi-rr-add"></i>
            &nbsp;
            Shto klientë
          </a>
        </div>
      </div>
      <ul class="nav nav-pills bg-white my-3 mx-0 rounded-5" style="width: fit-content; border: 1px solid lightgrey;" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link rounded-5 active" style="text-transform: none" id="pills-listaEKlienteve-tab" data-bs-toggle="pill" data-bs-target="#pills-listaEKlienteve" type="button" role="tab" aria-controls="pills-listaEKlienteve" aria-selected="true">Lista e klientëve</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link rounded-5" style="text-transform: none" id="pills-listaEKlienteveMonetizuar-tab" data-bs-toggle="pill" data-bs-target="#pills-listaEKlienteveMonetizuar" type="button" role="tab" aria-controls="pills-listaEKlienteveMonetizuar" aria-selected="false">Lista e klienteve të monetizuar</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link rounded-5" style="text-transform: none" id="pills-listaEKlientevePaMonetizuar-tab" data-bs-toggle="pill" data-bs-target="#pills-listaEKlientevePaMonetizuar" type="button" role="tab" aria-controls="pills-listaEKlientevePaMonetizuar" aria-selected="false">Lista e klienteve të pa monetizuar</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link rounded-5" style="text-transform: none" id="pills-listaEKlientevePasiv-tab" data-bs-toggle="pill" data-bs-target="#pills-listaEKlientevePasiv" type="button" role="tab" aria-controls="pills-listaEKlientevePasiv" aria-selected="false">Lista e klienteve pasiv</button>
        </li>
      </ul>
      <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-listaEKlienteve" role="tabpanel" aria-labelledby="pills-listaEKlienteve-tab" tabindex="0">
          <div class="card rounded-5 shadow-none d-none d-md-none d-lg-block">
            <div class="card-body">
              <div class="row">
                <div class="table-responsive">
                  <table id="listaKlientave" class="table">
                    <thead class="bg-light">
                      <tr>
                        <th class="text-dark">Emri & Mbiemri</th>
                        <th class="text-dark">Emri artistik</th>
                        <th class="text-dark">Adresa e email-it</th>
                        <th class="text-dark">Datat e kontrates ( Fillim - Skadim )</th>
                        <th class="text-dark">Data e kontrates ( Versioni i ri )</th>
                        <th class="text-dark">Data e skadimit ( Versioni i ri )</th>
                        <th class="text-dark">Veprim</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="tab-pane fade" id="pills-listaEKlienteveMonetizuar" role="tabpanel" aria-labelledby="pills-listaEKlienteveMonetizuar-tab" tabindex="0">
          <div class="card rounded-5 shadow-none d-none d-md-none d-lg-block">
            <div class="card-body">
              <div class="row">
                <div class="table-responsive">
                  <table id="listaKlientaveTeMonetizuar" class="table">
                    <thead class="bg-light">
                      <tr>
                        <th class="text-dark">Emri & Mbiemri</th>
                        <th class="text-dark">Emri artistik</th>
                        <th class="text-dark">Adresa e email-it</th>
                        <th class="text-dark">Statusi i monetizimit</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $sql = "SELECT * FROM klientet WHERE monetizuar = 'PO'";
                      $result = mysqli_query($conn, $sql);
                      if (mysqli_num_rows($result) > 0) {
                        $columns = [
                          ['emri', 'mbiemri'],
                          ['emriart'],
                          ['emailadd'],
                          ['monetizuar']
                        ];
                        while ($row = mysqli_fetch_assoc($result)) {
                          echo "<tr>";
                          foreach ($columns as $column) {
                            echo "<td>";
                            if (count($column) > 1) {
                              $value = trim(implode(' ', array_map(function ($field) use ($row) {
                                return $row[$field] ?? '';
                              }, $column)));
                            } else {
                              $value = $row[$column[0]] ?? '';
                            }
                            if (!empty($value)) {
                              echo htmlspecialchars($value);
                            } else {
                              echo '<span class="badge rounded-pill bg-warning text-dark">Mungon</span>';
                            }
                            echo "</td>";
                          }
                          echo "</tr>";
                        }
                      } else {
                        echo '<tr><td colspan="4"><div class="alert alert-info" role="alert">Nuk ka asnjë klient të monetizuar</div></td></tr>';
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="tab-pane fade" id="pills-listaEKlientevePaMonetizuar" role="tabpanel" aria-labelledby="pills-listaEKlientevePaMonetizuar-tab" tabindex="0">
          <div class="card rounded-5 shadow-none d-none d-md-none d-lg-block">
            <div class="card-body">
              <div class="row">
                <div class="table-responsive">
                  <table id="listaKlientaveTePaMonetizuar" class="table">
                    <thead class="bg-light">
                      <tr>
                        <th class="text-dark">Emri & Mbiemri</th>
                        <th class="text-dark">Emri artistik</th>
                        <th class="text-dark">Adresa e email-it</th>
                        <th class="text-dark">Statusi i monetizimit</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $sql = "SELECT * FROM klientet WHERE monetizuar = 'JO'";
                      $result = mysqli_query($conn, $sql);
                      if (mysqli_num_rows($result) > 0) {
                        $columns = [
                          ['emri', 'mbiemri'],
                          ['emriart'],
                          ['emailadd'],
                          ['monetizuar']
                        ];
                        while ($row = mysqli_fetch_assoc($result)) {
                          echo "<tr>";
                          foreach ($columns as $column) {
                            echo "<td>";
                            if (count($column) > 1) {
                              $value = trim(implode(' ', array_map(function ($field) use ($row) {
                                return $row[$field] ?? '';
                              }, $column)));
                            } else {
                              $value = $row[$column[0]] ?? '';
                            }
                            if (!empty($value)) {
                              echo htmlspecialchars($value);
                            } else {
                              echo '<span class="badge rounded-pill bg-warning text-dark">Mungon</span>';
                            }
                            echo "</td>";
                          }
                          echo "</tr>";
                        }
                      } else {
                        echo '<tr><td colspan="4"><div class="alert alert-info" role="alert">Nuk ka asnjë klient të monetizuar</div></td></tr>';
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="tab-pane fade" id="pills-listaEKlientevePasiv" role="tabpanel" aria-labelledby="pills-listaEKlientevePasiv-tab" tabindex="0">
          <div class="card rounded-5 shadow-none d-none d-md-none d-lg-block">
            <div class="card-body">
              <div class="row">
                <div class="table-responsive">
                  <table id="listaKlientaveTePasiv" class="table">
                    <thead class="bg-light">
                      <tr>
                        <th class="text-dark">Emri & Mbiemri</th>
                        <th class="text-dark">Emri artistik</th>
                        <th class="text-dark">Adresa e email-it</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $sql = "SELECT * FROM klientet WHERE aktiv = 1";
                      $result = mysqli_query($conn, $sql);
                      if (mysqli_num_rows($result) > 0) {
                        $columns = [
                          ['emri', 'mbiemri'],
                          ['emriart'],
                          ['emailadd']
                        ];
                        while ($row = mysqli_fetch_assoc($result)) {
                          echo "<tr>";
                          foreach ($columns as $column) {
                            echo "<td>";
                            if (count($column) > 1) {
                              $value = trim(implode(' ', array_map(function ($field) use ($row) {
                                return $row[$field] ?? '';
                              }, $column)));
                            } else {
                              $value = $row[$column[0]] ?? '';
                            }
                            if (!empty($value)) {
                              echo htmlspecialchars($value);
                            } else {
                              echo '<span class="badge rounded-pill bg-warning text-dark">Mungon</span>';
                            }
                            echo "</td>";
                          }
                          echo "</tr>";
                        }
                      } else {
                        echo '<tr><td colspan="4"><div class="alert alert-info" role="alert">Nuk ka asnjë klient të monetizuar</div></td></tr>';
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include 'partials/footer.php'; ?>
<script>
  const commonConfig = {
    ordering: false,
    fixedHeader: true,
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json"
    },
    stripeClasses: ['stripe-color'],
    dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>><'row'<'col-md-12'tr>><'row'<'col-md-6'i><'col-md-6'p>>",
    buttons: [{
        extend: 'pdfHtml5',
        text: '<i class="fi fi-rr-file-pdf fa-lg"></i> PDF',
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
      },
      {
        extend: 'excelHtml5',
        text: '<i class="fi fi-rr-file-excel fa-lg"></i> Excel',
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
      },
      {
        extend: 'copyHtml5',
        text: '<i class="fi fi-rr-copy fa-lg"></i> Kopjo',
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
      },
      {
        extend: 'print',
        text: '<i class="fi fi-rr-print fa-lg"></i> Printo',
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
      }
    ],
    initComplete: function() {
      $(".dt-buttons").removeClass("dt-buttons btn-group");
      $("div.dataTables_length select").addClass("form-select").css({
        width: "auto",
        margin: "0 8px",
        padding: "0.375rem 1.75rem 0.375rem 0.75rem",
        lineHeight: "1.5",
        border: "1px solid #ced4da",
        borderRadius: "0.25rem"
      });
    }
  };
  $('#listaKlientave').DataTable({
    ...commonConfig,
    searching: true,
    processing: true,
    serverSide: true,
    lengthMenu: [
      [10, 25, 50, 100, 500, 1000],
      [10, 25, 50, 100, 500, 1000]
    ],
    ajax: {
      url: 'get-clients.php',
      type: 'POST'
    },
    columns: [{
        data: 'emri',
        render: (data, type, row) => {
          const status = row.monetizuar == 'PO' ? 'success' : 'danger';
          const text = row.monetizuar == 'PO' ? 'Klient i monetizuar' : 'Klient i pa-monetizuar';
          return `<p>${data}</p><span class="text-${status}">${text}</span>`;
        }
      },
      {
        data: 'emriart'
      },
      {
        data: 'emailadd'
      },
      {
        data: null,
        render: (data, type, row) => `${row.dk} - ${row.dks}`
      },
      {
        data: 'data_e_krijimit',
        render: (data) => {
          moment.locale('sq');
          if (!data) return '<span class="text-danger">Nuk u gjet asnjë datë.</span>';
          const date = moment(data);
          return date.isValid() ? date.format('dddd, D MMMM YYYY') : '<span class="text-danger">Data e fillimit të kontratës e pavlefshme</span>';
        }
      },
      {
        data: 'kohezgjatja',
        render: function(data, type, row) {
          if (data == null || data === '') {
            return '<span class="text-danger">Nuk u gjet asnjë datë.</span>';
          } else {
            var months = parseInt(data);
            if (isNaN(months) || months <= 0) {
              return '<span class="text-danger">Data e skadimit jo-valide</span>';
            }
            var years = Math.floor(months / 12);
            var remainingMonths = months % 12;
            var durationHTML = '';
            if (years === 0) {
              durationHTML = '<p>' + data + ' Muaj</p>';
            } else if (remainingMonths === 0) {
              durationHTML = '<p>' + years + ' Vjet</p>';
            } else {
              durationHTML = '<p>' + years + ' Vjet ' + remainingMonths + ' Muaj</p>';
            }
            var contractDate = moment(row.data_e_krijimit);
            if (!contractDate.isValid()) {
              return '<span class="text-danger">Data e fillimit të kontratës e pavlefshme</span>';
            }
            var expirationDate = contractDate.clone().add(months, 'months');
            expirationDate.locale('sq');
            var expirationDateFormatted = expirationDate.format('dddd, LL');
            var today = moment();
            var daysUntilExpiration = expirationDate.diff(today, 'days');
            var nearExpirationThreshold = 30;
            var farExpirationThreshold = 90;
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
            if (contractStatus === 'Skaduar') {
              return durationHTML + '<span class="' + statusClass + '">' + statusMessage + '</span>';
            } else {
              return durationHTML + '<span class="' + statusClass + '">' + expirationDateFormatted + ' (' + statusMessage + ')</span>';
            }
          }
        }
      },
      {
        data: 'id',
        render: (data) => `
        <a class="input-custom-css px-3 py-2" href="editk.php?id=${data}"><i class="fi fi-rr-edit"></i></a>
        <a class="input-custom-css px-3 py-2" onclick="konfirmoDeaktivizimin(${data})"><i class="fi fi-rr-user-slash"></i></a>
      `
      }
    ],
    columnDefs: [{
      targets: [0, 1, 2, 3, 4, 5, 6],
      render: (data, type) => type === 'display' && data !== null ? `<div style="white-space: normal;">${data}</div>` : data
    }]
  });
  $('#listaKlientaveTeMonetizuar').DataTable(commonConfig);
  $('#listaKlientaveTePaMonetizuar').DataTable(commonConfig);
  $('#listaKlientaveTePasiv').DataTable(commonConfig);
</script>