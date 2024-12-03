<?php
include_once "../_db.php";
include_once "_class_UserWallet.php";

header('Content-Type: application/json');

// Initialize response
$response = ['success' => false, 'message' => null, 'error' => null, 'newBalance' => null, 'amount' => 0.00];

try {
    // Check if the user is logged in
    $userId = USER_LOGGED;
    if (!$userId) {
        throw new Exception("User not logged in.");
    }

    // Parse JSON input
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        throw new Exception("Invalid input data.");
    }

    // Extract input values
    $amount = $data['amount'] ?? 0;
    $gcashData = [
        'gcash_account_number' => $data['gcashAccountNumber'] ?? null,
        'gcash_account_name' => $data['gcashAccountName'] ?? null,
        'gcash_ref_number' => $data['refNumber'] ?? null
    ];

    // Validate the amount
    if ($amount <= 0) {
        throw new Exception("Invalid cash-out amount.");
    }

    // Create an instance of UserWallet
    $userWallet = new UserWallet($userId);

    // Attempt cash-out
    if ($userWallet->CashOut($amount, $gcashData)) {
        $response['success'] = true;
        $response['amount'] = $amount;
        $response['newBalance'] = $userWallet->getBalance(); // Assuming this method retrieves the updated balance
        $response['message'] = "Cash-out successful.";
    } else {
        throw new Exception("Cash-out failed. Please try again.");
    }
} catch (Exception $e) {
    // Handle exceptions
    $response['error'] = $e->getMessage();
    http_response_code(400); // Bad Request
} finally {
    // Output JSON response
    echo json_encode($response);
}
