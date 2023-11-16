<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Invoice Details and Payment <br> <span class="text-muted" style="font-size: 12px;">Klikoni në butonin "Bëj pagesën" për të kryer pagesën </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tr>
                        <th>Numri i faturës:</th>
                        <td><span id="invoiceNumber"></span></td>
                    </tr>
                    <tr>
                        <th>Emri i klientit:</th>
                        <td><span id="customerName"></span></td>
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
                <br>
                <div class="mb-3">
                    <label for="paymentAmount" class="form-label">Shkruani shumën e pagesës:</label>
                    <input type="text" class="form-control shadow-sm rounded-5 py-4" id="paymentAmount" required>
                </div>
                <p id="paymentAmountError" class="badge bg-danger px-2 rounded-5 py-2"></p>
                <input type="hidden" id="invoiceId" name="invoiceId">
                <button type="button" class="btn btn-primary rounded-5 shadow  text-white" id="submitPayment">Bëj pagesën</button>
            </div>

        </div>
    </div>
</div>