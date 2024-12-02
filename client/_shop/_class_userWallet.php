<?php
global $txn_cat = isset($_SESSION['txn_cat_id']) ? $_SESSION['txn_cat_id'] : 7;

include_once "../../_sql_utility.php";

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
            'txn_type_id' => $txn_cat,
            'wallet_txn_status' => 'C',
            'wallet_action' => 'Top Up Wallet'
        ];

        // Attempt to insert data and log errors if unsuccessful
        $result = insert_data('user_wallet', $data);
        if (!$result) {
            error_log("Failed to insert data: " . json_encode($data)); // Use json_encode for clean logging
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
    public function getBalance($user = null) {
        if($user != null){
            $userId = $user;
        }
        else{
            $user_id = $this->userId;
        }
            $sql = "SELECT SUM(CASE WHEN payment_type = 'R' AND payTo = ? THEN  wallet_txn_amt 
                                 WHEN payment_type = 'R' AND payFrom = ? THEN  wallet_txn_amt
                                 WHEN payment_type = 'S' AND payFrom = ? THEN abs(wallet_txn_amt) * -1 
                                 WHEN payment_type = 'T' THEN wallet_txn_amt
                                 ELSE wallet_txn_amt
                                END )
                        AS balance 
                       FROM user_wallet 
                      WHERE (user_id = ? or payTo = ? or payFrom = ?)
                        AND wallet_txn_status = 'C'";
                $result = query($sql,[$userId,$userId,$userId,$userId,$userId,$userId]);
    
            return (float) $result[0]['balance'] ?? 0;
        }

    /**
     * Make a payment using the user's wallet balance.
     * Inserts a negative transaction record to represent the payment.
     *
     * @param float $amount - Amount to be deducted.
     * @param string|null $walletAction - Description of the wallet action.
     * @return bool
     * @throws InvalidArgumentException if the amount is invalid.
     * @throws Exception if balance is insufficient.
     */
    public function makePayment($amount, $walletAction = null) {
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
            'txn_type_id' => $txn_cat,
            'wallet_action' => "Made Payment" . ($walletAction ? " $walletAction" : ""),
            'wallet_txn_status' => 'C'
        ];

        $result = insert_data('user_wallet', $data);
        if (!$result) {
            error_log("Failed to record payment: " . json_encode($data));
            return false;
        }

        return true;
    }
}
