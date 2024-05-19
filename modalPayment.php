<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Kryej pagesën</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="bankInfo" class="form-label">Menyra e pagesës</label>
                    <select id="bankInfo" name="bankInfo" class="form-select rounded-5 shadow-sm py-3">
                        <option value="BankaEkonomike">Banka Ekonomike (Kosovo)</option>
                        <option value="BankaKombetareTregtare">Banka Kombëtare Tregtare (Albania)</option>
                        <option value="BankaPerBiznes">Banka për Biznes (Kosovo)</option>
                        <option value="NLBKomercijalnaBanka">NLB Komercijalna banka (Slovenia)</option>
                        <option value="NLBBanka">NLB Banka (Slovenia)</option>
                        <option value="ProCreditBank">ProCredit Bank (Germany)</option>
                        <option value="RaiffeisenBankKosovo">Raiffeisen Bank Kosovo (Austria)</option>
                        <option value="TEBSHA">TEB SH.A. (Turkey)</option>
                        <option value="ZiraatBank">Ziraat Bank (Turkey)</option>
                        <option value="TurkiyeIsBank">Turkiye Is Bank (Turkey)</option>
                        <option value="PayPal">PayPal</option>
                        <option value="Ria">Ria</option>
                        <option value="Money Gram"> Money Gram</option>
                        <option value="Western Union">Western Union</option>
                        <option value="Tjera">Tjera</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="type_of_pay" class="form-label">Lloji i pageses</label>
                    <select id="type_of_pay" name="type_of_pay" class="form-select rounded-5 shadow-sm py-3">
                        <option value="Biznes">Biznes</option>
                        <option value="Personal">Personal</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="paymentAmount" class="form-label">Shkruani shumën e pagesës:</label>
                    <input type="text" class="form-control shadow-sm rounded-5 py-4" id="paymentAmount" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Shkruani përshkrimin e pagesës</label>
                    <textarea class="form-control shadow-sm rounded-5 py-4" id="description" required></textarea>
                </div>
                <p id="paymentAmountError" class="badge bg-danger px-2 rounded-5 py-2"></p>
                <input type="hidden" id="invoiceId" name="invoiceId">
                <button type="button" class="input-custom-css px-3 py-2" id="submitPayment">Bëj pagesën</button>
                <button class="input-custom-css px-3 py-2" data-bs-target="#exampleModalToggle2" data-bs-toggle="modal">Shfaq detajet e faturës</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="exampleModalToggle2" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="titleOfInvoice"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="invoiceTable">
                    <tr>
                        <th>Numri i faturës:</th>
                        <td><span id="invoiceNumber"></span></td>
                    </tr>
                    <tr>
                        <th>Emri i klientit:</th>
                        <td><span id="customerName"></span></td>
                    </tr>
                    <tr>
                        <th>ID klientit</th>
                        <td><span id="customerId"></span></td>
                    </tr>
                    <tr>
                        <th>Përshkrimi:</th>
                        <td><span id="item"></span></td>
                    </tr>
                    <tr>
                        <th>Shuma e përgjithshme:</th>
                        <td><span id="totalAmount"></span></td>
                    </tr>
                    <tr>
                        <th>Shuma e paguar:</th>
                        <td><span id="paidAmount"></span></td>
                    </tr>
                    <tr>
                        <th>Obligim:</th>
                        <td><span id="remainingAmount"></span></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button class="input-custom-css px-3 py-2" data-bs-target="#paymentModal" data-bs-toggle="modal">Kthehu</button>
            </div>
        </div>
    </div>
</div>