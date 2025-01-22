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
    public function topUp($amount, $gcashData = []) {
        if ($amount <= 0 || $amount > 9999999999.99) {
            throw new InvalidArgumentException("Top-up amount must be between 0.01 and 9999999999.99.");
        }
    
        $refNumber = gen_book_ref_num(8, "TUP");
        $txn_type_id = isset($_SESSION['txn_cat_id']) ? $_SESSION['txn_cat_id'] : 7;
    
        $data = [
            'user_id' => $this->userId,
            'wallet_txn_amt' => number_format($amount, 2, '.', ''),
            'txn_type_id' => $txn_type_id,
            'wallet_action' => 'Top Up Wallet',
            'payment_type' => 'T',
            'reference_number' => $refNumber,
            'gcash_account_number' => $gcashData['gcash_account_number'] ?? null,
            'gcash_account_name' => $gcashData['gcash_account_name'] ?? null,
            'gcash_reference_number' => $gcashData['gcash_ref_number'] ?? null,
            'gcash_attachment' => $gcashData['gcash_attachment'] ?? null
        ];
    
        // Attempt to insert data into the database
        $result = insert_data('user_wallet', $data);
    
        if (!$result) {
            error_log("Failed to insert data: " . print_r($data, true)); // Log the data being inserted
            return false;
        }
    
        return true;
    }


