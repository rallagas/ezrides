<?php
include_once '../_db.php';
require_once '_class_userWallet.php';

$response = ['success' => false];

try {
    // Verify user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in.');
    }

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

    // Handle file upload
    if (!isset($_FILES['gcashScreenshot']) || $_FILES['gcashScreenshot']['error'] != UPLOAD_ERR_OK) {
        throw new Exception('Screenshot is required.');
    }

    $file = $_FILES['gcashScreenshot'];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);

    if (!in_array(strtolower($extension), $allowedExtensions)) {
        throw new Exception('Invalid file type. Only JPG, PNG, or PDF allowed.');
    }

    $uploadDir = '../_upload_gcash_receipts/';
    $filename = $gcashRefNumber . '.' . $extension;
    $filePath = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        throw new Exception('Failed to upload the screenshot.');
    }

    // Initialize the user wallet and attempt the top-up
    $userWallet = new UserWallet(USER_LOGGED);

    $data = [
        'gcash_account_number' => $gcashAccountNumber,
        'gcash_account_name' => $gcashAccountName,
        'gcash_ref_number' => $gcashRefNumber,
        'gcash_attachment' => $filename
    ];

    if ($userWallet->topUp($amount, $data)) {
        $response['success'] = true;
    } else {
        throw new Exception('Failed to top-up. Please try again.');
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

// Output the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
