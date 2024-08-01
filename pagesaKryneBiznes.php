<div class="tab-pane fade" id="pills-lista_e_faturave_te_kryera_biznes" role="tabpanel" aria-labelledby="pills-lista_e_faturave_te_kryera_biznes-tab">
    <div class="row">
        <div class="col">
            <label for="max" class="form-label" style="font-size: 14px;">Prej:</label>
            <p class="text-muted" style="font-size: 10px;">Zgjidhni një diapazon fillues të
                dates për të filtruar rezultatet</p>
            <div class="input-group rounded-5">
                <span class="input-group-text border-0" style="background-color: white;cursor: pointer;"><i class="fi fi-rr-calendar"></i></span><input type="date" id="startDateBiznes" name="startDateBiznes" class="form-control rounded-5" placeholder="Zgjidhni datën e fillimit" style="cursor: pointer;" readonly>
            </div>
        </div>
        <div class="col">
            <label for="max" class="form-label" style="font-size: 14px;">Deri:</label>
            <p class="text-muted" style="font-size: 10px;">Zgjidhni një diapazon mbarues të
                dates për të filtruar rezultatet.</p>
            <div class="input-group rounded-5">
                <span class="input-group-text border-0" style="background-color: white;cursor: pointer;"><i class="fi fi-rr-calendar"></i></span><input type="text" id="endDateBiznes" name="endDateBiznes" class="form-control rounded-5" placeholder="Zgjidhni datën e mbarimit" style="cursor: pointer;" readonly>
            </div>
        </div>
    </div>
    <div class="col-2 my-4">
        <button id="clearFiltersBtnBiznes" class="input-custom-css px-3 py-2">
            <i class="fi fi-rr-clear-alt"></i>
            Pastro filtrat
        </button>
    </div>
    <hr>
    <div class="table-responsive">
        <table id="paymentsTableBiznes" class="table table-bordered w-100">
            <thead class="table-light">
                <tr>
                    <th style="white-space: normal;font-size: 12px;width: 10px;">Emri i klientit</th>
                    <!-- <th style="white-space: normal;font-size: 12px;">ID e faturës</th> -->
                    <!-- <th style="white-space: normal;font-size: 12px;">Vlera</th> -->
                    <th style="white-space: normal;font-size: 12px;width: 10px;">Data</th>
                    <th style="white-space: normal;font-size: 12px;width: 10px;">Banka</th>
                    <!-- <th style="white-space: normal;font-size: 12px;">Lloji</th> -->
                    <th style="white-space: normal;font-size: 12px;width: 10px;">Përshkrimi</th>
                    <th style="white-space: normal;font-size: 12px;width: 10px;">Shuma e paguar</th>
                    <th style="white-space: normal;font-size: 12px;width: 10px;">Veprim</th>
                </tr>
            </thead>
        </table>
    </div>
</div>