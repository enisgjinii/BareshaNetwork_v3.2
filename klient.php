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
      <?php
      $tabs = [
        "pills-listaEKlienteve-tab" => "Lista e klientëve",
        "pills-listaEKlienteveMonetizuar-tab" => "Monetizuar",
        "pills-listaEKlientevePaMonetizuar-tab" => "Pa monetizuar",
        "pills-listaEKlientevePasiv-tab" => "Pasiv",
        "pills-listaEKlienteveMeK-tab" => "Me kontratë",
        "pills-listaEKlientevePaK-tab" => "Pa kontratë"
      ];
      ?>
      <ul class="nav nav-pills bg-white my-3 mx-0 rounded-5" style="width: fit-content; border: 1px solid lightgrey;" id="pills-tab" role="tablist">
        <?php foreach ($tabs as $id => $label): ?>
          <li class="nav-item" role="presentation">
            <button class="nav-link rounded-5 <?php echo $id === 'pills-listaEKlienteve-tab' ? 'active' : ''; ?>"
              style="text-transform: none"
              id="<?php echo $id; ?>"
              data-bs-toggle="pill"
              data-bs-target="#<?php echo str_replace('-tab', '', $id); ?>"
              type="button" role="tab"
              aria-controls="<?php echo str_replace('-tab', '', $id); ?>"
              aria-selected="<?php echo $id === 'pills-listaEKlienteve-tab' ? 'true' : 'false'; ?>">
              <?php echo $label; ?>
            </button>
          </li>
        <?php endforeach; ?>
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
                        <th class="text-dark">Informatat nga Youtube</th>
                        <th class="text-dark">Emri & Mbiemri</th>
                        <th class="text-dark">Emri artistik</th>
                        <th class="text-dark">Adresa e email-it</th>
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
        <div class="tab-pane fade" id="pills-listaEKlienteveMeK" role="tabpanel" aria-labelledby="pills-listaEKlienteveMeK-tab" tabindex="0">
          <div class="card rounded-5 shadow-none d-none d-md-block d-lg-block"> <!-- Modified visibility classes -->
            <div class="card-body">
              <div class="row">
                <div class="table-responsive">
                  <table id="listaKlientaveMeKontrata" class="table table-striped table-bordered">
                    <thead class="bg-light">
                      <tr>
                        <th class="text-dark">Emri & Mbiemri</th>
                        <th class="text-dark">Emri artistik</th>
                        <th class="text-dark">Statusi i kontrates</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $sql = "SELECT 
                    k.emri, 
                    k.emriart,
                    IF(kg.youtube_id IS NOT NULL, '✔️ Digjitale', NULL) AS digital_contract,
                    k.statusi_i_kontrates
                FROM klientet k
                LEFT JOIN kontrata_gjenerale kg ON k.youtube = kg.youtube_id
                WHERE k.aktiv IS NULL AND 
                      (k.statusi_i_kontrates = 'Kontratë fizike' OR kg.youtube_id IS NOT NULL)
                GROUP BY k.id, k.emri, k.emriart, k.statusi_i_kontrates";
                      // Execute the query
                      $result = mysqli_query($conn, $sql);
                      if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                          // Decide what to display as contract status
                          $contract_status = $row['digital_contract'] ?: $row['statusi_i_kontrates'];
                          echo "<tr>";
                          echo "<td>" . htmlspecialchars($row['emri']) . "</td>";
                          echo "<td>" . htmlspecialchars($row['emriart']) . "</td>";
                          echo "<td>" . htmlspecialchars($contract_status) . "</td>";
                          echo "</tr>";
                        }
                      } else {
                        echo '<tr><td colspan="3" class="text-center"><div class="alert alert-info" role="alert">Nuk ka asnjë klientë me kontrate specifike</div></td></tr>';
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="tab-pane fade" id="pills-listaEKlientevePaK" role="tabpanel" aria-labelledby="pills-listaEKlientevePaK-tab" tabindex="0">
          <div class="card rounded-5 shadow-none d-none d-md-block d-lg-block"> <!-- Modified visibility classes -->
            <div class="card-body">
              <div class="row">
                <div class="table-responsive">
                  <table id="listaKlientavePaKontrata" class="table">
                    <thead class="bg-light">
                      <tr>
                        <th class="text-dark">Emri & Mbiemri</th>
                        <th class="text-dark">Emri artistik</th>
                        <th class="text-dark">Statusi i kontrates</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $sql = "SELECT 
                    k.emri, 
                    k.emriart,
                    COALESCE(NULLIF(k.statusi_i_kontrates, ''), 'S\'ka kontrate') AS statusi_i_kontrates
                FROM klientet k
                LEFT JOIN kontrata_gjenerale kg ON k.youtube = kg.youtube_id
                WHERE k.aktiv IS NULL AND 
                      (k.statusi_i_kontrates = 'S\'ka kontrate' OR k.statusi_i_kontrates = '' OR k.statusi_i_kontrates IS NULL)
                GROUP BY k.id, k.emri, k.emriart, k.statusi_i_kontrates";
                      // Execute the query
                      $result = mysqli_query($conn, $sql);
                      if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                          echo "<tr>";
                          echo "<td>" . htmlspecialchars($row['emri']) . "</td>";
                          echo "<td>" . htmlspecialchars($row['emriart']) . "</td>";
                          echo "<td>" . htmlspecialchars($row['statusi_i_kontrates']) . "</td>";
                          echo "</tr>";
                        }
                      } else {
                        echo '<tr><td colspan="3" class="text-center"><div class="alert alert-info" role="alert">Nuk ka asnjë klientë pa kontratë specifikë</div></td></tr>';
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
  <?php include 'partials/footer.php'; ?>
  <script>
    const commonConfig = {
      ordering: false,
      fixedHeader: true,
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json"
      },
      lengthMenu: [
        [10, 25, 50, 100, -1],
        [10, 25, 50, 100, "Te gjitha"]
      ],
      stripeClasses: ['stripe-color'],
      dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>><'row'<'col-md-12'tr>><'row'<'col-md-6'i><'col-md-6'p>>",
      buttons: ['pdfHtml5', 'excelHtml5', 'copyHtml5', 'print'].map(type => ({
        extend: type,
        text: `<i class="fi fi-rr-file-${type.split('Html5')[0]} fa-lg"></i> ${type.split('Html5')[0].toUpperCase()}`,
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
      })),
      initComplete: () => {
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
    $(document).ready(() => {
      const tables = ['#listaKlientaveTeMonetizuar', '#listaKlientaveTePaMonetizuar', '#listaKlientaveTePasiv', "#listaKlientaveMeKontrata", "#listaKlientavePaKontrata"];
      tables.forEach(table => $(table).DataTable(commonConfig));
      $('#listaKlientave').DataTable({
        ...commonConfig,
        searching: true,
        processing: true,
        serverSide: true,
        lengthMenu: [10, 25, 50, 100, 500, 1000],
        ajax: {
          url: 'api/get_methods/get_clients.php',
          type: 'POST'
        },
        columns: [{
            data: 'youtube',
            render: (data, type, row, meta) => {
              const containerId = `youtube-pic-${meta.row}`;
              const apiKey = 'AIzaSyCRFtIfiEyeYmCrCZ8Bvy8Z4IPBy1v2iwo';
              const youtubeLink = `https://www.youtube.com/channel/${data}`;
              const deleteButtonHTML = `
                <a style="text-decoration: none;" class="input-custom-css px-3 py-2 mx-1" onclick="confirmDelete(${row.id})"><i class="fi fi-rr-trash"></i></a>
              `;
              const editButtonHTML = `
                <div class="mt-3">
                  <a style="text-decoration: none;" class="input-custom-css px-3 py-2 mx-1" href="editk.php?id=${row.id}"><i class="fi fi-rr-edit"></i></a>
                  <a style="text-decoration: none;" class="input-custom-css px-3 py-2 mx-1" onclick="konfirmoDeaktivizimin(${row.id})"><i class="fi fi-rr-user-slash"></i></a>
                  <a style="text-decoration: none;" class="input-custom-css px-3 py-2 mx-1" href="${youtubeLink}" target="_blank"><i class="fi fi-brands-youtube"></i></a>
                  ${deleteButtonHTML}
                </div>
              `;
              if (!/^[a-zA-Z0-9_-]{24}$/.test(data)) {
                return `
                <div id="${containerId}">
                    <span class="text-muted">ID-ja e kanalit të pavlefshme</span>
                    ${editButtonHTML}
                </div>
            `;
              }
              // Asynchronously fetch YouTube data
              setTimeout(() => {
                fetch(`https://www.googleapis.com/youtube/v3/channels?part=snippet,statistics&id=${data}&key=${apiKey}`)
                  .then(response => response.json())
                  .then(data => {
                    const channel = data.items?.[0];
                    if (channel?.snippet) {
                      const {
                        thumbnails,
                        title: channelName,
                        description = 'No description available'
                      } = channel.snippet;
                      const {
                        subscriberCount = 'N/A', videoCount = 'N/A'
                      } = channel.statistics;
                      const profilePicUrl = thumbnails?.default?.url || '';
                      document.getElementById(containerId).innerHTML = `
                            <div class="d-flex flex-column align-items-center">
                                <img src="${profilePicUrl}" class="rounded-circle" style="width: 50px; height: 50px;" />
                                ${editButtonHTML}
                            </div>
                        `;
                    } else {
                      document.getElementById(containerId).innerHTML = `
                            <span class="text-warning">Channel data not available</span>
                            ${editButtonHTML}
                        `;
                    }
                  })
                  .catch(error => {
                    console.error(`YouTube API Error: ${error}`);
                    document.getElementById(containerId).innerHTML = `
                        <span class="text-danger">Failed to load channel data</span>
                        ${editButtonHTML}
                    `;
                  });
              }, 200);
              // Initial HTML with placeholder and buttons
              return `
            <div id="${containerId}">
                <div class="d-flex flex-column align-items-center">
                    <div class="placeholder" style="width: 50px; height: 50px; border-radius: 50%; background: #f0f0f0;"></div>
                    ${editButtonHTML}
                </div>
            </div>
        `;
            }
          },
          {
            data: 'emri',
            render: (data, type, row) => {
              try {
                // Determine the icon and its color based on the statusi_i_kontrates value
                let contractIcon;
                if (row.statusi_i_kontrates === 'Kontratë fizike') {
                  contractIcon = '<i class="fi fi-rr-document-signed text-success" style="font-size: 1.5rem;"></i>';
                } else if (row.statusi_i_kontrates === "S'ka kontratë" || row.has_contract === 'JO') {
                  contractIcon = '<i class="fi fi-rr-document-signed text-danger" style="font-size: 1.5rem;"></i>';
                } else {
                  contractIcon = ''; // Empty string if there's no relevant status or contract
                }
                // Generate the output HTML for monetization status and contract icon with improved layout
                return `
        <div class="d-flex flex-column align-items-start">
          <div class="d-flex justify-content-between w-100">
            <strong>${data}</strong>
            ${contractIcon}
          </div>
          <div class="mt-1">
            <span class="badge rounded-pill ${row.monetizuar === 'PO' ? 'bg-success' : 'bg-danger'}">
              ${row.monetizuar === 'PO' ? 'Klient i Monetizuar' : 'Klient i Pa-Monetizuar'}
            </span>
          </div>
        </div>
      `;
              } catch (error) {
                console.error('Gabim gjatë renderimit të rreshtit:', error);
                return `<p>Gabim gjatë renderimit të të dhënave</p>`;
              }
            }
          },
          {
            data: 'emriart'
          },
          {
            data: 'emailadd'
          },
        ],
        columnDefs: [{
          targets: [0, 1, 2, 3],
          render: (data, type) => (type === 'display' && data) ? `<div style="white-space: normal;">${data}</div>` : data
        }]
      });
      // Ensure columns are adjusted and responsiveness recalculated when the window is resized
      $(window).on('resize', () => {
        mainTable.columns.adjust().responsive.recalc();
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
        preConfirm: () => new Promise(resolve => setTimeout(resolve, 2000)),
        allowOutsideClick: () => !Swal.isLoading()
      }).then((result) => {
        if (result.isConfirmed) {
          Swal.fire({
            title: 'Aktivizuar!',
            text: 'Klienti është aktivizuar me sukses.',
            icon: 'success',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
          }).then(() => window.location.href = `activate_client.php?id=${clientId}`);
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
        preConfirm: () => new Promise(resolve => setTimeout(resolve, 2000)),
        allowOutsideClick: () => !Swal.isLoading()
      }).then((result) => {
        if (result.isConfirmed) {
          Swal.fire({
            title: 'Deaktivizuar!',
            text: 'Klienti është deaktivizuar me sukses.',
            icon: 'success',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
          }).then(() => window.location.href = `passive_client.php?id=${clientId}`);
        }
      });
    }

    function confirmDelete(clientId) {
      Swal.fire({
        title: 'A jeni i sigurt?',
        text: 'Jeni duke u përgatitur për të fshirë këtë klient!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Po, fshije!',
        cancelButtonText: 'Anulo',
        reverseButtons: true,
        showLoaderOnConfirm: true,
        preConfirm: () => {
          return fetch(`delete_client.php?id=${clientId}`, {
              method: 'POST'
            })
            .then(response => {
              if (!response.ok) {
                throw new Error(response.statusText);
              }
              return response.json();
            })
            .then(result => {
              if (result.success) {
                Swal.fire('Fshirë!', 'Klienti është fshirë me sukses.', 'success');
                // Reload the DataTable without refreshing the whole page
                $('#listaKlientave').DataTable().ajax.reload(null, false); // false = keep current page
              } else {
                Swal.fire('Gabim!', 'Diçka shkoi keq.', 'error');
              }
            })
            .catch(error => Swal.fire('Gabim!', 'Fshirja dështoi.', 'error'));
        },
        allowOutsideClick: () => !Swal.isLoading()
      });
    }
  </script>