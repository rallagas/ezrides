<?php
include_once "../../_db.php"; // Ensure the database connection is included
include_once "../_class_userWallet.php";
header('Content-Type: application/json');

try {
    // Check if the txn_id is provided
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['app_txn_id'])) {
        // Decode and decrypt the transaction ID
        $app_txn_id = $_POST['app_txn_id'];
        $actionId = $_POST['action_id'];
        $amount_to_pay = $_POST['amount_to_pay'];
        $user_id = $_POST['user_id'];

        $userWalletInstance = new UserWallet($user_id);
        $userWallet = $userWalletInstance->getBalance();
        //$detail_id = trim($_POST['detail_id']);
        $rental_reference = gen_book_ref_num(8,"RNT");

        
        if( $actionId === 'D'){ //decline

            $set = [
                "payment_status" => 'X',
                "txn_status" => 'D'
            ];
            $where = ['app_txn_id' => $app_txn_id];        
            update_data('app_transactions',$set, $where);
            echo json_encode(['success' => true, 'message' => 'Declined Rental Request']);
            exit;
        }
        else{
            if($userWallet < $amount_to_pay){
                // throw new Exception('Invalid transaction ID.');
                 echo json_encode(['success' => false, 'message' => 'Insufficient Balance from User']);
                 exit;
             }
            else{
                    // Prepare and execute the update query
                    $query = "UPDATE app_transactions SET txn_status = ?, rental_reference = ? WHERE app_txn_id = ? AND txn_status = 'P'";
    
                    if ($stmt = CONN->prepare($query)) {
                        $stmt->bind_param('sss',$actionId,$rental_reference,$app_txn_id); // Bind the wallet ID parameter
                        $stmt->execute();
    
                        if ($stmt->affected_rows > 0) {
                            $data = [
                                'user_id' => $user_id,
                                'payTo' => -1,
                                'payFrom' => $user_id,
                                'wallet_txn_amt' => -$amount_to_pay,
                                'txn_type_id' => '1',
                                'wallet_action' => "Made Payment for $rental_reference",
                                'payment_type' => 'A',
                                'reference_number' => $rental_reference,
                                'wallet_txn_status' => 'C'
                            ];
                            if(insert_data('user_wallet',$data)){
                                    $set = [
                                        "payment_status" => 'D'
                                    ];
                                    $where = ['app_txn_id' => $app_txn_id];
                                    
                                update_data('app_transactions',$set, $where);
    
                                echo json_encode(['success' => true, 'message' => 'Transaction approved . Payment Successful.']);
                            } 
                            else{
                                echo json_encode(['success' => true, 'message' => 'Transaction approved . Payment was NOT successful.']);
                            }
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Transaction not found or already processed.']);
                        }
    
                        $stmt->close();
                    } else {
                        throw new Exception('Failed to prepare query: ' . CONN->error);
                    }
            }
        }
       
    } else {
        throw new Exception('Invalid request parameters.');
    }
} catch (Exception $e) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
