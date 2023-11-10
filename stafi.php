<?php include 'shtesStaf.php'; ?>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="container">
        <div class="p-5 rounded-5 shadow-sm mb-4 card">
          <h4 class="font-weight-bold text-gray-800 mb-4">Lista e stafi-it</h4>
          <nav class="d-flex">
            <h6 class="mb-0">
              <a href="stafi.php" class="text-reset" data-bs-placement="top" data-bs-toggle="tooltip" title="<?php echo __FILE__; ?>"><u>Stafi</u></a>
            </h6>
          </nav>
        </div>
        <div class="modal fade" id="perdoruesiPopUp" tabindex="-1" aria-labelledby="perdoruesiPopUpLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="perdoruesiPopUpLabel">Shto nj&euml; p&euml;rdorues t&euml; ri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form method="POST" action="" enctype="multipart/form-data">
                  <div class="form-group">
                    <label for="emri">Emri dhe Mbiemri</label>
                    <input type="text" name="emri" class="form-control shadow-sm rounded-5" placeholder="Sh&euml;no Emrin Mbiemrin">
                  </div>
                  <div class="form-group">
                    <label for="nr">Nr. i telefonit</label>
                    <input type="text" name="nr" class="form-control shadow-sm rounded-5" placeholder="Sh&euml;no numrin e telefonit">
                  </div>
                  <div class="form-group">

                    <label for="email">Adresa e email-it</label>
                    <input type="text" name="email" class="form-control shadow-sm rounded-5" placeholder="Sh&euml;no email adresen">
                  </div>
                  <div class="form-group">
                    <label for="password">Fjal&euml;kalimi</label>
                    <input type="password" name="password" class="form-control shadow-sm rounded-5" placeholder="Sh&euml;no fjalkalimin">
                  </div>
                  <div class="form-group">
                    <label for="user">P&euml;rdoruesi</label>
                    <input type="text" name="user" class="form-control shadow-sm rounded-5" placeholder="Sh&euml;no p&euml;rdoruesin">
                  </div>
                  <div class="form-group">
                    <label for="user">Banka</label>
                    <input type="text" name="banka" class="form-control shadow-sm rounded-5" placeholder="Banka">
                  </div>
                  <div class="form-group">
                    <label for="user">Llogaria</label>
                    <input type="text" name="llogaria" class="form-control shadow-sm rounded-5" placeholder="Llogaria e bank&euml;s">
                  </div>
                  <div class="form-group">

                    <label for="adresa">Adresa</label>
                    <input type="text" name="adresa" class="form-control shadow-sm rounded-5" placeholder="Sh&euml;no adresen e bendbanimit">
                  </div>
                  <div class="form-group">

                    <label for="adresa">Ngarko foto</label>
                    <input type="file" name="fotojaProfilit" class="form-control shadow-sm rounded-5">
                  </div>
                  <div class="form-group">
                    <label for="acc">Pozita</label>
                    <select name="acc" class="form-select shadow-sm rounded-5">
                      <option value="2">Staff</option>
                      <option value="3">Financa</option>
                      <option value="1">Menagjer</option>
                      <option value="4">Kenget</option>
                    </select>
                  </div>


              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mbylle</button>
                <input type="submit" class="btn btn-primary" name="register" value="Ruaj">
                </form>
              </div>
            </div>
          </div>
        </div>


        <div class="card rounded-5 shadow-sm">
          <div class="card-body">
            <h4 class="card-title">Logs</h4>
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table id="example" class="table w-100 table-bordered">
                    <thead class="bg-light">
                      <tr>

                        <th>Emri & Mbiemri</th>
                        <th>Adresa</th>
                        <th>Nr.TEL</th>
                        <th>Email Adresa</th>
                        <th>P&euml;rdoruesi</th>
                        <th>Llogaria e bank&euml;s</th>
                        <th>Authenticator</th>
                        <th></th>
                      </tr>
                    </thead>

                    <tbody>
                      <?php
                      $kueri = $conn->query("SELECT * FROM googleauth ORDER BY id DESC");
                      while ($k = mysqli_fetch_array($kueri)) {
                        if (empty($k['ban'])) {
                          $eme = $k['firstName'];
                        } else {
                          $eme = '<del style="color:red;">' . $k['firstName'] . '</del> ';
                        }
                      ?>
                        <tr>

                          <td><?php echo $eme; ?></td>
                          <td><?php echo $k['adresa']; ?></td>
                          <td><?php echo $k['tel']; ?></td>
                          <td><?php echo $k['email']; ?></td>
                          <td><?php echo $k['perdoruesi']; ?></td>
                          <td><?php echo $k['emrib'] . ": " . $k['llogariab']; ?></td>

                          <td>
                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#kodi<?php echo $k['id']; ?>"><i class="ti-unlock"></i> Shfaq

                            </button>
                          </td>


                          <td><a class="btn btn-danger" href="?del=<?php echo $k['id']; ?>"><i class="fi fi-rr-trash"></i></a> &nbsp;
                            <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#edit<?php echo $k['id']; ?>"><i class="fi fi-rr-edit"></i></a> &nbsp;
                          </td>
                          </td>
                        </tr>
                        <div class="modal fade" id="kodi<?php echo $k['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel"><?php echo $k['name']; ?></h5>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
                                <b>Security code:</b> <code><?php echo $k['google_auth_code']; ?></code>
                                <?php
                                $secreti = $k['google_auth_code'];
                                $userik = $k['email'];
                                $linkui = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=otpauth://totp/BareshaOffice (' . $userik . ')?secret=' . $secreti . '&issuer=BareshaOffice';


                                ?>
                                <hr>
                                <center><img src="<?php echo $linkui; ?>"></center>
                              </div>

                            </div>
                          </div>
                        </div>
                      
                        <div class="modal fade" id="edit<?php echo $k['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Ndrysho Stafin</h5>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
                                <form method="POST" action="" enctype="multipart/form-data">
                                  <div class="form-row">
                                    <div class="form-group col-md-6">
                                      <input type="hidden" name="id" value="<?php echo $k['id']; ?>">
                                      <label for="emri">Emri & Mbiemri</label>
                                      <input type="text" name="emri" class="form-control shadow-sm rounded-5" placeholder="Shkruaj Emrin Mbiemrin" value="<?php echo $k['name']; ?>">
                                    </div>
                                    <div class="form-group col-md-6">
                                      <label for="tel">NR.Tel </label><br>
                                      <input type="text" name="nr" class="form-control shadow-sm rounded-5" placeholder="Shkruaj numrin e telefonit" value="<?php echo $k['tel']; ?>">

                                    </div>
                                    <div class="form-group col-md-6">
                                      <label for="dks">Email adresa</label>
                                      <input type="text" name="email" class="form-control shadow-sm rounded-5" placeholder="Shkruaj email adresen" value="<?php echo $k['email']; ?>">
                                    </div>

                                    <div class="form-group col-md-6">
                                      <label for="yt">P&euml;rdoruesi</label>
                                      <input type="text" name="user" class="form-control shadow-sm rounded-5" placeholder="Shkruaj p&euml;rdoruesin" value="<?php echo $k['perdoruesi']; ?>">
                                    </div>

                                    <div class="form-group col-md-6">
                                      <label for="dk">Adresa</label>
                                      <input type="text" name="adresa" class="form-control shadow-sm rounded-5" placeholder="Shkruaj adresen e bendbanimit" value="<?php echo $k['adresa']; ?>">
                                    </div>
                                    <div class="form-group col-md-6">
                                      <label for="dk">Banka</label>
                                      <input type="text" name="banka" class="form-control shadow-sm rounded-5" placeholder="Banka" value="<?php echo $k['emrib']; ?>">
                                    </div>
                                    <div class="form-group col-md-6">
                                      <label for="dk">Llogaria</label>
                                      <input type="text" name="llogariab" class="form-control shadow-sm rounded-5" placeholder="Llogaria e bank&euml;s" value="<?php echo $k['llogariab']; ?>">
                                    </div>
                                    <div class="form-group col-md-6">
                                      <label for="dk">Pozita</label>
                                      <select name="acc" class="form-control shadow-sm rounded-5">
                                        <?php
                                        if ($k['aksesi'] == '2') {
                                        ?>
                                          <option value="2" selected="selected">Staff</option>
                                          <option value="3">Financa</option>
                                          <option value="1">Menagjer</option>
                                        <?php } elseif ($k['aksesi'] == '3') {

                                        ?>
                                          <option value="2">Staff</option>
                                          <option value="3" selected="selected">Financa</option>
                                          <option value="1">Menagjer</option>
                                        <?php
                                        } else { ?>
                                          <option value="2">Staff</option>
                                          <option value="3">Financa</option>
                                          <option value="1" selected="selected">Menagjer</option>
                                        <?php
                                        }
                                        ?>
                                      </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                      <label><i class="fas fa-ban"></i> Disable</label>
                                      <select name="ban" class="form-control shadow-sm rounded-5">
                                        <option value="1"><i class="fas fa-ban"></i> Disable</option>
                                        <option value="0" selected="">Active</option>
                                      </select>
                                    </div>


                                  </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mbylle</button>
                                <input type="submit" class="btn btn-primary" name="update" value="Ruaj">
                                </form>
                              </div>
                            </div>
                          </div>
                        </div>
                      <?php } ?>

                    </tbody>
                    <tfoot class="bg-light">
                      <tr>
                        <th>Emri & Mbiemri</th>
                        <th>Adresa</th>
                        <th>Nr.TEL</th>
                        <th>Email Adresa</th>
                        <th>P&euml;rdoruesi</th>
                        <th>Llogaria e bank&euml;s</th>
                        <th>Authenticator</th>
                        <th></th>
                      </tr>
                    </tfoot>
                    </tfoot>
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
</div>
<?php include 'partials/footer.php'; ?>

