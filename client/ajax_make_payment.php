<?php
include_once "../_db.php";
include_once "_class_UserWallet.php";  // Include the UserWallet class

header('Content-Type: application/json');

try {
    // Get the user ID from the session
    $userId = USER_LOGGED;

    // Get the payment amount from the request body (assumed to be JSON)
    $data = json_decode(file_get_contents('php://input'), true);
    $amount = $data['amount'];
    $wallet_action = $data['wallet_action'];

    // Create an instance of the UserWallet class
    $userWallet = new UserWallet($userId);
    
    // Attempt to make the payment
    if ($userWallet->makePayment($amount)) {
        echo json_encode(['success' => true, 'amount' => $amount]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Payment failed']);
    }
} catch (Exception $e) {
    // Handle any exceptions (e.g., insufficient balance)
    echo json_encode(['error' => $e->getMessage()]);
}
