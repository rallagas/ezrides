<?php
include_once '../_db.php';
require_once '_class_userWallet.php';
//
//session_start();
//define('USER_LOGGED', $_SESSION['user_id']);

// Fetch transaction history
$transactionHistory = query(
    "SELECT wallet_txn_amt, wallet_action, wallet_txn_start_ts
          , CASE WHEN wallet_txn_status = 'P' THEN 'Pending Approval'
                WHEN wallet_txn_status = 'C' THEN 'Approved'
                ELSE 'DECLINED'
            END AS wallet_txn_status
     FROM `user_wallet` 
     WHERE `user_id` = ? 
     ORDER BY `wallet_txn_start_ts` DESC",
    [USER_LOGGED]
);

// Prepare the data as an array of transactions
$response = [];
foreach ($transactionHistory as $transaction) {
    $response[] = [
        'amount' => number_format($transaction['wallet_txn_amt'], 2),
        'status' => $transaction['wallet_action'],
        'date' => date('Y-m-d H:i:s', strtotime($transaction['wallet_txn_start_ts'])),
        'wallet_txn_status' => $transaction['wallet_txn_status']
    ];
}

// Set response headers and output JSON
header('Content-Type: application/json');
echo json_encode($response);
