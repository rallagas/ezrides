<?php
include_once '../_db.php';
require_once '_class_userWallet.php';

// Verify that user is logged in and `user_id` is set in the session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit;
}

$response = ['success' => false];
try {
    // Retrieve and validate the top-up amount
    $amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0.00;
    //$amount = number_format((float)$amount, 2, '.', '');
    if ($amount <= 0) {
        throw new Exception('Invalid top-up amount.');
    }

    // Initialize the user wallet and attempt top-up
    $userWallet = new UserWallet(USER_LOGGED);

    if ($userWallet->topUp($amount)) {
        $response['success'] = true;
    } else {
        $response['error'] = 'Failed to top-up. Please try again.';
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

// Output the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
