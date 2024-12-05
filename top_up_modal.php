<div class="modal fade" id="topUpModal" tabindex="-1" aria-labelledby="topUpModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="topUpForm" enctype="multipart/form-data">
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
                        <input placeholder="Amount Sent" type="number" class="form-control" id="topUpAmount"
                            name="topUpAmount" min=1 required />
                    </div>

                    <div class="mb-2">
                        <input placeholder="GCASH Account (number) Sender" type="text" class="form-control"
                            id="gcashAccountNumber" name="gcashAccountNumber" required>
                    </div>

                    <div class="mb-2">
                        <input placeholder="GCASH Account Name Sender" type="text" class="form-control"
                            id="gcashAccountName" name="gcashAccountName" required>
                    </div>

                    <div class="mb-2">
                        <input placeholder="GCASH Reference Number" type="text" class="form-control" id="gcashRefNumber"
                            name="gcashRefNumber" required>
                    </div>

                    <div class="mb-2">
                        <label for="gcashScreenshot" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-paperclip" viewBox="0 0 16 16">
                            <path d="M4.5 3a2.5 2.5 0 0 1 5 0v9a1.5 1.5 0 0 1-3 0V5a.5.5 0 0 1 1 0v7a.5.5 0 0 0 1 0V3a1.5 1.5 0 1 0-3 0v9a2.5 2.5 0 0 0 5 0V5a.5.5 0 0 1 1 0v7a3.5 3.5 0 1 1-7 0z"/>
                            </svg>
                            <small class="small fs-6">Attach Screenshot</small>
                        </label>
                        <input placeholder="Attach Screenshot" type="file" class="form-control btn btn-secondary d-none" title="Attach Screenshot" id="gcashScreenshot" name="gcashScreenshot" required>
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

