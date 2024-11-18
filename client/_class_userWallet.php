<?php
include_once "../_sql_utility.php";

class UserWallet {
    private $userId;

    public function __construct($userId) {
        $this->userId = $userId;
    }

    /**
     * Top-up the user's wallet with a specified amount.
     * Inserts a new record in the `user_wallet` table with a positive transaction amount.
     *
     * @param float $amount - Amount to be topped up.
     * @return bool
     */
public function topUp($amount) {
    if ($amount <= 0 || $amount > 9999999999.99) {
        throw new InvalidArgumentException("Top-up amount must be between 0.01 and 9999999999.99.");
    }

    $data = [
        'user_id' => $this->userId,
        'wallet_txn_amt' => number_format($amount, 2, '.', ''),
        'txn_type_id' => $_SESSION['txn_cat_id'],
        'wallet_txn_status' => 'C',
        'wallet_action' => 'Top Up Wallet',
        'wallet_txn_start_ts' => date('Y-m-d H:i:s')
    ];

    // Attempt to insert data and log errors if unsuccessful
    $result = insert_data('user_wallet', $data);
    if (!$result) {
        error_log("Failed to insert data: " . print_r($data, true)); // Log the data being inserted
        return false;
    }

    return true;
}

    /**
     * Check the total balance in the user's wallet.
     * Calculates the sum of all transaction amounts for the specified user.
     *
     * @return float - Total balance.
     */
    public function getBalance() {
        $result = query(
            "SELECT SUM(wallet_txn_amt) AS balance FROM user_wallet WHERE user_id = ? AND wallet_txn_status = 'C'",
            [$this->userId]
        );

        return $result[0]['balance'] ?? 0;
    }

    /**
     * Make a payment using the user's wallet balance.
     * Inserts a negative transaction record to represent the payment.
     *
     * @param float $amount - Amount to be deducted.
     * @return bool
     * @throws Exception if balance is insufficient.
     */
    public function makePayment($amount, $wallet_action="null") {
    if ($amount <= 0 || $amount > 9999999999.99) {
        throw new InvalidArgumentException("Payment amount must be between 0.01 and 9999999999.99.");
    }

    $balance = $this->getBalance();
    if ($balance < $amount) {
        throw new Exception("Insufficient balance.");
    }

    $data = [
        'user_id' => $this->userId,
        'wallet_txn_amt' => number_format(-$amount, 2, '.', ''),
        'txn_type_id' => $_SESSION['txn_cat_id'],
        'wallet_action' => "Made Payment $wallet_action",
        'wallet_txn_status' => 'C',
        'wallet_txn_start_ts' => date('Y-m-d H:i:s')
    ];

    return insert_data('user_wallet', $data);
}
}
