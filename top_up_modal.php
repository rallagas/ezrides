
<div class="modal fade" id="topUpModal" tabindex="-1" aria-labelledby="topUpModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="topUpForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="topUpModalLabel">Top-Up Wallet</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class="alert alert-info pt-2 pb-0">
                            <ul class="small">
                             <li>Send the Topup Amount to <?php echo GCASH_ADMIN_ACCOUNT; ?></li>
                             <li>Keep the GCash Reference number for the input below.</li>
                             <li>Wait for the approval of the Admin.</li>
                            </ul>
                        </div>

                        <div class="mb-2">
                            <input placeholder="Amount Sent" type="number" class="form-control" id="topUpAmount" name="topUpAmount" min=1 required />
                        </div>

                        <div class="mb-2">
                            <input placeholder="GCASH Account (number) Sender" type="text" class="form-control" id="gcashAccountNumber" name="gcashAccountNumber" required>
                        </div>

                        <div class="mb-2">
                            <input placeholder="GCASH Account Name Sender" type="text" class="form-control" id="gcashAccountName" name="gcashAccountName" required>
                        </div>

                        <div class="mb-2">
                            <input placeholder="GCASH Reference Number" type="text" class="form-control" id="gcashRefNumber" name="gcashRefNumber" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Top-Up</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
