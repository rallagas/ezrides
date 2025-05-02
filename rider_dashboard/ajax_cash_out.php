<?php
//include_once "../_db.php";
//include_once "_class_riderWallet.php";
//
//header('Content-Type: application/json');
//
//// Initialize response
//$response = ['success' => false, 'message' => null, 'error' => null, 'newBalance' => null, 'amount' => 0.00];
//
//try {
//    // Check if the user is logged in
//    $userId = USER_LOGGED;
//    if (!$userId) {
//        throw new Exception("User not logged in.");
//    }
//
//    // Parse JSON input
//    $data = json_decode(file_get_contents('php://input'), true);
//    if (!$data) {
//        throw new Exception("Invalid input data.");
//    }
//
//    // Extract input values
//    $amount = $data['amount'] ?? 0;
//    $gcashData = [
//        'gcash_account_number' => $data['gcashAccountNumber'] ?? null,
//        'gcash_account_name' => $data['gcashAccountName'] ?? null,
//        'gcash_ref_number' => $data['refNumber'] ?? null
//    ];
//
//    // Validate the amount
//    if ($amount <= 0) {
//        throw new Exception("Invalid cash-out amount.");
//    }
//
//    // Create an instance of UserWallet
//    $userWallet = new UserWallet($userId);
//
//    // Attempt cash-out
//    if ($userWallet->CashOut($amount, $gcashData)) {
//        $response['success'] = true;
//        $response['amount'] = $amount;
//        $response['newBalance'] = $userWallet->getEarnings(); // Assuming this method retrieves the updated balance
//        $response['message'] = "Cash-out Request successful.";
//    } else {
//        throw new Exception("Cash-out failed. Please try again.");
//    }
//} catch (Exception $e) {
//    // Handle exceptions
//    $response['error'] = $e->getMessage();
//    http_response_code(400); // Bad Request
//} finally {
//    // Output JSON response
//    echo json_encode($response);
//}



include_once "../_db.php";
include_once "_class_riderWallet.php";

header('Content-Type: application/json');

// Initialize response
$response = [
    'success' => false,
    'message' => null,
    'error' => null,
    'newBalance' => null,
    'amount' => 0.00
];

try {
    // Ensure user is logged in
    $userId = USER_LOGGED;
    if (!$userId) {
        throw new Exception("User not logged in.");
    }

    // Only accept POST requests with form-data
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method.");
    }

    // Handle optional file upload
    $uploadedFilePath = null;
    if (
        isset($_FILES['receivePaymentQR']) &&
        $_FILES['receivePaymentQR']['error'] === UPLOAD_ERR_OK
    ) {
        $uploadDir = __DIR__ . '/../_upload_gcash_receipts/gcash_qr/' . $userId ;
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $originalName = basename($_FILES['receivePaymentQR']['name']);
        $sanitizedFileName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $originalName);
        $filename = uniqid('gcash_qr_') . '_' . $sanitizedFileName;
        $targetFile = $uploadDir . "/" . $filename;

        if (move_uploaded_file($_FILES['receivePaymentQR']['tmp_name'], $targetFile)) {
            $uploadedFilePath =  '_upload_gcash_receipts/gcash_qr/' . $userId . "/" . $filename;
        } else {
            throw new Exception("Failed to upload QR image.");
        }
    }

    // Extract form fields
    $amount = isset($_POST['CashOutAmount']) ? floatval($_POST['CashOutAmount']) : 0;
    $gcashData = [
        'gcash_account_number' => $_POST['GCashAccountNumber'] ?? null,
        'gcash_account_name' => $_POST['GCashAccountName'] ?? null,
        'gcash_qr' => $uploadedFilePath // QR image path, if uploaded
    ];

    // Validate amount
    if ($amount <= 0) {
        throw new Exception("Invalid cash-out amount.");
    }

    // Create wallet instance and perform cash-out
    $userWallet = new UserWallet($userId);
    if ($userWallet->CashOut($amount, $gcashData)) {
        $response['success'] = true;
        $response['amount'] = $amount;
        $response['newBalance'] = $userWallet->getEarnings();
        $response['message'] = "Cash-out Request successful.";
    } else {
        throw new Exception("Cash-out failed. Please try again.");
    }

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    http_response_code(400);
} finally {
    echo json_encode($response);
}
