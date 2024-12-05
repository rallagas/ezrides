<?php
class UserWallet {
    private $userId;

    public function __construct($userId) {
        $this->userId = $userId;
    }

    public function getBalance($user = null) {
        if ($user == null) {

            $result = query("SELECT SUM( CASE WHEN ? = payTo THEN wallet_txn_amt * -1 
                                              ELSE wallet_txn_amt
                                         END 
                   ) AS balance 
                   FROM user_wallet 
                  WHERE (user_id = ? or payTo = ?)
                    AND wallet_txn_status = 'C'",
                [$this->userId, $this->userId, $this->userId]
            );

        }
        else{

        $result = query(
            "SELECT SUM(wallet_txn_amt) AS balance FROM user_wallet WHERE user_id = ? AND wallet_txn_status = 'C'",
            [$user]
        );

        }
        return $result[0]['balance'] ?? 0;
    }

  
        
    
}