public function CashOut($amount, $gcashData = []) {
    if ($amount <= 0 || $amount > 9999999999.99) {
        throw new InvalidArgumentException("Cash Out amount must be between 0.01 and 9999999999.99.");
    }

    $refNumber = gen_book_ref_num(8, "COUT");
    $txn_type_id = isset($_SESSION['txn_cat_id']) ? $_SESSION['txn_cat_id'] : 7;

    $data = [   
        'user_id' => $this->userId,
        'wallet_txn_amt' => number_format(-$amount, 2, '.', ''),
        'txn_type_id' => $txn_type_id,
        'wallet_action' => 'Cash Out',
        'payment_type' => 'C',
        'reference_number' => $refNumber,
        'gcash_account_number' => $gcashData['gcash_account_number'] ?? null,
        'gcash_account_name' => $gcashData['gcash_account_name'] ?? null,
        'gcash_reference_number' => $gcashData['gcash_ref_number'] ?? null,
        'wallet_txn_status' => 'P'
    ];

    // Attempt to insert data into the database
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

    //  public function getEarnings($user = null) {
    //     if ($user == null) {

    //         $result = query("SELECT SUM(CASE WHEN payment_type = 'R' AND payTo = ? THEN  wallet_txn_amt 
    //                          WHEN payment_type = 'S' AND payFrom = ? THEN abs(wallet_txn_amt) * -1 
    //                          WHEN payment_type = 'C' THEN wallet_txn_amt
    //                          ELSE wallet_txn_amt
    //                         END ) AS earnings 
    //                FROM user_wallet 
    //               WHERE (payTo = ?)
    //                 AND (wallet_txn_status = 'C'
    //                  OR (payment_type = 'C' and wallet_txn_status = 'P')
    //                  )
    //                 ",
    //             [$this->userId, $this->userId, $this->userId]
    //         );

    //     }
    //     else{

    //     $result = query(
    //         "SELECT SUM(wallet_txn_amt) AS balance FROM user_wallet WHERE user_id = ? AND wallet_txn_status = 'C'",
    //         [$user]
    //     );

    //     }
    //     return $result[0]['balance'] ?? 0;
    // }
    // public function getBalance($user = null) {
    //     if ($user == null) {

    //         $result = query("SELECT SUM( CASE WHEN ? = payTo THEN wallet_txn_amt * -1 
    //                           		          WHEN payment_type = 'T' THEN wallet_txn_amt
    //                                           WHEN payment_type = 'C' THEN wallet_txn_amt
    //                                           ELSE wallet_txn_amt
    //                                      END 
    //                ) AS balance 
    //                FROM user_wallet 
    //               WHERE (user_id = ? or payTo = ?)
    //                 AND (wallet_txn_status in ('C')
    //                       OR (payment_type = 'C' and wallet_txn_status = 'P')
    //                        )
    //                 ",
    //             [$this->userId, $this->userId, $this->userId]
    //         );

    //     }
    //     else{

    //     $result = query(
    //         "SELECT SUM(wallet_txn_amt) AS balance FROM user_wallet WHERE user_id = ? AND wallet_txn_status = 'C'",
    //         [$user]
    //     );

    //     }
    //     return $result[0]['balance'] ?? 0;
    // }

    /**
     * Make a payment using the user's wallet balance.
     * Inserts a negative transaction record to represent the payment.
     *
     * @param float $amount - Amount to be deducted.
     * @param int $payFrom - User Id who paid
     * @param int $payTo - User Id who will be paid
     * @param string $wallet_action - action taken
     * @return array
     * @throws Exception if balance is insufficient.
     */
    public function makePayment($amount, $payFrom = null, $payTo = null, $refNumber = null, $paymentType = null, $wallet_action = "Payment") {
        $response = ["success" => false, "message" => null];
        $refNum = $refNumber;
        $payType = $paymentType;
        $payTo = $payTo ?? -99;
        $payFrom = $payFrom ?? USER_LOGGED; 
        $walletAction = $wallet_action ?? "Payment";
    
        try {
            if ($amount <= 0 || $amount > 9999999999.99) {
                throw new InvalidArgumentException("Payment amount must be between 0.01 and 9999999999.99.");
            }
    
            $balance = $this->getBalance();
            if ($balance < $amount) {
                throw new Exception("Insufficient balance.");
            }
    
            // Start transaction
            mysqli_begin_transaction(CONN);
    
            // Deduction from customer wallet
            $data1 = [
                'user_id' => USER_LOGGED,
                'payFrom' => USER_LOGGED,
                'wallet_txn_amt' => number_format(-$amount, 2, '.', ''),
                'txn_type_id' => $_SESSION['txn_cat_id'],
                'wallet_action' => "$walletAction for $refNum",
                'reference_number' => $refNum,
                'payment_type' => $payType,
                'wallet_txn_status' => 'C'
            ];
            if (!insert_data('user_wallet', $data1)) {
                throw new Exception("Deducting Payment from Customer Failed");
            }
    
            if ($payType === 'R') {
                $amountToRider = $amount * 0.7;

                $data2 = [
                    'payFrom' => USER_LOGGED,
                    'wallet_txn_amt' => number_format($amountToRider, 2, '.', ''),
                    'txn_type_id' => $_SESSION['txn_cat_id'],
                    'wallet_action' => "$walletAction from $payFrom",
                    'reference_number' => $refNum,
                    'payment_type' => $payType,
                    'wallet_txn_status' => 'C'
                ];
                if (!insert_data('user_wallet', $data2)) {
                    throw new Exception("Inserting Funds to Rider Failed");
                }
            
    
                $amountToAdmin = $amount * 0.3;
                
                $data3 = [
                    'payFrom' => $payFrom,
                    'payTo' => -99, //pay for admin
                    'wallet_txn_amt' => number_format($amountToAdmin, 2, '.', ''),
                    'txn_type_id' => $_SESSION['txn_cat_id'],
                    'wallet_action' => "$walletAction to Admin",
                    'reference_number' => $refNum,
                    'payment_type' => $payType,
                    'wallet_txn_status' => 'C'
                ];
                if (!insert_data('user_wallet', $data3)) {
                    throw new Exception("Recording Wallet Funds to Admin Failed");
                }
            }
    
            // Commit transaction
            mysqli_commit(CONN);
            $response = ["success" => true, "message" => "Payment has been made."];
        } catch (Exception $e) {
            // Rollback transaction on failure
            mysqli_rollback(CONN);
            $response['message'] = $e->getMessage();
        }
    
        return $response;
    }
    
    
   

        
    
}
