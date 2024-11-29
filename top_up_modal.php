<div class="modal fade" id="topUpModal" tabindex="-1" aria-labelledby="topUpModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="topUpForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="topUpModalLabel">Top-Up Wallet</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="topUpAmount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="topUpAmount" name="amount" min="1" required>
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
