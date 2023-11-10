<div class="modal fade" id="pagesmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="flex">
                    <h5 class="modal-title" id="exampleModalLabel">Shto Pages&euml;</h5>
                    <p class="text-muted" style="font-size: 12px;">Plot&euml;soni formularin m&euml; posht&euml; p&euml;r t&euml; shtuar nj&euml;
                        pages&euml;.</p>

                </div>
                <button type="button" class="btn-close pe-5" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="user_form">
                    <div class="row">
                        <div class="col">
                            <label class="form-label">Fatura</label>
                            <input type="text" name="id_of_fatura" id="id_of_fatura" class="form-control input-custom-css" placeholder="Sh&euml;no numrin e fatur&euml;s">
                        </div>
                        <div class="col">
                            <label class="form-label">M&euml;nyra e pages&euml;s</label>
                            <select name="menyra" id="menyra" class="form-select input-custom-css" style="padding-top: 10px;padding-bottom: 10px;">
                                <option value="BANK">BANK</option>
                                <option value="CASH">CASH</option>
                                <option value="PayPal">PayPal</option>
                                <option value="Ria">Ria</option>
                                <option value="MoneyGram">Money Gram</option>
                                <option value="WesternUnion">Western Union</option>
                            </select>
                        </div>
                    </div>
                    <div class="row my-2">
                        <div class="col">
                            <label class="form-label">Shuma</label>
                            <input type="text" name="shuma" id="shuma" class="form-control input-custom-css" placeholder="0" aria-label="Shuma">

                        </div>
                        <div class="col">
                            <label class="form-label">Data</label>
                            <input type="text" name="data" id="data" value="<?php echo date("Y-m-d"); ?>" class="form-control input-custom-css">
                        </div>
                    </div>

                    <div class="my-1">
                        <label class="form-label">P&euml;rshkrimi</label>
                        <textarea class="form-control shadow-sm rounded-5" name="pershkrimi" id="pershkrimi"></textarea>
                    </div>



                    <input type="checkbox" name="kategorizimi[]" value="null" style="display:none;">
                    <table class="table table-bordered mt-3">
                        <thead class="bg-light">
                            <tr>
                                <th>Emri i kategoris&euml;</th>
                                <th>Zgjedhe</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Biznes</td>
                                <td><input type="checkbox" name="kategorizimi[]" value="Biznes"></td>
                            </tr>
                            <tr>
                                <td>Personal</td>
                                <td><input type="checkbox" name="kategorizimi[]" value="Personal"></td>
                            </tr>
                        </tbody>
                    </table>
                    <div id="mesg" style="color:red;"></div>
                </form>
            </div>
            <div class="modal-footer">
                <input type="button" name="ruajp" id="btnruaj" class="save-button-custom-css" value="Ruaj">
            </div>

        </div>
    </div>
</div>