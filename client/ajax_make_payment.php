<?php
include_once "../_db.php";
include_once "_class_UserWallet.php";  // Include the UserWallet class
$userId = USER_LOGGED;
header('Content-Type: application/json');
$transact = ['success' => false,'message'=> null, 'error' => null, 'amount' => 0.00];
try {
    // Get the payment amount from the request body (assumed to be JSON)
    $data = json_decode(file_get_contents('php://input'), true);
    $amount = $data['amount'];
    $payToUser = $data['payToUser'];
    $payFromUser = $userId;
    $refNumber = $data['refNum'];
    $paymentType = $data['paymentType'];
    $wallet_action = $data['wallet_action'];
    
    $transact = ['amount' => $amount];

    // Create an instance of the UserWallet class
    $userWallet = new UserWallet($userId);
    
    // Attempt to make the payment
    $transact = $userWallet->makePaymentToRider($amount, $payFromUser,$payToUser, $refNumber,  $paymentType ,$wallet_action);
    if (!empty($transact)) {
        $transact['amount'] = $amount;
        echo json_encode($transact);
    } else {
        echo json_encode(['success' => false, 'message' => 'Payment failed']);
    }
} catch (Exception $e) {
    // Handle any exceptions (e.g., insufficient balance)
    $transact['error'] = [$e->getMessage()];
    echo json_encode($transact);
}
