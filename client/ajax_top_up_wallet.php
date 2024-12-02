<?php
include_once '../_db.php';
require_once '_class_userWallet.php';

// Verify that user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit;
}

$response = ['success' => false];

try {
    // Validate and sanitize inputs
    $amount = isset($_POST['topUpAmount']) ? (float)$_POST['topUpAmount'] : 0.00;
    $gcashAccountNumber = isset($_POST['gcashAccountNumber']) ? trim($_POST['gcashAccountNumber']) : '';
    $gcashAccountName = isset($_POST['gcashAccountName']) ? trim($_POST['gcashAccountName']) : '';
    $gcashRefNumber = isset($_POST['gcashRefNumber']) ? trim($_POST['gcashRefNumber']) : '';

    if ($amount <= 0) {
        throw new Exception('Invalid top-up amount.');
    }
    if (empty($gcashAccountNumber) || empty($gcashAccountName) || empty($gcashRefNumber)) {
        throw new Exception('All fields are required.');
    }

    // Initialize the user wallet and attempt the top-up
    $userWallet = new UserWallet(USER_LOGGED);

    $data = [
        'gcash_account_number' => $gcashAccountNumber,
        'gcash_account_name' => $gcashAccountName,
        'gcash_ref_number' => $gcashRefNumber,
    ];

    if ($userWallet->topUp($amount, $data)) {
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
