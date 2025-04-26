<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../_db.php'; // Include your database connection file

    $userId = USER_LOGGED; // Replace this with the actual value for the logged-in user
    $userWalletId = $_POST['user_wallet_id'] ?? null;

    if ($userWalletId && $userId) {
        // Step 1: Check if user_wallet_id exists and fetch wallet_txn_amt and payment_type
        $stmt = CONN->prepare("SELECT wallet_txn_amt, payment_type FROM user_wallet WHERE user_wallet_id = ?");
        $stmt->bind_param('i', $userWalletId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $wallet = $result->fetch_assoc();
            $walletTxnAmt = (float)$wallet['wallet_txn_amt'];
            $paymentType = $wallet['payment_type'];

            // Step 2: Perform updates based on payment_type and wallet_txn_amt
            if ($paymentType === 'S' && $walletTxnAmt < 0) {
                $updateStmt = CONN->prepare("UPDATE user_wallet SET wallet_txn_amt = ?, user_id = ?, payTo = ? WHERE user_wallet_id = ?");
                $updateStmt->bind_param('diii', $walletTxnAmt, $userId, $userId, $userWalletId);
            } elseif ($paymentType === 'R') {
                // Only update user_id and payTo
                $updateStmt = CONN->prepare("UPDATE user_wallet SET user_id = ?, payTo = ? WHERE user_wallet_id = ?");
                $updateStmt->bind_param('iii', $userId, $userId, $userWalletId);
            } else {
                echo json_encode(['success' => false, 'message' => 'No update required for this payment type.']);
                exit;
            }

            // Execute the update query
            if ($updateStmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => $updateStmt->error]);
            }

            $updateStmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Record not found.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
    }

    CONN->close();
}