<script>
  $('#example tfoot tr th').each(function() {
    var title = $(this).text();
    $(this).html('<br><input type="text" class="form-control shadow-sm rounded-5 rounded-5 shadow-sm" placeholder="K&euml;rko n&euml; kolon&euml;n ' + title + '" />');
  });
  $('#example').DataTable({

    search: {
      return: true,
    },
    dom: 'Bfrtip',
    buttons: [{
      extend: 'pdfHtml5',
      text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
      titleAttr: 'Eksporto tabelen ne formatin PDF',
      className: 'btn btn-light border shadow-2 me-2'
    }, {
      extend: 'copyHtml5',
      text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
      titleAttr: 'Kopjo tabelen ne formatin Clipboard',
      className: 'btn btn-light border shadow-2 me-2'
    }, {
      extend: 'excelHtml5',
      text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
      titleAttr: 'Eksporto tabelen ne formatin CSV',
      className: 'btn btn-light border shadow-2 me-2'
    }, {
      extend: 'print',
      text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
      titleAttr: 'Printo tabel&euml;n',
      className: 'btn btn-light border shadow-2 me-2'
    }, {
      text: '<i class="fi fi-rr-user-add fa-lg"></i>&nbsp;&nbsp; Shto p&euml;rdorues',
      className: 'btn btn-light border shadow-2 me-2',
      action: function(e, node, config) {
        $('#perdoruesiPopUp').modal('show')
      }
    }, ],
    initComplete: function() {
      var btns = $('.dt-buttons');
      btns.addClass('');
      btns.removeClass('dt-buttons btn-group');
      this.api()
        .columns()
        .every(function() {
          var that = this;

          $('input', this.footer()).on('keyup change clear', function() {
            if (that.search() !== this.value) {
              that.search(this.value).draw();
            }
          });
        });
    },
    fixedHeader: true,
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
    },
    stripeClasses: ['stripe-color']
  })
</script>