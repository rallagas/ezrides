<?php
include_once "../_db.php";
include_once "_class_riderWallet.php";  // Include the UserWallet class

header('Content-Type: application/json');

$response = ['balance' => 0.00, 'error' => null];

try {
    // Assume user ID is stored in session
    $userId = USER_LOGGED;

    // Create an instance of the UserWallet class
    $userWallet = new UserWallet($userId);
    
    // Get the user's balance
    $balance = $userWallet->getBalance();
    
    // Return the balance as JSON
    $response['balance'] =  number_format($balance, 2);
    
} catch (Exception $e) {
    // Return an error message in case of failure
    $response ['error'] = $e->getMessage();
    
}

echo json_encode($response);
