<?php //Ezrides1234!
require_once '_class_userWallet.php'; // Assuming this is where the UserWallet class is defined

//define('USER_LOGGED', $_SESSION['user_id']); // Assuming user_id is stored in session upon login

// Initialize UserWallet for the logged-in user
$userWallet = new UserWallet(USER_LOGGED);
$balance = $userWallet->getBalance();
$transactionHistory = query(
    "SELECT wallet_txn_amt, wallet_action, wallet_txn_start_ts
          , CASE WHEN wallet_txn_status = 'P' THEN 'Pending Approval'
                WHEN wallet_txn_status = 'C' THEN 'Approved'
                ELSE 'DECLINED'
            END AS wallet_txn_status
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
                <table id="transactionHistoryTable" class="table table-bordered table-responsive">
                    <thead class="table-dark">
                        <tr>
                            <th>Amount (Php)</th>
                            <th>Action</th>
                            <th>Date</th>
                            <th>Status</th>
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
   
    <!-- Top-Up Modal -->
    <?php include_once "../top_up_modal.php";?>

    <!-- Bootstrap JS (Assuming CDN included) -->

</body>

</html>
