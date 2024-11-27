<?php //Ezrides1234!
require_once '_class_userWallet.php'; // Assuming this is where the UserWallet class is defined

//define('USER_LOGGED', $_SESSION['user_id']); // Assuming user_id is stored in session upon login

// Initialize UserWallet for the logged-in user
$userWallet = new UserWallet(USER_LOGGED);
$balance = $userWallet->getBalance();
$transactionHistory = query(
    "SELECT wallet_txn_amt, txn_type_id, wallet_action, wallet_txn_start_ts 
     FROM user_wallet 
     WHERE user_id = ? 
     ORDER BY wallet_txn_start_ts DESC",
    [USER_LOGGED]
);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My</title>
    <style>
        .pagination-container {
            text-align: center;
            margin-top: 10px;
        }

        .pagination-container button {
            margin: 0 5px;
        }

        .pagination-container button.active {
            font-weight: bold;
            background-color: #007bff;
            color: white;
        }

        .pagination-container button:disabled {
            background-color: #ddd;
            cursor: not-allowed;
        }

    </style>
</head>

<body>
    <div class="container mt-5">

        <!-- Display Current Balance -->
        <div class="card mt-3">
            <div class="card-body">
                <span class="card-title text-secondary">Current Wallet Balance</span>
                <p class="walletbalance card-text display-4">$<?php echo number_format($balance, 2); ?></p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#topUpModal">Top-Up</button>
            </div>
        </div>

        <!-- Transaction History Table -->
        <div class="card mt-4">
            <div class="card-header">
                Transaction History
            </div>
            <div class="card-body">
                <table id="transactionHistoryTable" class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Transactions will be loaded here -->
                    </tbody>
                </table>
<div id="pagination" class="pagination-container">
    <!-- Pagination buttons will be inserted here -->
</div>

            </div>
        </div>
    </div>

    <!-- Top-Up Modal -->
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

    <!-- Bootstrap JS (Assuming CDN included) -->

</body>

</html>
