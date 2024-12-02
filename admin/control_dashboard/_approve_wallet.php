<?php
include_once "../../_db.php"; // Ensure the database connection is included



header('Content-Type: application/json');

try {
    // Check if the txn_id is provided
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['txn_id'])) {
        // Decode and decrypt the transaction ID
        $encryptedTxnId = $_POST['txn_id'];
        $userWalletId = openssl_decrypt(base64_decode($encryptedTxnId), 'aes-256-cbc', SECRET_KEY, 0, SECRET_IV);

        if (!$userWalletId) {
            throw new Exception('Invalid transaction ID.');
        }

        // Prepare and execute the update query
        $query = "UPDATE user_wallet SET wallet_txn_status = 'C' WHERE user_wallet_id = ? AND wallet_txn_status = 'P'";
        if ($stmt = CONN->prepare($query)) {
            $stmt->bind_param('s', $userWalletId); // Bind the wallet ID parameter
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Transaction approved successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Transaction not found or already processed.']);
            }

            $stmt->close();
        } else {
            throw new Exception('Failed to prepare query: ' . CONN->error);
        }
    } else {
        throw new Exception('Invalid request parameters.');
    }
} catch (Exception $e) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
