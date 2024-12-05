<?php
require_once '_class_userWallet.php'; // Assuming this is where the UserWallet class is defined

//define('USER_LOGGED', $_SESSION['user_id']); // Assuming user_id is stored in session upon login

// Initialize UserWallet for the logged-in user
$userWallet = new UserWallet(USER_LOGGED);
$balance = $userWallet->getBalance();
$transactionHistory = query(
    "SELECT wallet_txn_amt, wallet_action, wallet_txn_start_ts 
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
    <title>My Wallet</title>
  
</head>

<body>
    <div class="container-fluid">

        <!-- Display Current Balance -->
        <div class="card mb-2">
            <div class="card-body">
                <span class="card-title text-secondary">EZ Wallet 
                     <button class="btn btn-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#topUpModal">
                        CASH IN
                     </button>
                </span>
                <p class="walletbalance card-text display-4 pt-0"> <small class="small fs-6 align-top">PHP</small><?php echo number_format($balance, 2); ?></p>
                
            </div>
        </div>


    </div>
    <!-- Top-Up Modal -->
     <?php include_once "../top_up_modal.php";?>
  
    <!-- Bootstrap JS (Assuming CDN included) -->

</body>

</html>
